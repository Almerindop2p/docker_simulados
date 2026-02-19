@php
    $enabled = (bool) ($adsenseEnabled ?? false);
    $formatName = strtolower((string) ($format ?? 'horizontal'));
    $formatCodes = is_array($adsenseFormatCodes ?? null) ? $adsenseFormatCodes : [];
    $adCode = trim((string) ($formatCodes[$formatName] ?? ''));
    $slotClass = trim((string) ($slotClass ?? ''));
    $placeholderClass = trim((string) ($placeholderClass ?? ''));
    $ariaLabel = (string) ($ariaLabel ?? 'Publicidade');
    $placeholder = (string) ($placeholder ?? ($formatName === 'vertical' ? 'Espaco anuncio vertical' : 'Espaco anuncio horizontal'));
    $tagName = strtolower((string) ($tag ?? 'div'));

    if (!in_array($tagName, ['div', 'section', 'article', 'aside'], true)) {
        $tagName = 'div';
    }
@endphp

@if ($enabled)
    <{{ $tagName }} @if ($slotClass !== '') class="{{ $slotClass }}" @endif aria-label="{{ $ariaLabel }}">
        @if ($adCode !== '')
            {!! $adCode !!}
        @else
            <div @if ($placeholderClass !== '') class="{{ $placeholderClass }}" @endif>{{ $placeholder }}</div>
        @endif
    </{{ $tagName }}>
@endif
