<?php

namespace App\Modules\Admin\Controllers;

use App\App;
use App\Modules\Admin\Helpers\Commands;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class AdditionallyController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->class = str_replace('Controller', '', class_basename(__CLASS__));
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
                    session()->put('success', __('a.cache_deleted'));
                    return redirect()->route("admin.{$t}");

                case 'views':
                    $res = Commands::getCommand('view:clear');
                    $res ? session()->put('success', $res) : session()->put('error', __('s.something_went_wrong'));
                    return redirect()->route("admin.{$t}");

                case 'routes':
                    $res1 = Commands::getCommand('route:clear');
                    $res2 = Commands::getCommand('route:cache');
                    $res1 && $res2 ? session()->put('success', "{$res1}\n{$res2}") : session()->put('error', __('s.something_went_wrong'));
                    return redirect()->route("admin.{$t}");

                case 'config':
                    $res1 = Commands::getCommand('config:clear');
                    $res2 = Commands::getCommand('optimize');
                    $res1 && $res2 ? session()->put('success', "{$res1}\n{$res2}") : session()->put('error', __('s.something_went_wrong'));
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

                session()->put('success', __('a.completed_successfully'));
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
            App::getError('Request', __METHOD__, null);
            session()->put('error', __('s.something_went_wrong'));
            return redirect()->route("admin.{$t}");
        }

        App::viewExists("{$t}.{$f}", __METHOD__);
        $this->setMeta(__('a.' . Str::ucfirst($t)));
        return view("{$t}.{$f}", compact('backupDisabled'));
    }


    public function files(Request $request) {
        $f = __FUNCTION__;
        $t = $request->segment(2);
        App::viewExists("{$t}.{$f}", __METHOD__);

        $this->setMeta(__('a.' . Str::ucfirst($t)));
        return view("{$t}.{$f}");
    }
}
