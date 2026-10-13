<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * ログイン後のデフォルト遷移先。
     * 管理画面の利用者はログイン後に必ず管理トップ（/admin）へ移動するため、HOME を /admin に設定している。
     *
     * @var string
     */
    public const HOME = '/admin';

    /**
     * ルート設定（API・Web）の初期化処理。
     * ・API ルートはレート制限を適用し、過剰アクセスを防止する
     * ・Web ルートはセッションや CSRF を利用するため web ミドルウェアを適用する
     * これらを分離することで、セキュリティとパフォーマンスを最適化している。
     */
    public function boot(): void
    {
        // API のアクセス制限（1分間に60回まで）
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // API と Web のルート定義を分離
        $this->routes(function () {
            // API ルート：ステートレス・JSONレスポンス
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Web ルート：セッション・CSRF保護を利用
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
