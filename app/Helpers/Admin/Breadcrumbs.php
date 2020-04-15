<?php


namespace App\Helpers\Admin;


class Breadcrumbs
{
    public static function breadcrumbs($arr, $currentRoute) {
        if ($arr && is_array($arr) && $currentRoute) {
            $breadcrumbs_array = self::getParts($arr, $currentRoute['id']);
            return self::getBreadcrumbs($breadcrumbs_array);
        }
        return false;
    }


    public static function getParts($values, $id)
    {
        if (!$id) return false;
        $breadcrumbs = [];
        if ($values) {
            foreach ($values as $k => $v) {
                if (isset($values[$id])) {
                    $breadcrumbs[$values[$id]['slug']] = $values[$id]['title'];

                    if ($id !== $values[$id]['parent_id']) {
                        $id = $values[$id]['parent_id'];
                    } else break;

                } else break;
            }
        }
        return array_reverse($breadcrumbs, true);
    }


    public static function getBreadcrumbs($breadcrumbs_array) {
        if ($breadcrumbs_array) {
            $end = end($breadcrumbs_array);
            array_pop($breadcrumbs_array);
            $breadcrumbs = "<a href='" . route('admin.main') . "' class='btn btn-outline-primary btn-pulse'><i aria-hidden='true' class='material-icons'>dashboard</i></a>";
            if ($breadcrumbs_array) {
                foreach ($breadcrumbs_array as $slug => $title) {
                    $title = __("a.$title");
                    $breadcrumbs .= "<a href='$slug' class='btn btn-outline-primary btn-pulse'>$title</a>";
                }
            }
            $breadcrumbs .= "<a class='btn btn-outline-primary disabled'>$end</a>";
            return $breadcrumbs;
        }
        return false;
    }
}
