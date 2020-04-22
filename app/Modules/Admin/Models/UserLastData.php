<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class UserLastData extends Model
{
    protected $table = 'users_last_data';
    protected $guarded = ['id', 'created_at', 'updated_at'];

}
