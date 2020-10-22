<?php

namespace App\Modules\Admin\Controllers;

use App\Models\Main;
use App\Modules\Admin\Helpers\Commands;
use App\Widgets\Upload\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class AdditionallyController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        View::share(compact('class','c'));
    }


    public function index(Request $request) {
        $f = __FUNCTION__;
        $t = $request->segment(2);

        // Работа с Кэшем
        $cache = $request->query('cache');
        if ($cache) {
            switch ($cache) {
                case 'db':
                    cache()->flush();
                    session()->flash('success', __("{$this->lang}::a.cache_deleted"));
                    return redirect()->route("admin.{$t}");

                case 'views':
                    $res = Commands::getCommand('view:clear');
                    $res ? session()->flash('success', $res) : session()->flash('error', __("{$this->lang}::s.something_went_wrong"));
                    return redirect()->route("admin.{$t}");

                case 'routes':
                    $res1 = Commands::getCommand('route:clear');
                    $res1 ? session()->flash('success', $res1) : session()->flash('error', __("{$this->lang}::s.something_went_wrong"));
                    return redirect()->route("admin.{$t}");

                case 'config':
                    $res1 = Commands::getCommand('config:clear');
                    $res1 ? session()->flash('success', $res1) : session()->flash('error', __("{$this->lang}::s.something_went_wrong"));
                    return redirect()->route("admin.{$t}");
            }
        }

        // Работа с Backup
        $backup = $request->query('backup');
        $backupDisabled = null;
        if ($backup) {

            // После backup заброкируем кнопку Выполнить
            if ($backup === 'disabled') {
                $backupDisabled = ' disabled';
            }

            if ($backup === 'run') {
                Artisan::call('backup:clean');
                Artisan::call('backup:run');

                return redirect()
                    ->route('admin.additionally', 'backup=disabled')
                    ->with('success', __("{$this->lang}::a.completed_successfully"));
            }
        }

        // Работа с Обновлением
        $update = $request->query('update');
        $updateDisabled = null;
        if ($update) {

            // После backup заброкируем кнопку Выполнить
            if ($update === 'disabled') {
                $updateDisabled = ' disabled';
            }

            if ($update === 'run') {

                // Обновляем файлы CMS, кроме данного файла
                Upload::init(false);

                return redirect()
                    ->route('admin.additionally', 'update=disabled')
                    ->with('success', __("{$this->lang}::a.completed_successfully"));
            }
        }

        // Работа с командами
        if ($request->isMethod('post')) {
            $command = $request->command ?? null;
            if ($command) {
                $command = ltrim($command, 'php artisan ');
                $command = trim($command);
                $res = Commands::getCommand($command);
                if ($res) {
                    return redirect()
                        ->route("admin.{$t}")
                        ->with('success', $res);
                }
            }

            // Сообщение об ошибке
            Main::getError('Request', __METHOD__, null);
            return redirect()
                ->route("admin.{$t}")
                ->with('error', __("{$this->lang}::s.something_went_wrong"));
        }

        Main::viewExists("{$this->viewPath}.{$t}.{$f}", __METHOD__);
        $this->setMeta(__("{$this->lang}::a." . Str::ucfirst($t)));
        return view("{$this->viewPath}.{$t}.{$f}", compact('backupDisabled', 'updateDisabled'));
    }


    public function files(Request $request) {
        $f = __FUNCTION__;
        $t = $request->segment(2);
        Main::viewExists("{$this->viewPath}.{$t}.{$f}", __METHOD__);

        $this->setMeta(__("{$this->lang}::a." . Str::ucfirst($f)));
        return view("{$this->viewPath}.{$t}.{$f}");
    }


    /*public function dbBuilder(Request $request) {
        $f = __FUNCTION__;
        $t = $request->segment(2);
        $view = Str::snake("{$t}.{$f}");
        Main::viewExists($view, __METHOD__);

        $this->setMeta(__("{$this->lang}::a." . Str::ucfirst($t)));
        return view($view);
    }*/
}
