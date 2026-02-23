<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class UploadRecipient extends Model
{
    protected $table = 'upload_recipient';
    protected $fillable = [
        'path', 'total_recipient','total_amount','name','notes','status','exception','created_at','updated_at','scheduled_at','approved_at',
        'created_by','approved_by'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::id();
            $model->updated_by = Auth::id();
        });
    }   

     public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
