<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Laporan LAF Project' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; border-bottom: 3px solid #2F3E2F; padding-bottom: 12px; margin-bottom: 15px; }
        .header h1 { font-size: 18px; font-weight: 800; color: #2F3E2F; letter-spacing: 2px; }
        .header .subtitle { font-size: 14px; font-weight: 700; color: #444; margin-top: 4px; }
        .header .meta { font-size: 9px; color: #666; margin-top: 6px; }
        .filter-info { background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px; padding: 8px 12px; margin-bottom: 12px; font-size: 9px; }
        .filter-info span { font-weight: 700; color: #2F3E2F; }
        .stats-row { display: table; width: 100%; margin-bottom: 12px; }
        .stat-box { display: table-cell; text-align: center; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .stat-box .value { font-size: 16px; font-weight: 800; }
        .stat-box .label { font-size: 8px; color: #666; text-transform: uppercase; }
        .stat-green .value { color: #155724; }
        .stat-red .value { color: #991b1b; }
        .stat-blue .value { color: #1e40af; }
        .stat-yellow .value { color: #92400e; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 9px; }
        th { background: #2F3E2F; color: #fff; padding: 6px 5px; text-align: left; font-weight: 700; font-size: 8px; text-transform: uppercase; }
        td { padding: 5px 5px; border-bottom: 1px solid #eee; }
        tr:nth-child(even) { background: #fafafa; }
        tfoot td { background: #f0f0f0; font-weight: 800; border-top: 2px solid #2F3E2F; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 8px; font-weight: 600; }
        .badge-green { background: #d4edda; color: #155724; }
        .badge-red { background: #f8d7da; color: #721c24; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-purple { background: #e0e7ff; color: #3730a3; }
        .footer { text-align: center; font-size: 8px; color: #aaa; border-top: 1px solid #ddd; padding-top: 8px; margin-top: 15px; }
        .page-break { page-break-before: always; }
        @yield('extra-styles')
    </style>
</head>
<body>
    <div class="header">
        <h1>LAF PROJECT</h1>
        <div class="subtitle">{{ $title }}</div>
        <div class="meta">Dicetak pada: {{ now()->format('d/m/Y H:i') }} WIB — oleh {{ auth()->user()->name ?? 'System' }}</div>
    </div>

    @if(isset($filterInfo))
    <div class="filter-info">
        {!! $filterInfo !!}
    </div>
    @endif

    @yield('content')

    <div class="footer">
        LAF Project — Sistem Inventori &amp; Penjualan &bull; {{ now()->format('Y') }}
    </div>
</body>
</html>
