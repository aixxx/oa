@extends('layouts.main')

@section('head')
    <style>
        p.form-info-p {
            border-bottom: 1px solid #eee;
        }
        .card-body label {
            font-weight: bold;
        }
    </style>
@endsection

@section('content')

    <section class="page-content container-fluid">
        <div class="row">
            <div class="col-xl-12 col-xxl-12">
                <div class="card">
                    <div class="form-group card-header">
                        <h3 class="pull-left" style="margin:7px 0 0 0;">
                            {{$entry->flow->flow_name}} : {{$entry->title}}
                        </h3>
                        <button class="print btn btn-success pull-right btn-sm">打印</button>
                    </div>
                    <div class="card-body">
                        {!! $form_html !!}
                    </div>
                </div>
            </div>
            <div class="col-xl-12 col-xxl-12">
                {!! $processes_html !!}
            </div>
        </div>
    </section>
@endsection
@section('javascript')
    <script>
        $("label:contains('付款金额')").width('11.5%');
    </script>
@endsection