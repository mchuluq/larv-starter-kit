<?php namespace App\Models\Rbac;

use Illuminate\Database\Eloquent\Model;

class DataPermission extends Model{

    protected $primaryKey = null;
    public $incrementing = false;
    
    protected $fillable = array('data_id','data_type','role_id','group_id','account_id');

    function assign($for, $data_id, $data_type, $type = 'account_id'){
        $data = array();
        if (!$data_id || !$data_type) {
            return;
        }
        if (in_array($type, ['account_id', 'group_id','role_id'])) {
            if (is_array($data_id)) {
                foreach ($data_id as $key => $d) {
                    $data[$key]['data_id'] = $d;
                    $data[$key]['data_type'] = $data_type;
                    $data[$key][$type] = $for;
                }
            } else {
                $data[0]['data_id'] = $data_id;
                $data[0]['data_type'] = $data_type;
                $data[0][$type] = $for;
            }
        }
        return $this->insert($data);
    }

    function remove($for,$data_type, $type = 'account_id'){
        if (in_array($type, ['role_id','account_id', 'group_id'])) {
            if(!$data_type){
                $this->where([$type => $for])->delete();
            }else{
                $this->where(['data_type' => $data_type, $type => $for])->delete();
            }
        }
    }

    function getFor($for, $type){
        $access_types = config('rbac.data_permission_type',[]);
        $result = [];
        foreach($access_types as $access_type){
            $result[$access_type] = array();
        }
        $get = $this->where([$type => $for])->get();
        foreach ($get as $dt) {
            $result[$dt->data_type][] = $dt->data_id;
        }
        return $result;
    }

    public function group(){
        return $this->hasOne(Group::class,'id','group_id');
    }
    public function account(){
        return $this->hasOne(Account::class,'id','account_id');
    }
    public function role(){
        return $this->hasOne(Role::class,'id','role_id');
    }
}
