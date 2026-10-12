<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // ① ModelNotFoundException → API用404
        if ($e instanceof ModelNotFoundException) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => 'お問い合わせが見つかりませんでした。',
                ], 404);
            }

            // Webの場合は404ページへ
            return response()->view('errors.404', [], 404);
        }

        // ② ValidationException → APIとWebで分岐
        if ($e instanceof ValidationException) {

            // APIリクエストの場合のみ JSON を返す
            if ($request->is('api/*')) {
                return response()->json([
                    'errors' => $e->errors(),
                ], 422);
            }

            // Webリクエストの場合は Blade に戻す
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        }

        return parent::render($request, $e);
    }
}