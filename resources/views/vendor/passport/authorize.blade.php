@extends('layouts.auth')

@section('content')
    <h5 class="text-center sign-in-heading">通行证登录授权</h5>
    <p class="text-center text-muted">即将登录 <strong>{{ $client->name }}</strong> ，请确认是本人操作</p>
    <hr class="dashed">
    <!-- Scope List -->
    @if (count($scopes) > 0)
        <div class="scopes">
            <ul>
                @foreach ($scopes as $scope)
                    <li>{{ $scope->description }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <hr class="dashed">

    <div class="buttons text-center">
        <!-- Authorize Button -->
        <form method="post" action="/oauth/authorize" style="display:inline">
            {{ csrf_field() }}
            <input type="hidden" name="state" value="{{ $request->state }}">
            <input type="hidden" name="client_id" value="{{ $client->id }}">
            <button type="submit" class="btn btn-success btn-approve">确认登录</button>
        </form>
        <!-- Cancel Button -->
        <form method="post" action="/oauth/authorize" style="display:inline">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
            <input type="hidden" name="state" value="{{ $request->state }}">
            <input type="hidden" name="client_id" value="{{ $client->id }}">
            <button class="btn btn-secondary">取消</button>
        </form>
    </div>
@endsection