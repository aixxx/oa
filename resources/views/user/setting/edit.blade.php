@extends("layouts.main",['title' => '设置'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">设置</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">设置</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-lg-3">
                                <div class="nav flex-column nav-pills" id="my-account-tabs" role="tablist" aria-orientation="vertical">
                                    <a class="nav-link active" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="true">密码修改</a>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-9">
                                <div class="tab-content" id="my-account-tabsContent">
                                    <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                        <h4 class="card-heading p-b-20">密码修改</h4>
                                        <form action="{{  route('users.setting.update',['id' => auth()->id()])  }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="_method" value="PUT">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">原密码</label>
                                                @if($errors->get('originalpassword'))
                                                    <div class="alert alert-danger">
                                                        <ul>
                                                            @foreach($errors->get('originalpassword') as $error)
                                                                <li>{{  $error  }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                                @if(session('failed'))
                                                    <div class="alert alert-danger">
                                                        <ul>
                                                            <li>{{  session('failed')  }}</li>
                                                        </ul>
                                                    </div>
                                                @endif
                                                <input type="password" class="form-control" id="exampleInputPassword1"  aria-describedby="passwordHelp" placeholder="Original Password" name="originalpassword" value="{{  old('originalpassword')  }}">
                                                {{--<small id="passwordHelp" class="form-text text-muted">We recommend at least 8 characters long, avoiding patterns and common words/phrases.</small>--}}
                                            </div>
                                            <div class="form-group">
                                                <label for="inputLocation">新密码</label>
                                                @if($errors->get('newpassword'))
                                                    <div class="alert alert-danger">
                                                        <ul>
                                                            @foreach($errors->get('newpassword') as $error)
                                                                <li>{{  $error  }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                                <input type="password" class="form-control" id="inputLocation" placeholder="New Password" name="newpassword" value="{{  old('newpassword')  }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="inputLocation">确认密码</label>
                                                @if($errors->get('newpassword_confirmation'))
                                                    <div class="alert alert-danger">
                                                        <ul>
                                                            @foreach($errors->get('newpassword_confirmation') as $error)
                                                                <li>{{  $error  }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                                <input type="password" class="form-control" placeholder="New Password Cofirmation" name="newpassword_confirmation" value="{{  old('newpassword_confirmation')  }}">
                                            </div>
                                            <button type="submit" class="btn btn-primary">保存</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection