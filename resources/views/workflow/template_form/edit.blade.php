@extends('layouts.main',['title' => '编辑模板'])

@section('content')
    <div class="row page-content container-fluid">
        <div class="col-md-12 col-lg-10">
            <div class="card">
                <h5 class="card-header">编辑表单控件</h5>
                <div class="card-body">
                    <form action="{{$template_form->id?route('workflow.template_form.update',['id'=>$template_form->id]):route('workflow.template_form.store')}}"
                          method="POST"
                          enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="exampleInputPassword1">字段类型</label>
                            <select id="type" class="form-control" name="field_type">
                                <option value="text" @if($template_form->field_type=='text') selected="selected" @endif >文本输入框</option>
                                <option value="div" @if($template_form->field_type=='div') selected="selected" @endif >文本显示框</option>
                                <option value="select" @if($template_form->field_type=='select') selected="selected" @endif>下拉框</option>
                                <option value="radio" @if($template_form->field_type=='radio') selected="selected" @endif>单选</option>
                                <option value="checkbox" @if($template_form->field_type=='checkbox') selected="selected" @endif>多选</option>
                                <option value="textarea" @if($template_form->field_type=='textarea') selected="selected" @endif>多行文本框</option>
                                <option value="date" @if($template_form->field_type=='date') selected="selected" @endif>日期选择器</option>
                                <option value="date_time" @if($template_form->field_type=='date_time') selected="selected" @endif>时间选择器</option>
                                <option value="date_time_hour" @if($template_form->field_type=='date_time_hour') selected="selected" @endif>时间选择器(精确到小时)
                                </option>
                                <option value="date_interval" @if($template_form->field_type=='date_interval') selected="selected" @endif>求两个日期间隔天数</option>
                                <option value="date_time_sub" @if($template_form->field_type=='date_time_sub') selected="selected" @endif>求时间小时差</option>
                                <option value="time_sub_by_hour" @if($template_form->field_type=='time_sub_by_hour') selected="selected" @endif>按小时求时间小时差
                                </option>
                                <option value="file" @if($template_form->field_type=='file') selected="selected" @endif>文件</option>
                                <option value="hidden" @if($template_form->field_type=='hidden') selected="selected" @endif>隐藏文本</option>
                                <option value="sum" @if($template_form->field_type=='sum') selected="selected" @endif>金额字段求和</option>
                                <option value="readonly_input" @if($template_form->field_type=='readonly_input') selected="selected" @endif>只读输入框</option>
                                <option value="plain_sum" @if($template_form->field_type=='plain_sum') selected="selected" @endif>普通字段求和</option>
                                <option value="big_money" @if($template_form->field_type=='big_money') selected="selected" @endif>金额转大写</option>
                                <option value="holiday_type" @if($template_form->field_type=='holiday_type') selected="selected" @endif>请假类型</option>
                                @foreach($key_info as $key => $info)
                                    <option value="{{$key}}" data-special="special" data-is-hidden="{{$info['is_hidden']}}"
                                            @if($template_form->field_type==$key) selected="selected"
                                            @endif>{{$info['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>控件名称</label>
                            <input type="text" class="form-control" name="field_name" placeholder="控件名称" value="{{$template_form->field_name}}">
                        </div>

                        <div class="form-group">
                            <label>字段名</label>
                            <input type="text" class="form-control" name="field" placeholder="字段名" value="{{$template_form->field}}">
                        </div>

                        <div class="form-group">
                            <label>字段值</label>
                            <textarea class="form-control" name="field_value" placeholder="单选、多选等类型的可供选择字段值，每行一个"
                                      rows="3">{{$template_form->field_value}}</textarea>
                        </div>

                        <div class="form-group">
                            <label>默认值</label>
                            <input type="text" class="form-control" name="field_default_value" placeholder="默认值"
                                   value="{{$template_form->field_default_value}}">
                        </div>
                        <div class="form-group">
                            <label>placeholder</label>
                            <input type="text" class="form-control" name="placeholder" placeholder="placeholder"
                                   value="{{$template_form->placeholder}}">
                        </div>
                        <div class="form-group">
                            <label>额外CSS样式类</label>
                            <input class="form-control" name="field_extra_css" placeholder="默认为空" value="{{$template_form->field_extra_css}}">
                        </div>

                        <div class="form-group">
                            <label>单位</label>
                            <input class="form-control" name="unit" placeholder="单位" value="{{$template_form->unit}}">
                        </div>

                        <div class="form-group">
                            <label>排序</label>
                            <input type="text" class="form-control" name="sort" placeholder="排序" value="{{$template_form->sort??50}}">
                        </div>
                        <div class="form-group">
                            <label>栅格化(n/12)</label>
                            <input type="text" class="form-control" name="location" placeholder="栅格化" value="{{$template_form->location??4}}">
                        </div>
                        <div class="form-group">
                            <label>是否必填（0选填，1，单控件必填，2，3，4...相同值为大组，大组选填，组内必填）</label>
                            <input type="text" class="form-control" name="required" placeholder="控件是否必填" value="{{$template_form->required??0}}">
                        </div>
                        <div class="form-group">
                            <label>是否在待办事项中显示（0，1）</label>
                            <input type="text" class="form-control" name="show_in_todo" placeholder="是否在待办事项中显示" value="{{$template_form->show_in_todo??0}}">
                        </div>
                        <div class="form-group">
                            <label>字段长度</label>
                            <input type="text" class="form-control" name="length" placeholder="长度" value="{{$template_form->length??0}}">
                        </div>
                        {{csrf_field()}}
                        {{$template_form->id?method_field('PUT'):''}}
                        <input type="hidden" name="template_id" value="{{$template_id}}">
                        <div class="card-footer bg-light">
                            <button type="submit" class="btn btn-primary btn-block">确定</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('javascript')
    <script type="text/javascript">
        $('#type').on('change', function () {
            var option = $('#type :selected');
            var val = $(this).val();
            if (option.attr('data-special') == 'special') {
                $('input[name="field_name"]').val(option.text());
                $('input[name="field"]').val(val).attr('readonly', 'true');
                $('textarea[name="field_value"]').text('').attr('readonly', 'true');
                $('input[name="field_default_value"]').attr('readonly', 'true');
            } else if (val == 'div') {
                $('input[name="field_name"]').val('');
                $('input[name="field"]').val('div').attr('readonly', 'true');
                $('textarea[name="field_value"]').text('').attr('readonly', 'true');
                $('input[name="field_default_value"]').attr('readonly', 'true');
            } else if (val == 'sum' || val == 'plain_sum' || val == 'readonly_input') {
                $('input[name="field_name"]').val('');
                $('input[name="field"]').val('').removeAttr('readonly');
                $('textarea[name="field_value"]').text('[name="tpl[field_name]"],[name="tpl[field_name2]"],[name="tpl[field_name3]"]').removeAttr('readonly');
                $('input[name="field_default_value"]').removeAttr('readonly');
            } else if (val == 'date_time_sub') {
                $('input[name="field_name"]').val('');
                $('input[name="field"]').val('date_time_sub').removeAttr('readonly');
                $('textarea[name="field_value"]').text('[name="tpl[field_name]"],[name="tpl[field_name2]"]').removeAttr('readonly');
                $('input[name="field_default_value"]').removeAttr('readonly');
            } else if (val == 'time_sub_by_hour') {
                $('input[name="field_name"]').val('');
                $('input[name="field"]').val('time_sub_by_hour').removeAttr('readonly');
                $('textarea[name="field_value"]').text('[name="tpl[field_name]"],[name="tpl[field_name2]"]').removeAttr('readonly');
                $('input[name="field_default_value"]').removeAttr('readonly');
            } else if (val == 'big_money') {
                $('input[name="field_name"]').val('');
                $('input[name="field"]').val('').removeAttr('readonly');
                $('textarea[name="field_value"]').text('[name="tpl[field_name]"]').removeAttr('readonly');
                $('input[name="field_default_value"]').removeAttr('readonly');
            } else if (val == 'company_change_info') {
                $('input[name="field_name"]').val('');
                $('input[name="field"]').val('company_change_info').attr('readonly', 'true');
                $('textarea[name="field_value"]').text('').attr('readonly', 'true');
                $('input[name="field_default_value"]').attr('readonly', 'true');
            } else if (val == 'contract_subject_info') {
                $('input[name="field_name"]').val('');
                $('input[name="field"]').val('contract_subject_info').attr('readonly', 'true');
                $('textarea[name="field_value"]').text('').attr('readonly', 'true');
                $('input[name="field_default_value"]').attr('readonly', 'true');
            } else if (val == 'contract_no_type') {
                $('input[name="field_name"]').val('');
                $('input[name="field"]').val('contract_no_type').attr('readonly', 'true');
                $('textarea[name="field_value"]').text('').attr('readonly', 'true');
                $('input[name="field_default_value"]').attr('readonly', 'true');
            } else if (val == 'expense_reimburse_list') {
                $('input[name="field_name"]').val('');
                $('input[name="field"]').val('expense_reimburse_list').attr('readonly', 'true');
                $('textarea[name="field_value"]').text('').attr('readonly', 'true');
                $('input[name="field_default_value"]').attr('readonly', 'true');
            } else if (val == 'file') {
                $('input[name="field_name"]').val('');
                $('input[name="field"]').val('file_upload').attr('readonly', 'true');
                $('textarea[name="field_value"]').text('').attr('readonly', 'true');
                $('input[name="field_default_value"]').attr('readonly', 'true');
            } else if (val == 'fixed_asset_list') {
                $('input[name="field_name"]').val('');
                $('input[name="field"]').val('fixed_asset_list').attr('readonly', 'true');
                $('textarea[name="field_value"]').text('').attr('readonly', 'true');
                $('input[name="field_default_value"]').attr('readonly', 'true');
            } else if (val == 'asset_apply') {
                $('input[name="field_name"]').val('');
                $('input[name="field"]').val('asset_apply').attr('readonly', 'true');
                $('textarea[name="field_value"]').text('').attr('readonly', 'true');
                $('input[name="field_default_value"]').attr('readonly', 'true');
            } else if (val == 'payment_amount') {
                $('input[name="field_name"]').val('');
                $('input[name="field"]').val('payment_amount').attr('readonly', 'true');
                $('textarea[name="field_value"]').text('').attr('readonly', 'true');
                $('input[name="field_default_value"]').attr('readonly', 'true');
            } else if (val == 'invoice_sign') {
                $('input[name="field_name"]').val('');
                $('input[name="field"]').val('invoice_sign').attr('readonly', 'true');
                $('textarea[name="field_value"]').text('').attr('readonly', 'true');
                $('input[name="field_default_value"]').attr('readonly', 'true');
            } else {
                $('input[name="field_name"]').val('');
                $('input[name="field"]').val('').removeAttr('readonly');
                $('textarea[name="field_value"]').text('').removeAttr('readonly');
                $('input[name="field_default_value"]').removeAttr('readonly');
            }
        });
        $(function () {
            var option = $('#type :selected');
            if (option.attr('data-special') == 'special' || option.attr('data-special') == 'div') {
                $('input[name="field"]').attr('readonly', 'true');
                $('textarea[name="field_value"]').attr('readonly', 'true');
                $('input[name="field_default_value"]').attr('readonly', 'true');
            }
        })
    </script>
@endsection