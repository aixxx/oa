@extends('layouts.main')

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1>添加模板</h1>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div class="card-body">
            <form action="{{route('workflow.template.store')}}" method="POST">
                {{csrf_field()}}
                <div class="form-group">
                    <label>模板名称</label>
                    <input type="text" class="form-control"  name="template_name" placeholder="模板名称">
                </div>
                <button type="submit" class="btn btn-primary">确定</button>
            </form>
        </div>
    </div>
@endsection
