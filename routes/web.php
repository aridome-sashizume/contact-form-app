<?php

use App\Http\Controllers\AdminController;        // 管理画面（PG05）用コントローラー
use App\Http\Controllers\ContactController;      // お問い合わせフォーム（PG01〜PG03）用コントローラー
use App\Http\Controllers\TagController;          // タグ管理（PG05-1）用コントローラー
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Web版（PG01〜PG05）のルーティング定義。
| すべてのルートは RouteServiceProvider により "web" ミドルウェアグループへ割り当てられる。
|
| 仕様書の「お問い合わせフォーム（入力・確認・送信）」「管理画面」「タグ管理」に対応。
| API版（PG10）とは異なり、画面表示とリダイレクトを行うルート群。
|
*/

/**********************************/
/* Site: お問い合わせフォーム入力ページ（PG01）
/**********************************/

// 入力ページを表示
// GET /
Route::get('/', [ContactController::class, 'index']);

// 確認ページへ遷移
// POST /contacts/confirm
Route::post('/contacts/confirm', [ContactController::class, 'confirm']);

/**********************************/
/* Site: お問い合わせフォーム確認ページ（PG02）
/**********************************/

// お問い合わせ保存（送信）
// POST /contacts
Route::post('/contacts', [ContactController::class, 'store']);

// サンクスページ表示
// GET /thanks
Route::get('/thanks', [ContactController::class, 'thanks']);

/**********************************/
/* Fortify 認証ルート（ログイン画面）
/**********************************/

// ログインページ表示
// GET /login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

/**********************************/
/* 認証が必要な管理画面ルート（PG05）
/**********************************/

Route::middleware(['auth'])->group(function () {

    /**********************************/
    /* お問い合わせ管理ページ（PG05）
    /**********************************/

    // 一覧表示
    // GET /admin
    Route::get('/admin', [AdminController::class, 'index']);

    // 詳細表示
    // GET /admin/contacts/{contact}
    Route::get('/admin/contacts/{contact}', [AdminController::class, 'show']);

    // CSV出力
    // GET /contacts/export
    Route::get('/contacts/export', [ContactController::class, 'export']);

    /**********************************/
    /* タグ管理（PG05-1）
    /**********************************/

    // タグ登録
    // POST /admin/tags
    Route::post('/admin/tags', [TagController::class, 'store']);

    // タグ編集ページ表示
    // GET /admin/tags/{tag}/edit
    Route::get('/admin/tags/{tag}/edit', [TagController::class, 'edit']);

    // タグ更新
    // PUT /admin/tags/{tag}
    Route::put('/admin/tags/{tag}', [TagController::class, 'update']);

    // タグ削除
    // DELETE /admin/tags/{tag}
    Route::delete('/admin/tags/{tag}', [TagController::class, 'destroy']);

    /**********************************/
    /* お問い合わせ削除（PG05-2）
    /**********************************/

    // お問い合わせ削除
    // DELETE /admin/contacts/{contact}
    Route::delete('/admin/contacts/{contact}', [AdminController::class, 'destroy']);
});
