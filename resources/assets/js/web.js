
var $=require('jquery');

function getTree() {
    // Some logic to retrieve, or generate tree structure
        $.get('/departments/all',function(data,status){
            treeData = formatTree(data);
            $('#tree').treeview({
                //color: "#967ADC",
                expandIcon: 'glyphicon glyphicon-triangle-right',
                collapseIcon: 'glyphicon glyphicon-triangle-bottom',
                nodeIcon: 'glyphicon glyphicon-folder-close',
                data: treeData,
                onNodeSelected: function(event, node) {
                    location.href = window.location.host+"/user?departid=" + node.departId;
                },
                onNodeHover: function(event) {
                    //console.log(event);
                    let eventType = event.type;
                    let node = $(event.target);

                        if(eventType=="mouseenter"){
                                action = node.find(".item-action")
                                if(action.length<1){
                                    template = $('<div class="tree-node-action text-right item-action dropdown" style="float:right;">' +
                                        '<a href="javascript:void(0)" data-toggle="dropdown" class="tree-node-action icon"><i class="tree-node-action fe fe-more-vertical"></i></a>' +
                                        '<div class="tree-node-action dropdown-menu dropdown-menu-right">' +
                                        '<a href="javascript:void(0)" class="tree-node-action dropdown-item"><i class="tree-node-action dropdown-icon fe fe-tag"></i> Action </a>' +
                                        '<a href="javascript:void(0)" class="tree-node-action dropdown-item"><i class="tree-node-action dropdown-icon fe fe-edit-2"></i> Another action </a> ' +
                                        '<a href="javascript:void(0)" class="tree-node-action dropdown-item"><i class="tree-node-action dropdown-icon fe fe-message-square"></i> Something else here</a> ' +
                                        '<div class="dropdown-divider"></div><a href="javascript:void(0)" class="tree-node-action dropdown-item">' +
                                        '<i class="tree-node-action dropdown-icon fe fe-link"></i> Separated link</a></div>' +
                                        '</div>');
                                    node.append(template);
                                }
                            action.show();
                        }
                        if(eventType=="mouseleave"){
                                action = node.find(".item-action")
                                //console.log(node.find(".item-action"));
                                action.hide();
                        }
                        //location.href = "/user?departid=" + node.id;
                    //console.log(event);
                },
            });

        });
}

function formatTree(data){
    let result = [];
    $(data).each(function(){
        if(this.childList){
            childNodes = formatTree(this.childList);
        }
        node = {text:this.name,departId:this.id,nodes:childNodes};
        result.push(node);
    });

    return result;

}

var treeview = require("./bootstrap-treeview.js");
document.addEventListener('DOMContentLoaded',function(){
    if($("#tree").length > 0) {
        //元素存在时执行的代码
        getTree();
    }


});
