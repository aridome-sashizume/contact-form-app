<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexContactRequest; //検索条件のバリデーション設定したリクエストを設定
use Illuminate\Http\Request;

use App\Models\Category; // Categoryモデルを使用するために追加
use App\Models\Contact; // Contactモデルを使用するために追加



class AdminController extends Controller
{
    //indexメソッドを追加
    public function index(IndexContactRequest $request)
    {
        // N+1回避
        $query = Contact::query()->with(['tags', 'category']);

        // 名前 or メールアドレス（部分一致 / 完全一致）
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            
            $query->where(function ($q) use ($keyword) {
                // 名前（first_name + last_name の部分一致）
                $q->whereRaw("CONCAT(first_name, last_name) LIKE ?", ["%{$keyword}%"])
                // メール（完全一致・部分一致）
                ->orWhere('email', 'like', '%' . $keyword . '%');
            });
        }

        // 性別（完全一致）
        if ($request->filled('gender') && $request->gender != 0) {
            $query->where('gender', $request->gender);
        }

        // カテゴリ（完全一致）
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 日付（完全一致）
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // ページネーション1ページあたり7件取得 + 検索条件保持
        $contacts = $query->paginate(7)->appends($request->query());

        // admin.indexビューにcontactsとcategoriesを渡す
        return view('admin.index', [
            'contacts' => $contacts,
            'categories' => Category::all(),
        ]);
    }

    //showメソッドを追加
    public function show(Contact $contact)   {
        return view('admin.show',compact('contact'));
    }
}
