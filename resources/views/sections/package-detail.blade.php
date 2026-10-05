@extends('laradar::layouts.laradar')
@section('content')
@php
$p       = $packageData;
$hasDocs = !empty($p['docs']);
$isDev   = !empty($p['dev']);

$dotHexColors = [
    'laravel'=>'#FF2D20','pink'=>'#F472B6','purple'=>'#C084FC','red'=>'#F87171',
    'blue'=>'#60A5FA','orange'=>'#FB923C','violet'=>'#A78BFA','amber'=>'#FBBF24',
    'sky'=>'#38BDF8','emerald'=>'#34D399','green'=>'#4ADE80','teal'=>'#2DD4BF',
    'slate'=>'#94A3B8','cyan'=>'#818CF8','indigo'=>'#818CF8','rose'=>'#FB7185',
];
$dotHex   = $dotHexColors[$p['color'] ?? ''] ?? '#94A3B8';
$initials = strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $p['name'] ?? 'PKG'), 0, 2)))) ?: 'PK';

$similarPkgs  = collect($data['packages'] ?? [])->filter(fn($pkg) => $pkg['category'] === $p['category'] && $pkg['key'] !== $p['key'])->take(5)->values()->all();
$license      = $p['license'] ?? null;
$providers    = $p['providers'] ?? [];
$aliases      = $p['aliases'] ?? [];
$phpRequire   = $p['php_require'] ?? null;
$laravelReq   = $p['laravel_require'] ?? null;
@endphp

<div class="detail-page">

<div class="detail-header reveal">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:20px;flex-wrap:wrap;">
        <div style="display:flex;align-items:flex-start;gap:16px;flex:1;min-width:0;">
            <div style="width:50px;height:50px;border-radius:13px;background:{{ $dotHex }}18;border:1px solid {{ $dotHex }}40;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:16px;font-weight:800;color:{{ $dotHex }};">{{ $initials }}</div>
            <div style="min-width:0">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:6px;">
                    <h1 class="detail-title">{{ $p['name'] }}</h1>
                    @if($isDev)<span class="detail-badge">DEV</span>@endif
                    <span style="font-family:var(--font-mono);font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;background:{{ $dotHex }}15;color:{{ $dotHex }};border:1px solid {{ $dotHex }}30;">{{ $p['category'] }}</span>
                </div>
                <p class="detail-ns">{{ $p['description'] ?? '' }}</p>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-shrink:0;flex-wrap:wrap;">
            @if($p['version'])
            <div class="detail-stat"><span class="detail-stat-val" style="color:{{ $dotHex }};">v{{ $p['version'] }}</span><span class="detail-stat-lbl">Version</span></div>
            @endif
            <div class="detail-stat"><span class="detail-stat-val" style="color:{{ $isDev ? 'var(--amber)' : 'var(--emerald)' }}">{{ $isDev ? 'Dev' : 'Prod' }}</span><span class="detail-stat-lbl">Env</span></div>
        </div>
    </div>
</div>

<div class="detail-body">
<div class="detail-main">

    <div class="detail-card reveal">
        <div class="detail-card-head"><p class="detail-card-title">Package Details</p></div>
        <div class="detail-card-body">
            <div class="pkg-dg">
                <span class="detail-dl">Display Name</span>
                <span class="detail-dv">{{ $p['name'] }}</span>
                <span class="detail-dl">Composer Key</span>
                <span class="detail-dv" style="font-family:var(--font-mono);font-size:10.5px;color:var(--text-dim);display:flex;align-items:center;gap:6px;">
                    {{ $p['key'] }}
                    <button onclick="navigator.clipboard.writeText({{ json_encode($p['key'] ?? '') }})" title="Copy" style="background:transparent;border:none;color:var(--text-faint);cursor:pointer;padding:2px;border-radius:4px;display:inline-flex;" onmouseenter="this.style.color='var(--text)'" onmouseleave="this.style.color='var(--text-faint)'">
                        <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                </span>
                <span class="detail-dl">Category</span>
                <span class="detail-dv">{{ $p['category'] }}</span>
                <span class="detail-dl">Version</span>
                <span class="detail-dv" style="font-family:var(--font-mono);font-weight:700;color:{{ $dotHex }};">{{ $p['version'] ? 'v'.$p['version'] : '—' }}</span>
                <span class="detail-dl">Environment</span>
                <span class="detail-dv" style="font-weight:700;color:{{ $isDev ? 'var(--amber)' : 'var(--emerald)' }}">{{ $isDev ? 'Dev only (require-dev)' : 'Production (require)' }}</span>
                @if($p['description'])
                <span class="detail-dl">Description</span>
                <span class="detail-dv" style="color:var(--text-dim);">{{ $p['description'] }}</span>
                @endif
                @if($license)
                <span class="detail-dl">License</span>
                <span class="detail-dv"><span style="font-family:var(--font-mono);font-size:11px;font-weight:700;padding:2px 8px;border-radius:4px;background:rgba(255,45,32,.08);color:#ff2d20;border:1px solid rgba(255,45,32,.2);">{{ $license }}</span></span>
                @endif
                @if($phpRequire)
                <span class="detail-dl">PHP</span>
                <span class="detail-dv" style="font-family:var(--font-mono);font-size:11px;color:var(--text-dim);">{{ $phpRequire }}</span>
                @endif
                @if($laravelReq)
                <span class="detail-dl">Laravel</span>
                <span class="detail-dv" style="font-family:var(--font-mono);font-size:11px;color:var(--text-dim);">{{ $laravelReq }}</span>
                @endif
                @if($hasDocs)
                <span class="detail-dl">Docs</span>
                <span class="detail-dv">
                    <a href="{{ filter_var($p['docs'], FILTER_VALIDATE_URL) ? $p['docs'] : '#' }}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:4px;font-size:12px;font-weight:600;color:{{ $dotHex }};text-decoration:none;" onmouseenter="this.style.opacity='.7'" onmouseleave="this.style.opacity='1'">
                        Official Documentation <svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </span>
                @endif
            </div>
        </div>
    </div>

    @if(!empty($providers) || !empty($aliases))
    <div class="detail-card reveal">
        <div class="detail-card-head">
            <p class="detail-card-title">Laravel Integration</p>
        </div>
        @if(!empty($providers))
        <div style="padding:4px 0 8px;">
            <div style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-faint);padding:0 16px 8px;">Service Providers ({{ count($providers) }})</div>
            @foreach($providers as $provider)
            <div class="detail-mth">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#ff2d20" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span style="font-family:var(--font-mono);font-size:11.5px;color:var(--text);">{{ $provider }}</span>
            </div>
            @endforeach
        </div>
        @endif
        @if(!empty($aliases))
        <div style="padding:{{ !empty($providers) ? '0' : '4px' }} 0 8px;">
            <div style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-faint);padding:0 16px 8px;">Facades / Aliases ({{ count($aliases) }})</div>
            <div class="detail-card-body" style="display:flex;flex-wrap:wrap;gap:8px;">
                @foreach($aliases as $aliasName)
                <span class="detail-pill">{{ is_string($aliasName) ? $aliasName : '' }}</span>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    @if(!empty($similarPkgs))
    <div class="detail-card reveal">
        <div class="detail-card-head">
            <p class="detail-card-title">Other {{ $p['category'] }} Packages</p>
            <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);">{{ count($similarPkgs) }} more</span>
        </div>
        @foreach($similarPkgs as $sp)
        @php $spHex = $dotHexColors[$sp['color'] ?? ''] ?? '#94A3B8'; @endphp
        <div class="pkg-similar-item">
            <div style="width:32px;height:32px;border-radius:8px;background:{{ $spHex }}15;color:{{ $spHex }};border:1px solid {{ $spHex }}30;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;flex-shrink:0;">{{ strtoupper(substr($sp['name'] ?? 'P', 0, 1)) }}</div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:12.5px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $sp['name'] }}</div>
                <div style="font-family:var(--font-mono);font-size:10.5px;color:var(--text-faint);">{{ $sp['version'] ? 'v'.$sp['version'] : $sp['key'] }}</div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>{{-- .detail-main --}}

<div class="detail-sidebar">
    <div class="detail-scard reveal">
        <div class="detail-scard-head"><p class="detail-scard-title">Info</p></div>
        <div class="detail-scard-body">
            <div class="detail-ir">
                <span class="detail-il">Category</span>
                <span class="detail-iv" style="color:{{ $dotHex }};">{{ $p['category'] }}</span>
            </div>
            <div class="detail-ir">
                <span class="detail-il">Version</span>
                <span class="detail-iv">{{ $p['version'] ? 'v'.$p['version'] : 'Not specified' }}</span>
            </div>
            <div class="detail-ir">
                <span class="detail-il">Environment</span>
                <span class="detail-iv" style="color:{{ $isDev ? 'var(--amber)' : 'var(--emerald)' }}">{{ $isDev ? 'Dev only' : 'Production' }}</span>
            </div>
            <div class="detail-ir">
                <span class="detail-il">Documentation</span>
                <span class="detail-iv" style="color:{{ $hasDocs ? 'var(--emerald)' : 'var(--text-faint)' }}">{{ $hasDocs ? '✓ Available' : '—' }}</span>
            </div>
            @if($license)
            <div class="detail-ir">
                <span class="detail-il">License</span>
                <span class="detail-iv" style="color:#ff2d20;">{{ $license }}</span>
            </div>
            @endif
            @if($phpRequire)
            <div class="detail-ir">
                <span class="detail-il">Requires PHP</span>
                <span class="detail-iv" style="font-family:var(--font-mono);font-size:11px;">{{ $phpRequire }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="detail-scard reveal">
        <div class="detail-scard-head"><p class="detail-scard-title">Quick Actions</p></div>
        <div class="detail-scard-body">
            <a href="{{ route('laradar.packages') }}" class="detail-act"
               onclick="event.preventDefault(); showList('packages', true)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                All packages
            </a>
            @if($hasDocs)
            <a href="{{ filter_var($p['docs'], FILTER_VALIDATE_URL) ? $p['docs'] : '#' }}" target="_blank" rel="noopener" class="detail-act">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Open docs
            </a>
            @endif
        </div>
    </div>
</div>

</div>{{-- .detail-body --}}
</div>{{-- .detail-page --}}
@endsection
