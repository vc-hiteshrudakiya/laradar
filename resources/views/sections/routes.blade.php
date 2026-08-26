@php $rs = $data['route_summary'] ?? []; @endphp

    @php
    $routeMethodCounts = [];
    foreach ($data['routes'] as $r) {
        foreach (array_filter($r['methods']??[], fn($m)=>$m!=='HEAD') as $m) {
            $routeMethodCounts[strtoupper($m)] = ($routeMethodCounts[strtoupper($m)] ?? 0) + 1;
        }
    }
    $routeMethodStyle = [
        'GET'    => 'text-white',
        'POST'   => 'text-white',
        'PUT'    => 'text-white',
        'PATCH'  => 'text-white',
        'DELETE' => 'text-white',
    ];
    $routeMethodColors = [
        'GET'    => ['hex'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.28)'],
        'POST'   => ['hex'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.28)'],
        'PUT'    => ['hex'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.28)'],
        'PATCH'  => ['hex'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.28)'],
        'DELETE' => ['hex'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.28)'],
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


