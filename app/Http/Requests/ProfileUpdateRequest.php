<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<string|Rule>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'display_name' => ['nullable', 'string', 'max:255'],
            'icon_path' => [
                'nullable',                // 必須ではない
                'mimes:jpeg,jpg,png,gif', // 許可される画像形式を指定
                'max:2048',               // ファイルサイズの上限（ここでは2MB）
            ],
        ];
    }
}
