@extends('layouts.main',['title' => '流程设置'])

@section('content')
    <form action="" method="get" id="export_form">
        <input type="hidden" name="checkedAllItems" id="checkedAllItems">
    </form>
    <header class="page-header">
        <div class="d-flex align-items-center">

            <div class="mr-auto">
                <h1>流程设置</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">流程管理</li>
                        <li class="breadcrumb-item active" aria-current="page">流程设置</li>
                    </ol>
                </nav>
            </div>
            <div class="actions top-right">
                <a href="{{ route('workflow.flow.create') }}" class="btn btn-primary">
                    新建流程
                </a>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                    导入流程
                </button>
                <button data-href="{{ route('workflow.flow.export') }}" class="btn btn-primary export-btn">
                    导出流程
                </button>
            </div>
        </div>
    </header>

    <section class="page-content container-fluid">
        <div class="card">
            <div class="card-body">

                <table class="table">
                    <thead>
                    <tr>
                        <th><input type="checkbox" id="checkAll"/></th>
                        <th>流程id</th>
                        <th>流程名称</th>
                        <th>分类</th>
                        <th>模板</th>
                        <th>领导人审批条线</th>
                        <th>状态</th>
                        <th>创建时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($flows as $v)
                        <tr>
                            <td><input type="checkbox" name="checkItems" value="{{$v->id}}"/></td>
                            <td>{{$v->id}}</td>
                            <td scope="row">{{$v->flow_name}}</td>
                            <td>
                                {{ $v->type ? $v->type->type_name : '未分类' }}
                            </td>
                            <td>
                                {{$v->template ? $v->template->template_name : '暂无模板'}}
                            </td>
                            <td>
                                {{$flow_leader_links[$v->leader_link_type]}}
                            </td>
                            <td>
                                @if ($v->is_publish)
                                    <span class="badge badge-pill badge-success">已发布</span>
                                @else
                                    <span class="badge badge-pill badge-warning">未发布</span>
                                @endif
                                @if ($v->is_abandon)
                                    <span class="badge badge-pill badge-danger">已废弃</span>
                                @endif
                            </td>
                            <td>{{$v->created_at}}</td>
                            <td>
                                <a href="{{ route('workflow.flow.design', $v->id) }}" class="badge badge-success">审批关系设计</a>
                                <a href="{{route('workflow.flow.edit',['id'=>$v->id])}}" class="badge badge-primary">编辑</a>

                                <a href="javascript:void(0);" data-href="{{route('workflow.flow.clone_new_version',['id'=>$v->id])}}"
                                   class="badge badge-info clone-flow">克隆新版本</a>
                                @if ($v->is_abandon)
                                    <a href="javascript:void(0);" data-href="{{route('workflow.flow.set_abandon')}}" data-flow-id="{{$v->id}}"
                                       class="badge badge-danger unabandon-flow">取消废弃</a>
                                @else
                                    <a href="javascript:void(0);" data-href="{{route('workflow.flow.set_abandon')}}" data-flow-id="{{$v->id}}"
                                       class="badge badge-danger abandon-flow">废弃</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">流程导入</h5>
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
        <script src="{{  asset('js/fileinput/fileinput.js')  }}"></script>
    </section>
@endsection

@section('javascript')
    <script>
        $(function () {
            $('.clone-flow').click(function () {
                if (confirm('确定发布生成新版本吗?发布新版本后,老版本将废弃。')) {
                    callGetAjax($(this), null)
                }
            });
            $('.unabandon-flow').click(function () {
                if (confirm('确定要取消废弃状态?')) {
                    var flowId = $(this).attr('data-flow-id');
                    var state = '{{\App\Models\Workflow\Flow::ABANDON_NO}}';
                    callPutAjax($(this), {'flow_id': flowId, 'abandon_state': state});
                }
            });
            $('.abandon-flow').click(function () {
                var flowId = $(this).attr('data-flow-id');
                var state = '{{\App\Models\Workflow\Flow::ABANDON_YES}}';
                callPutAjax($(this), {'flow_id': flowId, 'abandon_state': state});
            });

            $('#checkAll').click(function () {
                $(":checkbox").each(function () {
                    $(this).attr("checked", !($(this).attr("checked")));
                });
            });

            $('.export-btn').click(function () {
                var checkedAll = '';
                $('input:checked[name=checkItems]:checked').each(function (k) {
                    if (k == 0) {
                        checkedAll = $(this).val();
                    } else {
                        checkedAll += ',' + $(this).val();
                    }
                });

                if (!checkedAll) {
                    alert('请选择一个流程');
                    return;
                }

                $('#checkedAllItems').val(checkedAll);
                $("#export_form").attr("action", "{{ route('workflow.flow.export') }}").submit();
            });
        });

        $("input[name='import-file']").fileinput({
            uploadUrl: "{{  route('workflow.flow.import')  }}",
            showPreview: false,
            allowedFileExtensions: ['json'],
            enctype: 'multipart/form-data',
            uploadExtraData: {"_token": $('meta[name="csrf-token"]').attr('content')},
        }).on("fileuploaded", function (event, data, previewId, index) {
            alert(data.response.messages);
            if (data.response.status == "success") {
                window.location.reload();
            }
        });
    </script>
@endsection
