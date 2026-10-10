<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category; // Categoryモデルを使用するために追加
use App\Models\Contact; // Contactモデルを使用するために追加


class AdminController extends Controller
{
    //indexメソッドを追加
    public function index()
    {
        // ページネーションを使用して、お問い合わせ一覧に1ページあたり7件のコンタクトを取得
        $contacts = Contact::paginate(7);
        // 全てのカテゴリを取得
        $categories = Category::all();

        // admin.indexビューにcontactsとcategoriesを渡す
        return view('admin.index', compact('contacts', 'categories'));
    }
}
