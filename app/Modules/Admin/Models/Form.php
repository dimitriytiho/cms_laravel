<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];


    // Обратная связь один ко многим
    public function user() {
        return $this->belongsTo(User::class);
    }
}
