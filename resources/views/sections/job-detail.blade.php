@extends('laradar::layouts.laradar')
@section('content')
@php
$j             = $jobData;
$deps          = $j['dependencies'] ?? [];
$handleDeps    = $j['handle_deps'] ?? [];
$jobMiddleware = $j['job_middleware'] ?? [];
$dispatchedBy  = $j['dispatched_by'] ?? [];
$isQueued      = !empty($j['should_queue']);
$isUnique      = !empty($j['unique']);
$isEncrypted   = !empty($j['encrypted']);
$hasFailed     = !empty($j['has_failed']);
$queue         = $j['queue'] ?? 'default';
$connection    = $j['connection'] ?? null;
$tries         = $j['tries'] ?? null;
$timeout       = $j['timeout'] ?? null;
$delay         = $j['delay'] ?? null;
$backoff       = $j['backoff'] ?? null;
$maxExceptions = $j['max_exceptions'] ?? null;
$filePath      = 'app/' . str_replace('\\', '/', preg_replace('/^App\\\\/', '', $j['namespace'] ?? '')) . '/' . $j['name'] . '.php';
$absPath       = base_path(str_replace('/', DIRECTORY_SEPARATOR, $filePath));
$loc           = file_exists($absPath) ? count(file($absPath)) : null;
@endphp

<div class="detail-page">

<div class="detail-header reveal">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:20px;flex-wrap:wrap;">
        <div style="display:flex;align-items:flex-start;gap:16px;flex:1;min-width:0;">
            <div class="detail-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <div style="min-width:0">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:5px;">
                    <h1 class="detail-title">{{ $j['name'] }}</h1>
                    @if($isQueued)<span class="detail-badge">QUEUED</span>@endif
                    @if($isUnique)<span class="detail-badge">UNIQUE</span>@endif
                    @if($isEncrypted)<span class="detail-badge">ENCRYPTED</span>@endif
                </div>
                <span class="detail-ns">{{ $j['namespace'] ?? '' }}</span>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-shrink:0;flex-wrap:wrap;">
            <div class="detail-stat"><span class="detail-stat-val">{{ $queue }}</span><span class="detail-stat-lbl">Queue</span></div>
            <div class="detail-stat"><span class="detail-stat-val">{{ $tries ?? '∞' }}</span><span class="detail-stat-lbl">Tries</span></div>
            <div class="detail-stat"><span class="detail-stat-val">{{ $timeout ? $timeout.'s' : '∞' }}</span><span class="detail-stat-lbl">Timeout</span></div>
        </div>
    </div>
</div>

<div class="detail-body">
<div class="detail-main">

    <div class="detail-card reveal">
        <div class="detail-card-head"><p class="detail-card-title">Job Configuration</p></div>
        <div class="detail-card-body">
            <div class="job-dg">
                <span class="detail-dl">Class</span>
                <span class="detail-dv">{{ $j['name'] }}</span>
                <span class="detail-dl">Namespace</span>
                <span class="detail-dv" style="font-family:var(--font-mono);font-size:10.5px;color:var(--text-dim);">{{ $j['namespace'] ?? '—' }}</span>
                <span class="detail-dl">File</span>
                <span class="detail-dv" style="font-family:var(--font-mono);font-size:10.5px;color:var(--text-dim);">{{ $filePath }}</span>
                <span class="detail-dl">Queue</span>
                <span class="detail-dv"><span style="font-family:var(--font-mono);font-size:12px;font-weight:700;color:#ff2d20;">{{ $queue }}</span></span>
                @if($connection)
                <span class="detail-dl">Connection</span>
                <span class="detail-dv"><span style="font-family:var(--font-mono);font-size:12px;font-weight:700;color:var(--text);">{{ $connection }}</span></span>
                @endif
                <span class="detail-dl">Tries</span>
                <span class="detail-dv"><span style="font-family:var(--font-mono);font-size:12.5px;font-weight:700;color:var(--text);">{{ $tries ?? '—' }}</span></span>
                @if($maxExceptions !== null)
                <span class="detail-dl">Max Exceptions</span>
                <span class="detail-dv"><span style="font-family:var(--font-mono);font-size:12.5px;font-weight:700;color:var(--text);">{{ $maxExceptions }}</span></span>
                @endif
                <span class="detail-dl">Timeout</span>
                <span class="detail-dv"><span style="font-family:var(--font-mono);font-size:12.5px;font-weight:700;color:var(--text);">{{ $timeout ? $timeout.' seconds' : '—' }}</span></span>
                @if($backoff !== null)
                <span class="detail-dl">Backoff</span>
                <span class="detail-dv"><span style="font-family:var(--font-mono);font-size:12.5px;font-weight:700;color:var(--text);">{{ $backoff }} seconds</span></span>
                @endif
                @if($delay !== null)
                <span class="detail-dl">Delay</span>
                <span class="detail-dv"><span style="font-family:var(--font-mono);font-size:12.5px;font-weight:700;color:var(--text);">{{ $delay }} seconds</span></span>
                @endif
                <span class="detail-dl">ShouldQueue</span>
                <span class="detail-dv"><span style="font-weight:700;color:{{ $isQueued ? 'var(--emerald)' : 'var(--text-faint)' }}">{{ $isQueued ? 'Yes' : 'No' }}</span></span>
                <span class="detail-dl">Unique</span>
                <span class="detail-dv"><span style="font-weight:700;color:{{ $isUnique ? 'var(--emerald)' : 'var(--text-faint)' }}">{{ $isUnique ? 'Yes' : 'No' }}</span></span>
                <span class="detail-dl">Encrypted</span>
                <span class="detail-dv"><span style="font-weight:700;color:{{ $isEncrypted ? 'var(--emerald)' : 'var(--text-faint)' }}">{{ $isEncrypted ? 'Yes' : 'No' }}</span></span>
                <span class="detail-dl">Failure Handler</span>
                <span class="detail-dv"><span style="font-weight:700;color:{{ $hasFailed ? 'var(--emerald)' : 'var(--text-faint)' }}">{{ $hasFailed ? 'failed() defined' : 'Not defined' }}</span></span>
                @if(!empty($deps))
                <span class="detail-dl">Constructor Deps</span>
                <span class="detail-dv">
                    @foreach($deps as $dep)
                        <span class="detail-pill">{{ is_array($dep) ? ($dep['type'] ?? $dep['name'] ?? '') : $dep }}</span>
                    @endforeach
                </span>
                @endif
                @if(!empty($handleDeps))
                <span class="detail-dl">Handle() Deps</span>
                <span class="detail-dv">
                    @foreach($handleDeps as $dep)
                        <span class="detail-pill">{{ is_array($dep) ? ($dep['type'] ?? $dep['name'] ?? '') : $dep }}</span>
                    @endforeach
                </span>
                @endif
            </div>
        </div>
    </div>

    @if(!empty($jobMiddleware))
    <div class="detail-card reveal">
        <div class="detail-card-head">
            <p class="detail-card-title">Job Middleware</p>
            <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);">{{ count($jobMiddleware) }}</span>
        </div>
        <div class="detail-card-body" style="display:flex;flex-wrap:wrap;gap:8px;">
            @foreach($jobMiddleware as $mw)
            <span class="detail-pill">{{ $mw }}</span>
            @endforeach
        </div>
    </div>
    @endif

    @if(!empty($dispatchedBy))
    <div class="detail-card reveal">
        <div class="detail-card-head">
            <p class="detail-card-title">Dispatched By</p>
            <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);">{{ count($dispatchedBy) }} class{{ count($dispatchedBy) !== 1 ? 'es' : '' }}</span>
        </div>
        @foreach($dispatchedBy as $caller)
        <div class="detail-mth">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#ff2d20" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
            <span style="font-family:var(--font-mono);font-size:12.5px;color:var(--text);">{{ $caller }}</span>
        </div>
        @endforeach
    </div>
    @endif

</div>{{-- .detail-main --}}

<div class="detail-sidebar">
    <div class="detail-scard reveal">
        <div class="detail-scard-head"><p class="detail-scard-title">Queue Config</p></div>
        <div class="detail-scard-body">
            <div class="detail-ir">
                <span class="detail-il">Queue Name</span>
                <span class="detail-iv" style="color:#ff2d20;">{{ $queue }}</span>
            </div>
            <div class="detail-ir">
                <span class="detail-il">ShouldQueue</span>
                <span class="detail-iv" style="color:{{ $isQueued ? 'var(--emerald)' : 'var(--text-faint)' }}">{{ $isQueued ? 'Yes' : 'No' }}</span>
            </div>
            <div class="detail-ir">
                <span class="detail-il">Max Tries</span>
                <span class="detail-iv">{{ $tries ?? 'Default' }}</span>
            </div>
            <div class="detail-ir">
                <span class="detail-il">Timeout</span>
                <span class="detail-iv">{{ $timeout ? $timeout.'s' : 'Default' }}</span>
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
            <a href="{{ route('laradar.jobs') }}" class="detail-act"
               onclick="event.preventDefault(); showList('jobs', true)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                All jobs
            </a>
        </div>
    </div>
</div>

</div>{{-- .detail-body --}}
</div>{{-- .detail-page --}}
@endsection
