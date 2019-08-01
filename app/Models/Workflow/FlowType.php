<?php

namespace App\Models\Workflow;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Workflow\FlowType
 *
 * @property int $id
 * @property string $type_name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Workflow\Flow[] $flow
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\FlowType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\FlowType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\FlowType whereTypeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\FlowType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FlowType extends Model
{
    protected $table = "workflow_flow_types";

    public $fillable=['type_name'];

    public function flow()
    {
        return $this->hasMany('App\Models\Workflow\Flow', 'type_id');
    }

    public function publish_flow()
    {
        return $this->flow()->publish()->show();
    }


    public static function getTypes()
    {
        return static::orderBy('id')->get();
    }

    public function valid_flow()
    {
        return $this->flow()->publish()->show()->unabandon();
    }
}
