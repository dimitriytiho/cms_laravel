<?php


namespace App\Widgets\Upload;

use App\Helpers\Arr as helpersArr;
use Illuminate\Support\Facades\File;
use Curl\Curl;

class Upload
{
    private $github = 'https://raw.githubusercontent.com/dimitriyyuliy/cms_laravel/master/';
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
     * По-умолчанию обновляем рекомендованные файлы из источника GitHub, кроме исключённых в excludeFiles.php.
     * $all - Если нужно обновить все файлы передать true (Будьте осторожны с этим, сделайте сначала бэкап), необязательный параметр.
     * $excludeFiles - передайте путь к файлам строкой или массивом. например: 'app/Modules/Page', необязательный параметр.
     */
    public static function init($all = null, $excludeFiles = null)
    {
        $self = new self();

        // Выключем сайт на время обновления
        $siteOffFile = config('add.site_off_file');
        if (File::isFile($siteOffFile)) {
            File::replace($siteOffFile, '1');

            // Обновляем файлы
            $self->run($all, $excludeFiles);

            // Включаем сайт
            File::replace($siteOffFile, '');
            return true;
        }
        return false;
    }



    private function run($all, $excludeFiles)
    {
        $files = $all ? $this->allFiles : $this->recommend;
        if ($files) {

            // Добавляем в исключения по-умолчанию
            array_push($this->exclude, 'app/Widgets/Upload');
            array_push($this->exclude, 'app/Modules/Admin/add_routes.php');
            array_push($this->exclude, 'app/Modules/Admin/Nav.php');

            // Добавляем в исключения из параметра
            if (is_string($excludeFiles)) {
                array_push($this->exclude, $excludeFiles);
            } elseif (is_array($excludeFiles)) {
                $this->exclude = array_merge($excludeFiles, $this->exclude);
            }


            // Удалим из массива файлы исключения
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

                } else {

                    $files = helpersArr::unsetValue($item, $files);
                }
            }

            //dump($files);
            if ($files) {
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




    /*
     * Вызвать это метод, чтобы создать массивы (uploadAllFiles.php и uploadRecommendFiles.php) с файлами, если были созданы новые файлы на источнике GitHub.
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
}
