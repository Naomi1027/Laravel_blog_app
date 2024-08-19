<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', Rule::unique('users')->whereNotNull('email_verified_at')],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => '名前は必須です。',
            'name.string' => '名前は文字列にして下さい。',
            'email.required' => 'メールアドレスは必須です。',
            'email.string' => 'メールアドレスは文字列にして下さい。',
            'email.email' => 'メールアドレスを入力して下さい。',
            'password.required' => 'パスワードは必須です。',
            'password.string' => 'パスワードは文字列にして下さい。',
        ];
    }
}
