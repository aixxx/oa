
<!DOCTYPE HTML>
<html>
 <head>
    
    <title> Flowdesign.leipi.org</title>
    <meta name="keywords" content="流程设计器,Web Flowdesign,Flowdesigner,专业流程设计器,WEB流程设计器">
    <meta name="description" content="">
    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    
    <link href="/vendor/flowdesign/Public/css/bootstrap/css/bootstrap.css?2025" rel="stylesheet" type="text/css" />
    <!--[if lte IE 6]>
    <link rel="stylesheet" type="text/css" href="/vendor/flowdesign/Public/css/bootstrap/css/bootstrap-ie6.css?2025">
    <![endif]-->
    <!--[if lte IE 7]>
    <link rel="stylesheet" type="text/css" href="/vendor/flowdesign/Public/css/bootstrap/css/ie.css?2025">
    <![endif]-->
    <!--select 2-->
    <link rel="stylesheet" type="text/css" href="/vendor/flowdesign/Public/js/jquery.multiselect2side/css/jquery.multiselect2side.css" media="screen" />
    <link href="/vendor/flowdesign/Public/css/site.css?2025" rel="stylesheet" type="text/css" />

    <script type="text/javascript" src="/vendor/flowdesign/Public/js/jquery-1.7.2.min.js?2025"></script>
    <script type="text/javascript" src="/vendor/flowdesign/Public/css/bootstrap/js/bootstrap.min.js?2025"></script>
    <!--select 2-->
    <script type="text/javascript" src="/vendor/flowdesign/Public/js/jquery.multiselect2side/js/jquery.multiselect2side.js?2025" ></script>
<style>
    input[type="checkbox"] {
        margin: 0 5px;
    }
    .role-select {
        float: left;
        margin-right: 10px;
    }
    .dialog_main{margin:5px 0 0 5px;}
</style>

 </head>
<body>

<div class="container dialog_main">
    <h3>选择角色</h3>
    <div class="row span7">
        @foreach($roleList as $role)
            <label class="role-select">
                <input type="checkbox" name="role_id" value="{{$role->id}}" value-text="{{$role->role_name}}" @if(in_array($role->id, $auditor)) checked @endif >{{$role->role_name}}
            </label>
        @endforeach
    </div>
    <div class="row span7">
        <div class="pull-right">
            <button class="btn btn-info" type="button" id="dialog_confirm">确定</button>
            <button class="btn" type="button" id="dialog_close">取消</button>
        </div>
    </div>
</div><!--end container-->
    

<script type="text/javascript">
    $(function(){
        $("#dialog_confirm").on("click",function(){
            var nameText = [];
            var idText = [];
            var globalValue = '@leipi@';

            $("input[name='role_id']:checked").each(function() {
                nameText.push($(this).attr('value-text'));
                idText.push($(this).val());
            });
            globalValue = nameText.join(',') + '@leipi@' + idText.join(',');

            if(window.ActiveXObject){ //IE  
                window.returnValue = globalValue
            }else{ //非IE  
                if(window.opener) {  
                    window.opener.callbackSuperDialog(globalValue) ;  
                }
            }  
            window.close();
            
            
        });
        $("#dialog_close").on("click",function(){
            window.close();
        });
    });
</script>



    

</body>
</html>