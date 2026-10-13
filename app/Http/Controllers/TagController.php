<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest; // タグモデルのバリデーションルールのリクエストを追加（Store用）
use App\Http\Requests\UpdateTagRequest; // タグモデルのバリデーションルールのリクエストを追加（Update用）
use App\Models\Tag; // Tagモデルを追加

class TagController extends Controller
{
    // タグの新規登録処理（入力されたタグ名をバリデーション後にDBへ保存する）
    public function store(StoreTagRequest $request)
    {
        Tag::create($request->all());

        // 管理画面へ戻り、登録完了メッセージを表示
        return redirect('/admin')->with('success', 'タグを追加しました');
    }

    // タグ編集画面の表示（選択されたタグの内容を編集フォームに表示する）
    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    // タグの更新処理（バリデーション済みの入力値で既存タグを更新する）
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $tag->update($request->all());

        // 管理画面へ戻り、更新完了メッセージを表示
        return redirect('/admin')->with('success', 'タグを更新しました');
    }

    // タグの削除処理（選択されたタグをDBから削除する）
    public function destroy(Tag $tag)
    {
        $tag->delete();

        // 管理画面へ戻り、削除完了メッセージを表示
        return redirect('/admin')->with('success', 'タグを削除しました');
    }
}
