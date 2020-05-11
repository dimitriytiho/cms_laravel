<?php

namespace App\Modules\Admin\Controllers;

use App\Main;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryProductController extends AppController
{
    // Добавить категорию к товару
    public function productAddCategory(Request $request)
    {
        if ($request->isMethod('post') && $request->wantsJson()) {
            $productID = $request->productID ?? null;
            $categoryID = $request->categoryID ?? null;

            if ((int)$productID && (int)$categoryID) {
                if (DB::table('category_product')->insert(['category_id' => (int)$categoryID, 'product_id' => (int)$productID])) {
                    return __("{$this->lang}::s.add_successfully", ['id' => (int)$categoryID]);
                }
            }
        }
        Main::getError('Request No Ajax', __METHOD__);
    }


    // Удалить категорию у товару
    public function productDestroyCategory(Request $request)
    {
        if ($request->isMethod('post') && $request->wantsJson()) {
            $categoryID = $request->categoryID ?? null;

            if ((int)$categoryID) {
                if (DB::table('category_product')->where('category_id', (int)$categoryID)->delete()) {
                    return __("{$this->lang}::s.removed_successfully", ['id' => (int)$categoryID]);
                }
            }
            return 1;
        }
        Main::getError('Request No Ajax', __METHOD__);
    }
}
