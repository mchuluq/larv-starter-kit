<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;

class ModelStorage {

    protected $id = null;
    protected $name = null;

    public function __construct($name){
        $this->name = $name;
    }
    public function getObjectStorageKey(){
        return md5($this->name.$this->id);
    }
    public function setId($id){
        $this->id = $id;
    }
    public function get($data_key,$default=null){
        $data = $this->_getCache($default);
        return Arr::get($data,$data_key,$default);
    }
    public function set($key,$val=null){
        $data = $this->_getCache();
        if(is_array($key)){
            $data = array_merge($data,$key);
        }else{
            Arr::set($data, $key,$val);
        }
        return $this->_writeCache($data);
    }
    public function unset($key){
        $data = $this->_getCache();
        Arr::forget($data,$key);
        return $this->_writeCache($data);
    }
    public function destroy(){
        return Cache::store('redis')->forget($this->getObjectStorageKey());       
    }
    public function getAllStorage(){
        return $this->_getCache();
    }
    private function _getCache($default=array()){
        return Cache::store('redis')->get($this->getObjectStorageKey(),function() use ($default){
            $data = call_user_func($default);
            $this->_writeCache($data);
            return $data;
        });
    }
    private function _writeCache($data=null){
        return Cache::store('redis')->forever($this->getObjectStorageKey(),$data);
    }
}