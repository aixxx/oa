@extends('layouts.admin')

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">添加</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>OA基础分类选项</li>
                        <li class="breadcrumb-item active" aria-current="page">添加</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            {{--<h5 class="card-header">添加权限</h5>--}}
            <form action="{{ route('admin.vueaction.store') }}" id="con_submit" method="POST" class="form-horizontal">
			<input type="hidden" name="id" value="{{ $items->id or '' }} ">
            <input type="hidden" name="parent_id" value="{{ $parent_id or 0 }} ">
                {{csrf_field()}}
                <div class="form-body">
                    <div class="card-body">
                        <div class="row" style="margin-bottom:20px;">
							<div class="col-lg-6" >
								 <div class="form-group">
								<label class="control-label text-right col-sm-3">名称：</label>
								<div class="col-sm-9" style="float:right">
									<input type="text" class="form-control" id="title" name="title" value="{{ $items->title or '' }}" placeholder="名称">
									@if($errors->has('title'))
										<p class="help-block">
											{{ $errors->first('title') }}
										</p>
									@endif
								</div>
								</div>
							</div>
							<div class="col-lg-6" style="margin-bottom:20px;">
								 <div class="form-group">
								<label class="control-label text-right col-sm-3">编号：</label>
								<div class="col-sm-9" style="float:right">
									<input type="text" class="form-control" id="vue_path" name="vue_path" value="{{ $items->vue_path or '' }}" placeholder="编号">
									@if($errors->has('vue_path'))
										<p class="help-block">
											{{ $errors->first('vue_path') }}
										</p>
									@endif
								</div>
								</div>
							</div>
                        </div>
                        </div>
						 <h6 class="xj-border-bottom-gray xj-sup-title clearfix">
                                    <strong>  接口路由</strong>
                         </h6>

                    <div class="form-group row">
                        <label class="control-label text-right col-md-3"></label>
                        <div class="col-md-8">

                            @php
                                if (!empty($items)) {
                                        $count = 0;
                                        echo "<fieldset>";
                                        foreach ($items as $val) {
                                                $count++;
                                                echo "<input value='" . $val->id . "' type='checkbox' name='name_item[]' id='item_" .
                                                    $val['id'] . "'>&nbsp;";
                                                echo "<label for='item_" . $val->id . "'>" . $val->title .
                                                    "</label>&nbsp;&nbsp;";
                                                echo ($count % 6) == 0 ? '<br>' : '';
                                        }
                                        echo "</fieldset>";
                                }
                            @endphp
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="row">
                            <div class="offset-sm-3 col-md-5">

                                <input type="button" id="submit" data-toggle="modal"
                                       class="btn btn-primary xj-btn xj-btn-primary xj-btn-primary-lg mgr20 save"
                                       value="保存">
                                <button type="button"
                                        class="btn btn-default xj-btn xj-btn-normal xj-btn-normal-lg mgr20 give_up"
                                        data-toggle="modal" data-target="">放弃
                                </button>
                                <button type="button"
                                        class="btn btn-default xj-btn xj-btn-normal xj-btn-normal-lg"
                                        data-toggle="modal" data-target="" onclick="history.go(-1)">返回
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
			 <!-- Modal 保存-->
    <div class="modal fade" id="save" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog xj-modal-dialog xj-modal-400"
             role="document">
            <div class="modal-content xj-modal-content">
                <div class="modal-header xj-modal-header">
                    <button type="button" class="close xj-modal-close"
                            data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">X</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">&nbsp;</h4>
                </div>
                <div class="modal-body text-center">
                    <h3 class="xj-font-color xj-marginBottom20">保存成功！</h3>
                </div>
                <div class="modal-footer xj-modal-footer">
                    <div class="row">
                        <div class="col-lg-12 text-center">
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal 放弃-->
    <div class="modal fade" id="cancel" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog xj-modal-dialog xj-modal-400"
             role="document">
            <div class="modal-content xj-modal-content">
                <div class="modal-header xj-modal-header">
                    <button type="button" class="close xj-modal-close"
                            data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">X</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">&nbsp;</h4>
                </div>
                <div class="modal-body text-center">当前内容未保存，确定放弃？</div>
                <div class="modal-footer xj-modal-footer">
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <button type="button"
                                    class="btn btn-primary xj-btn xj-btn-primary cancel_ok">确定
                            </button>
                            <button type="button"
                                    class="btn btn-default xj-btn xj-btn-normal"
                                    data-dismiss="modal">取消
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
        </div>
    </section>
	 <script>
	  //add tr
        $("#add-tr").on("click", function () {
            var tpl = $('#new_tpl').html();
            var trlength = $('tbody tr').length - 2;
            $('#list').before('<tr><td><a class="xj-td-link xj-td-link-underline0" href="javascript:;" onclick="delProduct(this)">删除</a></td>\
        <td><input type="text"  name="itemd[' + trlength + '][title]"class="form-control xj-form-control" value=""> {{ $errors->has('title') ? $errors->first('title') : '' }}</span></td>\
		<td><input type="text" name="itemd[' + trlength + '][path]" class="form-control xj-form-control" value=""> {{ $errors->has('path') ? $errors->first('path') : '' }}</span></td>\
        <input type="hidden" name="itemd[' + trlength + '][id]"  value="">\
        ');
        });
		 $(".cancel_ok").click(function () {
            window.location.href = "{{route('admin.vueaction.index')}}";
        })
		
		function delProduct(tr) {
            $(tr).parents("tr").remove();
        }

		$(".xj-tr-add").click(function () {
            var str = $(this).parents("tr").prev("tr").clone();
            $(this).parents("tbody").find("tr:first-child").before(str);
        });

        function trDel(delbtn) {
            $(delbtn).parents("tr").remove();
        }
		function QueryString() {
            var arg1 = arguments[0];
            var arg2 = arguments[1];
            if (arguments.length > 1 && arg1 != "" && arg2 != "" && arg1 != undefined && arg2 != undefined) {
                var b = arguments[0].match(new RegExp("[?&]" + arg2 + "=([^&]*)(&?)", "i"));
            } else if (arg1 != "" && arg1 != undefined) {
                var b = location.search.match(new RegExp("[?&]" + arg1 + "=([^&]*)(&?)", "i"));
            }
            return b ? b[1] : b
        }
		
		$(function () {
			$('.give_up').click(function () {
                $("#cancel").modal('show');
            })
			$("#submit").click(function () {
				$.ajax({
					url: "{{ route('admin.vueaction.store') }}",
					type: "post",
					dataType: "json",
					data: $("form").serialize()
				}).done(function (rsp) {
					if (rsp.code == 200) {
						layer.msg(rsp.msg, {icon: 1});
						setTimeout(function () {
							location.href = "{{route('admin.vueaction.index')}}";
						}, 2000);
					}else if(rsp.code == 403){
						layer.msg(rsp.data || '抱歉！您没有该操作权限！', {icon: 5});
						//Commitlock = true;
					} else {
						layer.msg(rsp.msg || '提交失败请重试', {icon: 5});
						//Commitlock = true;
					}
				}).fail(function (e) {
					var json = (e.responseJSON);
					for (var n in json) {
						$('[name=' + n + ']').siblings(".help-block").text(json[n]).css('color', 'red');
					}
					//Commitlock = true;
				});
				
				
				/*$.ajax({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type: "POST",
					dataType: "json",
					data: $("form").serialize(),
					url: "{{ route('admin.vueaction.store') }}",
					success: function (response) {
						layer.msg(response.message, {icon: 1});
						//alert(response.message);
						if (response.code == '200') {
							window.location.href = "{{ route('admin.vueaction.index') }}";
						}
					}
					
				})*/
			});
		})		
		
		

	 
	  </script>
	 
@endsection

