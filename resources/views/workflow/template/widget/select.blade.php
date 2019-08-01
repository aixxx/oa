<select class="form-control form-commit" name="tpl[{{$v->field}}]">
    @php
        $options = explode(PHP_EOL, str_replace("\r\n", PHP_EOL, $v->field_value));
    @endphp
    <option value="">请选择{{$v->field_name}}</option>
    @foreach($options as $op)
        <option value="{{$op}}" @if($field_value==$op) selected="selected" @endif>{{$op}}</option>
    @endforeach
</select>
