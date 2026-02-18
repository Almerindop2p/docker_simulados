<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteConfiguration;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConfiguracaoController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('adm.configuracoes.index', [
            'user' => $request->user(),
            'siteConfig' => SiteConfiguration::current(),
        ]);
    }

    public function updateAdsense(Request $request): RedirectResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate(
            [
                'adsense_enabled' => ['required', 'boolean'],
                'adsense_head_script' => ['nullable', 'string', 'max:60000'],
            ],
            [
                'adsense_enabled.required' => 'Informe se o Adsense deve ficar ativo ou inativo.',
                'adsense_enabled.boolean' => 'Valor invalido para ativacao do Adsense.',
                'adsense_head_script.max' => 'O script do Adsense deve ter no maximo :max caracteres.',
            ]
        );

        $config = SiteConfiguration::current();
        $script = isset($data['adsense_head_script']) ? trim((string) $data['adsense_head_script']) : '';

        $config->update([
            'adsense_enabled' => (bool) $data['adsense_enabled'],
            'adsense_head_script' => $script === '' ? null : $script,
        ]);

        return redirect()
            ->route('adm.configuracoes.index')
            ->with('status', 'Configuracoes do Adsense atualizadas com sucesso.');
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->user_type === User::TYPE_ADM, 403);
    }
}
