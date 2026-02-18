@php
    $showAdsensePlacements = (bool) ($adsenseEnabled ?? false);
    $horizontalAdCode = (string) ($adsenseHorizontalCode ?? '');
    $verticalAdCode = (string) ($adsenseVerticalCode ?? '');
@endphp

@if ($showAdsensePlacements && ($horizontalAdCode !== '' || $verticalAdCode !== ''))
    <style>
        .adsense-global-wrapper {
            position: fixed;
            inset: auto 0 0 0;
            z-index: 32;
            pointer-events: none;
        }
        .adsense-slot {
            pointer-events: auto;
            background: #fff;
            border: 1px solid #dbe5f2;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(16, 36, 63, 0.12);
            overflow: hidden;
        }
        .adsense-slot-horizontal {
            width: min(960px, calc(100vw - 18px));
            min-height: 86px;
            margin: 0 auto 8px;
            padding: 10px;
        }
        .adsense-slot-vertical {
            position: fixed;
            right: 10px;
            top: 52%;
            transform: translateY(-50%);
            width: min(180px, 16vw);
            min-width: 120px;
            min-height: 280px;
            max-height: 70vh;
            padding: 10px;
            z-index: 31;
            overflow: auto;
        }
        @media (max-width: 1100px) {
            .adsense-slot-vertical {
                display: none;
            }
        }
        @media (max-width: 720px) {
            .adsense-slot-horizontal {
                width: calc(100vw - 12px);
                min-height: 74px;
                padding: 8px;
                margin-bottom: 6px;
            }
        }
    </style>

    @if ($verticalAdCode !== '')
        <aside class="adsense-slot adsense-slot-vertical" aria-label="Publicidade lateral">
            {!! $verticalAdCode !!}
        </aside>
    @endif

    @if ($horizontalAdCode !== '')
        <div class="adsense-global-wrapper" aria-label="Publicidade inferior">
            <div class="adsense-slot adsense-slot-horizontal">
                {!! $horizontalAdCode !!}
            </div>
        </div>
    @endif
@endif
