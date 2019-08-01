@extends('layouts.web')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8 offset-2">
            <div class="card card-profile">
                <div class="card-header" style="background-image: url({{ Auth::user()->avatar }});"></div>
                <div class="card-body text-center">
                    <img class="card-profile-img" src="{{ Auth::user()->avatar }}">
                    <h3 class="mb-3">{{ Auth::user()->chinese_name }}</h3>
                    <h5>{{ Auth::user()->english_name }}</h5>
                    <p class="mb-4">
                        {{ Auth::user()->position }}
                    </p>
                </div>
                <div class="card-footer">
                    <button onclick="window.open('/certificate/download')" class="btn btn-outline-success btn-block"><i class="fe fe-download"></i> 下载证书</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
