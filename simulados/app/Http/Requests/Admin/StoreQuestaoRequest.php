<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuestaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->user_type === User::TYPE_ADM;
    }

    public function rules(): array
    {
        return [
            'banca_id' => ['required', 'integer', 'exists:bancas,id'],
            'materia_id' => ['required', 'integer', 'exists:materias,id'],
            'instituicao_id' => ['required', 'integer', 'exists:instituicoes,id'],
            'simulado_id' => ['required', 'integer', 'exists:simulados,id'],
            'imagem' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'enunciado' => ['required', 'string'],
            'alternativa_a' => ['required', 'string'],
            'alternativa_b' => ['required', 'string'],
            'alternativa_c' => ['required', 'string'],
            'alternativa_d' => ['required', 'string'],
            'alternativa_e' => ['nullable', 'string'],
            'gabarito' => ['required', 'in:A,B,C,D,E'],
            'explicacao' => ['nullable', 'string'],
            'cargo_ids' => ['required', 'array', 'min:1'],
            'cargo_ids.*' => ['integer', 'distinct', 'exists:cargos,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'banca_id.required' => 'Selecione a banca.',
            'banca_id.exists' => 'A banca selecionada e invalida.',
            'materia_id.required' => 'Selecione a materia.',
            'materia_id.exists' => 'A materia selecionada e invalida.',
            'instituicao_id.required' => 'Selecione a instituicao.',
            'instituicao_id.exists' => 'A instituicao selecionada e invalida.',
            'simulado_id.required' => 'Selecione o simulado.',
            'simulado_id.exists' => 'O simulado selecionado e invalido.',
            'imagem.image' => 'Envie um arquivo de imagem valido.',
            'imagem.mimes' => 'A imagem deve estar em JPG, JPEG, PNG, WEBP ou GIF.',
            'imagem.max' => 'A imagem deve ter no maximo 5MB.',
            'enunciado.required' => 'Informe o enunciado da questao.',
            'alternativa_a.required' => 'Informe a alternativa A.',
            'alternativa_b.required' => 'Informe a alternativa B.',
            'alternativa_c.required' => 'Informe a alternativa C.',
            'alternativa_d.required' => 'Informe a alternativa D.',
            'gabarito.required' => 'Selecione o gabarito.',
            'gabarito.in' => 'O gabarito deve ser A, B, C, D ou E.',
            'cargo_ids.required' => 'Selecione pelo menos um cargo.',
            'cargo_ids.min' => 'Selecione ao menos um cargo.',
            'cargo_ids.*.exists' => 'Um dos cargos selecionados e invalido.',
        ];
    }
}
