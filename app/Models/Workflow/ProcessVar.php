<?php

namespace App\Models\Workflow;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Workflow\ProcessVar
 *
 * @property int $id
 * @property int $process_id
 * @property int $flow_id
 * @property string $expression_field
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\ProcessVar whereExpressionField($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\ProcessVar whereFlowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\ProcessVar whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\ProcessVar whereProcessId($value)
 * @mixin \Eloquent
 */
class ProcessVar extends Model
{
    protected $table = "workflow_process_var";

    public $timestamps = false;

    protected $fillable = ['process_id', 'flow_id', 'expression_field', 'description'];

    /**
     * @param $process_id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @author hurs
     */
    public static function getByProcessId($process_id)
    {
        return ProcessVar::where(['process_id' => $process_id])->get();

    }
}
