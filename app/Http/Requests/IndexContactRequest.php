<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexContactRequest extends FormRequest
{
    /**
     * 管理画面の問い合わせ一覧（PG10）の検索条件をバリデーションするためのリクエスト
     * 管理者は全員この機能を利用できるため true を返す
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 問い合わせ一覧の検索条件に適用するバリデーションルール
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

            // 日付検索（YYYY-MM-DD形式）
            'date' => 'nullable|date',
        ];
    }
}
