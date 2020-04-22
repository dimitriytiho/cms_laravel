<?php

namespace App\Console\Commands;

use App\Helpers\Upload;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class makeModule extends Command
{
    protected $namespace;
    protected $path;
    protected $name;
    protected $files;
    protected $folder;
    protected $modulesNamespace;
    protected $modulesPath;
    protected $model;


    /**
     * The name and signature of the console command.
     *
     * @var string
     *
     *
     * Сначало укажите модуль в конфиге /config/modules.php массив modules.
     * Этой командой создаётся модуль по-умолчанию.
     * Пример вызова команды:
     * php artisan make:module Blog --all
     *
     * Без опций создастся папка без модели и миграции.
     * --all - все опции включаются.
     * --model - создать модель.
     * --migration - создать миграцию.
     */
    protected $signature = 'make:module {name}
        {--all : All items}
        {--model : Only model}
        {--migration : Only migration}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new Module';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
        $this->modulesNamespace = config('modules.namespace');
        $this->modulesPath = config('modules.path');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->path = trim($this->argument('name'));
        $this->namespace = str_replace('/', '\\', $this->path);
        $this->name = class_basename($this->namespace);
        $this->folder = "{$this->modulesPath}/{$this->path}";

        // Если в команде есть all, то все остальные параметры в true
        if ($this->option('all')) {
            $this->input->setOption('migration', true);
            $this->input->setOption('model', true);
        }

        // Если в команде есть model, то model в true
        if ($this->option('model')) {

            // Создадим модель
            $this->createModel();
        } else {

            // Создадим папку с Модулем
            $this->files->makeDirectory($this->folder, 0777, true, true);

            // Создадим папку Models
            $this->files->makeDirectory("{$this->folder}/Models", 0777, true, true);
        }

        // Если в команде есть migration, то migration в true
        if ($this->option('migration')) {

            // Создадим миграцию
            $this->createMigration();
        }

        // Создадим иерархию папки и файлы
        $this->hierarchy();

        // Обновим файл webpack.mix.js
        Upload::resourceInit();

        // Создадим контроллеры и файл роутов
        $this->createControllersRoutes();
    }



    private function createModel()
    {
        try {

            $model = $this->model = Str::singular($this->name);
            $this->call('make:model', [
                'name' => "{$this->modulesNamespace}\\{$this->namespace}\\Models\\{$model}",
            ]);

        } catch (\Exception $e) {
            $e->getMessage();
        }
    }


    private function createMigration()
    {
        try {

            $table = Str::plural(Str::snake($this->name));
            $this->call('make:migration', [
                'name' => "create_{$table}",
                '--create' => $table,
            ]);

        } catch (\Exception $e) {
            $e->getMessage();
        }
    }


    private function hierarchy()
    {
        // Данные для папок
        $controllers = "{$this->folder}/Controllers";
        $js = "{$this->folder}/js";
        $sass = "{$this->folder}/sass";
        $views = "{$this->folder}/views";

        // Создадим папки
        $this->files->makeDirectory($controllers, 0777, true, true);
        $this->files->makeDirectory($js, 0777, true, true);
        $this->files->makeDirectory($sass, 0777, true, true);
        $this->files->makeDirectory($views, 0777, true, true);

        // Выводим информацию в консоль
        $this->info('Folders created successfully.');

        // Данные для файлов
        $name = Str::lower($this->name);
        $jsIndex = "{$this->folder}/js/index.js";
        $jsScript = "{$this->folder}/js/script.js";
        $sassIndex = "{$this->folder}/sass/index.scss";
        $sassStyle = "{$this->folder}/sass/style.scss";
        $viewIndex = "{$this->folder}/views/{$name}_index.blade.php";
        $viewShow = "{$this->folder}/views/{$name}_show.blade.php";

        $sassStyleContent = "@import \"../../sass/config/params\";
@import \"../../sass/config/mixins\";
@import \"../../sass/config/blocks\";\n";
        $viewIndexContent = $this->getTemplateStub('view.index');
        $viewShowContent = $this->getTemplateStub('view.show');

        // Создадим файлы
        $this->files->put($jsIndex, "import './script'\n");
        $this->files->put($jsScript, "\n");
        $this->files->put($sassIndex, "@import \"style\";\n");
        $this->files->put($sassStyle, $sassStyleContent);
        $this->files->put($viewIndex, $viewIndexContent);
        $this->files->put($viewShow, $viewShowContent);

        // Выводим информацию в консоль
        $this->info('Files created successfully.');
    }


    private function createControllersRoutes()
    {
        $controller = Str::studly($this->name);
        $modelStub = $this->model ? 'Model' : null;

        $appController = "{$this->folder}/Controllers/AppController.php";
        $nameController = "{$this->folder}/Controllers/{$controller}Controller.php";

        // Получаем шаблоны контроллеров
        $stubApp = $this->getTemplateStub("AppController{$modelStub}");
        $stubName = $this->getTemplateStub("NameController{$modelStub}", $controller);

        // Сохраняем контроллеры
        $this->files->put($appController, $stubApp);
        $this->files->put($nameController, $stubName);

        // Выводим информацию в консоль
        $this->info('Controllers created successfully.');

        // Созданим файл роутов
        $routes = "{$this->folder}/routes.php";
        $routesContent = $this->getTemplateStub('routes.web', $nameController);
        $this->files->put($routes, $routesContent);

        $this->info("Module {$this->name} created successfully.");
    }


    private function getTemplateStub($stubName, $controllerName = 'App')
    {
        if ($stubName) {
            $stub = "{$this->modulesPath}/stubs/{$stubName}.stub";

            if (is_file($stub)) {
                $namespace = "{$this->modulesNamespace}\\{$this->namespace}\\Controllers";
                $namespaceFull = str_replace('\\', '\\\\', $namespace);

                // Получаем содержимое файла
                $stub = $this->files->get($stub);

                // Заменяем шаблоные названия
                $stub = str_replace(
                    [
                        'TestNamespace',
                        'TestController',
                        'ModuleName',
                        'FullNamespace',
                        'ModuleLowerName',
                    ],
                    [
                        $namespace,
                        "{$controllerName}Controller",
                        $this->name,
                        $namespaceFull,
                        Str::snake($this->name),
                    ],
                    $stub
                );
                return $stub;
            }
        }
        return false;
    }
}
