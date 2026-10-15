<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexContactRequest extends FormRequest
{
    /**
     * 公開APIの検索リクエストは常に許可
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 公開APIの検索条件バリデーション
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // キーワード検索（部分一致用）
            'keyword' => 'nullable|string|max:255',

            // 性別（1,2,3 のいずれか）
            'gender' => 'nullable|integer| in:1,2,3',

            // カテゴリID（存在チェック）
            'category_id' => 'nullable|integer|exists:categories,id',

            // 日付検索（created_at の日付一致）
            'date' => 'nullable|date_format:Y-m-d',

            // ページネーション（任意）
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ];
    }

    /**
     * 仕様書で明記されている日本語エラーメッセージ
     */
    public function messages(): array
    {
        return [
            'gender.in' => '性別の値が不正です',
            'category_id.exists' => '選択されたカテゴリーが存在しません',
        ];
    }
}
