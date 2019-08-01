@extends('layouts.main')

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1>流程申请</h1>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <?php foreach($types as $type){ ?>
        <div class="card">
            <h5 class="card-header">{{ $type->type_name }}</h5>
            <div class="card-body demo-buttons--preview">
                <?php foreach($type->valid_flow as $flow){
                if (in_array($flow->id, $canSeeFlowIds)){ ?>
                <a class="btn btn-primary"
                   href="{{route('workflow.entry.create', ['flow_id'=>$flow->id])}}">{{$flow->flow_name}}</a>
                <?php } }?>
            </div>
        </div>
        <?php }?>
    </section>
@endsection
