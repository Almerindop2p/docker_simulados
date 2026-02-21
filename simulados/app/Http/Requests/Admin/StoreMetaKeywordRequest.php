<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMetaKeywordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->user_type === User::TYPE_ADM;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'keyword' => trim((string) $this->input('keyword')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $metaKeywordId = $this->route('metaKeyword')?->id;

        return [
            'keyword' => [
                'required',
                'string',
                'min:2',
                'max:160',
                Rule::unique('meta_keywords', 'keyword')->ignore($metaKeywordId),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'keyword.required' => 'Informe a palavra-chave.',
            'keyword.min' => 'A palavra-chave deve ter ao menos :min caracteres.',
            'keyword.max' => 'A palavra-chave deve ter no maximo :max caracteres.',
            'keyword.unique' => 'Essa palavra-chave ja esta cadastrada.',
        ];
    }
}

