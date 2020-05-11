<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];


    // Обратная связь один ко многим
    public function user() {
        return $this->belongsTo(User::class);
    }


    // Связь один ко многим (один заказ и много записей в order_product)
    public function orderProduct()
    {
        return $this->hasMany(OrderProduct::class);
    }
}
