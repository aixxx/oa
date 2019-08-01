@extends('layouts.main', ['title' => '消息模板'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">系统管理</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">系统管理</a></li>
                        <li class="breadcrumb-item active" aria-current="page">消息模板</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <style>
            .btn-flat {
                -webkit-box-shadow: none;
                -moz-box-shadow: none;
                box-shadow: none;
                border-radius: 0;
            }

            .table th, .table td {
                word-break: keep-all;
            }
        </style>
        <div class="card card-pills">
            <div class="card-header">
                <div class="card-title">消息模板</div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    @include('message._search', ['searchModel' => $searchModel])

                    {{ Form::open(['method' => 'POST', 'id' => 'data-form']) }}
                    <table class="table table-bordered">
                        <thead style="background-color: #098ddf; color: #ffffff;">
                        <tr>
                            <th><input type="checkbox" class="input-check-all"/></th>
                            <th>模板键值</th>
                            <th>名称</th>
                            <th>类型</th>
                            <th>签名</th>
                            <th>推送方式</th>
                            <th>模板标题</th>
                            <th>模板内容</th>
                            <th>状态</th>
                            <th>更新时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($messageTemplates as $messageTemplate)
                            <tr>
                                <td><input type="checkbox" name="templates[]"
                                           value="{{ $messageTemplate->template_id }}"
                                           class="input-check"/></td>
                                <td>{{ $messageTemplate->template_key }}</td>
                                <td>{{ $messageTemplate->template_name }}</td>
                                <td>{{ App\Constant\CommonConstant::MESSAGE_TYPE_MAPPING[$messageTemplate->template_type] }}</td>
                                <td>{{ $messageTemplate->template_sign }}</td>
                                <td>{{ App\Constant\CommonConstant::MESSAGE_PUSH_TYPE_MAPPING[$messageTemplate->template_push_type] }}</td>
                                <td>{{ mb_strlen($messageTemplate->template_title) > 20 ? mb_substr($messageTemplate->template_title, 0, 20) . '...' :  $messageTemplate->template_title}}</td>
                                <td>{{ mb_strlen($messageTemplate->template_content) > 20 ? mb_substr($messageTemplate->template_content, 0, 20) . '...' :  $messageTemplate->template_content}}</td>
                                <td>{{ App\Constant\CommonConstant::STATUS_MAPPING[$messageTemplate->template_status] }}</td>
                                <td>{{ $messageTemplate->template_updated_at }}</td>
                                <td>
                                    <a href="{{route('message.template.view', ['id' => $messageTemplate->template_id])}}">
                                        {{ Form::button('查看', ['class' => 'btn btn-sm btn-secondary btn-outline btn-flat m-b-5']) }}
                                    </a>
                                    <a href="{{route('message.template.update', ['id' => $messageTemplate->template_id])}}">
                                        {{ Form::button('编辑', ['class' => 'btn btn-sm btn-info btn-outline btn-flat m-b-5']) }}
                                    </a>
                                    <a href="javascript:void(0);" onclick="postDelete($(this));"
                                       data-href="{{ route('message.template.delete', ['id' => $messageTemplate->template_id]) }}"
                                       data-confirm="确认删除?" data-method="post">
                                        {{ Form::button('删除', ['class' => 'btn btn-sm btn-danger btn-outline btn-flat m-b-5']) }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ Form::close() }}
                </div>
            </div>
            <div class="card-footer">
                {!! $messageTemplates->appends($searchParams)->links() !!}
            </div>
        </div>
        <div class="modal fade" id="templateImportModal" tabindex="-1" role="dialog"
             aria-labelledby="templateImportModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="templateImportModal">消息模板导入</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="file-loading">
                            <input id="input-b2" name="import-file" type="file" class="file">
                        </div>
                        <div id="kartik-file-errors"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('javascript')
    <script src="{{ asset('js/fileinput/fileinput.js') }}"></script>
    <script>
        $(function () {
            $('.input-check-all').click(function () {
                $('.input-check').each(function () {
                    $(this).attr('checked', !($(this).attr('checked')));
                });
            });

            $('.btn-export-all').click(function () {
                $('#data-form').attr("action", "{{ route('message.template.exportAll') }}").submit();
            });

            $('.btn-export').click(function () {
                if ($('.input-check:checked').length < 1) {
                    toastr.error('请至少选择一个模板');
                    return;
                }

                $("#data-form").attr("action", "{{ route('message.template.export') }}").submit();
            });
        });

        $("input[name='import-file']").fileinput({
            uploadUrl: "{{ route('message.template.import') }}",
            showPreview: false,
            allowedFileExtensions: ['json'],
            enctype: 'multipart/form-data',
            uploadExtraData: {"_token": $('meta[name="csrf-token"]').attr('content')},
        }).on("fileuploaded", function (event, data, previewId, index) {
            alert(data.response.messages);
            if (data.response.code === 0) {
                window.location.reload();
            }
        });
    </script>
    @include('message._js')
@endsection