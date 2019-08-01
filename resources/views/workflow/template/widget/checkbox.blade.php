@php
    $checkbox    = explode(PHP_EOL, str_replace("\r\n", PHP_EOL, $v->field_value));
    $field_value = json_decode($field_value, true) ? : [];
@endphp
<span class="span-form-commit">
    @foreach($checkbox as $c)
        <label class="checkbox-inline">
            <input type="checkbox" class="form-commit" value="{{trim($c)}}" name="tpl[{{$v->field}}][]"
                   @if(in_array($c,$field_value)) checked="checked" @endif >&nbsp;{{trim($c)}}&nbsp;</label>
    @endforeach
</span>