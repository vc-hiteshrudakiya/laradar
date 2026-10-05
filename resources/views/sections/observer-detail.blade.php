@extends('laradar::layouts.laradar')
@section('content')
@php
$o            = $observerData;
$model        = $o['model'] ?? null;
$events       = $o['events'] ?? [];
$deps         = $o['dependencies'] ?? [];
$isQueued     = !empty($o['should_queue']);
$registration = $o['registration'] ?? null;
$filePath  = 'app/' . str_replace('\\', '/', preg_replace('/^App\\\\/', '', $o['namespace'] ?? '')) . '/' . $o['name'] . '.php';
$absPath   = base_path(str_replace('/', DIRECTORY_SEPARATOR, $filePath));
$loc       = file_exists($absPath) ? count(file($absPath)) : null;
$modelData = $model ? collect($data['models'] ?? [])->first(fn($m) => strtolower($m['name']) === strtolower($model)) : null;

$allLifecycle = ['creating','created','updating','updated','saving','saved','deleting','deleted','restoring','restored','forceDeleting','forceDeleted','retrieved'];
@endphp

<div class="detail-page">

<div class="detail-header reveal">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:20px;flex-wrap:wrap;">
        <div style="display:flex;align-items:flex-start;gap:16px;flex:1;min-width:0;">
            <div class="detail-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
            <div style="min-width:0">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:5px;">
                    <h1 class="detail-title">{{ $o['name'] }}</h1>
                    @if($isQueued)<span class="detail-badge">QUEUED</span>@endif
                </div>
                <span class="detail-ns">{{ $o['namespace'] ?? '' }}</span>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-shrink:0;flex-wrap:wrap;">
            <div class="detail-stat"><span class="detail-stat-val">{{ $model ?? '—' }}</span><span class="detail-stat-lbl">Observes</span></div>
            <div class="detail-stat"><span class="detail-stat-val">{{ count($events) }}</span><span class="detail-stat-lbl">Events</span></div>
        </div>
    </div>
</div>

<div class="detail-body">
<div class="detail-main">

    <div class="detail-card reveal">
        <div class="detail-card-head"><p class="detail-card-title">Observer Details</p></div>
        <div class="detail-card-body">
            <div class="obs-dg">
                <span class="detail-dl">Class</span>
                <span class="detail-dv">{{ $o['name'] }}</span>
                <span class="detail-dl">Namespace</span>
                <span class="detail-dv" style="font-family:var(--font-mono);font-size:10.5px;color:var(--text-dim);">{{ $o['namespace'] ?? '—' }}</span>
                <span class="detail-dl">File</span>
                <span class="detail-dv" style="font-family:var(--font-mono);font-size:10.5px;color:var(--text-dim);">{{ $filePath }}</span>
                <span class="detail-dl">Observes</span>
                <span class="detail-dv" style="font-weight:700;color:{{ $model ? '#ff2d20' : 'var(--text-faint)' }}">{{ $model ?? 'Unknown' }}</span>
                <span class="detail-dl">ShouldQueue</span>
                <span class="detail-dv"><span style="font-weight:700;color:{{ $isQueued ? 'var(--emerald)' : 'var(--text-faint)' }}">{{ $isQueued ? 'Yes' : 'No' }}</span></span>
                @if($registration)
                <span class="detail-dl">Registration</span>
                <span class="detail-dv" style="font-family:var(--font-mono);font-size:10.5px;color:var(--text-dim);">{{ $registration }}</span>
                @endif
                @if(!empty($deps))
                <span class="detail-dl">Dependencies</span>
                <span class="detail-dv">
                    @foreach($deps as $dep)
                        <span class="detail-pill">{{ is_array($dep) ? ($dep['type'] ?? $dep['name'] ?? '') : $dep }}</span>
                    @endforeach
                </span>
                @endif
            </div>
        </div>
    </div>

    <div class="detail-card reveal">
        <div class="detail-card-head">
            <p class="detail-card-title">Lifecycle Events</p>
            <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);">{{ count($events) }}/{{ count($allLifecycle) }} handled</span>
        </div>
        <div class="detail-card-body">
            <div class="obs-lc-grid">
                @foreach($allLifecycle as $lc)
                <div class="obs-lc {{ in_array($lc, $events) ? 'obs-lc--on' : 'obs-lc--off' }}">
                    {{ $lc }}
                    @if(in_array($lc, $events))
                    <span style="display:block;font-size:9px;opacity:.7;margin-top:2px;">✓ handled</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>{{-- .detail-main --}}

<div class="detail-sidebar">
    <div class="detail-scard reveal">
        <div class="detail-scard-head"><p class="detail-scard-title">Info</p></div>
        <div class="detail-scard-body">
            <div class="detail-ir">
                <span class="detail-il">Model</span>
                <span class="detail-iv" style="color:{{ $model ? '#ff2d20' : 'var(--text-faint)' }}">{{ $model ?? 'Unknown' }}</span>
            </div>
            <div class="detail-ir">
                <span class="detail-il">Events Handled</span>
                <span class="detail-iv" style="color:#ff2d20;">{{ count($events) }}</span>
            </div>
            <div class="detail-ir">
                <span class="detail-il">ShouldQueue</span>
                <span class="detail-iv" style="color:{{ $isQueued ? 'var(--emerald)' : 'var(--text-faint)' }}">{{ $isQueued ? 'Yes' : 'No' }}</span>
            </div>
            @if($registration)
            <div class="detail-ir">
                <span class="detail-il">Registration</span>
                <span class="detail-iv" style="font-family:var(--font-mono);font-size:10.5px;">{{ $registration }}</span>
            </div>
            @endif
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
            <a href="{{ route('laradar.observers') }}" class="detail-act"
               onclick="event.preventDefault(); showList('observers', true)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                All observers
            </a>
            @if($modelData)
            <a href="{{ route('laradar.model.detail', $model) }}" class="detail-act"
               onclick="event.preventDefault(); openDetailAjax('models', this.href)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                View {{ $model }} model
            </a>
            @endif
        </div>
    </div>
</div>

</div>{{-- .detail-body --}}
</div>{{-- .detail-page --}}
@endsection
