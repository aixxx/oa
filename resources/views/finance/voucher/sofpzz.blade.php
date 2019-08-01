<option value="">请选择模板类型</option>
@foreach($sofpzz as $_pzz)
    <option value="{{  $_pzz['id']  }}"
            @if(isset($sofpzz_id) && ($_pzz['id'] == $sofpzz_id))
            selected
            @endif>{{  $_pzz['title']  }}</option>
@endforeach