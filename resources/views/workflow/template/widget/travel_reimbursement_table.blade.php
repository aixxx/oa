<div class="form-control form-commit row">
    <table class="table_row {{!$entry || $entry->status == App\Models\Workflow\Entry::STATUS_DRAFT ? 'reimbursement-table':''}}">
        <thead>
        <tr>
            <th>项目事由</th>
            <th class="text-center">出发地</th>
            <th class="text-center">目的地</th>
            <th class="text-right">交通费</th>
            <th class="text-right">住宿费</th>
            <th class="text-right">出差补贴</th>
            <th class="text-right">其他杂费</th>
            <th class="text-right">单据张数</th>
        </tr>
        </thead>
        <tbody>
        @if(!$entry)
            <tr>
                <td class="text-center"><input type="text" class="form-control form-commit"
                                               name="tpl[reimbursement][0][description]"></td>
                <td class="text-center"><input type="text" class="form-control form-commit"
                                               name="tpl[reimbursement][0][travel_from]"></td>
                <td class="text-center"><input type="text" class="form-control form-commit"
                                               name="tpl[reimbursement][0][travel_to]"></td>
                <td class="text-right">
                    <input type="text"
                           class="form-control form-commit text-right data_sum"
                           name="tpl[reimbursement][0][telecom_amount]"
                           oninput="sumAmountColumn($(this))">
                </td>
                <td class="text-right">
                    <input type="text"
                           class="form-control form-commit text-right data_sum"
                           name="tpl[reimbursement][0][hotel_amount]"
                           oninput="sumAmountColumn($(this))">
                </td>
                <td class="text-right">
                    <input type="text"
                           class="form-control form-commit text-right data_sum"
                           name="tpl[reimbursement][0][allowance_amount]"
                           oninput="sumAmountColumn($(this))">
                </td>
                <td class="text-right">
                    <input type="text"
                           class="form-control form-commit text-right data_sum"
                           name="tpl[reimbursement][0][other_amount]"
                           oninput="sumAmountColumn($(this))"></td>
                <td class="text-right">
                    <input type="text"
                           class="form-control form-commit text-right"
                           name="tpl[reimbursement][0][invoice_quantity]"
                           oninput="sumIntegerColumn($(this))">
                </td>
            </tr>
        @else
            @php
                $tableData = json_decode($entry_data->where('field_name', 'reimbursement')->first()->field_value, true);
                if (!empty($tableData)) {
                    $input = '<input class="%s" type="text" value="%s" '.
                        ($entry->status == App\Models\Workflow\Entry::STATUS_DRAFT ? '' : 'readonly') .
                        ' name="tpl[reimbursement][%d][%s]" %s>';
                    $td = '<td class="%s">%s</td>';
                    $tr = '<tr>%s</tr>';
                    $telecomAmountTotal = 0;
                    $hotelAmountTotal = 0;
                    $allowanceAmountTotal = 0;
                    $otherAmountTotal = 0;
                    $invoiceQuantityTotal = 0;

                    foreach ($tableData as $key => $item) {
                        $description = sprintf($td, 'text-center', sprintf(
                            $input,
                            'form-control form-commit',
                            $item['description'],
                            $key,
                            'description',
                            ''
                        ));
                        $travelFrom = sprintf($td, 'text-center', sprintf(
                            $input,
                            'form-control form-commit',
                            $item['travel_from'],
                            $key,
                            'travel_from',
                            ''
                        ));
                        $travelTo = sprintf($td, ' text-center', sprintf(
                            $input,
                            'form-control form-commit',
                            $item['travel_to'],
                            $key,
                            'travel_to',
                            ''
                        ));
                        $telecomAmount= sprintf($td, 'text-right', sprintf(
                            $input,
                            'form-control form-commit text-right data_sum',
                            $item['telecom_amount'] ?? 0,
                            $key,
                            'telecom_amount',
                            'oninput="sumAmountColumn($(this))"'
                        ));
                        $hotelAmount = sprintf($td, 'text-right', sprintf(
                            $input,
                            'form-control form-commit text-right data_sum',
                            $item['hotel_amount'] ?? 0,
                            $key,
                            'hotel_amount',
                            'oninput="sumAmountColumn($(this))"'
                        ));
                        $allowanceAmount = sprintf($td, 'text-right', sprintf(
                            $input,
                            'form-control form-commit text-right data_sum',
                            $item['allowance_amount'] ?? 0,
                            $key,
                            'allowance_amount',
                            'oninput="sumAmountColumn($(this))"'
                        ));
                        $otherAmount = sprintf($td, 'text-right', sprintf(
                            $input,
                            'form-control form-commit text-right data_sum',
                            $item['other_amount'] ?? 0,
                            $key,
                            'other_amount',
                            'oninput="sumAmountColumn($(this))"'
                        ));
                        $invoiceQuantity = sprintf($td, 'text-right', sprintf(
                            $input,
                            'form-control form-commit text-right',
                            $item['invoice_quantity'] ?? 0,
                            $key,
                            'invoice_quantity',
                            'oninput="sumIntegerColumn($(this))"'
                        ));

                        echo sprintf($tr, $description . $travelFrom . $travelTo . $telecomAmount . $hotelAmount . $allowanceAmount . $otherAmount . $invoiceQuantity);
                        $telecomAmountTotal += $item['telecom_amount'];
                        $hotelAmountTotal += $item['hotel_amount'];
                        $allowanceAmountTotal += $item['allowance_amount'];
                        $otherAmountTotal += $item['other_amount'];
                        $invoiceQuantityTotal += $item['invoice_quantity'];
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
            <td class="text-right">{{$entry ? App\Http\Helpers\Mh::format($telecomAmountTotal, 2, false) : 0}}</td>
            <td class="text-right">{{$entry ? App\Http\Helpers\Mh::format($hotelAmountTotal, 2, false) : 0}}</td>
            <td class="text-right">{{$entry ? App\Http\Helpers\Mh::format($allowanceAmountTotal, 2, false) : 0}}</td>
            <td class="text-right">{{$entry ? App\Http\Helpers\Mh::format($otherAmountTotal, 2, false) : 0}}</td>
            <td class="text-right">{{$entry ? $invoiceQuantityTotal : 0}}</td>
        </tr>
        @if(!$entry || $entry->status == App\Models\Workflow\Entry::STATUS_DRAFT)
            <tr>
                <td colspan="9">
                    <button type="button" class="btn btn-info pull-right"
                            onclick="addReimbursementItem($(this).parents('.reimbursement-table'))">
                        新增报销项
                    </button>
                    <button type="button" class="btn btn-warning pull-right"
                            onclick="deleteReimbursementItem($(this).parents('.reimbursement-table'))" style="margin: 0 10px;">
                        删除最后一行
                    </button>
                </td>
            </tr>
        @endif
        </tfoot>
    </table>
</div>
<script>
    $(function () {
        $('input[name="tpl[total_amount]"]').change(function () {
            console.log($(this).val());
            var totalAmount = parseFloat($(this).val()).toFixed(2);
            var repayAmount = $('input[name="tpl[total_repay_amount]"]').val();
            if (!repayAmount) {
                repayAmount = 0.00;
            } else {
                repayAmount = parseFloat(repayAmount).toFixed(2);
            }
            var result = (totalAmount - repayAmount).toFixed(2);
            if (result < 0) {
                toastr.error('报销输入总额不能小于暂支输入总额');
                return false;
            } else {
                $('input[name="tpl[actual_amount]"]').val(result).change();
            }
        });
        $('input[name="tpl[total_repay_amount]"]').change(function () {
            var totalAmount = parseFloat($('input[name="tpl[total_amount]"]').val()).toFixed(2);
            var repayAmount = parseFloat($(this).val()).toFixed(2);
            var result = (totalAmount - repayAmount).toFixed(2);
            if (result < 0) {
                toastr.error('暂支输入总额大于报销输入总额');
                return false;
            } else {
                $('input[name="tpl[actual_amount]"]').val(result).change();
            }
        });
    });

    function sumIntegerColumn(obj) {
        obj.val(format2Integer(obj.val()));
        var columnIndex = obj.parents('td').index();
        var sumValue = 0;
        var tbodyObj = obj.parents('tbody');
        tbodyObj.find('tr').each(function () {
            var itemValue = $(this).find('td').eq(columnIndex).find('input').val();
            if (!itemValue) {
                itemValue = 0;
            }
            sumValue = parseInt(sumValue) + parseInt(itemValue);
        });

        tbodyObj.next('tfoot').find('tr td').eq(columnIndex).text(sumValue);
    }

    function sumAmountColumn(obj) {
        obj.val(format2Amount(obj.val()));
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

        tbodyObj.next('tfoot').find('tr td').eq(columnIndex).text(sumAmount);
        formatData();
    }

    function deleteReimbursementItem(tableObj) {
        var index = tableObj.find('tbody').find('tr').length;
        if (index == 1) {
            alert('至少保留一行');
            return;
        }
        var has_value = false;
        tableObj.find('tbody tr:last').find('td input').each(function (i, ele) {
            if ($(ele).val() != 0 && $(ele).val() != '') {
                console.log($(ele).val());
                has_value = true;
            }
        });
        if (has_value) {
            alert('最后一行还有数据，请先清空');
            return;
        }
        tableObj.find('tbody tr:last').remove();
        $("[name='tpl[reimbursement][0][telecom_amount]']").trigger('input').trigger('change');
        $("[name='tpl[reimbursement][0][hotel_amount]']").trigger('input').trigger('change');
        $("[name='tpl[reimbursement][0][other_amount]']").trigger('input').trigger('change');
        $("[name='tpl[reimbursement][0][allowance_amount]']").trigger('input').trigger('change');
        $("[name='tpl[reimbursement][0][invoice_quantity]']").trigger('input').trigger('change');
    }
    function addReimbursementItem(tableObj) {
        var index = tableObj.find('tbody').find('tr').length;
        var tr = '<tr>' +
            '<td class="text-center">' +
            '<input type="text" class="form-control form-commit" name="tpl[reimbursement][' +
            index +
            '][description]">' +
            '</td>' +
            '<td class="text-center">' +
            '<input type="text" class="form-control form-commit" name="tpl[reimbursement][' +
            index +
            '][travel_from]">' +
            '</td>' +
            '<td class="text-center">' +
            '<input type="text" class="form-control form-commit" name="tpl[reimbursement][' +
            index +
            '][travel_to]">' +
            '</td>' +
            '<td class="text-right">' +
            '<input type="text" class="form-control form-commit text-right data_sum" name="tpl[reimbursement][' +
            index +
            '][telecom_amount]" oninput="sumAmountColumn($(this))" value="0">' +
            '</td>' +
            '<td class="text-right">' +
            '<input type="text" class="form-control form-commit text-right data_sum" name="tpl[reimbursement][' +
            index +
            '][hotel_amount]" oninput="sumAmountColumn($(this))" value="0">' +
            '</td>' +
            '<td class="text-right">' +
            '<input type="text" class="form-control form-commit text-right data_sum" name="tpl[reimbursement][' +
            index +
            '][allowance_amount]" oninput="sumAmountColumn($(this))" value="0">' +
            '</td>' +
            '<td class="text-right">' +
            '<input type="text" class="form-control form-commit text-right data_sum" name="tpl[reimbursement][' +
            index +
            '][other_amount]" oninput="sumAmountColumn($(this))" value="0">' +
            '</td>' +
            '<td class="text-right">' +
            '<input type="text" class="form-control form-commit text-right" name="tpl[reimbursement][' +
            index +
            '][invoice_quantity]" oninput="sumIntegerColumn($(this))" value="0">' +
            '</td>' +
            '</tr>';
        tableObj.find('tbody').append(tr);
        formatData();
    }
</script>