<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'role';
    protected $fillable = ['name','description','created_at','updated_at','deleted_at'];

    public function user(){
        return $this->hasMany(User::class,'role_id','id');
    }

    public function permission(){
        return $this->belongsToMany(Permission::class,'role_permission','role_id','permission_id');
    }
}
