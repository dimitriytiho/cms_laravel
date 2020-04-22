<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class MenuName extends Model
{
    protected $table = 'menu_name';
    protected $guarded = ['id'];

    // Связь один к одному
    public function menu()
    {
        return $this->hasOne(Menu::class);
    }
}
