<!-- ================== GLOBAL VENDOR SCRIPTS ==================-->
<script src="/static/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="/static/vendor/js-storage/js.storage.js"></script>
<script src="/static/vendor/js-cookie/src/js.cookie.js"></script>
<script src="/static/vendor/pace/pace.js"></script>
<script src="/static/vendor/metismenu/dist/metisMenu.js"></script>
<script src="/static/vendor/switchery-npm/index.js"></script>
<script src="/static/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js"></script>
<!-- ================== GLOBAL APP SCRIPTS ==================-->
<script src="/static/js/global/app.js"></script>

<script src="/js/select2/select2.min.js"></script>
<script src="/js/select2/zh-CN.js"></script>
<script src="/js/toastr.min.js"></script>
<script src="/vendor/layer/build/layer.js"></script>
<script>
    $("#menu").metisMenu();
    $(document).ready(function () {
        $(".print").on('click', function () {
            window.print();
        });
        var path = location.pathname;
        path = path.replace(/\/search/, "");

        $("#menu li").removeClass("active");
        $("#menu li > li a").removeClass("active");
        var anchor = $("a[href$='" + decodeURI(path) + "']");

        anchor.parent().addClass("active");
        var expand = function (el) {
            if (el && el.length > 0) {
                el.addClass("active");
                var ul = el.parent().closest("ul");
                if (ul && ul.length > 0) {
                    ul.attr("aria-expanded", "true").removeClass("collapse");
                    ul.prev("a").attr("aria-expanded", "true").addClass("active");
                    ul.parent().addClass("active");
                }
                expand(ul);
            }
        };

        expand(anchor);
        //远程筛选
        $("select.select_user_name").select2({
            ajax: {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{ route('users.ajax_search',['type' => 'on_job']) }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },

                cache: true
            },
            placeholder: '请输入',
            escapeMarkup: function (markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 1,
            templateResult: formatRepoName, // omitted for brevity, see the source of this page
            templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
        });


        //远程筛选
        $("select.select_all_user_name").select2({
            ajax: {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{ route('users.ajax_search',['type' => 'all']) }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },

                cache: true
            },
            placeholder: '请输入',
            escapeMarkup: function (markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 1,
            templateResult: formatRepoName, // omitted for brevity, see the source of this page
            templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
        });

        function formatRepoName(repo) {
            console.log(repo);
            var markup = "<div>" + repo.text + "</div>";
            return markup;
        }

        function formatRepoSelection(repo) {
            return repo.full_name || repo.text;
        }
    });

    function callAjax(self, method, data, when_success, when_error) {
        if (!when_success) {
            when_success = function (response) {
                if (response.status == 'success' && response.message != '') {
                    alert(response.message);
                }
                window.location.reload();
            }
        }
        if (!when_error) {
            when_error = function (response) {
                if (response.status == 401) {
                    alert("当前为状态为：未登录");
                    window.location.reload();
                } else if (response.status == 403) {
                    alert("请联系管理员开启权限");
                } else {
                    alert(response.responseJSON.message);
                }
            }
        }
        if (!data) {
            data = {};
        }
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: method,
            dataType: "json",
            url: self.attr('data-href'),
            data: data,
            success: function (response) {
                when_success(response)
            },
            error: function (response) {
                when_error(response);
            }
        })
    }

    function callDeleteAjax(self, data, when_success, when_error) {
        callAjax(self, 'DELETE', data, when_success, when_error)
    }

    function callPostAjax(self, data, when_success, when_error) {
        callAjax(self, 'POST', data, when_success, when_error)
    }

    function callGetAjax(self, data, when_success, when_error) {
        callAjax(self, 'GET', data, when_success, when_error)
    }

    function callPutAjax(self, data, when_success, when_error) {
        callAjax(self, 'PUT', data, when_success, when_error)
    }

    function fadeAlert(self, message, time, color) {
        alert_node = '<div class="alert alert-' + color + ' alert-dismissible fade show" role="alert">' +
                '<strong>' + message + '</strong>' +
                '</div>';
        self.prepend(alert_node);
        $(".alert-dismissible").fadeOut(time);
    }

    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-center-center",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "3000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    function message_show_info(mes) {
        toastr.info(mes);
    }

    function message_show_warning(mes) {
        toastr.warning(mes);
    }

    function message_show_success(mes, url) {
        if (url) {
            toastr.options.onHidden = function () {
                window.location.href = url;
            };
        }else{
            toastr.options.onHidden = function () {
                window.location.reload();
            };
        }
        toastr.success(mes);
    }

    function message_show_error(mes) {
        toastr.error(mes);
    }

    $(function () {
        $('.simple_post').on('click', function () {
            callPostAjax($(this));
        });
        $('.simple_put').on('click', function () {
            callPutAjax($(this));
        });
        $('.simple_delete').on('click', function () {
            callDeleteAjax($(this));
        });
    })
</script>
@yield('javascript')