<?php

namespace App\Exceptions;

use App\App;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;

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
        $lang = lang();

        // Добавляем код
        if ($this->isHttpException($exception)) {
            $code = $exception->getStatusCode();

            // Ошибки 401
            if ($code == 401) {
                $title = __("{$lang}::s.unauthorized_error");
                $message = __("{$lang}::s.whoops_no_auth");
                App::getError($title, __METHOD__, false, 'critical');
                $status = 401; // HTTP/1.0 401 Unauthorized

                // Ошибки 500
            } elseif ($code == 500 || $code == 419 || $code == 422) {
                $title = __("{$lang}::s.unauthorized_error");
                $message = __("{$lang}::s.whoops_no_server");
                App::getError($title, __METHOD__, false, 'critical');
                $status = 500; // 500 Internal Server Error

                // Ошибки 404 и прочии
            } else {
                $title = __("{$lang}::s.page_not_found");
                $message = __("{$lang}::s.whoops_no_page");
                App::getError($title, __METHOD__, false, 'critical');
                $status = 404; // HTTP/1.1 404 Not Found
            }

            App::setMeta($title);
            $modulesPath = config('modules.path');
            $viewPath = config('modules.views');
            $view = 'views.errors.404';

            // Переопределим путь к видам
            view()->getFinder()->setPaths($modulesPath);

            if (view()->exists($view)) {
                return response()->view('views.errors.404', compact('title', 'message', 'viewPath', 'lang'), $status);

            } else {
                return redirect('/error.php');
            }
        }


        return parent::render($request, $exception);
    }
}
