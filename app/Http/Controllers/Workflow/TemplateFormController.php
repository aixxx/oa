<?php

namespace App\Http\Controllers\Workflow;

use App\Http\Controllers\Controller;
use App\Models\Workflow\Template;
use App\Models\Workflow\TemplateForm;
use App\Models\Workflow\Workflow;
use Illuminate\Http\Request;

class TemplateFormController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $template_id   = Template::findOrFail($request->input('template_id'))->id;
        $template_form = new TemplateForm();
        $key_info      = Workflow::getApplerKeyInfo();
        return view('workflow.template_form.edit')->with(compact('template_form', 'key_info', 'template_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $this->validate($request, [
//            'field_name' => 'required',
            'field'      => 'required',
            'field_type' => 'required',
        ]);
        foreach ($data as &$d) {
            if (is_null($d)) {
                $d = '';
            }
        }
        TemplateForm::create($data);

        return redirect()->route('workflow.template.edit', ['id' => $data['template_id']]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $template_form = TemplateForm::findOrFail($id);
        $template_id   = $template_form->template->id;
        $key_info      = Workflow::getApplerKeyInfo();
        return view('workflow.template_form.edit')->with(compact('template_form', 'key_info', 'template_id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();

        $this->validate($request, [
//            'field_name' => 'required',
            'field'      => 'required',
            'field_type' => 'required',
        ]);

        $template_form = TemplateForm::findOrFail($id);

        $template_form->update($data);

        return redirect()->route('workflow.template.edit', ['id' => $template_form->template_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $template_form = TemplateForm::findOrFail($id);

        $template_form->delete();

        return response()->json([
            'error' => 0,
            'msg'   => '模板控件删除成功',
        ]);
    }
}
