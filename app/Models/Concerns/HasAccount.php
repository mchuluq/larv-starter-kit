<?php

namespace App\Models\Concerns;

use App\Models\Rbac\Account;
use App\Models\Rbac\DataPermission;
use App\Models\Rbac\RoleActor;
use App\Models\Rbac\RoutePermission;

use App\Models\Concerns\ModelStorage;

use Illuminate\Support\Facades\DB;

trait HasAccount{

    protected static function bootHasAccount(){
        static::saved(function ($model) {
            $model->storage()->destroy();
        });
    }

    public function storage(){
        $storage = new ModelStorage('account');
        $storage->setId($this->id);
        return $storage;
    }

    public function accounts($active=true){
        return $this->hasMany(Account::class)->where(function($q) use ($active){
            if($active == true){
                $q->where('active','=',1);
            }
        });
    }
    
    public function account(){
        return $this->hasOne(Account::class,'id','account_id')->where('active','=',1);
    }

    public function accountOptions($active=true){
        return $this->accounts($active)->pluck('accountable_id','id');
    }

    public function setAccount($id){
        $account = $this->account(true)->where('id','=',$id)->first();
        if(!$account){
            return false;
        }
        return $this->fill(['account_id'=>$account->id])->save();
    }

    public function getRoutePermissions(){
        return $this->storage()->get('permission.route',function(){
            return $this->_getRoutePermissions();
        });
    }

    public function getDataPermissions(){
        return $this->storage()->get('permission.data',function(){
            return $this->_getDataPermissions();
        });
    }

    private function _getRoutePermissions(){
        $account = $this->account;
        $tperm = with(new RoutePermission)->getTable();
        $troleact = with(new RoleActor)->getTable();

        $result = [];
        $res = DB::table($tperm . " AS a")->select("a.route AS route")
        ->where("a.account_id", $account->id)
            ->orWhere("a.group_id", $account->group_id)
            ->orWhereRaw("(a.role_id IN (SELECT c.role_id FROM " . $troleact . " c WHERE (c.account_id = ?)))", [$account->id])
            ->orWhereRaw("(a.role_id IN (SELECT c.role_id FROM " . $troleact . " c WHERE (c.group_id = ?)))", [$account->group_id])
            ->groupBy('a.route')->get();
        foreach ($res as $r) {
            $result[] = $r->route;
        }
        return $result;
    }

    private function _getDataPermissions(){
        $account = $this->account;
        $tda = with(new DataPermission)->getTable();
        $troleact = with(new RoleActor)->getTable();

        $result = [];
        $res = DB::table($tda . " AS a")->select("a.data_type","a.data_id")
        ->where("a.account_id", $account->id)
            ->orWhere("a.group_id", $account->group_id)
            ->orWhereRaw("(a.role_id IN (SELECT c.role_id FROM " . $troleact . " c WHERE (c.account_id = ?)))", [$account->account_id])
            ->orWhereRaw("(a.role_id IN (SELECT c.role_id FROM " . $troleact . " c WHERE (c.group_id = ?)))", [$account->group_id])
            ->groupBy('a.data_id','a.data_type')->get();
        foreach ($res as $r) {
            $result[$r->data_type][] = $r->data_id;
        }
        return $result;
    }

}
