<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportContactRequest; // Categoryモデルを使用するために追加
use App\Http\Requests\StoreContactRequest; // tagモデルを使用するために追加
use App\Models\Category; // Contactモデルを使用するために追加
use App\Models\Contact; // StoreContactRequestを使用するために追加
use App\Models\Tag; // CSVを出力する際の条件データのバリデーションを設定したリクエストを追加

class ContactController extends Controller
{
    // indexメソッドを追加
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
        $validated = $request->validated();

        // カテゴリを取得
        $category = Category::find($validated['category_id']);

        // 確認画面にデータを渡す
        return view('contact.confirm', compact('validated', 'category'));
    }

    // storeメソッドを追加
    public function store(StoreContactRequest $request)
    {
        // バリデーションを実行
        $validated = $request->validated();

        // contactsテーブルにレコードを保存する
        $contact = Contact::create($validated);

        // contact_tagテーブルにタグを保存
        $contact->tags()->sync($request->tag_ids ?? []);

        // サンクスページへリダイレクト
        return redirect('/thanks');
    }

    public function thanks()
    {
        return view('contact.thanks');
    }

    public function export(ExportContactRequest $request)
    {
        // N+1回避、新着順で取得するように設定
        $query = Contact::query()->with(['tags', 'category'])->orderBy('created_at', 'desc');

        // 名前 or メールアドレス（部分一致 / 完全一致）
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                // 名前（first_name + last_name の部分一致）
                $q->whereRaw('CONCAT(first_name, last_name) LIKE ?', ["%{$keyword}%"])
                // メール（完全一致・部分一致）
                    ->orWhere('email', 'like', '%'.$keyword.'%');
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

        // 検索条件に一致する項目を全件取得
        $contacts = $query->get();

        // 文字列に変換
        $genderLabels = [1 => '男性', 2 => '女性', 3 => 'その他'];

        // CSVデータ作成
        $csvData = [];
        $csvData[] = ['ID', '氏名', '性別', 'メール', '電話', '住所', '建物', 'カテゴリ', '内容', '作成日時'];

        foreach ($contacts as $contact) {
            $csvData[] = [
                $contact->id,
                $contact->first_name.' '.$contact->last_name,
                $genderLabels[$contact->gender] ?? '', // 文字列
                $contact->email,
                $contact->tel,
                $contact->address,
                $contact->building,
                $contact->category->content ?? '', // 文字列
                $contact->detail,
                $contact->created_at->format('Y-m-d'),
            ];
        }

        // CSVデータを出力
        $filename = 'contacts_export_'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($csvData) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM を追加
            fwrite($file, "\xEF\xBB\xBF");

            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
