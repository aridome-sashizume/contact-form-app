<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    // タグ情報（DR03）のfillableプロパティを定義
    protected $fillable = [
        'name',
    ];

    /*
     *　お問い合わせ情報（DR01）多対多のリレーション
    */
    public function contacts()
    {
        return $this->belongsToMany(Contact::class); 
    }
}
