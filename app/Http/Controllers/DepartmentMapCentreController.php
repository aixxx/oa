<?php

namespace App\Http\Controllers;

use App\Models\DepartmentMapCentre;
use Illuminate\Http\Request;
use Auth;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use UserFixException;
class DepartmentMapCentreController extends Controller
{
    public function __construct()
    {
        $this->middleware('afterlog')->only('import');
    }

    public function index()
    {
        $times = DepartmentMapCentre::max('times');

        $list = DepartmentMapCentre::with('user')->where('times', $times)->get();
        $cnt  = $list->count();

        return view('department-map-centre.index', compact('list', 'cnt'));
    }

    public function import(Request $request)
    {
        $status = 'success';
        $mes    = '文件导入成功';
        try {
            $file        = $request->file('import-excel')->path();
            $spreadsheet = IOFactory::load($file);
            $sheetData   = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            $collect     = collect($sheetData);
            if ($collect->isEmpty()) {
                throw new UserFixException('上传文件内容为空');
            }

            $times = DepartmentMapCentre::max('times');
            $times = !$times ? 1 : $times + 1;
            $collect->each(function ($item, $key) use ($times) {

                if ($key > 2 && $item['A']) {
                    $row = [
                        'user_id'              => Auth::id(),
                        'department_level1'    => $item['A'],
                        'department_level2'    => $item['B'],
                        'department_full_path' => $item['A'] . '/' . $item['B'],
                        'centre_name'          => $item['C'],
                        'times'                => $times,
                    ];

                    DepartmentMapCentre::create($row);
                }
            });
        } catch (Exception $e) {
            $status = 'fail';
            $mes    = $e->getMessage();
        }

        return response()->json(['status' => $status, 'messages' => $mes]);
    }
}
