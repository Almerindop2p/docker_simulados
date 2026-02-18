<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdPost;
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

        $horizontalAd = AdPost::query()
            ->where('slug', AdPost::GLOBAL_HORIZONTAL_SLUG)
            ->first();
        $verticalAd = AdPost::query()
            ->where('slug', AdPost::GLOBAL_VERTICAL_SLUG)
            ->first();

        return view('adm.configuracoes.index', [
            'user' => $request->user(),
            'siteConfig' => SiteConfiguration::current(),
            'horizontalAd' => $horizontalAd,
            'verticalAd' => $verticalAd,
        ]);
    }

    public function updateAdsense(Request $request): RedirectResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate(
            [
                'adsense_enabled' => ['required', 'boolean'],
                'adsense_head_script' => ['nullable', 'string', 'max:60000'],
                'horizontal_ad_code' => ['nullable', 'string', 'max:60000'],
                'vertical_ad_code' => ['nullable', 'string', 'max:60000'],
            ],
            [
                'adsense_enabled.required' => 'Informe se o Adsense deve ficar ativo ou inativo.',
                'adsense_enabled.boolean' => 'Valor invalido para ativacao do Adsense.',
                'adsense_head_script.max' => 'O script do Adsense deve ter no maximo :max caracteres.',
                'horizontal_ad_code.max' => 'O codigo do anuncio horizontal deve ter no maximo :max caracteres.',
                'vertical_ad_code.max' => 'O codigo do anuncio vertical deve ter no maximo :max caracteres.',
            ]
        );

        $config = SiteConfiguration::current();
        $script = isset($data['adsense_head_script']) ? trim((string) $data['adsense_head_script']) : '';
        $horizontalCode = isset($data['horizontal_ad_code']) ? trim((string) $data['horizontal_ad_code']) : '';
        $verticalCode = isset($data['vertical_ad_code']) ? trim((string) $data['vertical_ad_code']) : '';

        $config->update([
            'adsense_enabled' => (bool) $data['adsense_enabled'],
            'adsense_head_script' => $script === '' ? null : $script,
        ]);

        AdPost::query()->updateOrCreate(
            ['slug' => AdPost::GLOBAL_HORIZONTAL_SLUG],
            [
                'title' => 'Anuncio Global Horizontal',
                'format' => AdPost::FORMAT_HORIZONTAL,
                'is_active' => $horizontalCode !== '',
                'embed_code' => $horizontalCode === '' ? null : $horizontalCode,
            ]
        );

        AdPost::query()->updateOrCreate(
            ['slug' => AdPost::GLOBAL_VERTICAL_SLUG],
            [
                'title' => 'Anuncio Global Vertical',
                'format' => AdPost::FORMAT_VERTICAL,
                'is_active' => $verticalCode !== '',
                'embed_code' => $verticalCode === '' ? null : $verticalCode,
            ]
        );

        return redirect()
            ->route('adm.configuracoes.index')
            ->with('status', 'Configuracoes do Adsense atualizadas com sucesso.');
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->user_type === User::TYPE_ADM, 403);
    }
}
