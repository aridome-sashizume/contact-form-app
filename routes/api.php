<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\ContactController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//公開APIのルーティング
Route::prefix('v1')->group(function () {
    Route::get('/contacts', [ContactController::class, 'index']); //お問い合わせ一覧を取得する
    Route::get('/contacts/{contact}', [ContactController::class, 'show']); //お問い合わせ詳細を取得する
    Route::post('/contacts', [ContactController::class,'store']); //お問い合わせを作成する
    Route::Put('/contacts/{contact}', [ContactController::class,'update']); //お問い合わせを更新する
    Route::delete('/contacts/{contact}', [ContactController::class,'destroy']); //お問い合わせを削除する
});