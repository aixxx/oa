<?php

namespace App\Models\Workflow;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Workflow\EntryData
 *
 * @property int $id
 * @property int $entry_id
 * @property int $flow_id
 * @property string $field_name
 * @property string $field_value
 * @property string $field_remark
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \App\Models\Workflow\Entry $entry
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\EntryData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\EntryData whereEntryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\EntryData whereFieldName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\EntryData whereFieldRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\EntryData whereFieldValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\EntryData whereFlowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\EntryData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\EntryData whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $use_coffer 是否使用coffer 保存数据
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\EntryData whereUseCoffer($value)
 */
class EntryData extends Model
{
    protected $table = "workflow_entry_data";

    protected $fillable = ['entry_id', 'flow_id', 'field_name', 'field_value'];

    public function entry()
    {
        return $this->belongsTo(\App\Models\Workflow\Entry::class, 'entry_id', 'id');
    }

    public static function getFieldValue($entry_id, $field_name)
    {
        return EntryData::where([
            'entry_id'   => $entry_id,
            'field_name' => $field_name,
        ])->value('field_value');
    }

    /**
     * @param $flow_ids
     * @param $begin_at
     * @param $end_at
     * @param $status
     * @param int $entry_id
     * @param int $user_id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @author hurs
     */
    public static function getFlowData($flow_ids, $begin_at = null, $end_at = null, $status = null, $entry_id = 0, $user_id = 0)
    {
        $building = self::whereIn('flow_id', $flow_ids)->with('entry');
        $building->whereHas('entry', function ($query) use ($entry_id, $begin_at, $end_at, $status, $user_id) {
            /** var $query */
            if ($user_id) {
                $query->where('user_id', $user_id);
            }
            if (!is_null($status)) {
                $query->where('status', $status);
            }

            if ($begin_at && $end_at) {
                $query->whereBetween('created_at', [$begin_at, $end_at]);
            }
            if ($entry_id) {
                $query->where('id', $entry_id);
            }
        });
        return $building->get();
    }


    public static function getFlowDataV2($flow_ids, $begin_at = null, $end_at = null, $status = null, $entry_id = 0, $user_id = 0)
    {
        $building = self::whereIn('flow_id', $flow_ids)->with('entry.flow', 'entry.user');
        $building->whereHas('entry', function ($query) use ($entry_id, $begin_at, $end_at, $status, $user_id) {
            /** var $query */
            if ($user_id) {
                if (gettype($user_id) == "array") {
                    $query->whereIn('user_id', $user_id);
                } else {
                    $query->where('user_id', $user_id);
                }
            }
            if (!is_null($status)) {
                $query->where('status', $status);
            }


            if ($entry_id) {
                $query->where('id', $entry_id);
            }

            if ($begin_at) {
                $query->where('created_at', '>=', $begin_at);
            }

            if ($end_at) {
                $query->where('created_at', '<=', $end_at." 23:59:59");
            }
        });
        return $building->get();
    }
}





















