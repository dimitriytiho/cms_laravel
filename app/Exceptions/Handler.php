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

        // Добавляем код
        if ($this->isHttpException($exception)) {
            $code = $exception->getStatusCode();

            // Ошибки 401
            if ($code == 401) {
                $title = __('s.unauthorized_error');
                $message = __('s.whoops_no_auth');
                Log::critical("$title. " . App::dataUser(true) . "Error in " . __METHOD__);
                $status = 401; // HTTP/1.0 401 Unauthorized

                // Ошибки 500
            } elseif ($code == 500 || $code == 419 || $code == 422) {
                $title = __('s.unauthorized_error');
                $message = __('s.whoops_no_server');
                Log::critical("$title. " . App::dataUser(true) . "Error in " . __METHOD__);
                $status = 500; // 500 Internal Server Error

                // Ошибки 404 и прочии
            } else {
                $title = __('s.page_not_found');
                $message = __('s.whoops_no_page');
                Log::info("$title. " . App::dataUser(true) . "Error in " . __METHOD__);
                $status = 404; // HTTP/1.1 404 Not Found
            }

            App::setMeta($title);
            return response()->view('views.errors.404', compact('title', 'message'), $status);
        }


        return parent::render($request, $exception);
    }
}
