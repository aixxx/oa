<input type="text" class="form-control form-commit" name="tpl[{{$v->field}}]"
       readonly="readonly" data-sum="{{$v->field_value}}"
       value="<?php echo ($v->unit == '元' && $field_value) ? number_format($field_value, 2) : $field_value; ?>">