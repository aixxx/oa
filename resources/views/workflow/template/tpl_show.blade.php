@foreach($template_forms as $v)
    <?php
    $field_value=$entry_data->where('field_name',$v->field)->first()?$entry_data->where('field_name',$v->field)->first()->field_value:'';
    ?>
    @if(in_array($v->field_type, ['text', 'textarea', 'date', 'select', ]))
        <div class="form-group">
            <label>{{$v->field_name}}</label>
            <p class="form-info-p">{{$field_value}}</p>
        </div>
    @elseif($v->field_type=='checkbox')
        <div class="form-group">
            <?php
            $checkbox=explode("\r\n", $v->field_value);
            $field_arr=explode("|",$field_value);
            ?>
            <label>{{$v->field_name}}</label>
            @foreach($checkbox as $c)
                @if(in_array($c,$field_arr))
                    <p class="form-info-p">{{$c}}</p>
                @endif
            @endforeach
        </div>
    @elseif($v->field_type=='radio')
        <div class="form-group">
            <?php
            $radios=explode("\r\n", $v->field_value);
            ?>
            <label>{{$v->field_name}}</label>
            @foreach($radios as $r)
                @if($field_value==$r)
                    <p class="form-info-p">{{$r}}</p>
                @endif
            @endforeach
        </div>
    @elseif($v->field_type=='file')
        <div class="form-group">
            <label>{{$v->field_name}}</label>
            @if(!empty($field_value))
                <p class="form-info-p"><a target="_blank" href="{{asset($field_value)}}">查看文件</a></p>
        @endif
        <!-- <p class="help-block">Example block-level help text here.</p> -->
        </div>
    @endif
@endforeach