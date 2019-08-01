<?php

namespace App\Services;

use DB;
use Log;
use Exception;
use Carbon\Carbon;
use DevFixException;
use Silber\Bouncer\Database\Ability;

class PermissionService
{
    protected $menu;
    protected $titleMap;

    public function __construct()
    {
        $this->menu     = config('constant.menu');
        $this->titleMap = config('constant.titleMap');
    }

    public function operateAbilities()
    {
        $existRecordMessage = $newRecordMessage = '';
        $allAbilitiesTitle  = Ability::all()->pluck('title', 'id');
        $allAbilitiesName   = Ability::all()->pluck('name', 'id');
        $constantData       = [];

        DB::beginTransaction();
        try {
            if (!empty($this->menu)) {
                foreach ($this->menu as $k1 => $m1) {
                    if (isset($m1['children'])) {
                        foreach ($m1['children'] as $m2) {
                            if (isset($m2['children'])) {
                                foreach ($m2['children'] as $m3) {
                                    if (isset($m3['abilities'])) {
                                        foreach ($m3['abilities'] as $k => $m4) {
                                            $data = [
                                                'name'       => $m3['prefix'] . '_' . $k,
                                                'title'      => $m3['title'] . '-' . $this->titleMap[$k],
                                                'level1_no'  => $m1['no'],
                                                'level2_no'  => $m2['no'],
                                                'level3_no'  => $m3['no'],
                                                'root_code'  => $k1,
                                                'created_at' => Carbon::now()->toDateTimeString(),
                                                'updated_at' => Carbon::now()->toDateTimeString(),
                                            ];

                                            $constantData[$data['name']] = $data['title'];
                                            $record                      = $this->fetchAbilityByName($data['name']);
                                            if ($record && $record->first()) {
                                                if ($this->check($record->first(), $data)) {
                                                    $existRecordMessage .= $data['name'] . '已经存在';
                                                } else {
                                                    $existRecordMessage .= $data['name'] . '更新';
                                                    $record->update($data);
                                                }
                                                Log::info($existRecordMessage);
                                            } else {
                                                DB::table('abilities')->insert($data);
                                                $newRecordMessage .= '写入name为：' . $data['name'] . '的信息';
                                                Log::info($newRecordMessage);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($allAbilitiesTitle)) {
                foreach ($allAbilitiesTitle as $k => $a) {
                    if (!isset($constantData[$allAbilitiesName[$k]])) {
                        Log::info('删除权限:' . $a);
                        DB::table('permissions')
                            ->where('ability_id', '=', $k)
                            ->where('entity_type', '=', 'roles')->delete();
                        DB::table('abilities')->delete($k);

                    }
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw $e;
        }
    }

    public function fetchFileName()
    {
        $allAbilities = Ability::all()->pluck('title', 'name');
        $fileName     = [];
        $constantData = [];

        if (!empty($this->menu)) {
            foreach ($this->menu as $k1 => $m1) {
                if (isset($m1['children'])) {
                    foreach ($m1['children'] as $m2) {
                        if (isset($m2['children'])) {
                            foreach ($m2['children'] as $m3) {
                                if (isset($m3['abilities'])) {
                                    foreach ($m3['abilities'] as $k => $m4) {
                                        $data                        = [
                                            'name'      => $m3['prefix'] . '_' . $k,
                                            'title'     => $m3['title'] . '-' . $this->titleMap[$k],
                                            'level1_no' => $m1['no'],
                                            'level2_no' => $m2['no'],
                                            'level3_no' => $m3['no'],
                                            'root_code' => $k1,
                                        ];
                                        $constantData[$data['name']] = $data['title'];
                                        $record                      = $this->fetchAbilityByName($data['name']);

                                        //通过constant新增的权限名字
                                        if (!$record || !$record->first()) {
                                            $fileName[] = $k1 . '_' . $data['name'];
                                        } else {
                                            //通过constant修改的权限名字
                                            if (!$this->check($record->first(), $data)) {
                                                $fileName[] = $k1 . '_' . $data['name'];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!empty($allAbilities)) {
            foreach ($allAbilities as $k => $a) {
                if (!isset($constantData[$k])) {
                    $fileName[] = $k;
                }
            }
        }

        if (empty($fileName)) {
            throw new DevFixException('权限没有任何修改，无法生成新的migration文件');
        }

        return $fileName['0'];
    }

    private function fetchAbilityByName($name)
    {
        return DB::table('abilities')->where('name', $name);
    }

    private function check($record, $data)
    {
        if ($record->title != $data['title']) {
            return false;
        }

        if ($record->level1_no != $data['level1_no']) {
            return false;
        }

        if ($record->level2_no != $data['level2_no']) {
            return false;
        }

        if ($record->level3_no != $data['level3_no']) {
            return false;
        }

        if ($record->root_code != $data['root_code']) {
            return false;
        }
        return true;
    }
}
