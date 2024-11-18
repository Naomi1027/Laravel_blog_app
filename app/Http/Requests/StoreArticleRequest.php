<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
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
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'nullable|array|max:3',
            'tags.*' => 'numeric|exists:tags,id',
            'image' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です。',
            'title.string' => 'タイトルは文字列にして下さい。',
            'title.max' => 'タイトルは:max文字以内でお願いします。',
            'content.required' => '本文は必須です。',
            'content.string' => '本文は文字列にして下さい。',
            'tags.max' => 'タグは:maxつまで選択して下さい',
            'image.file' => '画像はファイルにして下さい。',
            'image.max' => '2MBを超えるファイルは添付できません。',
            'image.mimes' => '指定のファイル形式以外は添付できません。',
        ];
    }
}
