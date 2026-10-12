<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class UpdateTagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        //name必須/<=50/ユニーク（自身の現在名は許可）
        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tags', 'name')->ignore($this->tag->id), //ユニークのルールにタグIDが同一であった場合は対象除外することをルールとして記述
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'=> 'タグ名を入力してください',
            'name.max'=> 'タグ名は50文字以内で入力してください',
            'name.unique'=> 'そのタグ名は既に使用されています'
        ];
    }
}
