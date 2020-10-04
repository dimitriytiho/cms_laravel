<?php

namespace App\Modules\Auth\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // Связь один ко многим
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
