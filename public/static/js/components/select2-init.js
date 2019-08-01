(function(window, document, $, undefined) {
	  "use strict";
	$(function() {

		// basic
		$("#s2_demo1").select2();

		// nested
		$('#s2_demo2').select2({
			placeholder: "请选择"
		});

		// multi select
		$('#s2_demo3').select2({
			placeholder: "请选择"
		});

		// placeholder
		$("#s2_demo4").select2({
			placeholder: "请选择",
			allowClear: true
		});

		// Minimum Input
		$("#s2_demo5").select2({
			minimumInputLength: 2,
			placeholder: "请选择",
		});

        $('#s2_demo6').select2({
            placeholder: "请选择"
        });

        $(".s2_demo4").select2({
            placeholder: "请选择",
            allowClear: true
        });

        $('.s2_demo2').select2({
            placeholder: "请选择"
        });

        $('.s2_demo6').select2({
            placeholder: "请选择"
        });
        $('.s2_demo3').select2({
            placeholder: "请选择",
            allowClear: true
        });
	});

})(window, document, window.jQuery);
