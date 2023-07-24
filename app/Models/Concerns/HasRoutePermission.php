<?php

namespace App\Models\Concerns;

use App\Models\Rbac\Account;
use App\Models\Rbac\Group;
use App\Models\Rbac\Role;
use App\Models\Rbac\RoutePermission;

trait HasRoutePermission {

    function routesPermissions(){
        if ($this instanceof Account) {
            return $this->hasMany(RoutePermission::class, 'account_id', 'id');
        } elseif ($this instanceof Group) {
            return $this->hasMany(RoutePermission::class, 'group_id', 'id');
        } elseif ($this instanceof Role) {
            return $this->hasMany(RoutePermission::class, 'role_id', 'id');
        }
        return;
    }

    function assignRoutePermissions($menu_id){
        $permissions = new RoutePermission();
        $this->removePermissions();
        if ($this instanceof Account) {
            return (!is_null($this->id)) ? $permissions->assign($this->id, $menu_id, 'account_id') : false;
        } elseif ($this instanceof Group) {
            return (!is_null($this->id)) ? $permissions->assign($this->id, $menu_id, 'group_id') : false;
        } elseif ($this instanceof Role) {
            return (!is_null($this->id)) ? $permissions->assign($this->id, $menu_id, 'role_id') : false;
        }
        return;
    }

    function removeRoutePermissions(){
        $permissions = new RoutePermission();
        if ($this instanceof Account) {
            return (!is_null($this->id)) ? $permissions->remove($this->id, 'account_id') : false;
        } elseif ($this instanceof Group) {
            return (!is_null($this->id)) ? $permissions->remove($this->id, 'group_id') : false;
        } elseif ($this instanceof Role) {
            return (!is_null($this->id)) ? $permissions->remove($this->id, 'role_id') : false;
        }
        return;
    }

    function getRoutePermissions(){
        $permissions = new RoutePermission();
        if ($this instanceof Account) {
            $this->attributes['route_permissions'] = (!is_null($this->id)) ? $permissions->getFor($this->id, 'account_id') : false;
        } elseif ($this instanceof Group) {
            $this->attributes['route_permissions'] = (!is_null($this->id)) ? $permissions->getFor($this->id, 'group_id') : false;
        } elseif ($this instanceof Role) {
            $this->attributes['route_permissions'] = (!is_null($this->id)) ? $permissions->getFor($this->id, 'role_id') : false;
        }
        return $this;
    }

    function isHasRoutePermission($route){
        if (!isset($this->attributes['route_permissions'])) {
            return false;
        }
        return in_array($route, $this->attributes['route_permissions']);
    }

}