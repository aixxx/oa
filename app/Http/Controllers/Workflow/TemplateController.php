<?php

namespace App\Http\Controllers\Workflow;

use App\Http\Controllers\Controller;
use App\Models\Workflow\Flow;
use App\Models\Workflow\Template;
use App\Models\Workflow\TemplateForm;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates = Template::orderBy('id','DESC')->get();
        return view('workflow.template.index')->with(compact('templates'));
    }

    public function create()
    {
        return view('workflow.template.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $this->validate($request,[
            'template_name'=>'required',
        ],[
            'template_name.required'=>'模板名称不能为空'
        ]);

        Template::create($request->all());

        return redirect()->route('workflow.template.index')->with(['success'=>1,'message'=>'添加成功']);
    }


    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $template = Template::findOrFail($id);
        $forms = TemplateForm::with('template')->where('template_id',$id)->orderBy('sort','ASC')->orderBy('id','DESC')->get();

        return view('workflow.template.edit')->with(compact('template','forms'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'template_name'=>'required',
        ],[
            'template_name.required'=>'模板名称不能为空'
        ]);

        Template::findOrFail($id)->update($request->all());

        return redirect()->route('workflow.template.index')->with(['success'=>1,'message'=>'更新成功']);
    }

    public function destroy($id)
    {
        $template=Template::findOrFail($id);

        if(Flow::where('template_id',$template->id)->first()){
            return response()->json([
                'error'=>1,
                'msg'=>'该模板正在被使用，不能删除'
            ]);
        }

        $template->delete();
        $template->template_form()->delete();

        return response()->json([
            'error'=>0,
            'msg'=>'模板删除成功'
        ]);
    }
}
