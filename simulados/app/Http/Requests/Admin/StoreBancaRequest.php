<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class StoreBancaRequest extends FormRequest
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
        $name = trim((string) $this->input('name'));
        $slug = trim((string) $this->input('slug'));

        $this->merge([
            'name' => $name,
            'slug' => Str::slug($slug !== '' ? $slug : $name),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $bancaId = $this->route('banca')?->id;

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:120',
                Rule::unique('bancas', 'name')->ignore($bancaId),
            ],
            'slug' => [
                'required',
                'string',
                'min:3',
                'max:140',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('bancas', 'slug')->ignore($bancaId),
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
            'name.required' => 'Informe o nome da banca.',
            'name.min' => 'O nome da banca deve ter ao menos :min caracteres.',
            'name.max' => 'O nome da banca deve ter no maximo :max caracteres.',
            'name.unique' => 'Essa banca ja esta cadastrada.',
            'slug.required' => 'Informe o slug da banca.',
            'slug.min' => 'O slug deve ter ao menos :min caracteres.',
            'slug.max' => 'O slug deve ter no maximo :max caracteres.',
            'slug.regex' => 'Use apenas letras minusculas, numeros e hifen no slug.',
            'slug.unique' => 'Esse slug ja esta em uso.',
        ];
    }
}
