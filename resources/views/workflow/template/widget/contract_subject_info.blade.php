<div class="form-control form-commit row">
    <table class="table_row">
        <thead>
        <th>合同方</th>
        <th>类型</th>
        <th>主体名称</th>
        <th>操作</th>
        </thead>
        <tbody id="table_body">
        @if(!empty($field_value))
            <?php $subject_info = json_decode($field_value, true);?>
            @foreach($subject_info as $s)
                <tr>
                    <td>{{ $s['0'] }}</td>
                    <td>{{ $s['1'] }}</td>
                    <td>{{ $s['2'] }}</td>
                    <td></td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>

    @if(empty($field_value))
        <input class="btn btn-primary" style="margin-top: 10px;" type="button" id="add_subject"
               value="新增主体">
    @endif
    <input type="hidden" id="tr_num" value='1'>
</div>