<?php

namespace App\Modules\publicly\Page\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $guarded = ['id'];


    public function parentId()
    {
        return $this->belongsTo(self::class);
    }
}
