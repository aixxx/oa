<?php

namespace App\Models\PAS;

use Illuminate\Database\Eloquent\Model;

class SpecificItem extends Model
{
    public $table = 'pas_specific_items';

    public $fillable = [
        'id',
        'spec_id',
        'name',
        'sort'
    ];
}
