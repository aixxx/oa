<?php

namespace App\Models\Position;

use App\Models\Department;
use App\Models\Power\PositionsRoles;
use App\Models\Power\Roles;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use \Exception;

class Position extends Model
{
    const STATUS_IS_LEADER_YES = 1;
    const STATUS_IS_LEADER_NO = 0;

    protected $table = 'position';

    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $primaryKey = 'id';
    public $fillable = [
        'name',
        'is_leader',
    ];

    public static function create($info){
        try{
            //创建职务
            $position = Position::query()->create($info);
            if(isset($info['roles'])){
                $roles = [];
                foreach ($info['roles'] as $v){
                    $roles[] = [
                        'position_id' => $position->id,
                        'role_id' => $v,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ];
                }
                //增加角色关联
                if($roles)
                    PositionsRoles::query()->insert($roles);
            }
            //关联部门
            PositionDepartment::query()->create([
                'position_id' => $position->id,
                'department_id' => $info['deptId'],
            ]);
            return $position;
        }catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }


    public function belongsToManyRoles()
    {
        return $this->belongsToMany(Roles::class, 'api_positions_roles',
            'position_id', 'role_id')->whereNull('api_positions_roles.deleted_at');
    }

    public function hasOneDept()
    {
        return $this->hasOne(PositionDepartment::class,'position_id', 'id');
    }

    public static function getList($deptId){
        return self::query()
            ->rightJoin('position_department as pd','position.id','=','pd.position_id')
            ->where('pd.department_id',$deptId)
            ->with(['belongsToManyRoles'])
            ->orderBy('id', 'desc')
            ->select(['position.*'])
            ->get();
    }
}
