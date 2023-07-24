<?php namespace App\Models\Rbac;

use App\Models\Concerns\HasDataPermission;
use App\Models\Concerns\HasRole;
use App\Models\Concerns\HasRoutePermission;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Group extends Model{

    use HasRole,HasRoutePermission,HasDataPermission;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name','description'
    ];

    protected static function boot(){
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::slug($model->name,'-');
        });
    }

}
