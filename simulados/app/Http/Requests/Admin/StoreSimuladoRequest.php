<?php

namespace App\Http\Requests\Admin;

use App\Models\Simulado;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreSimuladoRequest extends FormRequest
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
            'visibilidade' => trim((string) $this->input('visibilidade')),
            'descricao' => trim((string) $this->input('descricao')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $simuladoId = $this->route('simulado')?->id;

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:120',
                Rule::unique('simulados', 'name')->ignore($simuladoId),
            ],
            'slug' => [
                'required',
                'string',
                'min:3',
                'max:140',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('simulados', 'slug')->ignore($simuladoId),
            ],
            'visibilidade' => [
                'required',
                Rule::in(Simulado::VISIBILIDADES),
            ],
            'descricao' => [
                'nullable',
                'string',
                'max:3000',
            ],
            'imagem_destaque' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:5120',
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
            'name.required' => 'Informe o nome do simulado.',
            'name.min' => 'O nome do simulado deve ter ao menos :min caracteres.',
            'name.max' => 'O nome do simulado deve ter no maximo :max caracteres.',
            'name.unique' => 'Esse simulado ja esta cadastrado.',
            'slug.required' => 'Informe o slug do simulado.',
            'slug.min' => 'O slug deve ter ao menos :min caracteres.',
            'slug.max' => 'O slug deve ter no maximo :max caracteres.',
            'slug.regex' => 'Use apenas letras minusculas, numeros e hifen no slug.',
            'slug.unique' => 'Esse slug ja esta em uso.',
            'visibilidade.required' => 'Selecione a visibilidade do simulado.',
            'visibilidade.in' => 'A visibilidade selecionada e invalida.',
            'descricao.max' => 'A descricao deve ter no maximo :max caracteres.',
            'imagem_destaque.image' => 'O arquivo de imagem destaque precisa ser uma imagem valida.',
            'imagem_destaque.mimes' => 'Use um arquivo de imagem nos formatos: jpg, jpeg, png, webp ou gif.',
            'imagem_destaque.max' => 'A imagem destaque deve ter no maximo 5MB.',
        ];
    }
}
