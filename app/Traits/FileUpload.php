<?php
namespace App\Traits;
use App\Constant\ConstFile;
use Request;
use Exception;

trait FileUpload{
    /**
     * 文件上传
     * @param Request $request
     */
    public function upload($request){
        if ($request->hasFile('inputImport') && $request->file('inputImport')->isValid()) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $request->file('inputImport') );

                // $cells 是包含 Excel 表格数据的数组
                foreach ( $spreadsheet->getWorksheetIterator() as $cell ) {
                    $cells = $cell->toArray();
                    // 去掉表头
                    unset( $cells[ 0 ] );
                    return $cells;
                }
            } catch ( Exception $e ) {
                return returnJson($e->getMessage(), $e->getCode());
            }
        }else{
            return returnJson('文件上传失败',ConstFile::API_RESPONSE_FAIL);
        }
    }
}