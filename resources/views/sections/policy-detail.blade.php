@extends('laradar::layouts.laradar')
@section('content')
@php
$p         = $policyData;
$model     = $p['model'] ?? null;
$actions   = $p['actions'] ?? [];
$filePath  = 'app/' . str_replace('\\', '/', preg_replace('/^App\\\\/', '', $p['namespace'] ?? '')) . '/' . $p['name'] . '.php';
$absPath   = base_path(str_replace('/', DIRECTORY_SEPARATOR, $filePath));
$loc       = file_exists($absPath) ? count(file($absPath)) : null;
$modelData = $model ? collect($data['models'] ?? [])->first(fn($m) => strtolower($m['name']) === strtolower($model)) : null;

$customActions = $p['custom_actions'] ?? [];
$allActions = ['before','viewAny','view','create','update','delete','restore','forceDelete'];
$actionDesc = [
    'before'      => 'Bypass all checks',
    'viewAny'     => 'List all resources',
    'view'        => 'View a resource',
    'create'      => 'Create a resource',
    'update'      => 'Update a resource',
    'delete'      => 'Delete a resource',
    'restore'     => 'Restore from trash',
    'forceDelete' => 'Permanently delete',
];
@endphp

<div class="detail-page">

<div class="detail-header reveal">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:20px;flex-wrap:wrap;">
        <div style="display:flex;align-items:flex-start;gap:16px;flex:1;min-width:0;">
            <div class="detail-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div style="min-width:0">
                <h1 class="detail-title">{{ $p['name'] }}</h1>
                <span class="detail-ns">{{ $p['namespace'] ?? '' }}</span>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-shrink:0;flex-wrap:wrap;">
            <div class="detail-stat"><span class="detail-stat-val">{{ $model ?? '—' }}</span><span class="detail-stat-lbl">Model</span></div>
            <div class="detail-stat"><span class="detail-stat-val">{{ count($actions) }}</span><span class="detail-stat-lbl">Actions</span></div>
            @if(count($customActions))<div class="detail-stat"><span class="detail-stat-val">{{ count($customActions) }}</span><span class="detail-stat-lbl">Custom</span></div>@endif
        </div>
    </div>
</div>

<div class="detail-body">
<div class="detail-main">

    <div class="detail-card reveal">
        <div class="detail-card-head"><p class="detail-card-title">Policy Details</p></div>
        <div class="detail-card-body">
            <div class="pol-dg">
                <span class="detail-dl">Class</span>
                <span class="detail-dv">{{ $p['name'] }}</span>
                <span class="detail-dl">Namespace</span>
                <span class="detail-dv" style="font-family:var(--font-mono);font-size:10.5px;color:var(--text-dim);">{{ $p['namespace'] ?? '—' }}</span>
                <span class="detail-dl">File</span>
                <span class="detail-dv" style="font-family:var(--font-mono);font-size:10.5px;color:var(--text-dim);">{{ $filePath }}</span>
                <span class="detail-dl">Governs</span>
                <span class="detail-dv" style="font-weight:700;color:{{ $model ? '#ff2d20' : 'var(--text-faint)' }}">{{ $model ?? 'Unknown' }}</span>
            </div>
        </div>
    </div>

    <div class="detail-card reveal">
        <div class="detail-card-head">
            <p class="detail-card-title">Authorization Actions</p>
            <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);">{{ count($actions) }}/{{ count($allActions) }} defined</span>
        </div>
        <div class="detail-card-body">
            <div class="pol-act-grid">
                @foreach($allActions as $act)
                @php $isOn = in_array($act, $actions); @endphp
                <div class="pol-act-item {{ $isOn ? 'pol-act-item--on' : 'pol-act-item--off' }}">
                    <span class="pol-act-name" style="color:{{ $isOn ? '#ff2d20' : 'var(--text-faint)' }}">
                        {{ $isOn ? '✓' : '○' }} {{ $act }}
                    </span>
                    <span class="pol-act-desc">{{ $actionDesc[$act] ?? '' }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    @if(count($customActions))
    <div class="detail-card reveal">
        <div class="detail-card-head">
            <p class="detail-card-title">Custom Actions</p>
            <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);">{{ count($customActions) }} method{{ count($customActions) !== 1 ? 's' : '' }}</span>
        </div>
        @foreach($customActions as $ca)
        <div class="detail-mth">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#ff2d20" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
            <span style="font-family:var(--font-mono);font-size:12.5px;font-weight:700;color:var(--text);">{{ $ca }}</span>
            <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);margin-left:auto;">custom</span>
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
                <span class="detail-il">Model</span>
                <span class="detail-iv" style="color:{{ $model ? '#ff2d20' : 'var(--text-faint)' }}">{{ $model ?? 'Unknown' }}</span>
            </div>
            <div class="detail-ir">
                <span class="detail-il">Actions Defined</span>
                <span class="detail-iv" style="color:#ff2d20;">{{ count($actions) }}/{{ count($allActions) }}</span>
            </div>
            @if(count($customActions))
            <div class="detail-ir">
                <span class="detail-il">Custom Actions</span>
                <span class="detail-iv" style="color:#ff2d20;">{{ count($customActions) }}</span>
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
            <a href="{{ route('laradar.policies') }}" class="detail-act"
               onclick="event.preventDefault(); showList('policies', true)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                All policies
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
