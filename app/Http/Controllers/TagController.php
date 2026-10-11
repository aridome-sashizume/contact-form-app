<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreTagRequest; //タグモデルのバリデーションルールのリクエストを追加


use App\Models\Tag; //Tagモデルを追加

class TagController extends Controller
{

    //タグを追加するメソッドを追加
    public function store(StoreTagRequest $request)
    {
        Tag::create($request->all());

        ///adminへのリダイレクト
        return redirect('/admin')->with('success', 'タグを追加しました');
    }

    //タグの編集ページへ遷移するメソッドを作成
    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }
}
