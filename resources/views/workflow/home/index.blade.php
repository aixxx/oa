@extends('layouts.main',['title' => '审批'])

@section('content')

    <div class="card card-tabs">
        <div class="card-header p-0 no-border">
            <ul class="nav nav-tabs primary-tabs p-l-30 m-0">
                <li class="nav-item" role="presentation"><a href="#profile-about" class="nav-link active show" data-toggle="tab" aria-expanded="true">待处理</a></li>
                <li class="nav-item" role="presentation"><a href="#profile-photos" class="nav-link" data-toggle="tab" aria-expanded="true">已处理</a></li>
                <li class="nav-item" role="presentation"><a href="#profile-contacts" class="nav-link" data-toggle="tab" aria-expanded="true">抄送我</a></li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane fadeIn active" id="profile-about">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>类型</th>
                                <th>发起人</th>
                                <th>当前位置</th>
                                <th>当前状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><span class="badge badge-accent">休假</span></td>
                                <td>1,415,045,928</td>
                                <td>
                                    <div class="progress m-t-5">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 44%;" aria-valuenow="44" aria-valuemin="0" aria-valuemax="100">44%</div>
                                    </div>
                                </td>
                                <td><span class="badge badge-success">通过</span></td>
                            </tr>
                            <tr>
                                <td>India</td>
                                <td>1,354,051,854</td>
                                <td>1.11 %</td>
                                <td>14,871,727</td>
                            </tr>
                            <tr>
                                <td>U.S.</td>
                                <td>326,766,748</td>
                                <td>0.71 %</td>
                                <td>2,307,285</td>
                            </tr>
                            <tr>
                                <td>Indonesia</td>
                                <td>266,794,980</td>
                                <td>1.06 %</td>
                                <td>2,803,601</td>
                            </tr>
                            <tr>
                                <td>Brazil</td>
                                <td>210,867,954</td>
                                <td>0.75 %</td>
                                <td>1,579,676</td>
                            </tr>
                            <tr>
                                <td>Pakistan</td>
                                <td>200,813,818</td>
                                <td>1.93 %</td>
                                <td>3,797,863</td>
                            </tr>
                            <tr>
                                <td>Nigeria</td>
                                <td>195,875,237</td>
                                <td>2.61 %</td>
                                <td>4,988,926</td>
                            </tr>
                            <tr>
                                <td>Bangladesh</td>
                                <td>166,368,149</td>
                                <td>1.03 %</td>
                                <td>1,698,398</td>
                            </tr>
                            <tr>
                                <td>Russia</td>
                                <td>143,964,709</td>
                                <td>-0.02 %</td>
                                <td>-25,045</td>
                            </tr>
                            <tr>
                                <td>Mexico</td>
                                <td>130,759,074</td>
                                <td>1.24 %</td>
                                <td>1,595,798</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fadeIn" id="profile-photos">
                    <div class="card-columns">
                        <div class="card">
                            <img class="card-img" src="http://via.placeholder.com/300x200" alt="Card image">
                        </div>
                        <div class="card">
                            <img class="card-img" src="http://via.placeholder.com/300x200" alt="Card image">
                        </div>
                        <div class="card">
                            <img class="card-img" src="http://via.placeholder.com/300x200" alt="Card image">
                        </div>
                        <div class="card">
                            <img class="card-img" src="http://via.placeholder.com/300x200" alt="Card image">
                        </div>
                        <div class="card">
                            <img class="card-img" src="http://via.placeholder.com/300x200" alt="Card image">
                        </div>
                        <div class="card">
                            <img class="card-img" src="http://via.placeholder.com/300x200" alt="Card image">
                        </div>
                        <div class="card">
                            <img class="card-img" src="http://via.placeholder.com/300x200" alt="Card image">
                        </div>
                        <div class="card">
                            <img class="card-img" src="http://via.placeholder.com/300x200" alt="Card image">
                        </div>
                        <div class="card">
                            <img class="card-img" src="http://via.placeholder.com/300x200" alt="Card image">
                        </div>
                        <div class="card">
                            <img class="card-img" src="http://via.placeholder.com/300x200" alt="Card image">
                        </div>
                        <div class="card">
                            <img class="card-img" src="http://via.placeholder.com/300x200" alt="Card image">
                        </div>
                        <div class="card">
                            <img class="card-img" src="http://via.placeholder.com/300x200" alt="Card image">
                        </div>
                        <div class="card">
                            <img class="card-img" src="http://via.placeholder.com/300x200" alt="Card image">
                        </div>
                        <div class="card">
                            <img class="card-img" src="http://via.placeholder.com/300x200" alt="Card image">
                        </div>
                    </div>
                </div>
                <div class="tab-pane fadeIn contact-list" id="profile-contacts">
                    <div class="row">
                        <!-- .col -->
                        <div class="col-md-6 col-lg-4 col-xxl-3">
                            <div class="card contact-item border shadow-on-hover">
                                <div class="card-header border-none">
                                    <ul class="actions top-right">
                                        <li class="dropdown">
                                            <a href="javascript:void(0)" class="btn btn-fab" data-toggle="dropdown" aria-expanded="false">
                                                <i class="icon dripicons-dots-3 rotate-90 font-size-24"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <div class="dropdown-header">
                                                    Manage Contact
                                                </div>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-view-list"></i> View
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-pencil"></i> Edit
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-cloud-download"></i> Export
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-trash"></i> Remove
                                                </a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <img src="http://via.placeholder.com/128x128" alt="user" class="rounded-circle max-w-100 m-t-20">
                                        </div>
                                        <div class="col-md-12 text-center">
                                            <h5 class="card-title">Vanessa	Norton	</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-center p-0">
                                    <div class="row m-0">
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.mail.html" class="d-block p-20">
                                                    <i class="icon dripicons-mail"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.messages.html" class="d-block p-20">
                                                    <i class="icon dripicons-message"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="pages.profile.html" class="d-block p-20">
                                                    <i class="icon dripicons-user-id"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.col -->
                        <!-- .col -->
                        <div class="col-md-6 col-lg-4 col-xxl-3">
                            <div class="card contact-item border shadow-on-hover">
                                <div class="card-header border-none">
                                    <ul class="actions top-right">
                                        <li class="dropdown">
                                            <a href="javascript:void(0)" class="btn btn-fab" data-toggle="dropdown" aria-expanded="false">
                                                <i class="icon dripicons-dots-3 rotate-90 font-size-24"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <div class="dropdown-header">
                                                    Manage Contact
                                                </div>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-view-list"></i> View
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-pencil"></i> Edit
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-cloud-download"></i> Export
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-trash"></i> Remove
                                                </a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <img src="http://via.placeholder.com/128x128" alt="user" class="rounded-circle max-w-100 m-t-20">
                                        </div>
                                        <div class="col-md-12 text-center">
                                            <h5 class="card-title">Ramona Page</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-center p-0">
                                    <div class="row m-0">
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.mail.html" class="d-block p-20">
                                                    <i class="icon dripicons-mail"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.messages.html" class="d-block p-20">
                                                    <i class="icon dripicons-message"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="pages.profile.html" class="d-block p-20">
                                                    <i class="icon dripicons-user-id"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.col -->
                        <!-- .col -->
                        <div class="col-md-6 col-lg-4 col-xxl-3">
                            <div class="card contact-item border shadow-on-hover">
                                <div class="card-header border-none">
                                    <ul class="actions top-right">
                                        <li class="dropdown">
                                            <a href="javascript:void(0)" class="btn btn-fab" data-toggle="dropdown" aria-expanded="false">
                                                <i class="icon dripicons-dots-3 rotate-90 font-size-24"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <div class="dropdown-header">
                                                    Manage Contact
                                                </div>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-view-list"></i> View
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-pencil"></i> Edit
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-cloud-download"></i> Export
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-trash"></i> Remove
                                                </a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <img src="http://via.placeholder.com/128x128" alt="user" class="rounded-circle max-w-100 m-t-20">
                                        </div>
                                        <div class="col-md-12 text-center">
                                            <h5 class="card-title">Jacob	Ross</h5>

                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-center p-0">
                                    <div class="row m-0">
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.mail.html" class="d-block p-20">
                                                    <i class="icon dripicons-mail"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.messages.html" class="d-block p-20">
                                                    <i class="icon dripicons-message"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="pages.profile.html" class="d-block p-20">
                                                    <i class="icon dripicons-user-id"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.col -->
                        <!-- .col -->
                        <div class="col-md-6 col-lg-4 col-xxl-3">
                            <div class="card contact-item border shadow-on-hover">
                                <div class="card-header border-none">
                                    <ul class="actions top-right">
                                        <li class="dropdown">
                                            <a href="javascript:void(0)" class="btn btn-fab" data-toggle="dropdown" aria-expanded="false">
                                                <i class="icon dripicons-dots-3 rotate-90 font-size-24"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <div class="dropdown-header">
                                                    Manage Contact
                                                </div>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-view-list"></i> View
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-pencil"></i> Edit
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-cloud-download"></i> Export
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-trash"></i> Remove
                                                </a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <img src="http://via.placeholder.com/128x128" alt="user" class="rounded-circle max-w-100 m-t-20">
                                        </div>
                                        <div class="col-md-12 text-center">
                                            <h5 class="card-title">Rochelle Barton</h5>

                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-center p-0">
                                    <div class="row m-0">
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.mail.html" class="d-block p-20">
                                                    <i class="icon dripicons-mail"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.messages.html" class="d-block p-20">
                                                    <i class="icon dripicons-message"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="pages.profile.html" class="d-block p-20">
                                                    <i class="icon dripicons-user-id"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.col -->
                        <!-- .col -->
                        <div class="col-md-6 col-lg-4 col-xxl-3">
                            <div class="card contact-item border shadow-on-hover">
                                <div class="card-header border-none">
                                    <ul class="actions top-right">
                                        <li class="dropdown">
                                            <a href="javascript:void(0)" class="btn btn-fab" data-toggle="dropdown" aria-expanded="false">
                                                <i class="icon dripicons-dots-3 rotate-90 font-size-24"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <div class="dropdown-header">
                                                    Manage Contact
                                                </div>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-view-list"></i> View
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-pencil"></i> Edit
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-cloud-download"></i> Export
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-trash"></i> Remove
                                                </a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <img src="http://via.placeholder.com/128x128" alt="user" class="rounded-circle max-w-100 m-t-20">
                                        </div>
                                        <div class="col-md-12 text-center">
                                            <h5 class="card-title">Sophia Robinson</h5>

                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-center p-0">
                                    <div class="row m-0">
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.mail.html" class="d-block p-20">
                                                    <i class="icon dripicons-mail"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.messages.html" class="d-block p-20">
                                                    <i class="icon dripicons-message"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="pages.profile.html" class="d-block p-20">
                                                    <i class="icon dripicons-user-id"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.col -->
                        <!-- .col -->
                        <div class="col-md-6 col-lg-4 col-xxl-3">
                            <div class="card contact-item border shadow-on-hover">
                                <div class="card-header border-none">
                                    <ul class="actions top-right">
                                        <li class="dropdown">
                                            <a href="javascript:void(0)" class="btn btn-fab" data-toggle="dropdown" aria-expanded="false">
                                                <i class="icon dripicons-dots-3 rotate-90 font-size-24"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <div class="dropdown-header">
                                                    Manage Contact
                                                </div>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-view-list"></i> View
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-pencil"></i> Edit
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-cloud-download"></i> Export
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-trash"></i> Remove
                                                </a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <img src="http://via.placeholder.com/128x128" alt="user" class="rounded-circle max-w-100 m-t-20">
                                        </div>
                                        <div class="col-md-12 text-center">
                                            <h5 class="card-title">Claire Peters</h5>

                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-center p-0">
                                    <div class="row m-0">
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.mail.html" class="d-block p-20">
                                                    <i class="icon dripicons-mail"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.messages.html" class="d-block p-20">
                                                    <i class="icon dripicons-message"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="pages.profile.html" class="d-block p-20">
                                                    <i class="icon dripicons-user-id"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.col -->
                        <!-- .col -->
                        <div class="col-md-6 col-lg-4 col-xxl-3">
                            <div class="card contact-item border shadow-on-hover">
                                <div class="card-header border-none">
                                    <ul class="actions top-right">
                                        <li class="dropdown">
                                            <a href="javascript:void(0)" class="btn btn-fab" data-toggle="dropdown" aria-expanded="false">
                                                <i class="icon dripicons-dots-3 rotate-90 font-size-24"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <div class="dropdown-header">
                                                    Manage Contact
                                                </div>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-view-list"></i> View
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-pencil"></i> Edit
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-cloud-download"></i> Export
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-trash"></i> Remove
                                                </a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <img src="http://via.placeholder.com/128x128" alt="user" class="rounded-circle max-w-100 m-t-20">
                                        </div>
                                        <div class="col-md-12 text-center">
                                            <h5 class="card-title">Noah Harper</h5>

                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-center p-0">
                                    <div class="row m-0">
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.mail.html" class="d-block p-20">
                                                    <i class="icon dripicons-mail"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.messages.html" class="d-block p-20">
                                                    <i class="icon dripicons-message"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="pages.profile.html" class="d-block p-20">
                                                    <i class="icon dripicons-user-id"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.col -->
                        <!-- .col -->
                        <div class="col-md-6 col-lg-4 col-xxl-3">
                            <div class="card contact-item border shadow-on-hover">
                                <div class="card-header border-none">
                                    <ul class="actions top-right">
                                        <li class="dropdown">
                                            <a href="javascript:void(0)" class="btn btn-fab" data-toggle="dropdown" aria-expanded="false">
                                                <i class="icon dripicons-dots-3 rotate-90 font-size-24"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <div class="dropdown-header">
                                                    Manage Contact
                                                </div>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-view-list"></i> View
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-pencil"></i> Edit
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-cloud-download"></i> Export
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-trash"></i> Remove
                                                </a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <img src="http://via.placeholder.com/128x128" alt="user" class="rounded-circle max-w-100 m-t-20">
                                        </div>
                                        <div class="col-md-12 text-center">
                                            <h5 class="card-title">Colin Jones</h5>

                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-center p-0">
                                    <div class="row m-0">
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.mail.html" class="d-block p-20">
                                                    <i class="icon dripicons-mail"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.messages.html" class="d-block p-20">
                                                    <i class="icon dripicons-message"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="pages.profile.html" class="d-block p-20">
                                                    <i class="icon dripicons-user-id"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.col -->
                        <!-- .col -->
                        <div class="col-md-6 col-lg-4 col-xxl-3">
                            <div class="card contact-item border shadow-on-hover">
                                <div class="card-header border-none">
                                    <ul class="actions top-right">
                                        <li class="dropdown">
                                            <a href="javascript:void(0)" class="btn btn-fab" data-toggle="dropdown" aria-expanded="false">
                                                <i class="icon dripicons-dots-3 rotate-90 font-size-24"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <div class="dropdown-header">
                                                    Manage Contact
                                                </div>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-view-list"></i> View
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-pencil"></i> Edit
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-cloud-download"></i> Export
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-trash"></i> Remove
                                                </a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <img src="http://via.placeholder.com/128x128" alt="user" class="rounded-circle max-w-100 m-t-20">
                                        </div>
                                        <div class="col-md-12 text-center">
                                            <h5 class="card-title">Wendy Abbott</h5>

                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-center p-0">
                                    <div class="row m-0">
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.mail.html" class="d-block p-20">
                                                    <i class="icon dripicons-mail"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.messages.html" class="d-block p-20">
                                                    <i class="icon dripicons-message"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="pages.profile.html" class="d-block p-20">
                                                    <i class="icon dripicons-user-id"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.col -->
                        <!-- .col -->
                        <div class="col-md-6 col-lg-4 col-xxl-3">
                            <div class="card contact-item border shadow-on-hover">
                                <div class="card-header border-none">
                                    <ul class="actions top-right">
                                        <li class="dropdown">
                                            <a href="javascript:void(0)" class="btn btn-fab" data-toggle="dropdown" aria-expanded="false">
                                                <i class="icon dripicons-dots-3 rotate-90 font-size-24"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <div class="dropdown-header">
                                                    Manage Contact
                                                </div>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-view-list"></i> View
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-pencil"></i> Edit
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-cloud-download"></i> Export
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-trash"></i> Remove
                                                </a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <img src="http://via.placeholder.com/128x128" alt="user" class="rounded-circle max-w-100 m-t-20">
                                        </div>
                                        <div class="col-md-12 text-center">
                                            <h5 class="card-title">Cory Carter</h5>

                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-center p-0">
                                    <div class="row m-0">
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.mail.html" class="d-block p-20">
                                                    <i class="icon dripicons-mail"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.messages.html" class="d-block p-20">
                                                    <i class="icon dripicons-message"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="pages.profile.html" class="d-block p-20">
                                                    <i class="icon dripicons-user-id"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.col -->
                        <!-- .col -->
                        <div class="col-md-6 col-lg-4 col-xxl-3">
                            <div class="card contact-item border shadow-on-hover">
                                <div class="card-header border-none">
                                    <ul class="actions top-right">
                                        <li class="dropdown">
                                            <a href="javascript:void(0)" class="btn btn-fab" data-toggle="dropdown" aria-expanded="false">
                                                <i class="icon dripicons-dots-3 rotate-90 font-size-24"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <div class="dropdown-header">
                                                    Manage Contact
                                                </div>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-view-list"></i> View
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-pencil"></i> Edit
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-cloud-download"></i> Export
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-trash"></i> Remove
                                                </a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <img src="http://via.placeholder.com/128x128" alt="user" class="rounded-circle max-w-100 m-t-20">
                                        </div>
                                        <div class="col-md-12 text-center">
                                            <h5 class="card-title">Ken Patrick</h5>

                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-center p-0">
                                    <div class="row m-0">
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.mail.html" class="d-block p-20">
                                                    <i class="icon dripicons-mail"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.messages.html" class="d-block p-20">
                                                    <i class="icon dripicons-message"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="pages.profile.html" class="d-block p-20">
                                                    <i class="icon dripicons-user-id"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.col -->
                        <!-- .col -->
                        <div class="col-md-6 col-lg-4 col-xxl-3">
                            <div class="card contact-item border shadow-on-hover">
                                <div class="card-header border-none">
                                    <ul class="actions top-right">
                                        <li class="dropdown">
                                            <a href="javascript:void(0)" class="btn btn-fab" data-toggle="dropdown" aria-expanded="false">
                                                <i class="icon dripicons-dots-3 rotate-90 font-size-24"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <div class="dropdown-header">
                                                    Manage Contact
                                                </div>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-view-list"></i> View
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-pencil"></i> Edit
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-cloud-download"></i> Export
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="icon dripicons-trash"></i> Remove
                                                </a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <img src="http://via.placeholder.com/128x128" alt="user" class="rounded-circle max-w-100 m-t-20">
                                        </div>
                                        <div class="col-md-12 text-center">
                                            <h5 class="card-title">Cindy Tate</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-center p-0">
                                    <div class="row m-0">
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.mail.html" class="d-block p-20">
                                                    <i class="icon dripicons-mail"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="apps.messages.html" class="d-block p-20">
                                                    <i class="icon dripicons-message"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 p-0">
                                            <div class="contact">
                                                <a href="pages.profile.html" class="d-block p-20">
                                                    <i class="icon dripicons-user-id"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
