<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
{
    /**
     * タグ登録機能で使用するバリデーションを許可する。
     * 管理画面の利用者は全員この機能を使えるため true を返す。
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * タグ名の登録時に適用するバリデーションルール。
     * 「必須・文字列・50文字以内・未使用のタグ名であること」という管理画面の仕様に基づく。
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // タグ名（必須／文字列／50文字以内／重複不可）
            'name' => 'required|string|max:50|unique:tags,name',
        ];
    }

    /**
     * バリデーションエラーメッセージ
     */
    public function messages(): array
    {
        return [
            //
            'name.required' => 'タグ名を入力してください',
            'name.max' => 'タグ名は50文字以内で入力してください',
            'name.unique' => 'そのタグ名は既に使用されています',
        ];
    }
}
