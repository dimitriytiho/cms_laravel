<?php

namespace App\Modules\Admin\Controllers;

use App\Main;
use App\Modules\Admin\Helpers\Commands;
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
                    session()->put('success', __("{$this->lang}::a.cache_deleted"));
                    return redirect()->route("admin.{$t}");

                case 'views':
                    $res = Commands::getCommand('view:clear');
                    $res ? session()->put('success', $res) : session()->put('error', __("{$this->lang}::s.something_went_wrong"));
                    return redirect()->route("admin.{$t}");

                case 'routes':
                    $res1 = Commands::getCommand('route:clear');
                    $res1 ? session()->put('success', $res1) : session()->put('error', __("{$this->lang}::s.something_went_wrong"));
                    return redirect()->route("admin.{$t}");

                case 'config':
                    $res1 = Commands::getCommand('config:clear');
                    $res1 ? session()->put('success', $res1) : session()->put('error', __("{$this->lang}::s.something_went_wrong"));
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

                session()->put('success', __("{$this->lang}::a.completed_successfully"));
                return redirect()->route('admin.additionally', 'backup=disabled');
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
                    session()->put('success', $res);
                    return redirect()->route("admin.{$t}");
                }
            }

            // Сообщение об ошибке
            Main::getError('Request', __METHOD__, null);
            session()->put('error', __("{$this->lang}::s.something_went_wrong"));
            return redirect()->route("admin.{$t}");
        }

        Main::viewExists("{$t}.{$f}", __METHOD__);
        $this->setMeta(__("{$this->lang}::a." . Str::ucfirst($t)));
        return view("{$t}.{$f}", compact('backupDisabled'));
    }


    public function files(Request $request) {
        $f = __FUNCTION__;
        $t = $request->segment(2);
        Main::viewExists("{$t}.{$f}", __METHOD__);

        $this->setMeta(__("{$this->lang}::a." . Str::ucfirst($t)));
        return view("{$t}.{$f}");
    }
}
