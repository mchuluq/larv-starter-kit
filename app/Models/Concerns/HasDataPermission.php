<?php

namespace App\Models\Concerns;

use App\Models\Rbac\Account;
use App\Models\Rbac\Group;
use App\Models\Rbac\Role;
use App\Models\Rbac\DataPermission;

trait HasDataPermission {

    function dataPermissions(){
        if ($this instanceof Account) {
            return $this->hasMany(DataPermission::class, 'account_id', 'id');
        } elseif ($this instanceof Group) {
            return $this->hasMany(DataPermission::class, 'group_id', 'id');
        } elseif ($this instanceof Role) {
            return $this->hasMany(DataPermission::class, 'role_id', 'id');
        }
        return;
    }

    function assignDataPermission($data_id,$data_type){
        $dataaccess = new DataPermission();
        $this->removeDataAccess($data_type);
        if ($this instanceof Account) {
            return (!is_null($this->id)) ? $dataaccess->assign($this->id, $data_id,$data_type, 'account_id') : false;
        } elseif ($this instanceof Group) {
            return (!is_null($this->id)) ? $dataaccess->assign($this->id, $data_id,$data_type, 'group_id') : false;
        } elseif ($this instanceof Role) {
            return (!is_null($this->id)) ? $dataaccess->assign($this->id, $data_id,$data_type, 'role_id') : false;
        }
        return;
    }

    function removeDataPermission($data_type=null){
        $dataaccess = new DataPermission();
        if ($this instanceof Account) {
            return (!is_null($this->id)) ? $dataaccess->remove($this->id,$data_type, 'account_id') : false;
        } elseif ($this instanceof Group) {
            return (!is_null($this->id)) ? $dataaccess->remove($this->id,$data_type, 'group_id') : false;
        } elseif ($this instanceof Role) {
            return (!is_null($this->id)) ? $dataaccess->remove($this->id,$data_type, 'role_id') : false;
        }
        return;
    }

    function getDataPermission(){
        $dataaccess = new DataPermission();
        if ($this instanceof Account) {
            $this->attributes['data_permissions'] = (!is_null($this->id)) ? $dataaccess->getFor($this->id, 'account_id') : false;
        } elseif ($this instanceof Group) {
            $this->attributes['data_permissions'] = (!is_null($this->id)) ? $dataaccess->getFor($this->id, 'group_id') : false;
        } elseif ($this instanceof Role) {
            $this->attributes['data_permissions'] = (!is_null($this->id)) ? $dataaccess->getFor($this->id, 'role_id') : false;
        }
        return $this;
    }

    function isHasDataPermission($data_id,$data_type){
        if (!isset($this->attributes['data_permissions'])) {
            return false;
        }
        return in_array($data_id, $this->attributes['data_permissions'][$data_type]);
    }

}