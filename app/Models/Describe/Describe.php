<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/18
 * Time: 9:36
 */

namespace App\Models\Describe;

use Illuminate\Foundation\Auth\User as Authenticate;

class Describe extends Authenticate
{
    protected $table = "describe";

    protected $fillable = [
      "user_id",
      "positive_please",
      "wage_classes",
      "salary_scale",
      "points_scale",
    ];

    protected $hidden = [

    ];

}
