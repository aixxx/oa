@extends('layouts.web')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-2">
                <div class="card card-profile">
                    <div class="card-body text-center">

                        <table class="table table-bordered table-striped {{ count($users) > 0 ? 'datatable' : '' }} dt-select">
                            <thead>
                            <tr>
                                <th style="width:48px;text-align:center;">ID</th>
                                <th>姓名</th>
                                <th>英文名</th>
                                <th>职位</th>
                                <th>操作</th>
                            </tr>
                            </thead>

                            <tbody>
                            @if (count($users) > 0)
                                @foreach ($users as $user)
                                    <tr data-entry-id="{{ $user->id }}">
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->chinese_name }}</td>
                                        <td>{{ $user->english_name }}</td>
                                        <td>{{ $user->position }}</td>
                                        <td>
                                            <a href="{{ route('grantuser.grant',[$user->id]) }}"
                                               class="btn btn-sm btn-primary">行使他的权力</a>
                                        </td>

                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3">尚未配置任何权限</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
