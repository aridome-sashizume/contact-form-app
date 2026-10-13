<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
{
    /**
     * 公開APIの更新リクエストは常に許可
     * （PUT /api/v1/contacts/{contact} は認証不要のため true）
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * お問い合わせ更新API（PG10）のバリデーションルール
     * 仕様書の「お問い合わせフォーム入力ページ」のルールをそのまま適用し、公開API用にタグ(tag_ids)の存在チェックも追加している。
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 姓（必須・文字列・255文字以内）
            'first_name' => 'required|string|max:255',

            // 名（必須・文字列・255文字以内）
            'last_name' => 'required|string|max:255',

            // 性別（必須・1,2,3 のいずれか）
            'gender' => 'required|integer|in:1,2,3',

            // メールアドレス（必須・メール形式）
            'email' => 'required|email|max:255',

            // 電話番号（必須・ハイフンなし10〜11桁）
            'tel' => 'required|string|regex:/^[0-9]{10,11}$/',

            // 住所（必須）
            'address' => 'required|string|max:255',

            // 建物名（任意）
            'building' => 'nullable|string|max:255',

            // カテゴリ（必須・存在チェック）
            'category_id' => 'required|integer|exists:categories,id',

            // お問い合わせ内容（必須・120文字以内）
            'detail' => 'required|string|max:120',

            // タグ（任意・配列）
            'tag_ids' => 'nullable|array',

            // タグの各要素が整数かつ tags テーブルに存在するかチェック
            'tag_ids.*' => 'integer|exists:tags,id',
        ];
    }

    /**
     * 仕様書で明記されている日本語エラーメッセージ
     */
    public function messages(): array
    {
        return [
            'first_name.required' => '姓を入力してください',
            'last_name.required' => '名を入力してください',
            'gender.required' => '性別を選択してください',
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => 'メールアドレスはメール形式で入力してください',
            'tel.required' => '電話番号を入力してください',
            'address.required' => '住所を入力してください',
            'category_id.required' => 'お問い合わせの種類を選択してください',
            'detail.required' => 'お問い合わせ内容を入力してください',
            'detail.max' => 'お問い合わせ内容は120文字以内で入力してください',
            // 公式APIでの追加メッセージ
            'tel.regex' => '電話番号はハイフンなしの10〜11桁で入力してください',
            'gender.in' => '性別の値が不正です',
            'category_id.exists' => '選択されたカテゴリーが存在しません',
            'tag_ids.*' => '選択されたタグが存在しません',
        ];
    }
}
