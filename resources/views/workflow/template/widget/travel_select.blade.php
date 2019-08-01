<select class="form-control travel-selector col-sm-6 col-xs-12" name="tpl[travel_list]"
        data-selected="{{$field_value}}">
    <option value="">请选择出差申请单*</option>
</select>
@if($entry && auth()->id() == $entry->user_id)
    <button class="btn btn-info btn-sm btn-outline btn-travel-detail" data-entry-id="{{$entry->id}}"
            type="button">
        查看申请
    </button>
@elseif (!$entry)
    <button class="btn btn-info btn-sm btn-outline btn-travel-detail hidden"
            type="button">
        查看申请
    </button>
@endif
<div class="row m-t-10">
    <div class="form-group tpl_div col-sm-12 col-xs-12">
        <label class="form-label label_hidden">出差天数：</label>
        <input type="text" class="form-control form-commit" name="tpl[travel_days]" readonly>
    </div>
    <div class="form-group tpl_div col-sm-12 col-xs-12">
        <label class="form-label label_hidden">出差开始日期：</label>
        <input type="text" class="form-control form-commit" name="tpl[travel_date_start]"
               readonly>
    </div>
    <div class="form-group tpl_div col-sm-12 col-xs-12">
        <label class="form-label label_hidden">出差结束日期：</label>
        <input type="text" class="form-control form-commit" name="tpl[travel_date_end]"
               readonly>
    </div>
</div>
<script>
    $(function () {
        loadTravelApplications();
    });

    function loadTravelApplications() {
        $('.travel-selector').change(function () {
            var selectedOption = $(this).find("option:selected");
            var travelDetailBtn = $('.btn-travel-detail');
            var entryId = selectedOption.val();
            if (entryId && entryId > 0) {
                travelDetailBtn.attr('data-entry-id', entryId);
                travelDetailBtn.removeClass('hidden');
            } else {
                travelDetailBtn.removeAttr('data-entry-id');
                travelDetailBtn.addClass('hidden');
            }
            var travelDays = selectedOption.attr('data-travel-days');
            var travelStart = selectedOption.attr('data-travel-start');
            var travelEnd = selectedOption.attr('data-travel-end');
            $('input[name="tpl[travel_days]"]').val(travelDays);
            $('input[name="tpl[travel_date_start]"]').val(travelStart);
            $('input[name="tpl[travel_date_end]"]').val(travelEnd);
        });
        $('.btn-travel-detail').click(function () {
            if ($(this).attr('data-entry-id') > 0) {
                window.open('/workflow/entry/' + $(this).attr('data-entry-id'));
            }
            return false;
        });
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "GET",
            dataType: "json",
            async: true,
            url: "{{route('user.attendance.travels', ['id' => $entry ? $entry->user_id : auth()->id()])}}",
            success: function (response) {
                if (response.code !== 0) {
                    console.error(response.message);
                    return false;
                }
                $.each(response.data, function (index, elemet) {
                    var selectedValue = $('.travel-selector').attr('data-selected');
                    $('.travel-selector').append('<option value="' + elemet.entry_id +
                        '" data-travel-days="' + elemet.days + '" data-travel-start="' +
                        elemet.travel_start + '" data-travel-end="' + elemet.travel_end + '"' +
                        (selectedValue == elemet.entry_id ? 'selected' : '') + '>' +
                        elemet.title + '</option>');
                    $('.travel-selector').change();
                });
                // $('.travel-selector').change();
            },
            error: function (response) {
                console.log(response);
            }
        });
    }
</script>