<div>
    {{ $slot }}
    <div id="tree">{{ $title }}</div>
    @section('javascript')
        <script src="{{  asset('js/bootstrap-treeview.js')  }}"></script>
        <script>
            function getTree() {
                // Some logic to retrieve, or generate tree structure
                $.get('/departments/all', function (data, status) {
                    treeData = [formatTree(data)];
                    console.log(treeData)
                    $treeview = $('#tree').treeview({
                        //color: "#967ADC",
                        expandIcon: 'glyphicon glyphicon-triangle-right',
                        collapseIcon: 'glyphicon glyphicon-triangle-bottom',
                        nodeIcon: 'glyphicon glyphicon-folder-close',
                        data: treeData,
                        // onNodeSelected: function (event, node) {
                        //
                        //     location.href = "/deptuser?departid=" + node.departId;
                        //
                        //
                        // },
                        onNodeHover: function (event, node) {
                            let eventType = event.type;
                            if (eventType == "mouseenter") {
                                action = node.$el.find(".item-action");
                                departId = node.departId;
                                if (action.length < 1) {
                                    template = $('<div class="dropdown float-right item-action tree-node-action" style="height:20px;">' +
                                        '<a href="javascript:void(0)" class="btn btn-fab tree-node-action" data-toggle="dropdown" aria-expanded="false">' +
                                        '<i class="icon dripicons-dots-3 rotate-90 font-size-24 tree-node-action"></i>\n' +
                                        '</a>' +
                                        '<div class="dropdown-menu dropdown-menu-right tree-node-action">' +
                                        '<a href="javascript:void(0)" class="dropdown-item tree-node-action" data-toggle="modal" data-target="#myModal" data-department="' + node.departId + '"> <i class="icon dripicons-view-list tree-node-action"></i> 添加部门</a>' +
                                        '<a href="javascript:void(0)" class="dropdown-item tree-node-action" data-toggle="modal" data-target="#editDepartModal" data-parentid="' + node.parent_id + '" data-parentname="' + node.parent_name + '" data-sync="' + node.is_sync_wechat + '" data-order="' + node.order + '" data-text="' + node.text + '"  data-department="' + node.departId + '"> ' +
                                        '<i class="zmdi zmdi-edit zmdi-hc-fw tree-node-action"></i> 编辑部门</a>' +
                                        '<a href="javascript:void(0)" class="dropdown-item tree-node-action" id="dept" data-toggle="modal" data-target="#delModal" data-department="' + node.departId + '"><i class="icon dripicons-trash tree-node-action"></i>删除部门</a>' +
                                        '<a href="javascript:position('+ node.departId +')" class="dropdown-item tree-node-action " data-department="' + node.departId + '"><i class="icon dripicons-view-list tree-node-action"></i>职务管理</a>' +
                                        '</div>' +
                                        '</div>'
                                    );
                                    node.$el.append(template);
                                }
                                action.show();
                            }
                            if (eventType == "mouseleave") {
                                action = node.$el.find(".item-action");
                                action.hide();
                            }
                        },
                    });
                });
            }

            function position(id) {
                console.log(id);
                window.location.href = "{{route('position.index')}}?deptId="+id;
            }

            function GetQueryString(name) {
                var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
                var r = window.location.search.substr(1).match(reg);
                if (r != null) return unescape(r[2]);
                return null;
            }

            function formatTree(data) {

                let childNodes = [];
                if (data.childList !== undefined && data.childList.length > 0) {
                    for (var index = 0; index < data.childList.length; index++) {
                        let tmp = formatTree(data.childList[index])
                        //console.log(tmp)
                        childNodes.push(tmp);
                    }
                } else {

                }
                departid = GetQueryString("departid");
                if (departid && departid == data.id) {
                    node = {
                        text: data.name,
                        departId: data.id,
                        nodes: childNodes,
                        path: data.path,
                        order: data.order,
                        is_sync_wechat: data.is_sync_wechat,
                        parent_id: data.parent_id,
                        parent_name: data.parent_name,
                        state: {selected: true, expanded: true}
                    };
                    console.log(node);
                } else {
                    node = {
                        text: data.name,
                        departId: data.id,
                        path: data.path,
                        nodes: childNodes,
                        order: data.order,
                        is_sync_wechat: data.is_sync_wechat,
                        parent_id: data.parent_id,
                        parent_name: data.parent_name,
                    };
                }

                return node;

            }

            function findNode(data, departId) {
                for (index in data) {
                    dNode = data[index]
                    if (dNode.departId == departId) {
                        return dNode;
                    }
                    if (dNode.nodes !== undefined && dNode.nodes.length > 0) {

                        ret = findNode(dNode.nodes, departId);
                        if (ret != null) {
                            return ret;
                        }
                    }


                }
                return null;
            }

            if ($("#tree").length > 0) {
                //元素存在时执行的代码
                getTree();
            }
        </script>
        <script>

            //创建部门
            $('#myModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                departmentId = button.data('department');
                // Extract info from data-* attributes
                // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
                // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
                var modal = $(this);


            });

            save = $("#save");
            save.click(function () {
                department_name = $("#department_name").val();
                department_order = $("#department_order").val();
                department_sync = $("#add_is_sync_wechat").val();

                if (!department_name) {
                    alert("部门不能为空");
                    return;
                }

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    dataType: "json",
                    url: "{{  route('departments.store')  }}",
                    data: {
                        'name': department_name,
                        'order': department_order,
                        'departmentId': departmentId,
                        'is_sync_wechat': department_sync
                    },
                    success: function (response) {
                        if (response.status == 'success') {
                            location.reload();
                            window.location.href = "{{  route("dept.depart")  }}";
                        }

                        $('#myModal').modal('hide');
                    }
                })
            });

            //编辑部门
            $('#editDepartModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                edit_dept_id = button.data('department');
                edit_dept_name = button.data('text');
                edit_dept_order = button.data('order');
                edit_dept_sync_wechat = button.data('sync');
                edit_parent_name = button.data('parentname');
                edit_parent_id = button.data('parentid');

                $("#edit_department_name").val(edit_dept_name);
                $("#edit_department_order").val(edit_dept_order);

                if (edit_dept_sync_wechat == 1) {
                    $("#edit_is_sync_wechat").find("option[value='1']").attr("selected", true);
                } else if (edit_dept_sync_wechat == 0) {
                    $("#edit_is_sync_wechat").find("option[value='0']").attr("selected", true);
                }

                $("#edit_department_parent").val(edit_parent_name);
            });

            $("#edit_save").off('click').click(function () {
                after_edit_dept_name = $("#edit_department_name").val();
                after_edit_dept_order = $("#edit_department_order").val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    dataType: "json",
                    url: "{{  route('departments.update')  }}",
                    data: {
                        'name': after_edit_dept_name,
                        'order': after_edit_dept_order,
                        'is_sync_wechat': edit_dept_sync_wechat,
                        'departmentId': edit_dept_id,
                        'parent_id': edit_parent_id
                    },
                    success: function (response) {
                        console.log(response);
                        if (response.status == 'success') {
                            alert("编辑部门成功");
                            location.reload();
                        } else {
                            alert("编辑部门失败");
                        }
                    }
                })
            });


            //删除部门
            $('#delModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                deptId = button.data('department');
            })

            del_dept = $("#del_dept");

            del_dept.click(function () {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    dataType: "json",
                    url: "{{  route('departments.delete')  }}",
                    data: {'deptId': deptId},
                    success: function (response) {
                        if (response.status == 'success') {
                            $('#delModal').modal('hide');
                            window.location.href = "{{  route("dept.depart")  }}";
                        }

                        if (response.status == 'error') {
                            alert(response.messages);
                        }

                        if (response.status == 'failed') {
                            alert(response.messages);
                        }

                    }
                })
            });

            $('#input-import').click(function () {

                var formData = new FormData();
                var name = $("#inputImport").val();
                formData.append("inputImport",$("#inputImport")[0].files[0]);
                formData.append("name",name);
                console.log(formData);
                layer.confirm("确定导入文件中的数据?",function (index) {
                    //layer.close(index);
                    //layer.load(3);
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        dataType: "json",
                        //data: $('#input-import').serialize(),
                        data: formData,
                        url: "{{  route('dept.batch_import')  }}",
                        processData : false,
                        contentType : false,
                        success: function (response) {
                            if (response.status == 'success') {
                                window.location.href = "{{ route('dept.depart') }}";
                            }else {
                                layer.close(layer.load(3));
                                layer.msg(response.message);
                            }
                        },
                        error: function (e) {
                            var json = (e.responseJSON);
                            for (var n in json['errors']) {
                                layer.close(layer.load(3));
                                layer.msg(json['errors'][n][0]);
                                //layer.msg(json['errors'][n][0])
                                return;
                            }
                        }
                    })
                });
            });
        </script>
    @endsection
</div>