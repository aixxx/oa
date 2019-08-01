@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="">
            <div class="card card-profile">
                <div class="card-body text-center">
                    <table class="table table-bordered table-striped  dt-select">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>filehash</th>
                            <th>filename</th>
                            <th>storage_system</th>
                            <th>source_type</th>
                            <th>source</th>
                            <th>preview</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($files as $file)
                            <tr>
                                <td>{{$file->id}}</td>
                                <td>{{$file->filehash}}</td>
                                <td>{{$file->filename}}</td>
                                <td>{{$file->storage_system}}</td>
                                <td>{{$file->source_type}}</td>
                                <td>{{$file->source}}</td>
                                <td>{!! \App\Http\Controllers\FileController::showWithHtml($file->id) !!}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="row">
                        <input type="hidden" name="file_source_type" value="test">
                        <input type="hidden" name="file_source" value="test1">
                        <input type="file" class="file file_upload " name="file_upload" data-href="/file" data-auto="1">
                        {{--<label class="form-label">文件上传</label>--}}
                        {{--<input type="file" name="file"/>--}}
                    </div>
                    <div>
                        {{--<a class="button">上传</a>--}}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section("javascript")
    <script src="{{  asset('js/fileinput/fileinput.js')  }}"></script>
@endsection