<?php
    if ($entry_data) {
        $assetApplyData = $entry_data->whereNotIn('field_name',['primary_dept','applicant_chinese_name','apply_reason']);
        if ($assetApplyData->isNotEmpty()) {
            $assetApplyData->each(function ($item,$key){
                $item->field_value = json_decode($item->field_value,true);
            });
        }
        $applyReason = $entry_data->where('field_name','apply_reason')->first();
    }

?>

@if(isset($assetApplyData) && $assetApplyData)
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <table class="table table-bordered text-center table-sm" style="table-layout: fixed;">
                <thead>
                <th>设备名称</th>
                <th>设备规格</th>
                <th>申请数量</th>
                <th>备注说明</th>
                </thead>
                <tbody>
                @foreach($assetApplyData as $every)
                    <tr>
                        <td>
                            <input type="text" class="form-control" name="tpl[{{$every->field_name}}][name]" value="{{  $every->field_value['name']  }}">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="tpl[{{$every->field_name}}][spec]" value="{{  $every->field_value['spec']  }}">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="tpl[{{$every->field_name}}][num]" value="{{ $every->field_value['num']  }}">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="tpl[{{$every->field_name}}][remark]" value="{{  $every->field_value['remark']  }}">
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <label class="col-md-2">申请原因：</label>
        <div class="col-md-8" style="height: 100px;">
            <textarea name="tpl[apply_reason]" id="" class="form-control" style="width: 100%;height: 100%;">@if(isset($applyReason) && $applyReason){{$applyReason->field_value}}@endif</textarea>
        </div>
    </div>
@else
    <a href="javascript:void(0);" style="margin-left: -5%;" id="addApplyAsset">新增设备</a>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <table class="table table-bordered text-center table-sm" style="table-layout: fixed;">
                <thead>
                <th>设备名称</th>
                <th>设备规格</th>
                <th>申请数量</th>
                <th>备注说明</th>
                <th>操作</th>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <input type="text" class="form-control" name="tpl[1][name]">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="tpl[1][spec]">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="tpl[1][num]">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="tpl[1][remark]">
                    </td>
                    <td style="line-height: 250%;">
                        <a href="javascript:void(0);" onclick="deleteRow(this)">删除</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <label class="col-md-2">申请原因：</label>
        <div class="col-md-8" style="height: 100px;">
            <textarea name="tpl[apply_reason]" id="" class="form-control" style="width: 100%;height: 100%;"></textarea>
        </div>
    </div>
@endif
<script>
    window.onload = function () {
        $("#addApplyAsset").click(function () {
            let table_tbody_td_children = $("table > tbody").children();
            console.log(table_tbody_td_children);
            let clone_td = table_tbody_td_children.last().clone();
            clone_td.children().slice(0,-1).each(function () {
                let input_node = $(this).find('input');
                input_node.val('');
                input_node.attr('name',modifyInputName(input_node.attr('name')));

            });
            table_tbody_td_children.last().after(clone_td)
        });
    };

    function modifyInputName(name) {
        let split_name = name.split('][');
        split_name[0] = split_name[0].split('[');
        split_name[0][1] = parseInt(split_name[0][1])+1;
        split_name[0] = split_name[0].join('[');
        split_name = split_name.join('][');
        console.log(split_name);
        return split_name;
    }

    function deleteRow(obj) {
        let all_table_tds = $(obj).parent().parent().parent().children();
        if (all_table_tds.length > 1) {
            $(obj).parent().parent().remove();
        }
    }
</script>