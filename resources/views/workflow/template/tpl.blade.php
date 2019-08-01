<link rel="stylesheet" href="/static/vendor/workflow/index.css">
<link rel="stylesheet" href="/static/vendor/bootstrap-timepicker/bootstrap-datetimepicker.min.css">
<link rel="stylesheet" href="/static/vendor/autocomplete/autocomplete.css">
<style>
    @media screen and (max-width: 576px) {
        #tpl_model .field_type_hidden {
            display: none;
        }
    }
</style>
<div id="tpl_model" class="form-group row {{ !$entry||$entry->isDraft()?'':'none_edit_able'}}">
    @foreach($template_forms as $v)
        <?php
        if ($entry_data && $entry_data->where('field_name', $v->field)->first()) {
            $field_value = $entry_data->where('field_name', $v->field)->first()->field_value;
        } else {
            $field_value = $v->field_default_value;
        }
        $expense_order_num = ($v->field_type == 'expense_reimburse_list' && $entry_data) ? $entry_data->where
        ('field_name', 'expense_order_num')->first()->field_value : 0;
        ?>
        <div class="form-group tpl_div col-sm-{{$v->location}} col-xs-12 {{$v->field_extra_css}}
        {{$v->isHideFieldNameLabel($key_info)?' label_hidden ':''}}
        {{' field_type_'.$v->field_type}}"
             data-location="{{$v->location}}"
             data-required="{{$v->required}}">
            <label class="form-label label_hidden">
                {{$entry&&!$entry->isDraft()?trim($v->field_name?:$v->placeholder,"*"):($v->field_name?:$v->placeholder)}}
            </label>
            @if(file_exists(sprintf('%s/%s.blade.php', resource_path('views/workflow/template/widget'), $v->field_type)))
                @include("workflow.template.widget.$v->field_type", [
                    'v' => $v,
                    'field_value' =>$field_value,
                    'entry_data' => $entry_data,
                    'apply_basic_info' => $apply_basic_info,
                    'key_info' => $key_info,
                    'expense_order_num' => $expense_order_num??null,
                    'cnt' => $cnt ?? null
                ])
            @elseif(isset($key_info[$v->field]['is_hidden']) && !$key_info[$v->field]['is_hidden'])
                <input type="text" class="form-control form-commit" name="tpl[{{$v->field}}]"
                       value="{{ $apply_basic_info[$v->field]??'' }}"
                       readonly="readonly">
            @else
                @includeIf('workflow.template.widget.hidden', [
                    'v' => $v,
                    'field_value' => $field_value,
                    'entry_data' => $entry_data,
                    'apply_basic_info' => $apply_basic_info,
                    'key_info' => $key_info,
                    'cnt' => $cnt ?? null
                ])
            @endif

        </div>
    @endforeach
</div>

<script src="/static/vendor/moment/min/moment.min.js"></script>
<script src="/static/vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<script src="/static/vendor/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="/static/vendor/bootstrap-timepicker/bootstrap-datetimepicker.min.js"></script>
<script src="/static/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="/static/js/components/bootstrap-datepicker-init.js"></script>
<script src="/static/js/components/bootstrap-date-range-picker-init.js"></script>
<script src="/static/js/components/changeNumberForCN.js"></script>
<script src="{{asset('js/fileinput/fileinput.js')}}"></script>

<script>
    $('.datepicker').datepicker({
        "autoclose": true,
        "format": "yyyy-mm-dd",
        "language": "zh-CN"
    });

    $('.datetimepicker').datetimepicker({
        "format": "yyyy-mm-dd hh:ii:00",
        "language": "zh-CN",
        "autoclose": true,
        "sideBySide": true,
        "minView": "0"
    });

    $('.datetimepicker_hour').datetimepicker({
        "format": "yyyy-mm-dd hh:00:00",
        "language": "zh-CN",
        "autoclose": true,
        "sideBySide": true,
        "minView": "1"
    });

    if ({{ !$entry||$entry->isDraft()?'0':'1'}} ) {
        $("#tpl_model input,#tpl_model textarea").attr('readonly', 'true');
        $("#tpl_model textarea,#tpl_model input,#tpl_model select").attr('disabled', 'disabled');
        $("#tpl_model .file-input-ajax-new").hide();
        $("#tpl_model .preview-div .preview-fileinput-remove").hide();
    }
    var get_common_val = function (data_required) {
        var el = data_required.find(".form-commit");
        if (el.attr('type') == 'radio' || el.attr('type') == 'checkbox') {
            el = data_required.find(".form-commit:checked");
        }
        if (el.attr('type') == 'file') {
            return get_upload_file_ids();
        }

        if (el.attr('type') == 'contract_subject_info') {
            return get_contract_subject_info_json();
        }

        var val = el.val() ? el.val() : '';
        return val;
    };
    var smalltoBIG = function (n) {
        var fraction = ['角', '分'];
        var digit = ['零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'];
        var unit = [['元', '万', '亿'], ['', '拾', '佰', '仟']];
        var head = n < 0 ? '欠' : '';
        n = Math.abs(n);

        var s = '';

        for (var i = 0; i < fraction.length; i++) {
            s += (digit[Math.floor((n * 10 * Math.pow(10, i)).toFixed(fraction.length - i - 1)) % 10] +
                fraction[i]).replace(/零./, '');
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
    };
    var can_commit_tpl = function () {
        if ($('.input_required').length > 0) {
            return false;
        } else {
            return true;
        }
    };
    var get_upload_file_ids = function () {
        if ($(".file_upload").length > 0) {
            var ids = new Array();
            var i = 0;
            $("[data-file-id]").each(function () {
                ids[i] = $(this).attr('data-file-id');
                i++;
            });
            if (ids.toString() != '') {
                return '&tpl[' + $(".file_upload").attr('name') + ']=' + ids.toString();
            } else {
                return '';
            }
        } else {
            return '';
        }
    };

    var get_contract_subject_info_json = function () {
        var cnt = $('#tr_num').val();
        var info = new Array();
        if (cnt > 1) {
            var i = 0;
            while (cnt > 1) {
                cnt--;
                var temp = new Array();
                var radio_name = 'contract_subject_type_' + cnt;
                temp[0] = $('#contract_subject_privity_' + cnt).val();
                temp[1] = $('input[name=' + radio_name + ']:checked').val();
                temp[2] = $('#contract_subject_full_name_' + cnt).val();
                info[i] = temp;
                i++;
            }
            return '&tpl[contract_subject_info]=' + JSON.stringify(info.reverse());
        }

        return '';
    };

    /* var get_contract_old_no = function () {
         var vv = $("#contract_no_type").val();
         return 'old' == vv ? '&tpl[contract_old_list]=' + $("#contract_old_list").val() : '';
     };*/

    var group_check = function (required) {
        var count = 0;
        var not_empty = 0;
        $('[data-required=' + required + ']').each(function () {
            if (get_common_val($(this)) != '') {
                not_empty = not_empty + 1;
            }
            count = count + 1;
        });
        $('[data-required=' + required + ']').each(function () {
            if (not_empty == 0 || not_empty == count || get_common_val($(this)) != '') {
                $(this).removeClass('input_required');
            } else {
                $(this).addClass('input_required');
            }
        });

    };
    var check_data = function (self) {
        var required = self.attr('data-required') * 1;
        if (required == 1) {
            if (get_common_val(self) != '') {
                self.removeClass('input_required');
            } else {
                self.addClass('input_required');
            }
        } else if (required > 1) {
            group_check(required);
        } else {

        }
    };
    $('[data-required]').on('change', function () {
        $('[data-required]').each(function () {
            check_data($(this));
        })
    });

    var clearNoNum = function (num) {
        num = num.replace(/[^\d.]/g, "");  //清除“数字”和“.”以外的字符
        num = num.replace(/\.{2,}/g, "."); //只保留第一个. 清除多余的
        num = num.replace(".", "$#$").replace(/\./g, "").replace("$#$", ".");
        num = num.replace(/^(\-)*(\d+)\.(\d\d).*$/, '$1$2.$3');//只能输入两个小数
        if (num.indexOf(".") < 0 && num != "") {//以上已经过滤，此处控制的是如果没有小数点，首位不能为类似于 01、02的金额
            num = parseFloat(num);
        }
        return num;
    };

    /**
     * 分转元
     */
    var f2y = function(num) {
        if (typeof num !== "number" || isNaN(num)) return null;
        return (num / 100).toFixed(2);
    };

    /**
     * 格式化整数输入
     */
   var format2Integer = function(inputStr) {
        inputStr = inputStr.replace(/[^\d]/g, "");
        return inputStr ? parseInt(inputStr) : 0;
    };

    /**
     * 格式化金额输入
     */
    var format2Amount = function(inputStr) {
        return clearNoNum(inputStr);
    };

    $('.amount input').bind('input propertychange', function () {
        $(this).val(clearNoNum($(this).val()));
    });

    var formatData = function () {
        //金额求和，精确到小数点后两位
        $("#tpl_model .tpl_sum").each(function () {
            var self = $(this);
            var select = $('.data_sum');
            $(select).on('change', function () {
                var sum = 0;
                $(select).each(function () {
                    sum = sum + $(this).val() * 1;
                });
                self.val(sum.toFixed(2)).change();
            });
        });

        //普通数字求和，精确到个位数
        $("#tpl_model .tpl_plain_sum").each(function () {
            var self = $(this);
            var select = $('.expense_order_num');
            $(select).on('change', function () {
                var sum = 0;
                $(select).each(function () {
                    sum = sum + $(this).val() * 1;
                });
                self.val(sum).change();
            });
        });

//金额大写
        $("#tpl_model .tpl_big_money").each(function () {
            var self = $(this);
            var select = self.attr('data-based');
            $(select).on('change', function () {
                self.val(smalltoBIG($(this).val()));
            });
        });
    };

    $(function () {
        $("#tpl_model .tpl_sum").each(function () {
            var self = $(this);
            var select = self.attr('data-sum');
            $(select).on('change', function () {
                var sum = 0;
                $(select).each(function () {
                    sum = sum + $(this).val() * 1;
                });
                self.val(sum.toFixed(2)).change();
            });
        });
        $("#tpl_model .tpl_big_money").each(function () {
            var self = $(this);
            var select = self.attr('data-based');
            $(select).on('change', function () {
                self.val(smalltoBIG($(this).val()));
            });
        });

        $("#tpl_model .date_time_sub").each(function () {
            var self = $(this);
            var select = self.attr('data-based');
            $(select).on('change', function () {
                var times = new Array();
                var i = 0;
                var type = '';
                var user_id = $("#user_id").val();
                var self_change = $(this);
                $(select).each(function () {
                    if ($(this).hasClass('datepicker') ||
                        $(this).hasClass('datetimepicker') ||
                        $(this).hasClass('datetimepicker_hour')) {
                        times[i] = $(this).val();
                        i++;
                    } else {
                        type = $(this).val();
                    }
                });
                if (times[0] > times[1] && times[1] != '') {
                    alert('开始时间比结束时间大');
                    self_change.val('');
                    return;
                }

                if (times[0] != '' && times[1] != '') {
                    callGetAjax(self, {times: times, type: type, user_id: user_id}, function (response) {
                        if (response.status == 'error') {
                            self.val('');
                            self_change.val('');
                            alert(response.message);
                            return;
                        }
                        self.val(response.data.hour + response.data.unit);
                    });
                }
            });
        });

        //合同签约方信息
        $("#add_subject").on('click', function () {
            var num = $("#tr_num").val();
            var droplist_options = ['甲方', '乙方', '丙方', '丁方', '戊方', '己方'];
            var options_str = '';

            for (var i in droplist_options) {
                options_str += '<option value="' + droplist_options[i] + '">' + droplist_options[i] + '</option>';
            }

            var droplist = '<select class="form-control form-commit" id="contract_subject_privity_' +
                num +
                '">' +
                options_str +
                '</select>';

            var radio_button = '<input class="form-control form-commit" name="contract_subject_type_' + num
                + '" id="radio_company_' +
                num +
                '" type="radio" value="公司" checked><label for="radio_company_' +
                num +
                '">公司</label>';
            radio_button += '<input class="form-control form-commit" name="contract_subject_type_' +
                num + '" id="radio_personal_' +
                num + '" type="radio" value="个人"><label for="radio_personal_' +
                num + '">个人</label>';
            var input_text = '<input class="form-control form-commit" id="contract_subject_full_name_' +
                num +
                '" value="">';
            var operation = '<input id="delete_tr_' + num + '" type="button" value="删除" data-base="' + num + '">';
            var tr_arr = [droplist, radio_button, input_text, operation];
            var tr_content = '<tr id="tr_' + num + '"><td>' + tr_arr.join('</td><td>') + '</td></tr>';

            $("#table_body").append(tr_content);

            $("#delete_tr_" + num).on('click', function () {
                $("#tr_" + (num - 1)).remove();
            });

            num = num * 1 + 1;
            $("#tr_num").val(num);
        });

        /* //合同编号
         $("#contract_no_type").on('change', function () {
             var v = $(this).val();
             if (v == 'old') {
                 callGetAjax($(this), {type: v}, function (response) {
                     var option = '';
                     $.each(response.data, function (k, v) {
                         option += "<option value='" + v.number + "'>" + v.number + "</option>";
                     });
                     var str = '<select style="width:30%;" name="contract_old_list" id="contract_old_list" class="form-control form-commit">' +
                         option +
                         '</select>';
                     $("#contract_no_span").html(str)
                 });
             } else {
                 $("#contract_no_span").html('编号自动生成');
             }

         });*/

        //合同生效日
        $("select[name='tpl[contract_start_date_select]']").on('change', function () {
            var v = $(this).val();
            if (v == '盖章日有效') {
                $("[name='tpl[contract_start_date]']").hide();
            } else {
                $("[name='tpl[contract_start_date]']").show();
            }
        });

        //合同终止日
        $("select[name='tpl[contract_end_date_select]']").on('change', function () {
            var v = $(this).val();
            if (v == '无明确终止日') {
                $("[name='tpl[contract_end_date]']").hide();
            } else {
                $("[name='tpl[contract_end_date]']").show();
            }
        });

        $(".preview-div .preview-fileinput-remove").on('click', function () {
            $(this).parent().remove();
        });

        formatData();
    });

    $('[data-required]').trigger('change');
</script>