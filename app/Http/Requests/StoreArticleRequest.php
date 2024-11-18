<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Storage;

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

    protected function failedValidation(Validator $validator)
    {
        // 既にセッションに一時的な画像がある場合、それを削除
        if (session()->has('temp_image')) {
            $oldTempPath = session('temp_image');
            // S3から古い一時画像を削除
            Storage::disk('s3')->delete($oldTempPath);
            // セッションから削除
            session()->forget('temp_image');
        }

        // 新しく画像がアップロードされている場合
        if ($this->hasFile('image')) {
            // 画像を一時的にS3に保存
            $tempPath = Storage::disk('s3')->put('temp_images', $this->file('image'), 'public');

            // セッションに一時的な画像のパスを保存
            session(['temp_image' => $tempPath]);
        }

        throw new HttpResponseException(
            redirect()->back()
                ->withErrors($validator)
                ->withInput()
        );
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
