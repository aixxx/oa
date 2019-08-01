@if(!empty($field_value)&&$field_value=explode(",",$field_value))
    <div class="row">
        @foreach($field_value as $id)
            <div class="col-sm-4 col-xs-12 preview-div" data-file-id="{{$id}}">
                @if(!$entry||$entry->isDraft())
                    <button type="button" class="close preview-fileinput-remove" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                @endif
                <div class="preview-file">
                    {!! \App\Http\Controllers\FileController::showWithHtml($id) !!}
                </div>
            </div>
        @endforeach
    </div>
@endif
<input type="file" name="{{$v->field}}" class="form-control form-commit file_upload"
       data-href="{{route('file.store')}}">