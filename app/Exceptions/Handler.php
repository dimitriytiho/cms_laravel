<?php

namespace App\Exceptions;

use App\Helpers\Breadcrumbs;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Main;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Добавляем код
        $lang = lang();
        if ($this->isHttpException($exception)) {
            $code = $exception->getStatusCode();

            $title = __("{$lang}::s.page_not_found");
            $message = __("{$lang}::s.whoops_no_page");
            $status = 404; // HTTP/1.1 404 Not Found

            Main::setMeta($title);
            $modulesPath = config('modules.path');
            $viewPath = config('modules.views');
            $view = 'views.errors.404';

            // Переопределим путь к видам
            view()->getFinder()->setPaths($modulesPath);

            // Хлебные крошки
            $breadcrumbs = new Breadcrumbs();
            $breadcrumbs = $breadcrumbs
                ->end(['not_found' => $title])
                ->get();

            if (view()->exists($view)) {
                return response()->view('views.errors.404', compact('title', 'message', 'viewPath', 'lang', 'breadcrumbs'), $status);

            } else {
                return redirect('/error.php');
            }
        }

        return parent::render($request, $exception);
    }
}
