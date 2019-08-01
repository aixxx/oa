<select name="tpl[company_name_list]" class="form-control form-commit" id="company_name_list">
    <option value="">请选择</option>
    @foreach($apply_basic_info['company_name_list'] as $company_key =>$company_name)
        <option value="{{$company_key}}" {{$field_value==$company_key?'selected':''}}>{{$company_name}}</option>
    @endforeach
</select>
