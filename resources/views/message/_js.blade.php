<script>
    function postDelete(obj) {
        var method = obj.attr('data-method').toUpperCase();
        var confirmMessage = obj.attr('data-confirm');
        var url = obj.attr('data-href');
        if (method === 'POST') {
            if (window.confirm(confirmMessage)) {
                callPostAjax(obj, {}, function (response) {
                    if (response.code === 0) {
                        alert(response.message);
                        window.location.href = "{{ route('message.template.index') }}";
                    } else {
                        alert(response.message);
                    }
                });
            }
        }
    }
</script>