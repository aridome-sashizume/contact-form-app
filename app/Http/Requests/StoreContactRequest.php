<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションするためのデータを準備
     * フォーム内で分けられたデータを結合
     */
    protected function prepareForValidation()
    {
        //既にtelのtelの値がある場合はreturn　※この処理がないとstore処理の際にtel1、tel2、tel3をinputしていないため、結合処理でNULLを参照しエラー発生
        if ($this->has('tel')) {
            return;
        }
        // tel1, tel2, tel3 を結合して tel フィールドを作る
        $tel = ($this->tel1 ?? '') . ($this->tel2 ?? '') . ($this->tel3 ?? '');

        //数字以外は除外する
        $this->merge([
            'tel' => preg_replace('/[^0-9]/', '', $tel),
        ]);


    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // お問い合わせフォーム確認・送信のバリデーションルールを定義
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:1,2,3',
            'email' => 'required|email|max:255',
            'tel' => 'required|string|regex:/^[0-9]{10,11}$/',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'detail' => 'required|string|max:120',

            // お問い合わせ送信（タグ）のバリデーションルールを追加
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'integer|exists:tags,id'
        ];
    }


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
