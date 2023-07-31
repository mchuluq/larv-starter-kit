<?php

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

Builder::macro('filterDataPermission', function ($column_name,$data_type,$skip_null=false) {
    if((!Auth::check() && $skip_null)){
        return $this;
    }else{
        $data_permissions = Auth::user()->getDataPermissions();
        $data_ids = Arr::get($data_permissions,$data_type,[]);
        if((empty($data_ids) && $skip_null)){
            return $this;
        }elseif(is_array($data_ids)){
            $this->whereIn($column_name, $data_ids);
        }else{
            $this->where($column_name, $data_ids);
        }
    }    
    return $this;
});