<?php

namespace App\Http\Requests\Feedback;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedbackTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'mensagem' => ['required', 'string', 'min:5', 'max:5000'],
            'origem_rota' => ['nullable', 'string', 'max:255'],
            'pagina_url' => ['nullable', 'url', 'max:2048'],
        ];

        if (!$this->user()) {
            $rules['nome'] = ['required', 'string', 'min:2', 'max:120'];
            $rules['email'] = ['required', 'email', 'max:255'];
        } else {
            $rules['nome'] = ['nullable', 'string', 'max:120'];
            $rules['email'] = ['nullable', 'email', 'max:255'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'Informe seu nome.',
            'email.required' => 'Informe seu e-mail.',
            'email.email' => 'Informe um e-mail valido.',
            'mensagem.required' => 'Digite sua mensagem.',
            'mensagem.min' => 'A mensagem deve ter ao menos :min caracteres.',
            'mensagem.max' => 'A mensagem nao pode ultrapassar :max caracteres.',
            'pagina_url.url' => 'A URL da pagina e invalida.',
        ];
    }
}
