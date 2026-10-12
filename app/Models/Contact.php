<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;


    //お問い合わせ情報（DR01）のfillableプロパティを定義
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

    /*
    *　カテゴリ１対多のリレーション
    */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /*
     *　タグ多対多のリレーション
    */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
