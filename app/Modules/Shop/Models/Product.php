<?php

namespace App\Modules\Shop\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать
    //protected $fillable = ['title', 'price', 'description'];  // Разрешается редактировать


    // Связь многие ко многим
    public function category()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function orderProduct()
    {
        return $this->hasMany(OrderProduct::class, 'product_id');
    }
}
