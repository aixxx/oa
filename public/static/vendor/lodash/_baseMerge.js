var Stack=require("./_Stack"),assignMergeValue=require("./_assignMergeValue"),baseFor=require("./_baseFor"),baseMergeDeep=require("./_baseMergeDeep"),isObject=require("./isObject"),keysIn=require("./keysIn"),safeGet=require("./_safeGet");function baseMerge(a,i,u,b,g){a!==i&&baseFor(i,function(e,r){if(isObject(e))g||(g=new Stack),baseMergeDeep(a,i,r,u,baseMerge,b,g);else{var s=b?b(safeGet(a,r),e,r+"",a,i,g):void 0;void 0===s&&(s=e),assignMergeValue(a,r,s)}},keysIn)}module.exports=baseMerge;