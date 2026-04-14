<?php

namespace App\Models;
use App\Services\JobLockService;

use Illuminate\Database\Eloquent\Model;

class FinanceNotification extends Model
{
    protected $table = 'finance_notification';
    protected $fillable = ['path','created_by','status','upload_recipient_id'];
}
