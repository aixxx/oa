@extends("layouts.main",['title' => '费控模板管理'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">{{ $category['title'] }}费控点设置</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>费控预算管理</li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $category['title'] }}费控点设置</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="form-inline" style="margin-bottom: 18px;">
                    <div class="form-group">
                        <label class="col-form-label">维度选择&nbsp;&nbsp;</label>
                        <div style="border: solid 1px;height: 200px; width: 800px;display:inline-block;">
                            @foreach($dimension as $_dimension)
                                {{ $_dimension['title'] }}&nbsp;
                                <input type="checkbox" data-title="{{$_dimension['title']}}" class="dimension" value="{{$_dimension['id']}}"
                                       name="dimension[]"@if(in_array($_dimension['id'], $items)) checked @endif>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="button" class="btn btn-primary btn-sm line-height-fix">添加费控点</button>
                </div>
                <div class="table-responsive" id="form-condition">
                    @if(count($budget_item))
                        @foreach($budget_item as $_budget)
                            <table class="table table-hover table-outline table-vcenter text-nowrap card-table" data-id="{{$_budget['id']}}">
                                <thead>
                                <tr style="background:#E0E0E0;">
                                    <th style="width:50px;">费控名称&nbsp;<input type="text" class="point_name" style="width: 100px;"
                                                                             name="point_name[]" value="{{ $_budget['point_name'] }}"></th>
                                    <th style="width:50px;">维度</th>
                                    <th style="width:500px;">条件</th>
                                    <th style="width:100px;">限制单价&nbsp;<input type="text" class="limit_price" style="width: 100px;"
                                                                              name="limit_price[]"  value="{{ $_budget['limit_price'] }}">&nbsp;元</th>
                                    <th>费控控制&nbsp;<select name="is_control[]">
                                            <option value="0"@if(isset($_budget['is_control']) && $_budget['is_control'] == 0) selected @endif>否</option>
                                            <option value="1"@if(isset($_budget['is_control']) && $_budget['is_control'] == 1) selected @endif>是</option>
                                        </select></th>
                                    <th>
                                        <div class="text-left">
                                            <button type="button" style="height: 32px;padding: .35rem .75rem;" class="btn btn-danger delete line-del">删除</button>
                                        </div>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="item-condition">
                                @foreach($_budget['settings'] as $_setting)
                                    <tr class="des_{{$_setting['id']}}"><td>&nbsp;</td><td><div data-id="{{$_setting['id']}}">{{$_setting['title']}}</div></td>
                                    <td style="word-wrap:break-word;"><div style="width: 500px;display: flex;flex-wrap: wrap;">
                                            @foreach($_setting['categories'] as $_category)
                                                <div>{{$_category['condition_title']}}&nbsp;
                                                    <input type="checkbox" class="condition" value="{{$_category['condition_id']}}"
                                                                                                   data-condition-title="{{$_category['condition_title']}}" name="condition[]"
                                                           @if(in_array($_category['condition_id'], $_setting['category_ids'])) checked @endif>
                                                </div>
                                            @endforeach

                                        </div>
                                    </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endforeach
                    @else
                        <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                            <thead>
                            <tr style="background:#F0F5FA;">
                                <th style="width:50px;">费控名称&nbsp;<input type="text" class="point_name" style="width: 100px;" name="point_name[]"></th>
                                <th style="width:50px;">维度</th>
                                <th style="width:500px;">条件</th>
                                <th style="width:100px;">限制单价&nbsp;<input type="text" class="limit_price" style="width: 100px;" name="limit_price[]">&nbsp;元</th>
                                <th>费控控制&nbsp;<select name="is_control[]" >
                                        <option value="0"@if(isset($_budget['is_control']) && $_budget['is_control'] == 0) selected @endif>否</option>
                                        <option value="1"@if(isset($_budget['is_control']) && $_budget['is_control'] == 1) selected @endif>是</option>
                                    </select></th>
                                <th>
                                    <div class="text-left">
                                        <button type="button" style="height: 32px;padding: .35rem .75rem;" class="btn btn-danger delete line-del">删除</button>
                                    </div>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="item-condition">

                            </tbody>
                        </table>
                    @endif
                </div>
                <div class="text-left">
                    <button type="button" class="btn btn-primary btn-sm line-save" data-id="{{ $category['id'] }}">保存</button>
                </div>
            </div>
        </div>
    </section>
@endsection
@section("javascript")
    <!-- ================== DATEPICKER SCRIPTS ==================-->
    <script>
        $("button.line-height-fix").on('click', function () {
            var strVar = $("#form-condition").append($(".card-table:last").clone(true));
            $(".card-table:last").attr('data-id', "");
            // var num = $(".condition").length;
            // strVar.find(".condition:last").attr("id","condition_"+num);
        });

        $("button.line-del").on('click', function () {
            var num = $(".card-table").length;
            if(num == 1) {
                alert('费控点至少有1个');
            } else {
                $(this).parent().parent().parent().parent().parent().remove();
            }
        });


        $('.dimension').change(function() {
            // var check = [];
            // $(".dimension").each(function() {
            //     if(this.checked){
            //         check.push($(this).val());
            //     }
            // });

            //取消选择维度
            if(!this.checked){
                $('.des_'+$(this).val()).remove();
            } else {
                $.ajax({
                    url: '/financialManage/getDesCondition',
                    type: 'POST',
                    data: {'des_id' : $(this).val(), '_token':'{{csrf_token()}}'},
                    datatype: 'json',
                    success: function(response) {
                        if(response.code == 200) {
                            var html = '';
                            html += '<tr class="des_'+response.data[0].id+'" data-id=""><td>&nbsp;</td><td>' +
                                '<div data-id="'+response.data[0].id+'">'+response.data[0].title+'</div></td>';
                            if(response.data.length > 0) {
                                html += '<td style="word-wrap:break-word;"><div style="width: 500px;display: flex;flex-wrap: wrap;">';
                                for (var i=0; i < response.data.length;i++) {
                                    html += '<div>';
                                    html += response.data[i].condition_title + '&nbsp;<input type="checkbox" class="condition" ' +
                                        'value="'+response.data[i].condition_id+'" data-condition-title="'+response.data[i].condition_title+'" name="condition[]"></div>';
                                }
                                html += '</div></td></tr>';
                            }
                            $('.item-condition').append(html);
                        }
                    }
                });
            }
        });


        $("button.line-save").on('click',function () {
            var id = $(this).attr('data-id');
            var point_name = [];
            var limit_price = [];
            var is_control = [];
            var condition = [];
            var condition_ids = [];

            $("[name='point_name[]']").each(function(){
                point_name.push($(this).val());
            });
            $("[name='limit_price[]']").each(function(){
                limit_price.push($(this).val());
            });
            $("[name='is_control[]']").each(function(){
                is_control.push($(this).val());
            });
            //更新数据的id
            $(".card-table").each(function(){
                if($(this).attr('data-id'))
                    condition_ids.push($(this).attr('data-id'));
                else
                    condition_ids.push("");
            });

            $(".item-condition").each(function () {
                var item_title = [];
                $(this).find('tr').each(function () {
                    var condition_title = [];
                    var id = $(this).find('td:eq(1) div').attr('data-id');
                    var title = $(this).find('td:eq(1) div').html();
                    $(this).find('input[class=condition]:checked').each(function () {
                        condition_title.push({
                            'condition_id':$(this).val(),
                            'condition_title':$(this).attr('data-condition-title')
                        });
                    })
                    item_title.push({'id':id, 'title':title,'condition':condition_title});
                })
                condition.push(item_title)
            });

            var data = {
                'id' : id,
                'point_name' : point_name,
                'limit_price' : limit_price,
                'is_control' : is_control,
                'condition' : condition,
                'condition_ids' : condition_ids,
                '_token':'{{csrf_token()}}',
            };
            $.ajax({
                url: '/financialManage/desConditionSave',
                type: 'POST',
                data: data,
                datatype: 'json',
                success: function(response) {
                    alert(response.message);
                    if(response.code == 200) {
                        location.reload();
                    }
                }
            });

        });


    </script>
@endsection

