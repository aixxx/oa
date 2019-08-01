<div class="form-control form-commit row">
    <table class="table_row">
        <thead>
        <th>费用科目</th>
        <th>费用发生日期*</th>
        <th>报销事由*</th>
        <th>报销金额*</th>
        <th>单据数(张)*</th>
        </thead>
        <tbody id="expense_order_table_body">
        @if($entry && $entry_data->where('field_name', 'expense_reimburse_list_multiple')->count())
            @php
                $tableData = json_decode($entry_data->where('field_name', 'expense_reimburse_list_multiple')->first()->field_value, true);

                if (!empty($tableData)) {
                    $tr = '<tr>%s</tr>';
                    $td = '<td class="%s">%s</td>';
                    $input = '<input class="%s" type="text" value="%s" '.
                        ($entry->status == App\Models\Workflow\Entry::STATUS_DRAFT ? '' : 'readonly') .
                        ' name="tpl[expense_reimburse_list_multiple][%d][%s]" %s>';

                    foreach ($tableData as $key => $data) {

                        $option = '';
                        foreach(App\Services\Workflow\FlowExpensesReimburse::$expenseType as $optionKey => $value) {
                            $option .= "<option value='$value'" . ( $value ==  $data['expense_order_type'] ?  "selected" : " ") .  ">$value</option>";
                         }

                        $type = sprintf($td, 'text-center', sprintf(
                                    '<select class="form-control form-commit" name="tpl[expense_reimburse_list_multiple][%d][%s]"><option value="">请选择费用科目*</option>%s</select>',
                                        $key,
                                        'expense_order_type',
                                        $option
                                    )
                                );

                        $date   = "<td  class='text-center'><input class='datepicker form-control form-commit' name='tpl[expense_reimburse_list_multiple][$key][expense_order_date]' id=name='expense_order_date_$key'  value='". $data['expense_order_date'] ."'></td>";

                        $reason = sprintf($td, ' text-center', sprintf(
                            $input,
                            'form-control form-commit',
                            $data['expense_order_reason'],
                            $key,
                            'expense_order_reason',
                            ''
                        ));
                        $amount = sprintf($td, ' text-center', sprintf(
                            $input,
                            'form-control form-commit data_sum',
                            $data['expense_order_amount'],
                            $key,
                            'expense_order_amount',
                            'oninput="transformAmount(this)"'
                        ));
                        $num = sprintf($td, ' text-center', sprintf(
                            $input,
                            'form-control form-commit expense_order_num',
                            $data['expense_order_num'],
                            $key,
                            'expense_order_num',
                            'onkeyup="this.value=this.value.replace(/\D/gi,\'\')"'
                        ));

                        echo sprintf($tr, $type . $date . $reason . $amount . $num);
                    }
                }
            @endphp
        @else
            @for($i=1;$i<=5;$i++)
                <tr id="expense_order_tr_{{ $i }}">
                    <td class="text-center">
                        <select class="form-control form-commit" name="tpl[expense_reimburse_list_multiple][{{$i}}][expense_order_type]">
                            <option value="">请选择费用科目*</option>
                            @foreach(App\Services\Workflow\FlowExpensesReimburse::$expenseType as $key=>$value)
                                <option value="{{$value}}">{{$value}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="text-center"><input class="datepicker form-control form-commit"
                               name="tpl[expense_reimburse_list_multiple][{{$i}}][expense_order_date]"
                               id=name="expense_order_date_{{$i}}" value=""></td>
                    <td class="text-center"><input class="form-control form-commit" name="tpl[expense_reimburse_list_multiple][{{$i}}][expense_order_reason]"
                               value=""></td>
                    <td class="text-center"><input class="form-control form-commit data_sum"
                               name="tpl[expense_reimburse_list_multiple][{{$i}}][expense_order_amount]"
                               id="expense_order_amount_{{$i}}" value="" oninput="transformAmount(this)"></td>
                    <td class="text-center"><input class="form-control form-commit expense_order_num"
                               name="tpl[expense_reimburse_list_multiple][{{$i}}][expense_order_num]"
                               id="expense_order_num_{{$i}}" value="" onkeyup='this.value=this.value.replace(/\D/gi,"")'></td>
                </tr>
            @endfor
        @endif

        </tbody>
    </table>

    @if(!$expense_order_num || ($entry && $entry->isDraft()))
        <input class="btn btn-primary" style="margin-top: 20px;float: right;" type="button"
               id="add_expense_order"
               value="新增报销项">
    @endif
    <input type="hidden" id="expense_order_num" name="tpl[expense_order_num]"
           value='<?php echo $cnt ?? 6;?>'>
</div>
<script>
    function transformAmount(obj) {
        input_obj = $(obj).val();
        $(obj).val(clearNoNum(input_obj));
        if (!/^\d*(\.\d*)?$/.test(input_obj)) {
            alert("数字填写错误!");
            return;
        }
        amount_upper = smalltoBIG(input_obj);
        console.log(amount_upper);
        if (amount_upper != '数字填写错误!') {
            $("input[name='tpl[payment_amount][amount_upper]']").val(amount_upper);
            $("input[name='tpl[payment_amount_transfer]']").val(clearNoNum(input_obj));
        } else {
            $(obj).val('');
            $("input[name='tpl[payment_amount][amount_upper]']").val('');
        }
    }


    function clearNoNum(num) {
        num = num.replace(/[^\d.]/g, "");  //清除“数字”和“.”以外的字符
        num = num.replace(/\.{2,}/g, "."); //只保留第一个. 清除多余的
        num = num.replace(".", "$#$").replace(/\./g, "").replace("$#$", ".");
        num = num.replace(/^(\-)*(\d+)\.(\d\d).*$/, '$1$2.$3');//只能输入两个小数
        if (num.indexOf(".") < 0 && num != "") {//以上已经过滤，此处控制的是如果没有小数点，首位不能为类似于 01、02的金额
            num = parseFloat(num);
        }
        return num;
    }

    $(function () {
        //费用报销单
        $("#add_expense_order").on('click', function () {
            var num = $("#expense_order_num").val();
            var typeList = JSON.parse('<?php echo json_encode(App\Services\Workflow\FlowExpensesReimburse::$expenseType, JSON_UNESCAPED_UNICODE) ?>');

            var expense_order_type = '<select class="form-control form-commit" name="tpl[expense_reimburse_list_multiple][' + num +'][expense_order_type]">';
            expense_order_type +='<option value="">请选择费用科目*</option>';

            for (key in typeList) {
                expense_order_type += '<option value="' + typeList[key] + '">' + typeList[key] + '</option>';
            }
            expense_order_type += '</select>';

            var expense_order_date = '<input class="expense_order_date_datepicker form-control form-commit" name="tpl[expense_reimburse_list_multiple][' + num + '][expense_order_date]" ' +
                'id="expense_order_date_' + num + '" value="">';
            var expense_order_reason = '<input class="form-control form-commit" name="tpl[expense_reimburse_list_multiple][' + num + '][expense_order_reason]" id="expense_order_reason_' + num
                + '" value="">';
            var expense_order_amount = '<input class="form-control form-commit data_sum" name="tpl[expense_reimburse_list_multiple][' + num + '][expense_order_amount]" ' +
                'id="expense_order_amount_' + num + '" value="" oninput="transformAmount(this)">';
            var expense_order_num = '<input class="form-control form-commit expense_order_num" name="tpl[expense_reimburse_list_multiple]['+ num + '][expense_order_num]" ' + 'id="expense_order_num_' + num + '" ' + ' ' + 'value="" onkeyup=\'this.value=this.value.replace(/\\D/gi,"")\'>';
            var expense_order_tr_arr = [expense_order_type, expense_order_date, expense_order_reason, expense_order_amount, expense_order_num];
            var expense_order_tr_content = '<tr id="expense_order_tr_' + num + '"><td class="text-center">' + expense_order_tr_arr.join('</td><td  class="text-center">') + '</td></tr>';


            $("#expense_order_table_body").append(expense_order_tr_content);

            $('.expense_order_date_datepicker').datepicker({
                "autoclose": true,
                "format": "yyyy-mm-dd",
                "language": "zh-CN"
            });

            formatData();
            num = num * 1 + 1;
            $("#expense_order_num").val(num);
        });
    })
</script>