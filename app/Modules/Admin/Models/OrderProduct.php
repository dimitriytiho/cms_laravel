<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $table = 'order_product';
    protected $guarded = ['id']; // Запрещается редактировать


    public function order()
    {
        return $this->belongsTo('App\Order');
    }


    // Обратная связь один к одному (один id и один товар)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
