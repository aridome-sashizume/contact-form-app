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
        // レスポンスエラー（IDが存在しない場合）
        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'error' => 'お問い合わせが見つかりませんでした。'
            ], 404);
        }

        // レスポンスエラー（バリデーションエラー）
        if ($e instanceof ValidationException) {
            return response()->json([
                'errors'  => $e->errors(), //リクエストで設定したエラーメッセージを出力
            ], 422);
        }

        return parent::render($request, $e);
    }
}