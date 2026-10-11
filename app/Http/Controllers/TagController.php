<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreTagRequest; //タグモデルのバリデーションルールのリクエストを追加（Store用）
use App\Http\Requests\UpdateTagRequest; //タグモデルのバリデーションルールのリクエストを追加（Update用）

use App\Models\Tag; //Tagモデルを追加

class TagController extends Controller
{

    //タグを追加するメソッドを作成
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

    //タグを更新するメソッドを作成
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $tag->update($request->all());
        return redirect('/admin')->with('success','タグを更新しました');
    }

    //タグを削除するメソッドを作成
    public function destroy(Tag $tag)
    {
        $tag->delete();
        return redirect('/admin')->with('success','タグを削除しました');
    }
}
