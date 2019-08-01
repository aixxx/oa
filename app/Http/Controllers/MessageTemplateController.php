<?php
namespace App\Http\Controllers;

use App\Constant\CodeConstant;
use App\Constant\CommonConstant;
use App\Http\Helpers\Dh;
use App\Models\MessageTemplate;
use App\Services\Common\FileService;
use Carbon\Carbon;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use UserFixException;

class MessageTemplateController extends Controller
{
    /**
     * 列表页面
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $searchParams = $request->all();
        $searchModel  = new MessageTemplate();
        $searchModel->fill($searchParams);
        $messageTemplates = MessageTemplate::search($searchModel);

        return view('message.index', compact('messageTemplates', 'searchModel', 'searchParams'));
    }

    /**
     * 查看页面
     *
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view($id)
    {
        $model = $this->findModel($id);

        return view('message.view', compact('model'));
    }

    /**
     * 新建
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        $model = new MessageTemplate();
        if ($request->isMethod(Request::METHOD_POST)) {
            $this->validate($request, [
                'template_key'       => [
                    'required',
                    'string',
                    'max:45',
                    Rule::unique('message_template')->where(function ($query) use ($request) {
                        $query->where('template_push_type', $request->get('template_push_type'))
                            ->where('template_deleted', CommonConstant::FLAG_IS_NOT_DELETED);
                    }),
                ],
                'template_name'      => 'required|string|max:255',
                'template_type'      => 'required|string|max:45',
                'template_sign'      => 'required|string|max:45',
                'template_push_type' => [
                    'required',
                    'string',
                    Rule::in(array_keys(CommonConstant::MESSAGE_PUSH_TYPE_MAPPING)),
                ],
                'template_title'     => 'required|string|max:255',
                'template_content'   => 'required|string',
                'template_status'    => [
                    'required',
                    'string',
                    Rule::in(array_keys(CommonConstant::STATUS_MAPPING)),
                ],
                'template_memo'      => 'required',
            ]);
            $model->fill(array_merge($request->all(), [
                'template_created_user' => auth()->id(),
                'template_updated_user' => auth()->id(),
                'template_created_at'   => Dh::getcurrentDateTime(),
            ]));

            if ($model->save()) {
                return redirect()->route('message.template.view', ['id' => $model->template_id]);
            }
        }

        return view('message.create', compact('model'));
    }

    /**
     * 更新
     *
     * @param \Illuminate\Http\Request $request
     * @param                          $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function update(Request $request, $id)
    {
        $model = $this->findModel($id);

        if ($request->isMethod(Request::METHOD_POST)) {
            $this->validate($request, [
                'template_key'       => [
                    'required',
                    'string',
                    'max:45',
                    Rule::unique('message_template')->where(function ($query) use ($model, $request) {
                        $query->where('template_id', '!=', $model->template_id)
                            ->where(
                                'template_push_type',
                                $request->get('template_push_type', $model->template_push_type)
                            )
                            ->where('template_deleted', CommonConstant::FLAG_IS_NOT_DELETED);
                    }),
                ],
                'template_name'      => 'required|string|max:255',
                'template_type'      => 'required|string|max:45',
                'template_sign'      => 'required|string|max:45',
                'template_push_type' => [
                    'required',
                    'string',
                    Rule::in(array_keys(CommonConstant::MESSAGE_PUSH_TYPE_MAPPING)),
                ],
                'template_title'     => 'required|string|max:255',
                'template_content'   => 'required',
                'template_status'    => [
                    'required',
                    'string',
                    Rule::in(array_keys(CommonConstant::STATUS_MAPPING)),
                ],
                'template_memo'      => 'required',
            ]);
            $model->fill(array_merge($request->all(), [
                'template_updated_user' => auth()->id(),
            ]));

            if ($model->save()) {
                return redirect()->route('message.template.view', ['id' => $model->template_id]);
            }
        }

        return view('message.edit', compact('model'));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        try {
            $model = $this->findModel($id);
            if ($model->delete()) {
                return response()->json([
                    'code'    => CodeConstant::RESPONSE_SUCCESS,
                    'message' => '删除成功',
                ]);
            }

            return response()->json([
                'code'    => CodeConstant::RESPONSE_FAIL,
                'message' => '删除失败',
            ]);
        } catch (NotFoundHttpException $ex) {
            return response()->json([
                'code'    => CodeConstant::RESPONSE_FAIL,
                'message' => $ex->getMessage(),
            ]);
        } catch (\Exception $ex) {
            Log::error('删除消息模板出错', [
                'message' => $ex->getMessage(),
                'trace'   => $ex->getTrace(),
            ]);
            report($ex);

            return response()->json([
                'code'    => CodeConstant::RESPONSE_EXCEPTION,
                'message' => '服务器出错',
            ]);
        }
    }

    /**
     * 导出
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \UserFixException
     */
    public function export(Request $request)
    {
        $templateIds = $request->input('templates');
        if (empty($templateIds)) {
            throw new UserFixException('请至少选择一个模板');
        }

        $templates = MessageTemplate::whereIn('template_id', $templateIds)
            ->where('template_deleted', CommonConstant::FLAG_IS_NOT_DELETED)
            ->get([
                'template_key',
                'template_name',
                'template_type',
                'template_sign',
                'template_push_type',
                'template_title',
                'template_content',
                'template_status',
                'template_memo',
            ])
            ->toArray();

        $fileName = sprintf(
            'message_templates_%s_%s.json',
            auth()->user()->chinese_name,
            Carbon::now()->format('YmdHis')
        );
        $data     = json_encode($templates, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_UNICODE);

        (new FileService('json', $fileName, $data))->export();
    }

    /**
     * @param \Illuminate\Http\Request $request
     */
    public function exportAll(Request $request)
    {
        $templates = MessageTemplate::where('template_deleted', CommonConstant::FLAG_IS_NOT_DELETED)
            ->get([
                'template_key',
                'template_name',
                'template_type',
                'template_sign',
                'template_push_type',
                'template_title',
                'template_content',
                'template_status',
                'template_memo',
            ])
            ->toArray();

        $fileName = sprintf(
            'message_templates_all_%s_%s.json',
            auth()->user()->chinese_name,
            Carbon::now()->format('YmdHis')
        );

        $data = json_encode($templates, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_UNICODE);

        (new FileService('json', $fileName, $data))->export();
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        try {
            $path       = $request->file('import-file')->path();
            $importData = collect(json_decode(File::get($path), true));
            if ($importData->isEmpty()) {
                Log::error('消息模板导入出错，文件内容为空', [
                    'file_path' => $path,
                ]);
                throw new UserFixException('消息模板导入出错，文件内容为空');
            }
            DB::transaction(function () use ($importData) {
                $importData->each(function ($item, $key) {
                    $messageTemplate = MessageTemplate::where('template_key', $item['template_key'])
                        ->where('template_push_type', $item['template_push_type'])
                        ->where('template_deleted', CommonConstant::FLAG_IS_NOT_DELETED)
                        ->first();
                    if (!$messageTemplate) {
                        $messageTemplate = new MessageTemplate();
                        $messageTemplate->fill(array_merge($item, [
                            'template_created_user' => auth()->id(),
                            'template_updated_user' => auth()->id(),
                            'template_created_at'   => Carbon::now()->toDateTimeString(),
                        ]));
                    } else {
                        $messageTemplate->fill(array_merge($item, [
                            'template_updated_user' => auth()->id(),
                        ]));
                    }
                    if (!$messageTemplate->save()) {
                        throw new \Exception('保存数据出错' .
                            $messageTemplate->toJson(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_UNICODE));
                    }
                });
            });

            return response()->json(['code' => CodeConstant::RESPONSE_SUCCESS, 'messages' => '消息模板导入成功']);
        } catch (\Exception $ex) {
            Log::error('消息模板导入出错', [
                'message' => $ex->getMessage(),
                'trace'   => $ex->getTrace(),
            ]);
            report($ex);

            return response()->json(['code' => CodeConstant::RESPONSE_FAIL, 'messages' => '消息模板导入出错']);
        }
    }

    /**
     * @param $id
     *
     * @return \App\Models\MessageTemplate|\App\Models\MessageTemplate[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = MessageTemplate::where('template_deleted', CommonConstant::FLAG_IS_NOT_DELETED)
                ->find($id)) == null) {
            throw new NotFoundHttpException('该资源未找到');
        }

        return $model;
    }
}
