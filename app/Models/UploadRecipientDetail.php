<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadRecipientDetail extends Model
{
    protected $table = 'upload_recipient_detail';
    protected $fillable = [
        'upload_recipient_id', 'phone','bank_account','pol_num','name','created_at','updated_at','amount','bank_br_code','bank_name','status','product_name'
    ];
}
