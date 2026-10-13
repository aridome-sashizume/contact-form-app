<?php

use App\Http\Controllers\Api\V1\ContactController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| 公開API（/api/v1/...）のルーティング定義。
| すべてのルートは RouteServiceProvider により "api" ミドルウェアグループへ割り当てられる。
| 仕様書の「お問い合わせAPI（一覧・詳細・作成・更新・削除）」に対応。
|
*/

// 公開APIのルーティング
Route::prefix('v1')->group(function () {

    // お問い合わせ一覧を取得（検索 + ページネーション）
    // GET /api/v1/contacts
    Route::get('/contacts', [ContactController::class, 'index']);

    // お問い合わせ詳細を取得
    // GET /api/v1/contacts/{contact}
    Route::get('/contacts/{contact}', [ContactController::class, 'show']);

    // お問い合わせを新規作成
    // POST /api/v1/contacts
    Route::post('/contacts', [ContactController::class, 'store']);

    // お問い合わせを更新
    // PUT /api/v1/contacts/{contact}
    Route::put('/contacts/{contact}', [ContactController::class, 'update']);

    // お問い合わせを削除
    // DELETE /api/v1/contacts/{contact}
    Route::delete('/contacts/{contact}', [ContactController::class, 'destroy']);
});
