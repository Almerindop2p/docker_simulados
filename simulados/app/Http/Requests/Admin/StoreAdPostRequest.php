<?php

namespace App\Http\Requests\Admin;

use App\Models\AdPost;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreAdPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->user_type === User::TYPE_ADM;
    }

    protected function prepareForValidation(): void
    {
        $title = trim((string) $this->input('title'));
        $slug = trim((string) $this->input('slug'));

        $this->merge([
            'title' => $title,
            'slug' => Str::slug($slug !== '' ? $slug : $title),
            'format' => trim((string) $this->input('format')),
            'is_active' => filter_var($this->input('is_active', false), FILTER_VALIDATE_BOOLEAN),
            'embed_code' => trim((string) $this->input('embed_code', '')),
        ]);
    }

    public function rules(): array
    {
        $adPostId = $this->route('anuncio')?->id;

        return [
            'title' => [
                'required',
                'string',
                'min:3',
                'max:120',
                Rule::unique('ad_posts', 'title')->ignore($adPostId),
            ],
            'slug' => [
                'required',
                'string',
                'min:3',
                'max:140',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('ad_posts', 'slug')->ignore($adPostId),
            ],
            'format' => [
                'required',
                Rule::in(AdPost::FORMATS),
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
            'embed_code' => [
                'nullable',
                'string',
                'max:60000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Informe o titulo do anuncio.',
            'title.min' => 'O titulo deve ter ao menos :min caracteres.',
            'title.max' => 'O titulo deve ter no maximo :max caracteres.',
            'title.unique' => 'Ja existe um anuncio com esse titulo.',
            'slug.required' => 'Informe o slug do anuncio.',
            'slug.min' => 'O slug deve ter ao menos :min caracteres.',
            'slug.max' => 'O slug deve ter no maximo :max caracteres.',
            'slug.regex' => 'Use apenas letras minusculas, numeros e hifen no slug.',
            'slug.unique' => 'Esse slug ja esta em uso.',
            'format.required' => 'Selecione o formato do anuncio.',
            'format.in' => 'Formato de anuncio invalido para o site.',
            'is_active.required' => 'Informe se o anuncio deve ficar ativo.',
            'is_active.boolean' => 'Valor invalido para status do anuncio.',
            'embed_code.max' => 'O codigo do anuncio deve ter no maximo :max caracteres.',
        ];
    }
}
