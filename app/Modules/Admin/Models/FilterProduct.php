<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class FilterProduct extends Model
{
    // Связь один ко многим
    public function filter_values()
    {
        return $this->belongsTo(FilterValue::class);
    }
}
