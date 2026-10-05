@extends('laradar::layouts.laradar')
@section('content')
@php
$r       = $routeData;
$methods = collect($r['methods'] ?? [])->filter(fn($m) => $m !== 'HEAD')->values();
$ctrl    = class_basename($r['controller']['class'] ?? '');
$action  = $r['controller']['method'] ?? null;
$mws     = $r['middleware'] ?? [];
$name    = $r['name'] ?? '';
$uri     = $r['uri'] ?? '';

$isAuth    = collect($mws)->map('strtolower')->contains(fn($m) => str_contains($m, 'auth'));
$isApi     = str_starts_with($uri, 'api/') || collect($mws)->map('strtolower')->contains('api');
$handler   = $ctrl ? ($action === '__invoke' ? $ctrl : $ctrl.'@'.$action) : 'Closure';
$mwCount   = count($mws);
$prefix    = $r['prefix'] ?? null;
$domain    = $r['domain'] ?? null;
$rateLimit = $r['rate_limit'] ?? null;

$ctrlData = collect($data['controllers'] ?? [])->first(fn($c) =>
    class_basename($c['full_class'] ?? $c['name']) === $ctrl
);

$ctrlFullClass = $r['controller']['class'] ?? ($ctrlData['full_class'] ?? '');
$ctrlFilePath  = str_replace('\\', '/', ltrim($ctrlFullClass, '\\')) . '.php';
$ctrlDeps      = $ctrlData['dependencies'] ?? [];
$hasSvc        = !empty($ctrlDeps);

// Method signature
$methodDetail = $ctrlData
    ? collect($ctrlData['method_details'] ?? [])->first(fn($d) => $d['method'] === ($action ?? '__invoke'))
    : null;
$sigParams = $methodDetail
    ? collect($methodDetail['params'] ?? [])->map(fn($p) => ($p['type'] ? $p['type'].' ' : '').'$'.$p['var'])->implode(', ')
    : '';

$formRequest = null;
if ($methodDetail) {
    foreach ($methodDetail['params'] ?? [] as $p) {
        if (!empty($p['type']) && str_ends_with($p['type'], 'Request')) {
            $formRequest = class_basename($p['type']);
            break;
        }
    }
}

// Side effects from controller method
$routeEvents      = $methodDetail ? ($methodDetail['events_dispatched'] ?? []) : [];
$routeJobs        = $methodDetail ? ($methodDetail['jobs_dispatched'] ?? []) : [];
$hasSideEffects   = !empty($routeEvents) || !empty($routeJobs);

// Infer model — strategy 1: route model binding from URI params
$relatedModel = null;
preg_match_all('/\{([^}?]+)\??}/', $uri, $uriBindings);
foreach ($uriBindings[1] ?? [] as $binding) {
    $candidate = collect($data['models'] ?? [])->first(
        fn($m) => strtolower($m['name']) === strtolower($binding)
    );
    if ($candidate) { $relatedModel = $candidate; break; }
}

// Strategy 2: fallback — strip "Controller" suffix and name-match
if (!$relatedModel && $ctrl) {
    $modelBaseName = preg_replace('/Controller$/', '', preg_replace('/^(Admin|Api|Web)/', '', $ctrl));
    $relatedModel  = collect($data['models'] ?? [])->first(
        fn($m) => strtolower($m['name']) === strtolower($modelBaseName)
    );
}

preg_match_all('/\{[^}]+\}/', $uri, $_uriParams);
$uriParamCount = count($_uriParams[0]);

$uriParamDetails = [];
preg_match_all('/\{([^}?]+)(\?)?\}/', $uri, $_pd);
foreach ($_pd[1] as $_pi => $_pname) {
    $_lower = strtolower($_pname);
    $_bound = collect($data['models'] ?? [])->first(fn($m) => strtolower($m['name']) === $_lower);
    if ($_bound) {
        $_type = 'int'; $_binding = 'Route Model Binding';
        $_pass = 'Primary key value'; $_example = '1'; $_resolveBy = 'id (primary key)';
    } elseif ($_lower === 'id' || preg_match('/^[a-z_]+_id$/', $_lower)) {
        $_type = 'int';    $_binding = null; $_pass = 'A numeric integer';         $_example = '42';            $_resolveBy = null;
    } elseif (str_contains($_lower, 'uuid')) {
        $_type = 'string'; $_binding = 'UUID format'; $_pass = 'A UUID string';   $_example = '550e8400-...';  $_resolveBy = null;
    } elseif (str_contains($_lower, 'slug')) {
        $_type = 'string'; $_binding = 'URL slug'; $_pass = 'A URL-friendly slug'; $_example = 'my-post-title'; $_resolveBy = null;
    } elseif (str_contains($_lower,'token') || str_contains($_lower,'hash') || str_contains($_lower,'key')) {
        $_type = 'string'; $_binding = 'Token / Hash'; $_pass = 'A secure token string'; $_example = 'abc123xyz'; $_resolveBy = null;
    } elseif (str_contains($_lower,'date') || str_contains($_lower,'year') || str_contains($_lower,'month')) {
        $_type = 'string'; $_binding = 'Date value'; $_pass = 'A date string'; $_example = '2024-01-15'; $_resolveBy = null;
    } else {
        $_type = 'string'; $_binding = null; $_pass = 'Any URL-safe string value'; $_example = $_pname; $_resolveBy = null;
    }
    $uriParamDetails[] = [
        'name'       => $_pname,
        'optional'   => isset($_pd[2][$_pi]) && $_pd[2][$_pi] === '?',
        'boundModel' => $_bound,
        'type'       => $_type,
        'binding'    => $_binding,
        'pass'       => $_pass,
        'example'    => $_example,
        'resolveBy'  => $_resolveBy,
    ];
}

// Request body — for POST / PUT / PATCH
$isBodyMethod = $methods->contains(fn($m) => in_array(strtoupper($m), ['POST','PUT','PATCH']));
$requestBodyModel = null;
if ($isBodyMethod) {
    foreach ($uriParamDetails as $_p) {
        if (!empty($_p['boundModel'])) { $requestBodyModel = $_p['boundModel']; break; }
    }
    if (!$requestBodyModel) $requestBodyModel = $relatedModel ?? null;
}
$isPatch  = $methods->contains(fn($m) => strtoupper($m) === 'PATCH');
$isPost   = $methods->contains(fn($m) => strtoupper($m) === 'POST');
$bodyNote = $isPatch
    ? 'Partial update — only send the fields you want to change'
    : ($isPost ? 'Create — send the required fields' : 'Full update — send all fillable fields');

@endphp

<div class="detail-page" data-route-idx="{{ $index }}">

{{-- Header (unchanged) --}}
<div class="detail-header reveal">
    <div class="detail-header-left">
        <div class="detail-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="6" cy="19" r="3"/><path d="M9 19h8.5a3.5 3.5 0 0 0 0-7h-11a3.5 3.5 0 0 1 0-7H15"/><circle cx="18" cy="5" r="3"/>
            </svg>
        </div>
        <div style="min-width:0;flex:1">
            <div style="display:flex;align-items:center;flex-wrap:wrap;gap:6px;margin-bottom:2px">
                <h1 class="detail-title">/{{ $uri }}</h1>
            </div>
            <span class="detail-ns">{{ $handler }}</span>
        </div>
    </div>
    <div class="detail-stats">
        <div class="detail-stat">
            <span class="detail-stat-val">{{ $methods->count() }}</span>
            <span class="detail-stat-lbl">Methods</span>
        </div>
        <div class="detail-stat">
            <span class="detail-stat-val">{{ $mwCount }}</span>
            <span class="detail-stat-lbl">Middleware</span>
        </div>
        <div class="detail-stat">
            <span class="detail-stat-val">{{ $uriParamCount }}</span>
            <span class="detail-stat-lbl">URI Params</span>
        </div>
    </div>
</div>

{{-- Flat info bar --}}
<div class="dp-infobar reveal">
    <div class="dp-infobar-item">
        <span class="dp-infobar-lbl">Access</span>
        <span class="dp-infobar-val" style="color:{{ $isAuth ? 'var(--brand)' : 'var(--text-faint)' }}">{{ $isAuth ? 'Auth protected' : 'Public' }}</span>
    </div>
    <div class="dp-infobar-sep"></div>
    <div class="dp-infobar-item">
        <span class="dp-infobar-lbl">Handler</span>
        <span class="dp-infobar-val" style="color:var(--brand)">{{ $handler }}</span>
    </div>
    <div class="dp-infobar-sep"></div>
    <div class="dp-infobar-item">
        <span class="dp-infobar-lbl">Route Name</span>
        <span class="dp-infobar-val" style="color:{{ $name ? 'var(--brand)' : 'var(--text-faint)' }}">{{ $name ?: '—' }}</span>
    </div>
    <div class="dp-infobar-sep"></div>
    <div class="dp-infobar-item">
        <span class="dp-infobar-lbl">Type</span>
        <span class="dp-infobar-val" style="color:var(--brand)">{{ $isApi ? 'API' : 'Web' }}</span>
    </div>
</div>

{{-- Body --}}
<div class="detail-body">

    {{-- Main column --}}
    <div class="detail-main">

        {{-- Graph (canvas only — node info lives in sidebar #rf-panel) --}}
        <div class="rd-graph-container reveal">
            <div class="rd-graph-canvas-wrap" id="rf-canvas-wrap"></div>
        </div>

        {{-- Route Details --}}
        <div class="detail-card reveal">
            <div class="detail-card-head">
                <p class="detail-card-title">Route Details</p>
            </div>
            <div class="dp-details-grid">
                <span class="dp-det-lbl">HTTP method</span>
                <span class="dp-det-val">
                    @foreach($methods as $m)
                        <span class="dp-verb dp-verb--{{ strtolower($m) }}">{{ $m }}</span>
                    @endforeach
                </span>

                <span class="dp-det-lbl">Action</span>
                <span class="dp-det-val dp-det-mono">{{ $handler }}</span>

                @if($formRequest)
                <span class="dp-det-lbl">Form Request</span>
                <span class="dp-det-val dp-det-mono">{{ $formRequest }}</span>
                @endif

                @if($prefix)
                <span class="dp-det-lbl">Prefix</span>
                <span class="dp-det-val dp-det-mono">/{{ $prefix }}</span>
                @endif

                @if($domain)
                <span class="dp-det-lbl">Domain</span>
                <span class="dp-det-val dp-det-mono">{{ $domain }}</span>
                @endif

                @if($rateLimit)
                <span class="dp-det-lbl">Rate Limit</span>
                <span class="dp-det-val dp-det-mono">{{ $rateLimit }} req/min</span>
                @endif

                <span class="dp-det-lbl">Response</span>
                <span class="dp-det-val dp-det-mono">{{ $isApi ? 'JSON' : 'HTML / View' }}</span>
            </div>
        </div>

        {{-- Middleware Stack --}}
        @if(!empty($mws))
        <div class="detail-card reveal">
            <div class="detail-card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <p class="detail-card-title" style="margin:0;">Middleware Stack</p>
                <span style="font-size:10px;font-family:var(--font-mono);color:var(--text-faint);font-weight:600;letter-spacing:0.05em;">{{ $mwCount }} applied</span>
            </div>
            <div style="padding:14px 18px;display:flex;flex-wrap:wrap;gap:6px;">
                @foreach($mws as $mw)
                <span class="dp-mw-pill">{{ class_basename($mw) }}</span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Side Effects --}}
        @if($hasSideEffects)
        <div class="detail-card reveal">
            <div class="detail-card-head">
                <p class="detail-card-title">Side Effects</p>
                <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);">fired when route is called</span>
            </div>
            @if(!empty($routeEvents))
            <div style="padding:10px 16px 4px;">
                <div style="font-size:10px;font-family:var(--font-mono);font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-faint);margin-bottom:6px;">Events dispatched</div>
                <div style="display:flex;flex-wrap:wrap;gap:5px;">
                    @foreach($routeEvents as $ev)
                    @php $evData = collect($data['events'] ?? [])->first(fn($e) => strtolower($e['name']) === strtolower($ev)); @endphp
                    @if($evData)
                    <a href="{{ route('laradar.event.detail', $ev) }}" style="font-family:var(--font-mono);font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:rgba(255,45,32,.08);color:#ff2d20;border:1px solid rgba(255,45,32,.2);text-decoration:none;" onmouseenter="this.style.opacity='.7'" onmouseleave="this.style.opacity='1'" onclick="event.preventDefault(); openDetailAjax('events', this.href)">{{ $ev }}</a>
                    @else
                    <span class="detail-pill">{{ $ev }}</span>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif
            @if(!empty($routeJobs))
            <div style="padding:10px 16px 12px;">
                <div style="font-size:10px;font-family:var(--font-mono);font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-faint);margin-bottom:6px;">Jobs dispatched</div>
                <div style="display:flex;flex-wrap:wrap;gap:5px;">
                    @foreach($routeJobs as $job)
                    @php $jobData = collect($data['jobs'] ?? [])->first(fn($j) => strtolower($j['name']) === strtolower($job)); @endphp
                    @if($jobData)
                    <a href="{{ route('laradar.job.detail', $job) }}" style="font-family:var(--font-mono);font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:rgba(255,45,32,.08);color:#ff2d20;border:1px solid rgba(255,45,32,.2);text-decoration:none;" onmouseenter="this.style.opacity='.7'" onmouseleave="this.style.opacity='1'" onclick="event.preventDefault(); openDetailAjax('jobs', this.href)">{{ $job }}</a>
                    @else
                    <span class="detail-pill">{{ $job }}</span>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif

    </div>{{-- .detail-main --}}

    {{-- Sidebar --}}
    <div class="detail-sidebar">

        {{-- Node info — JS writes selected node into #rf-panel --}}
        <div class="detail-scard reveal">
            <div class="detail-scard-head"><p class="detail-scard-title">Node Info</p></div>
            <div id="rf-panel" style="padding:14px;min-height:60px;"></div>
        </div>

        {{-- Quick actions --}}
        <div class="detail-scard reveal">
            <div class="detail-scard-head"><p class="detail-scard-title">Quick actions</p></div>
            <div class="detail-scard-body">
                @if($ctrl && $ctrl !== 'Closure' && $ctrlData)
                <a href="{{ route('laradar.controller.detail', $ctrl) }}" class="detail-act"
                   onclick="event.preventDefault(); openDetailAjax('controllers', this.href)">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                    View {{ $ctrl }}
                </a>
                @endif
                <a href="{{ route('laradar.routes') }}" class="detail-act"
                   onclick="event.preventDefault(); showList('routes', true)">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    View all routes
                </a>
                @if($relatedModel)
                @php $mi = collect($data['models'] ?? [])->search(fn($m) => $m['name'] === $relatedModel['name']); @endphp
                @if($mi !== false)
                <a href="{{ route('laradar.model.detail', $relatedModel['name']) }}" class="detail-act"
                   onclick="event.preventDefault(); openDetailAjax('models', this.href)">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                    View {{ $relatedModel['name'] }} model
                </a>
                @endif
                @endif
            </div>
        </div>

        {{-- Related resource --}}
        @if($ctrl || $relatedModel)
        <div class="detail-scard reveal">
            <div class="detail-scard-head"><p class="detail-scard-title">Related resource</p></div>
            <div class="detail-scard-body">
                @if($ctrl)
                <div class="dp-res-item">
                    <span class="dp-res-name">{{ $ctrl }}</span>
                    <span class="dp-res-path">{{ $ctrlFilePath }}</span>
                </div>
                @endif
                @if($relatedModel)
                <div class="dp-res-item">
                    <span class="dp-res-name">{{ $relatedModel['name'] }}</span>
                    <span class="dp-res-path">app/Models/{{ $relatedModel['name'] }}.php</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- URI Parameters --}}
        @if(count($uriParamDetails) > 0)
        <div class="detail-scard reveal">
            <div class="detail-scard-head"><p class="detail-scard-title">URI Parameters <span style="font-size:10px;color:var(--text-faint);font-weight:400;">({{ count($uriParamDetails) }})</span></p></div>
            <div class="detail-scard-body">
                @foreach($uriParamDetails as $param)
                <div style="padding:10px 0;{{ !$loop->last ? 'border-bottom:1px solid var(--border);' : '' }}">
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:6px;margin-bottom:10px;">
                        <code style="font-family:var(--font-mono);font-size:12px;font-weight:700;color:var(--text);background:var(--bg-sunken);padding:2px 7px;border-radius:5px;border:1px solid var(--border);">{{ '{' . $param['name'] . '}' }}</code>
                        @if($param['optional'])
                            <span style="font-size:10px;font-weight:600;padding:2px 7px;border-radius:20px;background:rgba(100,116,139,.12);color:var(--text-faint);border:1px solid rgba(100,116,139,.28);">Optional</span>
                        @else
                            <span style="font-size:10px;font-weight:600;padding:2px 7px;border-radius:20px;background:rgba(255,45,32,.1);color:var(--brand);border:1px solid rgba(255,45,32,.25);">Required</span>
                        @endif
                    </div>
                    <span style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--text-faint);display:block;margin-bottom:3px;">Type</span>
                    <p style="margin:0 0 8px;font-size:13px;font-weight:400;color:var(--text);font-family:var(--font-mono);">{{ $param['type'] }}</p>
                    @if($param['binding'])
                    <span style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--text-faint);display:block;margin-bottom:3px;">Binding</span>
                    <p style="margin:0 0 8px;font-size:13px;font-weight:400;color:var(--text);">{{ $param['binding'] }}</p>
                    @endif
                    @if($param['boundModel'])
                    <span style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--text-faint);display:block;margin-bottom:3px;">Model</span>
                    <p style="margin:0 0 4px;font-size:13px;font-weight:400;color:var(--text);">{{ $param['boundModel']['name'] }}</p>
                    @php $mi = collect($data['models'] ?? [])->search(fn($m) => $m['name'] === $param['boundModel']['name']); @endphp
                    @if($mi !== false)
                    <a href="{{ route('laradar.model.detail', $param['boundModel']['name']) }}" class="rf-panel-link">View Model <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg></a>
                    @endif
                    @else
                    <span style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--text-faint);display:block;margin-bottom:3px;">Example</span>
                    <p style="margin:0;font-size:13px;font-weight:400;color:var(--text);font-family:var(--font-mono);">{{ $param['example'] }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Request Body --}}
        @if($isBodyMethod && $requestBodyModel && !empty($requestBodyModel['fillable']))
        <div class="detail-scard reveal">
            <div class="detail-scard-head"><p class="detail-scard-title">Request Body</p></div>
            <div class="detail-scard-body">
                <p style="font-size:13px;font-weight:400;color:var(--text-faint);margin:0 0 4px;line-height:1.5;">{{ $bodyNote }}</p>
                <p style="font-size:13px;font-weight:400;color:var(--text-faint);margin:0 0 10px;">Model: <span style="color:var(--brand);font-weight:400;">{{ $requestBodyModel['name'] }}</span></p>
                @foreach($requestBodyModel['fillable'] as $field)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;{{ !$loop->last ? 'border-bottom:1px solid var(--border);' : '' }}">
                    <code style="font-family:var(--font-mono);font-size:12px;color:var(--text);font-weight:600;">{{ $field }}</code>
                    <span style="font-size:10px;padding:1px 6px;border-radius:10px;background:var(--bg-sunken);color:var(--text-faint);border:1px solid var(--border);">fillable</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>{{-- .detail-sidebar --}}

</div>{{-- .detail-body --}}

</div>{{-- .detail-page --}}
@endsection
