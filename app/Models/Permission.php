<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    protected $table = 'permission';
    protected $fillable = ['name','slug','module_id','menu_id'];

    public function role(){
        return $this->belongsToMany(Role::class,'role_permission','permission_id','role_id');
    }
}
