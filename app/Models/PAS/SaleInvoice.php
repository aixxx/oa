<?php

namespace App\Models\PAS;

use Illuminate\Database\Eloquent\Model;

class SaleInvoice extends Model
{
    protected $table = 'pas_sale_invoices';
    protected $guarded = ['created_at', 'updated_at'];
    public $timestamps = false;

}
