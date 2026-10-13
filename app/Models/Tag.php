<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    /**
     * タグ情報（DR03）の登録・更新で利用する属性を指定。
     * 不要なフィールドの一括代入を防ぎ、安全にタグを作成できるようにfillable を定義している。
     */
    protected $fillable = [
        'name',
    ];

    /**
     * お問い合わせ情報（DR01）との多対多リレーション。
     * 1つのタグが複数のお問い合わせに紐づき、1件のお問い合わせに複数のタグを付与できるため、中間テーブル（contact_tag：DR04）を介して belongsToMany を定義する。
     */
    public function contacts()
    {
        return $this->belongsToMany(Contact::class);
    }
}
