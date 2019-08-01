@extends("layouts.main",['title' => $budget_item['title'].'费控条件设定'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">{{ $budget_item['title'] }}费控条件设定</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>费控预算管理</li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $budget_item['title'] }}费控条件设定</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="form-group" style="border: solid 1px;height: 200px;">
                    <div>
                        @foreach($budget_item_condition as $_condition)
                            <label style="margin-left: 20px;">{{ $_condition['condition_name'] }}</label>
                        @endforeach
                    </div>
                    <div class="form-inline">
                        <input type="text" name="condition_name" class="form-control" value="" style="width: 100px;margin-left: 20px;margin-top: 100px;">
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <div class="text-left" style="margin-top: 100px;">
                            <button type="button" class="btn btn-primary btn-sm line-add">+</button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="button" class="btn btn-primary btn-sm line-height-fix">添加条件</button>
                </div>
                <div class="form-inline" id="form-condition">
                    @if($settings)
                        @foreach($settings['settings'] as $_k => $_setting)
                            <div class="form-group condition" id="condition_{{ intval($_k+1) }}">
                                <label class="col-form-label">条件
                                    {{--                            <div class="label_num">1</div>--}}
                                </label>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <select name="condition_type[]" class="form-control" style="width: 100px;">
                                    @foreach($budget_item_condition as $_condition)
                                        <option value="{{ $_condition['id'] }}" @if(isset($_setting[0]['cost_budget_item_condition_id']) && ($_condition['id'] == $_setting[0]['cost_budget_item_condition_id']))
                                        selected
                                                @endif>{{  $_condition['condition_name'] }}</option>
                                    @endforeach
                                </select>

                                <select name="condition_name[]" class="form-control" value="" style="width: 100px;@if(mb_substr($_setting[0]['title'], 0, 2) != '职位')display:none;@endif">
                                    @foreach($position as $_position)
                                        <option value="{{ $_position['position'] }}" @if(isset($_setting[0]['cost_budget_item_condition_name']) && ($_position['position'] == $_setting[0]['cost_budget_item_condition_name']))
                                        selected
                                                @endif>{{  $_position['position'] }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="condition_name[]" class="form-control"
                                       value="@if(isset($_setting[0]['cost_budget_item_condition_name'])){{ $_setting[0]['cost_budget_item_condition_name'] }}@endif"
                                       style="width: 100px;@if(mb_substr($_setting[0]['title'], 0, 2) == '职位')display:none;@endif">
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <select name="condition_type[]" class="form-control" style="width: 100px;">
                                    <option value="算法">算法</option>
                                    {{--                            @foreach($budget_item_condition as $_condition)--}}
                                    {{--                                <option value="{{ $_condition['id'] }}">{{  $_condition['condition_name'] }}</option>--}}
                                    {{--                            @endforeach--}}
                                </select>
                                {{--                        <input type="text" name="condition_type[]" class="form-control" value="算法" style="width: 100px;" readonly="readonly">--}}
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <select name="condition_name[]" class="form-control" value="" style="width: 100px;">
                                    <option value="<" @if(isset($_setting[1]['cost_budget_item_condition_name']) && ($_setting[1]['cost_budget_item_condition_name'] == '<'))
                                    selected
                                            @endif><</option>
                                    <option value="<=" @if(isset($_setting[1]['cost_budget_item_condition_name']) && ($_setting[1]['cost_budget_item_condition_name'] == '<='))
                                    selected
                                            @endif><=</option>
                                    <option value=">" @if(isset($_setting[1]['cost_budget_item_condition_name']) && ($_setting[1]['cost_budget_item_condition_name'] == '>'))
                                    selected
                                            @endif>></option>
                                    <option value=">=" @if(isset($_setting[1]['cost_budget_item_condition_name']) && ($_setting[1]['cost_budget_item_condition_name'] == '>='))
                                    selected
                                            @endif>>=</option>
                                    <option value="=" @if(isset($_setting[1]['cost_budget_item_condition_name']) && ($_setting[1]['cost_budget_item_condition_name'] == '='))
                                    selected
                                            @endif>=</option>
                                </select>
                                {{--                        <input type="text" name="condition_name[]" class="form-control" value="" style="width: 100px;">--}}

                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <select name="condition_type[]" class="form-control" style="width: 100px;">
                                    @foreach($budget_item_condition as $_condition)
                                        <option value="{{ $_condition['id'] }}" @if(isset($_setting[2]['cost_budget_item_condition_id']) && ($_condition['id'] == $_setting[2]['cost_budget_item_condition_id']))
                                        selected
                                                @endif>{{  $_condition['condition_name'] }}</option>
                                    @endforeach
                                </select>
                                <select name="condition_name[]" class="form-control" value="" style="width: 100px;@if(mb_substr($_setting[2]['title'], 0, 2) != '职位')display:none;@endif">
                                    @foreach($position as $_position)
                                        <option value="{{ $_position['position'] }}" @if(isset($_setting[2]['cost_budget_item_condition_name']) && ($_position['position'] == $_setting[2]['cost_budget_item_condition_name']))
                                        selected
                                                @endif>{{  $_position['position'] }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="condition_name[]" class="form-control"
                                       value="@if(isset($_setting[2]['cost_budget_item_condition_name'])){{ $_setting[2]['cost_budget_item_condition_name'] }}@endif"
                                       style="width: 100px;@if(mb_substr($_setting[2]['title'], 0, 2) == '职位')display:none;@endif">
                                <div class="text-left">
                                    <button type="button" style="height: 34px;margin-left: 10px;" class="btn btn-danger delete line-del">删除</button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="form-group condition" id="condition_1">
                            <label class="col-form-label">条件
                                {{--                            <div class="label_num">1</div>--}}
                            </label>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <select name="condition_type[]" class="form-control" style="width: 100px;">
                                @foreach($budget_item_condition as $_condition)
                                    <option value="{{ $_condition['id'] }}">{{  $_condition['condition_name'] }}</option>
                                @endforeach
                            </select>

                            <select name="condition_name[]" class="form-control" value="" style="width: 100px;">
                                @foreach($position as $_position)
                                    <option value="{{ $_position['position'] }}">{{  $_position['position'] }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="condition_name[]" class="form-control" value="" style="width: 100px;display: none;">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <select name="condition_type[]" class="form-control" style="width: 100px;">
                                <option value="算法">算法</option>
                                {{--                            @foreach($budget_item_condition as $_condition)--}}
                                {{--                                <option value="{{ $_condition['id'] }}">{{  $_condition['condition_name'] }}</option>--}}
                                {{--                            @endforeach--}}
                            </select>
                            {{--                        <input type="text" name="condition_type[]" class="form-control" value="算法" style="width: 100px;" readonly="readonly">--}}
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <select name="condition_name[]" class="form-control" value="" style="width: 100px;">
                                <option value="<"><</option>
                                <option value="<="><=</option>
                                <option value=">">></option>
                                <option value=">=">>=</option>
                                <option value="=">=</option>
                            </select>
                            {{--                        <input type="text" name="condition_name[]" class="form-control" value="" style="width: 100px;">--}}

                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <select name="condition_type[]" class="form-control" style="width: 100px;">
                                @foreach($budget_item_condition as $_condition)
                                    <option value="{{ $_condition['id'] }}">{{  $_condition['condition_name'] }}</option>
                                @endforeach
                            </select>
                            <select name="condition_name[]" class="form-control" value="" style="width: 100px;">
                                @foreach($position as $_position)
                                    <option value="{{ $_position['position'] }}">{{  $_position['position'] }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="condition_name[]" class="form-control" value="" style="width: 100px;display: none;">
                            {{--                        <input type="text" name="condition_name[]" class="form-control" value="" style="width: 100px;">--}}
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <div class="text-left">
                                <button type="button" style="height: 34px;margin-left: 10px;" class="btn btn-danger delete line-del">删除</button>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="text-left">
                    <button type="button" class="btn btn-primary btn-sm line-save" data-editId="@if(isset($settings['id'])){{ $settings['id'] }}@endif">保存</button>
                </div>
            </div>
        </div>
    </section>
@endsection
@section("javascript")
    <!-- ================== DATEPICKER SCRIPTS ==================-->
    <script>
        $("button.line-height-fix").on('click', function () {
            var strVar = $("#form-condition").append($(".condition:last").clone(true));
            var num = $(".condition").length;
            strVar.find(".condition:last").attr("id","condition_"+num);
        });

        $("button.line-del").on('click', function () {
            var num = $(".condition").length;
            if(num == 1) {
                alert('条件至少有1个');
            } else {
                $(this).parent().parent().remove();
            }
        });

        $("button.line-add").on('click',function () {
            var cost_budget_item_id = '{{ $budget_item["id"] }}';
            var condition_name = $("[name='condition_name']").val();

            if (!condition_name) {
                alert('类目条件不能为空哦');
                return;
            }
            $.ajax({
                url: '/financialManage/budgetConditionAdd',
                type: 'POST',
                data: {'cost_budget_item_id' : cost_budget_item_id, 'condition_name' : condition_name, '_token':'{{csrf_token()}}'},
                datatype: 'json',
                success: function(response) {
                    alert(response.message);
                    if(response.code = 200) {
                        window.location.href = "{{  route('finance.financialManage.budgetCondition',['id'=>$budget_item['id']])}}";
                    }
                }
            });
        });

        $("button.line-save").on('click',function () {
            var cost_budget_item_id = '{{ $budget_item["id"] }}';

            var condition_type = [];
            var condition_name = [];
            var condition_setting = [];
            $("[name='condition_type[]']").each(function(){
                condition_type.push($(this).val());
                condition_name.push($(this).find("option:selected").text());
            });
            $("[name='condition_name[]']").each(function(){
                if($(this).css('display') != 'none') {
                    condition_setting.push($(this).val());
                }
            });
            var data = {
                'cost_budget_item_id' : cost_budget_item_id,
                'condition_type' : condition_type,
                'condition_name' : condition_name,
                'condition_setting' : condition_setting,
                '_token':'{{csrf_token()}}',
                'id': $(this).attr('data-editId')
            };
            // console.log(condition_type)
            // console.log(condition_name)
            // console.log(condition_setting)
            // return false;

            $.ajax({
                url: '/financialManage/budgetConditionSetting',
                type: 'POST',
                data: data,
                datatype: 'json',
                success: function(response) {
                    alert(response.message);
                    if(response.code == 200) {
                        window.location.href = "{{  route('finance.financialManage.budgetCondition',['id'=>$budget_item['id']])}}";
                    }
                }
            });
        });

        $("[name='condition_type[]']").on('change',function(){
            if($(this).find('option:selected').text() == '职位') {
                $(this).next().show();
                $(this).next().next().css('display','none');
            } else {
                $(this).next().css('display','none');
                $(this).next().next().show();
            }
        });

    </script>
@endsection

