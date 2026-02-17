@php
    $notifData = $headerNotifications ?? ['title' => 'Notificacoes', 'items' => [], 'count' => 0];
    $notifItems = $notifData['items'] ?? [];
    $notifCount = (int) ($notifData['count'] ?? 0);
    $notifMenuId = 'notification-menu-' . uniqid();
@endphp

<div class="notif-wrap" data-notif-root>
    <button
        class="notif-btn"
        type="button"
        aria-label="Abrir notificacoes"
        aria-expanded="false"
        aria-controls="{{ $notifMenuId }}"
        data-notif-button
    >
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M15.5 17h-7v-6.2a3.5 3.5 0 0 1 7 0V17Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"></path>
            <path d="M5 17h14M10.1 20h3.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
            <path d="M12 4.2v1.1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
        </svg>
        @if ($notifCount > 0)
            <span class="notif-badge">{{ $notifCount > 99 ? '99+' : $notifCount }}</span>
        @endif
    </button>

    <section id="{{ $notifMenuId }}" class="notif-menu" hidden aria-label="Lista de notificacoes" data-notif-menu>
        <header class="notif-header">
            <h3>{{ $notifData['title'] ?? 'Notificacoes' }}</h3>
        </header>

        @if (empty($notifItems))
            <p class="notif-empty">Nenhuma notificacao no momento.</p>
        @else
            <ul class="notif-list">
                @foreach ($notifItems as $item)
                    @php
                        $typeClass = match ($item['type'] ?? 'info') {
                            'warning' => 'is-warning',
                            'success' => 'is-success',
                            'danger' => 'is-danger',
                            default => 'is-info',
                        };
                        $itemUrl = $item['url'] ?? null;
                    @endphp
                    <li class="notif-item {{ $typeClass }}">
                        @if ($itemUrl)
                            <a href="{{ $itemUrl }}" class="notif-link">
                                <strong>{{ $item['title'] ?? 'Notificacao' }}</strong>
                                <span>{{ $item['message'] ?? '' }}</span>
                            </a>
                        @else
                            <div class="notif-link">
                                <strong>{{ $item['title'] ?? 'Notificacao' }}</strong>
                                <span>{{ $item['message'] ?? '' }}</span>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>

<style>
    .notif-wrap {
        position: relative;
    }

    .notif-btn {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        border: 1px solid #d3e0f2;
        background: #fff;
        color: #1f3f67;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
    }

    .notif-btn svg {
        width: 20px;
        height: 20px;
    }

    .notif-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        min-width: 20px;
        height: 20px;
        border-radius: 999px;
        background: #e0314b;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 6px;
        border: 2px solid #fff;
    }

    .notif-menu {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: min(360px, calc(100vw - 24px));
        border: 1px solid #d4dfef;
        border-radius: 14px;
        background: #fff;
        box-shadow: var(--shadow-soft);
        padding: 8px;
        z-index: 65;
    }

    .notif-header h3 {
        margin: 0;
        padding: 4px 6px 8px;
        font-size: 14px;
        color: #1f3f67;
    }

    .notif-empty {
        margin: 0;
        border-radius: 10px;
        border: 1px solid #dde7f5;
        background: #f8fbff;
        color: #4d6582;
        font-size: 13px;
        padding: 10px;
    }

    .notif-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 6px;
        max-height: min(360px, 60vh);
        overflow-y: auto;
    }

    .notif-item {
        border: 1px solid #dde7f5;
        border-radius: 10px;
        background: #f8fbff;
    }

    .notif-item.is-warning {
        border-color: #f1d7ad;
        background: #fffaf1;
    }

    .notif-item.is-success {
        border-color: #c6e7d2;
        background: #f2fbf5;
    }

    .notif-item.is-danger {
        border-color: #f2c4cc;
        background: #fff4f6;
    }

    .notif-link {
        display: grid;
        gap: 4px;
        padding: 10px;
        color: #1f3f67;
        text-decoration: none;
    }

    .notif-link strong {
        font-size: 13px;
        line-height: 1.3;
    }

    .notif-link span {
        font-size: 12px;
        line-height: 1.45;
        color: #4a6483;
    }
</style>

<script>
    (function () {
        var roots = document.querySelectorAll('[data-notif-root]');

        if (!roots.length) {
            return;
        }

        roots.forEach(function (root) {
            var button = root.querySelector('[data-notif-button]');
            var menu = root.querySelector('[data-notif-menu]');

            if (!button || !menu) {
                return;
            }

            function openMenu() {
                menu.hidden = false;
                button.setAttribute('aria-expanded', 'true');
            }

            function closeMenu() {
                menu.hidden = true;
                button.setAttribute('aria-expanded', 'false');
            }

            button.addEventListener('click', function (event) {
                event.stopPropagation();
                if (menu.hidden) {
                    openMenu();
                } else {
                    closeMenu();
                }
            });

            document.addEventListener('click', function (event) {
                if (!menu.hidden && !menu.contains(event.target) && !button.contains(event.target)) {
                    closeMenu();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeMenu();
                }
            });
        });
    })();
</script>
