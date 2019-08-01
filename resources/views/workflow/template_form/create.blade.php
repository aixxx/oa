@extends('layouts.main',['title' => '添加表单控件'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1>添加表单控件</h1>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div class="card-body">
                <form action="{{route('workflow.template_form.store')}}" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="type">字段类型</label>
                        <select id="type" class="form-control" name="field_type">
                            <option value="text">文本输入框</option>
                            <option value="div">文本显示框</option>
                            <option value="select">下拉框</option>
                            <option value="radio">单选框</option>
                            <option value="checkbox">多选框</option>
                            <option value="textarea">多行文本框</option>
                            <option value="date">日期选择器</option>
                            <option value="date_time">时间选择器</option>
                            <option value="date_interval">求两个日期间隔天数</option>
                            <option value="time_sub">求时间小时差</option>
                            <option value="time_sub_by_hour">按小时求时间小时差</option>
                            <option value="file">上传文件</option>
                            <option value="hidden">隐藏文本</option>
                            <option value="sum">字段求和</option>
                            <option value="big_money">金额转大写</option>
                            <option value="holiday_type">请假类型</option>
                            @foreach($key_info as $key => $info)
                                <option value="{{$key}}" data-special="special" data-is-hidden="{{$info['is_hidden']}}">{{$info['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>控件名称</label>
                        <input type="text" class="form-control" name="field_name" placeholder="控件名称">
                    </div>

                    <div class="form-group">
                        <label>字段名</label>
                        <input type="text" class="form-control" name="field" placeholder="字段名">
                    </div>
                    <div class="form-group">
                        <label>可选字段值</label>
                        <textarea class="form-control" name="field_value" placeholder="字段值" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label>默认值</label>
                        <input type="text" class="form-control" name="field_default_value" placeholder="默认值">
                    </div>

                    <div class="form-group">
                        <label>placeholder</label>
                        <input type="text" class="form-control" name="placeholder" placeholder="placeholder">
                    </div>
                    <div class="form-group">
                        <label>额外CSS样式类</label>
                        <input class="form-control" name="field_extra_css" placeholder="默认为空" value="{{$template_form->field_extra_css}}">
                    </div>
                    <div class="form-group">
                        <label>单位</label>
                        <input class="form-control" name="unit" placeholder="单位">
                    </div>

                    <div class="form-group">
                        <label>排序</label>
                        <input type="text" class="form-control" name="sort" placeholder="排序" value="50">
                    </div>
                    <div class="form-group">
                        <label>栅格化(n/12)</label>
                        <input type="text" class="form-control" name="location" placeholder="栅格化" value="4">
                    </div>
                    <div class="form-group">
                        <label>是否必填（0选填，1，单控件必填，2，3，4...相同值为大组，大组选填，组内必填）</label>
                        <input type="text" class="form-control" name="required" placeholder="控件是否必填" value="0">
                    </div>
                    <div class="form-group">
                        <label>是否在待办事项中显示（0，1）</label>
                        <input type="text" class="form-control" name="show_in_todo" placeholder="是否在待办事项中显示" value="0">
                    </div>
                    <div class="form-group">
                        <label>字段长度</label>
                        <input type="text" class="form-control" name="length" placeholder="长度" value="0">
                    </div>
                    {{csrf_field()}}
                    <input type="hidden" name="template_id" value="{{Request::get('template_id')}}">

                    <button type="submit" class="btn btn-primary">确定</button>
                </form>
            </div>
        </div>
    </section>
@endsection
@section('javascript')
    <script type="text/javascript">
        $('#type').on('change', function () {
            var option = $('#type :selected');
            var val = $(this).val();
            if (option.attr('data-special') == 'special') {
                $('input[name="field_name"]').val(option.text());
                $('input[name="field"]').val(val).attr('readonly', 'true');
                $('textarea[name="field_value"]').attr('readonly', 'true');
                $('input[name="field_default_value"]').attr('readonly', 'true');
            } else if (val == 'div') {
                $('input[name="field_name"]').val('');
                $('input[name="field"]').val('div').attr('readonly', 'true');
                $('textarea[name="field_value"]').attr('readonly', 'true');
                $('input[name="field_default_value"]').attr('readonly', 'true');
            } else if (val == 'sum') {
                $('input[name="field_name"]').val('');
                $('input[name="field"]').val('').removeAttr('readonly');
                $('textarea[name="field_value"]').text('[name="field_name"],[name="field_name2"],[name="field_name3"]').removeAttr('readonly');
                $('input[name="field_default_value"]').removeAttr('readonly');
            } else if (val == 'time_sub_by_hour') {
                $('input[name="field_name"]').val('');
                $('input[name="field"]').val('time_sub_by_hour').removeAttr('readonly');
                $('textarea[name="field_value"]').text('[name="tpl[field_name]"],[name="tpl[field_name2]"]').removeAttr('readonly');
                $('input[name="field_default_value"]').removeAttr('readonly');
            } else if (val == 'time_minus') {
                $('input[name="field_name"]').val('');
                $('input[name="field"]').val('').removeAttr('readonly');
                $('textarea[name="field_value"]').text('[name="field_name"],[name="field_name2"],[name="field_name3"]').removeAttr('readonly');
                $('input[name="field_default_value"]').removeAttr('readonly');
            } else if (val == 'big_money') {
                $('input[name="field_name"]').val('');
                $('input[name="field"]').val('').removeAttr('readonly');
                $('textarea[name="field_value"]').text('[name="field_name"]').removeAttr('readonly');
                $('input[name="field_default_value"]').removeAttr('readonly');
            } else {
                $('input[name="field_name"]').val('');
                $('input[name="field"]').val('').removeAttr('readonly');
                $('textarea[name="field_value"]').removeAttr('readonly');
                $('input[name="field_default_value"]').removeAttr('readonly');
            }
        });
    </script>
@endsection