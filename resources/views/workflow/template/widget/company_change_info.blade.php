<?php $company_change_info = json_decode($field_value, true) ? : []; ?>
<div class="row">
    @foreach($company_change_info as $key=> $info)
        <div class="form-group col-12"><h4>{{$info['comment']}}</h4></div>
        @if(isset($info['add']))
            <div class="form-group col-12">新增内容</div>
            @foreach($info['add'] as $id => $add_list)
                <div class="form-group col-1"></div>
                <div class="form-group col-11 row ">
                    @foreach($add_list as $key => $detail)
                        <div class="form-group form-group-sp col-6">
                            <label class="form-label">{{$detail['comment']}}</label>
                            <input class="form-control form-commit" type="text" readonly="readonly"
                                   value="{{$detail['after_comment']}}">
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endif
        @if(isset($info['edit']))
            <div class="form-group col-12">修改内容</div>
            @foreach($info['edit'] as $id => $add_list)
                <div class="form-group col-1"></div>
                <div class="form-group col-11 row">
                    @foreach($add_list as $key => $detail)
                        <div class="form-group form-group-sp col-6">
                            <label class="form-label">{{$detail['comment']}}</label>
                            <input class="form-control form-commit" type="text" readonly="readonly"
                                   value="{{$detail['before_comment']}}">
                        </div>
                        <div class="form-group form-group-sp col-6">
                            <label class="form-label">=></label>
                            <input class="form-control form-commit" type="text" readonly="readonly"
                                   value="{{$detail['after_comment']}}">
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endif
        @if(isset($info['delete']))
            <div class="form-group col-12">删除内容</div>
            @foreach($info['delete'] as $id => $add_list)
                <div class="form-group col-1"></div>
                <div class="form-group col-11 row">
                    @foreach($add_list as $key => $detail)
                        <div class="form-group form-group-sp col-6">
                            <label class="form-label">{{$detail['comment']}}</label>
                            <input class="form-control form-commit" type="text" readonly="readonly"
                                   value="{{$detail['before_comment']}}">
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endif
    @endforeach
</div>
