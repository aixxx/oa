<style>
    thead.invoice-head th {
        vertical-align: middle;
    }

    tbody.invoice-body td {
        vertical-align: middle;
    }

    div.remark textarea {
        width: 50% !important;
        height: 200px !important;
    }
</style>
<?php
if ($entry_data) {
    $assetFormData = $entry_data->whereNotIn('field_name', \App\Services\Workflow\FlowFinanceInvoiceSign::$noValidColumn);
    $assetFormData->each(function ($item, $key) {
        $item->field_value = json_decode($item->field_value, true);
    });
//    dump($assetFormData);
}
//dump(\Route::currentRouteName());
?>

@if(!in_array(\Route::currentRouteName(),['workflow.entry.show','workflow.proc.show']))
    <a href="javascript:void(0);" style="position: absolute; left:7em;" onclick="addTr()">新增</a>
@endif


<div class="row" style="margin-top: 10px;">
    <div class="col-md-12">
        <table class="table table-bordered text-center table-sm">
            <thead class="invoice-head">
            <th>付款流程</th>
            <th>供应商名称</th>
            <th>应收发票金额（元）</th>
            <th>发票号</th>
            <th>发票类型</th>
            <th>发票金额（元）</th>
            <th>不含税金额（元）</th>
            <th>税额（元）</th>
            <th>发票张数</th>
            <th>剩余应收发票金额（元）</th>
            @if(!in_array(\Route::currentRouteName(),['workflow.entry.show','workflow.proc.show']))
                <th>操作</th>
            @endif
            </thead>
            <tbody class="invoice-body">
            @if(in_array(\Route::currentRouteName(),['workflow.entry.show','workflow.proc.show']))
                @foreach($assetFormData as $key => $value)
                    <tr>
                        <td>
                            <select name="tpl[1][payment_amount_flow]" class="form-control payment_amount_flow">
                                <option value="">{{$value->field_value['payment_amount_flow_title']}}</option>
                            </select>
                        </td>
                        <td class="beneficiary"><input type="text" name="tpl[1][beneficiary]" class="form-control" value="{{$value->field_value['beneficiary']}}" readonly></td>
                        <td class="should_payemnt"><input type="text" name="tpl[1][should_payemnt]"  value="{{$value->field_value['should_payemnt']}}" class="form-control" readonly></td>
                        <td><input type="text" name="tpl[1][invoice_num]" class="form-control" value="{{$value->field_value['invoice_num']}}"></td>
                        <td>
                            <select name="tpl[1][invoice_type]" id="" class="form-control">
                                <option value="">请选择</option>
                                <option value="增值税专用发票" @if($value->field_value['invoice_type'] == "增值税专用发票") selected @endif>增值税专用发票</option>
                                <option value="增值税普通发票" @if($value->field_value['invoice_type'] == "增值税普通发票") selected @endif>增值税普通发票</option>
                            </select>
                        </td>
                        <td class="amount"><input type="text" class="form-control" name="tpl[1][invoice_amount]"
                                                  value="{{$value->field_value['invoice_amount']}}"
                                                  oninput="calculateRemainInvoiceAmount(this)"></td>
                        <td class="amount"><input type="text" class="form-control" name="tpl[1][without_tax_amount]"
                                                  value="{{$value->field_value['without_tax_amount']}}"></td>
                        <td class="amount"><input type="text" class="form-control" name="tpl[1][tax_amount]" value="{{$value->field_value['tax_amount']}}"></td>
                        <td class="amount"><input type="text" class="form-control" name="tpl[1][invoice_sheet]"
                                                  value="{{$value->field_value['invoice_sheet']}}">
                        </td>
                        <td><input type="text" class="form-control" name="tpl[1][remain_invoice_amount]" readonly
                                   value="{{$value->field_value['remain_invoice_amount']}}"></td>
                        @if(!in_array(\Route::currentRouteName(),['workflow.entry.show','workflow.proc.show']))
                            <td><a href="javascript:void(0);">删除</a></td>
                        @endif
                    </tr>
                @endforeach
            @elseif(\Route::currentRouteName() == 'workflow.entry.create')
                <tr data-trnum="1">
                    <td>
                        <select name="tpl[1][payment_amount_flow]" class="form-control payment_amount_flow">

                        </select>
                        <input type="hidden" name="tpl[1][payment_amount_flow_title]">
                    </td>
                    <td class="beneficiary"><input type="text" name="tpl[1][beneficiary]" class="form-control" readonly></td>
                    <td class="should_payemnt"><input type="text" name="tpl[1][should_payemnt]" class="form-control" readonly></td>
                    <td><input type="text" name="tpl[1][invoice_num]" class="form-control"></td>
                    <td>
                        <select name="tpl[1][invoice_type]" id="" class="form-control">
                            <option value="">请选择</option>
                            <option value="增值税专用发票">增值税专用发票</option>
                            <option value="增值税普通发票">增值税普通发票</option>
                        </select>
                    </td>
                    <td class="amount"><input type="text" class="form-control" name="tpl[1][invoice_amount]" oninput="calculateRemainInvoiceAmount(this)"></td>
                    <td class="amount"><input type="text" class="form-control" name="tpl[1][without_tax_amount]"></td>
                    <td class="amount"><input type="text" class="form-control" name="tpl[1][tax_amount]"></td>
                    <td class="amount"><input type="text" class="form-control" name="tpl[1][invoice_sheet]"></td>
                    <td><input type="text" class="form-control" name="tpl[1][remain_invoice_amount]" readonly></td>
                    <td><a href="javascript:void(0);">删除</a></td>
                </tr>
            @elseif(\Route::currentRouteName() == 'workflow.entry.edit')
                @foreach($assetFormData as $key => $value)
                    <tr data-trnum="{{$value->field_name}}">
                        <td>
                            <select name="tpl[{{$value->field_name}}][payment_amount_flow]" class="form-control payment_amount_flow">

                            </select>
                            <input type="hidden" name="tpl[{{$value->field_name}}][payment_amount_flow_title]" value="{{$value->field_value['payment_amount_flow_title']}}">
                        </td>
                        <td class="beneficiary"><input type="text" name="tpl[{{$value->field_name}}][beneficiary]" class="form-control"
                                                       value="{{$value->field_value['beneficiary']}}" readonly></td>
                        <td class="should_payemnt"><input type="text" name="tpl[{{$value->field_name}}][should_payemnt]"  value="{{$value->field_value['should_payemnt']}}"
                                                          class="form-control" readonly></td>
                        <td><input type="text" name="tpl[{{$value->field_name}}][invoice_num]" class="form-control" value="{{$value->field_value['invoice_num']}}"></td>
                        <td>
                            <select name="tpl[{{$value->field_name}}][invoice_type]" class="form-control">
                                <option value="">请选择</option>
                                <option value="增值税专用发票" @if($value->field_value['invoice_type'] == "增值税专用发票") selected @endif>增值税专用发票</option>
                                <option value="增值税普通发票" @if($value->field_value['invoice_type'] == "增值税普通发票") selected @endif>增值税普通发票</option>
                            </select>
                        </td>
                        <td><input type="text" class="form-control" name="tpl[{{$value->field_name}}][invoice_amount]"
                                                  value="{{$value->field_value['invoice_amount']}}"
                                                  oninput="calculateRemainInvoiceAmount(this)"></td>
                        <td class="amount"><input type="text" class="form-control" name="tpl[{{$value->field_name}}][without_tax_amount]"
                                                  value="{{$value->field_value['without_tax_amount']}}"></td>
                        <td class="amount"><input type="text" class="form-control" name="tpl[{{$value->field_name}}][tax_amount]"
                                                  value="{{$value->field_value['tax_amount']}}"></td>
                        <td class="amount"><input type="text" class="form-control" name="tpl[{{$value->field_name}}][invoice_sheet]"
                                                  value="{{$value->field_value['invoice_sheet']}}">
                        </td>
                        <td><input type="text" class="form-control" name="tpl[{{$value->field_name}}][remain_invoice_amount]" readonly
                                   value="{{$value->field_value['remain_invoice_amount']}}"></td>
                        @if(!in_array(\Route::currentRouteName(),['workflow.entry.show','workflow.proc.show']))
                            <td><a href="javascript:void(0);" onclick="delTr(this);">删除</a></td>
                        @endif
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
</div>

<script>
    window.onload = function () {
        var workflow_payment_data = (function () {
            var result;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                async: false,
                type: 'get',
                dataType: 'json',
                url: "{{route('finance.invoice.flowPaymentData',['id'=> $user_id])}}",
                success: function (response) {
                    result = response;
                }
            });
            return result;
        })();

        //填充付款流程
        payment_option_node = getFlowPaymentOption(workflow_payment_data);
        $(".payment_amount_flow").append(payment_option_node);


        $(".payment_amount_flow").change(function () {  //校验选择付款流程每种只能选择一个

            current_entry_id = $(this).val();

            existed_flow_node = $("select.payment_amount_flow option[value='" + current_entry_id + "']:selected").parent();
            if (existed_flow_node.length > 1) {
                alert("同一付款流程只能选择一次。");
                cur_tr = $(this).parent().parent(); //清空当前tr
                cur_tr.find("td.beneficiary").find('input').val('');
                cur_tr.find('td.should_payemnt').find('input').val('');
                cur_tr.find("select,input").each(function () {
                    $(this).val('');
                });
                return;
            }
            //切换流程清空当前表单数据
            $(this).parent().nextAll().find("select,input").each(function () {
                $(this).val('')
            });



            if (current_entry_id) {
                current_flow_payment_amount_data = workflow_payment_data[current_entry_id];
                $(this).parent().next().find('input').val(current_flow_payment_amount_data.beneficiary); //供应商名称

                if (current_flow_payment_amount_data.payment_amount_transfer) {
                    current_total_payment = parseFloat(current_flow_payment_amount_data.payment_amount_transfer);
                } else {
                    current_total_payment = 0;
                }

                if (current_flow_payment_amount_data.paid_payment_amount_transfer) {
                    current_paid_payment = parseFloat(current_flow_payment_amount_data.paid_payment_amount_transfer);
                } else {
                    current_paid_payment = 0;
                }
                should_payment = Math.floor((current_total_payment - current_paid_payment)*100) / 100 ;
                $(this).parent().next().next().find('input').val(should_payment); // 应收发票金额（元）

                selected_payment_amount_title = $("select.payment_amount_flow option[value='" + current_entry_id + "']:selected").text(); //付款流程标题
                $(this).next().val(selected_payment_amount_title);
            }
        });

        @if(\Route::currentRouteName() == 'workflow.entry.edit')
            @foreach($assetFormData as $key => $value)
                $("select[name='tpl[{{$value->field_name}}][payment_amount_flow]']").find("option[value='{{$value->field_value['payment_amount_flow']}}']").attr("selected",true);
            @endforeach
        @endif
    };


    /**
     * 获取付款流程名称
     * @param workflow_payment_data
     * @returns {string|*}
     */
    function getFlowPaymentOption(workflow_payment_data) {
        //付款流程
        payment_option_node = '<option value="">请选择</option>';
        if (Object.keys(workflow_payment_data).length) {
            for (var key in workflow_payment_data) {
                payment_option_node += "<option value='" + workflow_payment_data[key].entry_id + "'>" + workflow_payment_data[key].title + "</option>";
            }
        }
        return payment_option_node;
    }


    /**
     * 计算剩余应收发票金额
     * @param obj
     */
    function calculateRemainInvoiceAmount(obj) {
        if ($(obj).parent().prev().prev().prev().find('input').val()) {//应收发票金额（元）
            should_payment = parseFloat($(obj).parent().prev().prev().prev().find('input').val());
        } else {
            should_payment = 0;
        }
        $(obj).val(clearNoNum($(obj).val()));
        if ($(obj).val()) { //发票金额（元）
            invoice_amount = parseFloat($(obj).val());
        } else {
            invoice_amount = 0;
        }

        if (invoice_amount > should_payment) {
            alert("发票金额不能大于应收发票金额。");
            $(obj).val('');
            $(obj).parent().next().next().next().next().find("input").val('');
            return
        }

        remain_invoice_amount = (should_payment.toFixed(2) - invoice_amount.toFixed(2)).toFixed(2);
        //剩余应收发票金额
        $(obj).parent().next().next().next().next().find("input").val(remain_invoice_amount);
    }

    /**
     * 添加新的一行
     */
    function addTr() {
        last_tr = $("tr").last();
        last_tr_num = parseInt(last_tr.attr('data-trnum'));
        new_tr_num = last_tr_num + 1;
        new_tr = last_tr.clone(true);

        new_tr.find('a').click(function () {
            delTr($(this));
        }); //删除当前行

        new_tr.find("td.beneficiary").find('input').val('');
        new_tr.find('td.should_payemnt').find('input').val('');

        new_tr.find("select,input").each(function () {
            old_name = $(this).attr('name');
            old_name_arr = old_name.split('][');
            old_name_arr[0] = old_name_arr[0].split('[');
            old_name_arr[0][1] = new_tr_num;
            old_name_arr[0] = old_name_arr[0].join('[');
            new_name = old_name_arr.join('][');
            $(this).attr('name', new_name);
            $(this).val('');
        });

        new_tr.attr('data-trnum', new_tr_num);
        last_tr.after(new_tr);
    }

    function delTr(obj) {
        $(obj).parent().parent().remove();
    }
</script>


