<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    /**
     * 問い合わせフォームの入力内容をバリデーションするためのリクエスト。
     * 一般ユーザーが利用する機能のため true を返す。
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーション前に入力値を整形する処理。
     * confirm → store の流れでは tel が既に結合済みで送られるため、tel1/tel2/tel3 が存在しない場合のみ結合処理を行う。
     * これにより store 時の NULL 結合エラーを防止する。
     */
    protected function prepareForValidation()
    {
        // confirm → store の流れでは tel が既に存在するため結合処理は不要
        if ($this->has('tel')) {
            return;
        }

        // tel1, tel2, tel3 を結合して tel フィールドを生成
        $tel = ($this->tel1 ?? '').($this->tel2 ?? '').($this->tel3 ?? '');

        // 数字以外は除外して整形
        $this->merge([
            'tel' => preg_replace('/[^0-9]/', '', $tel),
        ]);

    }

    /**
     * 問い合わせフォームの確認画面・送信処理で使用する
     * バリデーションルールは仕様に準拠
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // お問い合わせフォーム確認・送信のバリデーションルールを定義
            // 氏名（姓・名）
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',

            // 性別（男性=1、女性=2、その他=3）
            'gender' => 'required|in:1,2,3',

            // メールアドレス
            'email' => 'required|email|max:255',

            // 電話番号（10〜11桁の数字）
            'tel' => 'required|string|regex:/^[0-9]{10,11}$/',

            // 住所
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',

            // カテゴリ（存在するカテゴリのみ）
            'category_id' => 'required|integer|exists:categories,id',

            // お問い合わせ内容（120文字以内）
            'detail' => 'required|string|max:120',

            // タグ（任意）
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'integer|exists:tags,id',
        ];
    }

    /**
     * バリデーションエラーメッセージ
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
        ];

    }
}
