<?php

namespace App\Models\Workflow;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Workflow\Template
 *
 * @property int $id
 * @property string $template_name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Workflow\TemplateForm[] $template_form
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Template whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Template whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Template whereTemplateName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Template whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Template extends Model
{
    protected $table = "workflow_templates";

    protected $fillable = ['template_name'];

    public function template_form()
    {
        return $this->hasMany('App\Models\Workflow\TemplateForm', 'template_id')->orderBy('sort', 'asc')->orderBy('id', 'DESC');
    }
    public function template_form_show_in_todo()
    {
        return $this->hasMany('App\Models\Workflow\TemplateForm', 'template_id')
            ->where('show_in_todo', '=', 1)->orderBy('sort', 'asc')->orderBy('id', 'DESC');
    }
}
