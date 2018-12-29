<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMessageLookLog extends Model
{
    //
    protected $dateFormat = 'U';
    protected $fillable = ['message_id','u_id'];
}
