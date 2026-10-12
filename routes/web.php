<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController; // AdminControllerをインポート
use App\Http\Controllers\TagController; // ContactControllerをインポート
use Illuminate\Support\Facades\Route; // TagControllerをインポート

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/**********************************/
/* Site:お問い合わせフォーム入力ページ（PG01）
/**********************************/
Route::get('/', [ContactController::class, 'index']);    // お問い合わせページを表示
Route::post('/contacts/confirm', [ContactController::class, 'confirm']); // 確認ページへ遷移

/**********************************/
/* Site:お問い合わせフォーム確認ページ（PG02）
/**********************************/
Route::post('/contacts', [ContactController::class, 'store']); // お問い合わせ保存
Route::get('/thanks', [ContactController::class, 'thanks']);    // サンクスページ

/**********************************/
/* Fortifyの認証ルート
/**********************************/
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

/**********************************/
/* 認証が必要な管理画面ルート
/**********************************/
Route::middleware(['auth'])->group(function () {

    /**********************************/
    /* Site:お問い合わせ管理ページ（PG05）
    /**********************************/
    Route::get('/admin', [AdminController::class, 'index']); // 一覧
    Route::get('/admin/contacts/{contact}', [AdminController::class, 'show']); // 詳細
    Route::get('/contacts/export', [ContactController::class, 'export']); // CSV

    /**********************************/
    /* タグ管理
    /**********************************/
    Route::post('/admin/tags', [TagController::class, 'store']); // 登録
    Route::get('/admin/tags/{tag}/edit', [TagController::class, 'edit']); // 編集ページ
    Route::put('/admin/tags/{tag}', [TagController::class, 'update']); // 更新
    Route::delete('/admin/tags/{tag}', [TagController::class, 'destroy']); // 削除

    /**********************************/
    /* お問い合わせ削除（PG05-2）
    /**********************************/
    Route::delete('/admin/contacts/{contact}', [AdminController::class, 'destroy']); // 削除
});
