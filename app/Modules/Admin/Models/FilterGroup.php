<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class FilterGroup extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать

    // Связь один к многим
    public function filterValue()
    {
        return $this->hasMany(FilterValue::class);
    }
}
