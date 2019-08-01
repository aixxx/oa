<!--
销假流程控件
-->

<select name="tpl[resumption_leave_list]" class="form-control form-commit" id="resumption_leave_list">
    <option value="">请选择</option>
    @foreach($apply_basic_info['resumption_leave_list'] as $entry_key =>$entry_title)
        <option value="{{$entry_key}}" @if($field_value==$entry_key) selected @endif>{{$entry_title}}</option>
    @endforeach
</select>
<br>
<a id ="entry_data"></a>
<br>
<div style="background-color: #f0f2f5; margin-left: 15%;margin-right: 3%;" id="entry_show">
    <div style="margin-left: 15%;font-size: .575rem!important;margin-bottom: 1.2em;">

        @if(!in_array(\Route::currentRouteName(),['workflow.entry.show','workflow.proc.show']))
            {{--<br>--}}
            {{--<div class="row">--}}
                {{--<div class="col-md-3 text-left">请假类型：</div>--}}
                {{--<div class="col-md-9">法定年假 </div>--}}
            {{--</div>--}}
            {{--<div class="row">--}}
                {{--<div class="col-md-3 text-left">请假开始时间：</div>--}}
                {{--<div class="col-md-3">2018-9-27 09:00:00 </div>--}}
                {{--<div class="col-md-3 text-left">请假结束时间：</div>--}}
                {{--<div class="col-md-3">2018-9-27 09:00:00 </div>--}}
            {{--</div>--}}
            {{--<div class="row">--}}
                {{--<div class="col-md-3 text-left">请假时长：</div>--}}
                {{--<div class="col-md-9">24 小时 </div>--}}
            {{--</div>--}}
            {{--<div class="row">--}}
                {{--<div class="col-md-3 text-left">请假备注：</div>--}}
                {{--<div class="col-md-9">国庆节请事假三天，批准望 </div>--}}
            {{--</div>--}}
            {{--<br>--}}
            {{--<br>--}}
        @endif

    </div>
</div>


<script>
    $('#resumption_leave_list').change(function () {
        var entryId = $("option:selected",this).val();
        ajaxEntry(entryId);
    });


    $(function() {
        // do something
        var entryId = "{{ $field_value }}";
        ajaxEntry(entryId);

    });

    function ajaxEntry(entryId) {
        if (entryId) {
            $("#entry_data").attr('data-href', "/workflow/entry/data/" + entryId);
            callGetAjax($("#entry_data"), null, function (response) {
                console.log(response.data);

                $('#entry_show').empty();

                var html = '';
                html += '<div style="margin-left: 15%;font-size: .575rem!important;">';
                html += '<br>';

                html += '<div class="row"><div class="col-md-3 text-left">请假类型：</div><div class="col-md-9">' + response.data.holiday_type + ' </div></div>';
                html += '<div class="row"><div class="col-md-3 text-left">请假开始时间：</div><div class="col-md-3">' + response.data.date_begin + ' </div><div class="col-md-3 text-left">请假结束时间：</div><div class="col-md-3">' + response.data.date_end + ' </div></div>';
                html += '<div class="row"><div class="col-md-3 text-left">请假时长：</div><div class="col-md-9">' + response.data.date_time_sub + ' </div></div>';
                html += '<div class="row"><div class="col-md-3 text-left">请假备注：</div><div class="col-md-9">' + response.data.cause + ' </div></div>';

                html += '<br>';
                html += '<br>';

                html += '</div>';

                $('#entry_show').append(html);
                $("input[name='tpl[resumption_leave_length]']").attr("readonly", "readonly");
                $("input[name='tpl[resumption_leave_length]']").val(response.data.date_time_sub.match(/[0-9]+/g));
            })
        } else {
            $('#entry_show').empty();
        }
    }

</script>
