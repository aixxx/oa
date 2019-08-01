@php
$cnt_cost_center = count($apply_basic_info['cost_center']);
@endphp
<select class="form-control form-commit cost_center" name="tpl[{{$v->field}}]">
    <option value="">请选择{{$v->field_name}}</option>
    @foreach($apply_basic_info['cost_center'] as $op)
    <option value="{{$op}}"
            {{$apply_basic_info['cost_center'][$op]['readonly']??''}}
    @if($field_value==$op || $cnt_cost_center == 1) selected="selected" @endif>{{$op}}</option>
    @endforeach
</select>
{{--采购设备--}}
