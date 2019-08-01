@extends('layouts.main',['title' => $flow->flow_name])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1>{{ $flow->flow_name }}</h1>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div class="card-header">
                @if ($flow->is_abandon)
                    <h4 style="color: red;">注意:【{{ $flow->flow_name }}】流程已更新,请删除后重新发起</h4>
                @endif
            </div>
            <div class="card-body">
                <form id="order-form" action="" method="POST" enctype="multipart/form-data">
                    {{csrf_field()}}
                    {{method_field('PUT')}}
                    <input type="hidden" name="user_id" id="user_id" value="{{ $user_id }}">
                    <input type="hidden" name="flow_id" value="{{ $flow->id }}">
                    <input type="hidden" name="file_source_type" value="workflow">
                    <input type="hidden" name="file_source" value="{{ $flow->flow_no }}">
                    <input type="hidden" name="is_draft" value="">
                    <input type="hidden" name="entry_id" @if(empty($entry)) value="" @else value="{{$entry->id}}" @endif>
                    <div class="form-group">
                        <label for="title">标题*</label>
                        @if(empty($entry))
                            <input type="text" class="form-control" id="title" name="title" placeholder="标题*">
                        @else
                            <input type="text" class="form-control" id="title" name="title" placeholder="标题*" value="{{$entry->title}}">
                        @endif

                    </div>


                    {!! $form_html !!}

                    @if (!$flow->is_abandon)
                        <button type="button" class="btn btn-primary pull-right" id="save-draft-btn" data-href="{{route('workflow.entry.update',['id'=>$entry->id])}}">保存草稿</button>
                        <button type="button" class="btn btn-success pull-right" id="submit-apply" data-href="{{route('workflow.entry.update',['id'=>$entry->id])}}" style="margin-right: 10px;">提交申请</button>
                    @endif
                </form>
            </div>
        </div>
    </section>
@endsection

@section('javascript')
    <script type="text/javascript">
        $('#save-draft-btn').click(function () {
            $('input[name="is_draft"]').val('1'); // 标记为草稿
            // 保存草稿
            $('#submit-apply').trigger('click');
        });
        $('#title').on('change', function () {
            if ($(this).val() != '') {
                $(this).removeClass('input_required');
            } else {
                $(this).addClass('input_required');
            }
        });
        $('#title').trigger('change');
        $('#submit-apply').click(function () {
            $("#title").addClass('input_required_no_show');
            $("#tpl_model").addClass('input_required_no_show');
            if ($('#title').val() == '') {
                message_show_error('标题是必填项');
                return;
            }
            if ($('input[name="is_draft"]').val() != '1' && !can_commit_tpl()) {
                message_show_error('必填项没有填');
                return;
            }

            if ($("#expense_order_table_body")) { //费用报销费用科目鉴别
                var personnelList = <?php echo json_encode(App\Services\Workflow\FlowExpensesReimburse::$personnelType, JSON_UNESCAPED_UNICODE) ?>;
                $("#expense_order_table_body").find("select option:selected").each(function () {
                    personnelIndex = $.inArray($(this).val(), personnelList);
                    if (personnelIndex >= 0) {
                        $('#is_personnel_dept').val(1);
                    }
                });
            }


            var data = $('#order-form').serialize();
            data = data + get_upload_file_ids();
            data = data + get_contract_subject_info_json();
            data = data + computeAllTotalPrice(); //计算采购申请单金额合计
            $('#submit-apply').attr('disabled', true);
            callPostAjax($(this), data, function (res) {
                if (res.success == 1) {
                    message_show_success(res.msg,"{{route('workflow.entry.index')}}");
                } else {
                    $('#submit-apply').removeAttr('disabled');
                    message_show_error(res.msg);
                }
            });
        });

        function computeAllTotalPrice() {
            if (typeof total_all_price != 'undefined') {
                return total_all_price ? '&tpl[total_all_price]=' + total_all_price  : '&tpl[total_all_price]=0';
            } else {
                return "";
            }
        }
    </script>
@endsection

