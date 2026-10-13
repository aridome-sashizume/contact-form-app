<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    /**
     * お問い合わせ情報（DR01）の登録・更新で利用する属性を指定。
     * 不要なフィールドの一括代入を防ぎ、安全に問い合わせデータを保存できるようにするために fillable を定義している。
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'email',
        'tel',
        'address',
        'building',
        'category_id',
        'detail',
    ];

    /**
     * カテゴリ（DR02）との 1 対多のリレーション。
     * 問い合わせは必ず 1 つのカテゴリに属するため、Contact → Category の belongsTo を定義している。
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * タグ（DR03・DR04）との多対多リレーション。
     * 1件のお問い合わせに複数のタグを付与でき、1つのタグが複数のお問い合わせに紐づくため、belongsToMany を使用して中間テーブル（contact_tag）を扱う。
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
