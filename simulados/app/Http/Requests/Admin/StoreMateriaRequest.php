<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreMateriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->user_type === User::TYPE_ADM;
    }

    protected function prepareForValidation(): void
    {
        $name = trim((string) $this->input('name'));
        $slug = trim((string) $this->input('slug'));

        $this->merge([
            'name' => $name,
            'slug' => Str::slug($slug !== '' ? $slug : $name),
        ]);
    }

    public function rules(): array
    {
        $materiaId = $this->route('materia')?->id;

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:120',
                Rule::unique('materias', 'name')->ignore($materiaId),
            ],
            'slug' => [
                'required',
                'string',
                'min:3',
                'max:140',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('materias', 'slug')->ignore($materiaId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Informe o nome da materia.',
            'name.min' => 'O nome da materia deve ter ao menos :min caracteres.',
            'name.max' => 'O nome da materia deve ter no maximo :max caracteres.',
            'name.unique' => 'Essa materia ja esta cadastrada.',
            'slug.required' => 'Informe o slug da materia.',
            'slug.min' => 'O slug deve ter ao menos :min caracteres.',
            'slug.max' => 'O slug deve ter no maximo :max caracteres.',
            'slug.regex' => 'Use apenas letras minusculas, numeros e hifen no slug.',
            'slug.unique' => 'Esse slug ja esta em uso.',
        ];
    }
}
