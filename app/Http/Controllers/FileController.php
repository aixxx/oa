<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Dh;
use App\Models\FileStorage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Response;
use Auth;
use Exception;
use Storage;
use DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DevFixException;
class FileController extends Controller
{
    public function index()
    {
        $files = FileStorage::getUserFile(Auth::id());
        return view('file.index', ['files' => $files]);
    }

    public function store(Request $request)
    {
        $files       = $request->file();
        $source      = $request->input('file_source');
        $source_type = $request->input('file_source_type');
        if (!$source_type || !$source) {
            throw new DevFixException('请求参数缺失', 422);
        }
        $user_id = Auth::id();
        try {
            $fileModels = [];
            DB::beginTransaction();
            foreach ($files as $key => $file) {
                $content                      = file_get_contents($file->path());
                $hash                         = $this->getHash($content, $user_id);
                $fileModel                    = $this->saveFile($hash, $user_id);
                $fileModel->source            = $source;
                $fileModel->source_type       = $source_type;
                $fileModel->filename          = $file->getClientOriginalName();
                $fileModel->mime_type         = $file->getMimeType();
                $fileModel->storage_system    = Storage::getDefaultDriver();
                $fileModel->storage_full_path = "workflow/$source_type/$source/{$fileModel->id}.{$file->getClientOriginalExtension()}";
                $fileModel->save();
                $fileModels[$key] = [
                    'id'       => $fileModel->id,
                    'filehash' => $fileModel->filehash,
                ];;
                $fileModel->save_result = Storage::put($fileModel->storage_full_path, $content);
                if (!Storage::exists($fileModel->storage_full_path)) {
                    throw new DevFixException('文件存储失败', 500);
                }
            }
            DB::commit();
            return response()->json(['code' => 0, 'status' => 'success', 'message' => '文件上传成功！', 'data' => $fileModels]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['code' => $e->getCode(), 'status' => 'failed', 'message' => '文件上传失败' . $e->getMessage()], 500);
        }

    }

    public static function getFileUrl($id)
    {
        $file = FileStorage::findOrFail($id);
        return self::getFileUrlByModel($file);
    }

    public static function getFileUrlByModel(FileStorage $file)
    {
        $sign = self::getSign($file, $timestamp);
        return route('file.show', ['id' => $file->id, 'filehash' => $file->filehash, 'sign' => $sign, 'timestamp' => $timestamp]);
    }

    public static function showWithHtml($id)
    {
        $file = FileStorage::findOrFail($id);
        $url  = self::getFileUrlByModel($file);
        if ($file->getType() == 'image') {
            return "<a href='$url' class='preview-a' target='_blank' title='{$file->filename}'><img src='$url' class='preview'/></a>";
        } else {
            return "<a href='$url' class='preview-a' target='_blank'>{$file->filename}</a>";
        }
    }

    /**
     * @param FileStorage $fileStorage
     * @param string $timestamp
     * @return string
     * @author hurs
     */
    public static function getSign(FileStorage $fileStorage, &$timestamp = '')
    {
        if (!$timestamp) {
            $timestamp = time();
        }
        return md5($fileStorage->user_id . $fileStorage->filehash . $timestamp . config('app.key'));
    }

    public function show(Request $request, $id, $filehash = '')
    {
        $file = FileStorage::findOrFail($id);
        if ($file->filehash != $request->input('filehash', $filehash)) {
            throw new NotFoundHttpException(1);
        }
        $timestamp = $request->input('timestamp', time());
        $sign      = $request->input('sign');
        if (abs($timestamp - time()) > Dh::PERIOD_1DAY) {
            throw new NotFoundHttpException();
        }
        if ($sign != self::getSign($file, $timestamp)) {
            throw new NotFoundHttpException();
        }
        $headers = [
            'content-type'        => $file->mime_type,
            'Cache-Control'       => 'public',
            'Last-Modified'       => $file->updated_at,
            'Content-Disposition' => 'filename=' . $file->filename,
        ];
        if ($request->header('If-Modified-Since', Carbon::now()) <= $file->updated_at) {
            return Response::make('', 304, $headers);
        }
        $remote_path = Storage::disk($file->storage_system)->path(strval(str_replace("\0", "", $file->storage_full_path)));
        if (in_array($file->storage_system, FileStorage::FILE_SYSTEM)) {
            return Response::file($remote_path, $headers);
        } else {
            $real_file_bit = Storage::get($remote_path);
            return Response::make($real_file_bit, 200, $headers);
        }
    }


    private function saveFile($hash, $user_id)
    {
        return FileStorage::firstOrCreateFile($hash, $user_id);
    }

    private function getHash($content, $user_id)
    {
        return md5($user_id . '-' . $content);

    }
}
