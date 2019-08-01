<?php

namespace App\Models\PAS\Purchase;
   
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model {

    protected $table = 'pas_supplier';


    protected $fillable = ['id',"user_id","code","title","mnemonic","address","tel",'fax','opening_bank','number','corporations','contacts','phone'];

}