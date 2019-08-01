<?php

namespace App\Models\Vacations;

use Illuminate\Database\Eloquent\Model;

class VacationExtraWorkflowPass extends Model
{
    //
    protected $table = 'vacation_extra_workflow_pass';

    protected $fillable = [
        'id',
        'begin_end_dates',
        'times',
        'user_id',
        'entry_id',
        'deleted_at',
        'created_at',
        'updated_at',
    ];
}
