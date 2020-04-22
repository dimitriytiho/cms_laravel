<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $guarded = ['id'];


    public function parentId()
    {
        return $this->belongsTo(self::class);
    }
}
