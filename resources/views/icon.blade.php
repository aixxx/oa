@extends('layouts.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-2">
                <div class="card card-profile" style="padding: 10px;">
                    @foreach($class as $c)
                        <div>
                            <i class="icon {{$c}}" style="display: inline-block;margin: 4px;font-size: 30px;"></i>{{$c}}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection