<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvatarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.required' => 'Selecione uma imagem para o avatar.',
            'avatar.image' => 'O arquivo enviado precisa ser uma imagem valida.',
            'avatar.mimes' => 'O avatar deve estar em JPG, JPEG, PNG ou WEBP.',
            'avatar.max' => 'O avatar nao pode ter mais de 2MB.',
        ];
    }
}
