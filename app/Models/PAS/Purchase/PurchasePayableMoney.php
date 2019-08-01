<?php

namespace App\Models\PAS\Purchase;
use Illuminate\Database\Eloquent\Model;

class PurchasePayableMoney extends Model {

    protected $table = 'pas_purchase_payable_money';

    protected $fillable = ['id',"supplier_id",'money','status'];
}