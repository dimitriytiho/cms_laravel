<?php

namespace App\Modules\Admin\Controllers;

use App\Main;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryProductController extends AppController
{
    private $belongsId = 'category_id';


    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $model = $this->model = '\App\\Modules\\Admin\\Models\\' . $this->class;
        $this->table = with(new $model)->getTable(); // Получаем название таблицы
    }

    // Добавить категорию к товару
    public function productAdd(Request $request)
    {
        if ($request->isMethod('post') && $request->wantsJson()) {
            $productId = $request->productId ?? null;
            $belongsId = $request->belongsId ?? null;

            if ((int)$productId && (int)$belongsId) {
                if (DB::table($this->table)->insert([$this->belongsId => (int)$belongsId, 'product_id' => (int)$productId])) {
                    return __("{$this->lang}::s.add_successfully", ['id' => (int)$belongsId]);
                }
            }
        }
        Main::getError('Request No Ajax', __METHOD__);
    }


    // Удалить категорию у товару
    public function productDestroy(Request $request)
    {
        if ($request->isMethod('post') && $request->wantsJson()) {
            $productId = $request->productId ?? null;
            $belongsId = $request->belongsId ?? null;

            if ((int)$productId && (int)$belongsId) {
                if (DB::table($this->table)->where($this->belongsId, (int)$belongsId)->where('product_id', (int)$productId)->delete()) {
                    return __("{$this->lang}::s.removed_successfully", ['id' => (int)$productId]);
                }
            }
            return 1;
        }
        Main::getError('Request No Ajax', __METHOD__);
    }
}
