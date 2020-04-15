<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenuName extends Model
{
    protected $table = 'menu_name';
    protected $guarded = ['id'];

    // Связь один к одному
    public function menu()
    {
        return $this->hasOne(MenuName::class);
    }
}
