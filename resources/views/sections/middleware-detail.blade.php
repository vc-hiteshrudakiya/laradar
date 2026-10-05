@extends('laradar::layouts.laradar')
@section('content')
@php
$mwName      = $middlewareName;
$mwRoutes    = collect($data['routes'] ?? [])->filter(fn($r) => in_array($mwName, $r['middleware'] ?? []))->values()->all();
$isAuth      = str_contains(strtolower($mwName), 'auth');
$isThrottle  = str_contains(strtolower($mwName), 'throttle');
$apiRoutes   = array_filter($mwRoutes, fn($r) => str_starts_with($r['uri'] ?? '', 'api/'));
@endphp

<div class="detail-page">

<div class="detail-header reveal">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:20px;flex-wrap:wrap;">
        <div style="display:flex;align-items:flex-start;gap:16px;flex:1;min-width:0;">
            <div class="detail-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div style="min-width:0">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:5px;">
                    <h1 class="detail-title">{{ $mwName }}</h1>
                    @if($isAuth)<span style="font-family:var(--font-mono);font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;background:rgba(255,45,32,.08);color:#ff2d20;border:1px solid rgba(255,45,32,.2);">AUTH</span>@endif
                    @if($isThrottle)<span style="font-family:var(--font-mono);font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;background:rgba(255,45,32,.08);color:#ff2d20;border:1px solid rgba(255,45,32,.2);">RATE LIMIT</span>@endif
                </div>
                <span class="detail-ns">Middleware · used on {{ count($mwRoutes) }} {{ Str::plural('route', count($mwRoutes)) }}</span>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-shrink:0;flex-wrap:wrap;">
            <div class="detail-stat"><span class="detail-stat-val">{{ count($mwRoutes) }}</span><span class="detail-stat-lbl">Routes</span></div>
            <div class="detail-stat"><span class="detail-stat-val">{{ count($apiRoutes) }}</span><span class="detail-stat-lbl">API Routes</span></div>
        </div>
    </div>
</div>

<div class="detail-body">
<div class="detail-main">

    @if(!empty($mwRoutes))
    <div class="detail-card reveal">
        <div class="detail-card-head">
            <p class="detail-card-title">Routes Using This Middleware</p>
            <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);">{{ count($mwRoutes) }} routes</span>
        </div>
        @foreach($mwRoutes as $ri => $rr)
        @php
            $rVerb = collect($rr['methods'] ?? [])->filter(fn($v) => $v !== 'HEAD')->first();
            $rName = $rr['name'] ?? '';
            $rId   = ($rName !== '' && preg_match('/^[a-zA-Z0-9_.\-]+$/', $rName)) ? $rName : array_search($rr, $data['routes'] ?? []);
        @endphp
        <div class="mwd-route">
            @if($rVerb)<span class="mwd-verb">{{ $rVerb }}</span>@endif
            <span class="mwd-uri">/{{ $rr['uri'] ?? '' }}</span>
            @if($rName)<span class="mwd-name">{{ $rName }}</span>@endif
            @if($rId !== false)
            <a href="{{ route('laradar.route.detail', $rId) }}" class="mwd-link"
               onclick="event.preventDefault(); openDetailAjax('routes', this.href)">→</a>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <div class="detail-card reveal">
        <div class="detail-card-head"><p class="detail-card-title">Routes</p></div>
        <div style="padding:40px;text-align:center;color:var(--text-faint);font-size:13px;">No routes found for this middleware in the current scan.</div>
    </div>
    @endif

</div>{{-- .detail-main --}}

<div class="detail-sidebar">
    <div class="detail-scard reveal">
        <div class="detail-scard-head"><p class="detail-scard-title">Info</p></div>
        <div class="detail-scard-body">
            <div class="detail-ir">
                <span class="detail-il">Alias</span>
                <span class="detail-iv" style="font-family:var(--font-mono);color:#ff2d20;">{{ $mwName }}</span>
            </div>
            <div class="detail-ir">
                <span class="detail-il">Total Routes</span>
                <span class="detail-iv" style="color:#ff2d20;">{{ count($mwRoutes) }}</span>
            </div>
            <div class="detail-ir">
                <span class="detail-il">Auth Guard</span>
                <span class="detail-iv" style="color:{{ $isAuth ? 'var(--emerald)' : 'var(--text-faint)' }}">{{ $isAuth ? 'Yes' : 'No' }}</span>
            </div>
            <div class="detail-ir">
                <span class="detail-il">Rate Limiting</span>
                <span class="detail-iv" style="color:{{ $isThrottle ? 'var(--emerald)' : 'var(--text-faint)' }}">{{ $isThrottle ? 'Yes' : 'No' }}</span>
            </div>
        </div>
    </div>

    <div class="detail-scard reveal">
        <div class="detail-scard-head"><p class="detail-scard-title">Quick Actions</p></div>
        <div class="detail-scard-body">
            <a href="{{ route('laradar.middleware') }}" class="detail-act"
               onclick="event.preventDefault(); showList('middleware', true)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                All middleware
            </a>
            <a href="{{ route('laradar.routes') }}" class="detail-act"
               onclick="event.preventDefault(); showList('routes', true)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                View routes
            </a>
        </div>
    </div>
</div>

</div>{{-- .detail-body --}}
</div>{{-- .detail-page --}}
@endsection
