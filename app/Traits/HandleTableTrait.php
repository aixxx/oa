<?php
/**
 * Created by yyp.
 * User: yyp
 * Date: 2019/6/18
 * Time: 13:59
 */

namespace App\Http\Traits;


trait HandleTableTrait
{
    /*批量更新*/
    function updateBatch($tableName = "", $multipleData = array()){
        if( $tableName && !empty($multipleData) ) {
            $updateColumn = array_keys($multipleData[0]);
            $referenceColumn = $updateColumn[0]; //e.g id
            unset($updateColumn[0]);
            $whereIn = "";

            $q = "UPDATE ".$tableName." SET ";
            foreach ( $updateColumn as $uColumn ) {
                $q .=  $uColumn." = CASE ";

                foreach( $multipleData as $data ) {
                    $q .= "WHEN ".$referenceColumn." = ".$data[$referenceColumn]." THEN '".$data[$uColumn]."' ";
                }
                $q .= "ELSE ".$uColumn." END, ";
            }
            foreach( $multipleData as $data ) {
                $whereIn .= "'".$data[$referenceColumn]."', ";
            }
            $q = rtrim($q, ", ")." WHERE ".$referenceColumn." IN (".  rtrim($whereIn, ', ').")";

            return DB::update(DB::raw($q));
        } else {
            return false;
        }
    }
}