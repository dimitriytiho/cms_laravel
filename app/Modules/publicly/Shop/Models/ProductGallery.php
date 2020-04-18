<?php

namespace App\Modules\publicly\Shop\Models;

use Illuminate\Database\Eloquent\Model;

class ProductGallery extends Model
{
    protected $table = 'product_gallery';


    // Обратная связь один к одному (один id и один товар)
    public function product()
    {
        return $this->belongsToMany(Product::class, 'product_gallery');
    }
}
