<?php namespace App\Models\Rbac;

use App\Models\Concerns\HasDataPermission;
use App\Models\Concerns\HasRole;
use App\Models\Concerns\HasRoutePermission;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Account extends Model{

    use HasUuids,HasRole,HasRoutePermission,HasDataPermission;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','user_id', 'group_id', 'active','accountable_id','accountable_type'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    protected static function boot(){
        parent::boot();
        static::saved(function($model){
            if($model->active == true){
                if($model->user){
                    $model->user->update(['account_id'=>$model->id]);
                }
            }
        });
    }

    public function user(){
        return $this->belongsTo(config('auth.providers.users.model'),'user_id','id');
    }
    
    public function group(){
        return $this->belongsTo(Group::class,'group_id','id');
    }

    public function accountable(){
        return $this->morphTo();
    }

    public function disableOthers(){
        return $this->where('user_id','=',$this->user_id)->where('id','<>',$this->id)->update(['active'=>false]);
    }

}
