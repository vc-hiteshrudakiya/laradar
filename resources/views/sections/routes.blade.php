@php
$rs              = $data['route_summary'] ?? [];
$routeTotal      = $rs['total'] ?? 0;
$routeAuthCount  = count(array_filter($data['routes'], fn($r) => in_array('auth', array_map('strtolower', $r['middleware']??[]))));
$routeApiCount   = $rs['api'] ?? 0;
$routePublic     = $routeTotal - $routeAuthCount;
@endphp

{{-- List view --}}
<div id="routes-list">

    {{-- Stats --}}
    <div class="mds-top-stats">
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $routeTotal }}</span>
            <span class="mds-top-stat-lbl">Total Routes</span>
        </div>
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $routeAuthCount }}</span>
            <span class="mds-top-stat-lbl">Auth Protected</span>
        </div>
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $routeApiCount }}</span>
            <span class="mds-top-stat-lbl">API Endpoints</span>
        </div>
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $routePublic }}</span>
            <span class="mds-top-stat-lbl">Public</span>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="mds-toolbar">
        <div style="position:relative;">
            <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--text-faint);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input id="routes-search" oninput="filterRoutes()" type="search" placeholder="Search URI or handler…"
                style="border:1px solid var(--border);border-radius:8px;padding:8px 12px 8px 32px;font-size:12px;width:220px;font-family:var(--font-mono);background:var(--bg-elevated);color:var(--text);">
        </div>
        <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ $routeTotal }} routes</span>
    </div>

    {{-- List --}}
    <div style="background:var(--bg-elevated);border-radius:12px;border:1px solid var(--border);overflow:hidden;">

        {{-- Column headers --}}
        <div class="mds-list-head" style="grid-template-columns:80px 1fr 130px 160px 24px;">
            <span>Method</span>
            <span>URI / Handler</span>
            <span>Middleware</span>
            <span>Route Name</span>
            <span></span>
        </div>

        {{-- Rows --}}
        <div id="routes-tbody">
            @foreach($data['routes'] as $i => $route)
            @php
                $methods       = array_values(array_filter($route['methods']??[], fn($m)=>$m!=='HEAD'));
                $ctrl          = class_basename($route['controller']['class']??'');
                $action        = $route['controller']['method']??'Closure';
                $isInvokable   = $action === '__invoke';
                $mwsRaw        = implode(',', array_map('strtolower', $route['middleware']??[]));
                $allMws        = $route['middleware'] ?? [];
                $mwCount       = count($allMws);
                $primaryMw     = $mwCount > 0 ? class_basename($allMws[0]) : null;
                $fullMwTitle   = implode(' · ', $allMws);
                $routeName     = $route['name'] ?? '';
                $routeDetailId = ($routeName !== '' && preg_match('/^[a-zA-Z0-9_.\-]+$/', $routeName)) ? $routeName : $i;
                $handlerText   = $ctrl ? ($ctrl . ($isInvokable ? '' : '@'.$action)) : 'Closure';
            @endphp
            <div class="mds-list-row route-row route-row-anim"
                 style="grid-template-columns:80px 1fr 130px 160px 24px;--ri:{{$i}};"
                 data-uri="{{ strtolower($route['uri']??'') }}"
                 data-methods="{{ implode(',',array_map('strtoupper',$methods)) }}"
                 data-mw="{{ $mwsRaw }}"
                 data-handler="{{ strtolower($handlerText) }}"
                 onclick="openDetailAjax('routes','{{ route('laradar.route.detail', $routeDetailId) }}')">

                {{-- Method badges --}}
                <div style="display:flex;flex-wrap:wrap;gap:3px;align-items:flex-start;">
                    @foreach($methods as $m)
                    <span style="font-family:var(--font-mono);font-size:10px;font-weight:700;padding:2px 7px;border-radius:4px;background:rgba(255,45,32,0.08);color:#FF2D20;border:1px solid rgba(255,45,32,0.25);white-space:nowrap;letter-spacing:0.04em;">{{ strtoupper($m) }}</span>
                    @endforeach
                </div>

                {{-- URI + Handler --}}
                <div style="min-width:0;">
                    <div style="font-family:var(--font-mono);font-weight:700;font-size:13px;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="/{{ $route['uri'] }}">/{{ $route['uri'] }}</div>
                    <div style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        @if($ctrl){{ $handlerText }}@else<span style="font-style:italic;">Closure</span>@endif
                    </div>
                </div>

                {{-- Middleware --}}
                <div style="display:flex;align-items:center;gap:4px;" title="{{ $fullMwTitle }}">
                    @if($mwCount === 0)
                        <span style="font-size:12px;color:var(--text-faint);">—</span>
                    @else
                        <span class="ctrl-chip" style="max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $allMws[0] }}">{{ $primaryMw }}</span>
                        @if($mwCount > 1)
                        <span class="ctrl-chip" style="white-space:nowrap;flex:none;" title="{{ $fullMwTitle }}">+{{ $mwCount - 1 }}</span>
                        @endif
                    @endif
                </div>

                {{-- Route Name --}}
                <div style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $routeName }}">
                    {{ $routeName ?: '—' }}
                </div>

                {{-- Chevron --}}
                <svg style="width:14px;height:14px;color:var(--text-faint);flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>

            </div>
            @endforeach
        </div>

    </div>
</div>

<div id="routes-detail" style="display:none"><div id="routes-detail-content"></div></div>
