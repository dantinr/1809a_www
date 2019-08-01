<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    //
    protected $primaryKey = 'uid';
    public $table = 'p_users';
    public $timestamps = false;
}
