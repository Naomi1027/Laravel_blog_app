<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'tags' => ['nullable', 'array', 'max:3'],
            'tags.*' => ['integer', 'exists:tags,id'],
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
            'tags.*.integer' => '選択されたタグが不正です。',
            'tags.*.exists' => '選択されたタグが不正です。',
        ];
    }

    protected function failedValidation(Validator $validator): HttpResponseException
    {
        $response = [
            'status' => 422,
            'message' => '適切な値を入力してください',
            'errors' => $validator->errors(),
        ];

        throw new HttpResponseException(response()->json($response, 422));
    }
}
