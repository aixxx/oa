<div class="card">
    <div class="card-header">审批记录</div>
    <div class="card-body">
        <div class="timeline timeline-border">
            @foreach($processes as $process)
                @if(!isset($companyType) || (isset($companyType) && $process->map_names))  {{--映射逻辑判断--}}
                    <div class="timeline-list timeline-border timeline-primary">
                        <div class="timeline-info">
                            <div class="row">
                                <div class="col-sm-3 col-xs-6">
                                    <div class="proc-name ">{{$process->process_name}}</div>
                                    <div class="proc-status">
                                        @if($process->proc && $process->proc->status== \App\Models\Workflow\Proc::STATUS_REJECTED )
                                            <span class="text-accent">[驳回]</span>
                                        @elseif($process->proc && $process->proc->status== \App\Models\Workflow\Proc::STATUS_PASSED)
                                            <span class="text-success">[完成]</span>
                                        @else
                                            <span class="text-info">[待处理]</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-3 col-xs-6">
                                    <div class="proc-auditor">
                                        @if($process->map_names)
                                            {{$process->map_names}}
                                        @elseif($process->proc && $process->proc->auditor_name)
                                            {{$process->proc->auditor_name}}
                                        @elseif($process->proc && $process->proc->user_name)
                                            {{$process->proc->user_name}}
                                        @else
                                            {{$process->auditors}}
                                        @endif
                                    </div>
                                    <div class="proc-time">
                                        <small class="text-muted">{{$process->proc && $process->proc->auditor_name?$process->proc->updated_at:''}}</small>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                    <div class="proc-auditor">
                                        {{isset($process->proc->content)&&$process->proc->content?('批复：'.$process->proc->content):''}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>