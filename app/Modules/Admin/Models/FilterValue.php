<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class FilterValue extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать

    // Связь один к многим обратная
    public function filterGroup()
    {
        return $this->belongsTo(FilterGroup::class);
    }


    // Связь многие ко многим
    public function products()
    {
        return $this->belongsToMany(Product::class, 'filter_products');
    }
}
