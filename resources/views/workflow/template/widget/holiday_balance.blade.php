<div class="row">
    @foreach($apply_basic_info['holiday_balance'] as $name=>$val)
        @if($val['show'])
            <div class="form-group col-sm-6 col-xs-12">
                <label class="form-label">{{$name}}</label>
                <input class="form-control form-commit" type="text" readonly="readonly"
                       value="{{$val['balance'].$val['unit']}}">
            </div>
        @endif
    @endforeach
</div>
