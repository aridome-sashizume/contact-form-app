<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTagRequest extends FormRequest
{
    /**
     * タグ更新機能で使用するバリデーションを許可する。
     * 管理画面の利用者は全員この機能を使えるため true を返す。
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * タグ名の更新時に適用するバリデーションルール
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // タグ名（必須／文字列／50文字以内／自分以外と重複不可）
        return [
            'name' => [
                'required',
                'string',
                'max:50',
                // 自身のタグIDを除外してユニークチェックを行う
                Rule::unique('tags', 'name')->ignore($this->tag->id),
            ],
        ];
    }

    /**
     * バリデーションエラーメッセージ
     */
    public function messages(): array
    {
        return [
            'name.required' => 'タグ名を入力してください',
            'name.max' => 'タグ名は50文字以内で入力してください',
            'name.unique' => 'そのタグ名は既に使用されています',
        ];
    }
}
