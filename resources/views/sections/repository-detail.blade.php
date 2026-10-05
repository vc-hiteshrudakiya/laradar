@extends('laradar::layouts.laradar')
@section('content')
@php
$r             = $repositoryData;
$methods       = $r['methods'] ?? [];
$methodDetails = $r['method_details'] ?? [];
$deps          = $r['dependencies'] ?? [];
$ifaces        = $r['implements'] ?? [];
$usedBy        = $r['used_by'] ?? [];
$filePath = 'app/' . str_replace('\\', '/', preg_replace('/^App\\\\/', '', $r['namespace'] ?? '')) . '/' . $r['name'] . '.php';
// Infer related model by stripping Repository suffix
$modelBaseName = preg_replace('/Repository$/', '', $r['name']);
$relatedModel  = collect($data['models'] ?? [])->first(fn($m) => strtolower($m['name']) === strtolower($modelBaseName));
$absPath  = base_path(str_replace('/', DIRECTORY_SEPARATOR, $filePath));
$loc      = file_exists($absPath) ? count(file($absPath)) : null;
$testPaths = [
    base_path('tests/Unit/' . $r['name'] . 'Test.php'),
    base_path('tests/Feature/' . $r['name'] . 'Test.php'),
];
$hasTest = collect($testPaths)->contains(fn($p) => file_exists($p));
@endphp

<div class="detail-page">

<div class="detail-header reveal">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:20px;flex-wrap:wrap;">
        <div style="display:flex;align-items:flex-start;gap:16px;flex:1;min-width:0;">
            <div class="detail-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                </svg>
            </div>
            <div style="min-width:0">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:5px;">
                    <h1 class="detail-title">{{ $r['name'] }}</h1>
                    @if(!empty($ifaces))
                        <span style="font-family:var(--font-mono);font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;background:rgba(255,45,32,.08);color:#ff2d20;border:1px solid rgba(255,45,32,.2);">IMPLEMENTS {{ count($ifaces) }}</span>
                    @endif
                </div>
                <span class="detail-ns">{{ $r['namespace'] ?? '' }}</span>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-shrink:0;flex-wrap:wrap;">
            <div class="detail-stat"><span class="detail-stat-val">{{ count($methods) }}</span><span class="detail-stat-lbl">Methods</span></div>
            <div class="detail-stat"><span class="detail-stat-val">{{ count($deps) }}</span><span class="detail-stat-lbl">Deps</span></div>
            @if($loc)<div class="detail-stat"><span class="detail-stat-val">{{ $loc }}</span><span class="detail-stat-lbl">Lines</span></div>@endif
        </div>
    </div>
</div>

<div class="detail-body">
<div class="detail-main">

    <div class="detail-card reveal">
        <div class="detail-card-head"><p class="detail-card-title">Repository Details</p></div>
        <div class="detail-card-body">
            <div class="rep-dg">
                <span class="detail-dl">Class</span>
                <span class="detail-dv">{{ $r['name'] }}</span>
                <span class="detail-dl">Namespace</span>
                <span class="detail-dv" style="font-family:var(--font-mono);font-size:10.5px;color:var(--text-dim);">{{ $r['namespace'] ?? '—' }}</span>
                <span class="detail-dl">File</span>
                <span class="detail-dv" style="font-family:var(--font-mono);font-size:10.5px;color:var(--text-dim);">{{ $filePath }}</span>
                @if(!empty($ifaces))
                <span class="detail-dl">Implements</span>
                <span class="detail-dv">
                    @foreach($ifaces as $iface)
                        <span class="detail-pill">{{ $iface }}</span>
                    @endforeach
                </span>
                @endif
                @if(!empty($deps))
                <span class="detail-dl">Dependencies</span>
                <span class="detail-dv">
                    @foreach($deps as $dep)
                        <span class="detail-pill"><span style="width:14px;height:14px;border-radius:3px;background:#ff2d20;color:#fff;font-size:8px;font-weight:700;display:inline-flex;align-items:center;justify-content:center;">D</span>{{ is_array($dep) ? ($dep['type'] ?? $dep['name'] ?? '') : $dep }}</span>
                    @endforeach
                </span>
                @endif
            </div>
        </div>
    </div>

    @if(!empty($methodDetails))
    <div class="detail-card reveal">
        <div class="detail-card-head">
            <p class="detail-card-title">Public Methods</p>
            <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);">{{ count($methodDetails) }} methods</span>
        </div>
        @foreach($methodDetails as $md)
        @php
            $paramStr   = collect($md['params'] ?? [])->map(fn($p) => ($p['type'] ? $p['type'].' ' : '').'$'.$p['var'])->implode(', ');
            $returnType = $md['return_type'] ?? null;
        @endphp
        <div class="detail-mth" style="flex-direction:column;align-items:flex-start;gap:3px;">
            <div style="display:flex;align-items:center;gap:8px;width:100%;">
                <span style="font-family:var(--font-mono);font-size:12.5px;font-weight:700;color:var(--text);">{{ $md['method'] }}</span>
                @if($returnType)
                <span style="font-family:var(--font-mono);font-size:10px;padding:1px 7px;border-radius:4px;background:rgba(255,45,32,.07);color:#ff2d20;border:1px solid rgba(255,45,32,.18);margin-left:auto;">{{ ($md['return_nullable'] ?? false) ? '?' : '' }}{{ $returnType }}</span>
                @else
                <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);margin-left:auto;">public</span>
                @endif
            </div>
            @if($paramStr !== '')
            <span style="font-family:var(--font-mono);font-size:10.5px;color:var(--text-faint);">({{ $paramStr }})</span>
            @endif
        </div>
        @endforeach
    </div>
    @elseif(!empty($methods))
    <div class="detail-card reveal">
        <div class="detail-card-head">
            <p class="detail-card-title">Public Methods</p>
            <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);">{{ count($methods) }} methods</span>
        </div>
        @foreach($methods as $method)
        <div class="detail-mth">
            <span style="font-family:var(--font-mono);font-size:13px;font-weight:700;color:var(--text);">{{ $method }}</span>
            <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);">public</span>
        </div>
        @endforeach
    </div>
    @endif

    @if(!empty($usedBy))
    <div class="detail-card reveal">
        <div class="detail-card-head">
            <p class="detail-card-title">Used By</p>
            <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);">{{ count($usedBy) }} controller{{ count($usedBy) !== 1 ? 's' : '' }}</span>
        </div>
        @foreach($usedBy as $ctrl)
        @php $ctrlExists = collect($data['controllers'] ?? [])->first(fn($c) => strtolower($c['name']) === strtolower($ctrl)); @endphp
        <div class="detail-mth">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#ff2d20" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
            @if($ctrlExists)
            <a href="{{ route('laradar.controller.detail', $ctrl) }}" style="font-family:var(--font-mono);font-size:12.5px;color:#ff2d20;text-decoration:none;font-weight:600;" onmouseenter="this.style.opacity='.7'" onmouseleave="this.style.opacity='1'" onclick="event.preventDefault(); openDetailAjax('controllers', this.href)">{{ $ctrl }}</a>
            @else
            <span style="font-family:var(--font-mono);font-size:12.5px;color:var(--text);">{{ $ctrl }}</span>
            @endif
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
                <span class="detail-il">Tests</span>
                <span class="detail-iv" style="font-weight:700;color:{{ $hasTest ? 'var(--emerald)' : 'var(--rose)' }}">{{ $hasTest ? '✓ Found' : '✗ Not found' }}</span>
            </div>
            @if($loc)
            <div class="detail-ir">
                <span class="detail-il">Lines of Code</span>
                <span class="detail-iv" style="font-weight:700;color:{{ $loc > 300 ? 'var(--rose)' : ($loc > 150 ? 'var(--amber)' : 'var(--emerald)') }}">{{ $loc }} lines</span>
            </div>
            @endif
            <div class="detail-ir">
                <span class="detail-il">Interfaces</span>
                <span class="detail-iv" style="font-weight:700;">{{ count($ifaces) ?: '—' }}</span>
            </div>
        </div>
    </div>

    <div class="detail-scard reveal">
        <div class="detail-scard-head"><p class="detail-scard-title">Quick Actions</p></div>
        <div class="detail-scard-body">
            <a href="{{ route('laradar.repositories') }}" class="detail-act"
               onclick="event.preventDefault(); showList('repositories', true)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
                All repositories
            </a>
            @if($relatedModel)
            <a href="{{ route('laradar.model.detail', $relatedModel['name']) }}" class="detail-act"
               onclick="event.preventDefault(); openDetailAjax('models', this.href)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14c0 1.657 4.03 3 9 3s9-1.343 9-3V5"/><path d="M3 12c0 1.657 4.03 3 9 3s9-1.343 9-3"/></svg>
                View {{ $relatedModel['name'] }} model
            </a>
            @endif
            <a href="{{ route('laradar.services') }}" class="detail-act"
               onclick="event.preventDefault(); showList('services', true)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Services
            </a>
        </div>
    </div>
</div>

</div>{{-- .detail-body --}}
</div>{{-- .detail-page --}}
@endsection
