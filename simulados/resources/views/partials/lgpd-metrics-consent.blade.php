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
            dismissUntilCookieName: 'lgpd_metrics_dismiss_until',
            localDismissUntilKey: 'lgpd_metrics_dismiss_until_local',
            dismissTtlSeconds: {{ 7 * 24 * 60 * 60 }},
        };

        function readCookie(name) {
            var parts = document.cookie ? document.cookie.split('; ') : [];
            for (var i = 0; i < parts.length; i++) {
                var part = parts[i];
                var eqIndex = part.indexOf('=');
                if (eqIndex === -1) {
                    continue;
                }
                var key = part.substring(0, eqIndex);
                if (key === name) {
                    return decodeURIComponent(part.substring(eqIndex + 1));
                }
            }
            return '';
        }

        function getLocalDismissUntil() {
            try {
                var raw = window.localStorage.getItem(config.localDismissUntilKey);
                var value = parseInt(raw || '0', 10);
                return Number.isFinite(value) ? value : 0;
            } catch (e) {
                return 0;
            }
        }

        function setLocalDismissUntil(untilTimestamp) {
            try {
                window.localStorage.setItem(config.localDismissUntilKey, String(untilTimestamp));
            } catch (e) {
                // no-op
            }
        }

        function setDismissCookie(untilTimestamp) {
            var maxAge = Math.max(0, parseInt(config.dismissTtlSeconds, 10) || 0);
            var cookieValue = encodeURIComponent(String(untilTimestamp));
            var cookie = config.dismissUntilCookieName + '=' + cookieValue + '; path=/; max-age=' + maxAge + '; SameSite=Lax';
            if (window.location.protocol === 'https:') {
                cookie += '; Secure';
            }
            document.cookie = cookie;
        }

        function hasActiveDismissWindow() {
            var cookieUntil = parseInt(readCookie(config.dismissUntilCookieName) || '0', 10);
            var localUntil = getLocalDismissUntil();
            var until = Math.max(
                Number.isFinite(cookieUntil) ? cookieUntil : 0,
                Number.isFinite(localUntil) ? localUntil : 0
            );

            if (until <= 0) {
                return false;
            }

            var nowTs = Math.floor(Date.now() / 1000);
            return until > nowTs;
        }

        var consentGranted = !!config.consentGranted;
        var captureSent = false;
        var bannerElement = null;
        var isPageReady = false;

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
            if (bannerElement || consentGranted || !document.body || hasActiveDismissWindow()) {
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
                    var until = Math.floor(Date.now() / 1000) + (parseInt(config.dismissTtlSeconds, 10) || 0);
                    setLocalDismissUntil(until);
                    setDismissCookie(until);
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

                        if (isPageReady && !captureSent) {
                            setTimeout(sendMetric, 120);
                        }
                    } catch (e) {
                        acceptButton.removeAttribute('disabled');
                    }
                });
            }
        }

        function buildPayload() {
            return {
                route_name: config.routeName || null,
                page_url: window.location.href || null,
                path: (window.location.pathname || '') + (window.location.search || ''),
                referrer: document.referrer || null,
                timezone: (Intl.DateTimeFormat && Intl.DateTimeFormat().resolvedOptions().timeZone) || null,
                language: navigator.language || null,
                device_model: (navigator.userAgentData && navigator.userAgentData.model) || null,
                viewport_width: window.innerWidth || null,
                viewport_height: window.innerHeight || null,
                consent_granted: consentGranted ? 1 : 0
            };
        }

        async function sendMetric() {
            if (captureSent) {
                return;
            }

            captureSent = true;

            try {
                var response = await postJson(config.collectUrl, buildPayload());
                if (!response.ok) {
                    captureSent = false;
                }
            } catch (e) {
                captureSent = false;
            }
        }

        function scheduleMetricCapture(delayMs) {
            window.setTimeout(function () {
                sendMetric();
            }, delayMs);
        }

        window.addEventListener('load', function () {
            isPageReady = true;

            if (!consentGranted) {
                buildBanner();
            }

            scheduleMetricCapture(5000);
        });

        window.addEventListener('pageshow', function (event) {
            if (!event || !event.persisted) {
                return;
            }

            captureSent = false;

            if (!consentGranted) {
                buildBanner();
            }

            scheduleMetricCapture(5000);
        });
    })();
</script>
@endif
