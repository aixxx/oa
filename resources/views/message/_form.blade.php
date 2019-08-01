<style>
    .control-label {
        font-weight: bold;
    }

    .btn-flat {
        -webkit-box-shadow: none;
        -moz-box-shadow: none;
        box-shadow: none;
        border-radius: 0;
    }
</style>
<div class="card">
    {!! Form::open(['method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
    <div class="card-header">
        <h3>{{ $title }}</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            {{ Form::label('template_key', '模板键值', ['class' => 'control-label']) }}
            {{ Form::text('template_key', $model->template_key, [
                'class' => 'form-control',
                'maxlength' => '45',
                'placeholder' => '若为流程通知，请使用流程编号'
            ]) }}
        </div>
        <div class="form-group">
            {{ Form::label('template_name', '名称', ['class' => 'control-label']) }}
            {{ Form::text('template_name', $model->template_name, [
                'class' => 'form-control',
                'maxlength' => '255',
                'placeholder' => '名称自定义'
            ]) }}
        </div>
        <div class="form-group">
            {{ Form::label('template_sign', '签名，自动加【】', ['class' => 'control-label']) }}
            <a tabindex="0" href="javascript:void(0);" data-toggle="popover" data-trigger="focus"
               title="说明" data-content="1.自动加【】<br> 2.标签加在标题之前">
                <i class="la la-exclamation-circle"></i>
            </a>
            {{ Form::text('template_sign', $model->template_sign, ['class' => 'form-control', 'maxlength' => '45']) }}
        </div>
        <div class="form-group">
            {{ Form::label('template_type', '类型', ['class' => 'control-label']) }}
            {{ Form::select('template_type', App\Constant\CommonConstant::MESSAGE_TYPE_MAPPING, $model->template_type, [
                'class' => 'form-control'
            ]) }}
        </div>
        <div class="form-group">
            {{ Form::label('template_push_type', '推送方式', ['class' => 'control-label']) }}
            {{ Form::select(
                'template_push_type',
                App\Constant\CommonConstant::MESSAGE_PUSH_TYPE_MAPPING,
                $model->template_push_type, ['class' => 'form-control']
            ) }}
        </div>
        <div class="form-group">
            {{ Form::label('template_title', '模板标题', ['class' => 'control-label']) }}
            <a tabindex="0" href="javascript:void(0);" data-toggle="popover" data-trigger="focus"
               title="说明" data-content="1.使用blade模板样式<br>变量使用 @php echo '{{ $变量名 }}' @endphp<br><br>2.公共变量<br>申请人@php echo '{{ $applicant }}' @endphp<br><br>3.流程使用模板变量<br><br>4.不能换行">
                <i class="la la-exclamation-circle"></i>
            </a>
            {{ Form::input('text', 'template_title', $model->template_title, [
                'class' => 'form-control',
                'placeholder' => '请使用blade模板样式',
            ]) }}
        </div>
        <div class="form-group">
            {{ Form::label('template_content', '模板内容', ['class' => 'control-label']) }}
            <a tabindex="0" href="javascript:void(0);" data-toggle="popover" data-trigger="focus"
               title="说明" data-content="1.使用blade模板样式<br>变量使用 @php echo '{{ $变量名 }}' @endphp<br><br>2.公共变量<br>申请人@php echo '{{ $applicant }}' @endphp<br><br>3.流程使用模板变量<br><br>4.可以直接换行">
                <i class="la la-exclamation-circle"></i>
            </a>
            {{ Form::textarea('template_content', $model->template_content, [
                'class' => 'form-control',
                'style' => 'resize:none;',
                'placeholder' => '请使用blade模板样式',
            ]) }}
        </div>
        <div class="form-group">
            {{ Form::label('template_memo', '备注', ['class' => 'control-label']) }}
            {{ Form::textarea('template_memo', $model->template_memo, [
                'class' => 'form-control',
                'placeholder' => '字数限制255字内',
                'maxlength' => '255',
                'style' => 'resize:none;',
                'rows' => 4
            ]) }}
        </div>
        <div class="form-group">
            {{ Form::label('template_status', '状态', ['class' => 'control-label']) }}
            {{ Form::select('template_status', App\Constant\CommonConstant::STATUS_MAPPING, $model->template_status, [
                'class' => 'form-control'
            ]) }}
        </div>
    </div>
    <div class="card-footer bg-light">
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="form-group">
            {{ Form::submit('保存', ['class' => 'btn btn-success btn-flat']) }}
        </div>
    </div>
    {!! Form::close() !!}
</div>