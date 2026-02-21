<!-- Shared theme tokens mapped from login/cadastro to keep visual consistency across auth and dashboard pages. -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="keywords" content="{{ $metaKeywordsContent ?? '' }}">
<style>
    :root {
        color-scheme: light;
        --bg-main: #f6f7f3;
        --bg-soft: #eef3f8;
        --card: #ffffff;
        --text-main: #112033;
        --text-soft: #4b5a71;
        --line: #dce4ee;
        --brand: #1f5fe0;
        --brand-dark: #1545a8;
        --ok-bg: #ecfdf3;
        --ok-text: #13663d;
        --ok-line: #92e6b2;
        --error-bg: #fff1f1;
        --error-text: #a42323;
        --error-line: #ffcccc;
        --radius-lg: 18px;
        --radius-md: 14px;
        --radius-sm: 12px;
        --shadow-card: 0 10px 30px rgba(17, 32, 51, 0.08);
        --shadow-soft: 0 4px 18px rgba(17, 32, 51, 0.05);
    }
</style>
@if (!empty($adsenseHeadScript))
    {!! $adsenseHeadScript !!}
@endif
@include('partials.lgpd-metrics-consent')
