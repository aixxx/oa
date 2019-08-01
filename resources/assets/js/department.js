require(['jquery'],function($){
    $(function(){
        departments = $("input[name='departments[]']");
        main = $("#main");
        departments.each(function(){
            $(this).click(function(){
                let node = '<label class="custom-control custom-radio custom-control-inline">\n' +
                    '<input type="radio" class="custom-control-input" name="pri_dept_id" value="' + $(this).attr('value') + '">\n' +
                    '<span class="custom-control-label">'+ $(this).next().text() +'</span>\n' +
                    '</label>';

                if($(this).prop("checked") == true)
                {
                    main.append(node);
                }

                if ($(this).prop("checked") == false)
                {
                    main.find("input[value="+$(this).attr('value')+"]").parent().remove()
                }
            })
        })

    })

})


