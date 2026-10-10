<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
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
