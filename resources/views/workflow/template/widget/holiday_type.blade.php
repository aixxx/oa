<select class="form-control form-commit holiday_type" name="tpl[{{$v->field}}]">
    <?php
    $options = explode(PHP_EOL, str_replace("\r\n", PHP_EOL, $v->field_value));
    ?>
    <option value="">请选择{{$v->field_name}}</option>
    @foreach($options as $op)
        <option value="{{$op}}"
                {{$apply_basic_info['holiday_balance'][$op]['readonly']??''}}
                data-balance="{{isset($apply_basic_info['holiday_balance'][$op]['balance'])?$apply_basic_info['holiday_balance'][$op]['balance']:-1}}"
                @if($field_value==$op) selected="selected" @endif>{{$op}}</option>
    @endforeach
</select>
