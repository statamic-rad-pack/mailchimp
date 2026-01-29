<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler;
use Throwable;

class ExceptionHandler extends Handler
{
    protected $dontReport = [
    ];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }
}
