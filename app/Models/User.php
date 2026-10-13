<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * ユーザー登録・更新時に一括代入を許可する属性。
     * name / email / password のみを許可することで、不正なフィールドの代入を防ぎ安全にユーザー情報を扱う。
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * JSON などへシリアライズする際に非表示にする属性。
     * password や remember_token を隠すことで、セキュリティ上の情報漏洩を防止する。
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * モデル属性の型変換設定。
     * email_verified_at は日時として扱い、password は自動的にハッシュ化して保存する。
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
