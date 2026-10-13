<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ExportContactRequest extends FormRequest
{
    /***********************************/
    /* CSVエクスポート機能で使用する検索条件のバリデーションを許可する
    /* 管理画面の利用者は全員この機能を使えるため true を返す
    /***********************************/
    public function authorize(): bool
    {
        return true;
    }

    /**
     * CSVエクスポート時に使用する検索条件のバリデーションルール
     * keyword / gender / category_id / date の任意入力を安全に受け取るための設定
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // キーワード検索（氏名・メール）／任意入力
            'keyword' => 'nullable|string|max:255',

            // 性別（未選択=0、男性=1、女性=2、その他=3）
            'gender' => 'nullable|integer| in:0,1,2,3',

            // カテゴリID（存在するカテゴリのみ選択可能）
            'category_id' => 'nullable|integer|exists:categories,id',

            // 日付検索
            'date' => 'nullable|date',
        ];
    }
}
