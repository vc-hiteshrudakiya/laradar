@php
$score   = $data['score']         ?? [];
$summary = $data['summary']       ?? [];
$rs      = $data['route_summary'] ?? [];
@endphp

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
        'Packages'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11l0 .01"/>',
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
        'Packages'     => ['color'=>'var(--brand)', 'bg'=>'var(--brand-bg)'],
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
        ['Packages',     $summary['packages']??count($data['packages']??[])],
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
        'Packages'     => 'packages',
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
    <div class="ov-panel ov-reveal" data-ov-reveal style="margin-bottom:24px;" id="ovArchPanel">
        <div class="ov-panel-head">
            <div>
                <h3>Architecture Explorer</h3>
                <p>Request flow &mdash; from HTTP kernel to your database tables</p>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <span id="ovArchDetail" style="font-size:11.5px;color:var(--text-faint);">Hover a node to trace it</span>
                <button class="ov-btn-icon" id="ovZoomIn" title="Zoom in">+</button>
                <button class="ov-btn-icon" id="ovZoomOut" title="Zoom out">&minus;</button>
                <button class="ov-btn-icon" id="ovFullscreen" title="Full screen">
                    <svg id="ovFsIconExpand" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3H5a2 2 0 00-2 2v3m18 0V5a2 2 0 00-2-2h-3m0 18h3a2 2 0 002-2v-3M3 16v3a2 2 0 002 2h3"/></svg>
                    <svg id="ovFsIconCompress" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M8 3v3a2 2 0 01-2 2H3m18 0h-3a2 2 0 01-2-2V3m0 18v-3a2 2 0 012-2h3M3 16h3a2 2 0 012 2v3"/></svg>
                </button>
            </div>
        </div>
        <div class="ov-panel-body" style="padding:18px 20px;" id="ovArchPanelBody">
            <div class="ov-diag-shell" id="ovDiagShell">
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
                        <span style="font-family:var(--font-mono);color:#FF2D20;">{{ $perf['execution_time_ms']??0 }} ms</span>
                    </div>
                    <div class="atlas-score-bar"><div class="atlas-score-fill" data-score-w="{{ min(100,($perf['execution_time_ms']??0)/50) }}" style="width:0;background:linear-gradient(90deg,#FF2D20,#FF5349);"></div></div>
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


