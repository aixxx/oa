<?php

namespace App\Models\Workflow;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Workflow\TemplateForm
 *
 * @property int $id
 * @property int $template_id
 * @property string $field
 * @property string $field_name
 * @property string $field_type
 * @property string $field_value
 * @property string $field_default_value
 * @property string $unit
 * @property string $rules
 * @property int $sort
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Workflow\Template $template
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\TemplateForm whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\TemplateForm whereField($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\TemplateForm whereFieldDefaultValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\TemplateForm whereFieldName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\TemplateForm whereFieldType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\TemplateForm whereFieldValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\TemplateForm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\TemplateForm whereRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\TemplateForm whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\TemplateForm whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\TemplateForm whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $location
 * @property string $required
 * @property int $show_in_todo
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\TemplateForm whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\TemplateForm whereRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\TemplateForm whereUnit($value)
 * @property string $placeholder placeholder
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\TemplateForm wherePlaceholder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\TemplateForm whereShowInTodo($value)
 * @property int $use_coffer 是否使用coffer 保存数据
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\TemplateForm whereUseCoffer($value)
 * @property string $field_extra_css 额外的css样式类
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\TemplateForm whereFieldExtraCss($value)
 */
class TemplateForm extends Model
{
    protected $table = "workflow_template_forms";

    protected $fillable = [
        'template_id',
        'field',
        'field_name',
        'placeholder',
        'field_type',
        'field_value',
        'field_default_value',
        'field_extra_css',
        'unit',
        'sort',
        'location',
        'required',
        'show_in_todo',
        'length',
    ];

    public function template()
    {
        return $this->belongsTo('App\Models\Workflow\Template', 'template_id');
    }

    public function getShowFieldName()
    {
        return $this->field_name ?? $this->field;
    }

    public function isHideFieldNameLabel($key_info = [])
    {
        return !$this->field_name ||
            in_array($this->field_type, ['div', 'hidden']) ||
            (isset($key_info[$this->field]['is_hidden']) && $key_info[$this->field]['is_hidden']);
    }
}