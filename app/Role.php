<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // Связь один ко многим
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
