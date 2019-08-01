<style>
    .form-inline .form-group {
        display: inline-block;
        margin-bottom: 15px;
        vertical-align: middle;
    }

    .col-xs-12 {
        width: 100%;
    }

    .search .search-btn {
        -webkit-box-shadow: none;
        -moz-box-shadow: none;
        box-shadow: none;
        border-width: 1px;
    }

    select.form-control:not([size]):not([multiple]) {
        height: 35px;
    }

    .search .search-btn, .search .search-input, .search .search-select {
        width: 200px;
        height: 35px;
        border-radius: 0;
        -webkit-border-radius: 0;
        -moz-border-radius: 0;
        -khtml-border-radius: 0;
        -webkit-appearance: button;
    }

    .search .search-input {
        -webkit-box-shadow: none;
        -moz-box-shadow: none;
        box-shadow: none;
        border: 1px solid #d2d6de;
    }
</style>
<div class="search m-t-10">
    {!! Form::open(['method' => 'GET', 'class' => 'form-inline']) !!}

    <div class="col-xs-12">
        <div class="form-group">
            {{ Form::text('template_key', $searchModel->template_key, [
                'class' => 'form-control search-input',
                'maxlength' => '45',
                'placeholder' => '模板键值'
            ]) }}
        </div>
        <div class="form-group">
            {{ Form::text('template_name', $searchModel->template_name, [
                'class' => 'form-control search-input',
                'maxlength' => '45',
                'placeholder' => '名称'
            ]) }}
        </div>
        <div class="form-group">
            {{ Form::select('template_type', App\Constant\CommonConstant::MESSAGE_TYPE_MAPPING, $searchModel->template_type, [
                'class' => 'form-control search-select',
                'placeholder' => '类型'
            ]) }}
        </div>
        <div class="form-group">
            {{ Form::select(
                'template_push_type',
                App\Constant\CommonConstant::MESSAGE_PUSH_TYPE_MAPPING,
                $searchModel->template_push_type, [
                    'class' => 'form-control search-select',
                    'placeholder' => '推送方式'
                ]
            ) }}
        </div>
        <div class="form-group">
            {{ Form::select('template_status', App\Constant\CommonConstant::STATUS_MAPPING, $searchModel->template_status, [
                'class' => 'form-control search-select',
                'placeholder' => '状态'
            ]) }}
        </div>
    </div>

    <div class="col-xs-12">
        <div class="form-group">
            {{ Form::button('搜索', ['class' => 'btn btn-primary search-btn', 'type' => 'submit']) }}
            {{ Form::button('重置', ['class' => 'btn btn-secondary search-btn clear-form']) }}
            <a href="{{ route('message.template.create') }}">{{ Form::button('新建', ['class' => 'btn btn-success search-btn']) }}</a>
        </div>
    </div>

    <div class="col-xs-12">
        <div class="form-group">
            {{ Form::button('导出模板', ['class' => 'btn btn-info btn-outline search-btn btn-export']) }}
            {{ Form::button('导出所有模板', ['class' => 'btn btn-success btn-outline search-btn btn-export-all']) }}
            {{ Form::button('导入模板', [
                'class' => 'btn btn-danger btn-outline search-btn btn-import',
                'data-toggle' => 'modal',
                'data-target' => '#templateImportModal'
            ]) }}
        </div>
    </div>
    {!! Form::close() !!}
</div>