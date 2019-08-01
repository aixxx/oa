@php
    use App\Services\Attendance\TravelsService;
    $paymentMethod = TravelsService::PAYMENT_METHOD_TRANSFER;
    if ($entry && !empty($entry_data)) {
        $paymentMethodField = $entry_data->where('field_name', 'payment_method')->first();
        $displayTable = !$paymentMethod || ($paymentMethodField && ($paymentMethod = $paymentMethodField->field_value) == TravelsService::PAYMENT_METHOD_REPAY) ? '' : 'hidden';
    } else {
        $displayTable = 'hidden';
    }
@endphp
<div class="form-control form-commit row">
    <table class="table_row {{!$entry || $entry->status == App\Models\Workflow\Entry::STATUS_DRAFT ? 'table-advance-repayment':''}}">
        <thead>
        <tr>
            <th>流程编号</th>
            <th>我的暂支</th>
            <th class="text-right">暂支金额</th>
            <th class="text-right">剩余金额</th>
            <th class="text-right">销暂支金额</th>
        </tr>
        </thead>
        <tbody>
        @if($entry && $entry->status != App\Models\Workflow\Entry::STATUS_DRAFT && $paymentMethod == TravelsService::PAYMENT_METHOD_REPAY)
            @php
                $totalRepayAmount = $entry_data->where('field_name', 'total_repay_amount')->first()->field_value ?? 0;
                $tableData = json_decode($entry_data->where('field_name', 'finance_ap')->first()->field_value, true);
                if (!empty($tableData)) {
                    $input = '<input class="%s" type="text" value="%s" readonly>';
                    $td = '<td class="%s">%s</td>';
                    $tr = '<tr>%s</tr>';

                    foreach ($tableData as $item) {
                        $no = sprintf($td, 'text-center', sprintf($input,'form-control form-commit text-center', $item['no']));
                        $title = sprintf($td, 'text-center', sprintf($input,'form-control form-commit text-center', $item['title']));
                        $borrowAmount = sprintf($td, 'text-right', sprintf($input,'form-control form-commit text-right', $item['borrow_amount']));
                        $remainAmount= sprintf($td, 'text-right', sprintf($input,'form-control form-commit text-right', $item['remain_amount']));
                        $repayAmount = sprintf($td, 'text-right', sprintf($input,'form-control form-commit text-right', $item['repay_amount']));
                        if (App\Http\Helpers\Mh::y2f($item['repay_amount']) > 0) {
                            echo sprintf($tr, $no . $title . $borrowAmount . $remainAmount . $repayAmount);
                        }
                    }
                }
            @endphp
        @endif
        </tbody>
        <tfoot>
        <tr>
            <td>小计</td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right"><input type="text" class="form-control form-commit text-right"
                                          name="tpl[total_repay_amount]" value="{{$totalRepayAmount??0}}" readonly></td>
        </tr>
        </tfoot>
    </table>
</div>


<script>
    $(function () {
        @if($entry)
            $('input[name="tpl[payment_method]"]').attr('disabled', 'disabled');
            @if($entry->status == App\Models\Workflow\Entry::STATUS_DRAFT && $paymentMethod == TravelsService::PAYMENT_METHOD_REPAY)
                $('input[name="tpl[actual_amount]"]').val(0);
                $('input[name="tpl[total_repay_amount]"]').val(0);
            @endif
        @endif

        if ("{{$displayTable}}" == 'hidden') {
            $('.field_type_advance_repayment_list').hide();
            $('.table-advance-repayment > tbody').html('');
        } else {
            loadAdvanceRepayment();
        }

        $('input[name="tpl[payment_method]"]').click(function () {
            if ($('input[name="tpl[payment_method]"]:checked').val() == '销暂支') {
                loadAdvanceRepayment();
                $('.field_type_advance_repayment_list').show();
            } else {
                $('.field_type_advance_repayment_list').hide();
                $('.table-advance-repayment > tbody').html('');
                $('input[name="tpl[actual_amount]"]').val($('input[name="tpl[total_amount]"]').val());
                $('input[name="tpl[total_repay_amount]"]').val(0);
            }
        });
    });

    var changeAmount = function (obj) {
        var repay_amount = obj.val();
        if (!repay_amount) {
            repay_amount = 0;
        } else {
            repay_amount = format2Amount(repay_amount);
        }
        obj.val(repay_amount).change();
        var remain_amount_obj = obj.parents('tr').find('.remain_amount');
        var remain_amount = format2Amount(remain_amount_obj.attr('data-amount'));
        var result_amount = (remain_amount - repay_amount).toFixed(2);
        if (result_amount < 0) {
            remain_amount_obj.find('input').val(remain_amount);
            obj.val(0).change();
            toastr.error('输入金额超限');
            return false;
        } else {
            remain_amount_obj.find('input').val(result_amount);
        }
        var columnIndex = obj.parents('td').index();
        var sumAmount = 0;
        var tbodyObj = obj.parents('tbody');
        tbodyObj.find('tr').each(function () {
            var itemAmount = $(this).find('td').eq(columnIndex).find('input').val();
            if (!itemAmount) {
                itemAmount = 0;
            }
            sumAmount = (parseFloat(sumAmount) + parseFloat(itemAmount)).toFixed(2);
        });
        var totalAmount = parseFloat($('input[name="tpl[total_amount]"]').val()).toFixed(2);
        var result = (totalAmount - sumAmount).toFixed(2);
        if (result < 0) {
            toastr.error('暂支输入总额大于报销输入总额');
            obj.val(0).change();
            return false;
        } else {
            tbodyObj.next('tfoot').find('tr td').eq(columnIndex).find('input').val(sumAmount).change();
        }
        formatData();
    };

    function loadAdvanceRepayment() {
        $('.table-advance-repayment > tbody').html('');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "GET",
            dataType: "json",
            async: true,
            url: "{{route('user.finance.advancePayment')}}",
            success: function (response) {
                if (response.code !== 0) {
                    console.error(response.message);
                    return false;
                }
                var indexCont = 0;
                $.each(response.data, function (index, elemet) {
                    indexCont++;
                    $('.table-advance-repayment tbody').append('<tr>' +
                        '<td class="text-center">' +
                        '<input class="form-control form-commit text-center" type="text" name="tpl[finance_ap][' +
                        index +
                        '][no]" value="' +
                        elemet.entry_id +
                        '" readonly>' +
                        '</td>' +
                        '<td class="text-center">' +
                        '<input class="form-control form-commit text-center" type="text" name="tpl[finance_ap][' +
                        index +
                        '][title]" value="' +
                        elemet.title +
                        '" readonly>' +
                        '</td>' +
                        '<td class="text-right borrow_amount">' +
                        '<input class="form-control form-commit text-right" type="text" name="tpl[finance_ap][' +
                        index +
                        '][borrow_amount]" value="' +
                        f2y(elemet.borrow_amount) +
                        '" readonly>' +
                        '</td>' +
                        '<td class="text-right remain_amount" data-amount="' +
                        f2y(elemet.borrow_amount - elemet.repay_amount) +
                        '">' +
                        '<input class="form-control form-commit text-right" type="text" name="tpl[finance_ap][' +
                        index +
                        '][remain_amount]" value="' +
                        f2y(elemet.borrow_amount - elemet.repay_amount) +
                        '" readonly>' +
                        '</td>' +
                        '<td class="text-right">' +
                        '<input type="text" class="form-control text-right" name="tpl[finance_ap][' +
                        index +
                        '][repay_amount]" value="0" oninput="changeAmount($(this))">' +
                        '</td>' +
                        '</tr>');
                });
            },
            error: function (response) {
                console.log(response);
            }
        });
        formatData();
    }
</script>