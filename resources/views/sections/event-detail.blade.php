@extends('laradar::layouts.laradar')
@section('content')
@php
$e                = $eventData;
$props            = $e['properties'] ?? [];
$listeners        = $e['listeners'] ?? [];
$dispatchedBy     = $e['dispatched_by'] ?? [];
$isBroadcast      = !empty($e['should_broadcast']);
$isBroadcastNow   = !empty($e['broadcastNow']);
$broadcastChannel = $e['broadcast_channel'] ?? null;
$filePath      = 'app/' . str_replace('\\', '/', preg_replace('/^App\\\\/', '', $e['namespace'] ?? '')) . '/' . $e['name'] . '.php';
$absPath       = base_path(str_replace('/', DIRECTORY_SEPARATOR, $filePath));
$loc           = file_exists($absPath) ? count(file($absPath)) : null;
@endphp

<div class="detail-page">

<div class="detail-header reveal">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:20px;flex-wrap:wrap;">
        <div style="display:flex;align-items:flex-start;gap:16px;flex:1;min-width:0;">
            <div class="detail-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div style="min-width:0">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:5px;">
                    <h1 class="detail-title">{{ $e['name'] }}</h1>
                    @if($isBroadcast)<span class="detail-badge">BROADCAST</span>@endif
                    @if($isBroadcastNow)<span class="detail-badge">BROADCAST NOW</span>@endif
                </div>
                <span class="detail-ns">{{ $e['namespace'] ?? '' }}</span>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-shrink:0;flex-wrap:wrap;">
            <div class="detail-stat"><span class="detail-stat-val">{{ count($listeners) }}</span><span class="detail-stat-lbl">Listeners</span></div>
            <div class="detail-stat"><span class="detail-stat-val">{{ count($props) }}</span><span class="detail-stat-lbl">Properties</span></div>
            <div class="detail-stat"><span class="detail-stat-val">{{ $isBroadcast ? 'Yes' : 'No' }}</span><span class="detail-stat-lbl">Broadcast</span></div>
        </div>
    </div>
</div>

<div class="detail-body">
<div class="detail-main">

    <div class="detail-card reveal">
        <div class="detail-card-head"><p class="detail-card-title">Event Details</p></div>
        <div class="detail-card-body">
            <div class="evt-dg">
                <span class="detail-dl">Class</span>
                <span class="detail-dv">{{ $e['name'] }}</span>
                <span class="detail-dl">Namespace</span>
                <span class="detail-dv" style="font-family:var(--font-mono);font-size:10.5px;color:var(--text-dim);">{{ $e['namespace'] ?? '—' }}</span>
                <span class="detail-dl">File</span>
                <span class="detail-dv" style="font-family:var(--font-mono);font-size:10.5px;color:var(--text-dim);">{{ $filePath }}</span>
                <span class="detail-dl">Broadcast</span>
                <span class="detail-dv"><span style="font-weight:700;color:{{ $isBroadcast ? 'var(--emerald)' : 'var(--text-faint)' }}">{{ $isBroadcast ? 'Yes (ShouldBroadcast)' : 'No' }}</span></span>
                @if($isBroadcast)
                <span class="detail-dl">Broadcast Now</span>
                <span class="detail-dv"><span style="font-weight:700;color:{{ $isBroadcastNow ? 'var(--emerald)' : 'var(--text-faint)' }}">{{ $isBroadcastNow ? 'Yes (ShouldBroadcastNow)' : 'No' }}</span></span>
                @if($broadcastChannel)
                <span class="detail-dl">Channel</span>
                <span class="detail-dv" style="font-family:var(--font-mono);font-size:12px;font-weight:700;color:#ff2d20;">{{ $broadcastChannel }}</span>
                @endif
                @endif
            </div>
        </div>
    </div>

    @if(!empty($listeners))
    <div class="detail-card reveal">
        <div class="detail-card-head">
            <p class="detail-card-title">Listeners</p>
            <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);">{{ count($listeners) }} registered</span>
        </div>
        @foreach($listeners as $listener)
        <div class="evt-prop">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ff2d20" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
            <span style="font-family:var(--font-mono);font-size:12.5px;font-weight:700;color:var(--text);">{{ $listener }}</span>
        </div>
        @endforeach
    </div>
    @endif

    @if(!empty($dispatchedBy))
    <div class="detail-card reveal">
        <div class="detail-card-head">
            <p class="detail-card-title">Fired By</p>
            <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);">{{ count($dispatchedBy) }} class{{ count($dispatchedBy) !== 1 ? 'es' : '' }}</span>
        </div>
        @foreach($dispatchedBy as $caller)
        <div class="evt-prop">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#ff2d20" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
            <span style="font-family:var(--font-mono);font-size:12.5px;color:var(--text);">{{ $caller }}</span>
        </div>
        @endforeach
    </div>
    @endif

    @if(!empty($props))
    <div class="detail-card reveal">
        <div class="detail-card-head">
            <p class="detail-card-title">Payload / Properties</p>
            <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);">{{ count($props) }}</span>
        </div>
        @foreach($props as $prop)
        <div class="evt-prop">
            @if(!empty($prop['type']))<span style="font-family:var(--font-mono);font-size:10px;font-weight:700;padding:2px 8px;border-radius:4px;background:rgba(255,45,32,.08);color:#ff2d20;border:1px solid rgba(255,45,32,.2);">{{ $prop['type'] }}</span>@endif
            <span style="font-family:var(--font-mono);font-size:12.5px;font-weight:700;color:var(--text);">${{ $prop['name'] ?? ($prop['var'] ?? '') }}</span>
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
                <span class="detail-il">Broadcast</span>
                <span class="detail-iv" style="color:{{ $isBroadcast ? 'var(--emerald)' : 'var(--text-faint)' }}">{{ $isBroadcast ? 'Yes' : 'No' }}</span>
            </div>
            <div class="detail-ir">
                <span class="detail-il">Properties</span>
                <span class="detail-iv" style="color:#ff2d20;">{{ count($props) }}</span>
            </div>
            @if($loc)
            <div class="detail-ir">
                <span class="detail-il">Lines</span>
                <span class="detail-iv">{{ $loc }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="detail-scard reveal">
        <div class="detail-scard-head"><p class="detail-scard-title">Quick Actions</p></div>
        <div class="detail-scard-body">
            <a href="{{ route('laradar.events') }}" class="detail-act"
               onclick="event.preventDefault(); showList('events', true)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                All events
            </a>
        </div>
    </div>
</div>

</div>{{-- .detail-body --}}
</div>{{-- .detail-page --}}
@endsection
