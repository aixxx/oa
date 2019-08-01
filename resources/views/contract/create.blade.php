@extends('layouts.main')

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1>创建合同</h1>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div class="card-body">
                <form action="{{route('contract.store')}}" method="post" class="form-horizontal">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="title" class="col-sm-2 control-label">标题</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="title" id="title" placeholder="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tags" class="col-sm-2 control-label">标签</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="tags" id="tags" placeholder="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="content" class="col-sm-2 control-label">内容</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="content" id="content" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-default">提交</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection