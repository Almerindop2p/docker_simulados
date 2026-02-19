@php
    $loggedUser = auth()->user();
    $metricsConsentEnabledForCurrentUser = (bool) ($metricsConsentEnabled ?? true);
    if ($loggedUser && ($loggedUser->user_type ?? null) === \App\Models\User::TYPE_ADM) {
        $metricsConsentEnabledForCurrentUser = false;
    }
    $metricsConsentInitial = (bool) ($metricsConsentGranted ?? false);
    $metricsRouteName = (string) ($metricsCurrentRouteName ?? request()->route()?->getName() ?? '');
@endphp

@if ($metricsConsentEnabledForCurrentUser)
<style>
    .lgpd-consent-bar {
        position: fixed;
        left: 12px;
        right: 12px;
        bottom: 12px;
        z-index: 120;
        border: 1px solid #c9d8ee;
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 14px 34px rgba(17, 32, 51, 0.24);
        padding: 12px;
        display: grid;
        gap: 10px;
    }

    .lgpd-consent-title {
        margin: 0;
        font-size: 14px;
        font-weight: 800;
        color: #17365a;
    }

    .lgpd-consent-text {
        margin: 0;
        color: #35567b;
        font-size: 13px;
        line-height: 1.55;
    }

    .lgpd-consent-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .lgpd-consent-btn {
        min-height: 38px;
        border-radius: 10px;
        border: 1px solid #c9d8ee;
        background: #fff;
        color: #20466f;
        font-weight: 700;
        font-size: 13px;
        padding: 0 12px;
        cursor: pointer;
    }

    .lgpd-consent-btn.primary {
        border-color: transparent;
        color: #fff;
        background: linear-gradient(135deg, #1f5fe0, #4d84f1);
        box-shadow: 0 10px 22px rgba(31, 95, 224, 0.26);
    }

    .lgpd-consent-btn[disabled] {
        opacity: 0.7;
        cursor: not-allowed;
    }

    @media (min-width: 760px) {
        .lgpd-consent-bar {
            max-width: 760px;
            margin: 0 auto;
            left: 16px;
            right: 16px;
            padding: 14px;
        }
    }
</style>

<script>
    (function () {
        var config = {
            consentGranted: {{ $metricsConsentInitial ? 'true' : 'false' }},
            grantUrl: @json(route('metrics.consent.grant')),
            collectUrl: @json(route('metrics.capture')),
            routeName: @json($metricsRouteName),
            isLogged: {{ auth()->check() ? 'true' : 'false' }},
        };

        var consentGranted = !!config.consentGranted;
        var captureSent = false;
        var loadTimerReady = false;
        var bannerElement = null;

        function getCsrfToken() {
            var meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        }

        function postJson(url, payload) {
            return fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify(payload || {})
            });
        }

        function hideBanner() {
            if (bannerElement && bannerElement.parentNode) {
                bannerElement.parentNode.removeChild(bannerElement);
            }
            bannerElement = null;
        }

        function buildBanner() {
            if (bannerElement || consentGranted || !document.body) {
                return;
            }

            bannerElement = document.createElement('section');
            bannerElement.className = 'lgpd-consent-bar';
            bannerElement.setAttribute('role', 'dialog');
            bannerElement.setAttribute('aria-live', 'polite');
            bannerElement.setAttribute('aria-label', 'Permissao LGPD para metricas');
            bannerElement.innerHTML = '' +
                '<p class="lgpd-consent-title">Privacidade e metricas de uso</p>' +
                '<p class="lgpd-consent-text">Para melhorar a plataforma, podemos coletar IP, navegador, dispositivo e localizacao aproximada por IP. Deseja autorizar?</p>' +
                '<div class="lgpd-consent-actions">' +
                    '<button type="button" class="lgpd-consent-btn" data-lgpd-action="dismiss">Agora nao</button>' +
                    '<button type="button" class="lgpd-consent-btn primary" data-lgpd-action="accept">Autorizar</button>' +
                '</div>';

            document.body.appendChild(bannerElement);

            var dismissButton = bannerElement.querySelector('[data-lgpd-action="dismiss"]');
            var acceptButton = bannerElement.querySelector('[data-lgpd-action="accept"]');

            if (dismissButton) {
                dismissButton.addEventListener('click', function () {
                    hideBanner();
                });
            }

            if (acceptButton) {
                acceptButton.addEventListener('click', async function () {
                    if (acceptButton.hasAttribute('disabled')) {
                        return;
                    }

                    acceptButton.setAttribute('disabled', 'disabled');

                    try {
                        var response = await postJson(config.grantUrl, {});
                        if (!response.ok) {
                            acceptButton.removeAttribute('disabled');
                            return;
                        }

                        consentGranted = true;
                        hideBanner();

                        if (loadTimerReady && !captureSent) {
                            setTimeout(sendMetric, 120);
                        }
                    } catch (e) {
                        acceptButton.removeAttribute('disabled');
                    }
                });
            }
        }

        async function sendMetric() {
            if (captureSent || !consentGranted) {
                return;
            }

            captureSent = true;

            var payload = {
                route_name: config.routeName || null,
                page_url: window.location.href || null,
                path: (window.location.pathname || '') + (window.location.search || ''),
                referrer: document.referrer || null,
                timezone: (Intl.DateTimeFormat && Intl.DateTimeFormat().resolvedOptions().timeZone) || null,
                language: navigator.language || null,
                device_model: (navigator.userAgentData && navigator.userAgentData.model) || null,
                viewport_width: window.innerWidth || null,
                viewport_height: window.innerHeight || null
            };

            try {
                var response = await postJson(config.collectUrl, payload);
                if (!response.ok) {
                    captureSent = false;
                }
            } catch (e) {
                captureSent = false;
            }
        }

        window.addEventListener('load', function () {
            if (!consentGranted) {
                buildBanner();
            }

            setTimeout(function () {
                loadTimerReady = true;
                sendMetric();
            }, 5000);
        });
    })();
</script>
@endif
