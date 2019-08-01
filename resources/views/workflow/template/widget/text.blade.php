<input class="form-control form-commit" type="text" placeholder="{{$v->placeholder}}"
       name="tpl[{{$v->field}}]"
       value="<?php echo ($v->unit == '元' && $field_value) ? number_format($field_value, 2) : $field_value; ?>">
