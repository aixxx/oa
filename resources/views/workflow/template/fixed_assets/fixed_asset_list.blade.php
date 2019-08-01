<?php
if ($entry_data) {
    $assetFormData = $entry_data->whereNotIn('field_name', [
        'primary_dept',
        'applicant_chinese_name',
        'apply_date',
        'expected_arrival_date',
        'hidden_text_1',
        'is_it',
        'hidden_text_2',
        'total_all_price'
    ]);
    $assetFormData->each(function ($item, $key) {
        $item->field_value = json_decode($item->field_value, true);
    });
    $totalAllPrice = $entry_data->where('field_name', 'total_all_price')->first();
//    dd($assetFormData);
}
?>


@if(isset($assetFormData) && $assetFormData)
    @foreach($assetFormData as $key => $every)
        @if(!in_array(\Route::currentRouteName(),['workflow.entry.show','workflow.proc.show']))
            {{--<a href="javascript:void(0);"  aria-hidden="true" id="add">新增</a>--}}
            <br>
        @endif
        <div class="card card_asset card_{{ $every->field_name }}">
            <br>
        @if(\Route::currentRouteName() == 'workflow.entry.show' || \Route::currentRouteName() == 'workflow.proc.show') <!--展示 -->
            <div class="row">
                &nbsp;&nbsp;&nbsp;&nbsp;<div class="col-md-1" style="padding: 0;">采购类别：</div>
                <div class="col-md-8">
                    <label class="radio-inline">
                        <input type="radio" name="tpl[{{$every->field_name}}][cat]" value="办公用品" @if(isset($every->field_value['cat']) &&
                        $every->field_value['cat']=='办公用品') checked @endif> 办公用品
                    </label>&nbsp;&nbsp;
                    <label class="radio-inline">
                        <input type="radio" name="tpl[{{$every->field_name}}][cat]" value="日用品" @if(isset($every->field_value['cat']) &&
                        $every->field_value['cat']=='日用品')
                        checked
                                @endif> 日用品
                    </label>&nbsp;&nbsp;
                    <label class="radio-inline">
                        <input type="radio" name="tpl[{{$every->field_name}}][cat]" value="食品" @if(isset($every->field_value['cat']) &&
                        $every->field_value['cat']=='食品') checked @endif> 食品
                    </label>&nbsp;&nbsp;
                    <label class="radio-inline">
                        <input type="radio" name="tpl[{{$every->field_name}}][cat]" value="设备电器" @if(isset($every->field_value['cat']) &&
                        $every->field_value['cat']=='设备电器')
                        checked
                                @endif>
                        设备电器
                    </label>&nbsp;&nbsp;
                    <label class="radio-inline">
                        <input type="radio" name="tpl[{{$every->field_name}}][cat]" value="IT设备配件" @if(isset($every->field_value['cat']) &&
                        $every->field_value['cat']=='IT设备配件')
                        checked
                                @endif>
                        IT设备配件
                    </label>&nbsp;&nbsp;
                    <label class="radio-inline">
                        <input type="radio" name="tpl[{{$every->field_name}}][cat]" value="其他" @if(isset($every->field_value['cat']) &&
                        $every->field_value['cat']=='其他') checked
                                @endif> 其他
                    </label>
                    <label class="row-inline">
                        (<input type="text" class="form-control" name="tp[{{$every->field_name}}][other_cat]"
                                value="@if(isset($every->field_value['other_cat'])) {{ $every->field_value['other_cat'] }} @endif">)
                    </label>
                </div>
            </div>
            @else
                <div class="row"> <!--编辑 -->
                    <div class="col-md-1" style="padding: 0;">&nbsp;&nbsp;&nbsp;&nbsp;采购类别：</div>
                    <div class="col-md-6">
                        <label class="radio-inline">
                            <input type="radio" name="tpl[{{$every->field_name}}][cat]" value="办公用品" @if(isset($every->field_value['cat']) &&
                        $every->field_value['cat']=='办公用品') checked @endif> 办公用品
                        </label>&nbsp;&nbsp;
                        <label class="radio-inline">
                            <input type="radio" name="tpl[{{$every->field_name}}][cat]" value="日用品" @if(isset($every->field_value['cat']) &&
                        $every->field_value['cat']=='日用品')
                            checked
                                    @endif> 日用品
                        </label>&nbsp;&nbsp;
                        <label class="radio-inline">
                            <input type="radio" name="tpl[{{$every->field_name}}][cat]" value="食品" @if(isset($every->field_value['cat']) &&
                        $every->field_value['cat']=='食品') checked @endif> 食品
                        </label>&nbsp;&nbsp;
                        <label class="radio-inline">
                            <input type="radio" name="tpl[{{$every->field_name}}][cat]" value="设备电器" @if(isset($every->field_value['cat']) &&
                        $every->field_value['cat']=='设备电器')
                            checked
                                    @endif>
                            设备电器
                        </label>&nbsp;&nbsp;
                        <label class="radio-inline">
                            <input type="radio" name="tpl[{{$every->field_name}}][cat]" value="IT设备配件" @if(isset($every->field_value['cat']) &&
                        $every->field_value['cat']=='IT设备配件')
                            checked
                                    @endif>
                            IT设备配件
                        </label>&nbsp;&nbsp;
                        <label class="radio-inline">
                            <input type="radio" name="tpl[{{$every->field_name}}][cat]" value="其他" @if(isset($every->field_value['cat']) &&
                        $every->field_value['cat']=='其他') checked
                                    @endif> 其他
                        </label>
                        <label class="row-inline">
                            (<input type="text" class="form-control" name="tp[{{$every->field_name}}][other_cat]"
                                    value="@if(isset($every->field_value['other_cat'])) {{ $every->field_value['other_cat'] }} @endif">)
                        </label>
                    </div>
                    {{--<div class="col-md-4"></div>--}}
                    {{--@if(\Route::currentRouteName() != 'workflow.entry.show')--}}
                        {{--<div class="col-md-1" style="font-size: 20px;font-weight: bold">&times;</div>--}}
                    {{--@endif--}}
                </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered text-center table-sm" data-tablenum="{{ $every->field_name }}" style="table-layout: fixed;">
                        <thead>
                        <tr>
                            <th>名称*</th>
                            <th>型号</th>
                            <th>单价*</th>
                            <th>数量*</th>
                            <th>总价*</th>
                            <th>供应商推荐</th>
                            <th>备注</th>
                            @if(!in_array(\Route::currentRouteName(),['workflow.entry.show','workflow.proc.show']))
                                <th>操作</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach(collect($every->field_value)->except(['cat','other_cat']) as $k => $value)
                            <tr data-trnum="{{  $k  }}">
                                <td>
                                    <input type="text" class="form-control" name="tpl[{{ $every->field_name }}][{{ $k }}][name]" value="{{  $value['name']  }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="tpl[{{ $every->field_name }}][{{ $k }}][item]" value="{{ $value['item'] }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="tpl[{{ $every->field_name }}][{{ $k }}][purchase_price]" value="{{
                                    $value['purchase_price'] }}" onblur="computeTotalPriceSecond(this)">&nbsp;元
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="tpl[{{ $every->field_name }}][{{ $k }}][stock]" oninput="computeTotalPrice
                                    (this)" value="{{  $value['stock']  }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control" readonly name="tpl[{{ $every->field_name }}][{{ $k }}][total_price]"
                                           value="{{ $value['total_price'] }}">&nbsp;元
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="tpl[{{ $every->field_name }}][{{ $k }}][supplier]"
                                           value="{{  $value['supplier']  }}">

                                </td>
                                <td>
                                    <textarea type="text" class="form-control"
                                              name="tpl[{{ $every->field_name }}][{{ $k }}][remark]">{{  $value['remark']  }}</textarea>
                                </td>
                                @if(!in_array(\Route::currentRouteName(),['workflow.entry.show','workflow.proc.show']))
                                    <td style="line-height: 250%;" class="text-center">
                                        <a href="javascript:void(0);" onclick="deleteTr(this)">删除</a>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if(!in_array(\Route::currentRouteName(),['workflow.entry.show','workflow.proc.show']))
                <div class="text-right" style="padding-right: 4%;">
                    <button class="btn btn-info" onclick="addAsset(this)">添加</button>
                </div>
            @endif
            <br>
        </div>
    @endforeach
    {{--<div class="row">--}}
    {{--<label class="col-md-2" id="total_money">金额合计：@if(isset($totalAllPrice) && $totalAllPrice){{$totalAllPrice->field_value}} 元 @endif</label>--}}
    {{--</div>--}}
@else
    {{--<a href="javascript:void(0);"  aria-hidden="true" id="add">新增</a>--}}
    <br>
    <div class="card card_asset card_1 clearfix">
        <br>
        {{--@if(\Route::currentRouteName() != 'workflow.entry.show')--}}
            {{--<div style="font-size: 20px;font-weight: bold;position: absolute;right: 15px;top: 5px;cursor:pointer">&times;</div>--}}
        {{--@endif--}}
        <div class="row">
            <div class="col-md-2">&nbsp;&nbsp;采购类别：</div>
            <div class="col-md-10">
                <label class="radio-inline">
                    <input type="radio" name="tpl[1][cat]" value="办公用品"> 办公用品
                </label>&nbsp;&nbsp;
                <label class="radio-inline">
                    <input type="radio" name="tpl[1][cat]" value="日用品"> 日用品
                </label>&nbsp;&nbsp;
                <label class="radio-inline">
                    <input type="radio" name="tpl[1][cat]" value="食品"> 食品
                </label>&nbsp;&nbsp;
                <label class="radio-inline">
                    <input type="radio" name="tpl[1][cat]" value="设备电器"> 设备电器
                </label>&nbsp;&nbsp;
                <label class="radio-inline">
                    <input type="radio" name="tpl[1][cat]" value="IT设备配件"> IT设备配件
                </label>
                <label class="radio-inline">
                    <input type="radio" name="tpl[1][cat]" value="其他" onclick="addOther(this);"> 其他
                </label>
                <label class="row-inline">
                    (<input type="text" class="form-control" name="tpl[1][other_cat]" disabled>)
                </label>
            </div>
            {{--<div class="col-md-2" style="position:relative">--}}

            {{--</div>--}}
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered text-center table-sm" data-tablenum="1" style="table-layout: fixed;">
                    <thead>
                    <tr>
                        <th>名称*</th>
                        <th>型号</th>
                        <th>单价*</th>
                        <th>数量*</th>
                        <th>总价*</th>
                        <th>供应商推荐</th>
                        <th>备注</th>
                        @if(\Route::currentRouteName() != 'workflow.entry.show')
                            <th>操作</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    <tr data-trnum="1">
                        <td>
                            <input type="text" class="form-control" name="tpl[1][1][name]">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="tpl[1][1][item]">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="tpl[1][1][purchase_price]" onblur="computeTotalPriceSecond(this)">&nbsp;元
                        </td>
                        <td>
                            <input type="text" class="form-control" name="tpl[1][1][stock]" oninput="computeTotalPrice(this)">
                        </td>
                        <td>
                            <input type="text" class="form-control" readonly name="tpl[1][1][total_price]">&nbsp;元
                        </td>
                        <td>
                            <input type="text" class="form-control" name="tpl[1][1][supplier]">
                        </td>
                        <td>
                            <textarea type="text" class="form-control" name="tpl[1][1][remark]"></textarea>
                        </td>
                        @if(\Route::currentRouteName() != 'workflow.entry.show')
                            <td style="line-height: 250%;" class="text-center">
                                <a href="javascript:void(0);" onclick="deleteTr(this)">删除</a>
                            </td>
                        @endif
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @if(\Route::currentRouteName() != 'workflow.entry.show')
            <div class="text-right" style="padding-right: 4%;">
                <button class="btn btn-info" onclick="addAsset(this)">添加</button>
            </div>
        @endif
        <br>
    </div>
    {{--<div class="row">--}}
    {{--<label class="col-md-2" id="total_money">金额合计：</label>--}}
    {{--</div>--}}
    {{--<div class="row">--}}
    {{--<div class="col-md-1">--}}
    {{--<a href="javascript:void(0);" class="btn-primary btn btn-sm" name="tpl[total_all_price]">--}}
    {{--点击计算--}}
    {{--</a>--}}
    {{--</div>--}}
    {{--</div>--}}
@endif

<script>
    window.onload = function () {
        var myDate = new Date();
        current_year  = myDate.getFullYear();
        current_month = myDate.getMonth()+1;
        console.log(current_month);
        current_day   = myDate.getDate();
        current_date = current_year+'-'+current_month+'-'+current_day;
        $("input[name='tpl[apply_date]']").val(current_date);

        card_num = 2;
        @if(\Route::currentRouteName() != 'workflow.entry.edit')
            total_all_price = 0;
        @else
            total_all_price = parseInt("{{$totalAllPrice->field_value}}");
        @endif
        $("#add").click(function () {
            asset_card_node = "<div class='card card_asset card_" + card_num + "'>" +
                "<br>" +
                "<div style='font-size: 20px;font-weight: bold;position: absolute;right: 15px;top: 5px;cursor:pointer;' onclick='deleteAssetCard(this)" +
                "'>&times;</div>" +
                "<div class='row'>" +
                "<div class='col-md-2'>&nbsp;&nbsp;采购类别：</div>" +
                "<div class='col-md-10'>" +
                "<label class='radio-inline'>" +
                "<input type='radio' name='tpl[" + card_num + "][cat]' value='办公用品'> 办公用品" +
                "</label>&nbsp;&nbsp;" +
                "<label class='radio-inline'>" +
                "<input type='radio' name='tpl[" + card_num + "][cat]' value='日用品'> 日用品" +
                "</label>&nbsp;&nbsp;" +
                "<label class='radio-inline'>" +
                "<input type='radio' name='tpl[" + card_num + "][cat]' value='食品'> 食品" +
                "</label>&nbsp;&nbsp;" +
                "<label class='radio-inline'>" +
                "<input type='radio' name='tpl[" + card_num + "][cat]' value='设备电器'> 设备电器" +
                "</label>&nbsp;&nbsp;" +
                "<label class='radio-inline'>" +
                "<input type='radio' name='tpl[" + card_num + "][cat]' value='IT设备配件'> IT设备配件" +
                "</label>&nbsp;&nbsp;" +
                "<label class='radio-inline'>" +
                "<input type='radio' name='tpl[" + card_num + "][cat]' value='其他' onclick='addOther(this);'> 其他" +
                "</label>" +
                "<label class='row-inline'>" +
                "(<input type='text' class='form-control' name='tpl[" + card_num + "][other_cat]' disabled>)" +
                "</label>" +
                "</div>" +
                "</div>" +
                "<br>" +
                "<div class='row'>" +
                "<div class='col-md-12'>" +
                "<table class='table table-bordered text-center table-sm' data-tablenum='" + card_num + "' style='table-layout: fixed;' >" +
                "<thead>" +
                "<tr>" +
                "<th>名称</th>" +
                "<th>型号</th>" +
                "<th>单价</th>" +
                "<th>数量</th>" +
                "<th>总价</th>" +
                "<th>供应商推荐</th>" +
                "<th>备注</th>" +
                "<th>操作</th>" +
                "</tr>" +
                "</thead>" +
                "<tbody>" +
                "<tr data-trnum='1'>" +
                "<td>" +
                "<input type='text' class='form-control' name='tpl[" + card_num + "][1][name]'>" +
                "</td>" +
                "<td>" +
                "<input type='text' class='form-control' name='tpl[" + card_num + "][1][item]'>" +
                "</td>" +
                "<td>" +
                "<input type='text' class='form-control' name='tpl[" + card_num + "][1][purchase_price]' oninput='computeTotalPriceSecond(this)" +
                "'>&nbsp;元" +
                "</td>" +
                "<td>" +
                "<input type='text' class='form-control' name='tpl[" + card_num + "][1][stock]' oninput='computeTotalPrice(this)'>" +
                "</td>" +
                "<td>" +
                "<input type='text' class='form-control' readonly name='tpl[" + card_num + "][1][total_price]'>&nbsp;元" +
                "</td>" +
                "<td>" +
                "<input type='text' class='form-control' name='tpl[" + card_num + "][1][supplier]'>" +
                "</td>" +
                "<td>" +
                "<textarea type='text' class='form-control' name='tpl[" + card_num + "][1][remark]'></textarea>" +
                "</td>" +
                "<td style='line-height: 250%;' class='text-center'>" +
                "<a href='javascript:void(0);' onclick='deleteTr(this)'>删除</a>" +
                "</td>" +
                "</tr>" +
                "</tbody>" +
                "</table>" +
                "</div>" +
                "</div>" +
                "<div class='text-right'>" +
                "<button class='btn btn-info' onclick='addAsset(this)'>添加</button>" +
                "</div>" +
                "<br>" +
                "</div>";
            $(".card_asset").last().after(asset_card_node);
            card_num = card_num + 1;
        });

        $("a[name='tpl[total_all_price]']").click(function () {
            total_all_price = 0;
            $(".card_asset").each(function () {
                let children_tr_nodes = $(this).find('table > tbody').children();
                children_tr_nodes.each(function () {
                    let every_total_price = $(this).children().first().next().next().next().next().children().first().val();
                    if (every_total_price) {
                        total_all_price = parseInt(total_all_price) + parseInt(every_total_price);
                    }
                });
            });
            $("div[name='tpl[total_all_price]']").text("金额合计：" + total_all_price + " 元");
        });

        @if($entry_data && $entry_data->where('field_name','total_all_price')->first())
            total_all_price_text = {{$entry_data->where('field_name','total_all_price')->first()->field_value}}
        $("div[name='tpl[total_all_price]']").text('金额合计：' + total_all_price_text + ' 元');
        @endif


        $(window).keydown(function (e) {
            var key = window.event ? e.keyCode : e.which;
            if (key.toString() == "13") {
                return false;
            }
        });

    };

    function deleteAssetCard(obj) {
        $(obj).parent().remove();
    }

    function addAsset(obj, e) {
        e = e || window.event;
        e.preventDefault();

        total_tbody_tr_nodes = $(obj).parent().prev().find('tbody').children();
        if (total_tbody_tr_nodes.length > 0) {
            last_tr_num = total_tbody_tr_nodes.last().data('trnum');
            new_tbody_tr_nodes = total_tbody_tr_nodes.last().clone(true);
            new_tbody_tr_nodes.data('trnum', parseInt(last_tr_num) + 1);
            new_tbody_tr_nodes.children().slice(0, -1).each(function () {
                child_node = $(this).children().first();
                child_node.attr('name', modifyName(child_node.attr('name')));
                child_node.val('');
            });
            total_tbody_tr_nodes.last().after(new_tbody_tr_nodes);
        }

    }

    /**
     * 格式化新增的表单元素name
     * @param name
     * @returns {*}
     */
    function modifyName(name) {
        let split_name = name.split('][');
        split_name[1] = parseInt(split_name[1]) + 1;
        let after_modify_name = split_name.join("][");
        return after_modify_name;
    }


    function deleteTr(obj) {
        let tbody_children = $(obj).parent().parent().parent().children();
        if (tbody_children.length > 1) {
            $(obj).parent().parent().remove();
            del_price = $(obj).parent().prev().prev().prev().find('input').val();
            total_all_price = total_all_price - del_price;
            $("div[name='tpl[total_all_price]']").text("金额合计：" + total_all_price + " 元");
            console.log(obj);
        }
    }

    function computeTotalPrice(obj) {
        let purchase_price = $(obj).parent().prev().children().first();
        let purchase_price_value = $(obj).parent().prev().children().first().val();
        let total_price_node = $(obj).parent().next().children().first();
        let stock_value = $(obj).val();
        let next_node_price = $(obj).parent().next().find('input').val();
        if (!next_node_price) {
            next_node_price = 0;
        } else {
            next_node_price = parseInt(next_node_price);
        }

        if (!purchase_price) {
            alert("请填写采购单价");
            return false;
        }

        if (!stock_value) {
            alert("请填写采购数量");
            return false;
        }
        if (!(/(^[1-9]\d*(\.\d{1,2})?$)|(^0(\.\d{1,2})?$)/.test(purchase_price_value))) {
            purchase_price.val('');
            total_price_node.val('');
            $(obj).val('');
            alert("采购单价为数值，请正确填写！");
            return false;
        }


        if (!(/(^[1-9]{1}[0-9]*$)/.test(stock_value))) {
            $(obj).val('');
            total_price_node.val('');
            alert("库存为正整数，请正确填写！");
            return false;
        }

        let compute_total_price = parseFloat(purchase_price_value) * parseInt(stock_value);
        total_all_price = total_all_price - next_node_price + compute_total_price;
        total_price_node.val(compute_total_price);
        $("div[name='tpl[total_all_price]']").text("金额合计：" + total_all_price + " 元");
    }


    function computeTotalPriceSecond(obj) {
        let purchase_price = $(obj).val();
        let stock_value = $(obj).parent().next().find('input').val();
        let next_node_price = $(obj).parent().next().next().find('input').val();

        if (!(/(^[1-9]\d*(\.\d{1,2})?$)|(^0(\.\d{1,2})?$)/.test(purchase_price))) {
            alert("采购单价为数值，请正确填写！");
            $(obj).val('');
            $(obj).parent().next().find('input').val('');
            return false;
        }

        if (!stock_value) {
            stock_value = 0;
        } else {
            stock_value = parseInt(stock_value);
        }

        if (!next_node_price) {
            next_node_price = 0;
        } else {
            next_node_price = parseInt(next_node_price);
        }

        let compute_total_price = purchase_price * stock_value;
        $(obj).parent().next().next().find('input').val(compute_total_price);
        total_all_price = total_all_price - next_node_price + compute_total_price;
        $("div[name='tpl[total_all_price]']").text("金额合计：" + total_all_price + " 元");
    }


    function addOther(obj) {
        $(obj).parent().next().children().first().attr('disabled', false);
    }

</script>
