@extends('laradar::layouts.laradar')
@section('content')
@php
$c = $controllerData;

// Related routes
$relatedRoutes       = [];
$relatedRouteIndexes = [];
foreach (($data['routes'] ?? []) as $ri => $r) {
    $cls = $r['controller']['class'] ?? '';
    if (class_basename($cls) === $c['name'] || $cls === ($c['full_class'] ?? '')) {
        $relatedRouteIndexes[count($relatedRoutes)] = $ri;
        $relatedRoutes[] = $r;
    }
}

$deps        = $c['dependencies'] ?? [];
$mws         = $c['middleware'] ?? [];
$details     = $c['method_details'] ?? [];
$methods     = $c['methods'] ?? [];
$isRes       = !empty($c['is_resource']);
$items       = !empty($details) ? $details : array_map(fn($m) => ['method' => $m, 'params' => []], $methods);
$methodCount = count($items);

// First matching route per method name
$routeByMethod      = [];
$routeIndexByMethod = [];
foreach ($relatedRoutes as $ri => $r) {
    $m = $r['controller']['method'] ?? null;
    if ($m && !isset($routeByMethod[$m])) {
        $routeByMethod[$m]      = $r;
        $routeIndexByMethod[$m] = $relatedRouteIndexes[$ri];
    }
}

// Deduplicated middleware
$allMwsRaw = array_merge($mws, ...array_map(fn($r) => $r['middleware'] ?? [], $relatedRoutes));
$allMws    = array_values(array_unique(array_filter($allMwsRaw)));

// Dependency graph
$depNodes = $data['dependencies']['nodes'] ?? [];
$depEdges = $data['dependencies']['edges'] ?? [];

// Glance values
$isApi            = !empty(array_filter($relatedRoutes, fn($r) => str_starts_with($r['uri'] ?? '', 'api/')))
                    || in_array('api', array_map('strtolower', $allMws));
$isCustomHandler  = !empty($c['is_custom_handler']);
$handlerType      = $c['handler_type'] ?? null;
$ctrlType         = $isCustomHandler && $handlerType
                    ? $handlerType
                    : ($isRes ? 'Resource' : ($isApi ? 'API' : 'Web'));
$isProtected = (bool) array_filter($allMws, fn($m) => str_contains(strtolower($m), 'auth'));

// Primary model
$primaryModel = null;
foreach ($deps as $dep) {
    $bn = class_basename($dep['type'] ?? '');
    foreach ($depEdges as $edge) {
        if ($edge['from'] === $bn) {
            foreach ($depNodes as $node) {
                if ($node['name'] === $edge['to'] && ($node['layer'] ?? '') === 'model') {
                    $primaryModel = $edge['to'];
                    break 3;
                }
            }
        }
    }
}
if (!$primaryModel) {
    $stripped = preg_replace('/Controller$/', '', preg_replace('/^(Admin|Api|Web|Auth)/', '', $c['name']));
    foreach (($data['models'] ?? []) as $model) {
        if (strtolower($model['name']) === strtolower($stripped)) {
            $primaryModel = $model['name'];
            break;
        }
    }
}

$complexity      = ($methodCount >= 10 || count($deps) >= 5) ? 'High' : (($methodCount >= 5 || count($deps) >= 3) ? 'Medium' : 'Low');
$complexityColor = match($complexity) { 'High' => 'var(--rose)', 'Medium' => 'var(--amber)', default => 'var(--emerald)' };

$resourceVerbs = ['index' => 'GET', 'create' => 'GET', 'store' => 'POST', 'show' => 'GET', 'edit' => 'GET', 'update' => 'PUT', 'destroy' => 'DELETE'];

// Method descriptions
$methodDescriptions = [
    'index'   => 'Returns a listing of resources',
    'show'    => 'Display the specified resource',
    'store'   => 'Store a new resource',
    'create'  => 'Show the form for creating a resource',
    'edit'    => 'Show the form for editing a resource',
    'update'  => 'Updates the specified resource',
    'destroy' => 'Remove the specified resource',
];

// Related classes for sidebar
$relatedClasses = [];
foreach ($deps as $dep) {
    $bn     = class_basename($dep['type'] ?? '');
    $isRepo = (bool) preg_match('/repositor/i', $dep['type'] ?? '');
    $relatedClasses[] = ['name' => $bn, 'type' => $isRepo ? 'Repository' : 'Service'];
}
if ($primaryModel && !collect($relatedClasses)->contains('name', $primaryModel)) {
    $relatedClasses[] = ['name' => $primaryModel, 'type' => 'Model'];
}
foreach ($deps as $dep) {
    $bn = class_basename($dep['type'] ?? '');
    foreach ($depEdges as $edge) {
        if ($edge['from'] === $bn) {
            foreach ($depNodes as $node) {
                if ($node['name'] === $edge['to'] && ($node['layer'] ?? '') === 'model') {
                    if (!collect($relatedClasses)->contains('name', $node['name'])) {
                        $relatedClasses[] = ['name' => $node['name'], 'type' => 'Model'];
                    }
                }
            }
        }
    }
}

// File path
$filePath = 'app/' . str_replace('\\', '/', preg_replace('/^App\\\\/', '', $c['full_class'] ?? $c['namespace'] ?? '')) . '.php';

// HTTP verb breakdown from related routes
$verbCounts = [];
foreach ($relatedRoutes as $r) {
    foreach ($r['methods'] ?? [] as $v) {
        if ($v === 'HEAD') continue;
        $verbCounts[$v] = ($verbCounts[$v] ?? 0) + 1;
    }
}

// Has tests
$ctrlBaseName = $c['name'];
$testPaths = [
    base_path('tests/Feature/' . $ctrlBaseName . 'Test.php'),
    base_path('tests/Unit/' . $ctrlBaseName . 'Test.php'),
    base_path('tests/Feature/Http/Controllers/' . $ctrlBaseName . 'Test.php'),
    base_path('tests/Feature/Http/' . $ctrlBaseName . 'Test.php'),
];
$hasTest = collect($testPaths)->contains(fn($p) => file_exists($p));

// Invokable (single-action controller)
$isInvokable = in_array('__invoke', $methods);

// Lines of code
$absPath      = base_path(str_replace('/', DIRECTORY_SEPARATOR, $filePath));
$linesOfCode  = file_exists($absPath) ? count(file($absPath)) : null;

// Flow nodes
$flowNodes = [
    ['type' => 'route',      'label' => 'Route',         'sub' => count($relatedRoutes) . ' registered',  'icon' => 'route'],
    ['type' => 'controller', 'label' => $c['name'],       'sub' => $methodCount . ' methods',              'icon' => 'controller'],
];
if (!empty($deps)) {
    $label = count($deps) === 1 ? class_basename($deps[0]['type'] ?? 'Service') : count($deps) . ' Services';
    $flowNodes[] = ['type' => 'service', 'label' => $label, 'sub' => count($deps) . ' injected', 'icon' => 'service'];
}
$flowNodes[] = ['type' => 'database', 'label' => 'Database', 'sub' => $primaryModel ?? 'Eloquent ORM', 'icon' => 'database'];
@endphp

<div class="detail-page">

{{-- Header (unchanged) --}}
<div class="detail-header reveal">
    <div class="detail-header-top">
        <div class="detail-header-left">
            <div class="detail-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                </svg>
            </div>
            <div style="min-width:0">
                <div class="detail-title-row">
                    <h1 class="detail-title">{{ $c['name'] }}</h1>
                    @if($isRes)<span class="detail-badge">RESOURCE</span>@endif
                    @if($isCustomHandler && $handlerType)<span class="detail-badge">{{ strtoupper($handlerType) }}</span>@endif
                </div>
                <span class="detail-ns">{{ $c['full_class'] ?? ($c['namespace'] ?? '') }}</span>
            </div>
        </div>
        <div class="detail-stats">
            <div class="detail-stat"><span class="detail-stat-val">{{ $methodCount }}</span><span class="detail-stat-lbl">Methods</span></div>
            <div class="detail-stat"><span class="detail-stat-val">{{ count($deps) }}</span><span class="detail-stat-lbl">Deps</span></div>
            <div class="detail-stat"><span class="detail-stat-val">{{ count($allMws) }}</span><span class="detail-stat-lbl">Middleware</span></div>
            <div class="detail-stat"><span class="detail-stat-val">{{ count($relatedRoutes) }}</span><span class="detail-stat-lbl">Routes</span></div>
        </div>
    </div>
</div>

{{-- Glance chips --}}
<div class="detail-glance reveal">
    <div class="detail-chip">
        <div class="detail-chip-ico" style="color:var(--brand)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
        </div>
        <div><div class="detail-chip-lbl">Type</div><div class="detail-chip-val" style="color:var(--brand)">{{ $ctrlType }}</div></div>
    </div>
    <div class="detail-chip">
        <div class="detail-chip-ico" style="color:{{ $isProtected ? 'var(--brand)' : 'var(--text-faint)' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div><div class="detail-chip-lbl">Access</div><div class="detail-chip-val" style="color:{{ $isProtected ? 'var(--brand)' : 'var(--text-faint)' }}">{{ $isProtected ? 'Auth Protected' : 'Public' }}</div></div>
    </div>
    <div class="detail-chip">
        <div class="detail-chip-ico" style="color:{{ $primaryModel ? 'var(--brand)' : 'var(--text-faint)' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14c0 1.657 4.03 3 9 3s9-1.343 9-3V5"/><path d="M3 12c0 1.657 4.03 3 9 3s9-1.343 9-3"/></svg>
        </div>
        <div><div class="detail-chip-lbl">Primary Model</div><div class="detail-chip-val" style="color:{{ $primaryModel ? 'var(--brand)' : 'var(--text-faint)' }}">{{ $primaryModel ?? 'Unknown' }}</div></div>
    </div>
    <div class="detail-chip">
        <div class="detail-chip-ico" style="color:{{ $complexityColor }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
        </div>
        <div><div class="detail-chip-lbl">Complexity</div><div class="detail-chip-val" style="color:{{ $complexityColor }}">{{ $complexity }}</div></div>
    </div>
</div>

{{-- Controller Flow --}}
<div class="cd-flow-wrap reveal">
    <p class="cd-flow-label">Controller Flow</p>
    <div class="cd-flow-nodes">
        @foreach($flowNodes as $fi => $fn)
        <div class="cd-flow-node">
            <div class="cd-flow-circle">
                @if($fn['icon'] === 'route')
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                @elseif($fn['icon'] === 'controller')
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                @elseif($fn['icon'] === 'service')
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                @else
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                @endif
            </div>
            <div>
                <div class="cd-flow-node-label">{{ $fn['label'] }}</div>
                <div class="cd-flow-node-sub">{{ $fn['sub'] }}</div>
            </div>
        </div>
        @if(!$loop->last)
        <div class="cd-flow-arrow"><div class="cd-flow-arrow-line"></div></div>
        @endif
        @endforeach
    </div>
</div>

{{-- Body: main + sidebar --}}
<div class="detail-body">

    {{-- Main --}}
    <div class="detail-main">

        {{-- Controller Details --}}
        <div class="detail-card reveal">
            <div class="detail-card-head">
                <p class="detail-card-title">Controller Details</p>
            </div>
            <div class="detail-card-body">
                <div class="cd-details-grid">
                    <span class="cd-det-label">Class name</span>
                    <span class="cd-det-value">{{ $c['name'] }}</span>

                    <span class="cd-det-label">Namespace</span>
                    <span class="cd-det-value cd-det-mono">{{ $c['namespace'] ?? ($c['full_class'] ?? '—') }}</span>

                    <span class="cd-det-label">File path</span>
                    <span class="cd-det-value cd-det-mono" style="font-size:10.5px;opacity:.8;">{{ $filePath }}</span>

                    @if(!empty($allMws))
                    <span class="cd-det-label">Middleware</span>
                    <span class="cd-det-value">
                        @foreach($allMws as $mw)
                            <span class="cd-mw-badge">{{ class_basename($mw) }}</span>
                        @endforeach
                    </span>
                    @endif

                    @if(!empty($deps))
                    <span class="cd-det-label">Dependencies</span>
                    <span class="cd-det-value">
                        @foreach($deps as $dep)
                            @php $bn = class_basename($dep['type'] ?? ''); $isRepo = (bool) preg_match('/repositor/i', $dep['type'] ?? ''); @endphp
                            <span class="cd-dep-pill"><span class="cd-dep-pill-ico">{{ $isRepo ? 'R' : 'S' }}</span>{{ $bn }}</span>
                        @endforeach
                    </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Routes & Methods --}}
        @if(!empty($relatedRoutes))
        <div class="detail-card reveal">
            <div class="detail-card-head">
                <p class="detail-card-title">Routes & Methods</p>
                <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);">{{ count($relatedRoutes) }} routes</span>
            </div>
            @foreach($relatedRoutes as $ri => $rr)
            @php
                $rVerb = collect($rr['methods'] ?? [])->filter(fn($v) => $v !== 'HEAD')->first();
                $rMethod = $rr['controller']['method'] ?? '';
                $rIdx = $relatedRouteIndexes[$ri];
                $rName = $rr['name'] ?? '';
                $rId = ($rName !== '' && preg_match('/^[a-zA-Z0-9_.\-]+$/', $rName)) ? $rName : $rIdx;
            @endphp
            <div class="cd-route-item">
                @if($rVerb)<span class="cd-verb cd-verb--{{ strtolower($rVerb) }}">{{ $rVerb }}</span>@endif
                <span class="cd-route-uri">/{{ $rr['uri'] ?? '' }}</span>
                @if($rMethod)<span class="cd-route-method">{{ $rMethod }}</span>@endif
                <a href="{{ route('laradar.route.detail', $rId) }}" class="cd-route-link"
                   onclick="event.preventDefault(); openDetailAjax('routes', this.href)">→</a>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Methods Table --}}
        <div class="detail-card reveal">
            <div class="detail-card-head">
                <p class="detail-card-title">Methods ({{ $methodCount }})</p>
            </div>
            @if(empty($items))
                <div class="cd-empty">No public methods detected.</div>
            @else
            <div style="overflow-x:auto">
                <table class="cd-table">
                    <thead>
                        <tr>
                            <th class="cd-th">Method</th>
                            <th class="cd-th">HTTP</th>
                            <th class="cd-th">URI</th>
                            <th class="cd-th" style="min-width:160px">Description</th>
                            <th class="cd-th">Params</th>
                            <th class="cd-th"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $d)
                        @php
                            $rr     = $routeByMethod[$d['method']] ?? null;
                            $rIdx   = $routeIndexByMethod[$d['method']] ?? -1;
                            $verb   = null;
                            if ($rr) {
                                $verb = collect($rr['methods'] ?? [])->filter(fn($v) => $v !== 'HEAD')->first();
                            } elseif (isset($resourceVerbs[$d['method']])) {
                                $verb = $resourceVerbs[$d['method']];
                            }
                            $params = $d['params'] ?? [];
                            $desc   = $methodDescriptions[$d['method']] ?? null;
                            if (!$desc && !empty($params)) {
                                $desc = 'Accepts ' . collect($params)->map(fn($p) => '$' . $p['var'])->implode(', ');
                            }
                        @endphp
                        <tr class="cd-tr">
                            <td class="cd-td"><span class="cd-mname">{{ $d['method'] }}</span></td>
                            <td class="cd-td">
                                @if($verb)
                                    <span class="cd-verb cd-verb--{{ strtolower($verb) }}">{{ $verb }}</span>
                                @else
                                    <span class="cd-tdash">—</span>
                                @endif
                            </td>
                            <td class="cd-td">
                                @if($rr)
                                    <span class="cd-turi">/{{ $rr['uri'] ?? '' }}</span>
                                @else
                                    <span style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);font-style:italic">no route</span>
                                @endif
                            </td>
                            <td class="cd-td"><span class="cd-tdesc">{{ $desc ?? '—' }}</span></td>
                            <td class="cd-td">
                                @if(!empty($d['form_request']))
                                <span style="font-family:var(--font-mono);font-size:9px;font-weight:700;padding:2px 7px;border-radius:4px;background:rgba(255,45,32,.08);color:#ff2d20;border:1px solid rgba(255,45,32,.2);display:inline-block;margin-bottom:3px;" title="FormRequest">{{ $d['form_request'] }}</span>
                                @endif
                                @if(!empty($params))
                                    @foreach($params as $p)
                                        @if(($p['type'] ?? '') !== ($d['form_request'] ?? ''))
                                        <span class="cd-tparam">@if(!empty($p['type']))<span class="cd-tparam-t">{{ $p['type'] }}</span> @endif${{ $p['var'] }}</span>
                                        @endif
                                    @endforeach
                                @endif
                                @if(empty($d['form_request']) && empty($params))
                                    <span class="cd-tdash">—</span>
                                @endif
                            </td>
                            <td class="cd-td" style="text-align:center;width:42px">
                                @if($rIdx >= 0)
                                    @php
                                        $routeName = $rr['name'] ?? '';
                                        $routeId   = ($routeName !== '' && preg_match('/^[a-zA-Z0-9_.\-]+$/', $routeName)) ? $routeName : $rIdx;
                                    @endphp
                                    <a href="{{ route('laradar.route.detail', $routeId) }}" class="cd-act-btn"
                                       onclick="event.preventDefault(); openDetailAjax('routes', this.href)">→</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- Events & Jobs Dispatched --}}
        @php $methodsWithDispatches = array_values(array_filter($items, fn($d) => !empty($d['events_dispatched']) || !empty($d['jobs_dispatched']))); @endphp
        @if(!empty($methodsWithDispatches))
        <div class="detail-card reveal">
            <div class="detail-card-head">
                <p class="detail-card-title">Events &amp; Jobs Dispatched</p>
                <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);">{{ count($methodsWithDispatches) }} method{{ count($methodsWithDispatches) !== 1 ? 's' : '' }}</span>
            </div>
            @foreach($methodsWithDispatches as $d)
            <div style="padding:12px 16px;{{ !$loop->last ? 'border-bottom:1px solid var(--border);' : '' }}">
                <div style="font-family:var(--font-mono);font-size:12.5px;font-weight:700;color:var(--text);margin-bottom:8px;">{{ $d['method'] }}()</div>
                @if(!empty($d['events_dispatched']))
                <div style="{{ !empty($d['jobs_dispatched']) ? 'margin-bottom:8px;' : '' }}">
                    <div style="font-size:10px;font-family:var(--font-mono);font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-faint);margin-bottom:5px;">Events</div>
                    <div style="display:flex;flex-wrap:wrap;gap:4px;">
                        @foreach($d['events_dispatched'] as $ev)
                        <span class="detail-pill">{{ $ev }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                @if(!empty($d['jobs_dispatched']))
                <div>
                    <div style="font-size:10px;font-family:var(--font-mono);font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-faint);margin-bottom:5px;">Jobs</div>
                    <div style="display:flex;flex-wrap:wrap;gap:4px;">
                        @foreach($d['jobs_dispatched'] as $job)
                        <span class="detail-pill">{{ $job }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif

    </div>{{-- .detail-main --}}

    {{-- Sidebar --}}
    <div class="detail-sidebar">

        {{-- Related Information --}}
        <div class="detail-scard reveal">
            <div class="detail-scard-head"><p class="detail-scard-title">Related Information</p></div>
            <div class="detail-scard-body" style="padding:0 14px">

                {{-- HTTP Verbs --}}
                @if(!empty($verbCounts))
                <div class="detail-ir">
                    <span class="detail-il">HTTP Verbs</span>
                    <div style="display:flex;flex-wrap:wrap;gap:4px;margin-top:4px;">
                        @foreach($verbCounts as $verb => $count)
                            <span class="cd-verb cd-verb--{{ strtolower($verb) }}">{{ $verb }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Has Tests --}}
                <div class="detail-ir">
                    <span class="detail-il">Tests</span>
                    <span class="detail-iv" style="font-weight:700;color:{{ $hasTest ? 'var(--emerald)' : 'var(--rose)' }}">
                        {{ $hasTest ? '✓ Found' : '✗ Not found' }}
                    </span>
                </div>

                {{-- Invokable --}}
                @if($isInvokable)
                <div class="detail-ir">
                    <span class="detail-il">Pattern</span>
                    <span class="detail-iv" style="font-weight:700;color:var(--brand)">Single Action</span>
                </div>
                @endif

                {{-- Lines of code --}}
                @if($linesOfCode)
                <div class="detail-ir">
                    <span class="detail-il">Lines of Code</span>
                    <span class="detail-iv" style="font-weight:700;color:{{ $linesOfCode > 300 ? 'var(--rose)' : ($linesOfCode > 150 ? 'var(--amber)' : 'var(--emerald)') }}">
                        {{ $linesOfCode }} lines
                        @if($linesOfCode > 300) <span style="font-size:10px;font-weight:400;color:var(--rose)">(consider splitting)</span>
                        @elseif($linesOfCode > 150) <span style="font-size:10px;font-weight:400;color:var(--amber)">(getting large)</span>
                        @endif
                    </span>
                </div>
                @endif

            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="detail-scard reveal">
            <div class="detail-scard-head"><p class="detail-scard-title">Quick Actions</p></div>
            <div class="detail-scard-body" style="padding:0 16px">
                <a href="{{ route('laradar.routes') }}" class="detail-act"
                   onclick="event.preventDefault(); showList('routes', true)">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    View all routes
                </a>
                <a href="{{ route('laradar.controllers') }}" class="detail-act"
                   onclick="event.preventDefault(); showList('controllers', true)">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                    All controllers
                </a>
                @if($primaryModel)
                @php $mi = collect($data['models'] ?? [])->search(fn($m) => $m['name'] === $primaryModel); @endphp
                @if($mi !== false)
                <a href="{{ route('laradar.model.detail', $primaryModel) }}" class="detail-act"
                   onclick="event.preventDefault(); openDetailAjax('models', this.href)">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                    View {{ $primaryModel }} model
                </a>
                @endif
                @endif
            </div>
        </div>

        {{-- Related Classes --}}
        @if(!empty($relatedClasses))
        <div class="detail-scard reveal">
            <div class="detail-scard-head"><p class="detail-scard-title">Related Classes</p></div>
            <div class="detail-scard-body" style="padding:0 16px">
                @foreach($relatedClasses as $rc)
                <div class="cd-rel-item">
                    <span class="cd-rel-name">{{ $rc['name'] }}</span>
                    <span class="cd-rel-type">{{ $rc['type'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>{{-- .detail-sidebar --}}

</div>{{-- .detail-body --}}

</div>{{-- .detail-page --}}
@endsection
