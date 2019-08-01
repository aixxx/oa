<?php
if ($entry_data) {
    $paymentFormData = $entry_data->where('field_name', 'payment_amount')->first();
    $paymentFormData = json_decode($paymentFormData->field_value, true);
}
?>


<select class="form-control form-commit col-md-3" name="tpl[payment_amount][type]">
    <option value="">请选择付款金额*</option>
    @if(isset($paymentFormData) && $paymentFormData['type'])
        <option value="人民币¥" @if($paymentFormData['type'] == "人民币¥") selected @endif>人民币¥</option>
        <option value="美元$" @if($paymentFormData['type'] == "美元$") selected @endif>美元$</option>
        <option value="港币HK$" @if($paymentFormData['type'] == "港币HK$") selected @endif>港币HK$</option>
    @else
        <option value="人民币¥" selected>人民币¥</option>
        <option value="美元$">美元$</option>
        <option value="港币HK$">港币HK$</option>
    @endif
</select>
<input type="text" class="form-control col-md-2" name="tpl[payment_amount][amount]" style="margin-left: 3.5%" oninput="transformAmount(this)"
       value="@if(isset($paymentFormData) && $paymentFormData['amount']) {{$paymentFormData['amount']}} @endif">
<input type="text" class="form-control col-md-3" name="tpl[payment_amount][amount_upper]" style="margin-left: 3.5%" placeholder="大写金额"
       value="@if(isset($paymentFormData) && $paymentFormData['amount_upper']) {{$paymentFormData['amount_upper']}} @endif" readonly>

<script>
    window.onload = function () {
        $("label:contains('付款金额*')").width('11.5%');
    };

    function transformAmount(obj) {
        input_obj = $(obj).val();
        $(obj).val(clearNoNum(input_obj));
        if (!/^\d*(\.\d*)?$/.test(input_obj)) {
            alert("付款金额填写错误!");
            return;
        }
        amount_upper = smalltoBIG(input_obj);
        console.log(amount_upper);
        if (amount_upper != '付款金额填写错误!') {
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


    function smalltoBIG(n) {
        var fraction = ['角', '分'];
        var digit = ['零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'];
        var unit = [['元', '万', '亿'], ['', '拾', '佰', '仟']];
        var head = n < 0 ? '欠' : '';
        n = Math.abs(n);

        var s = '';

        for (var i = 0; i < fraction.length; i++) {
            s += (digit[Math.floor(n * 10 * Math.pow(10, i)) % 10] + fraction[i]).replace(/零./, '');
        }
        s = s || '整';
        n = Math.floor(n);

        for (var i = 0; i < unit[0].length && n > 0; i++) {
            var p = '';
            for (var j = 0; j < unit[1].length && n > 0; j++) {
                p = digit[n % 10] + unit[1][j] + p;
                n = Math.floor(n / 10);
            }
            s = p.replace(/(零.)*零$/, '').replace(/^$/, '零') + unit[0][i] + s;
        }
        return head + s.replace(/(零.)*零元/, '元').replace(/(零.)+/g, '零').replace(/^整$/, '零元整');
    }

</script>