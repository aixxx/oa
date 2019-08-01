<div class="form-control form-commit row">
    <table class="table_row">
        <thead>
        <th>费用发生日期*</th>
        <th>报销事由*</th>
        <th>报销金额*</th>
        <th>单据数(张)*</th>
        </thead>
        <tbody id="expense_order_table_body">
        @if($expense_order_num || ($entry && $entry->isDraft()))
            @for($i=1;$i<$expense_order_num;$i++)
                @if($entry_data && $entry_data->where('field_name','expense_order_date_' . $i)->first() && $entry_data->where('field_name','expense_order_date_' . $i)->first()->field_value)
                    <tr id="expense_order_tr_{{ $i }}">
                        <td><input class="datepicker form-control form-commit"
                                   name="tpl[expense_order_date_{{$i}}]"
                                   id=name="expense_order_date_{{$i}}"
                                   value="{{ $entry_data->where('field_name','expense_order_date_' . $i)->first()->field_value }}"></td>
                        <td><input class="form-control form-commit" name="tpl[expense_order_reason_{{$i}}]"
                                   value="{{ $entry_data->where('field_name','expense_order_reason_' . $i)->first()->field_value }}"></td>
                        <td><input class="form-control form-commit data_sum"
                                   name="tpl[expense_order_amount_{{$i}}]"
                                   id="expense_order_amount_{{$i}}"
                                   value="{{ $entry_data->where('field_name','expense_order_amount_' . $i)->first()->field_value }}"></td>
                        <td><input class="form-control form-commit expense_order_num"
                                   name="tpl[expense_order_num_{{$i}}]" id="expense_order_num_{{$i}}"
                                   value="{{ $entry_data->where('field_name','expense_order_num_' . $i)->first()->field_value }}">
                        </td>
                    </tr>
                @endif
            @endfor
        @else
            @for($i=1;$i<=5;$i++)
                <tr id="expense_order_tr_{{ $i }}">
                    <td><input class="datepicker form-control form-commit"
                               name="tpl[expense_order_date_{{$i}}]"
                               id=name="expense_order_date_{{$i}}" value=""></td>
                    <td><input class="form-control form-commit" name="tpl[expense_order_reason_{{$i}}]"
                               value=""></td>
                    <td><input class="form-control form-commit data_sum"
                               name="tpl[expense_order_amount_{{$i}}]"
                               id="expense_order_amount_{{$i}}" value=""></td>
                    <td><input class="form-control form-commit expense_order_num"
                               name="tpl[expense_order_num_{{$i}}]"
                               id="expense_order_num_{{$i}}" value=""></td>
                </tr>
            @endfor
        @endif
        </tbody>
    </table>

    @if(!$expense_order_num || ($entry && $entry->isDraft()))
        <input class="btn btn-primary" style="margin-top: 20px;float: right;" type="button"
               id="add_expense_order"
               value="新增报销项">
    @endif
    <input type="hidden" id="expense_order_num" name="tpl[expense_order_num]"
           value='<?php echo $cnt ?? 6;?>'>
</div>
