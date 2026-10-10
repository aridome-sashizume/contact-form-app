<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category; // Categoryモデルを使用するために追加
use App\Models\Tag; // tagモデルを使用するために追加


use App\Http\Requests\StoreContactRequest; // StoreContactRequestを使用するために追加

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

    // confirmメソッドを追加
    public function confirm(StoreContactRequest $request)
    {
        // バリデーションを実行
        $validated =$request->validated();

        // カテゴリを取得
        $category = Category::find($validated['category_id']);


        // 確認画面にデータを渡す
        return view('contact.confirm', compact('validated','category'));
    }



}
