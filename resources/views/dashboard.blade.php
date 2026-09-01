@extends('laradar::layouts.laradar')

@section('content')
{{-- Overview --}}
<section id="sec-overview" class="p-6" style="{{ $section === 'overview' ? '' : 'display:none' }}">

    @php
    $kpiIcons = [
        'Models'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>',
        'Controllers'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>',
        'Routes'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
        'Jobs'         => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>',
        'Events'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>',
        'Services'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
        'Repositories' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>',
        'Observers'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>',
        'Policies'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
        'Modules'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
        'Middleware'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>',
    ];
    $kpiColors = [
        'Models'       => ['color'=>'var(--brand)', 'bg'=>'var(--brand-bg)'],
        'Controllers'  => ['color'=>'var(--brand)', 'bg'=>'var(--brand-bg)'],
        'Routes'       => ['color'=>'var(--brand)', 'bg'=>'var(--brand-bg)'],
        'Jobs'         => ['color'=>'var(--brand)', 'bg'=>'var(--brand-bg)'],
        'Events'       => ['color'=>'var(--brand)', 'bg'=>'var(--brand-bg)'],
        'Services'     => ['color'=>'var(--brand)', 'bg'=>'var(--brand-bg)'],
        'Repositories' => ['color'=>'var(--brand)', 'bg'=>'var(--brand-bg)'],
        'Observers'    => ['color'=>'var(--brand)', 'bg'=>'var(--brand-bg)'],
        'Policies'     => ['color'=>'var(--brand)', 'bg'=>'var(--brand-bg)'],
        'Modules'      => ['color'=>'var(--brand)', 'bg'=>'var(--brand-bg)'],
        'Middleware'   => ['color'=>'var(--brand)', 'bg'=>'var(--brand-bg)'],
    ];
    $stats = [
        ['Models',       $summary['models']??0],
        ['Controllers',  $summary['controllers']??0],
        ['Routes',       $rs['total']??0],
        ['Jobs',         $summary['jobs']??0],
        ['Events',       $summary['events']??0],
        ['Services',     $summary['services']??0],
        ['Repositories', $summary['repositories']??0],
        ['Observers',    $summary['observers']??0],
        ['Policies',     $summary['policies']??0],
        ['Modules',      $summary['modules']??0],
        ['Middleware',   count($rs['middleware_usage']??[])],
    ];
    $kpiNav = [
        'Models'       => 'models',
        'Controllers'  => 'controllers',
        'Routes'       => 'routes',
        'Jobs'         => 'jobs',
        'Events'       => 'events',
        'Services'     => 'services',
        'Repositories' => 'repositories',
        'Observers'    => 'observers',
        'Policies'     => 'policies',
        'Modules'      => 'modules',
        'Middleware'   => 'middleware',
    ];
    @endphp

    <div class="kpi-grid" style="margin-bottom:28px;grid-template-columns:repeat(4,1fr);">
        @foreach($stats as [$label,$count])
        @php $kc = $kpiColors[$label] ?? ['color'=>'var(--text-dim)','bg'=>'rgba(91,103,133,0.18)']; $ki = $kpiIcons[$label] ?? ''; $kn = $kpiNav[$label] ?? ''; @endphp
        <div class="kpi-card ov-reveal" data-ov-reveal style="transition-delay:{{ $loop->index * 45 }}ms;{{ $kn ? 'cursor:pointer;' : '' }}" @if($kn) onclick="navigate('{{ $kn }}')" @endif>
            <div class="kpi-card__icon" style="background:{{ $kc['bg'] }};color:{{ $kc['color'] }};">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">{!! $ki !!}</svg>
            </div>
            <span class="kpi-card__label">{{ $label }}</span>
            <span class="kpi-card__num" data-count="{{ $count }}" style="color:{{ $kc['color'] }};">{{ $count }}</span>
        </div>
        @endforeach
    </div>

    {{-- Architecture Explorer --}}
    <div class="ov-panel ov-reveal" data-ov-reveal style="margin-bottom:24px;">
        <div class="ov-panel-head">
            <div>
                <h3>Architecture Explorer</h3>
                <p>Request flow &mdash; from HTTP kernel to your database tables</p>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <span id="ovArchDetail" style="font-size:11.5px;color:var(--text-faint);">Hover a node to trace it</span>
                <button class="ov-btn-icon" id="ovZoomIn" title="Zoom in">+</button>
                <button class="ov-btn-icon" id="ovZoomOut" title="Zoom out">&minus;</button>
            </div>
        </div>
        <div class="ov-panel-body" style="padding:18px 20px;">
            <div class="ov-diag-shell">
                <div id="ovArchDiagram" style="min-width:960px;"></div>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;">
        {{-- Route breakdown --}}
        <div class="atlas-card ov-reveal" data-ov-reveal style="transition-delay:0ms;">
            <div class="atlas-card__head"><h3>Route Breakdown</h3></div>
            <div style="display:flex;flex-direction:column;gap:10px;font-size:13px;">
                <div style="display:flex;justify-content:space-between;align-items:center;"><span style="color:var(--text-faint);">Total</span><span style="font-family:var(--font-mono);font-weight:600;color:var(--text);">{{ $rs['total']??0 }}</span></div>
                @foreach($rs['by_group']??[] as $group => $cnt)
                <div style="display:flex;justify-content:space-between;align-items:center;"><span style="color:var(--text-faint);">{{ ucfirst($group) }}</span><span style="font-family:var(--font-mono);font-weight:600;color:var(--text);">{{ $cnt }}</span></div>
                @endforeach
                <div style="display:flex;justify-content:space-between;align-items:center;"><span style="color:var(--text-faint);">Named</span><span style="font-family:var(--font-mono);font-weight:600;color:var(--text);">{{ $rs['named_count']??0 }} / {{ $rs['total']??0 }}</span></div>
                @if(!empty($rs['api_versions']))
                <div style="display:flex;justify-content:space-between;align-items:center;"><span style="color:var(--text-faint);">API Versions</span><span style="font-family:var(--font-mono);font-weight:600;color:var(--text);">{{ implode(', ', array_keys($rs['api_versions'])) }}</span></div>
                @endif
            </div>
            @if(!empty($rs['by_method']))
            <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--border);">
                <p style="font-family:var(--font-mono);font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--text-faint);margin-bottom:10px;">By Method</p>
                <div style="display:flex;flex-wrap:wrap;gap:6px;">
                    @foreach($rs['by_method'] as $method => $cnt)
                    <span class="text-xs px-2 py-0.5 rounded font-semibold method-{{ strtolower($method) }}" style="font-family:var(--font-mono);">{{ strtoupper($method) }} {{ $cnt }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Performance --}}
        <div class="atlas-card ov-reveal" data-ov-reveal style="transition-delay:80ms;">
            <div class="atlas-card__head"><h3>Performance</h3></div>
            @php $perf = $data['performance']??[]; @endphp
            <div style="display:flex;flex-direction:column;gap:18px;">
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:8px;">
                        <span style="color:var(--text-faint);">Scan Time</span>
                        <span style="font-family:var(--font-mono);color:var(--cyan);">{{ $perf['execution_time_ms']??0 }} ms</span>
                    </div>
                    <div class="atlas-score-bar"><div class="atlas-score-fill" data-score-w="{{ min(100,($perf['execution_time_ms']??0)/50) }}" style="width:0;background:linear-gradient(90deg,#6366F1,#818CF8);"></div></div>
                </div>
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:8px;">
                        <span style="color:var(--text-faint);">Memory</span>
                        <span style="font-family:var(--font-mono);color:var(--emerald);">{{ $perf['memory_usage_mb']??0 }} MB</span>
                    </div>
                    <div class="atlas-score-bar"><div class="atlas-score-fill" data-score-w="{{ min(100,($perf['memory_usage_mb']??0)/1.28) }}" style="width:0;background:var(--emerald);"></div></div>
                </div>
            </div>
        </div>

        {{-- Score checks --}}
        @if(!empty($score['checks']))
        <div class="atlas-card ov-reveal" data-ov-reveal style="transition-delay:160ms;">
            <div class="atlas-card__head"><h3>Score Checks</h3></div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($score['checks'] as $check)
                @php
                    $icColor = match($check['status']??'fail'){'pass'=>'var(--emerald)','warn'=>'var(--amber)',default=>'var(--rose)'};
                    $icSymbol = match($check['status']??'fail'){'pass'=>'✔','warn'=>'⚠',default=>'✘'};
                @endphp
                <div class="hc-row" style="--hc-i:{{ $loop->index }};display:flex;align-items:flex-start;gap:10px;">
                    <span style="font-weight:700;font-size:13px;color:{{ $icColor }};margin-top:1px;flex:none;">{{ $icSymbol }}</span>
                    <div>
                        <p style="font-size:13px;color:var(--text);">{{ $check['label'] }}</p>
                        @if(!empty($check['note']))<p style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);margin-top:2px;">{{ $check['note'] }}</p>@endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>

{{-- Models --}}
<section id="sec-models" class="p-6" style="{{ $section === 'models' ? '' : 'display:none' }}">
    @php
    $mTotalRels    = collect($data['models'])->sum(fn($m) => count($m['relationships']??[]));
    $mWithObs      = collect($data['models'])->filter(fn($m) => !empty($m['observer']))->count();
    $mSoftDel      = collect($data['models'])->filter(fn($m) => collect($m['traits']??[])->contains(fn($t)=>str_contains($t,'SoftDeletes')))->count();
    $mPalette = [
        ['color'=>'var(--cyan)',    'bg'=>'rgba(99,102,241,.15)',   'border'=>'rgba(99,102,241,.3)',   'hex'=>'#6366F1'],
        ['color'=>'var(--violet)',  'bg'=>'rgba(167,139,250,.15)', 'border'=>'rgba(167,139,250,.3)', 'hex'=>'#A78BFA'],
        ['color'=>'var(--emerald)', 'bg'=>'rgba(52,211,153,.15)',  'border'=>'rgba(52,211,153,.3)',  'hex'=>'#34D399'],
        ['color'=>'var(--amber)',   'bg'=>'rgba(251,191,36,.15)',  'border'=>'rgba(251,191,36,.3)',  'hex'=>'#FBBF24'],
        ['color'=>'var(--rose)',    'bg'=>'rgba(248,113,113,.15)', 'border'=>'rgba(248,113,113,.3)', 'hex'=>'#F87171'],
        ['color'=>'var(--sky)',     'bg'=>'rgba(96,165,250,.15)',  'border'=>'rgba(96,165,250,.3)',  'hex'=>'#60A5FA'],
    ];
    $mRelColors = ['hasMany'=>'#34D399','hasOne'=>'#6366F1','belongsTo'=>'#60A5FA','belongsToMany'=>'#A78BFA','morphMany'=>'#F87171','morphTo'=>'#F87171','morphOne'=>'#F87171','hasManyThrough'=>'#FBBF24'];
    @endphp

    <div id="models-list">
        {{-- Top stats --}}
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--violet);">{{ count($data['models']) }}</span>
                <span class="mds-top-stat-lbl">Total Models</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--cyan);">{{ $mTotalRels }}</span>
                <span class="mds-top-stat-lbl">Relationships</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--amber);">{{ $mWithObs }}</span>
                <span class="mds-top-stat-lbl">With Observer</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--rose);">{{ $mSoftDel }}</span>
                <span class="mds-top-stat-lbl">Soft Deletes</span>
            </div>
        </div>

        {{-- Toolbar --}}
        <div class="mds-toolbar">
            <input id="models-search" oninput="filterGrid('models')" type="search" placeholder="Search models…" style="border:1px solid var(--border);border-radius:8px;padding:8px 14px;font-size:13px;flex:1;max-width:240px;font-family:var(--font-mono);">
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ count($data['models']) }} models</span>
        </div>

        {{-- Grid view --}}
        <div id="mds-grid-view" style="display:none;">
            <div class="mds-grid" id="models-grid">
                @foreach($data['models'] as $i => $model)
                @php
                    $mp      = $mPalette[$i % count($mPalette)];
                    $mRels   = $model['relationships'] ?? [];
                    $mRelCnt = count($mRels);
                    $mFillCnt= count($model['fillable'] ?? []);
                    $mTrCnt  = count($model['traits'] ?? []);
                    $mRelGrp = collect($mRels)->groupBy('type');
                    $mTotalFieldsInCard = $mRelCnt + $mFillCnt;
                @endphp
                <div class="mds-card" onclick="showDetail('models',{{ $i }})" data-name="{{ strtolower($model['name']) }}" style="cursor:pointer;--card-hover-border:{{ $mp['border'] }};">
                    <div class="mds-card-glow" style="background:linear-gradient(90deg,{{ $mp['color'] }},transparent);"></div>
                    <div class="mds-card-head">
                        <div class="mds-card-av" style="background:{{ $mp['bg'] }};color:{{ $mp['color'] }};border-color:{{ $mp['border'] }};">{{ substr($model['name'],0,1) }}</div>
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:6px;">
                                <p class="mds-card-title">{{ $model['name'] }}</p>
                                @if(!empty($model['observer']))<span class="mds-card-obs">obs</span>@endif
                            </div>
                            <p class="mds-card-table">{{ $model['table'] }}</p>
                            <p class="mds-card-ns">{{ $model['namespace'] ?? '' }}</p>
                        </div>
                    </div>
                    <div class="mds-card-sep"></div>
                    <div class="mds-card-body">
                        {{-- Stats row --}}
                        <div class="mds-card-stats" style="background:var(--bg-hover);border-radius:10px;border:1px solid var(--border);margin-bottom:12px;">
                            <div class="mds-card-stat-item">
                                <div class="mds-card-stat-num" style="color:{{ $mp['color'] }};">{{ $mRelCnt }}</div>
                                <div class="mds-card-stat-lbl">Relations</div>
                            </div>
                            <div class="mds-card-stat-item">
                                <div class="mds-card-stat-num" style="color:var(--cyan);">{{ $mFillCnt }}</div>
                                <div class="mds-card-stat-lbl">Fillable</div>
                            </div>
                            <div class="mds-card-stat-item">
                                <div class="mds-card-stat-num" style="color:var(--text-dim);">{{ $mTrCnt }}</div>
                                <div class="mds-card-stat-lbl">Traits</div>
                            </div>
                        </div>

                        {{-- Relationship bar --}}
                        @if($mRelCnt > 0)
                        <div class="mds-rel-bar">
                            @foreach($mRelGrp as $rType => $rItems)
                            @php $rw = round(count($rItems) / $mRelCnt * 100); $rCol = $mRelColors[$rType] ?? '#6B778C'; @endphp
                            <div class="mds-rel-seg" data-flex="{{ $rw }}" style="flex:0;min-width:0;background:{{ $rCol }};opacity:.75;"></div>
                            @endforeach
                        </div>
                        <div class="mds-rel-legend">
                            @foreach($mRelGrp as $rType => $rItems)
                            @php $rCol = $mRelColors[$rType] ?? '#6B778C'; @endphp
                            <span class="mds-rel-dot"><i style="background:{{ $rCol }};"></i>{{ $rType }} ·{{ count($rItems) }}</span>
                            @endforeach
                        </div>
                        @endif

                        {{-- Traits --}}
                        @if(!empty($model['traits']))
                        <div class="mds-trait-row">
                            @foreach($model['traits'] as $tr)
                            <span class="mds-trait-pip">{{ $tr }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- List view --}}
        <div id="mds-list-view" style="background:var(--bg-elevated);border-radius:12px;border:1px solid var(--border);overflow:hidden;">
            <div class="mds-list-head">
                <span></span><span>Model</span><span>Table</span><span>Rels</span><span>Fillable</span><span>Traits</span>
            </div>
            @foreach($data['models'] as $i => $model)
            @php $mp = $mPalette[$i % count($mPalette)]; @endphp
            <div class="mds-list-row" onclick="showDetail('models',{{ $i }})" data-name="{{ strtolower($model['name']) }}">
                <div class="mds-list-av" style="background:{{ $mp['bg'] }};color:{{ $mp['color'] }};border-color:{{ $mp['border'] }};">{{ substr($model['name'],0,1) }}</div>
                <div>
                    <span style="font-weight:700;font-size:13.5px;color:var(--text);">{{ $model['name'] }}</span>
                    @if(!empty($model['observer']))<span style="font-size:9px;padding:2px 6px;border-radius:4px;background:rgba(251,191,36,.12);color:var(--amber);border:1px solid rgba(251,191,36,.2);font-family:var(--font-mono);margin-left:8px;">obs</span>@endif
                </div>
                <span style="font-family:var(--font-mono);font-size:11.5px;color:var(--text-faint);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $model['table'] }}</span>
                <span style="font-family:var(--font-mono);font-size:14px;font-weight:800;" style="color:{{ $mp['color'] }}">{{ count($model['relationships']??[]) }}</span>
                <span style="font-family:var(--font-mono);font-size:14px;font-weight:800;color:var(--cyan);">{{ count($model['fillable']??[]) }}</span>
                <span style="font-family:var(--font-mono);font-size:14px;font-weight:800;color:var(--text-dim);">{{ count($model['traits']??[]) }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Detail --}}
    <div id="models-detail" style="display:none">
        <div id="models-detail-content"></div>
    </div>
</section>

{{-- Controllers --}}
<section id="sec-controllers" class="p-6" style="{{ $section === 'controllers' ? '' : 'display:none' }}">
    <div id="controllers-list">

        @php
            $ctrlTotal      = count($data['controllers']);
            $ctrlTotalMeth  = array_sum(array_column($data['controllers'], 'method_count'));
            $ctrlWithMw     = count(array_filter($data['controllers'], fn($c) => !empty($c['middleware'])));
            $ctrlResource   = count(array_filter($data['controllers'], fn($c) => !empty($c['is_resource'])));
            $ctrlMaxMethods = max(array_column($data['controllers'], 'method_count') ?: [1]);
        @endphp
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--amber);">{{ $ctrlTotal }}</span>
                <span class="mds-top-stat-lbl">Controllers</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--cyan);">{{ $ctrlTotalMeth }}</span>
                <span class="mds-top-stat-lbl">Total Methods</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--emerald);">{{ $ctrlResource }}</span>
                <span class="mds-top-stat-lbl">Resource</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--rose);">{{ $ctrlWithMw }}</span>
                <span class="mds-top-stat-lbl">With Middleware</span>
            </div>
        </div>

        <div class="mds-toolbar">
            <input id="controllers-search" oninput="filterGrid('controllers')" type="search" placeholder="Search controllers…" style="border:1px solid var(--border);border-radius:8px;padding:8px 14px;font-size:13px;flex:1;max-width:240px;font-family:var(--font-mono);">
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ $ctrlTotal }} controllers</span>
        </div>

        <div id="controllers-grid" style="background:var(--bg-elevated);border-radius:12px;border:1px solid var(--border);overflow:hidden;">
            <div class="mds-list-head" style="grid-template-columns:36px 1fr 80px 70px 70px 90px;">
                <span></span><span>Controller</span><span>Methods</span><span>Routes</span><span>Deps</span><span>Complexity</span>
            </div>
            @foreach($data['controllers'] as $i => $ctrl)
            @php
                $ctrlRouteCount = count(array_filter($data['routes'] ?? [], fn($r) => class_basename($r['controller']['class'] ?? '') === $ctrl['name']));
                $ctrlComplexity = $ctrlMaxMethods > 0 ? round(($ctrl['method_count']??0) / $ctrlMaxMethods * 100) : 0;
            @endphp
            <div class="mds-list-row" style="grid-template-columns:36px 1fr 80px 70px 70px 90px;" data-name="{{ strtolower($ctrl['name']) }}">
                <div class="mds-list-av" style="background:rgba(255,139,0,0.10);color:var(--amber);border-color:rgba(255,139,0,0.25);">{{ substr($ctrl['name'],0,1) }}</div>
                <div style="min-width:0;">
                    <div style="font-weight:700;font-size:13.5px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $ctrl['name'] }}
                        @if(!empty($ctrl['is_resource']))<span style="font-family:var(--font-mono);font-size:9px;padding:2px 6px;border-radius:10px;background:rgba(52,211,153,0.12);color:var(--emerald);border:1px solid rgba(52,211,153,0.25);margin-left:6px;">Resource</span>@endif
                    </div>
                    <div style="font-family:var(--font-mono);font-size:10.5px;color:var(--text-faint);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $ctrl['namespace'] }}</div>
                </div>
                <div style="font-family:var(--font-mono);font-size:13px;font-weight:700;color:var(--text);text-align:center;">{{ $ctrl['method_count']??0 }}</div>
                <div style="font-family:var(--font-mono);font-size:13px;font-weight:700;color:var(--sky);text-align:center;">{{ $ctrlRouteCount }}</div>
                <div style="font-family:var(--font-mono);font-size:13px;font-weight:700;color:var(--violet);text-align:center;">{{ count($ctrl['dependencies']??[]) }}</div>
                <div style="padding-right:8px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                        <span style="font-family:var(--font-mono);font-size:10px;color:var(--amber);font-weight:700;">{{ $ctrlComplexity }}%</span>
                    </div>
                    <div class="ctrl-complexity-track">
                        <div class="ctrl-complexity-fill" style="width:0;" data-target="{{ $ctrlComplexity }}"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Model Relationships Map --}}

{{-- Routes --}}
<section id="sec-routes" class="p-6" style="{{ $section === 'routes' ? '' : 'display:none' }}">

    @php
    $routeMethodCounts = [];
    foreach ($data['routes'] as $r) {
        foreach (array_filter($r['methods']??[], fn($m)=>$m!=='HEAD') as $m) {
            $routeMethodCounts[strtoupper($m)] = ($routeMethodCounts[strtoupper($m)] ?? 0) + 1;
        }
    }
    $routeMethodStyle = [
        'GET'    => 'bg-emerald-500 text-white',
        'POST'   => 'bg-blue-500 text-white',
        'PUT'    => 'bg-amber-500 text-white',
        'PATCH'  => 'bg-orange-500 text-white',
        'DELETE' => 'bg-red-500 text-white',
    ];
    $routeMethodColors = [
        'GET'    => ['hex'=>'#34D399','bg'=>'rgba(52,211,153,.12)','border'=>'rgba(52,211,153,.3)'],
        'POST'   => ['hex'=>'#60A5FA','bg'=>'rgba(96,165,250,.12)','border'=>'rgba(96,165,250,.3)'],
        'PUT'    => ['hex'=>'#A78BFA','bg'=>'rgba(167,139,250,.12)','border'=>'rgba(167,139,250,.3)'],
        'PATCH'  => ['hex'=>'#FB923C','bg'=>'rgba(251,146,60,.12)','border'=>'rgba(251,146,60,.3)'],
        'DELETE' => ['hex'=>'#F87171','bg'=>'rgba(248,113,113,.12)','border'=>'rgba(248,113,113,.3)'],
    ];
    $routeTotal      = $rs['total'] ?? 0;
    $routeAuthCount  = count(array_filter($data['routes'], fn($r) => in_array('auth', array_map('strtolower', $r['middleware']??[]))));
    $routeApiCount   = count($data['api_docs'] ?? []);
    $routePublic     = $routeTotal - $routeAuthCount;
    $routeDistTotal  = max(array_sum($routeMethodCounts), 1);
    @endphp

    {{-- List view --}}
    <div id="routes-list">

        {{-- Stats (models style) --}}
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--cyan);">{{ $routeTotal }}</span>
                <span class="mds-top-stat-lbl">Total Routes</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--emerald);">{{ $routeAuthCount }}</span>
                <span class="mds-top-stat-lbl">Auth Protected</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--amber);">{{ $routeApiCount }}</span>
                <span class="mds-top-stat-lbl">API Endpoints</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--rose);">{{ $routePublic }}</span>
                <span class="mds-top-stat-lbl">Public</span>
            </div>
        </div>
        <div class="mds-toolbar">
            <div style="position:relative;">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--text-faint);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input id="routes-search" oninput="filterRoutes()" type="search" placeholder="Search URI or handler…"
                    style="border:1px solid var(--border);border-radius:8px;padding:8px 12px 8px 32px;font-size:12px;width:220px;font-family:var(--font-mono);background:var(--bg-elevated);color:var(--text);">
            </div>
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ $routeTotal }} routes</span>
        </div>

        {{-- Table --}}
        <div style="background:var(--bg-elevated);border-radius:12px;border:1px solid var(--border);overflow:hidden;">
            <table class="w-full" style="border-collapse:collapse;">
                <thead>
                    <tr style="background:var(--bg-hover);border-bottom:1px solid var(--border);">
                        <th style="text-align:left;padding:12px 16px;font-family:var(--font-mono);font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.08em;width:100px;">Method</th>
                        <th style="text-align:left;padding:12px 16px;font-family:var(--font-mono);font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.08em;">URI</th>
                        <th style="text-align:left;padding:12px 16px;font-family:var(--font-mono);font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.08em;width:200px;">Handler</th>
                        <th style="text-align:left;padding:12px 16px;font-family:var(--font-mono);font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.08em;width:160px;">Middleware</th>
                        <th style="text-align:left;padding:12px 16px;font-family:var(--font-mono);font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.08em;width:200px;">Route Name</th>
                        <th style="width:32px;"></th>
                    </tr>
                </thead>
                <tbody id="routes-tbody">
                    @foreach($data['routes'] as $i => $route)
                    @php
                        $methods     = array_values(array_filter($route['methods']??[], fn($m)=>$m!=='HEAD'));
                        $ctrl        = class_basename($route['controller']['class']??'');
                        $action      = $route['controller']['method']??'Closure';
                        $isInvokable = $action === '__invoke';
                        $mwsRaw      = implode(',', array_map('strtolower', $route['middleware']??[]));
                        $allMws      = $route['middleware'] ?? [];
                        $mwCount     = count($allMws);
                        $primaryMw   = $mwCount > 0 ? class_basename($allMws[0]) : null;
                        $fullMwTitle = implode(' · ', $allMws);
                        $routeName   = $route['name'] ?? '';
                    @endphp
                    <tr class="route-row route-row-anim" style="--ri:{{$i}};border-bottom:1px solid var(--border);transition:background .15s;"
                        data-uri="{{ strtolower($route['uri']??'') }}"
                        data-methods="{{ implode(',',array_map('strtoupper',$methods)) }}"
                        data-mw="{{ $mwsRaw }}">

                        {{-- Method --}}
                        <td style="padding:10px 16px;">
                            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                                @foreach($methods as $m)
                                <span class="text-xs px-2 py-0.5 rounded-md font-bold method-{{ strtolower($m) }}" style="font-family:var(--font-mono);">{{ $m }}</span>
                                @endforeach
                            </div>
                        </td>

                        {{-- URI --}}
                        <td style="padding:10px 16px;">
                            <code style="font-size:12px;font-family:var(--font-mono);color:var(--text);">{{ $route['uri'] }}</code>
                        </td>

                        {{-- Handler --}}
                        <td style="padding:10px 16px;font-size:12px;">
                            @if($ctrl)
                                <span style="font-weight:700;color:var(--text);font-family:var(--font-mono);">{{ $ctrl }}</span>
                                @if(!$isInvokable)
                                    <span style="color:var(--border-strong);margin:0 2px;">@</span><span style="color:var(--text-faint);font-family:var(--font-mono);">{{ $action }}</span>
                                @endif
                            @else
                                <span style="color:var(--text-faint);font-style:italic;">Closure</span>
                            @endif
                        </td>

                        {{-- Middleware — compact single line --}}
                        <td style="padding:10px 16px;">
                            @if($mwCount === 0)
                                <span style="font-size:12px;color:var(--text-faint);">—</span>
                            @else
                                <div style="display:flex;align-items:center;gap:6px;" title="{{ $fullMwTitle }}">
                                    <span class="ctrl-chip" style="max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $allMws[0] }}">{{ $primaryMw }}</span>
                                    @if($mwCount > 1)
                                    <span class="ctrl-chip" style="white-space:nowrap;flex:none;" title="{{ $fullMwTitle }}">+{{ $mwCount - 1 }}</span>
                                    @endif
                                </div>
                            @endif
                        </td>

                        {{-- Route name --}}
                        <td style="padding:10px 16px;font-family:var(--font-mono);font-size:12px;color:var(--text-faint);">
                            @if($routeName)
                                <span style="display:block;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $routeName }}">{{ $routeName }}</span>
                            @else
                                <span style="color:var(--border-strong);">—</span>
                            @endif
                        </td>

                        {{-- Chevron --}}
                        <td style="padding:10px 16px 10px 0;text-align:right;">
                            <svg style="width:16px;height:16px;color:var(--text-faint);display:inline;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


</section>


{{-- Jobs --}}
<section id="sec-jobs" class="p-6" style="{{ $section === 'jobs' ? '' : 'display:none' }}">
    <div id="jobs-list">
        @php
            $jobsQueued  = count(array_filter($data['jobs'], fn($j) => $j['should_queue']??false));
            $jobsTimeout = count(array_filter($data['jobs'], fn($j) => !empty($j['timeout'])));
        @endphp
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ count($data['jobs']) }}</span>
                <span class="mds-top-stat-lbl">Total Jobs</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $jobsQueued }}</span>
                <span class="mds-top-stat-lbl">ShouldQueue</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $jobsTimeout }}</span>
                <span class="mds-top-stat-lbl">With Timeout</span>
            </div>
        </div>
        <div class="mds-toolbar">
            <input id="jobs-search" oninput="filterGrid('jobs')" type="search" placeholder="Search jobs…" style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;flex:1;max-width:240px;font-family:var(--font-mono);">
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ count($data['jobs']) }} jobs</span>
        </div>
        @if(empty($data['jobs']))
        <div class="atlas-card" style="text-align:center;padding:48px;"><p style="color:var(--text-faint);">No jobs found in <code>app/Jobs</code></p></div>
        @else
        <div id="jobs-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;">
            @foreach($data['jobs'] as $i => $job)
            <div class="sec2-card" style="--ci:{{$i}};border-left-color:#FF2D20;" onclick="showDetail('jobs',{{$i}})" data-name="{{ strtolower($job['name']) }}">
                <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:10px;">
                    <div class="sec2-icon" style="background:rgba(255,45,32,.10);color:#FF2D20;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div style="min-width:0;flex:1;">
                        <p class="sec2-name">{{ $job['name'] }}</p>
                        <p class="sec2-sub">queue: {{ $job['queue']??'default' }}</p>
                    </div>
                    @if($job['should_queue']??false)
                    <span class="sec2-chip" style="background:rgba(255,45,32,.08);color:#FF2D20;border-color:rgba(255,45,32,.20);flex:none;">queued</span>
                    @endif
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    @if($job['tries']??null)<span class="sec2-chip" style="background:var(--bg-hover);color:var(--text-dim);border-color:var(--border);">{{ $job['tries'] }} tries</span>@endif
                    @if($job['timeout']??null)<span class="sec2-chip" style="background:var(--bg-hover);color:var(--text-dim);border-color:var(--border);">{{ $job['timeout'] }}s timeout</span>@endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="jobs-detail" style="display:none"><div id="jobs-detail-content"></div></div>
</section>

{{-- Events --}}
<section id="sec-events" class="p-6" style="{{ $section === 'events' ? '' : 'display:none' }}">
    <div id="events-list">
        @php $evtBroadcast = count(array_filter($data['events'], fn($e) => $e['should_broadcast']??false)); @endphp
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ count($data['events']) }}</span>
                <span class="mds-top-stat-lbl">Total Events</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $evtBroadcast }}</span>
                <span class="mds-top-stat-lbl">Broadcast</span>
            </div>
        </div>
        <div class="mds-toolbar">
            <input id="events-search" oninput="filterGrid('events')" type="search" placeholder="Search events…" style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;flex:1;max-width:240px;font-family:var(--font-mono);">
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ count($data['events']) }} events</span>
        </div>
        @if(empty($data['events']))
        <div class="atlas-card" style="text-align:center;padding:48px;"><p style="color:var(--text-faint);">No events found in <code>app/Events</code></p></div>
        @else
        <div id="events-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;">
            @foreach($data['events'] as $i => $evt)
            <div class="sec2-card" style="--ci:{{$i}};border-left-color:#FF2D20;" onclick="showDetail('jobs','events',{{$i}})" data-name="{{ strtolower($evt['name']) }}">
                <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:8px;">
                    <div class="sec2-icon" style="background:rgba(255,45,32,.10);color:#FF2D20;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <div style="min-width:0;flex:1;">
                        <p class="sec2-name">{{ $evt['name'] }}</p>
                        <p class="sec2-sub">{{ $evt['namespace'] }}</p>
                    </div>
                    @if($evt['should_broadcast']??false)
                    <span class="sec2-chip" style="background:rgba(255,45,32,.08);color:#FF2D20;border-color:rgba(255,45,32,.20);flex:none;">broadcast</span>
                    @endif
                </div>
                @if(!empty($evt['properties']))
                <div style="margin-top:8px;font-size:11px;color:var(--text-faint);font-family:var(--font-mono);">{{ count($evt['properties']) }} payload props</div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="events-detail" style="display:none"><div id="events-detail-content"></div></div>
</section>

{{-- Services --}}
<section id="sec-services" class="p-6" style="{{ $section === 'services' ? '' : 'display:none' }}">
    <div id="services-list">
        @php
            $svcTotal   = count($data['services']);
            $svcMethods = $svcTotal > 0 ? round(array_sum(array_map(fn($s) => count($s['methods']??[]), $data['services'])) / $svcTotal) : 0;
        @endphp
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $svcTotal }}</span>
                <span class="mds-top-stat-lbl">Total Services</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $svcMethods }}</span>
                <span class="mds-top-stat-lbl">Avg Methods</span>
            </div>
        </div>
        <div class="mds-toolbar">
            <input id="services-search" oninput="filterGrid('services')" type="search" placeholder="Search services…" style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;flex:1;max-width:240px;font-family:var(--font-mono);">
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ $svcTotal }} services</span>
        </div>
        @if(empty($data['services']))
        <div class="atlas-card" style="text-align:center;padding:48px;"><p style="color:var(--text-faint);">No services found in <code>app/Services</code></p></div>
        @else
        <div id="services-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;">
            @foreach($data['services'] as $i => $svc)
            <div class="sec2-card" style="--ci:{{$i}};border-left-color:#FF2D20;" onclick="showDetail('services',{{$i}})" data-name="{{ strtolower($svc['name']) }}">
                <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:10px;">
                    <div class="sec2-icon" style="background:rgba(255,45,32,.10);color:#FF2D20;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div style="min-width:0;flex:1;">
                        <p class="sec2-name">{{ $svc['name'] }}</p>
                        <p class="sec2-sub">{{ $svc['namespace'] }}</p>
                    </div>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <span class="sec2-chip" style="background:rgba(255,45,32,.08);color:#FF2D20;border-color:rgba(255,45,32,.20);">{{ count($svc['methods']??[]) }} methods</span>
                    @if(!empty($svc['dependencies']))<span class="sec2-chip" style="background:var(--bg-hover);color:var(--text-dim);border-color:var(--border);">{{ count($svc['dependencies']) }} deps</span>@endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="services-detail" style="display:none"><div id="services-detail-content"></div></div>
</section>

{{-- Repositories --}}
<section id="sec-repositories" class="p-6" style="{{ $section === 'repositories' ? '' : 'display:none' }}">
    <div id="repositories-list">
        @php
            $repoTotal    = count($data['repositories']);
            $repoMethods  = $repoTotal > 0 ? round(array_sum(array_map(fn($r) => count($r['methods']??[]), $data['repositories'])) / $repoTotal) : 0;
            $repoDeps     = array_sum(array_map(fn($r) => count($r['dependencies']??[]), $data['repositories']));
        @endphp
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $repoTotal }}</span>
                <span class="mds-top-stat-lbl">Repositories</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $repoMethods }}</span>
                <span class="mds-top-stat-lbl">Avg Methods</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $repoDeps }}</span>
                <span class="mds-top-stat-lbl">Total Deps</span>
            </div>
        </div>
        <div class="mds-toolbar">
            <input id="repositories-search" oninput="filterGrid('repositories')" type="search" placeholder="Search repositories…" style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;flex:1;max-width:240px;font-family:var(--font-mono);">
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ $repoTotal }} repositories</span>
        </div>
        @if(empty($data['repositories']))
        <div class="atlas-card" style="text-align:center;padding:48px;"><p style="color:var(--text-faint);">No repositories found in <code>app/Repositories</code></p></div>
        @else
        <div id="repositories-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;">
            @foreach($data['repositories'] as $i => $repo)
            @php $repoDotCount = min(count($repo['dependencies']??[]), 8); @endphp
            <div class="repo-card" style="--ci:{{$i}};background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;overflow:hidden;cursor:pointer;box-shadow:var(--shadow);transition:transform .2s,box-shadow .2s,border-color .2s;"
                 onclick="showDetail('repositories',{{$i}})" data-name="{{ strtolower($repo['name']) }}"
                 onmouseenter="this.style.borderColor='rgba(99,102,241,0.4)';this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 28px rgba(99,102,241,0.10)'"
                 onmouseleave="this.style.borderColor='var(--border)';this.style.transform='';this.style.boxShadow='var(--shadow)'">
                {{-- Top bar --}}
                <div style="height:5px;background:linear-gradient(90deg,var(--cyan),var(--cyan-bright));"></div>
                <div style="padding:18px;">
                    <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:14px;">
                        <div style="width:38px;height:38px;border-radius:10px;background:rgba(255,45,32,.10);color:#FF2D20;display:flex;align-items:center;justify-content:center;flex:none;border:1px solid rgba(99,102,241,.2);">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </div>
                        <div style="min-width:0;flex:1;">
                            <p style="font-weight:700;font-size:14px;color:var(--text);margin:0 0 3px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $repo['name'] }}</p>
                            <p style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $repo['namespace'] }}</p>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                        <span class="sec2-chip" style="background:rgba(255,45,32,.08);color:#FF2D20;border-color:rgba(255,45,32,.20);">{{ count($repo['methods']??[]) }} methods</span>
                        {{-- Dependency dots --}}
                        @if($repoDotCount > 0)
                        <div style="display:flex;align-items:center;gap:3px;" title="{{ count($repo['dependencies']??[]) }} dependencies">
                            @for($d=0;$d<$repoDotCount;$d++)
                            <div class="repo-dep-dot" style="opacity:{{ round(1 - ($d / max($repoDotCount,1)) * 0.5, 2) }};"></div>
                            @endfor
                            @if(count($repo['dependencies']??[]) > 8)
                            <span style="font-size:9px;color:var(--text-faint);font-family:var(--font-mono);">+{{ count($repo['dependencies'])-8 }}</span>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="repositories-detail" style="display:none">
        <div id="repositories-detail-content"></div>
    </div>
</section>

{{-- Observers --}}
<section id="sec-observers" class="p-6" style="{{ $section === 'observers' ? '' : 'display:none' }}">
    <div id="observers-list">
        @php
            $obsModels = count(array_unique(array_filter(array_column($data['observers'], 'model'))));
        @endphp
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ count($data['observers']) }}</span>
                <span class="mds-top-stat-lbl">Total Observers</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $obsModels }}</span>
                <span class="mds-top-stat-lbl">Models Covered</span>
            </div>
        </div>
        <div class="mds-toolbar">
            <input id="observers-search" oninput="filterGrid('observers')" type="search" placeholder="Search observers…" style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;flex:1;max-width:240px;font-family:var(--font-mono);">
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ count($data['observers']) }} observers</span>
        </div>
        @if(empty($data['observers']))
        <div class="atlas-card" style="text-align:center;padding:48px;"><p style="color:var(--text-faint);">No observers found in <code>app/Observers</code></p></div>
        @else
        <div id="observers-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;">
            @foreach($data['observers'] as $i => $obs)
            <div class="sec2-card" style="--ci:{{$i}};border-left-color:#FF2D20;" onclick="showDetail('observers',{{$i}})" data-name="{{ strtolower($obs['name']) }}">
                <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:10px;">
                    <div class="sec2-icon" style="background:rgba(255,45,32,.10);color:#FF2D20;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <div style="min-width:0;flex:1;">
                        <p class="sec2-name">{{ $obs['name'] }}</p>
                        <p class="sec2-sub">observes: <span style="color:var(--text);">{{ $obs['model']??'Unknown' }}</span></p>
                    </div>
                </div>
                @if(!empty($obs['events']))
                <div style="display:flex;flex-wrap:wrap;gap:5px;">
                    @foreach($obs['events'] as $e)
                    <span class="sec2-chip" style="background:rgba(255,45,32,.08);color:#FF2D20;border-color:rgba(255,45,32,.20);">{{ $e }}</span>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="observers-detail" style="display:none"><div id="observers-detail-content"></div></div>
</section>

{{-- Policies --}}
<section id="sec-policies" class="p-6" style="{{ $section === 'policies' ? '' : 'display:none' }}">
    <div id="policies-list">
        @php
            $polActions = array_sum(array_map(fn($p) => count($p['actions']??[]), $data['policies']));
        @endphp
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ count($data['policies']) }}</span>
                <span class="mds-top-stat-lbl">Total Policies</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $polActions }}</span>
                <span class="mds-top-stat-lbl">Total Actions</span>
            </div>
        </div>
        <div class="mds-toolbar">
            <input id="policies-search" oninput="filterGrid('policies')" type="search" placeholder="Search policies…" style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;flex:1;max-width:240px;font-family:var(--font-mono);">
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ count($data['policies']) }} policies</span>
        </div>
        @if(empty($data['policies']))
        <div class="atlas-card" style="text-align:center;padding:48px;"><p style="color:var(--text-faint);">No policies found in <code>app/Policies</code></p></div>
        @else
        <div id="policies-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;">
            @foreach($data['policies'] as $i => $pol)
            <div class="sec2-card" style="--ci:{{$i}};border-left-color:#FF2D20;" onclick="showDetail('policies',{{$i}})" data-name="{{ strtolower($pol['name']) }}">
                <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:10px;">
                    <div class="sec2-icon" style="background:rgba(255,45,32,.10);color:#FF2D20;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div style="min-width:0;flex:1;">
                        <p class="sec2-name">{{ $pol['name'] }}</p>
                        <p class="sec2-sub">guards: <span style="color:var(--text);">{{ $pol['model']??'Unknown' }}</span></p>
                    </div>
                </div>
                @if(!empty($pol['actions']))
                <div style="display:flex;flex-wrap:wrap;gap:5px;">
                    @foreach($pol['actions'] as $a)
                    <span class="sec2-chip" style="background:rgba(255,45,32,.08);color:#FF2D20;border-color:rgba(255,45,32,.20);">{{ $a }}</span>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="policies-detail" style="display:none"><div id="policies-detail-content"></div></div>
</section>

{{-- Dependencies --}}

{{-- ══ MODULE EXPLORER ══ --}}
<section id="sec-modules" class="p-6" style="{{ $section === 'modules' ? '' : 'display:none' }}">

    @php
        $modules    = $data['modules'] ?? [];
        $modCtrl    = count($modules) > 0 ? array_sum(array_column($modules, 'controllers')) : 0;
        $modModel   = count($modules) > 0 ? array_sum(array_column($modules, 'models'))      : 0;
        $modRoute   = count($modules) > 0 ? array_sum(array_column($modules, 'routes'))      : 0;
    @endphp
    <div class="mds-top-stats">
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ count($modules) }}</span>
            <span class="mds-top-stat-lbl">Total Modules</span>
        </div>
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $modCtrl }}</span>
            <span class="mds-top-stat-lbl">Controllers</span>
        </div>
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $modModel }}</span>
            <span class="mds-top-stat-lbl">Models</span>
        </div>
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $modRoute }}</span>
            <span class="mds-top-stat-lbl">Routes</span>
        </div>
    </div>
    <div class="mds-toolbar">
        <input id="modules-search" oninput="filterGrid('modules')" type="search" placeholder="Search modules…" style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;flex:1;max-width:240px;font-family:var(--font-mono);">
        <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ count($modules) }} modules</span>
    </div>

    @if(empty($modules))
    <div class="atlas-card" style="text-align:center;padding:64px;">
        <div style="width:56px;height:56px;background:var(--bg-hover);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg style="width:28px;height:28px;color:var(--text-faint);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <p style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:6px;">No modules detected</p>
        <p style="font-size:13px;color:var(--text-faint);">Create a <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">Modules/</code> directory at your project root with subfolders per module.</p>
        <p style="font-size:12px;color:var(--text-faint);margin-top:8px;">Compatible with <a style="color:var(--cyan);" href="https://nwidart.com/laravel-modules" target="_blank">nwidart/laravel-modules</a> structure.</p>
    </div>
    @else

    {{-- Module cards --}}
    <div id="modules-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
        @php
        $modPalette = [
            ['color'=>'#A78BFA','bg'=>'rgba(167,139,250,.15)','border'=>'rgba(167,139,250,.3)'],
            ['color'=>'#6366F1','bg'=>'rgba(99,102,241,.15)','border'=>'rgba(99,102,241,.3)'],
            ['color'=>'#34D399','bg'=>'rgba(52,211,153,.15)','border'=>'rgba(52,211,153,.3)'],
            ['color'=>'#60A5FA','bg'=>'rgba(96,165,250,.15)','border'=>'rgba(96,165,250,.3)'],
            ['color'=>'#F87171','bg'=>'rgba(248,113,113,.15)','border'=>'rgba(248,113,113,.3)'],
            ['color'=>'#FBBF24','bg'=>'rgba(251,191,36,.15)','border'=>'rgba(251,191,36,.3)'],
            ['color'=>'#2DD4BF','bg'=>'rgba(45,212,191,.15)','border'=>'rgba(45,212,191,.3)'],
            ['color'=>'#FB923C','bg'=>'rgba(251,146,60,.15)','border'=>'rgba(251,146,60,.3)'],
            ['color'=>'#E879F9','bg'=>'rgba(232,121,249,.15)','border'=>'rgba(232,121,249,.3)'],
            ['color'=>'#38BDF8','bg'=>'rgba(56,189,248,.15)','border'=>'rgba(56,189,248,.3)'],
        ];
        @endphp
        @foreach($modules as $i => $mod)
        @php
        $mp      = $modPalette[$i % count($modPalette)];
        $initial = strtoupper(substr($mod['name'], 0, 1));
        $hasExtras = $mod['jobs'] > 0 || $mod['events'] > 0 || $mod['services'] > 0;
        @endphp
        <div data-name="{{ strtolower($mod['name']) }}" style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:16px;overflow:hidden;display:flex;flex-direction:column;transition:border-color .2s,transform .2s;" onmouseenter="this.style.borderColor='{{ $mp['border'] }}';this.style.transform='translateY(-3px)'" onmouseleave="this.style.borderColor='var(--border)';this.style.transform=''">
            {{-- Top glow bar --}}
            <div style="height:3px;background:linear-gradient(90deg,{{ $mp['color'] }},transparent);"></div>
            {{-- Header --}}
            <div style="display:flex;align-items:center;gap:12px;padding:16px 18px;border-bottom:1px solid var(--border);">
                <div style="width:42px;height:42px;border-radius:12px;background:{{ $mp['bg'] }};color:{{ $mp['color'] }};border:1px solid {{ $mp['border'] }};display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800;flex:none;">{{ $initial }}</div>
                <div style="min-width:0;flex:1;">
                    <p style="font-weight:700;font-size:14px;color:var(--text);line-height:1.25;">{{ $mod['name'] }}</p>
                    <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-top:2px;">{{ $mod['path'] }}</p>
                </div>
            </div>
            {{-- Core stats --}}
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;border-bottom:1px solid var(--border);">
                <div style="text-align:center;padding:12px 8px;border-right:1px solid var(--border);">
                    <p style="font-size:20px;font-weight:800;color:var(--violet);font-family:var(--font-sans);">{{ $mod['controllers'] }}</p>
                    <p style="font-size:10px;color:var(--text-faint);font-family:var(--font-mono);margin-top:2px;">Controllers</p>
                </div>
                <div style="text-align:center;padding:12px 8px;border-right:1px solid var(--border);">
                    <p style="font-size:20px;font-weight:800;color:var(--cyan);font-family:var(--font-sans);">{{ $mod['models'] }}</p>
                    <p style="font-size:10px;color:var(--text-faint);font-family:var(--font-mono);margin-top:2px;">Models</p>
                </div>
                <div style="text-align:center;padding:12px 8px;">
                    <p style="font-size:20px;font-weight:800;color:var(--emerald);font-family:var(--font-sans);">{{ $mod['routes'] }}</p>
                    <p style="font-size:10px;color:var(--text-faint);font-family:var(--font-mono);margin-top:2px;">Routes</p>
                </div>
            </div>
            {{-- Extra chips --}}
            <div style="padding:12px 18px;display:flex;flex-wrap:wrap;gap:6px;">
                @if($mod['jobs'] > 0)
                <span style="display:inline-flex;align-items:center;gap:4px;font-size:10px;font-family:var(--font-mono);color:var(--amber);background:rgba(251,191,36,.1);padding:3px 9px;border-radius:20px;border:1px solid rgba(251,191,36,.2);">
                    <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9" stroke-width="2"/></svg>
                    {{ $mod['jobs'] }} Jobs
                </span>
                @endif
                @if($mod['events'] > 0)
                <span style="display:inline-flex;align-items:center;gap:4px;font-size:10px;font-family:var(--font-mono);color:var(--violet);background:rgba(167,139,250,.1);padding:3px 9px;border-radius:20px;border:1px solid rgba(167,139,250,.2);">
                    <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    {{ $mod['events'] }} Events
                </span>
                @endif
                @if($mod['services'] > 0)
                <span style="display:inline-flex;align-items:center;gap:4px;font-size:10px;font-family:var(--font-mono);color:var(--sky);background:rgba(96,165,250,.1);padding:3px 9px;border-radius:20px;border:1px solid rgba(96,165,250,.2);">
                    <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    {{ $mod['services'] }} Services
                </span>
                @endif
                @if(!$hasExtras)
                <span style="font-size:11px;color:var(--text-faint);font-style:italic;">No jobs, events, or services</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

</section>

{{-- ══ MIDDLEWARE EXPLORER ══ --}}
<section id="sec-middleware" class="p-6" style="{{ $section === 'middleware' ? '' : 'display:none' }}">
    @php
        $mwUsage   = $rs['middleware_usage'] ?? [];
        $mwTotal   = count($mwUsage);
        $mwTopName = $mwTotal > 0 ? array_key_first($mwUsage) : '—';
        $mwTopCnt  = $mwTotal > 0 ? reset($mwUsage) : 0;
        $mwAuthCnt = $mwUsage['auth'] ?? 0;
    @endphp
    <div class="mds-top-stats">
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $mwTotal }}</span>
            <span class="mds-top-stat-lbl">Total Middleware</span>
        </div>
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $mwTopCnt }}</span>
            <span class="mds-top-stat-lbl">Most Used Routes</span>
        </div>
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $mwAuthCnt }}</span>
            <span class="mds-top-stat-lbl">Auth Protected</span>
        </div>
    </div>
    <div class="mds-toolbar">
        <input id="middleware-search" oninput="filterGrid('middleware')" type="search" placeholder="Search middleware…" style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;flex:1;max-width:240px;font-family:var(--font-mono);">
        <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ $mwTotal }} middleware</span>
    </div>
    @if(empty($mwUsage))
    <div class="atlas-card" style="text-align:center;padding:48px;">
        <p style="color:var(--text-faint);">No middleware found in routes.</p>
    </div>
    @else
    @php $mwMax = max(array_values($mwUsage) ?: [1]); @endphp
    <div class="atlas-card" style="padding:0;overflow:hidden;">
        <div style="display:grid;grid-template-columns:40px 1fr 180px 80px;gap:10px;padding:10px 16px;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--text-faint);font-family:var(--font-mono);background:var(--bg-sunken);border-bottom:1px solid var(--border);">
            <div></div>
            <div>Middleware</div>
            <div>Usage</div>
            <div style="text-align:right;">Routes</div>
        </div>
        <div id="middleware-grid">
            @foreach($mwUsage as $mwName => $mwCnt)
            <div class="mw-row" data-name="{{ strtolower($mwName) }}">
                <div style="width:32px;height:32px;border-radius:9px;background:rgba(255,45,32,.10);border:1px solid rgba(255,45,32,.20);color:#FF2D20;display:flex;align-items:center;justify-content:center;flex:none;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div style="font-family:var(--font-mono);font-size:13px;font-weight:600;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $mwName }}</div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="flex:1;background:var(--bg-hover);border-radius:4px;height:6px;overflow:hidden;">
                        <div style="height:100%;width:{{ round($mwCnt / $mwMax * 100) }}%;background:#FF2D20;border-radius:4px;"></div>
                    </div>
                    <span style="font-size:10px;color:var(--text-faint);font-family:var(--font-mono);white-space:nowrap;">{{ round($mwCnt / $mwMax * 100) }}%</span>
                </div>
                <div style="text-align:right;font-family:var(--font-mono);font-size:13px;font-weight:700;color:#FF2D20;">{{ $mwCnt }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</section>

{{-- ══ PACKAGE DETECTION ══ --}}
<section id="sec-packages" class="p-6" style="{{ $section === 'packages' ? '' : 'display:none' }}">

    @php
    $packages   = $data['packages'] ?? [];
    $byCategory = [];
    foreach ($packages as $pkg) {
        $byCategory[$pkg['category']][] = $pkg;
    }
    ksort($byCategory);

    $devCount = count(array_filter($packages, fn($p) => $p['dev']));

    $categoryMeta = [
        'Admin Panel'       => ['color'=>'#FBBF24','bg'=>'rgba(251,191,36,.1)','border'=>'#FBBF24','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>'],
        'API Authentication'=> ['color'=>'#60A5FA','bg'=>'rgba(96,165,250,.1)','border'=>'#60A5FA','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>'],
        'Architecture'      => ['color'=>'#A78BFA','bg'=>'rgba(167,139,250,.1)','border'=>'#A78BFA','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>'],
        'Audit'             => ['color'=>'#F87171','bg'=>'rgba(248,113,113,.1)','border'=>'#F87171','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'],
        'Auth Scaffolding'  => ['color'=>'#E879F9','bg'=>'rgba(232,121,249,.1)','border'=>'#E879F9','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
        'Authorization'     => ['color'=>'#34D399','bg'=>'rgba(52,211,153,.1)','border'=>'#34D399','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'],
        'Backup'            => ['color'=>'#34D399','bg'=>'rgba(52,211,153,.1)','border'=>'#34D399','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>'],
        'Debug'             => ['color'=>'#94A3B8','bg'=>'rgba(148,163,184,.1)','border'=>'#94A3B8','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>'],
        'Import / Export'   => ['color'=>'#34D399','bg'=>'rgba(52,211,153,.1)','border'=>'#34D399','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>'],
        'Media'             => ['color'=>'#818CF8','bg'=>'rgba(129,140,248,.1)','border'=>'#818CF8','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>'],
        'Payments'          => ['color'=>'#A78BFA','bg'=>'rgba(167,139,250,.1)','border'=>'#A78BFA','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>'],
        'PDF'               => ['color'=>'#F87171','bg'=>'rgba(248,113,113,.1)','border'=>'#F87171','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>'],
        'Queue Monitoring'  => ['color'=>'#2DD4BF','bg'=>'rgba(45,212,191,.1)','border'=>'#2DD4BF','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'],
        'Search'            => ['color'=>'#60A5FA','bg'=>'rgba(96,165,250,.1)','border'=>'#60A5FA','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>'],
        'UI Framework'      => ['color'=>'#A78BFA','bg'=>'rgba(167,139,250,.1)','border'=>'#A78BFA','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>'],
    ];
    $defaultCatMeta = ['color'=>'#94A3B8','bg'=>'rgba(148,163,184,.1)','border'=>'#94A3B8','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>'];

    $dotHexColors = [
        'pink'=>'#F472B6','purple'=>'#C084FC','red'=>'#F87171','blue'=>'#60A5FA',
        'orange'=>'#FB923C','violet'=>'#A78BFA','amber'=>'#FBBF24',
        'sky'=>'#38BDF8','emerald'=>'#34D399','green'=>'#4ADE80',
        'teal'=>'#2DD4BF','slate'=>'#94A3B8','cyan'=>'#818CF8','indigo'=>'#818CF8',
        'rose'=>'#FB7185',
    ];
    @endphp

    @if(empty($packages))
    <div class="atlas-card" style="text-align:center;padding:64px;">
        <div style="width:56px;height:56px;background:var(--bg-hover);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg style="width:28px;height:28px;color:var(--text-faint);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
        </div>
        <p style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:6px;">No known packages detected</p>
        <p style="font-size:13px;color:var(--text-faint);">None of the tracked packages appear in your <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">composer.json</code>.</p>
    </div>
    @else

    {{-- Stats --}}
    <div class="mds-top-stats">
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ count($packages) }}</span>
            <span class="mds-top-stat-lbl">Total Packages</span>
        </div>
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ count($byCategory) }}</span>
            <span class="mds-top-stat-lbl">Categories</span>
        </div>
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $devCount }}</span>
            <span class="mds-top-stat-lbl">Dev Only</span>
        </div>
    </div>
    <div class="mds-toolbar">
        <input id="packages-search" oninput="filterPackages()" type="search" placeholder="Search packages…" style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;flex:1;max-width:240px;font-family:var(--font-mono);">
        <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ count($packages) }} packages</span>
    </div>

    {{-- Categories --}}
    <div id="packages-categories">
    @php $globalPkgIdx = 0; @endphp
    @foreach($byCategory as $category => $pkgs)
    @php $catMeta = $categoryMeta[$category] ?? $defaultCatMeta; @endphp
    <div style="margin-bottom:32px;">

        {{-- Category Header --}}
        <div class="pkg-cat-header" style="background:{{ $catMeta['bg'] }};border-left-color:{{ $catMeta['border'] }};">
            <div style="width:32px;height:32px;border-radius:8px;background:{{ $catMeta['bg'] }};border:1px solid {{ $catMeta['border'] }}40;display:flex;align-items:center;justify-content:center;flex:none;">
                <svg style="width:16px;height:16px;color:{{ $catMeta['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $catMeta['icon'] !!}</svg>
            </div>
            <span style="font-weight:700;font-size:13px;color:{{ $catMeta['color'] }};flex:1;">{{ $category }}</span>
            <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:{{ $catMeta['color'] }}22;color:{{ $catMeta['color'] }};border:1px solid {{ $catMeta['color'] }}44;">{{ count($pkgs) }} pkg{{ count($pkgs) !== 1 ? 's' : '' }}</span>
        </div>

        {{-- Cards Grid --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;">
            @foreach($pkgs as $pkg)
            @php
                $dotHex  = $dotHexColors[$pkg['color']] ?? '#94A3B8';
                $initials = strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $pkg['name']), 0, 2))));
                $hasDoc  = !empty($pkg['docs']);
            @endphp
            <div class="pkg-card" data-name="{{ strtolower($pkg['name'] . ' ' . $pkg['key']) }}" style="--pkg-i:{{ $globalPkgIdx++ }};background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;overflow:hidden;display:flex;flex-direction:column;transition:border-color .2s,transform .2s,box-shadow .2s;" onmouseenter="this.style.borderColor='{{ $dotHex }}88';this.style.boxShadow='0 8px 28px {{ $dotHex }}22';" onmouseleave="this.style.borderColor='var(--border)';this.style.boxShadow='';">
                {{-- Colored top bar --}}
                <div style="height:6px;background:linear-gradient(90deg,{{ $dotHex }},{{ $dotHex }}99);"></div>

                <div style="padding:16px;display:flex;flex-direction:column;gap:12px;flex:1;">
                    {{-- Header row: avatar + name + badges --}}
                    <div style="display:flex;align-items:flex-start;gap:12px;">
                        {{-- Avatar --}}
                        <div style="width:42px;height:42px;border-radius:10px;background:{{ $dotHex }}18;border:1px solid {{ $dotHex }}33;display:flex;align-items:center;justify-content:center;flex:none;font-weight:800;font-size:13px;color:{{ $dotHex }};letter-spacing:.02em;">{{ $initials }}</div>
                        {{-- Name & badges --}}
                        <div style="min-width:0;flex:1;padding-top:2px;">
                            <p style="font-weight:700;font-size:14px;color:var(--text);line-height:1.3;margin-bottom:5px;">{{ $pkg['name'] }}</p>
                            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                                @if($pkg['version'])
                                <span class="pkg-ver-badge" style="font-family:var(--font-mono);font-size:10px;background:{{ $dotHex }}15;color:{{ $dotHex }};padding:2px 8px;border-radius:5px;border:1px solid {{ $dotHex }}33;font-weight:600;">v{{ $pkg['version'] }}</span>
                                @endif
                                @if($pkg['dev'])
                                <span style="font-family:var(--font-mono);font-size:10px;color:var(--amber);background:rgba(251,191,36,.12);padding:2px 8px;border-radius:5px;border:1px solid rgba(251,191,36,.25);font-weight:600;">dev-only</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <p style="font-size:12px;color:var(--text-dim);line-height:1.6;flex:1;">{{ $pkg['description'] }}</p>

                    {{-- Composer key --}}
                    <div style="background:var(--bg-hover);border:1px solid var(--border);border-radius:8px;padding:8px 10px;display:flex;align-items:center;justify-content:space-between;gap:8px;">
                        <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $pkg['key'] }}</span>
                        <button onclick="copyPkgKey(this,'{{ $pkg['key'] }}')" title="Copy composer require command" style="flex:none;display:flex;align-items:center;gap:4px;background:transparent;border:none;color:var(--text-faint);cursor:pointer;padding:2px 4px;border-radius:4px;font-size:10px;transition:color .15s;" onmouseenter="this.style.color='var(--text)'" onmouseleave="this.style.color='var(--text-faint)'">
                            <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>

                    {{-- Docs link --}}
                    @if($hasDoc)
                    <a href="{{ $pkg['docs'] }}" target="_blank" rel="noopener" class="pkg-docs-btn" style="color:{{ $dotHex }};border-color:{{ $dotHex }}44;background:{{ $dotHex }}10;align-self:flex-start;" onmouseenter="this.style.background='{{ $dotHex }}20'" onmouseleave="this.style.background='{{ $dotHex }}10'">
                        <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        View Docs
                        <svg style="width:11px;height:11px;opacity:.7;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
    </div>{{-- /packages-categories --}}

    @endif

</section>

{{-- Export --}}

{{-- AI Insights --}}
<section id="sec-ai" class="p-6" style="{{ $section === 'ai' ? '' : 'display:none' }}">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;flex-wrap:wrap;gap:12px;">
        <div class="sec-header" style="margin-bottom:0;">
            <div class="sec-header__icon" style="background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.18);color:var(--cyan);">
                <svg viewBox="0 0 24 24"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
            <div>
                <h1 class="sec-header__title">AI Insights</h1>
                <p class="sec-header__sub">AI-powered architecture review — score, SOLID analysis, code smells, and actionable suggestions.</p>
            </div>
        </div>
        @if(config('laradar.ai.enabled', false))
        <span style="display:flex;align-items:center;gap:6px;font-family:var(--font-mono);font-size:11px;color:var(--emerald);background:rgba(52,211,153,0.12);border:1px solid rgba(52,211,153,0.25);padding:5px 12px;border-radius:20px;">
            <span style="width:6px;height:6px;border-radius:50%;background:var(--emerald);"></span>
            AI Ready · {{ config('laradar.ai.model', 'gemini-2.5-flash') }}
        </span>
        @else
        <span style="display:flex;align-items:center;gap:6px;font-family:var(--font-mono);font-size:11px;color:var(--text-faint);background:var(--bg-hover);border:1px solid var(--border);padding:5px 12px;border-radius:20px;">
            <span style="width:6px;height:6px;border-radius:50%;background:var(--text-faint);"></span>
            AI Disabled
        </span>
        @endif
    </div>
    <div style="margin-bottom:24px;"></div>

    @if(!config('laradar.ai.enabled', false))
    {{-- Setup card --}}
    <div style="max-width:560px;background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.25);border-radius:12px;padding:24px;margin-bottom:24px;">
        <div style="display:flex;align-items:flex-start;gap:12px;">
            <svg style="width:20px;height:20px;color:var(--amber);margin-top:2px;flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <p style="font-weight:700;color:var(--amber);margin-bottom:8px;">AI is not enabled</p>
                <p style="font-size:13px;color:var(--text-dim);margin-bottom:12px;">To enable AI insights, add the following to your <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">.env</code> file and publish the config:</p>
                <div style="background:var(--bg-sunken);border-radius:8px;padding:12px;font-family:var(--font-mono);font-size:12px;color:var(--emerald);margin-bottom:10px;">
                    GEMINI_API_KEY=your_api_key_here
                </div>
                <p style="font-size:13px;color:var(--text-dim);margin-bottom:8px;">Then in <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">config/laradar.php</code>:</p>
                <div style="background:var(--bg-sunken);border-radius:8px;padding:12px;font-family:var(--font-mono);font-size:12px;color:var(--text);">
                    'ai' => [<br>
                    &nbsp;&nbsp;'enabled' => <span style="color:var(--emerald);">true</span>,<br>
                    &nbsp;&nbsp;'provider' => 'gemini',<br>
                    &nbsp;&nbsp;'model' => 'gemini-2.5-flash',<br>
                    ]
                </div>
                <p style="font-size:11px;color:var(--text-faint);margin-top:10px;font-family:var(--font-mono);">Get a free API key at aistudio.google.com</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Analyze button --}}
    <div id="ai-trigger" style="margin-bottom:28px;">
        <button onclick="aiAnalyze()" class="ai-analyze-btn" {{ !config('laradar.ai.enabled', false) ? 'disabled' : '' }}>
            <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            Analyze with AI
        </button>
        <p style="font-size:12px;color:var(--text-faint);margin-top:10px;font-family:var(--font-mono);">
            Sends your architecture to <span style="color:var(--cyan);">{{ config('laradar.ai.model', 'gemini-2.5-flash') }}</span> · Takes 10–30 seconds
        </p>
    </div>

    {{-- Loading state --}}
    <div id="ai-loading" style="display:none;margin-bottom:28px;">
        <div style="display:inline-flex;align-items:center;gap:14px;background:rgba(99,102,241,.06);border:1px solid rgba(99,102,241,.18);border-radius:12px;padding:14px 20px;">
            <svg style="width:22px;height:22px;color:var(--cyan);animation:aiSpin 1s linear infinite;flex:none;" fill="none" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="40 20" opacity=".3"></circle>
                <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" opacity=".8"></path>
            </svg>
            <div>
                <p style="font-size:13px;font-weight:700;color:var(--cyan);font-family:var(--font-mono);margin-bottom:2px;">Analyzing architecture…</p>
                <p style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);">Usually takes 10–30 seconds</p>
            </div>
        </div>
    </div>

    {{-- Error state --}}
    <div id="ai-error" style="display:none;margin-bottom:24px;max-width:560px;background:rgba(248,113,113,0.08);border:1px solid rgba(248,113,113,0.3);border-radius:12px;padding:16px;">
        <p style="font-size:13px;font-weight:600;color:var(--rose);margin-bottom:4px;">Analysis failed</p>
        <p id="ai-error-msg" style="font-size:12px;color:var(--rose);font-family:var(--font-mono);"></p>
    </div>

    {{-- Results --}}
    <div id="ai-results" style="display:none;max-width:900px;display:flex;flex-direction:column;gap:16px;">

        {{-- Summary + AI Score --}}
        <div style="display:flex;gap:16px;flex-wrap:wrap;">
            <div style="flex:1;min-width:240px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
                <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:10px;">AI Summary</p>
                <p id="ai-summary" style="font-size:13px;color:var(--text-dim);line-height:1.65;"></p>
            </div>
            <div style="width:160px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;flex:none;">
                <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);">AI Score</p>
                <div style="position:relative;width:90px;height:90px;">
                    <svg width="90" height="90" viewBox="0 0 90 90">
                        <circle cx="45" cy="45" r="36" fill="none" stroke="var(--bg-sunken)" stroke-width="7"/>
                        <circle id="ai-score-ring" class="ai-score-ring" cx="45" cy="45" r="36" fill="none"
                            stroke="var(--cyan)" stroke-width="7" stroke-linecap="round"
                            stroke-dasharray="226" stroke-dashoffset="226"
                            style="transition:stroke-dashoffset .9s cubic-bezier(.4,0,.2,1),stroke .4s;"/>
                    </svg>
                    <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                        <span id="ai-score-num" style="font-size:26px;font-weight:800;color:var(--cyan);font-family:var(--font-sans);line-height:1;"></span>
                        <span style="font-size:10px;color:var(--text-faint);font-family:var(--font-mono);">/100</span>
                    </div>
                </div>
                <div id="ai-score-bar" style="display:none;"></div>
            </div>
        </div>

        {{-- SOLID Review --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
            <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:14px;">SOLID Principles</p>
            <div id="ai-solid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;"></div>
        </div>

        {{-- Problems --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
            <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:14px;">Problems Detected</p>
            <div id="ai-problems" style="display:flex;flex-direction:column;gap:10px;"></div>
        </div>

        {{-- Suggestions --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
            <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:14px;">Suggestions</p>
            <div id="ai-suggestions" style="display:flex;flex-direction:column;gap:10px;"></div>
        </div>

        {{-- Laravel Best Practices --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
            <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:14px;">Laravel Best Practices</p>
            <div id="ai-laravel-practices" style="display:flex;flex-direction:column;gap:8px;"></div>
        </div>

        {{-- Best Practices (followed) --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
            <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:12px;">Practices Already Followed</p>
            <ul id="ai-best-practices" style="display:flex;flex-direction:column;gap:6px;"></ul>
        </div>

        {{-- Re-analyze --}}
        <div style="display:flex;align-items:center;gap:12px;padding-top:4px;">
            <button onclick="aiAnalyze()" class="ai-analyze-btn" style="padding:9px 18px;font-size:12px;">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Re-analyze
            </button>
            <span id="ai-provider-badge" style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);"></span>
        </div>

    </div>

</section>

{{-- ══ AI CHAT ══ --}}
<section id="sec-chat" style="{{ $section === 'chat' ? 'display:flex;flex-direction:column;height:100%;padding:24px;' : 'display:none' }}">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div class="sec-header" style="margin-bottom:0;">
            <div class="sec-header__icon" style="background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.18);color:var(--cyan);">
                <svg viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <div>
                <h1 class="sec-header__title">AI Chat</h1>
                <p class="sec-header__sub">Ask anything about your architecture — only relevant context is sent to AI.</p>
            </div>
        </div>
        @if(config('laradar.ai.enabled', false))
        <span style="display:flex;align-items:center;gap:6px;font-family:var(--font-mono);font-size:11px;color:var(--emerald);background:rgba(52,211,153,0.12);border:1px solid rgba(52,211,153,0.25);padding:5px 12px;border-radius:20px;">
            <span style="width:6px;height:6px;border-radius:50%;background:var(--emerald);"></span>
            {{ config('laradar.ai.model') }}
        </span>
        @endif
    </div>

    @if(!config('laradar.ai.enabled', false))
    <div style="max-width:560px;background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.25);border-radius:10px;padding:16px;margin-bottom:20px;">
        <p style="font-size:13px;color:var(--amber);">AI is not enabled. Set <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">ai.enabled = true</code> and <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">GEMINI_API_KEY</code> in your .env.</p>
    </div>
    @endif

    {{-- Suggestion chips --}}
    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px;" id="chat-suggestions">
        <button onclick="chatSuggest('Which controller has the most methods?')" class="chat-suggestion-chip">
            <svg style="width:12px;height:12px;color:var(--amber);flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
            Which controller is largest?
        </button>
        <button onclick="chatSuggest('Trace the main request flow from route through controller to model.')" class="chat-suggestion-chip">
            <svg style="width:12px;height:12px;color:var(--emerald);flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
            Trace request flow
        </button>
        <button onclick="chatSuggest('Are there any SOLID principle violations?')" class="chat-suggestion-chip">
            <svg style="width:12px;height:12px;color:var(--rose);flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            SOLID violations?
        </button>
        <button onclick="chatSuggest('Which models have the most relationships?')" class="chat-suggestion-chip">
            <svg style="width:12px;height:12px;color:var(--violet);flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
            Model relationships
        </button>
        <button onclick="chatSuggest('What services should I extract from my controllers?')" class="chat-suggestion-chip">
            <svg style="width:12px;height:12px;color:#2DD4BF;flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Suggest service extractions
        </button>
        <button onclick="chatSuggest('Explain the overall architecture and data flow.')" class="chat-suggestion-chip">
            <svg style="width:12px;height:12px;color:var(--cyan);flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Overall architecture
        </button>
    </div>

    {{-- Messages --}}
    <div id="chat-messages" style="flex:1;overflow-y:auto;display:flex;flex-direction:column;gap:16px;margin-bottom:16px;max-height:calc(100vh - 420px);min-height:200px;padding-right:4px;">
        <div id="chat-empty" style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:200px;color:var(--text-faint);">
            <div style="width:56px;height:56px;border-radius:16px;background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.18);display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                <svg style="width:26px;height:26px;color:var(--cyan);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <p style="font-size:13px;font-weight:600;color:var(--text-dim);">Ask anything about your architecture</p>
            <p style="font-size:11px;color:var(--text-faint);margin-top:4px;">Use a suggestion above or type your own question</p>
        </div>
    </div>

    {{-- Input --}}
    <div style="border:1px solid var(--border);border-radius:12px;background:var(--bg-elevated);overflow:hidden;transition:border-color .2s,box-shadow .2s;" onfocusin="this.style.borderColor='rgba(99,102,241,.4)';this.style.boxShadow='0 0 0 3px rgba(99,102,241,.08)'" onfocusout="this.style.borderColor='var(--border)';this.style.boxShadow=''">
        <textarea id="chat-input" rows="2"
            placeholder="e.g. Trace the main request flow  •  Which controller is too large?  •  Where should I add a service?"
            {{ !config('laradar.ai.enabled', false) ? 'disabled' : '' }}
            oninput="chatPreviewContext(this.value)"
            onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();chatSend();}"
            style="width:100%;padding:14px 16px 6px;font-size:13px;color:var(--text);background:transparent;resize:none;outline:none;border:none;font-family:var(--font-sans);box-sizing:border-box;line-height:1.6;"></textarea>
        <div style="display:flex;align-items:center;justify-content:space-between;padding:6px 12px 10px;">
            <span id="chat-context-hint" style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:280px;"></span>
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:10px;color:var(--text-faint);font-family:var(--font-mono);">Enter to send</span>
                <button onclick="chatSend()" id="chat-send-btn" class="chat-send-btn" {{ !config('laradar.ai.enabled', false) ? 'disabled' : '' }}>
                    <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </div>
        </div>
    </div>
</section>

{{-- ══ AI DOCS ══ --}}
<section id="sec-aidocs" class="p-6" style="{{ $section === 'aidocs' ? '' : 'display:none' }}">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div class="sec-header" style="margin-bottom:0;">
            <div class="sec-header__icon" style="background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.18);color:var(--cyan);">
                <svg viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div>
                <h1 class="sec-header__title">AI Documentation</h1>
                <p class="sec-header__sub">AI writes full markdown docs for each layer of your architecture. One click per file — or generate all at once.</p>
            </div>
        </div>
        @if(config('laradar.ai.enabled', false))
        <span style="display:flex;align-items:center;gap:6px;font-family:var(--font-mono);font-size:11px;color:var(--emerald);background:rgba(52,211,153,0.12);border:1px solid rgba(52,211,153,0.25);padding:5px 12px;border-radius:20px;">
            <span style="width:6px;height:6px;border-radius:50%;background:var(--emerald);"></span>
            {{ config('laradar.ai.model') }}
        </span>
        @endif
    </div>

    @if(!config('laradar.ai.enabled', false))
    <div style="max-width:560px;background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.25);border-radius:10px;padding:16px;margin-bottom:24px;">
        <p style="font-size:13px;color:var(--amber);">AI is not enabled. Set <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">ai.enabled = true</code> and <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">GEMINI_API_KEY</code> in your .env.</p>
    </div>
    @endif

    <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:24px;">
        <button onclick="docsGenerateAll()"
            {{ !config('laradar.ai.enabled', false) ? 'disabled' : '' }}
            class="ai-analyze-btn"
            style="opacity:{{ config('laradar.ai.enabled', false) ? 1 : 0.4 }};cursor:{{ config('laradar.ai.enabled', false) ? 'pointer' : 'not-allowed' }}">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Generate All Docs
        </button>
        <button onclick="docsDownloadAll()" id="docs-download-all-btn" class="atlas-btn" style="display:none;padding:9px 18px;font-size:13px;border-radius:10px;">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Download All
        </button>

        {{-- AI Graphic Report button --}}
        <button onclick="generateAIGraphicReport()" id="ai-report-btn"
            {{ !config('laradar.ai.enabled', false) ? 'disabled' : '' }}
            class="atlas-btn"
            style="padding:9px 18px;font-size:13px;border-radius:10px;border-color:rgba(167,139,250,0.4);color:var(--violet);background:rgba(167,139,250,0.08);opacity:{{ config('laradar.ai.enabled', false) ? 1 : 0.4 }};cursor:{{ config('laradar.ai.enabled', false) ? 'pointer' : 'not-allowed' }}">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span id="ai-report-btn-label">Generate AI Graphic Report</span>
        </button>
    </div>

    {{-- AI Report progress panel --}}
    <div id="ai-report-progress" style="display:none;max-width:480px;margin-bottom:32px;border-radius:16px;overflow:hidden;border:1px solid rgba(167,139,250,0.3);background:var(--bg-elevated);">
        <div style="display:flex;align-items:center;gap:12px;padding:14px 20px;background:rgba(167,139,250,0.15);border-bottom:1px solid rgba(167,139,250,0.2);">
            <svg style="width:16px;height:16px;animation:spin 1s linear infinite;flex-shrink:0;color:var(--violet);" fill="none" viewBox="0 0 24 24" id="ai-report-spinner">
                <circle style="opacity:.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path style="opacity:.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <p style="font-size:13px;font-weight:600;color:var(--violet);font-family:var(--font-mono);" id="ai-report-progress-title">Generating AI Report…</p>
        </div>
        <div style="padding:16px 20px;display:flex;flex-direction:column;gap:10px;" id="ai-report-steps">
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="analyze">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Analyzing architecture with AI</span>
            </div>
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="architecture">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Generating Architecture documentation</span>
            </div>
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="models">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Generating Models documentation</span>
            </div>
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="controllers">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Generating Controllers documentation</span>
            </div>
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="routes">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Generating Routes documentation</span>
            </div>
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="services">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Generating Services documentation</span>
            </div>
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="modules">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Generating Modules documentation</span>
            </div>
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="build">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Building graphic report</span>
            </div>
        </div>
        <div id="ai-report-error" style="display:none;padding:0 20px 16px;font-size:13px;color:var(--rose);font-family:var(--font-mono);"></div>
    </div>

    {{-- Doc cards --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;max-width:880px;" id="docs-grid">

        @php
        $docTypes = [
            ['architecture', 'Architecture.md', 'Overall design, patterns, score, strengths & improvements.', '#4f46e5', 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
            ['models',       'Models.md',       'Each model — purpose, fields, relationships, business role.', '#8b5cf6', 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
            ['controllers',  'Controllers.md',  'Each controller — responsibilities, methods, observations.',  '#3b82f6', 'M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18'],
            ['routes',       'Routes.md',       'All routes grouped by resource — purpose, auth, middleware.',  '#10b981', 'M13 10V3L4 14h7v7l9-11h-7z'],
            ['services',     'Services.md',     'Service layer, repositories, jobs, and events explained.',    '#f59e0b', 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
            ['modules',      'Modules.md',      'Module breakdown — domain responsibilities and coupling.',    '#06b6d4', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ];
        @endphp

        @foreach($docTypes as [$type, $filename, $desc, $color, $icon])
        <div class="card" id="doc-card-{{ $type }}" style="padding:20px;display:flex;flex-direction:column;gap:12px;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:{{ $color }}18;border:1px solid {{ $color }}40">
                        <svg style="width:16px;height:16px;color:{{ $color }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                    </div>
                    <div>
                        <p style="font-weight:600;color:var(--text);font-size:13px;font-family:var(--font-mono);">{{ $filename }}</p>
                    </div>
                </div>
                <span id="doc-status-{{ $type }}" style="font-size:11px;color:var(--text-faint);flex-shrink:0;font-family:var(--font-mono);">Pending</span>
            </div>
            <p style="font-size:12px;color:var(--text-dim);line-height:1.6;">{{ $desc }}</p>

            {{-- Excerpt preview (shown after generation) --}}
            <div id="doc-excerpt-{{ $type }}" onclick="docsPreview('{{ $type }}')"
                style="display:none;padding:10px 13px;background:var(--bg-sunken);border-radius:9px;border:1px solid var(--border);font-size:11.5px;color:var(--text-dim);line-height:1.6;max-height:68px;overflow:hidden;position:relative;cursor:pointer;transition:border-color .2s;">
                <div id="doc-excerpt-text-{{ $type }}"></div>
                <div style="position:absolute;bottom:0;left:0;right:0;height:24px;background:linear-gradient(transparent,var(--bg-sunken));pointer-events:none;border-radius:0 0 9px 9px;"></div>
            </div>

            <div style="display:flex;gap:6px;margin-top:auto;padding-top:4px;flex-wrap:wrap;">
                <button onclick="docsGenerate('{{ $type }}')"
                    {{ !config('laradar.ai.enabled', false) ? 'disabled' : '' }}
                    id="doc-gen-btn-{{ $type }}"
                    class="atlas-btn atlas-btn--cyan"
                    style="flex:1;justify-content:center;font-size:11px;padding:7px 10px;border-radius:8px;opacity:{{ config('laradar.ai.enabled', false) ? 1 : 0.4 }};cursor:{{ config('laradar.ai.enabled', false) ? 'pointer' : 'not-allowed' }}">
                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    Generate
                </button>
                {{-- Preview button --}}
                <button onclick="docsPreview('{{ $type }}')"
                    id="doc-preview-btn-{{ $type }}"
                    class="atlas-btn"
                    style="display:none;align-items:center;justify-content:center;gap:5px;font-size:11px;padding:7px 10px;border-radius:8px;">
                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Preview
                </button>
                {{-- Download .md --}}
                <button onclick="docsDownload('{{ $type }}')"
                    id="doc-dl-btn-{{ $type }}"
                    class="atlas-btn"
                    style="display:none;align-items:center;justify-content:center;gap:5px;font-size:11px;padding:7px 10px;border-radius:8px;">
                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    .md
                </button>
                {{-- Download .html --}}
                <button onclick="docsDownloadHtml('{{ $type }}')"
                    id="doc-dl-html-btn-{{ $type }}"
                    class="atlas-btn atlas-btn--cyan"
                    style="display:none;align-items:center;justify-content:center;gap:5px;font-size:11px;padding:7px 10px;border-radius:8px;">
                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    .html
                </button>
            </div>
        </div>
        @endforeach

    </div>

</section>


</main>

{{-- ── Doc Preview Modal ──────────────────────────────────────────────────── --}}
<div id="doc-modal" class="doc-modal-ov" style="display:none;" onclick="if(event.target===this)closeDocModal()">
    <div class="doc-modal-box">
        <div class="doc-modal-head">
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="width:8px;height:8px;border-radius:50%;background:var(--cyan);flex:none;"></span>
                <h3 id="doc-modal-title" style="font-family:var(--font-mono);font-size:14px;font-weight:700;color:var(--text);margin:0;"></h3>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <button id="doc-modal-dl-md" class="atlas-btn" style="font-size:11px;padding:5px 12px;border-radius:7px;gap:5px;">
                    <svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    .md
                </button>
                <button id="doc-modal-dl-html" class="atlas-btn atlas-btn--cyan" style="font-size:11px;padding:5px 12px;border-radius:7px;gap:5px;">
                    <svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    .html
                </button>
                <button onclick="closeDocModal()" style="width:30px;height:30px;border-radius:8px;border:1px solid var(--border);background:var(--bg-sunken);color:var(--text-dim);font-size:18px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;flex:none;">&#x2715;</button>
            </div>
        </div>
        <div class="doc-modal-body doc-r" id="doc-modal-body"></div>
    </div>
</div>
@endsection
