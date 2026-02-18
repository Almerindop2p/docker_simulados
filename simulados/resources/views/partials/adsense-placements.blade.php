@php
    $showAdsensePlacements = (bool) ($adsenseEnabled ?? false);
    $horizontalAdCode = (string) ($adsenseHorizontalCode ?? '');
    $verticalAdCode = (string) ($adsenseVerticalCode ?? '');
@endphp

@if ($showAdsensePlacements)
    <style>
        .adsense-global-wrapper {
            width: min(1200px, calc(100% - 24px));
            margin: 18px auto 0;
            padding: 0 0 12px;
        }
        .adsense-global-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) clamp(180px, 20vw, 260px);
            gap: 14px;
            align-items: start;
        }
        .adsense-slot {
            background: #e7eaef;
            border: 1px dashed #b6bfcb;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(16, 36, 63, 0.08);
            overflow: hidden;
        }
        .adsense-placeholder {
            min-height: inherit;
            display: grid;
            place-items: center;
            color: #5f6e83;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            text-align: center;
            padding: 8px;
        }
        .adsense-slot-horizontal {
            min-height: 96px;
            padding: 10px;
        }
        .adsense-slot-vertical {
            min-height: 280px;
            padding: 10px;
        }
        @media (max-width: 980px) {
            .adsense-global-grid {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 720px) {
            .adsense-global-wrapper {
                width: calc(100% - 12px);
                margin-top: 14px;
            }
            .adsense-slot-horizontal {
                min-height: 74px;
                padding: 8px;
            }
            .adsense-slot-vertical {
                min-height: 170px;
                padding: 8px;
            }
        }
    </style>

    <div class="adsense-global-wrapper" aria-label="Publicidade">
        <div class="adsense-global-grid">
            <div class="adsense-slot adsense-slot-horizontal">
                @if ($horizontalAdCode !== '')
                    {!! $horizontalAdCode !!}
                @else
                    <div class="adsense-placeholder">Espaco anuncio horizontal</div>
                @endif
            </div>
            <aside class="adsense-slot adsense-slot-vertical" aria-label="Publicidade lateral">
                @if ($verticalAdCode !== '')
                    {!! $verticalAdCode !!}
                @else
                    <div class="adsense-placeholder">Espaco anuncio vertical</div>
                @endif
            </aside>
        </div>
    </div>
@endif
