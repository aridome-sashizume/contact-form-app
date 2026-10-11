<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController; // AdminControllerをインポート

use App\Http\Controllers\ContactController; // ContactControllerをインポート
use App\Http\Controllers\TagController; //TagControllerをインポート

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


/********************************** */
/* Fortifyの認証ルートを有効化するためのコード
/********************************** */
// ルートの定義
Route::get('/', function () {
    return redirect()->route('login');
});

/*
*認証が必要なルートをグループ化　
*AdminController、ContactController、TagControllerのメソッドを呼び出すルートを定義
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
    Route::get(' /admin/contacts/{contact}', [AdminController::class,'show']);
    Route::get('/contacts/export',[ContactController::class, 'export']);
    Route::post('/admin/tags', [TagController::class, 'store']);
    Route::get('/admin/tags/{tag}/edit',[TagController::class, 'edit']);
    Route::put('/admin/tags/{tag}', [TagController::class,'update']);
    Route::delete('/admin/contacts/{contact}', [AdminController::class,'destroy']);
});




/********************************** */
/*　ContactControllerのルート定義
/********************************** */
// ルートの定義
Route::get('/', [ContactController::class, 'index']);    

// お問い合わせフォーム確認ページのルートを定義
Route::post('/contacts/confirm', [ContactController::class, 'confirm']); 

// お問い合わせ内容を保存するルートを定義
Route::post('/contacts', [ContactController::class, 'store']); 

// サンクスページへのリダイレクトするためのルートを定義
Route::get('/thanks',[ContactController::class, 'thanks']);
