<?php

namespace App\Http\Controllers\Admin;

use App\App;
use App\Helpers\Admin\Img;
use App\Helpers\Admin\Slug;
use App\Helpers\File;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImgUploadController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->class = str_replace('Controller', '', class_basename(__CLASS__));
    }


    public function upload(Request $request)
    {
        if ($request->isMethod('post') && $request->wantsJson()) {

            /*$destinationPath = 'files/';
            // Create directory if not exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }*/
            /*Storage::disk('local')->putFileAs(
                'files/' . $filename,
                '$uploadedFile',
                '$filename'
            );*/
            //$path = $request->photo->storeAs('images', 'filename.jpg', 's3');

            $requestAll = $request->all();
            if ($requestAll && is_array($requestAll)) {
                $class = $request->input('class');
                $route = Str::lower($class);
                $table = $request->input('table');

                // ID элемента, для которого картинка
                $imgUploadID = (int)$request->input('imgUploadID');

                // Начальная часть названия передаваемой картинки
                $requestName = $request->input('name');

                // Если передаём одну картинку, то передать 1. Если передаём множество картинок, то передать цифру больше 1
                $maxFiles = (int)$request->input('maxFiles');

                // Множественная загрузка
                if ($maxFiles > 1) {
                    $class = "{$class}Gallery";
                    $table = "{$route}_gallery";
                }
            }

            if ($class && $imgUploadID && $request->hasFile('file') && Schema::hasTable($table)) {

                $img = $request->file('file');
                $size = $img->getSize();
                $ext = '.' . $img->extension();
                $extValidText = '.jpg, .jpeg, .png, .gif';
                $extValid = explode(', ', $extValidText);
                //$path = $img->path();
                //$originName = $img->getClientOriginalName();

                // Если файл больше 2mb
                $maxSize = 2097152;
                $maxSizeRound = (int)round($maxSize / 1000000);
                if ($size > $maxSize) {
                    return response()->json(['answer' => __('s.maximum_file_size', ['size' => $maxSizeRound])]);
                }

                // Если разрешение файла не в разрешённых
                if (!in_array($ext, $extValid)) {
                    return response()->json(['answer' => __('s.allowed_to_upload_files') . $extValidText]);
                }

                // Путь на сервере для картинки
                $imgSavePath = config("admin.imgPath{$class}") . '/';

                // Дата картинки
                $date = Slug::exceptionsName(d(time(), config('admin.date_format')));

                // Имя картинки
                //$imgName = "{$requestName}_" . Str::lower(Str::random(3)) . "_{$date}{$ext}";
                $imgName = File::nameCount($imgSavePath, $requestName, $ext, $date);


                // Перемещаем картинку в нужное место
                $img->move($imgSavePath, $imgName);

                // Путь URL для новой картинки
                $imgNewPaht = config("admin.img{$class}") . "/{$imgName}";

                // Одиночная загрузка
                if ($maxFiles <= 1) {

                    // Получаем данные из БД
                    $oldSql = DB::table($table)->find($imgUploadID);

                    // Удаляем старый файл из папки на сервере, кроме картинки по-умолчанию
                    $oldImg = $oldSql->img ?? null;
                    Img::deleteImg($oldImg, config("admin.img{$class}Default"));

                    // Сохраняем картинку в БД для одиночной загрузки
                    $sql = DB::table($table)->where('id', $imgUploadID)->update(['img' => $imgNewPaht]);

                // Множественная загрузка
                } else {

                    // Сохраняем картинки в БД
                    $insertData = [
                        ["{$route}_id" => $imgUploadID, 'img' => $imgNewPaht],
                    ];
                    $sql = DB::table($table)->insert($insertData);
                }

                if ($sql) {

                    // Ответ для JS
                    $res = [
                        'answer' => 'success',
                        'name' => $imgName,
                        'href' => $imgNewPaht,
                        //'test' => $imgName,
                    ];
                    return response()->json($res);
                }


                // Если не сохранено в БД, то удалим файл
                if (is_file($imgSavePath . $imgName)) {
                    \Illuminate\Support\Facades\File::delete(($imgSavePath . $imgName));
                }
            }
            return response()->json(['answer' => __('s.whoops')]);
        }
        App::getError('Request No Post', __METHOD__);
    }


    public function remove(Request $request)
    {
        if ($request->isMethod('post') && $request->wantsJson()) {
            $table = $request->table ?? null;
            $class = $request->class ?? null;
            $route = Str::lower($class);
            $img = $request->img ?? null;
            $maxFiles = $request->maxFiles ?? null;
            $defaultImg = config("admin.img{$class}Default");

            // Множественная загрузка
            if ($maxFiles > 1) {
                $class = "{$class}Gallery";
                $table = "{$route}_gallery";
            }

            if ($table && $class && $img && $maxFiles && $img !== $defaultImg && Schema::hasTable($table)) {

                // Если одиночная загрузка картинок, то заменим название на название по-умолчанию
                if ($maxFiles <= 1) {

                    // Если меняется картинка пользователя, то заменим её в объекте auth
                    /*if ($class === 'User') {
                        auth()->user()->update(['img' => $defaultImg]);
                    }*/

                    // Обновим ячейку в БД
                    $sql = DB::table($table)->where('img', $img)->update(['img' => $defaultImg]);


                // Если множественная загрузка картинок, то удалим ряд записи
                } else {
                    $sql = DB::table($table)->where('img', $img)->delete();
                }


                if ($sql) {

                    // Удалим картинку с сервера
                    Img::deleteImg($img);

                    // Ответ JS
                    return __('s.removed_successfully_name', ['name' => $img]);
                }

            } else {
                return __('s.whoops');
            }
        }
        App::getError('Request No Ajax', __METHOD__);
    }
}
