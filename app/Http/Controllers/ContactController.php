<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category; // Categoryモデルを使用するために追加
use App\Models\Tag; // tagモデルを使用するために追加


class ContactController extends Controller
{
    //indexメソッドを追加
    public function index()
    {
        // 全てのカテゴリを取得
        $categories = Category::all();
        
        // 全てのタグを取得
        $tags = Tag::all();

        // contact.indexビューにカテゴリデータとタグデータを渡す
        return view('contact.index', compact('categories', 'tags'));
    }
}
