@extends('laradar::layouts.laradar')
@section('content')
@php
$m          = $moduleData;
$totalItems = ($m['controllers'] ?? 0) + ($m['models'] ?? 0) + ($m['routes'] ?? 0) + ($m['jobs'] ?? 0) + ($m['events'] ?? 0) + ($m['services'] ?? 0);
@endphp

<div class="detail-page">

<div class="detail-header reveal">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:20px;flex-wrap:wrap;">
        <div style="display:flex;align-items:flex-start;gap:16px;flex:1;min-width:0;">
            <div class="detail-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div style="min-width:0">
                <h1 class="detail-title">{{ $m['name'] }}</h1>
                <span class="mod-path">{{ $m['path'] }}</span>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-shrink:0;flex-wrap:wrap;">
            <div class="detail-stat"><span class="detail-stat-val">{{ $m['controllers'] }}</span><span class="detail-stat-lbl">Controllers</span></div>
            <div class="detail-stat"><span class="detail-stat-val">{{ $m['models'] }}</span><span class="detail-stat-lbl">Models</span></div>
            <div class="detail-stat"><span class="detail-stat-val">{{ $m['routes'] }}</span><span class="detail-stat-lbl">Routes</span></div>
        </div>
    </div>
</div>

<div class="detail-body">
<div class="detail-main">

    <div class="detail-card reveal">
        <div class="detail-card-head"><p class="detail-card-title">Module Overview</p></div>
        <div class="detail-card-body">
            <div class="mod-stats-grid">
                <div class="mod-stat-big"><span class="mod-stat-big-val">{{ $m['controllers'] ?? 0 }}</span><span class="mod-stat-big-lbl">Controllers</span></div>
                <div class="mod-stat-big"><span class="mod-stat-big-val">{{ $m['models'] ?? 0 }}</span><span class="mod-stat-big-lbl">Models</span></div>
                <div class="mod-stat-big"><span class="mod-stat-big-val">{{ $m['routes'] ?? 0 }}</span><span class="mod-stat-big-lbl">Routes</span></div>
                <div class="mod-stat-big"><span class="mod-stat-big-val">{{ $m['jobs'] ?? 0 }}</span><span class="mod-stat-big-lbl">Jobs</span></div>
                <div class="mod-stat-big"><span class="mod-stat-big-val">{{ $m['events'] ?? 0 }}</span><span class="mod-stat-big-lbl">Events</span></div>
                <div class="mod-stat-big"><span class="mod-stat-big-val">{{ $m['services'] ?? 0 }}</span><span class="mod-stat-big-lbl">Services</span></div>
            </div>
        </div>
    </div>

    @if($totalItems > 0)
    <div class="detail-card reveal">
        <div class="detail-card-head"><p class="detail-card-title">Breakdown</p></div>
        <div class="detail-card-body">
            @php $maxVal = max($m['controllers'] ?? 0, $m['models'] ?? 0, $m['routes'] ?? 0, $m['jobs'] ?? 0, $m['events'] ?? 0, $m['services'] ?? 0, 1); @endphp
            @foreach(['Controllers' => $m['controllers'], 'Models' => $m['models'], 'Routes' => $m['routes'], 'Jobs' => $m['jobs'], 'Events' => $m['events'], 'Services' => $m['services']] as $label => $val)
            <div class="mod-bar-row">
                <span class="mod-bar-lbl">{{ $label }}</span>
                <div class="mod-bar-track"><div class="mod-bar-fill" style="width:{{ round($val / $maxVal * 100) }}%"></div></div>
                <span class="mod-bar-val">{{ $val }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>{{-- .detail-main --}}

<div class="detail-sidebar">
    <div class="detail-scard reveal">
        <div class="detail-scard-head"><p class="detail-scard-title">Info</p></div>
        <div class="detail-scard-body">
            <div class="detail-ir">
                <span class="detail-il">Path</span>
                <span class="detail-iv" style="font-family:var(--font-mono);font-size:10.5px;">{{ $m['path'] }}</span>
            </div>
            <div class="detail-ir">
                <span class="detail-il">Total Items</span>
                <span class="detail-iv" style="color:#ff2d20;">{{ $totalItems }}</span>
            </div>
        </div>
    </div>

    <div class="detail-scard reveal">
        <div class="detail-scard-head"><p class="detail-scard-title">Quick Actions</p></div>
        <div class="detail-scard-body">
            <a href="{{ route('laradar.modules') }}" class="detail-act"
               onclick="event.preventDefault(); showList('modules', true)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                All modules
            </a>
            <a href="{{ route('laradar.controllers') }}" class="detail-act"
               onclick="event.preventDefault(); showList('controllers', true)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                Controllers
            </a>
        </div>
    </div>
</div>

</div>{{-- .detail-body --}}
</div>{{-- .detail-page --}}
@endsection
