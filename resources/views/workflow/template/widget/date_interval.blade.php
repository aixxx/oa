<input type="text" class="form-control form-commit date_time_sub" placeholder="{{$v->placeholder}}"
       name="tpl[{{$v->field}}]" value="{{$field_value}}" readonly
       data-href="{{route('workflow.common.date-interval')}}"
       data-based="{{$v->field_value}}">
