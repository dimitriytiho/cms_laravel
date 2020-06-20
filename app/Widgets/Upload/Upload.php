<?php


namespace App\Widgets\Upload;

use App\Helpers\Arr as helpersArr;
use Illuminate\Support\Facades\File;
use Curl\Curl;

class Upload
{
    private $github = 'https://raw.githubusercontent.com/dimitriyyuliy/cms_laravel/master/'; // https://raw.githubusercontent.com/dimitriyyuliy/cms_laravel/master/app/Main.php
    private $allFilesName = 'service/uploadAllFiles.php';
    private $recommendName = 'service/uploadRecommendFiles.php';
    private $excludeName = 'excludeFiles.php';
    private $allFiles;
    private $allServer;


    /*
     * Прежде обновления рекомендуем сдалеть бэкап.
     * Обновление происходить с GitHub.
     */
    private function __construct()
    {
        $path = __DIR__;
        $this->allFiles = File::isFile("{$path}/{$this->allFilesName}") ? require "{$this->allFilesName}" : [];
        $this->allServer = $this->curl($this->github . "app/Widgets/Upload/$this->allFilesName}");
        $this->allServer = $this->allServer ?: [];
        $this->recommend = File::isFile("{$path}/{$this->recommendName}") ? require "{$this->recommendName}" : [];
        $this->exclude = File::isFile("{$path}/{$this->excludeName}") ? require "{$this->excludeName}" : [];

        /*$result = array_diff($a, $this->all); // Возвращает названия файлов, которых нет в основном массиве
        dump($result);*/
    }



    /*
     * По-умолчанию обновляем рекомендованные файлы, кроме исключённых в excludeFiles.php.
     * $all - Если нужно обновить все файлы передать true (Будьте осторожны с этим, сделайте сначала бэкап).
     */
    public static function init($all = null)
    {
        $self = new self();
        $self->run($all);
        return true;
    }


    /*
     * Вызвать это метод, чтобы создать массивы (uploadAllFiles.php и uploadRecommendFiles.php) с файлами, если были созданые новые файлы.
     */
    public static function allFilesToArr()
    {
        $self = new self();
        $pathBase = __DIR__;
        $all = File::isFile("{$pathBase}/uploadAllFiles.php") ? require 'uploadAllFiles.php' : [];
        $recommend = File::isFile("{$pathBase}/uploadRecommendAllFiles.php") ? require 'uploadRecommendAllFiles.php' : [];

        $partAll = $self->makePart($all);
        File::put("{$pathBase}/{$self->allFilesName}", $partAll);

        $partRecommend = $self->makePart($recommend);
        File::put("{$pathBase}/{$self->recommendName}", $partRecommend);
    }

    private function makePart($data)
    {
        $part = "<?php\n";
        $part .= "\n/*\n";
        $part .= " * Файл сформирован программно.\n";
        $part .= " */\n\n";
        $part .= "return [\n\n";

        if ($data) {
            foreach ($data as $v) {
                $path = base_path($v);

                if (File::isFile($path)) {
                    $part .= "\t'{$v}',\n";

                } elseif (File::isDirectory($path)) {

                    $directories = File::allFiles($path);
                    if ($directories) {
                        foreach ($directories as $file) {

                            if ($file->isFile()) {
                                $path = str_replace(base_path() . '/', '', $file->getRealPath());
                                $part .= "\t'{$path}',\n";
                            }
                        }
                    }
                }
            }
        }

        $part .= "\n];\n";
        return $part;
    }



    private function run($all)
    {
        $files = $all ? $this->allFiles : $this->recommend;
        if ($files) {

            // Удалим из массива файлы исключения
            if ($this->exclude) {
                foreach ($this->exclude as $item) {
                    $path = base_path($item);

                    // Если файл
                    if (File::isFile($path)) {
                        $files = helpersArr::unsetValue($item, $files);

                    // Если папка
                    } elseif (File::isDirectory($path)) {

                        $directories = File::allFiles($path);
                        if ($directories) {
                            foreach ($directories as $file) {
                                $path = str_replace(base_path() . '/', '', $file->getRealPath());
                                $files = helpersArr::unsetValue($path, $files);
                            }
                        }
                    }
                }
            }

            foreach ($files as $file) {
                $path = base_path($file);

                // Если нет папки по пути, то создадим её
                $this->makeDirectory($path);

                // Получим файл с GitHub
                $fileGitHub = $this->curl($this->github . $file);

                // Перезаписываем файлы
                if ($fileGitHub) {
                    File::put($path, $fileGitHub);
                }
            }
        }
    }


    /*
     * Если нет папки по пути, то создадим её.
     * $path - полный путь.
     */
    private function makeDirectory($path)
    {
        $file = pathinfo($path)['basename'] ?? null;
        $pathCms = $file ? str_replace('/' . $file, '', $path) : null;

        if (!File::isDirectory($pathCms)) {
            File::makeDirectory($pathCms, 0755, true);
        }
    }


    // Библиотека php-curl-class
    private function curl($url)
    {
        $curl = new Curl();
        $curl->get($url);
        $response = $curl->response;

        if (!$curl->isCurlError() && $response !== '404: Not Found') {
            return $response;
        }
        return false;
    }
}
