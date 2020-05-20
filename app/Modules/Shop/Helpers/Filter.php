<?php


namespace App\Modules\Shop\Helpers;

use Illuminate\Support\Facades\DB;
use App\Widgets\Filter\Filter as widgetsFilter;

class Filter
{
    // Получаем из get filter только цифры и запятые
    public static function getFilter()
    {
        $filter = request()->query('filter');
        if ($filter) {

            // Берём только цифры и запятые из get
            $filter = preg_replace('#[^\d,]+#', '', $filter);
            $filter = trim($filter, ',');
        }
        return $filter;
    }


    public static function getCountGroups($filter)
    {
        $data = [];
        if ($filter) {
            $filters = explode(',', $filter);
            $filtersAll = widgetsFilter::getFilters();

            if ($filtersAll) {
                foreach ($filtersAll as $key => $filterValue) {
                    if (in_array($filterValue->id, $filters)) {
                        $data[$filterValue->parent_id] = $filterValue->parent_id;
                    }
                }
            }

        }
        return count($data);
    }
}
