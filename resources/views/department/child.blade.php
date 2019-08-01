<ul class="menu">

    @foreach($children as $child)
        @if(in_array($child->auto_id,$allDepartAutoIds))
            <li style="margin-top: 1%">
                {{ $child->name }}
                @if(in_array($child->id,$childrenDeparts))
                    @foreach(\App\Models\User::getByDepartIdAndDatetime($child->id, $chooseTime) as $user)
                        @if($isLeader[$user->id.'_'.$child->id])
                            @if($isPrimary[$user->id.'_'.$child->id] && $isCanLookUserInfo)
                                {{ $user->chinese_name }}
                            @endif
                            @if($isPrimary[$user->id.'_'.$child->id] && !$isCanLookUserInfo)
                                {{ $user->chinese_name }}
                            @endif
                            @if(!$isPrimary[$user->id.'_'.$child->id] && $isCanLookUserInfo)
                                {{ $user->chinese_name }}
                            @endif
                            @if(!$isPrimary[$user->id.'_'.$child->id] && !$isCanLookUserInfo)
                                {{ $user->chinese_name }}
                            @endif
                        @endif
                    @endforeach
                @endif
                <div class="tags" style="margin-top: 1%">
                    @if(in_array($child->id,$childrenDeparts))
                        @php
                            $report['cost']=$_SESSION['cost']??[];
                            $report['subcost']=$_SESSION['subcost']??[];
                            $report['subtotal']=$_SESSION['subtotal']??[];
                        @endphp
                        @foreach(\App\Models\User::getByDepartIdAndDatetime($child->id, $chooseTime) as $user)
                            @php
                                if($isPrimary[$user->id.'_'.$child->id]){
                                        $report['cost'][$user->id]=1;
                                        $report['subcost'][$user->id]=1;
                                }
                                $report['subtotal'][$user->id]=1;
                            @endphp
                            @if($isPrimary[$user->id.'_'.$child->id] && $isCanLookUserInfo)
                                <a href="/users/{{ $user->id }}"
                                   class="btn badge badge-pill badge-secondary btn-sm line-height-fix">{{ $user->chinese_name }}</a>
                            @endif
                            @if($isPrimary[$user->id.'_'.$child->id] && !$isCanLookUserInfo)
                                <a href="javascript:void(0)"
                                   class="btn badge badge-pill badge-secondary btn-sm line-height-fix">{{ $user->chinese_name }}</a>
                            @endif
                            @if(!$isPrimary[$user->id.'_'.$child->id] && $isCanLookUserInfo)
                                <a href="/users/{{ $user->id }}"
                                   class="btn btn-secondary btn-sm line-height-fix">{{ $user->chinese_name }}</a>
                            @endif
                            @if(!$isPrimary[$user->id.'_'.$child->id] && !$isCanLookUserInfo)
                                <a href="javascript:void(0)"
                                   class="btn btn-secondary btn-sm line-height-fix">{{ $user->chinese_name }}</a>
                            @endif
                            {{--  @if($user->pivot->is_primary)
                                  <a href="/users/{{ $user->id }}"
                                     class="btn badge badge-pill badge-secondary btn-sm line-height-fix">{{ $user->chinese_name }}</a>
                              @else
                                  <a href="/users/{{ $user->id }}"
                                     class="btn btn-secondary btn-sm line-height-fix">{{ $user->chinese_name }}</a>
                              @endif--}}
                        @endforeach
                        @php
                            $_SESSION['cost']=$report['cost'];
                            $_SESSION['subcost']=$report['subcost'];
                            $_SESSION['subtotal']=$report['subtotal'];
                        @endphp
                    @endif
                </div>
                @if(count($isHaveChildren[$child->id]))
                    @include('department.child', ['children' =>$isHaveChildren[$child->id]  ])
                @endif
            </li>
        @endif
    @endforeach

</ul>
