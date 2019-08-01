@php
    $radios = explode(PHP_EOL, str_replace("\r\n", PHP_EOL, $v->field_value));
@endphp
<span class="span-form-commit">
    @foreach($radios as $r)
        <label class="radio-inline">
            <input type="radio" class="form-commit" name="tpl[{{$v->field}}]" value="{{$r}}"
                   @if(trim($field_value)==trim($r)) checked="checked" @endif>&nbsp;{{$r}}&nbsp;
        </label>
    @endforeach
</span>