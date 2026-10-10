<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController; // AdminControllerをインポート

use App\Http\Controllers\ContactController; // ContactControllerをインポート
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
*AdminControllerのindexメソッドを呼び出すルートを定義
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
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
