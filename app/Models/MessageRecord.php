<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageRecord extends Model {

    protected $fillable = [
        'message_id',
        'user_id',
        'username',
        'msg_body',
        'group_id',
        'group_name',
    ];
}
