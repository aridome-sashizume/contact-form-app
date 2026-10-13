<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * カテゴリ情報の登録・更新で利用する属性を指定。
     * content のみを一括代入可能にすることで、不要なフィールドの代入を防ぎ、安全にカテゴリを作成できるようにしている。
     */
    protected $fillable = [
        'content',
    ];

    /**
     * 問い合わせ情報（DR01）との 1 対多のリレーション。
     * 1つのカテゴリに複数のお問い合わせが紐づくため、
     * Category → Contact の hasMany を定義している。
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
