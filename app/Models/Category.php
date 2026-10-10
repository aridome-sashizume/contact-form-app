<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    //  カテゴリ情報（DR02）のfillableプロパティを定義
    protected $fillable = [
        'content',
    ];

    /*
     *　お問い合わせ情報（DR01）１対多のリレーション
    */
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
