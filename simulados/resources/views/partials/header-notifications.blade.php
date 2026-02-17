@php
    $notifData = $headerNotifications ?? ['title' => 'Notificacoes', 'items' => [], 'count' => 0];
    $notifItems = $notifData['items'] ?? [];
    $notifCount = (int) ($notifData['count'] ?? 0);
    $notifMenuId = 'notification-menu-' . uniqid();
    $notifModalId = 'notification-modal-' . uniqid();
@endphp

<div class="notif-wrap" data-notif-root data-feed-url="{{ route('notifications.feed') }}">
    <button
        class="notif-btn {{ $notifCount > 0 ? 'has-unread' : '' }}"
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
            <h3 data-notif-title>{{ $notifData['title'] ?? 'Notificacoes' }}</h3>
        </header>

        <p class="notif-empty" data-notif-empty @if (!empty($notifItems)) hidden @endif>Nenhuma notificacao no momento.</p>

        <ul class="notif-list" data-notif-list @if (empty($notifItems)) hidden @endif>
            @foreach ($notifItems as $item)
                @php
                    $typeClass = match ($item['type'] ?? 'info') {
                        'warning' => 'is-warning',
                        'success' => 'is-success',
                        'danger' => 'is-danger',
                        default => 'is-info',
                    };
                    $isUnread = !($item['read'] ?? false);
                    $itemUrl = $item['url'] ?? null;
                    $itemAction = $item['action'] ?? ($itemUrl ? 'link' : 'none');
                    $markReadUrl = $item['mark_read_url'] ?? null;
                    $modalTitle = $item['modal_title'] ?? ($item['title'] ?? 'Notificacao');
                    $modalMessage = $item['modal_message'] ?? ($item['message'] ?? '');
                @endphp
                <li class="notif-item {{ $typeClass }} {{ $isUnread ? 'is-unread' : '' }}">
                    @if ($itemAction === 'link' && $itemUrl)
                        <a
                            href="{{ $itemUrl }}"
                            class="notif-link"
                            data-notif-link-trigger
                            @if ($markReadUrl)
                                data-mark-read-url="{{ $markReadUrl }}"
                            @endif
                        >
                            <strong>{{ $item['title'] ?? 'Notificacao' }}</strong>
                            <span>{{ $item['message'] ?? '' }}</span>
                            @if (!empty($item['created_at']))
                                <small class="notif-meta">{{ $item['created_at'] }}</small>
                            @endif
                        </a>
                    @elseif ($itemAction === 'modal')
                        <button
                            type="button"
                            class="notif-link notif-link-btn"
                            data-notif-modal-trigger
                            data-modal-title="{{ $modalTitle }}"
                            data-modal-message="{{ $modalMessage }}"
                            @if ($markReadUrl)
                                data-mark-read-url="{{ $markReadUrl }}"
                            @endif
                        >
                            <strong>{{ $item['title'] ?? 'Notificacao' }}</strong>
                            <span>{{ $item['message'] ?? '' }}</span>
                            @if (!empty($item['created_at']))
                                <small class="notif-meta">{{ $item['created_at'] }}</small>
                            @endif
                        </button>
                    @else
                        <div class="notif-link">
                            <strong>{{ $item['title'] ?? 'Notificacao' }}</strong>
                            <span>{{ $item['message'] ?? '' }}</span>
                            @if (!empty($item['created_at']))
                                <small class="notif-meta">{{ $item['created_at'] }}</small>
                            @endif
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    </section>

    <div id="{{ $notifModalId }}" class="notif-modal" hidden data-notif-modal>
        <div class="notif-modal-card" role="dialog" aria-modal="true" aria-label="Detalhes da notificacao">
            <header class="notif-modal-head">
                <h4 data-notif-modal-title>Notificacao</h4>
                <button type="button" class="notif-modal-close" aria-label="Fechar" data-notif-modal-close>X</button>
            </header>
            <div class="notif-modal-body" data-notif-modal-body></div>
            <footer class="notif-modal-footer">
                <button type="button" class="notif-modal-action" data-notif-modal-close>Fechar</button>
            </footer>
        </div>
    </div>
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
        transition: background-color .2s ease, border-color .2s ease, color .2s ease, box-shadow .2s ease;
    }

    .notif-btn.has-unread {
        border-color: #1f5fe0;
        background: linear-gradient(135deg, #1f5fe0, #4c83f0);
        color: #fff;
        box-shadow: 0 8px 18px rgba(31, 95, 224, 0.35);
    }

    .notif-btn.has-unread .notif-badge {
        border-color: #1f5fe0;
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
        width: min(380px, calc(100vw - 24px));
        border: 1px solid #d4dfef;
        border-radius: 14px;
        background: #fff;
        box-shadow: var(--shadow-soft);
        padding: 8px;
        z-index: 65;
    }

    .notif-menu[hidden] {
        display: none !important;
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
        background: #fff;
        transition: background-color .2s ease, border-color .2s ease, box-shadow .2s ease;
    }

    .notif-item.is-unread {
        border-color: #c8daf7;
        background: #f4f8ff;
        box-shadow: inset 3px 0 0 #1f5fe0;
    }

    .notif-item.is-unread.is-warning {
        border-color: #f1d7ad;
        background: #fffaf1;
    }

    .notif-item.is-unread.is-success {
        border-color: #c6e7d2;
        background: #f2fbf5;
    }

    .notif-item.is-unread.is-danger {
        border-color: #f2c4cc;
        background: #fff4f6;
    }

    .notif-link {
        width: 100%;
        display: grid;
        gap: 4px;
        padding: 10px;
        color: #1f3f67;
        text-decoration: none;
    }

    .notif-link-btn {
        border: 0;
        background: transparent;
        text-align: left;
        cursor: pointer;
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

    .notif-meta {
        font-size: 11px;
        color: #6a7f99;
    }

    .notif-modal {
        position: fixed;
        inset: 0;
        background: rgba(16, 36, 63, 0.45);
        z-index: 80;
        display: grid;
        place-items: center;
        padding: 16px;
    }

    .notif-modal[hidden] {
        display: none !important;
    }

    .notif-modal-card {
        width: min(520px, 100%);
        border: 1px solid #d4dfef;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 20px 40px rgba(18, 39, 67, 0.2);
        overflow: hidden;
    }

    .notif-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 14px;
        border-bottom: 1px solid #e5edf9;
    }

    .notif-modal-head h4 {
        margin: 0;
        color: #1f3f67;
        font-size: 16px;
    }

    .notif-modal-close {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        border: 1px solid #d4dfef;
        background: #fff;
        color: #365a81;
        cursor: pointer;
        font-weight: 700;
    }

    .notif-modal-body {
        padding: 14px;
        color: #305174;
        line-height: 1.65;
        white-space: pre-wrap;
        word-break: break-word;
    }

    .notif-modal-footer {
        border-top: 1px solid #e5edf9;
        padding: 10px 14px;
        display: flex;
        justify-content: flex-end;
    }

    .notif-modal-action {
        min-height: 38px;
        border: 0;
        border-radius: 9px;
        background: linear-gradient(135deg, #1f5fe0, #4c83f0);
        color: #fff;
        padding: 8px 14px;
        font-weight: 700;
        cursor: pointer;
    }
</style>

<script>
    (function () {
        var roots = document.querySelectorAll('[data-notif-root]');

        if (!roots.length) {
            return;
        }

        var csrfToken = '{{ csrf_token() }}';
        var POLL_INTERVAL_MS = 20000;

        roots.forEach(function (root) {
            var button = root.querySelector('[data-notif-button]');
            var menu = root.querySelector('[data-notif-menu]');
            var list = root.querySelector('[data-notif-list]');
            var empty = root.querySelector('[data-notif-empty]');
            var title = root.querySelector('[data-notif-title]');
            var modal = root.querySelector('[data-notif-modal]');
            var modalTitle = root.querySelector('[data-notif-modal-title]');
            var modalBody = root.querySelector('[data-notif-modal-body]');
            var feedUrl = root.getAttribute('data-feed-url');

            if (!button || !menu || !list || !empty || !title) {
                return;
            }

            function updateBadge(unreadCount) {
                var badge = root.querySelector('.notif-badge');
                var count = Number(unreadCount);

                if (!Number.isFinite(count)) {
                    return;
                }

                if (count <= 0) {
                    button.classList.remove('has-unread');
                    if (badge) {
                        badge.remove();
                    }
                    return;
                }

                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'notif-badge';
                    button.appendChild(badge);
                }

                button.classList.add('has-unread');
                badge.textContent = count > 99 ? '99+' : String(count);
            }

            function syncBadgeWithDomUnread() {
                var unreadCount = root.querySelectorAll('.notif-item.is-unread').length;
                updateBadge(unreadCount);
            }

            function openMenu() {
                menu.hidden = false;
                button.setAttribute('aria-expanded', 'true');
            }

            function closeMenu() {
                menu.hidden = true;
                button.setAttribute('aria-expanded', 'false');
            }

            function openModal(titleText, bodyText) {
                if (!modal || !modalTitle || !modalBody) {
                    return;
                }

                modalTitle.textContent = titleText || 'Notificacao';
                modalBody.textContent = bodyText || '';
                modal.hidden = false;
            }

            function closeModal() {
                if (!modal) {
                    return;
                }

                modal.hidden = true;
            }

            function markItemAsRead(itemNode) {
                if (!itemNode || !itemNode.classList.contains('is-unread')) {
                    return;
                }

                itemNode.classList.remove('is-unread');
                syncBadgeWithDomUnread();
            }

            function markRead(url, itemNode, callback) {
                if (!url) {
                    if (typeof callback === 'function') {
                        callback();
                    }
                    return;
                }

                markItemAsRead(itemNode);

                fetch(url, {
                    method: 'PATCH',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                    .then(function (response) {
                        return response.json().catch(function () {
                            return {};
                        });
                    })
                    .then(function (data) {
                        if (typeof data.unread_count === 'number') {
                            updateBadge(data.unread_count);
                        }

                        if (typeof callback === 'function') {
                            callback();
                        }
                    })
                    .catch(function () {
                        if (typeof callback === 'function') {
                            callback();
                        }
                    });
            }

            function createItemNode(item) {
                var li = document.createElement('li');
                var type = item && item.type ? item.type : 'info';
                var typeClass = type === 'warning'
                    ? 'is-warning'
                    : type === 'success'
                        ? 'is-success'
                        : type === 'danger'
                            ? 'is-danger'
                            : 'is-info';
                var isRead = !!(item && item.read);
                li.className = 'notif-item ' + typeClass + (isRead ? '' : ' is-unread');

                var action = item && item.action ? item.action : ((item && item.url) ? 'link' : 'none');
                var titleText = item && item.title ? item.title : 'Notificacao';
                var messageText = item && item.message ? item.message : '';
                var createdAt = item && item.created_at ? item.created_at : '';
                var markReadUrl = item && item.mark_read_url ? item.mark_read_url : '';

                var container;
                if (action === 'link' && item && item.url) {
                    container = document.createElement('a');
                    container.href = item.url;
                    container.className = 'notif-link';
                    container.setAttribute('data-notif-link-trigger', '');
                    if (markReadUrl) {
                        container.setAttribute('data-mark-read-url', markReadUrl);
                    }
                } else if (action === 'modal') {
                    container = document.createElement('button');
                    container.type = 'button';
                    container.className = 'notif-link notif-link-btn';
                    container.setAttribute('data-notif-modal-trigger', '');
                    container.setAttribute('data-modal-title', item && item.modal_title ? item.modal_title : titleText);
                    container.setAttribute('data-modal-message', item && item.modal_message ? item.modal_message : messageText);
                    if (markReadUrl) {
                        container.setAttribute('data-mark-read-url', markReadUrl);
                    }
                } else {
                    container = document.createElement('div');
                    container.className = 'notif-link';
                }

                var strong = document.createElement('strong');
                strong.textContent = titleText;

                var span = document.createElement('span');
                span.textContent = messageText;

                container.appendChild(strong);
                container.appendChild(span);

                if (createdAt) {
                    var small = document.createElement('small');
                    small.className = 'notif-meta';
                    small.textContent = createdAt;
                    container.appendChild(small);
                }

                li.appendChild(container);
                return li;
            }

            function renderNotifications(data) {
                var notificationTitle = data && data.title ? data.title : 'Notificacoes';
                var items = data && Array.isArray(data.items) ? data.items : [];
                var count = data && typeof data.count === 'number' ? data.count : 0;

                title.textContent = notificationTitle;
                updateBadge(count);

                list.innerHTML = '';

                if (!items.length) {
                    list.hidden = true;
                    empty.hidden = false;
                    return;
                }

                empty.hidden = true;
                list.hidden = false;

                items.forEach(function (item) {
                    list.appendChild(createItemNode(item));
                });
            }

            function refreshNotificationsSilently() {
                if (!feedUrl) {
                    return;
                }

                fetch(feedUrl, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(function (response) {
                        return response.json().catch(function () {
                            return {};
                        });
                    })
                    .then(function (data) {
                        if (!data || !data.ok || !data.notifications) {
                            return;
                        }

                        renderNotifications(data.notifications);
                    })
                    .catch(function () {});
            }

            button.addEventListener('click', function (event) {
                event.stopPropagation();
                if (menu.hidden) {
                    openMenu();
                } else {
                    closeMenu();
                }
            });

            root.addEventListener('click', function (event) {
                var modalTrigger = event.target.closest('[data-notif-modal-trigger]');
                if (modalTrigger && root.contains(modalTrigger)) {
                    event.preventDefault();
                    var readUrl = modalTrigger.getAttribute('data-mark-read-url');
                    var itemNode = modalTrigger.closest('.notif-item');
                    openModal(
                        modalTrigger.getAttribute('data-modal-title') || 'Notificacao',
                        modalTrigger.getAttribute('data-modal-message') || ''
                    );
                    markRead(readUrl, itemNode);
                    return;
                }

                var linkTrigger = event.target.closest('[data-notif-link-trigger]');
                if (linkTrigger && root.contains(linkTrigger)) {
                    var readUrlForLink = linkTrigger.getAttribute('data-mark-read-url');
                    if (!readUrlForLink) {
                        return;
                    }

                    event.preventDefault();

                    var destination = linkTrigger.getAttribute('href');
                    var itemForLink = linkTrigger.closest('.notif-item');
                    var hasNavigated = false;

                    function navigateNow() {
                        if (hasNavigated) {
                            return;
                        }

                        hasNavigated = true;
                        if (destination) {
                            window.location.href = destination;
                        }
                    }

                    markRead(readUrlForLink, itemForLink, navigateNow);
                    window.setTimeout(navigateNow, 350);
                    return;
                }

                var closeBtn = event.target.closest('[data-notif-modal-close]');
                if (closeBtn && root.contains(closeBtn)) {
                    event.preventDefault();
                    closeModal();
                }
            });

            document.addEventListener('click', function (event) {
                if (!menu.hidden && !menu.contains(event.target) && !button.contains(event.target)) {
                    closeMenu();
                }

                if (modal && !modal.hidden && event.target === modal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeMenu();
                    closeModal();
                }
            });

            if (feedUrl) {
                window.setInterval(function () {
                    if (document.visibilityState === 'visible') {
                        refreshNotificationsSilently();
                    }
                }, POLL_INTERVAL_MS);

                document.addEventListener('visibilitychange', function () {
                    if (document.visibilityState === 'visible') {
                        refreshNotificationsSilently();
                    }
                });
            }
        });
    })();
</script>
