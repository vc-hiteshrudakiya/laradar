<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $data['project']['name'] }} — Laradar</title>
<link rel="icon" type="image/x-icon" href="{{ route('laradar.asset', ['filename' => 'favicon.ico']) }}">
<script>if(localStorage.getItem('laradar_sidebar')==='0'){document.documentElement.classList.add('sidebar-pre-collapsed');}</script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="{{ route('laradar.asset', ['filename' => 'laradar.css']) }}">
</head>
<body class="atlas-layout">

@php
$score   = $data['score'] ?? [];
$summary = $data['summary'] ?? [];
$rs      = $data['route_summary'] ?? [];
$grade   = $score['grade'] ?? 'N/A';
$gradeClass = match(strtoupper($grade[0] ?? 'F')) {
    'A' => 'grade-a', 'B' => 'grade-b', 'C' => 'grade-c', 'D' => 'grade-d', default => 'grade-f',
};
$section ??= 'overview';
$sectionLabel = [
    'overview'     => 'Overview',
    'models'       => 'Models',
    'controllers'  => 'Controllers',
    'routes'       => 'Routes',
    'jobs'         => 'Jobs',
    'events'       => 'Events',
    'services'     => 'Services',
    'repositories' => 'Repositories',
    'observers'    => 'Observers',
    'policies'     => 'Policies',
    'modules'      => 'Modules',
    'middleware'   => 'Middleware',
    'packages'     => 'Packages',
    'ai'           => 'AI Insights',
    'chat'         => 'AI Chat',
    'aidocs'       => 'AI Docs',
][$section] ?? 'Overview';
$nav = fn(string $s) => $section === $s ? 'nav-item nav-active' : 'nav-item';
@endphp

{{-- ══ SIDEBAR ══ --}}
<aside class="sidebar" id="sidebar">
    <div class="sidebar__brand">
        <div class="mark">
            <img src="{{ route('laradar.asset', ['filename' => 'laradar-icon.svg']) }}" alt="Laradar" width="34" height="34" style="display:block;object-fit:cover;border-radius:8px;">
        </div>
        <div><strong>Laradar</strong></div>
    </div>

    @if(!empty($score))
    @php
        $scorePct   = $score['max'] > 0 ? ($score['score'] / $score['max']) * 100 : 0;
        $scoreColor = $scorePct >= 90 ? 'var(--emerald)' : ($scorePct >= 70 ? '#FBBF24' : ($scorePct >= 50 ? '#FB923C' : 'var(--rose)'));
        $scoreBg    = $scorePct >= 90 ? 'rgba(52,211,153,0.12)' : ($scorePct >= 70 ? 'rgba(251,191,36,0.12)' : ($scorePct >= 50 ? 'rgba(251,146,60,0.12)' : 'rgba(244,63,94,0.12)'));
        $scoreBorder= $scorePct >= 90 ? 'rgba(52,211,153,0.3)' : ($scorePct >= 70 ? 'rgba(251,191,36,0.3)' : ($scorePct >= 50 ? 'rgba(251,146,60,0.3)' : 'rgba(244,63,94,0.3)'));
    @endphp
    <div class="score-spin-border" style="margin-bottom:20px;padding:14px 10px;border-radius:10px;">
        <span style="font-family:var(--font-mono);font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:var(--text-faint);display:block;margin-bottom:8px;">Score</span>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <span style="font-family:var(--font-mono);font-size:24px;font-weight:700;color:var(--text);">{{ $score['score'] }}<span style="font-size:13px;color:var(--text-faint);">/{{ $score['max'] }}</span></span>
            <span style="font-family:var(--font-mono);font-size:11px;font-weight:700;padding:2px 8px;border-radius:6px;color:{{ $scoreColor }};background:{{ $scoreBg }};border:1px solid {{ $scoreBorder }};">{{ $grade }}</span>
        </div>
        <div class="atlas-score-bar"><div class="atlas-score-fill" id="sidebar-score-bar" data-score-w="{{ round(($score['score']/max(1,$score['max']))*100) }}" style="width:0;background:{{ $scoreColor }};"></div></div>
    </div>
    @endif

    <nav>
        <div class="nav-group">
            <button onclick="navigate('overview')" id="nav-overview" class="{{ $nav('overview') }}" title="Overview">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span class="nav-label">Overview</span>
            </button>
            <button onclick="navigate('ai')" id="nav-ai" class="{{ $nav('ai') }}" title="AI Insights">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                <span class="nav-label">AI Insights
                @if(config('laradar.ai.enabled', false))
                <span style="margin-left:6px;width:8px;height:8px;border-radius:50%;background:var(--emerald);box-shadow:0 0 0 3px rgba(52,211,153,0.18);display:inline-block;vertical-align:middle;"></span>
                @endif
                </span>
            </button>
            <button onclick="navigate('chat')" id="nav-chat" class="{{ $nav('chat') }}" title="AI Chat">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <span class="nav-label">AI Chat</span>
            </button>
            <button onclick="navigate('aidocs')" id="nav-aidocs" class="{{ $nav('aidocs') }}" title="AI Docs">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span class="nav-label">AI Docs</span>
            </button>
        </div>

        <div class="nav-group">
            <span class="nav-group__label">Core</span>
            <button onclick="navigate('models')" id="nav-models" class="{{ $nav('models') }}" title="Models">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                <span class="nav-label">Models</span>
                @if(($summary['models']??0)>0)<span class="nav-badge">{{ $summary['models'] }}</span>@endif
            </button>
            <button onclick="navigate('controllers')" id="nav-controllers" class="{{ $nav('controllers') }}" title="Controllers">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                <span class="nav-label">Controllers</span>
                @if(($summary['controllers']??0)>0)<span class="nav-badge">{{ $summary['controllers'] }}</span>@endif
            </button>
            <button onclick="navigate('routes')" id="nav-routes" class="{{ $nav('routes') }}" title="Routes">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span class="nav-label">Routes</span>
                @if(($rs['total']??0)>0)<span class="nav-badge">{{ $rs['total'] }}</span>@endif
            </button>
            <button onclick="navigate('migrations')" id="nav-migrations" class="{{ $nav('migrations') }}" title="Migrations">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                <span class="nav-label">Migrations</span>
                @if(($summary['migrations']??0)>0)<span class="nav-badge">{{ $summary['migrations'] }}</span>@endif
            </button>
        </div>

        <div class="nav-group">
            <span class="nav-group__label">Components</span>
            <button onclick="navigate('jobs')" id="nav-jobs" class="{{ $nav('jobs') }}" title="Jobs">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span class="nav-label">Jobs</span>
                @if(($summary['jobs']??0)>0)<span class="nav-badge">{{ $summary['jobs'] }}</span>@endif
            </button>
            <button onclick="navigate('events')" id="nav-events" class="{{ $nav('events') }}" title="Events">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span class="nav-label">Events</span>
                @if(($summary['events']??0)>0)<span class="nav-badge">{{ $summary['events'] }}</span>@endif
            </button>
            <button onclick="navigate('services')" id="nav-services" class="{{ $nav('services') }}" title="Services">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="nav-label">Services</span>
                @if(($summary['services']??0)>0)<span class="nav-badge">{{ $summary['services'] }}</span>@endif
            </button>
            <button onclick="navigate('repositories')" id="nav-repositories" class="{{ $nav('repositories') }}" title="Repositories">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                <span class="nav-label">Repositories</span>
                @if(($summary['repositories']??0)>0)<span class="nav-badge">{{ $summary['repositories'] }}</span>@endif
            </button>
            <button onclick="navigate('observers')" id="nav-observers" class="{{ $nav('observers') }}" title="Observers">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span class="nav-label">Observers</span>
                @if(($summary['observers']??0)>0)<span class="nav-badge">{{ $summary['observers'] }}</span>@endif
            </button>
            <button onclick="navigate('policies')" id="nav-policies" class="{{ $nav('policies') }}" title="Policies">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span class="nav-label">Policies</span>
                @if(($summary['policies']??0)>0)<span class="nav-badge">{{ $summary['policies'] }}</span>@endif
            </button>
            <button onclick="navigate('modules')" id="nav-modules" class="{{ $nav('modules') }}" title="Modules">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span class="nav-label">Modules</span>
                @if(($summary['modules']??0)>0)<span class="nav-badge">{{ $summary['modules'] }}</span>@endif
            </button>
            <button onclick="navigate('middleware')" id="nav-middleware" class="{{ $nav('middleware') }}" title="Middleware">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span class="nav-label">Middleware</span>
                @php $mwCount = count($rs['middleware_usage']??[]); @endphp
                @if($mwCount > 0)<span class="nav-badge">{{ $mwCount }}</span>@endif
            </button>
            <button onclick="navigate('packages')" id="nav-packages" class="{{ $nav('packages') }}" title="Packages">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                <span class="nav-label">Packages</span>
                @if(($summary['packages']??0)>0)<span class="nav-badge">{{ $summary['packages'] }}</span>@endif
            </button>
        </div>


    </nav>

</aside>

{{-- Sidebar collapse toggle (sits on right edge of sidebar) --}}
<button id="sidebar-collapse-btn" onclick="toggleSidebarDesktop()" title="Toggle sidebar" aria-label="Toggle sidebar">
    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
</button>

{{-- ══ MAIN ══ --}}
<main class="content" style="display:flex;flex-direction:column;">

{{-- ══ TOPBAR ══ --}}
<div id="sidebar-overlay" onclick="toggleSidebar()"></div>
<header class="topbar">
    <button id="menu-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
    <div class="breadcrumb">
        <b id="topbar-section">{{ $sectionLabel }}</b>
    </div>
    <div style="display:flex;align-items:center;gap:10px;margin-left:auto;">
        <div class="sync-pill">
            <span class="sync-dot"></span>
            Laravel {{ $data['laravel_version'] }} · {{ $data['project']['name'] }}
        </div>
    </div>
</header>

{{-- PAGE CONTENT --}}
@isset($model)
    <div class="p-6 section-pane">
        @yield('content')
    </div>
@else
    @foreach(['overview','models','controllers','routes','migrations','jobs','events','services','repositories','observers','policies','modules','middleware','packages','ai','chat','aidocs'] as $__sec)
    <div id="sec-{{ $__sec }}" class="p-6 section-pane" @if($__sec !== $section) style="display:none" @endif>
        @include('laradar::sections.' . $__sec)
    </div>
    @endforeach
@endisset

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

<script>
const APP = @json($data);
const SECTIONS = ['overview','models','controllers','routes','migrations','jobs','events','services','repositories','observers','policies','modules','middleware','packages','ai','chat','aidocs'];

const LARADAR_ROUTES = {
    overview:     '{{ route("laradar.dashboard") }}',
    models:       '{{ route("laradar.models") }}',
    controllers:  '{{ route("laradar.controllers") }}',
    routes:       '{{ route("laradar.routes") }}',
    migrations:   '{{ route("laradar.migrations") }}',
    jobs:         '{{ route("laradar.jobs") }}',
    events:       '{{ route("laradar.events") }}',
    services:     '{{ route("laradar.services") }}',
    repositories: '{{ route("laradar.repositories") }}',
    observers:    '{{ route("laradar.observers") }}',
    policies:     '{{ route("laradar.policies") }}',
    modules:      '{{ route("laradar.modules") }}',
    middleware:   '{{ route("laradar.middleware") }}',
    packages:     '{{ route("laradar.packages") }}',
    ai:           '{{ route("laradar.ai") }}',
    chat:         '{{ route("laradar.chat") }}',
    aidocs:       '{{ route("laradar.aidocs") }}',
};

const _ALL_SECTION_LABELS = {
    overview:'Overview', models:'Models', controllers:'Controllers',
    routes:'Route Explorer', jobs:'Jobs', events:'Events', services:'Services',
    repositories:'Repositories', observers:'Observers', policies:'Policies',
    modules:'Modules', middleware:'Middleware', packages:'Packages',
    ai:'AI Insights', chat:'AI Chat', aidocs:'AI Docs',
};

function navigate(s) {
    if (!SECTIONS.includes(s)) return;
    const sb = document.querySelector('.sidebar');
    if (sb && sb.classList.contains('is-open')) toggleSidebar();
    const navEl = sb ? sb.querySelector('nav') : null;
    localStorage.setItem('laradar_sidebar_scroll', navEl ? navEl.scrollTop : (sb ? sb.scrollTop : 0));
    SECTIONS.forEach(sec => {
        const el = document.getElementById('sec-' + sec);
        if (el) el.style.display = 'none';
    });
    const target = document.getElementById('sec-' + s);
    if (!target) {
        // Model detail page — section panes not in DOM, do full navigation
        window.location.href = LARADAR_ROUTES[s];
        return;
    }
    target.style.display = '';
    // If we were in a detail view, reset to list when navigating to a section
    if (_activeDetailType) { showList(_activeDetailType); }
    history.pushState({ section: s }, '', LARADAR_ROUTES[s]);
    document.querySelectorAll('.nav-item').forEach(btn => btn.classList.remove('nav-active'));
    const navBtn = document.getElementById('nav-' + s);
    if (navBtn) navBtn.classList.add('nav-active');
    const labelEl = document.getElementById('topbar-section');
    if (labelEl) labelEl.textContent = _ALL_SECTION_LABELS[s] || s;
    const content = document.querySelector('.content');
    if (content) content.scrollTop = 0;
}

history.replaceState({ section: '{{ $section }}' }, '', window.location.href);

window.addEventListener('popstate', (e) => {
    const s   = e.state?.section || 'overview';
    const det = e.state?.detail;
    const idx = e.state?.idx;

    // If going back to a detail view (e.g. model detail)
    if (det !== undefined && idx !== undefined && s === 'models') {
        SECTIONS.forEach(sec => { const el = document.getElementById('sec-' + sec); if (el) el.style.display = 'none'; });
        const target = document.getElementById('sec-models');
        if (target) target.style.display = '';
        showDetail('models', idx, false);
        return;
    }

    // If going back to a list — reset any open detail first
    if (_activeDetailType) {
        document.getElementById(_activeDetailType + '-list').style.display = 'block';
        document.getElementById(_activeDetailType + '-detail').style.display = 'none';
        _activeDetailType = null;
        _activeDetailIdx  = null;
        _hideBackBtn();
    }

    SECTIONS.forEach(sec => {
        const el = document.getElementById('sec-' + sec);
        if (el) el.style.display = 'none';
    });
    const target = document.getElementById('sec-' + s);
    if (target) target.style.display = '';
    document.querySelectorAll('.nav-item').forEach(btn => btn.classList.remove('nav-active'));
    const navBtn = document.getElementById('nav-' + s);
    if (navBtn) navBtn.classList.add('nav-active');
    const labelEl = document.getElementById('topbar-section');
    if (labelEl) labelEl.textContent = _ALL_SECTION_LABELS[s] || s;
    const content = document.querySelector('.content');
    if (content) content.scrollTop = 0;
});

function _atlasTheme(el) {
    // Light theme: Tailwind's native colours are correct — no post-processing needed.
}

const _SECTION_LABELS = {
    models:'Models', controllers:'Controllers', routes:'Route Explorer',
    events:'Events', services:'Services',
    repositories:'Repositories', observers:'Observers', policies:'Policies',
};
let _activeDetailType = null;
let _activeDetailIdx  = null;

function _showBackBtn(label) {}
function _hideBackBtn() {}

function showDetail(type, idx, pushState = true) {
    document.getElementById(type + '-list').style.display = 'none';
    document.getElementById(type + '-detail').style.display = 'block';
    const contentEl = document.getElementById(type + '-detail-content');
    contentEl.innerHTML = renderDetail(type, APP[type][idx]);
    _atlasTheme(contentEl);
    _activeDetailType = type;
    _activeDetailIdx  = idx;
    _showBackBtn('Back to ' + (_SECTION_LABELS[type] || type));
    if (pushState && type === 'models' && APP.models[idx]) {
        const name = APP.models[idx].name || idx;
        history.pushState({ section: type, detail: name, idx }, '', LARADAR_ROUTES.models + '/' + encodeURIComponent(name));
    }
}

function showList(type) {
    document.getElementById(type + '-list').style.display = 'block';
    document.getElementById(type + '-detail').style.display = 'none';
    _activeDetailType = null;
    _activeDetailIdx  = null;
    _hideBackBtn();
    if (type === 'models') {
        history.pushState({ section: 'models' }, '', LARADAR_ROUTES.models);
    }
}

function topbarGoBack() {
    if (_activeDetailType) showList(_activeDetailType);
}

function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('is-open');
    document.getElementById('sidebar-overlay').classList.toggle('is-open');
}

function toggleSidebarDesktop() {
    const layout = document.querySelector('.atlas-layout');
    const collapsed = layout.classList.toggle('sidebar-collapsed');
    document.documentElement.classList.toggle('sidebar-pre-collapsed', collapsed);
    localStorage.setItem('laradar_sidebar', collapsed ? '0' : '1');
}

// Sync html class → atlas-layout class (html class applied in <head> to prevent flash)
// Also restore sidebar scroll position after navigation
document.addEventListener('DOMContentLoaded', function() {
    if (document.documentElement.classList.contains('sidebar-pre-collapsed')) {
        document.querySelector('.atlas-layout').classList.add('sidebar-collapsed');
    }
    const sb = document.getElementById('sidebar');
    const saved = localStorage.getItem('laradar_sidebar_scroll');
    if (sb && saved) {
        const pos = parseInt(saved, 10);
        const nav = sb.querySelector('nav');
        if (nav) nav.scrollTop = pos;
        else sb.scrollTop = pos;
        localStorage.removeItem('laradar_sidebar_scroll');
    }
});

function filterGrid(type) {
    const q = document.getElementById(type + '-search').value.toLowerCase();
    document.querySelectorAll('#' + type + '-grid [data-name]').forEach(el => {
        el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
    const lv = document.getElementById('mds-list-view');
    if (lv && type === 'models') lv.querySelectorAll('[data-name]').forEach(el => {
        el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
}

function filterPackages() {
    const q = document.getElementById('packages-search').value.toLowerCase();
    document.querySelectorAll('#packages-categories .pkg-card').forEach(el => {
        el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
    document.querySelectorAll('#packages-categories > div').forEach(cat => {
        const visible = [...cat.querySelectorAll('.pkg-card')].some(c => c.style.display !== 'none');
        cat.style.display = visible ? '' : 'none';
    });
}

function filterRoutes() {
    const q   = (document.getElementById('routes-search')?.value || '').toLowerCase();
    const mf  = document.getElementById('routes-method-filter')?.value || '';
    const mwf = (document.getElementById('routes-mw-filter')?.value || '').toLowerCase();
    document.querySelectorAll('.route-row').forEach(row => {
        const handler  = (row.querySelector('td:nth-child(3)')?.textContent || '').toLowerCase();
        const textOk   = !q   || row.dataset.uri.includes(q) || handler.includes(q);
        const methodOk = !mf  || row.dataset.methods.includes(mf);
        const mwOk     = !mwf || row.dataset.mw.includes(mwf);
        row.style.display = textOk && methodOk && mwOk ? '' : 'none';
    });
}

// ── Detail renderers ──────────────────────────────────────────────────────────

function renderDetail(type, item) {
    const map = {
        models: renderModel,
    };
    return (map[type] || (() => ''))(item);
}

function detailCard(title, body) {
    return `<div style="background:var(--bg-elevated);border-radius:12px;border:1px solid var(--border);padding:20px;margin-bottom:16px;"><h3 style="font-size:14px;font-weight:600;color:var(--text);margin-bottom:12px;margin-top:0;">${title}</h3>${body}</div>`;
}

function pill(text, color) {
    const c = color || '#6B778C';
    return `<span style="font-size:11px;font-family:var(--font-mono);padding:2px 8px;border-radius:5px;background:rgba(142,155,184,.12);color:${c};border:1px solid rgba(142,155,184,.2);">${text}</span>`;
}

function avatar(letter, color) {
    const c = color || '#6B778C';
    const rgb = c.replace('#','').match(/.{2}/g).map(x=>parseInt(x,16)).join(',');
    return `<div style="width:48px;height:48px;border-radius:12px;background:rgba(${rgb},.15);border:1px solid rgba(${rgb},.3);display:flex;align-items:center;justify-content:center;color:${c};font-size:18px;font-weight:700;flex:none;">${letter}</div>`;
}

const MDS_PALETTE = [
    {color:'#FF2D20', rgb:'255,45,32', hex:'#FF2D20'},
    {color:'#FF2D20', rgb:'255,45,32', hex:'#FF2D20'},
    {color:'#FF2D20', rgb:'255,45,32', hex:'#FF2D20'},
    {color:'#FF2D20', rgb:'255,45,32', hex:'#FF2D20'},
    {color:'#FF2D20', rgb:'255,45,32', hex:'#FF2D20'},
    {color:'#FF2D20', rgb:'255,45,32', hex:'#FF2D20'},
];
const MDS_REL_CFG = {
    hasMany:       {hex:'#FF2D20',color:'#FF2D20',bg:'rgba(255,45,32,.10)',border:'rgba(255,45,32,.28)'},
    hasOne:        {hex:'#FF2D20',color:'#FF2D20',bg:'rgba(255,45,32,.10)',border:'rgba(255,45,32,.28)'},
    belongsTo:     {hex:'#FF2D20',color:'#FF2D20',bg:'rgba(255,45,32,.10)',border:'rgba(255,45,32,.28)'},
    belongsToMany: {hex:'#FF2D20',color:'#FF2D20',bg:'rgba(255,45,32,.10)',border:'rgba(255,45,32,.28)'},
    morphMany:     {hex:'#FF2D20',color:'#FF2D20',bg:'rgba(255,45,32,.10)',border:'rgba(255,45,32,.28)'},
    morphTo:       {hex:'#FF2D20',color:'#FF2D20',bg:'rgba(255,45,32,.10)',border:'rgba(255,45,32,.28)'},
    morphOne:      {hex:'#FF2D20',color:'#FF2D20',bg:'rgba(255,45,32,.10)',border:'rgba(255,45,32,.28)'},
    hasManyThrough:{hex:'#FF2D20',color:'#FF2D20',bg:'rgba(255,45,32,.10)',border:'rgba(255,45,32,.28)'},
};

function _mdsColor(name) {
    const code = Math.abs((name || 'A').charCodeAt(0) - 65);
    return MDS_PALETTE[code % MDS_PALETTE.length];
}

function renderModel(m) {
    const esc = s => String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    const pal = _mdsColor(m.name || 'A');

    // Find which controllers use this model via dep edges
    const depEdges = APP.dependencies?.edges || [];
    const usedBy = [];
    depEdges.forEach(e => {
        if (e.to === m.name || e.to === (m.name||'').split('\\').pop()) {
            const ctrl = (APP.controllers||[]).find(c => c.name === e.from);
            if (ctrl && !usedBy.find(u => u.name === ctrl.name)) {
                const rCnt = (APP.routes||[]).filter(r => (r.controller?.class||r.action||'').includes(ctrl.name)).length;
                usedBy.push({name:ctrl.name, routes:rCnt});
            }
        }
    });

    // Build column type map from migrations matched by table name
    const columnTypes = {};
    (APP.migrations||[]).forEach(mig => {
        if (mig.table === m.table) {
            (mig.columns||[]).forEach(col => { columnTypes[col.name] = col.type; });
        }
    });

    // Build unified field map from fillable + hidden + casts + db type
    const fieldMap = new Map();
    (m.fillable||[]).forEach(f => fieldMap.set(f, {fillable:true, hidden:false, cast:null, db_type:columnTypes[f]||null}));
    (m.hidden||[]).forEach(f => {
        const ex = fieldMap.get(f) || {fillable:false, hidden:false, cast:null, db_type:columnTypes[f]||null};
        ex.hidden = true; fieldMap.set(f, ex);
    });
    Object.entries(m.casts||{}).forEach(([f,type]) => {
        const ex = fieldMap.get(f) || {fillable:false, hidden:false, cast:null, db_type:columnTypes[f]||null};
        ex.cast = String(type); fieldMap.set(f, ex);
    });

    const fillCnt = m.fillable?.length || 0;
    const hideCnt = m.hidden?.length   || 0;
    const relCnt  = m.relationships?.length || 0;
    const castCnt = Object.keys(m.casts||{}).length;
    const traitCnt= m.traits?.length || 0;
    const initial = (m.name?.[0]||'?').toUpperCase();
    const traits  = (m.traits||[]).map(t => t.split('\\').pop());

    // ── 2-column wrapper ──
    let h = `<div class="mds-det-wrap">`;

    // ── LEFT SIDEBAR ──
    h += `<aside class="mds-sidebar">
      <div class="mds-side-card">
        <div class="mds-side-top">
          <div class="mds-side-av" style="background:rgba(${pal.rgb},.18);color:${pal.color};border-color:rgba(${pal.rgb},.4);">${esc(initial)}</div>
          <p class="mds-side-name">${esc(m.name)}</p>
          <p class="mds-side-tbl">
            <svg style="width:10px;height:10px;display:inline;margin-right:4px;vertical-align:middle;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0018 0V5"/></svg>
            ${esc(m.table)}
          </p>
          <p class="mds-side-ns">${esc(m.namespace||'')}</p>
        </div>

        <div class="mds-side-stats">
          ${fillCnt  ? `<div class="mds-side-stat" onclick="mdsTab('fields')" title="Go to fields"><span class="mds-side-stat-lbl">Fillable</span><span class="mds-side-stat-val" style="color:#FF2D20;">${fillCnt}</span></div>` : ''}
          ${hideCnt  ? `<div class="mds-side-stat" onclick="mdsTab('fields')" title="Go to fields"><span class="mds-side-stat-lbl">Hidden</span><span class="mds-side-stat-val" style="color:#FF2D20;">${hideCnt}</span></div>` : ''}
          ${castCnt  ? `<div class="mds-side-stat" onclick="mdsTab('fields')" title="Go to fields"><span class="mds-side-stat-lbl">Casts</span><span class="mds-side-stat-val" style="color:#FF2D20;">${castCnt}</span></div>` : ''}
          ${relCnt   ? `<div class="mds-side-stat" onclick="mdsTab('relations')" title="Go to relationships"><span class="mds-side-stat-lbl">Relationships</span><span class="mds-side-stat-val" style="color:#FF2D20;">${relCnt}</span></div>` : ''}
          ${usedBy.length ? `<div class="mds-side-stat" onclick="mdsTab('usedby')" title="Go to used by"><span class="mds-side-stat-lbl">Used by</span><span class="mds-side-stat-val" style="color:#FF2D20;">${usedBy.length}</span></div>` : ''}
          ${!fillCnt && !relCnt && !usedBy.length ? `<p style="font-size:12px;color:var(--text-faint);text-align:center;padding:8px 0;">No data available</p>` : ''}
        </div>

        <div class="mds-side-meta">
          ${m.observer ? `<p style="font-size:10.5px;color:var(--text-faint);font-family:var(--font-mono);margin-bottom:6px;text-transform:uppercase;letter-spacing:.06em;font-weight:700;">Observer</p>
          <span class="mds-side-chip" style="background:rgba(255,45,32,.08);color:#FF2D20;border-color:rgba(255,45,32,.25);">${esc(m.observer.split('\\').pop())}</span>` : ''}
          ${traits.length ? `<p style="font-size:10.5px;color:var(--text-faint);font-family:var(--font-mono);margin:${m.observer?'12':'0'}px 0 8px;text-transform:uppercase;letter-spacing:.06em;font-weight:700;">Traits</p>
          <div style="display:flex;flex-wrap:wrap;gap:5px;">${traits.map(t => `<span class="mds-side-chip" style="background:rgba(255,45,32,.08);color:#FF2D20;border-color:rgba(255,45,32,.25);">${esc(t)}</span>`).join('')}</div>` : ''}
          ${m.timestamps !== false ? `<span class="mds-side-chip" style="background:rgba(255,45,32,.06);color:#FF2D20;border-color:rgba(255,45,32,.2);margin-top:8px;">timestamps</span>` : ''}
        </div>
      </div>
    </aside>`;

    // ── RIGHT MAIN CONTENT ──
    h += `<div>`;

    // Tabs
    h += `<div class="mds-tabs" id="mds-tabs-row">
      <button class="mds-tab-btn active" id="mds-tab-fields"    onclick="mdsTab('fields')">
        Fields ${fieldMap.size ? `<span style="font-size:10px;padding:1px 6px;border-radius:4px;background:rgba(255,45,32,.12);color:#FF2D20;margin-left:5px;font-family:var(--font-mono);">${fieldMap.size}</span>` : ''}
      </button>
      ${relCnt ? `<button class="mds-tab-btn" id="mds-tab-relations" onclick="mdsTab('relations')">Relationships <span style="font-size:10px;padding:1px 6px;border-radius:4px;background:rgba(255,45,32,.12);color:#FF2D20;margin-left:5px;font-family:var(--font-mono);">${relCnt}</span></button>` : ''}
      ${usedBy.length ? `<button class="mds-tab-btn" id="mds-tab-usedby" onclick="mdsTab('usedby')">Used By <span style="font-size:10px;padding:1px 6px;border-radius:4px;background:rgba(255,45,32,.12);color:#FF2D20;margin-left:5px;font-family:var(--font-mono);">${usedBy.length}</span></button>` : ''}
    </div>`;

    // ── Fields Tab ──
    h += `<div class="mds-tab-pane active" id="mds-pane-fields">`;
    if (fieldMap.size) {
        h += `<div class="mds-schema-wrap">
          <table class="mds-schema-tbl">
            <thead><tr>
              <th>Field</th><th>Status</th><th>Type</th><th>Cast Type</th>
            </tr></thead>
            <tbody>`;
        fieldMap.forEach((info, fname) => {
            h += `<tr>
              <td><span class="mds-field-name">${esc(fname)}</span></td>
              <td>
                ${info.fillable ? `<span class="mds-fbadge fill">FILLABLE</span>` : ''}
                ${info.hidden   ? `<span class="mds-fbadge hide">HIDDEN</span>`   : ''}
              </td>
              <td>${info.db_type ? `<span class="mds-cast-val">${esc(info.db_type)}</span>` : '<span style="color:var(--text-faint);font-size:12px;">—</span>'}</td>
              <td>${info.cast ? `<span class="mds-cast-val">${esc(info.cast)}</span>` : '<span style="color:var(--text-faint);font-size:12px;">—</span>'}</td>
            </tr>`;
        });
        h += `</tbody></table></div>`;
    }

    // Feature flags
    const flags = [];
    if (traits.some(t => t.includes('SoftDeletes'))) flags.push({label:'SoftDeletes',        color:'#FF2D20', bg:'rgba(255,45,32,.08)', border:'rgba(255,45,32,.2)'});
    if (traits.some(t => t.includes('HasFactory')))  flags.push({label:'HasFactory',          color:'#FF2D20', bg:'rgba(255,45,32,.08)', border:'rgba(255,45,32,.2)'});
    if (traits.some(t => t.includes('Searchable')))  flags.push({label:'Searchable',          color:'#FF2D20', bg:'rgba(255,45,32,.08)', border:'rgba(255,45,32,.2)'});
    if (m.timestamps !== false)                       flags.push({label:'$timestamps = true', color:'#FF2D20', bg:'rgba(255,45,32,.06)', border:'rgba(255,45,32,.15)'});
    if (flags.length) {
        h += `<div class="mds-flag-row">${flags.map(f => `<span class="mds-flag" style="color:${f.color};background:${f.bg};border-color:${f.border};">${esc(f.label)}</span>`).join('')}</div>`;
    }
    if (!fieldMap.size && !flags.length) {
        h += `<p style="color:var(--text-faint);font-size:13px;text-align:center;padding:40px 0;">No fillable, hidden or cast fields detected.</p>`;
    }
    h += `</div>`; // end fields pane

    // ── Relationships Tab ──
    if (relCnt) {
        h += `<div class="mds-tab-pane" id="mds-pane-relations">`;
        (m.relationships||[]).forEach(r => {
            const rc         = MDS_REL_CFG[r.type] || {color:'var(--text-dim)',bg:'rgba(91,103,133,.1)',border:'var(--border)'};
            const rel        = r.related ? r.related.split('\\').pop() : '—';
            const navIdx     = (APP.models||[]).findIndex(md => md.name === rel);
            const fk         = r.foreign_key || null;
            const isManyMany = r.type === 'belongsToMany';
            const fkLine     = fk
                ? `<span style="font-family:var(--font-mono);font-size:10px;color:${isManyMany ? 'var(--text-faint)' : '#FF2D20'};opacity:.85;">${isManyMany ? 'via ' + esc(fk) : esc(fk)}</span>`
                : '';
            h += `<div class="mds-rel-card" style="border-color:var(--border);" onmouseenter="this.style.borderColor='${rc.border}'" onmouseleave="this.style.borderColor='var(--border)'">
              <div style="min-width:160px;display:flex;flex-direction:column;gap:3px;">
                <span class="mds-rel-method">${esc(r.method)}()</span>
                ${fkLine}
              </div>
              <span class="mds-rel-type" style="color:${rc.color};background:${rc.bg};border-color:${rc.border};">${esc(r.type)}</span>
              <span class="mds-rel-arrow">→</span>
              <span class="mds-rel-target">${esc(rel)}</span>
              ${navIdx >= 0
                ? `<button class="mds-nav-btn" style="color:${rc.color};background:${rc.bg};border-color:${rc.border};" onclick="event.stopPropagation();showDetail('models',${navIdx});">View →</button>`
                : '<span></span>'}
            </div>`;
        });
        h += `</div>`;
    }

    // ── Used By Tab ──
    if (usedBy.length) {
        h += `<div class="mds-tab-pane" id="mds-pane-usedby">`;
        usedBy.forEach(u => {
            const ci = (APP.controllers||[]).findIndex(c => c.name === u.name);
            h += `<div class="mds-usedby-card">
              <div>
                <span style="font-size:14px;font-weight:700;color:var(--text);">${esc(u.name)}</span>
                <span style="font-size:11px;color:var(--text-faint);margin-left:10px;font-family:var(--font-mono);">${u.routes} route${u.routes!==1?'s':''}</span>
              </div>
              ${ci >= 0 ? `<button class="mds-nav-btn" style="color:#FF2D20;background:rgba(255,45,32,.08);border-color:rgba(255,45,32,.2);" onclick="event.stopPropagation();navigate('controllers');">View controller →</button>` : ''}
            </div>`;
        });
        h += `</div>`;
    }

    h += `</div></div>`; // close main + wrap
    return h;
}

function mdsTab(tab) {
    document.querySelectorAll('.mds-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.mds-tab-pane').forEach(p => p.classList.remove('active'));
    const btn  = document.getElementById('mds-tab-' + tab);
    const pane = document.getElementById('mds-pane-' + tab);
    if (btn)  btn.classList.add('active');
    if (pane) pane.classList.add('active');
}

function mdsView(view) {
    document.getElementById('mds-grid-view').style.display = view === 'grid' ? '' : 'none';
    document.getElementById('mds-list-view').style.display = view === 'list' ? '' : 'none';
    document.getElementById('mds-vbtn-grid').classList.toggle('active', view === 'grid');
    document.getElementById('mds-vbtn-list').classList.toggle('active', view === 'list');
}


// ── Model Relationship Map ────────────────────────────────────────────────────

const REL_COLORS = {
    hasMany:        { color:'#34D399', bg:'rgba(52,211,153,.13)',  border:'rgba(52,211,153,.3)'  },
    hasOne:         { color:'#FF2D20', bg:'rgba(255,45,32,.12)',   border:'rgba(255,45,32,.3)'   },
    belongsTo:      { color:'#60A5FA', bg:'rgba(96,165,250,.13)',  border:'rgba(96,165,250,.3)'  },
    belongsToMany:  { color:'#A78BFA', bg:'rgba(167,139,250,.13)', border:'rgba(167,139,250,.3)' },
    morphMany:      { color:'#FB923C', bg:'rgba(251,146,60,.13)',  border:'rgba(251,146,60,.3)'  },
    morphOne:       { color:'#FBBF24', bg:'rgba(251,191,36,.13)',  border:'rgba(251,191,36,.3)'  },
    morphTo:        { color:'#F87171', bg:'rgba(248,113,113,.13)', border:'rgba(248,113,113,.3)' },
    morphToMany:    { color:'#E879F9', bg:'rgba(232,121,249,.13)', border:'rgba(232,121,249,.3)' },
    hasManyThrough: { color:'#2DD4BF', bg:'rgba(45,212,191,.13)',  border:'rgba(45,212,191,.3)'  },
    hasOneThrough:  { color:'#38BDF8', bg:'rgba(56,189,248,.13)',  border:'rgba(56,189,248,.3)'  },
};

// ── Boot ──────────────────────────────────────────────────────────────────────

// ── Architecture Explorer ────────────────────────────────────────────────────
const OV_VIOLET = [255,45,32], OV_CYAN_RGB = [255,83,73];
function _lerpColor(a, b, t) {
    return `rgb(${Math.round(a[0]+(b[0]-a[0])*t)},${Math.round(a[1]+(b[1]-a[1])*t)},${Math.round(a[2]+(b[2]-a[2])*t)})`;
}
function _svgEl(tag, attrs) {
    const el = document.createElementNS('http://www.w3.org/2000/svg', tag);
    Object.entries(attrs || {}).forEach(([k, v]) => el.setAttribute(k, v));
    return el;
}

let _ovArchScale = 1;

function _buildOvArchDiagram() {
    const host = document.getElementById('ovArchDiagram');
    if (!host) return;

    const MAX_NODES = 8;
    const BOX_W = 180, BOX_H = 52;

    const allCtrls    = APP.controllers || [];
    const allModels   = APP.models || [];
    const allServices = APP.services || [];
    const allRepos    = APP.repositories || [];
    const routes      = APP.routes || [];
    const depEdges    = (APP.dependencies?.edges || []);
    const depNodes    = (APP.dependencies?.nodes || []);

    // Build node→layer lookup from dep graph metadata
    const nodeLayerMap = {};
    depNodes.forEach(n => { nodeLayerMap[n.name] = n.layer; });

    // ── Controllers ──
    const seenCtrl = new Set();
    const sortedCtrls = [...allCtrls]
        .sort((a, b) => (b.method_count || 0) - (a.method_count || 0))
        .filter(c => { if (seenCtrl.has(c.name)) return false; seenCtrl.add(c.name); return true; });
    const visibleCtrls = sortedCtrls.slice(0, MAX_NODES);
    const extraCtrlCnt = sortedCtrls.length - visibleCtrls.length;
    const ctrlNodes = visibleCtrls.map((c, i) => ({
        id: 'c-' + i, label: c.name.replace(/Controller$/, ''), sub: (c.method_count || 0) + ' methods',
        rawName: c.name, isMore: false,
    }));
    if (extraCtrlCnt > 0) ctrlNodes.push({ id:'c-more', label:`+ ${extraCtrlCnt} more`, sub:'controllers', rawName:null, isMore:true });
    const ctrlNameToId = {};
    ctrlNodes.forEach(n => { if (n.rawName) ctrlNameToId[n.rawName] = n.id; });

    // ── Services (separate layer) ──
    const visibleSvcs = allServices.slice(0, MAX_NODES);
    const extraSvcCnt = allServices.length - visibleSvcs.length;
    const svcOnlyNodes = visibleSvcs.map((s, i) => ({
        id: 'sv-' + i, label: s.name, sub: 'service', rawName: s.name, isMore: false,
    }));
    if (extraSvcCnt > 0) svcOnlyNodes.push({ id:'sv-more', label:`+ ${extraSvcCnt} more`, sub:'services', rawName:null, isMore:true });
    const svcNameToId = {};
    svcOnlyNodes.forEach(n => { if (n.rawName) svcNameToId[n.rawName] = n.id; });
    const hasServices = svcOnlyNodes.length > 0;

    // ── Repositories (separate layer) ──
    const visibleRepos = allRepos.slice(0, MAX_NODES);
    const extraRepoCnt = allRepos.length - visibleRepos.length;
    const repoOnlyNodes = visibleRepos.map((r, i) => ({
        id: 'rp-' + i, label: r.name, sub: 'repository', rawName: r.name, isMore: false,
    }));
    if (extraRepoCnt > 0) repoOnlyNodes.push({ id:'rp-more', label:`+ ${extraRepoCnt} more`, sub:'repositories', rawName:null, isMore:true });
    const repoNameToId = {};
    repoOnlyNodes.forEach(n => { if (n.rawName) repoNameToId[n.rawName] = n.id; });
    const hasRepos = repoOnlyNodes.length > 0;

    // ── Categorise dep edges by target layer ──
    const ctrlSvcEdgeList  = []; // [ctrlId, svcId]
    const ctrlRepoEdgeList = []; // [ctrlId, repoId]
    const ctrlModelEdgeList = []; // [ctrlId, modelName]
    const referencedModelNames = new Set();
    depEdges.forEach(e => {
        const cId = ctrlNameToId[e.from];
        if (!cId || !e.to) return;
        const layer = nodeLayerMap[e.to] || '';
        if (layer === 'service') {
            const sId = svcNameToId[e.to]; if (sId) ctrlSvcEdgeList.push([cId, sId]);
        } else if (layer === 'repository') {
            const rId = repoNameToId[e.to]; if (rId) ctrlRepoEdgeList.push([cId, rId]);
        } else {
            referencedModelNames.add(e.to);
            ctrlModelEdgeList.push([cId, e.to]);
        }
    });

    // ── Models ──
    const allModelsSorted = [
        ...allModels.filter(m => referencedModelNames.has(m.name)),
        ...allModels.filter(m => !referencedModelNames.has(m.name)),
    ].slice(0, MAX_NODES);
    const extraModelCnt = allModels.length - allModelsSorted.length;
    const modelNodes = allModelsSorted.map((m, i) => ({
        id: 'm-' + i, label: m.name, sub: m.table || 'model', rawName: m.name, isMore: false,
    }));
    if (extraModelCnt > 0) modelNodes.push({ id:'m-more', label:`+ ${extraModelCnt} more`, sub:'models', rawName:null, isMore:true });
    const modelNameToId = {};
    modelNodes.forEach(n => { if (n.rawName) modelNameToId[n.rawName] = n.id; });

    // ── Route nodes ──
    const webCnt   = routes.filter(r => (r.middleware||[]).includes('web')).length;
    const apiCnt   = routes.filter(r => (r.middleware||[]).includes('api')).length;
    const otherCnt = routes.length - webCnt - apiCnt;
    const routeNodes = [];
    if (webCnt > 0)   routeNodes.push({ id:'r-web',   label:'web.php',   sub: webCnt   + ' routes' });
    if (apiCnt > 0)   routeNodes.push({ id:'r-api',   label:'api.php',   sub: apiCnt   + ' routes' });
    if (otherCnt > 0) routeNodes.push({ id:'r-other', label:'routes',    sub: otherCnt + ' routes' });
    if (!routeNodes.length) routeNodes.push({ id:'r-all', label:'Routes', sub: routes.length + ' total' });

    // ── Build layer stack ──
    const LAYERS = [
        { name:'Application',  nodes: [{ id:'app', label:(APP.project?.name)||'Laravel App', sub:'HTTP Kernel', isMore:false }] },
        { name:'Routes',       nodes: routeNodes },
        { name:'Controllers',  nodes: ctrlNodes.length ? ctrlNodes : [{ id:'c-all', label:'Controllers', sub: allCtrls.length + ' total', isMore:true }] },
    ];
    if (hasServices)  LAYERS.push({ name:'Services',     nodes: svcOnlyNodes });
    if (hasRepos)     LAYERS.push({ name:'Repositories', nodes: repoOnlyNodes });
    LAYERS.push({ name:'Models',   nodes: modelNodes.length ? modelNodes : [{ id:'m-all', label:'Models', sub: allModels.length + ' total', isMore:true }] });
    LAYERS.push({ name:'Database', nodes: [{ id:'db', label:'Database', sub: allModels.length + ' model(s)', isMore:false }] });

    // ── Edges ──
    const EDGES = [];
    const realCtrlNodes  = LAYERS[2].nodes.filter(n => !n.isMore);
    const modelsLayerIdx = LAYERS.findIndex(l => l.name === 'Models');
    const realModelNodes = LAYERS[modelsLayerIdx].nodes.filter(n => !n.isMore);
    const realSvcNodes   = svcOnlyNodes.filter(n => !n.isMore);
    const realRepoNodes  = repoOnlyNodes.filter(n => !n.isMore);

    // App → Routes
    LAYERS[1].nodes.forEach(r => EDGES.push(['app', r.id]));

    // Routes → Controllers (representative: up to 3)
    if (realCtrlNodes.length) {
        LAYERS[1].nodes.forEach(r => {
            const picks = realCtrlNodes.length <= 3
                ? realCtrlNodes
                : [realCtrlNodes[0], realCtrlNodes[Math.floor(realCtrlNodes.length / 2)], realCtrlNodes[realCtrlNodes.length - 1]];
            picks.forEach(c => EDGES.push([r.id, c.id]));
        });
    }

    // Track which controllers already have a forward edge from real dep data
    const connectedCtrls = new Set();

    // Controllers → Services (real dep edges)
    ctrlSvcEdgeList.forEach(([cId, sId]) => { EDGES.push([cId, sId]); connectedCtrls.add(cId); });

    // Controllers → Repositories (real dep edges, direct)
    ctrlRepoEdgeList.forEach(([cId, rId]) => { EDGES.push([cId, rId]); connectedCtrls.add(cId); });

    // Any controller with no real forward edge gets a representative connection
    const unconnectedCtrls = realCtrlNodes.filter(c => !connectedCtrls.has(c.id));
    if (unconnectedCtrls.length > 0) {
        if (hasServices && realSvcNodes.length) {
            unconnectedCtrls.forEach((c, i) => EDGES.push([c.id, realSvcNodes[i % realSvcNodes.length].id]));
        } else if (hasRepos && realRepoNodes.length) {
            unconnectedCtrls.forEach((c, i) => EDGES.push([c.id, realRepoNodes[i % realRepoNodes.length].id]));
        } else if (realModelNodes.length) {
            unconnectedCtrls.forEach((c, i) => EDGES.push([c.id, realModelNodes[i % realModelNodes.length].id]));
        }
    }

    // Services → Repositories (always connect when both layers exist)
    if (hasServices && hasRepos && realSvcNodes.length && realRepoNodes.length) {
        realSvcNodes.forEach((sv, i) => EDGES.push([sv.id, realRepoNodes[i % realRepoNodes.length].id]));
    }

    // Repositories → Models (every visible repo gets at least one edge)
    if (hasRepos && realRepoNodes.length && realModelNodes.length) {
        realRepoNodes.forEach((rp, i) => EDGES.push([rp.id, realModelNodes[i % realModelNodes.length].id]));
    }

    // Services → Models (only when no repo layer sits between them)
    if (hasServices && !hasRepos && realSvcNodes.length && realModelNodes.length) {
        realSvcNodes.forEach((sv, i) => EDGES.push([sv.id, realModelNodes[i % realModelNodes.length].id]));
    }

    // Controllers → Models (only when no service or repo layers exist at all)
    if (!hasServices && !hasRepos) {
        if (ctrlModelEdgeList.length > 0) {
            ctrlModelEdgeList.forEach(([cId, mName]) => {
                const mId = modelNameToId[mName]; if (mId) EDGES.push([cId, mId]);
            });
        } else if (realCtrlNodes.length && realModelNodes.length) {
            realCtrlNodes.forEach((c, i) => EDGES.push([c.id, realModelNodes[i % realModelNodes.length].id]));
        }
    }

    // Models → Database (every visible model connects)
    (realModelNodes.length ? realModelNodes : LAYERS[modelsLayerIdx].nodes).forEach(m => EDGES.push([m.id, 'db']));

    // ── Layout ──
    const n = LAYERS.length;
    const maxNodes = Math.max(...LAYERS.map(l => l.nodes.length));
    const NODE_SPACING = 64;
    const BAND_TOP = 60;
    const BAND_BTM = Math.max(420, BAND_TOP + (maxNodes - 1) * NODE_SPACING);
    const VB_W = Math.max(1240, n * 240 + 100), VB_H = BAND_BTM + 70;
    const colX = LAYERS.map((_, i) => 100 + i * ((VB_W - 220) / (n - 1)));
    const positions = {};

    LAYERS.forEach((layer, li) => {
        const cnt = layer.nodes.length;
        layer.nodes.forEach((node, ni) => {
            const y = cnt === 1
                ? (BAND_TOP + BAND_BTM) / 2
                : BAND_TOP + ni * ((BAND_BTM - BAND_TOP) / (cnt - 1));
            positions[node.id] = { x: colX[li], y, layer: li, label: node.label, sub: node.sub, layerName: layer.name, isMore: !!node.isMore };
        });
    });

    // ── SVG ──
    const svg = _svgEl('svg', { viewBox:`0 0 ${VB_W} ${VB_H}`, width:VB_W, height:VB_H, style:'display:block;overflow:visible;' });

    // Drop-shadow filter for node boxes
    const ovDefs = _svgEl('defs');
    const ovFilter = _svgEl('filter', { id:'ov-shadow', x:'-20%', y:'-30%', width:'140%', height:'160%' });
    const ovFds = _svgEl('feDropShadow', { dx:'0', dy:'2', stdDeviation:'4' });
    ovFds.setAttribute('flood-color', 'rgba(23,43,77,0.10)');
    ovFds.setAttribute('flood-opacity', '1');
    ovFilter.appendChild(ovFds);
    ovDefs.appendChild(ovFilter);
    svg.appendChild(ovDefs);

    // Layer header labels
    LAYERS.forEach((layer, li) => {
        const t = li / (n - 1);
        const color = _lerpColor(OV_VIOLET, OV_CYAN_RGB, t);
        const tx = _svgEl('text', { x:colX[li], y:22, 'text-anchor':'middle', 'font-family':'Inter,sans-serif', 'font-size':10, 'font-weight':700, 'letter-spacing':'0.08em', fill:color });
        tx.textContent = layer.name.toUpperCase();
        svg.appendChild(tx);
        // Subtle column separator line
        if (li > 0) {
            const sep = _svgEl('line', { x1: colX[li] - (colX[1]-colX[0])/2, y1: 30, x2: colX[li] - (colX[1]-colX[0])/2, y2: VB_H - 20, stroke:'rgba(23,43,77,0.08)', 'stroke-width':'1' });
            svg.appendChild(sep);
        }
    });

    // Edges with traveling dots
    const edgeGroup = _svgEl('g');
    const edgeEls = [];
    EDGES.forEach(([from, to], i) => {
        const a = positions[from], b = positions[to]; if (!a || !b) return;
        const x1 = a.x + BOX_W/2, y1 = a.y, x2 = b.x - BOX_W/2, y2 = b.y, midX = (x1+x2)/2;
        const d = `M ${x1} ${y1} C ${midX} ${y1}, ${midX} ${y2}, ${x2} ${y2}`;
        const t = (a.layer + b.layer) / (2*(n-1));
        const color = _lerpColor(OV_VIOLET, OV_CYAN_RGB, t);
        const path = _svgEl('path', { d, id:`ov-edge-${i}`, fill:'none', stroke:color, 'stroke-width':'1.5' });
        path.dataset.from = from; path.dataset.to = to;
        path.style.opacity = '0.3';
        edgeGroup.appendChild(path);
        edgeEls.push(path);
        const dot = _svgEl('circle', { r:'2.5', fill:color });
        const anim = _svgEl('animateMotion', { dur:'3s', repeatCount:'indefinite', begin:`${(i*0.3).toFixed(2)}s` });
        const mp = _svgEl('mpath'); mp.setAttributeNS('http://www.w3.org/1999/xlink','href',`#ov-edge-${i}`);
        anim.appendChild(mp); dot.appendChild(anim);
        const opAnim = _svgEl('animate', { attributeName:'opacity', values:'0;1;1;0', keyTimes:'0;0.08;0.9;1', dur:'3s', repeatCount:'indefinite', begin:`${(i*0.3).toFixed(2)}s` });
        dot.appendChild(opAnim); edgeGroup.appendChild(dot);
    });
    svg.appendChild(edgeGroup);

    // Node boxes
    const nodeGroup = _svgEl('g');
    const nodeEls = [];
    Object.entries(positions).forEach(([id, pos]) => {
        const t = pos.layer / (n-1);
        const color = _lerpColor(OV_VIOLET, OV_CYAN_RGB, t);
        const g = _svgEl('g', { class:'ov-arch-node', role:'button', 'aria-label':pos.label });
        g.dataset.id = id;
        const x = pos.x - BOX_W/2, y = pos.y - BOX_H/2;

        // "more" nodes get a dashed, dimmer style
        const rectFill   = pos.isMore ? 'rgba(255,45,32,0.04)' : '#FFFFFF';
        const rectStroke = pos.isMore ? `rgba(255,45,32,0.25)` : color;
        const rectDash   = pos.isMore ? '4,3' : 'none';
        const rect = _svgEl('rect', { x, y, width:BOX_W, height:BOX_H, rx:'10', fill:rectFill, stroke:rectStroke, 'stroke-width':'1.5', 'stroke-dasharray':rectDash, filter:'url(#ov-shadow)' });
        rect.dataset.origStroke = rectStroke;

        const dotR = pos.isMore ? '2.5' : '4';
        const dotEl = _svgEl('circle', { cx:x+16, cy:y+BOX_H/2, r:dotR, fill: pos.isMore ? 'rgba(255,45,32,0.35)' : color });

        // Label: max 22 chars (wider box allows more)
        const lbl = _svgEl('text', { x:x+30, y:y+20, 'font-family':'Inter,sans-serif', 'font-size':'11', 'font-weight': pos.isMore ? '500' : '600', fill: pos.isMore ? '#6B778C' : '#172B4D' });
        lbl.textContent = pos.label.length > 22 ? pos.label.slice(0, 22) + '…' : pos.label;

        const sub = _svgEl('text', { x:x+30, y:y+36, 'font-family':'JetBrains Mono,monospace', 'font-size':'9.5', fill:'#6B778C' });
        sub.textContent = pos.sub || '';

        g.appendChild(rect); g.appendChild(dotEl); g.appendChild(lbl); g.appendChild(sub);
        nodeGroup.appendChild(g); nodeEls.push(g);
    });
    svg.appendChild(nodeGroup);
    host.innerHTML = ''; host.appendChild(svg);

    // Hover + click-to-pin path highlight
    const detail = document.getElementById('ovArchDetail');
    const defaultDetail = detail ? detail.innerHTML : '';
    const neighbors = {};
    EDGES.forEach(([f, t]) => { (neighbors[f]=neighbors[f]||new Set()).add(t); (neighbors[t]=neighbors[t]||new Set()).add(f); });

    let _ovPinnedId = null;

    function ovGetFlowPath(id) {
        // Backward only: find upstream ancestors (never follow forward from ancestors)
        const ancestors = new Set([id]);
        const bwdQ = [id];
        while (bwdQ.length) {
            const curr = bwdQ.shift();
            EDGES.forEach(([f, t]) => {
                if (t === curr && !ancestors.has(f)) { ancestors.add(f); bwdQ.push(f); }
            });
        }
        // Forward only: find downstream descendants (never follow backward from descendants)
        const descendants = new Set([id]);
        const fwdQ = [id];
        while (fwdQ.length) {
            const curr = fwdQ.shift();
            EDGES.forEach(([f, t]) => {
                if (f === curr && !descendants.has(t)) { descendants.add(t); fwdQ.push(t); }
            });
        }
        return new Set([...ancestors, ...descendants]);
    }

    function ovHighlight(id) {
        const rel = neighbors[id] || new Set();
        edgeEls.forEach(p => { const c = p.dataset.from===id||p.dataset.to===id; p.style.opacity=c?'0.9':'0.05'; p.style.strokeWidth=c?'2.2':'1.5'; });
        nodeEls.forEach(nd => { nd.style.opacity=(nd.dataset.id===id||rel.has(nd.dataset.id))?'1':'0.25'; });
        if (detail) { const pos=positions[id]; detail.textContent = `${pos.label} · ${pos.sub||pos.layerName} — ${rel.size} connection${rel.size===1?'':'s'}`; }
    }

    function ovHighlightPath(id) {
        const pathNodes = ovGetFlowPath(id);
        edgeEls.forEach(p => {
            const inPath = pathNodes.has(p.dataset.from) && pathNodes.has(p.dataset.to);
            p.style.opacity = inPath ? '1' : '0.04';
            p.style.strokeWidth = inPath ? '2.5' : '1.5';
        });
        nodeEls.forEach(nd => {
            const inPath = pathNodes.has(nd.dataset.id);
            nd.style.opacity = inPath ? '1' : '0.12';
            const rect = nd.querySelector('rect');
            if (rect) {
                rect.setAttribute('stroke-width', nd.dataset.id === id ? '2.5' : '1.5');
                if (nd.dataset.id === id) rect.setAttribute('stroke', '#FF2D20');
            }
        });
        if (detail) {
            const pos = positions[id];
            detail.innerHTML = `<span style="color:#FF2D20;font-weight:700;">${pos.label}</span> &mdash; flow pinned &middot; click again to clear`;
        }
    }

    function ovReset() {
        edgeEls.forEach(p => { p.style.opacity='0.3'; p.style.strokeWidth='1.5'; });
        nodeEls.forEach(nd => {
            nd.style.opacity = '1';
            const rect = nd.querySelector('rect');
            if (rect) { rect.setAttribute('stroke-width','1.5'); if (rect.dataset.origStroke) rect.setAttribute('stroke', rect.dataset.origStroke); }
        });
        if (detail) detail.innerHTML = defaultDetail;
    }

    nodeEls.forEach(nd => {
        nd.style.cursor = 'pointer';
        nd.addEventListener('mouseenter', () => { if (!_ovPinnedId) ovHighlight(nd.dataset.id); });
        nd.addEventListener('mouseleave', () => { if (!_ovPinnedId) ovReset(); });
        nd.addEventListener('click', () => {
            const id = nd.dataset.id;
            if (_ovPinnedId === id) { _ovPinnedId = null; ovReset(); }
            else { _ovPinnedId = id; ovHighlightPath(id); }
        });
    });

    // Click on SVG background clears pin
    svg.addEventListener('click', e => {
        if (e.target === svg || e.target.tagName === 'svg') { _ovPinnedId = null; ovReset(); }
    });

    // Zoom controls
    const zoomIn  = document.getElementById('ovZoomIn');
    const zoomOut = document.getElementById('ovZoomOut');
    function _applyOvZoom() { svg.setAttribute('width', VB_W*_ovArchScale); svg.setAttribute('height', VB_H*_ovArchScale); }
    if (zoomIn)  zoomIn.addEventListener('click',  () => { _ovArchScale = Math.min(1.7, _ovArchScale+0.15); _applyOvZoom(); });
    if (zoomOut) zoomOut.addEventListener('click', () => { _ovArchScale = Math.max(0.6, _ovArchScale-0.15); _applyOvZoom(); });

    // Fullscreen toggle
    const fsBtn        = document.getElementById('ovFullscreen');
    const fsPanel      = document.getElementById('ovArchPanel');
    const fsIconExp    = document.getElementById('ovFsIconExpand');
    const fsIconCompr  = document.getElementById('ovFsIconCompress');
    function _setFsIcon(isFs) {
        if (fsIconExp)   fsIconExp.style.display   = isFs ? 'none'  : '';
        if (fsIconCompr) fsIconCompr.style.display = isFs ? ''      : 'none';
    }
    if (fsBtn && fsPanel) {
        fsBtn.addEventListener('click', () => {
            if (!document.fullscreenElement && !document.webkitFullscreenElement) {
                (fsPanel.requestFullscreen || fsPanel.webkitRequestFullscreen).call(fsPanel);
            } else {
                (document.exitFullscreen || document.webkitExitFullscreen).call(document);
            }
        });
        document.addEventListener('fullscreenchange',       () => _setFsIcon(!!document.fullscreenElement));
        document.addEventListener('webkitfullscreenchange', () => _setFsIcon(!!document.webkitFullscreenElement));
    }
}

// Reveal animation with count-up and bar wipe
function _countUp(el, target, duration) {
    if (el.dataset.animating) return;
    el.dataset.animating = '1';
    const safeTarget = Math.max(0, Math.round(+target));
    const start = performance.now();
    (function step(now) {
        const t = Math.min(1, Math.max(0, (now - start) / duration));
        const eased = 1 - (1 - t) * (1 - t);
        el.textContent = Math.round(eased * safeTarget);
        if (t < 1) requestAnimationFrame(step);
        else el.textContent = safeTarget;
    })(performance.now());
}

(function() {
    function _animateBars(root) {
        root.querySelectorAll('.atlas-score-fill[data-score-w]').forEach(b => {
            b.style.transition = 'width 0.85s var(--ease)';
            requestAnimationFrame(() => { b.style.width = b.dataset.scoreW + '%'; });
        });
    }

    function _revealEl(el) {
        if (el.classList.contains('ov-in')) return;
        el.classList.add('ov-in');
        el.querySelectorAll('.kpi-card__num[data-count]').forEach(n => {
            delete n.dataset.animating;
            _countUp(n, +n.dataset.count, 900);
        });
        _animateBars(el);
    }

    if (!window.IntersectionObserver) {
        document.querySelectorAll('[data-ov-reveal]').forEach(_revealEl);
    } else {
        const io = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (!e.isIntersecting) return;
                io.unobserve(e.target);
                _revealEl(e.target);
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('[data-ov-reveal]').forEach(el => io.observe(el));

        // Force-reveal cards already in viewport before IO fires (large projects).
        requestAnimationFrame(() => {
            document.querySelectorAll('[data-ov-reveal]').forEach(_revealEl);
        });
    }

    // Sidebar score bar — always visible, animate shortly after load
    const sideBar = document.getElementById('sidebar-score-bar');
    if (sideBar) {
        setTimeout(() => {
            sideBar.style.transition = 'width 1s var(--ease)';
            sideBar.style.width = sideBar.dataset.scoreW + '%';
        }, 500);
    }
})();

_buildOvArchDiagram();

// ── Dependency Graph (custom layered SVG) ────────────────────────────────────

const _DEP_NW  = 114;   // node width
const _DEP_NH  = 32;    // node height
const _DEP_HG  = 10;    // horizontal gap between nodes
const _DEP_MR  = 9;     // max nodes per row within a layer
const _DEP_RG  = 14;    // gap between rows within a layer
const _DEP_LG  = 80;    // gap between layers

const _DEP_CFG = {
    // ATLAS dark theme: dark bg, bright stroke, label is the border/text color
    controller: { label:'Controllers', color:'#FF2D20', bg:'#FFF1F0', order:0 },
    job:        { label:'Jobs',        color:'#FF5630', bg:'#FFF4E5', order:1 },
    event:      { label:'Events',      color:'#BF40BF', bg:'#FFF0FB', order:1 },
    listener:   { label:'Listeners',   color:'#DA62AC', bg:'#FEE4FA', order:2 },
    service:    { label:'Services',    color:'#00875A', bg:'#E3FCEF', order:2 },
    repository: { label:'Repositories',color:'#FF8B00', bg:'#FFFAE6', order:3 },
    model:      { label:'Models',      color:'#6554C0', bg:'#F3F0FF', order:4 },
    database:   { label:'Database',    color:'#6B778C', bg:'#F4F5F7', order:5 },
};

let _depT  = { tx:0, ty:0, s:1 };
let _depDrag = null;
let _depPos  = {};
let _depSel  = null;
const NS = 'http://www.w3.org/2000/svg';

function initDepGraph() {
    const nodes = (APP.dependencies || {}).nodes || [];
    const edges = (APP.dependencies || {}).edges || [];
    if (!nodes.length) return;

    const canvas  = document.getElementById('dep-canvas');
    const bandsG  = document.getElementById('dep-bands-g');
    const edgesG  = document.getElementById('dep-edges-g');
    const nodesG  = document.getElementById('dep-nodes-g');
    if (!canvas) return;

    // Group nodes by layer order
    const byOrder = {};
    nodes.forEach(n => {
        const cfg = _DEP_CFG[n.layer] || { order: 4 };
        (byOrder[cfg.order] = byOrder[cfg.order] || []).push(n);
    });

    // Build positions — layered layout
    let curY = 30;
    const layerBands = []; // { y1, y2, order }

    Object.keys(byOrder).sort((a,b)=>+a-+b).forEach(order => {
        const layerNodes = byOrder[order];
        // Split into rows of _DEP_MR
        const rows = [];
        for (let i = 0; i < layerNodes.length; i += _DEP_MR) {
            rows.push(layerNodes.slice(i, i + _DEP_MR));
        }
        const maxCols = Math.max(...rows.map(r => r.length));
        const bandY1 = curY;

        rows.forEach((row, ri) => {
            const rowW   = row.length * (_DEP_NW + _DEP_HG) - _DEP_HG;
            const maxW   = maxCols * (_DEP_NW + _DEP_HG) - _DEP_HG;
            const startX = -maxW / 2 + (maxW - rowW) / 2;   // center this row

            row.forEach((n, ci) => {
                _depPos[n.name] = {
                    x: startX + ci * (_DEP_NW + _DEP_HG),
                    y: curY,
                    layer: n.layer,
                };
            });
            curY += _DEP_NH + (ri < rows.length - 1 ? _DEP_RG : 0);
        });

        layerBands.push({ y1: bandY1, y2: curY, order: +order });
        curY += _DEP_LG;
    });

    // Draw faint layer bands (background stripes)
    const allX = Object.values(_depPos).map(p => p.x);
    const bandMinX = Math.min(...allX) - 20;
    const bandMaxX = Math.max(...allX) + _DEP_NW + 20;

    layerBands.forEach(band => {
        // Find one node in this order to get layer name
        const repNode = byOrder[band.order]?.[0];
        if (!repNode) return;
        const cfg = _DEP_CFG[repNode.layer] || {};
        const rect = document.createElementNS(NS, 'rect');
        rect.setAttribute('x', bandMinX);
        rect.setAttribute('y', band.y1 - 8);
        rect.setAttribute('width', bandMaxX - bandMinX);
        rect.setAttribute('height', band.y2 - band.y1 + 16);
        rect.setAttribute('rx', '10');
        rect.setAttribute('fill', cfg.color || '#6B778C');
        rect.setAttribute('opacity', '0.08');
        bandsG.appendChild(rect);

        // Layer label on left
        const lbl = document.createElementNS(NS, 'text');
        lbl.setAttribute('x', bandMinX + 6);
        lbl.setAttribute('y', band.y1 + (band.y2 - band.y1) / 2 + 4);
        lbl.setAttribute('font-size', '10');
        lbl.setAttribute('font-family', 'system-ui,sans-serif');
        lbl.setAttribute('fill', cfg.color || '#64748b');
        lbl.setAttribute('font-weight', '600');
        lbl.setAttribute('opacity', '0.7');
        lbl.textContent = cfg.label || '';
        bandsG.appendChild(lbl);
    });

    // Draw edges (bezier curves)
    edges.forEach(e => {
        const fp = _depPos[e.from];
        const tp = _depPos[e.to];
        if (!fp || !tp) return;

        const x1 = fp.x + _DEP_NW / 2;
        const y1 = fp.y + _DEP_NH;
        const x2 = tp.x + _DEP_NW / 2;
        const y2 = tp.y;
        const cy = (y1 + y2) / 2;

        const path = document.createElementNS(NS, 'path');
        path.setAttribute('d', `M${x1},${y1} C${x1},${cy} ${x2},${cy} ${x2},${y2}`);
        path.setAttribute('fill', 'none');
        path.setAttribute('stroke', 'rgba(148,178,222,0.4)');
        path.setAttribute('stroke-width', '1.5');
        path.setAttribute('marker-end', 'url(#dep-arr)');
        path.setAttribute('opacity', '1');
        path.dataset.from = e.from;
        path.dataset.to   = e.to;
        edgesG.appendChild(path);
    });

    // Draw nodes
    nodes.forEach(n => {
        const pos = _depPos[n.name];
        if (!pos) return;
        const cfg = _DEP_CFG[n.layer] || { color:'#6B778C', bg:'#F4F5F7' };

        const g = document.createElementNS(NS, 'g');
        g.style.cursor = 'pointer';
        g.dataset.name = n.name;

        const rect = document.createElementNS(NS, 'rect');
        rect.setAttribute('x', pos.x);
        rect.setAttribute('y', pos.y);
        rect.setAttribute('width', _DEP_NW);
        rect.setAttribute('height', _DEP_NH);
        rect.setAttribute('rx', '7');
        rect.setAttribute('fill', '#FFFFFF');
        rect.setAttribute('stroke', cfg.color);
        rect.setAttribute('stroke-width', '1.5');
        rect.setAttribute('filter', 'url(#dep-shadow)');

        // Truncate display name: strip suffix, add ellipsis
        const suffixes = /Controller$|Service$|Repository$|Observer$|Policy$|Listener$/;
        const short = n.name.replace(suffixes, '') || n.name;
        const display = short.length > 13 ? short.substring(0, 12) + '…' : short;

        const text = document.createElementNS(NS, 'text');
        text.setAttribute('x', pos.x + _DEP_NW / 2);
        text.setAttribute('y', pos.y + _DEP_NH / 2 + 4);
        text.setAttribute('text-anchor', 'middle');
        text.setAttribute('font-size', '10.5');
        text.setAttribute('font-family', 'system-ui,sans-serif');
        text.setAttribute('font-weight', '600');
        text.setAttribute('fill', '#172B4D');
        text.textContent = display;

        const title = document.createElementNS(NS, 'title');
        title.textContent = n.name;

        g.appendChild(rect); g.appendChild(text); g.appendChild(title);

        g.addEventListener('click',       () => depNodeClick(n.name));
        g.addEventListener('mouseenter',  () => depHighlight(n.name));
        g.addEventListener('mouseleave',  () => { if (_depSel !== n.name) depClearHighlight(false); });

        nodesG.appendChild(g);
    });

    // Fit on first render
    depFit();

    // Zoom (scroll wheel)
    canvas.addEventListener('wheel', e => {
        e.preventDefault();
        const rect   = canvas.getBoundingClientRect();
        const mx     = e.clientX - rect.left;
        const my     = e.clientY - rect.top;
        const delta  = e.deltaY > 0 ? -0.1 : 0.1;
        const newS   = Math.max(0.12, Math.min(3, _depT.s + delta));
        _depT.tx    += (mx - _depT.tx) * (1 - newS / _depT.s);
        _depT.ty    += (my - _depT.ty) * (1 - newS / _depT.s);
        _depT.s      = newS;
        _depApplyT();
    }, { passive: false });

    // Pan (drag)
    canvas.addEventListener('mousedown', e => {
        if (e.target.closest('g[data-name]')) return;
        _depDrag = { sx: e.clientX - _depT.tx, sy: e.clientY - _depT.ty };
        canvas.style.cursor = 'grabbing';
    });
    window.addEventListener('mousemove', e => {
        if (!_depDrag) return;
        _depT.tx = e.clientX - _depDrag.sx;
        _depT.ty = e.clientY - _depDrag.sy;
        _depApplyT();
    });
    window.addEventListener('mouseup', () => {
        _depDrag = null;
        if (canvas) canvas.style.cursor = 'grab';
    });
    window.addEventListener('resize', depFit, { passive: true });
}

function _depApplyT() {
    const vp = document.getElementById('dep-vp');
    if (vp) vp.setAttribute('transform', `translate(${_depT.tx},${_depT.ty}) scale(${_depT.s})`);
}

function depFit() {
    const canvas = document.getElementById('dep-canvas');
    if (!canvas || !Object.keys(_depPos).length) return;

    const allX = Object.values(_depPos).map(p => p.x);
    const allY = Object.values(_depPos).map(p => p.y);
    const minX = Math.min(...allX), maxX = Math.max(...allX) + _DEP_NW;
    const minY = Math.min(...allY), maxY = Math.max(...allY) + _DEP_NH;
    const gW = maxX - minX, gH = maxY - minY;
    const par = canvas.parentElement;
    const cW = (par && par.clientWidth  > 0 ? par.clientWidth  : null) || canvas.getBoundingClientRect().width  || 900;
    const cH = (par && par.clientHeight > 0 ? par.clientHeight : null) || canvas.getBoundingClientRect().height || 600;
    const pad = 48;

    _depT.s  = Math.min((cW - pad*2) / gW, (cH - pad*2) / gH, 1.4);
    _depT.tx = cW/2 - _depT.s * (minX + gW/2);
    _depT.ty = cH/2 - _depT.s * (minY + gH/2);
    _depApplyT();
}

function depZoom(delta) {
    const canvas = document.getElementById('dep-canvas');
    const par = canvas?.parentElement;
    const cW = (par && par.clientWidth  > 0 ? par.clientWidth  : null) || canvas?.getBoundingClientRect().width  || 900;
    const cH = (par && par.clientHeight > 0 ? par.clientHeight : null) || canvas?.getBoundingClientRect().height || 600;
    const newS = Math.max(0.12, Math.min(3, _depT.s + delta));
    _depT.tx += (cW/2 - _depT.tx) * (1 - newS / _depT.s);
    _depT.ty += (cH/2 - _depT.ty) * (1 - newS / _depT.s);
    _depT.s   = newS;
    _depApplyT();
}

function depNodeClick(name) {
    if (_depSel === name) {
        _depSel = null;
        depClearHighlight();
        const lbl = document.getElementById('dep-sel-label');
        if (lbl) lbl.style.display = 'none';
    } else {
        _depSel = name;
        depHighlight(name);
        const lbl = document.getElementById('dep-sel-label');
        if (lbl) { lbl.textContent = name; lbl.style.display = 'block'; }
    }
}

function depHighlight(name) {
    const edges = (APP.dependencies || {}).edges || [];
    const connected = new Set([name]);
    edges.forEach(e => {
        if (e.from === name) connected.add(e.to);
        if (e.to   === name) connected.add(e.from);
    });

    document.querySelectorAll('#dep-edges-g path').forEach(p => {
        const on = p.dataset.from === name || p.dataset.to === name;
        p.setAttribute('stroke',       on ? '#FF2D20' : 'rgba(148,178,222,0.15)');
        p.setAttribute('stroke-width', on ? '2'       : '1.5');
        p.setAttribute('opacity',      on ? '1'       : '0.5');
        p.setAttribute('marker-end',   on ? 'url(#dep-arr-hi)' : 'url(#dep-arr)');
    });

    document.querySelectorAll('#dep-nodes-g g[data-name]').forEach(g => {
        g.style.opacity = connected.has(g.dataset.name) ? '1' : '0.18';
    });
}

function depClearHighlight(resetSel = true) {
    if (resetSel) _depSel = null;
    document.querySelectorAll('#dep-edges-g path').forEach(p => {
        p.setAttribute('stroke',       'rgba(148,178,222,0.4)');
        p.setAttribute('stroke-width', '1.5');
        p.setAttribute('opacity',      '1');
        p.setAttribute('marker-end',   'url(#dep-arr)');
    });
    document.querySelectorAll('#dep-nodes-g g[data-name]').forEach(g => {
        g.style.opacity = '1';
    });
    const lbl = document.getElementById('dep-sel-label');
    if (lbl && resetSel) lbl.style.display = 'none';
}

// ── AI Chat ───────────────────────────────────────────────────────────────────

const CHAT_ENDPOINT = '{{ route("laradar.ai.chat") }}';
let _chatBusy = false;

function chatSuggest(text) {
    document.getElementById('chat-input').value = text;
    chatPreviewContext(text);
    chatSend();
}

function chatSend() {
    const input = document.getElementById('chat-input');
    const msg   = input.value.trim();
    if (!msg || _chatBusy) return;

    _chatBusy = true;
    input.value = '';
    document.getElementById('chat-context-hint').textContent = '';
    document.getElementById('chat-empty').style.display = 'none';

    // Extract context
    const {data: ctx, labels} = chatExtractContext(msg);

    // Append user bubble
    chatAppendBubble('user', msg);

    // Append loading AI bubble
    const loadingId = 'chat-loading-' + Date.now();
    chatAppendBubble('ai', null, loadingId, labels);

    fetch(CHAT_ENDPOINT, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': AI_CSRF },
        body:    JSON.stringify({ message: msg, context: ctx }),
    })
    .then(r => r.json())
    .then(json => {
        if (json.error) throw new Error(json.error);
        chatReplaceBubble(loadingId, json.reply, labels);
    })
    .catch(err => {
        chatReplaceBubble(loadingId, '**Error:** ' + err.message, labels, true);
    })
    .finally(() => { _chatBusy = false; });
}

function chatPreviewContext(msg) {
    const {labels} = chatExtractContext(msg);
    const hint = document.getElementById('chat-context-hint');
    hint.textContent = labels.length ? 'Context: ' + labels.join(' · ') : '';
}

function chatExtractContext(question) {
    const q      = question.toLowerCase();
    const words  = (q.match(/\b\w{4,}\b/g) || []);
    const labels = [];
    const data   = {};

    data.project = APP.project;
    data.summary = APP.summary;

    // Fat/large controller queries
    if (/large|fat|big|most method|too many|which.*controller|longest/.test(q)) {
        const sorted = [...(APP.controllers || [])].sort((a, b) => (b.method_count || 0) - (a.method_count || 0));
        data.controllers_by_size = sorted.slice(0, 10);
        labels.push('Controllers sorted by size');
    }

    // Controller keyword match
    const ctrlHits = (APP.controllers || []).filter(c => words.some(w => c.name.toLowerCase().includes(w)));
    if (ctrlHits.length) { data.controllers = ctrlHits; labels.push(ctrlHits.map(c => c.name).join(', ')); }

    // Model keyword match
    const modelHits = (APP.models || []).filter(m => words.some(w => m.name.toLowerCase().includes(w)));
    if (modelHits.length) { data.models = modelHits; labels.push(modelHits.map(m => m.name).join(', ')); }

    // Route keyword match
    const routeHits = (APP.routes || []).filter(r =>
        words.some(w => (r.uri || '').toLowerCase().includes(w) || (r.controller?.class || '').toLowerCase().includes(w))
    );
    if (routeHits.length) { data.routes = routeHits.slice(0, 20); labels.push(routeHits.length + ' routes'); }

    // Service keyword match
    const svcHits = (APP.services || []).filter(s => words.some(w => (s.name || '').toLowerCase().includes(w)));
    if (svcHits.length) { data.services = svcHits; labels.push(svcHits.map(s => s.name).join(', ')); }

    // Score / SOLID / quality
    if (/score|solid|grade|quality|best practice|principle/.test(q)) {
        data.score = APP.score; labels.push('Architecture Score');
    }

    // Dependencies / coupling
    if (/depend|inject|coupl|graph|layer/.test(q)) {
        data.dependencies = APP.dependencies; labels.push('Dependency Graph');
    }

    // Modules
    if (/module/.test(q) && (APP.modules || []).length) {
        data.modules = APP.modules; labels.push('Modules');
    }

    // Jobs / Events
    if (/job|queue|dispatch/.test(q) && (APP.jobs || []).length) {
        data.jobs = APP.jobs; labels.push('Jobs');
    }
    if (/event|listener|broadcast/.test(q) && (APP.events || []).length) {
        data.events = APP.events; labels.push('Events');
    }

    // Fallback — send a compact summary
    if (labels.length === 0) {
        data.controllers = (APP.controllers || []).slice(0, 15);
        data.models      = (APP.models || []).slice(0, 15);
        data.score       = APP.score;
        labels.push('Full architecture summary');
    }

    return { data, labels };
}

function chatAppendBubble(role, text, loadingId = null, contextLabels = []) {
    const wrap = document.getElementById('chat-messages');
    const isAI = role === 'ai';
    const id   = loadingId || ('msg-' + Date.now());

    const ctxHtml = contextLabels.length && isAI
        ? `<p style="font-size:11px;color:var(--text-faint);margin-top:8px;padding-top:8px;border-top:1px solid var(--border);">Context used: ${contextLabels.map(_esc).join(' · ')}</p>`
        : '';

    const dot = (delay) => `<span style="width:6px;height:6px;border-radius:50%;background:#FF2D20;opacity:.5;display:inline-block;animation:chatBounce 1.2s ease-in-out ${delay} infinite;"></span>`;
    const bodyHtml = text === null
        ? `<span style="display:inline-flex;gap:4px;align-items:center;">${dot('0s')}${dot('.15s')}${dot('.3s')}</span>`
        : `<div style="font-size:13px;color:#334155;line-height:1.65;">${chatMarkdown(text)}</div>`;

    const rowStyle    = `display:flex;align-items:flex-end;gap:10px;${isAI ? 'justify-content:flex-start' : 'justify-content:flex-end'}`;
    const aiAvatar    = isAI  ? `<div style="width:28px;height:28px;border-radius:50%;background:rgba(255,45,32,.12);border:1px solid rgba(255,45,32,.25);display:flex;align-items:center;justify-content:center;color:#FF2D20;font-size:10px;font-weight:700;flex-shrink:0;margin-bottom:2px;">AI</div>` : '';
    const userAvatar  = !isAI ? `<div style="width:28px;height:28px;border-radius:50%;background:rgba(255,45,32,.12);border:1px solid rgba(255,45,32,.25);display:flex;align-items:center;justify-content:center;color:#FF2D20;font-size:10px;font-weight:700;flex-shrink:0;margin-bottom:2px;">You</div>` : '';
    const bubbleStyle = isAI
        ? 'background:var(--bg-elevated);border:1px solid var(--border);border-radius:16px;border-top-left-radius:4px;padding:12px 16px;max-width:80%;'
        : 'background:#FF2D20;color:#fff;border-radius:16px;border-top-right-radius:4px;padding:12px 16px;max-width:80%;';

    wrap.insertAdjacentHTML('beforeend', `
        <div style="${rowStyle};animation:chatBubbleIn .22s cubic-bezier(.22,1,.36,1) both;" id="${id}">
            ${aiAvatar}
            <div style="${bubbleStyle}" data-bubble="1">
                ${bodyHtml}
                ${ctxHtml}
            </div>
            ${userAvatar}
        </div>
    `);

    wrap.scrollTop = wrap.scrollHeight;
    return id;
}

function chatReplaceBubble(id, text, contextLabels = [], isError = false) {
    const el = document.getElementById(id);
    if (!el) return;
    const inner = el.querySelector('[data-bubble]');
    if (!inner) return;

    const ctxHtml = contextLabels.length
        ? `<p style="font-size:11px;color:var(--text-faint);margin-top:8px;padding-top:8px;border-top:1px solid var(--border);">Context used: ${contextLabels.map(_esc).join(' · ')}</p>`
        : '';

    if (isError) {
        inner.innerHTML = `<div style="font-size:13px;color:#dc2626;line-height:1.65;">${chatMarkdown(text)}</div>${ctxHtml}`;
        document.getElementById('chat-messages').scrollTop = 99999;
        return;
    }

    // Typewriter reveal: type plain text first, then swap to full rendered HTML
    const rendered = chatMarkdown(text);
    const plain    = rendered.replace(/<[^>]+>/g, '').replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>');

    const box = document.createElement('div');
    box.style.cssText = 'font-size:13px;color:#334155;line-height:1.65;';
    box.className = 'type-cursor';
    inner.innerHTML = '';
    inner.appendChild(box);

    const wrap  = document.getElementById('chat-messages');
    const speed = plain.length > 400 ? 6 : plain.length > 150 ? 12 : 18;
    const chunk = plain.length > 400 ? 5 : plain.length > 150 ? 3 : 1;
    let i = 0;

    const tick = () => {
        if (i >= plain.length) {
            inner.innerHTML = `<div style="font-size:13px;color:#334155;line-height:1.65;">${rendered}</div>${ctxHtml}`;
            wrap.scrollTop = wrap.scrollHeight;
            return;
        }
        i = Math.min(i + chunk, plain.length);
        box.textContent = plain.slice(0, i);
        wrap.scrollTop = wrap.scrollHeight;
        setTimeout(tick, speed);
    };
    tick();
}

function chatMarkdown(text) {
    // Escape HTML
    text = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

    // Normalize line endings
    text = text.replace(/\r\n/g, '\n');

    // Code blocks
    text = text.replace(/```(\w*)\n?([\s\S]*?)```/g, (_, lang, code) => {
        code = code.trim();
        // Reformat single-line flow diagrams: "A ↓ B ↓ C" → each step on its own line
        if (/\S[ \t]*↓[ \t]*\S/.test(code)) {
            code = code.split('↓').map(s => s.trim()).filter(Boolean).join('\n    ↓\n');
        }
        return `<pre style="background:#1e293b;color:#86efac;border-radius:8px;padding:12px;margin:8px 0;font-size:11px;white-space:pre-wrap;word-break:break-word;overflow-x:auto;font-family:monospace;"><code style="background:none;border:none;padding:0;color:inherit;">${code}</code></pre>`;
    });

    // Normalize inline tables — AI sometimes returns all rows on one line separated by "| |"
    // e.g. "| Col1 | Col2 | |---|---| | val1 | val2 |" → split each row onto its own line
    text = text.replace(/\|\s*\|/g, '|\n|');

    // Tables — process line by line
    const lines = text.split('\n');
    const out   = [];
    let inTable = false, headerDone = false, tableHtml = '';

    for (const line of lines) {
        const tr = line.trim();
        if (tr.startsWith('|')) {
            // Separator row (e.g. |---|---|)
            if (/^\|[\s\-\|:]+\|?\s*$/.test(tr)) {
                headerDone = true;
                continue;
            }
            const cells = tr.replace(/^\||\|$/g, '').split('|').map(c => c.trim());
            if (!inTable) {
                inTable    = true;
                headerDone = false;
                tableHtml  = '<div style="overflow-x:auto;margin:8px 0"><table style="width:100%;border-collapse:collapse;font-size:11px">';
            }
            if (!headerDone) {
                tableHtml += '<thead><tr>' + cells.map(c =>
                    `<th style="border:1px solid #e2e8f0;background:#f8fafc;padding:5px 10px;text-align:left;font-weight:700;color:#475569;white-space:nowrap">${c}</th>`
                ).join('') + '</tr></thead><tbody>';
                headerDone = true;
            } else {
                tableHtml += '<tr>' + cells.map(c =>
                    `<td style="border:1px solid #e2e8f0;padding:5px 10px;color:#334155;vertical-align:top">${c}</td>`
                ).join('') + '</tr>';
            }
        } else {
            if (inTable) {
                tableHtml += '</tbody></table></div>';
                out.push(tableHtml);
                tableHtml = ''; inTable = false; headerDone = false;
            }
            out.push(line);
        }
    }
    if (inTable) { tableHtml += '</tbody></table></div>'; out.push(tableHtml); }
    text = out.join('\n');

    // Inline formatting
    return text
        .replace(/`([^`]+)`/g, '<code style="background:#f1f5f9;color:#4f46e5;padding:1px 5px;border-radius:3px;font-size:11px;font-family:monospace;border:none;">$1</code>')
        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.+?)\*/g, '<em>$1</em>')
        .replace(/^### (.+)$/gm, '<p style="font-weight:600;color:#1e293b;margin:10px 0 3px;font-size:13px;">$1</p>')
        .replace(/^## (.+)$/gm,  '<p style="font-weight:700;color:#1e293b;margin:12px 0 4px;font-size:14px;">$1</p>')
        .replace(/^# (.+)$/gm,   '<p style="font-weight:700;color:#0f172a;margin:14px 0 6px;font-size:16px;">$1</p>')
        .replace(/^- (.+)$/gm,   '<li style="margin-left:16px;list-style-type:disc;margin-bottom:2px;">$1</li>')
        .replace(/\n\n/g, '<br>')
        .replace(/\n/g, ' ');
}

// ── AI Docs ────────────────────────────────────────────────────────────────────

const DOCS_ENDPOINT = '{{ route("laradar.ai.documentation") }}';
const _docsContent  = {};
const DOC_TYPES     = ['architecture','models','controllers','routes','services','modules'];


const _docAbortCtrls = {};
const _docPollTimers = {};

function cancelDocGenerate(type) {
    if (_docAbortCtrls[type]) { _docAbortCtrls[type].abort(); }
    if (_docPollTimers[type]) { clearInterval(_docPollTimers[type]); delete _docPollTimers[type]; }
    const btn       = document.getElementById('doc-gen-btn-' + type);
    const cancelBtn = document.getElementById('doc-cancel-btn-' + type);
    const status    = document.getElementById('doc-status-' + type);
    if (cancelBtn) cancelBtn.style.display = 'none';
    if (status)    { status.textContent = 'Pending'; status.style.color = 'var(--text-faint)'; }
    if (btn)       { btn.disabled = false; btn.innerHTML = `<svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg> Generate`; }
}

async function docsGenerate(type) {
    const btn       = document.getElementById('doc-gen-btn-' + type);
    const cancelBtn = document.getElementById('doc-cancel-btn-' + type);
    const status    = document.getElementById('doc-status-' + type);

    _docAbortCtrls[type] = new AbortController();
    const signal = _docAbortCtrls[type].signal;

    btn.disabled = true;
    btn.innerHTML = `<svg style="width:12px;height:12px;animation:spin 1s linear infinite;" fill="none" viewBox="0 0 24 24"><circle style="opacity:.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path style="opacity:.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Generating…`;
    if (cancelBtn) cancelBtn.style.display = 'inline-flex';
    status.textContent = 'Generating…';
    status.style.color = 'var(--cyan)';

    const docsFinish = (json) => {
        _docsContent[type] = { content: json.content, filename: json.filename };
        status.textContent = '✔ Ready';
        status.style.color = 'var(--emerald)';
        btn.innerHTML = `<svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Re-generate`;
        btn.disabled = false;
        if (cancelBtn) cancelBtn.style.display = 'none';
        const fmt = document.getElementById('doc-fmt-' + type)?.value || 'md';
        if (fmt === 'html') { docsDownloadHtml(type); } else { docsDownload(type); }
        document.getElementById('docs-download-all-btn').style.display = 'inline-flex';
    };

    try {
        const res  = await fetch(DOCS_ENDPOINT, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': AI_CSRF },
            body:    JSON.stringify({ type, report: APP }),
            signal,
        });
        const json = await res.json();
        if (!res.ok || json.error) throw new Error(json.error || 'Server error');

        if (json.job_id) {
            // Async mode — poll for result
            status.textContent = 'Queued…';
            _docPollTimers[type] = _aiPoll(json.job_id,
                (result) => { delete _docPollTimers[type]; docsFinish(result); },
                (err)    => {
                    delete _docPollTimers[type];
                    if (cancelBtn) cancelBtn.style.display = 'none';
                    status.textContent = '✘ Failed';
                    status.style.color = 'var(--rose)';
                    btn.innerHTML = `<svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Retry`;
                    btn.disabled = false;
                }
            );
        } else {
            docsFinish(json);
        }

    } catch (err) {
        if (cancelBtn) cancelBtn.style.display = 'none';
        if (err.name === 'AbortError') {
            status.textContent = 'Pending';
            status.style.color = 'var(--text-faint)';
            btn.innerHTML = `<svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg> Generate`;
        } else {
            status.textContent = '✘ Failed';
            status.style.color = 'var(--rose)';
            btn.innerHTML = `<svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Retry`;
        }
        btn.disabled = false;
    } finally {
        delete _docAbortCtrls[type];
    }
}

let _fmtInputId = null;
let _fmtBtn     = null;

function openFmtPanel(btn, inputId) {
    const panel  = document.getElementById('fmt-shared-panel');
    const isOpen = panel.style.display !== 'none' && _fmtInputId === inputId;

    // Close any open panel first
    panel.style.display = 'none';
    if (_fmtBtn) { const s = _fmtBtn.querySelector('svg'); if (s) s.style.transform = ''; }

    if (!isOpen) {
        _fmtInputId = inputId;
        _fmtBtn     = btn;

        const rect   = btn.getBoundingClientRect();
        const panelW = Math.max(rect.width, 88);
        const panelH = 82; // 2 options

        // Horizontal: left-align with button, clamp inside viewport
        let left = rect.left;
        if (left + panelW > window.innerWidth - 8) left = rect.right - panelW;
        if (left < 8) left = 8;

        // Vertical: open below; flip above if not enough room
        const top = (window.innerHeight - rect.bottom >= panelH + 8)
            ? rect.bottom + 4
            : rect.top - panelH - 4;

        panel.style.left    = left + 'px';
        panel.style.top     = top  + 'px';
        panel.style.width   = panelW + 'px';
        panel.style.display = 'block';

        const svg = btn.querySelector('svg');
        if (svg) svg.style.transform = 'rotate(180deg)';
    }
}

function selectFmtOption(value) {
    if (_fmtInputId) {
        const input = document.getElementById(_fmtInputId);
        if (input) input.value = value;
    }
    if (_fmtBtn) {
        const span = _fmtBtn.querySelector('span');
        if (span) span.textContent = '.' + value;
        const svg = _fmtBtn.querySelector('svg');
        if (svg) svg.style.transform = '';
    }
    document.getElementById('fmt-shared-panel').style.display = 'none';
    _fmtInputId = null;
    _fmtBtn     = null;
}

document.addEventListener('click', (e) => {
    if (!e.target.closest('[data-fmt-btn]') && !e.target.closest('#fmt-shared-panel')) {
        const panel = document.getElementById('fmt-shared-panel');
        if (panel) panel.style.display = 'none';
        if (_fmtBtn) { const s = _fmtBtn.querySelector('svg'); if (s) s.style.transform = ''; }
        _fmtInputId = null;
        _fmtBtn     = null;
    }
});

async function docsGenerateAll() {
    const globalFmt = document.getElementById('docs-fmt-all')?.value || 'md';
    for (const type of DOC_TYPES) {
        const fmtEl = document.getElementById('doc-fmt-' + type);
        if (fmtEl) fmtEl.value = globalFmt;
        const cardBtn = document.getElementById('doc-fmt-btn-' + type);
        if (cardBtn) { const s = cardBtn.querySelector('span'); if (s) s.textContent = '.' + globalFmt; }
        await docsGenerate(type);
    }
}

function _normalizeMdTables(md) {
    const lines  = md.replace(/\r\n/g, '\n').replace(/\r/g, '\n').split('\n');
    const result = [];
    let   block  = [];

    const parseCells = r => r.trim().replace(/^\||\|[ \t]*$/g, '').split('|').map(c => c.trim());
    const isSep      = r => /^\|?[\s|:=-]+\|?$/.test(r.trim());

    // Fit an arbitrary-length cells array into exactly colCount columns.
    // When there are MORE cells than columns, the extras are merged into column 1
    // (the "middle" column) so no data is lost — e.g. per-route columns get
    // joined with ", " into a single Routes cell.
    const fitCells = (cells, colCount) => {
        if (cells.length === colCount) return cells;
        if (cells.length < colCount)  return Array.from({ length: colCount }, (_, i) => cells[i] ?? '');

        // cells.length > colCount: merge the overflow into the second column
        const extraCount  = cells.length - colCount;
        const mergedSlice = cells.slice(1, 2 + extraCount).filter(Boolean).join(', ');
        const tail        = cells.slice(2 + extraCount); // remaining columns after the merge
        return [cells[0], mergedSlice, ...tail];
    };

    const flushBlock = () => {
        if (!block.length) return;
        const tableRows = block.filter(r => r.trim().startsWith('|'));
        if (!tableRows.length) { result.push(...block); block = []; return; }

        const dataRows = tableRows.filter(r => !isSep(r));
        if (dataRows.length < 1) { result.push(...block); block = []; return; }

        // Column count is authoritative from the header row (first data row)
        const colCount = parseCells(dataRows[0]).length;

        tableRows.forEach(row => {
            if (isSep(row)) {
                result.push('| ' + Array(colCount).fill('---').join(' | ') + ' |');
            } else {
                const normalised = fitCells(parseCells(row), colCount);
                result.push('| ' + normalised.join(' | ') + ' |');
            }
        });
        block = [];
    };

    for (const line of lines) {
        if (line.trim().startsWith('|')) {
            block.push(line);
        } else {
            flushBlock();
            result.push(line);
        }
    }
    flushBlock();
    return result.join('\n');
}

function docsDownload(type) {
    const doc = _docsContent[type];
    if (!doc) return;
    _downloadBlob(_normalizeMdTables(doc.content), doc.filename, 'text/markdown');
}

function docsDownloadAll() {
    const ready = DOC_TYPES.filter(t => _docsContent[t]);
    ready.forEach((type, i) => {
        setTimeout(() => docsDownload(type), i * 300);
    });
}

// ── AI Graphic Report ─────────────────────────────────────────────────────────

let _reportAbortCtrl = null;

function cancelAIReport() {
    if (_reportAbortCtrl) _reportAbortCtrl.abort();
}

async function generateAIGraphicReport() {
    const btn    = document.getElementById('ai-report-btn');
    const label  = document.getElementById('ai-report-btn-label');
    const panel  = document.getElementById('ai-report-progress');
    const errEl  = document.getElementById('ai-report-error');
    const spinner = document.getElementById('ai-report-spinner');
    const title  = document.getElementById('ai-report-progress-title');

    _reportAbortCtrl = new AbortController();
    const signal     = _reportAbortCtrl.signal;

    btn.disabled  = true;
    label.textContent = 'Generating…';
    panel.style.display = 'block';
    errEl.style.display = 'none';
    errEl.textContent = '';

    // Reset all step icons
    document.querySelectorAll('#ai-report-steps .step-icon').forEach(el => {
        el.style.cssText = 'width:14px;height:14px;border-radius:50%;border:2px solid rgba(148,178,222,0.4);flex-shrink:0;display:inline-block;background:none;';
        el.innerHTML = '';
    });

    const _stepDone  = (step) => {
        const el = document.querySelector(`[data-step="${step}"] .step-icon`);
        if (el) { el.style.cssText = 'width:14px;height:14px;border-radius:50%;flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;background:#34D399;border:none;'; el.innerHTML = '<svg viewBox="0 0 16 16" fill="none" width="10" height="10"><path stroke="white" stroke-width="2" stroke-linecap="round" fill="none" d="M3 8l3.5 3.5 6-7"/></svg>'; }
    };
    const _stepActive = (step) => {
        const el = document.querySelector(`[data-step="${step}"] .step-icon`);
        if (el) { el.style.cssText = 'width:14px;height:14px;border-radius:50%;border:2px solid #FF2D20;background:rgba(255,45,32,0.15);flex-shrink:0;display:inline-block;animation:pulse 1s infinite;'; }
    };
    const _stepFail  = (step) => {
        const el = document.querySelector(`[data-step="${step}"] .step-icon`);
        if (el) { el.style.cssText = 'width:14px;height:14px;border-radius:50%;flex-shrink:0;display:inline-block;background:#FBBF24;border:none;'; }
    };

    let aiAnalysis = null;
    const aiDocs   = {};

    try {
        // ── Steps 1–7: Single request generates everything ───────────────────
        ['analyze', ...DOC_TYPES].forEach(s => _stepActive(s));
        try {
            const res  = await fetch(AI_REPORT_ENDPOINT, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': AI_CSRF },
                signal,
            });
            const json = await res.json();
            if (!res.ok || json.error) throw new Error(json.error || 'AI report generation failed');

            let result = json;
            if (json.job_id) {
                const jobUrl = AI_JOB_ENDPOINT.replace('__ID__', json.job_id);
                let jobDone = false;
                for (let i = 0; i < 150; i++) {
                    await new Promise(r => setTimeout(r, 7000));
                    const pr = await fetch(jobUrl, { signal });
                    const pj = await pr.json();
                    if (pj.status === 'done') { result = pj.result; jobDone = true; break; }
                    if (pj.status === 'failed') throw new Error(pj.error || 'AI report job failed');
                }
                if (!jobDone) throw new Error('AI report timed out. The job took too long to complete.');
            }

            aiAnalysis = result.analysis ?? null;
            Object.assign(aiDocs, result.docs ?? {});
            ['analyze', ...DOC_TYPES].forEach(s => _stepDone(s));
        } catch(e) {
            if (e.name === 'AbortError') throw e;
            ['analyze', ...DOC_TYPES].forEach(s => _stepFail(s));
        }

        // ── Step 8: Build & download ────────────────────────────────────────
        _stepActive('build');
        const html = _buildAIGraphicReport(APP, aiAnalysis, aiDocs);
        _downloadBlob(html, 'ai-architecture-report.html', 'text/html;charset=utf-8');
        _stepDone('build');

        title.textContent = 'Report ready — downloading!';
        spinner.style.display = 'none';

    } catch(err) {
        if (err.name === 'AbortError') {
            panel.style.display = 'none';
        } else {
            errEl.textContent = 'Error: ' + err.message;
            errEl.style.display = 'block';
            title.textContent = 'Generation failed';
            spinner.style.display = 'none';
        }
    } finally {
        btn.disabled = false;
        label.textContent = 'Generate AI Graphic Report';
        _reportAbortCtrl = null;
    }
}

function _mdToHtml(md) {
    if (!md) return '';
    // Normalize line endings FIRST — AI responses may use \r\n (Windows) or \r (old Mac)
    const normalized = md.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
    let html = _esc(normalized);

    // ── Code blocks (before inline code) ────────────────────────────────────
    html = html.replace(/```[\w]*\n?([\s\S]*?)```/g, (_, code) =>
        `<pre style="background:#1e293b;color:#e2e8f0;border-radius:10px;padding:16px;overflow-x:auto;font-family:'JetBrains Mono',ui-monospace,monospace;font-size:12px;line-height:1.6;margin:12px 0">${code.trim()}</pre>`
    );
    // ── Inline code ──────────────────────────────────────────────────────────
    html = html.replace(/`([^`\n]+)`/g,
        '<code style="background:#f1f5f9;color:#0f172a;padding:2px 6px;border-radius:4px;font-family:\'JetBrains Mono\',ui-monospace,monospace;font-size:0.85em">$1</code>'
    );
    // ── Headings ─────────────────────────────────────────────────────────────
    html = html.replace(/^### (.+)$/gm, '<h3 style="font-size:16px;font-weight:700;color:#1e293b;margin:20px 0 8px">$1</h3>');
    html = html.replace(/^## (.+)$/gm,  '<h2 style="font-size:20px;font-weight:800;color:#0f172a;margin:28px 0 10px;padding-bottom:6px;border-bottom:2px solid #e2e8f0">$1</h2>');
    html = html.replace(/^# (.+)$/gm,   '<p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 20px;padding-bottom:10px;border-bottom:1px solid #f1f5f9">$1</p>');

    // ── Tables (line-by-line approach — immune to \r\n and trailing-pipe bugs) ─
    const tableStyle  = 'width:100%;border-collapse:collapse;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden';
    const thStyle     = 'background:#f8fafc;padding:9px 12px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#64748b;border-bottom:2px solid #e2e8f0;white-space:nowrap';
    const tdStyle     = 'padding:9px 12px;border-bottom:1px solid #f1f5f9;font-size:13px;color:#334155;vertical-align:top';
    const isSep       = r => /^\|?[\s|:=-]+\|?$/.test(r.trim());
    const parseCells  = r => r.trim().replace(/^\||\|[ \t]*$/g, '').split('|').map(c => c.trim());

    // Collect runs of lines that start with | and render each run as a table
    const lines = html.split('\n');
    const out   = [];
    let tableLines = [];

    const flushTable = () => {
        if (!tableLines.length) return;
        const dataRows = tableLines.filter(r => r.trim().startsWith('|') && !isSep(r));
        if (dataRows.length < 1) { out.push(...tableLines); tableLines = []; return; }
        const [hRow, ...bRows] = dataRows;
        const ths = parseCells(hRow).map(h => `<th style="${thStyle}">${h}</th>`).join('');
        const trs = bRows.map(r =>
            `<tr>${parseCells(r).map(c => `<td style="${tdStyle}">${c}</td>`).join('')}</tr>`
        ).join('');
        out.push(`<div style="overflow-x:auto;margin:12px 0"><table style="${tableStyle}"><thead><tr>${ths}</tr></thead><tbody>${trs}</tbody></table></div>`);
        tableLines = [];
    };

    for (const line of lines) {
        if (line.trim().startsWith('|')) {
            tableLines.push(line);
        } else {
            flushTable();
            out.push(line);
        }
    }
    flushTable();
    html = out.join('\n');

    // ── Bold / italic ────────────────────────────────────────────────────────
    html = html.replace(/\*\*([^*\n]+)\*\*/g, '<strong>$1</strong>');
    html = html.replace(/\*([^*\n]+)\*/g,     '<em>$1</em>');
    // ── Unordered lists ──────────────────────────────────────────────────────
    html = html.replace(/((?:^[ \t]*[-*] .+\n?)+)/gm, (block) => {
        const items = block.trim().split('\n')
            .map(l => `<li style="margin-bottom:4px">${l.replace(/^[ \t]*[-*] /, '')}</li>`).join('');
        return `<ul style="list-style:disc;padding-left:20px;margin:8px 0">${items}</ul>`;
    });
    // ── Ordered lists ────────────────────────────────────────────────────────
    html = html.replace(/((?:^\d+\. .+\n?)+)/gm, (block) => {
        const items = block.trim().split('\n')
            .map(l => `<li style="margin-bottom:4px">${l.replace(/^\d+\. /, '')}</li>`).join('');
        return `<ol style="list-style:decimal;padding-left:20px;margin:8px 0">${items}</ol>`;
    });
    // ── Horizontal rule ──────────────────────────────────────────────────────
    html = html.replace(/^---+$/gm, '<hr style="border:none;border-top:1px solid #e2e8f0;margin:20px 0"/>');
    // ── Paragraphs ───────────────────────────────────────────────────────────
    html = html.split(/\n{2,}/).map(block => {
        if (/^<(h[1-3]|ul|ol|pre|hr|div)/.test(block.trimStart())) return block;
        const trimmed = block.trim();
        return trimmed ? `<p style="margin:0 0 12px;color:#334155;line-height:1.7">${trimmed}</p>` : '';
    }).join('\n');

    return html;
}

function _buildAIGraphicReport(d, ai, docs) {
    const esc   = s => String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    const proj  = d.project?.name ?? 'Laravel App';
    const score = d.score?.score  ?? 0;
    const grade = d.score?.grade  ?? '';
    const s     = d.summary       ?? {};
    const rs    = d.route_summary ?? {};

    // ── Score gauge SVG ─────────────────────────────────────────────────────
    const R = 64, CX = 80, CY = 80, SW = 14;
    const circ   = 2 * Math.PI * R;
    const arc    = circ * 0.75;
    const offset = arc - (arc * score / 100);
    const gColor = score >= 80 ? '#10b981' : score >= 60 ? '#f59e0b' : '#ef4444';
    const gaugeSvg = `<svg width="160" height="160" viewBox="0 0 160 160">
        <circle cx="${CX}" cy="${CY}" r="${R}" fill="none" stroke="#f1f5f9" stroke-width="${SW}" stroke-linecap="round" stroke-dasharray="${arc} ${circ}" stroke-dashoffset="0" transform="rotate(135 ${CX} ${CY})"/>
        <circle cx="${CX}" cy="${CY}" r="${R}" fill="none" stroke="${gColor}" stroke-width="${SW}" stroke-linecap="round" stroke-dasharray="${arc} ${circ}" stroke-dashoffset="${offset}" transform="rotate(135 ${CX} ${CY})"/>
        <text x="${CX}" y="${CY - 8}" text-anchor="middle" font-size="28" font-weight="800" fill="#1e293b" font-family="system-ui,sans-serif">${score}</text>
        <text x="${CX}" y="${CY + 14}" text-anchor="middle" font-size="12" font-weight="600" fill="${gColor}" font-family="system-ui,sans-serif">${esc(grade)}</text>
        <text x="${CX}" y="${CY + 30}" text-anchor="middle" font-size="9" fill="#94a3b8" font-family="system-ui,sans-serif">/ 100</text>
    </svg>`;

    // ── Component stat cards ─────────────────────────────────────────────────
    const stats = [
        ['Models',       s.models??0],
        ['Controllers',  s.controllers??0],
        ['Routes',       s.routes??0],
        ['Services',     s.services??0],
        ['Repositories', s.repositories??0],
        ['Jobs',         s.jobs??0],
        ['Events',       s.events??0],
        ['Policies',     s.policies??0],
        ['API Routes',   rs.api??0],
        ['Named Routes', rs.named_count??0],
    ];
    const statCards = stats.map(([name, count]) =>
        `<div style="background:#fff;border:1px solid #fecaca;border-top:3px solid #FF2D20;border-radius:12px;padding:16px 18px">
            <div style="font-size:26px;font-weight:800;color:#FF2D20;font-family:system-ui,sans-serif">${count}</div>
            <div style="font-size:11px;color:#64748b;font-weight:500;margin-top:2px">${esc(name)}</div>
        </div>`
    ).join('');

    // ── Score checks ─────────────────────────────────────────────────────────
    const checkRows = (d.score?.checks ?? []).map(c => {
        const icon  = c.status === 'pass' ? '✔' : c.status === 'warn' ? '⚠' : '✘';
        const color = c.status === 'pass' ? '#10b981' : c.status === 'warn' ? '#f59e0b' : '#ef4444';
        return `<tr style="border-bottom:1px solid #f1f5f9">
            <td style="padding:9px 10px;font-size:15px;color:${color}">${icon}</td>
            <td style="padding:9px 10px;font-size:13px;font-weight:600;color:#1e293b">${esc(c.label)}</td>
            <td style="padding:9px 10px;font-size:12px;color:#64748b">${esc(c.note ?? '')}</td>
        </tr>`;
    }).join('');

    // ── AI problems ──────────────────────────────────────────────────────────
    const sevColor = { error:'#ef4444', warning:'#f59e0b', info:'#3b82f6' };
    const sevBg    = { error:'#fef2f2', warning:'#fffbeb', info:'#eff6ff' };
    const problemCards = (ai?.problems ?? []).map(p => {
        const col = sevColor[p.severity] ?? '#64748b';
        const bg  = sevBg[p.severity]   ?? '#f8fafc';
        return `<div style="background:${bg};border:1px solid ${col}30;border-left:4px solid ${col};border-radius:10px;padding:14px 16px;margin-bottom:10px">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
                <span style="font-size:11px;font-weight:700;color:${col};text-transform:uppercase;letter-spacing:0.06em;background:${col}20;padding:2px 8px;border-radius:20px">${esc(p.severity)}</span>
                <strong style="font-size:13px;color:#1e293b">${esc(p.title)}</strong>
            </div>
            <p style="font-size:13px;color:#475569;margin:0 0 6px;line-height:1.6">${esc(p.description)}</p>
            ${p.location ? `<code style="font-size:11px;color:#64748b;background:#f1f5f9;padding:2px 8px;border-radius:6px">${esc(p.location)}</code>` : ''}
        </div>`;
    }).join('') || '<p style="color:#94a3b8;font-size:13px">No problems detected.</p>';

    // ── AI suggestions ───────────────────────────────────────────────────────
    const priColor = { high:'#ef4444', medium:'#f59e0b', low:'#10b981' };
    const suggCards = (ai?.suggestions ?? []).map(p => {
        const col = priColor[p.priority] ?? '#64748b';
        return `<div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:14px 16px;margin-bottom:10px">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
                <span style="font-size:11px;font-weight:700;color:${col};background:${col}20;padding:2px 8px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em">${esc(p.priority)}</span>
                <strong style="font-size:13px;color:#1e293b">${esc(p.title)}</strong>
            </div>
            <p style="font-size:13px;color:#475569;margin:0 0 6px;line-height:1.6">${esc(p.description)}</p>
            ${p.example ? `<pre style="font-size:12px;color:#1e293b;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:10px;margin:6px 0 0;overflow-x:auto;font-family:ui-monospace,monospace">${esc(p.example)}</pre>` : ''}
        </div>`;
    }).join('') || '<p style="color:#94a3b8;font-size:13px">No suggestions available.</p>';

    // ── SOLID review ─────────────────────────────────────────────────────────
    const solidCards = Object.entries(ai?.solid_review ?? {}).map(([letter, data]) => {
        const col = data.status === 'pass' ? '#10b981' : data.status === 'warn' ? '#f59e0b' : '#ef4444';
        const bg  = data.status === 'pass' ? '#f0fdf4' : data.status === 'warn' ? '#fffbeb' : '#fef2f2';
        const fullName = { S:'Single Responsibility', O:'Open / Closed', L:'Liskov Substitution', I:'Interface Segregation', D:'Dependency Inversion' };
        return `<div style="background:${bg};border:1px solid ${col}30;border-radius:12px;padding:16px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
                <div style="width:36px;height:36px;border-radius:50%;background:${col};display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:900;color:#fff;flex-shrink:0">${esc(letter)}</div>
                <div>
                    <p style="font-size:11px;font-weight:700;color:${col};text-transform:uppercase;margin:0">${data.status}</p>
                    <p style="font-size:12px;font-weight:600;color:#1e293b;margin:0">${esc(fullName[letter] ?? letter)}</p>
                </div>
            </div>
            <p style="font-size:12px;color:#475569;margin:0;line-height:1.6">${esc(data.note ?? '')}</p>
        </div>`;
    }).join('');

    // ── Best practices ───────────────────────────────────────────────────────
    const bpItems = (ai?.laravel_best_practices ?? []).map(bp => {
        const icon  = bp.status === 'pass' ? '✔' : bp.status === 'warn' ? '⚠' : '✘';
        const color = bp.status === 'pass' ? '#10b981' : bp.status === 'warn' ? '#f59e0b' : '#ef4444';
        return `<div style="display:flex;gap:10px;padding:10px 0;border-bottom:1px solid #f1f5f9">
            <span style="font-size:14px;color:${color};flex-shrink:0;width:18px">${icon}</span>
            <div>
                <p style="font-size:13px;font-weight:600;color:#1e293b;margin:0 0 2px">${esc(bp.name)}</p>
                <p style="font-size:12px;color:#64748b;margin:0">${esc(bp.note)}</p>
            </div>
        </div>`;
    }).join('');

    // ── Dependency graph ─────────────────────────────────────────────────────
    const depSvg = _buildDepSvg(d.dependencies?.nodes ?? [], d.dependencies?.edges ?? []);

    // ── Section helper ───────────────────────────────────────────────────────
    const sec = (title, _color, content) =>
        `<section style="margin-bottom:52px">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:22px">
                <div style="width:4px;height:34px;border-radius:2px;background:#FF2D20;flex-shrink:0"></div>
                <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0">${esc(title)}</h2>
            </div>
            ${content}
        </section>`;

    const docSection = (type, label, color) => {
        if (!docs[type]) return '';
        return sec(label, color,
            `<div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:32px">${_mdToHtml(docs[type])}</div>`
        );
    };

    // ── Full HTML ─────────────────────────────────────────────────────────────
    return `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>AI Architecture Report — ${esc(proj)}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}
body {
  background: #f8fafc;
  font-family: 'Figtree', system-ui, -apple-system, sans-serif;
  color: #1e293b;
  line-height: 1.5;
}
code, pre {
  font-family: 'JetBrains Mono', ui-monospace, monospace;
}
@media print {
  body { background: #fff; }
  .no-print { display: none !important; }
  section { page-break-inside: avoid; }
}
</style>
</head>
<body>

<!-- HEADER -->
<div style="background:#FF2D20;padding:48px;display:flex;align-items:center;justify-content:space-between;gap:24px;flex-wrap:wrap">
    <div>
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,0.2);border:1px solid rgba(255,255,255,0.4);border-radius:20px;padding:4px 14px;margin-bottom:16px">
            <span style="font-size:11px;color:#fff;font-weight:700;letter-spacing:0.1em">AI-POWERED ARCHITECTURE REPORT</span>
        </div>
        <h1 style="font-size:36px;font-weight:900;color:#fff;margin-bottom:10px">${esc(proj)}</h1>
        <div style="display:flex;gap:20px;flex-wrap:wrap">
            <span style="font-size:13px;color:rgba(255,255,255,0.78)">Laravel ${esc(d.laravel_version ?? '')}</span>
            <span style="font-size:13px;color:rgba(255,255,255,0.78)">PHP ${esc(d.php_version ?? '')}</span>
            <span style="font-size:13px;color:rgba(255,255,255,0.78)">Generated ${esc(d.generated_at ?? '')}</span>
            <span style="font-size:13px;color:rgba(255,255,255,0.78)">Provider: ${esc(ai?.provider ?? d.ai_provider ?? 'AI')}</span>
        </div>
    </div>
    <div style="text-align:center;background:rgba(0,0,0,0.18);border:1px solid rgba(255,255,255,0.3);border-radius:20px;padding:24px 32px;flex-shrink:0">
        <div style="font-size:52px;font-weight:900;line-height:1;color:#fff">${score}</div>
        <div style="font-size:15px;font-weight:700;color:#fff;margin-top:6px">${esc(grade)}</div>
        <div style="font-size:11px;color:rgba(255,255,255,0.6);margin-top:4px">Architecture Score</div>
    </div>
</div>

<!-- PRINT BUTTON -->
<div class="no-print" style="background:#fff;border-bottom:1px solid #e2e8f0;padding:10px 48px;display:flex;justify-content:flex-end;">
    <button onclick="window.print()" style="display:inline-flex;align-items:center;gap:7px;background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:7px 16px;font-size:12px;font-weight:600;color:#475569;cursor:pointer;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9V2h12v7"/><rect x="6" y="17" width="12" height="5" rx="1"/><path d="M6 13H4a2 2 0 0 0-2 2v4h4"/><path d="M18 13h2a2 2 0 0 1 2 2v4h-4"/></svg>
        Print / Save PDF
    </button>
</div>

<!-- BODY -->
<div style="max-width:1200px;margin:0 auto;padding:48px 32px">

    <!-- AI Summary (inside centered container) -->
    ${ai?.summary ? `<div style="background:#fff8f8;border:1px solid #fecaca;border-left:4px solid #FF2D20;border-radius:12px;padding:20px 24px;margin-bottom:44px">
        <p style="font-size:11px;font-weight:700;color:#FF2D20;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:8px">AI Executive Summary</p>
        <p style="font-size:14px;color:#1e293b;line-height:1.75;margin:0">${esc(ai.summary)}</p>
    </div>` : ''}

    <!-- Stats Grid -->
    ${sec('Component Overview', '#FF2D20',
        `<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px">${statCards}</div>`
    )}

    <!-- Score -->
    ${sec('Architecture Score', '#10b981',
        `<div style="display:grid;grid-template-columns:160px 1fr;gap:24px;background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:24px;align-items:start">
            ${gaugeSvg}
            <table style="width:100%;border-collapse:collapse;font-family:system-ui,sans-serif"><tbody>${checkRows}</tbody></table>
        </div>`
    )}

    <!-- SOLID Review -->
    ${solidCards ? sec('SOLID Principles', '#FF2D20',
        `<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px">${solidCards}</div>`
    ) : ''}

    <!-- Best Practices -->
    ${bpItems ? sec('Laravel Best Practices', '#10b981',
        `<div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:8px 20px">${bpItems}</div>`
    ) : ''}

    <!-- Problems -->
    ${sec('Issues Detected', '#ef4444',
        `<div>${problemCards}</div>`
    )}

    <!-- Suggestions -->
    ${sec('AI Suggestions', '#f59e0b',
        `<div>${suggCards}</div>`
    )}

    <!-- Dependency Graph -->
    ${depSvg ? sec('Dependency Graph', '#FF2D20',
        `<div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden"><div style="overflow-x:auto;padding:20px">${depSvg}</div></div>`
    ) : ''}

    <!-- AI Documentation sections -->
    ${docSection('architecture', 'Architecture Overview',      '#FF2D20')}
    ${docSection('models',       'Models Documentation',       '#FF2D20')}
    ${docSection('controllers',  'Controllers Documentation',  '#FF2D20')}
    ${docSection('routes',       'Routes Documentation',       '#FF2D20')}
    ${docSection('services',     'Services Documentation',     '#FF2D20')}
    ${docSection('modules',      'Modules Documentation',      '#FF2D20')}

</div>

<!-- FOOTER -->
<div style="background:#0f172a;border-top:3px solid #FF2D20;color:#64748b;text-align:center;padding:28px;font-size:12px;">
    AI Architecture Report &nbsp;·&nbsp; Generated by <strong style="color:#FF2D20">Laradar</strong> &nbsp;·&nbsp; ${esc(d.generated_at ?? '')}
</div>

</body>
</html>`;
}

// ── Export helpers ────────────────────────────────────────────────────────────

function exportJson() {
    _downloadBlob(
        JSON.stringify(APP, null, 2),
        'architecture.json',
        'application/json'
    );
}

function copyPkgKey(btn, key) {
    const cmd = 'composer require ' + key;
    navigator.clipboard.writeText(cmd).then(() => {
        const origHTML = btn.innerHTML;
        btn.innerHTML = '<svg style="width:13px;height:13px;color:#34D399;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        setTimeout(() => { btn.innerHTML = origHTML; }, 1800);
    }).catch(() => {});
}

function copyJson() {
    const btn  = document.getElementById('copy-json-btn');
    const text = JSON.stringify(APP, null, 2);
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        setTimeout(() => { btn.innerHTML = orig; }, 1800);
    }).catch(() => {
        btn.title = 'Copy failed — try JSON download instead';
    });
}

function exportMarkdown() {
    const d   = APP;
    const s   = d.summary || {};
    const rs  = d.route_summary || {};
    const sc  = d.score || {};
    const out = [];

    out.push('# Architecture Report — ' + (d.project?.name || 'Laravel Application'));
    out.push('');
    out.push('> Generated: ' + d.generated_at);
    out.push('> Laravel ' + d.laravel_version + ' · PHP ' + d.php_version + ' · laradar v' + d.package_version);
    out.push('');
    out.push('---');
    out.push('');

    if (sc.score !== undefined) {
        out.push('## Architecture Score');
        out.push('');
        out.push('**' + sc.score + ' / ' + sc.max + '** — ' + sc.grade);
        out.push('');
        (sc.checks || []).forEach(c => {
            const icon = c.status === 'pass' ? '✔' : c.status === 'warn' ? '⚠' : '✘';
            out.push(icon + ' ' + c.label + (c.note ? ' — *' + c.note + '*' : ''));
        });
        out.push('');
        out.push('---');
        out.push('');
    }

    out.push('## Summary');
    out.push('');
    out.push('| Component | Count |');
    out.push('|-----------|------:|');
    const rows = [
        ['Models', s.models], ['Controllers', s.controllers], ['Routes', s.routes],
        ['Jobs',         $summary['jobs']??0],
        ['Jobs', s.jobs], ['Events', s.events], ['Services', s.services],
        ['Repositories', s.repositories], ['Observers', s.observers],
        ['Policies', s.policies], ['Modules', s.modules], ['Packages', s.packages],
    ];
    rows.forEach(([label, count]) => { if (count) out.push('| ' + label + ' | ' + count + ' |'); });
    out.push('');

    if ((d.dependencies?.edges || []).length > 0) {
        out.push('---');
        out.push('');
        out.push('## Dependency Graph');
        out.push('');
        out.push('```mermaid');
        out.push('flowchart TD');
        const nodes = d.dependencies.nodes || [];
        const edges = d.dependencies.edges || [];
        const byLayer = {};
        nodes.forEach(n => { const l = n.layer || 'model'; (byLayer[l] = byLayer[l] || []).push(n.name); });
        ['controller','job','event','listener','service','repository','model'].forEach(layer => {
            if (!(byLayer[layer] || []).length) return;
            out.push('    subgraph ' + layer.charAt(0).toUpperCase() + layer.slice(1) + 's');
            byLayer[layer].forEach(nm => out.push('        ' + nm));
            out.push('    end');
        });
        edges.forEach(e => out.push('    ' + e.from + ' --> ' + e.to));
        out.push('```');
        out.push('');
    }

    out.push('---');
    out.push('');
    out.push('## Models');
    out.push('');
    (d.models || []).forEach(m => {
        out.push('### ' + m.name);
        out.push('');
        out.push('**Table:** `' + m.table + '`');
        if ((m.fillable || []).length) out.push('**Fillable:** `' + m.fillable.join('`, `') + '`');
        if ((m.relationships || []).length) {
            out.push('');
            out.push('| Method | Type | Related |');
            out.push('|--------|------|---------|');
            m.relationships.forEach(r => out.push('| `' + r.method + '` | `' + r.type + '` | `' + (r.related || '').split('\\').pop() + '` |'));
        }
        out.push('');
    });

    out.push('---');
    out.push('');
    out.push('## Routes');
    out.push('');
    out.push('| Method | URI | Controller | Name |');
    out.push('|--------|-----|------------|------|');
    (d.routes || []).forEach(r => {
        const methods = (r.methods || []).filter(m => m !== 'HEAD').join(',');
        const ctrl    = (r.controller?.class || '').split('\\').pop() || '—';
        const name    = r.name || '—';
        out.push('| ' + methods + ' | `' + r.uri + '` | ' + ctrl + ' | ' + name + ' |');
    });

    _downloadBlob(out.join('\n'), 'architecture.md', 'text/markdown');
}

// ── Graphic Report ────────────────────────────────────────────────────────────

// Module-level guard — true while a build+download is in progress.
// Any number of clicks during that window are silently dropped.
let _exportingHTML = false;

function exportGraphicHTML() {
    if (_exportingHTML) return;   // hard guard — drop every extra click
    _exportingHTML = true;

    const btn     = document.getElementById('graphic-report-btn');
    const label   = document.getElementById('graphic-report-label');
    const icon    = document.getElementById('graphic-report-icon');
    const spinner = document.getElementById('graphic-report-spinner');

    // Show busy state immediately
    btn.disabled        = true;
    btn.style.opacity   = '0.72';
    btn.style.cursor    = 'not-allowed';
    label.textContent   = 'Building…';
    icon.style.display  = 'none';
    spinner.style.display = '';

    // Defer the heavy work by one paint so the browser renders the loading state
    // before the main thread is blocked by report generation
    setTimeout(() => {
        try {
            const html = _buildGraphicReport(APP);
            const url  = URL.createObjectURL(new Blob([html], { type: 'text/html;charset=utf-8' }));
            const a    = document.createElement('a');
            a.href     = url;
            a.download = 'architecture-report.html';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            setTimeout(() => URL.revokeObjectURL(url), 3000);

            // Brief success confirmation before restoring button
            label.textContent     = 'Downloaded!';
            spinner.style.display = 'none';
            icon.innerHTML        = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>';
            icon.style.display    = '';
            setTimeout(() => _exportGraphicHTMLReset(btn, label, icon, spinner), 2000);

        } catch(e) {
            spinner.style.display = 'none';
            icon.innerHTML        = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
            icon.style.display    = '';
            label.textContent     = 'Failed — try again';
            btn.style.opacity     = '1';
            btn.style.cursor      = 'pointer';
            // Re-enable after showing error so user can retry
            setTimeout(() => _exportGraphicHTMLReset(btn, label, icon, spinner), 2500);
        }
    }, 50);
}

function _exportGraphicHTMLReset(btn, label, icon, spinner) {
    _exportingHTML        = false;
    btn.disabled          = false;
    btn.style.opacity     = '';
    btn.style.cursor      = '';
    spinner.style.display = 'none';
    icon.style.display    = '';
    icon.innerHTML        = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>';
    label.textContent     = 'Generate & Download';
}

function _buildGraphicReport(d) {
    const esc   = s => String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    const proj  = d.project?.name  ?? 'Laravel App';
    const score = d.score?.score   ?? 0;
    const grade = d.score?.grade   ?? '';
    const s     = d.summary        ?? {};
    const rs    = d.route_summary  ?? {};

    // ── Score gauge SVG ──────────────────────────────────────────────────────
    const R = 64, CX = 80, CY = 80, SW = 14;
    const circ     = 2 * Math.PI * R;
    const arc      = circ * 0.75;           // 270° sweep
    const offset   = arc - (arc * score / 100);
    const gColor   = score >= 80 ? '#10b981' : score >= 60 ? '#f59e0b' : '#ef4444';
    const gaugeSvg = `<svg width="160" height="160" viewBox="0 0 160 160">
        <circle cx="${CX}" cy="${CY}" r="${R}" fill="none" stroke="#f1f5f9" stroke-width="${SW}" stroke-linecap="round"
            stroke-dasharray="${arc} ${circ}" stroke-dashoffset="0" transform="rotate(135 ${CX} ${CY})"/>
        <circle cx="${CX}" cy="${CY}" r="${R}" fill="none" stroke="${gColor}" stroke-width="${SW}" stroke-linecap="round"
            stroke-dasharray="${arc} ${circ}" stroke-dashoffset="${offset}" transform="rotate(135 ${CX} ${CY})"/>
        <text x="${CX}" y="${CY - 8}" text-anchor="middle" font-size="28" font-weight="800" fill="#1e293b" font-family="system-ui,sans-serif">${score}</text>
        <text x="${CX}" y="${CY + 14}" text-anchor="middle" font-size="12" font-weight="600" fill="${gColor}" font-family="system-ui,sans-serif">${esc(grade)}</text>
        <text x="${CX}" y="${CY + 30}" text-anchor="middle" font-size="9" fill="#94a3b8" font-family="system-ui,sans-serif">/ 100</text>
    </svg>`;

    // ── Route method bars ────────────────────────────────────────────────────
    const methodColors = { GET:'#10b981', POST:'#3b82f6', PUT:'#f59e0b', PATCH:'#f97316', DELETE:'#ef4444' };
    const byMethod     = rs.by_method ?? {};
    const maxMCount    = Math.max(1, ...Object.values(byMethod));
    const routeBarsSvg = `<svg width="260" height="${Math.max(40, Object.keys(byMethod).length * 38 + 10)}" font-family="system-ui,sans-serif">
        ${Object.entries(byMethod).map(([m, cnt], i) => {
            const bw  = Math.max(4, Math.round((cnt / maxMCount) * 180));
            const col = methodColors[m] ?? '#64748b';
            const y   = i * 38 + 6;
            return `<rect x="0" y="${y}" width="${bw}" height="22" rx="6" fill="${col}" opacity="0.85"/>
                    <text x="${bw + 8}" y="${y + 15}" font-size="12" font-weight="700" fill="${col}">${cnt}</text>
                    <text x="${bw + 34}" y="${y + 15}" font-size="11" fill="#64748b">${esc(m)}</text>`;
        }).join('')}
    </svg>`;

    // ── Dependency graph SVG ─────────────────────────────────────────────────
    const depNodes = d.dependencies?.nodes ?? [];
    const depEdges = d.dependencies?.edges ?? [];
    const depSvg   = _buildDepSvg(depNodes, depEdges);

    // ── Stat cards HTML ──────────────────────────────────────────────────────
    const stats = [
        ['Models',       s.models       ?? 0],
        ['Controllers',  s.controllers  ?? 0],
        ['Routes',       s.routes       ?? 0],
        ['Services',     s.services     ?? 0],
        ['Repositories', s.repositories ?? 0],
        ['Jobs',         s.jobs         ?? 0],
        ['Events',       s.events       ?? 0],
        ['Observers',    s.observers    ?? 0],
        ['Policies',     s.policies     ?? 0],
        ['Modules',      s.modules      ?? 0],
        ['API Routes',   rs.api         ?? 0],
        ['Named Routes', rs.named_count ?? 0],
    ];
    const statCards = stats.map(([name, count]) =>
        `<div style="background:#fff;border:1px solid #fecaca;border-top:3px solid #FF2D20;border-radius:12px;padding:16px 20px;display:flex;flex-direction:column;gap:4px">
            <span style="font-size:24px;font-weight:800;color:#FF2D20;font-family:system-ui,sans-serif">${count}</span>
            <span style="font-size:12px;color:#64748b;font-family:system-ui,sans-serif;font-weight:500">${esc(name)}</span>
        </div>`
    ).join('');

    // ── Score checks ─────────────────────────────────────────────────────────
    const checkRows = (d.score?.checks ?? []).map(c => {
        const icon  = c.status === 'pass' ? '✔' : c.status === 'warn' ? '⚠' : '✘';
        const color = c.status === 'pass' ? '#10b981' : c.status === 'warn' ? '#f59e0b' : '#ef4444';
        return `<tr>
            <td style="padding:8px 12px;font-size:13px;color:${color};font-weight:700">${icon}</td>
            <td style="padding:8px 12px;font-size:13px;color:#1e293b;font-weight:500">${esc(c.label)}</td>
            <td style="padding:8px 12px;font-size:12px;color:#64748b">${esc(c.note ?? '')}</td>
        </tr>`;
    }).join('');

    // ── Models table ─────────────────────────────────────────────────────────
    const modelRows = (d.models ?? []).slice(0, 40).map(m => {
        const rels = (m.relationships ?? []).map(r => r.type + ':' + (r.related ?? '').split('\\').pop()).join(', ');
        const fill = (m.fillable ?? []).slice(0, 5).join(', ') + ((m.fillable ?? []).length > 5 ? ' …' : '');
        return `<tr>
            <td style="padding:9px 12px;font-weight:700;color:#1e293b;font-family:ui-monospace,monospace;font-size:13px">${esc(m.name)}</td>
            <td style="padding:9px 12px;color:#64748b;font-family:ui-monospace,monospace;font-size:12px">${esc(m.table ?? '')}</td>
            <td style="padding:9px 12px;color:#64748b;font-size:12px">${esc(fill)}</td>
            <td style="padding:9px 12px;color:#8b5cf6;font-size:12px">${esc(rels)}</td>
        </tr>`;
    }).join('');

    // ── Controllers table ────────────────────────────────────────────────────
    const ctrlRows = (d.controllers ?? []).slice(0, 30).map(c => {
        const barW = Math.min(120, Math.round((c.method_count ?? 0) * 8));
        const barColor = (c.method_count ?? 0) > 15 ? '#ef4444' : (c.method_count ?? 0) > 10 ? '#f59e0b' : '#10b981';
        return `<tr>
            <td style="padding:9px 12px;font-weight:700;color:#1e293b;font-family:ui-monospace,monospace;font-size:13px">${esc(c.name)}</td>
            <td style="padding:9px 12px">
                <div style="display:flex;align-items:center;gap:8px">
                    <div style="width:${barW}px;height:8px;background:${barColor};border-radius:4px;opacity:0.8"></div>
                    <span style="font-size:12px;font-weight:700;color:${barColor}">${c.method_count ?? 0}</span>
                </div>
            </td>
            <td style="padding:9px 12px;font-size:12px;color:#64748b;max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${esc((c.methods ?? []).slice(0,8).join(', '))}</td>
        </tr>`;
    }).join('');

    // ── Routes table ─────────────────────────────────────────────────────────
    const routeRows = (d.routes ?? []).slice(0, 50).map(r => {
        const methods = (r.methods ?? []).filter(m => m !== 'HEAD').join('|');
        const ctrl    = (r.controller?.class ?? '—').split('\\').pop();
        const action  = r.controller?.method ?? '—';
        const mwShort = (r.middleware ?? []).map(m => m.split('\\').pop()).slice(0, 2).join(', ');
        const mCol    = methodColors[methods] ?? '#64748b';
        return `<tr>
            <td style="padding:8px 12px"><span style="font-size:11px;font-weight:700;color:${mCol};background:${mCol}18;padding:2px 8px;border-radius:6px;font-family:ui-monospace,monospace">${esc(methods)}</span></td>
            <td style="padding:8px 12px;font-family:ui-monospace,monospace;font-size:12px;color:#1e293b;font-weight:600">${esc(r.uri)}</td>
            <td style="padding:8px 12px;font-family:ui-monospace,monospace;font-size:11px;color:#64748b">${esc(ctrl)}@${esc(action)}</td>
            <td style="padding:8px 12px;font-size:11px;color:#94a3b8">${esc(mwShort)}</td>
        </tr>`;
    }).join('');

    // ── Section header helper ─────────────────────────────────────────────────
    const secHeader = (title, sub, color = '#FF2D20') =>
        `<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
            <div style="width:4px;height:32px;border-radius:2px;background:${color};flex-shrink:0"></div>
            <div>
                <h2 style="margin:0;font-size:20px;font-weight:800;color:#0f172a;font-family:system-ui,sans-serif">${esc(title)}</h2>
                ${sub ? `<p style="margin:2px 0 0;font-size:13px;color:#64748b;font-family:system-ui,sans-serif">${esc(sub)}</p>` : ''}
            </div>
        </div>`;

    const tableWrap = (headers, rows) =>
        `<div style="border-radius:12px;overflow:hidden;border:1px solid #e2e8f0;background:#ffffff">
            <table style="width:100%;border-collapse:collapse;font-family:system-ui,sans-serif">
                <thead><tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0">
                    ${headers.map(h => `<th style="padding:10px 12px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.06em">${esc(h)}</th>`).join('')}
                </tr></thead>
                <tbody style="divide-y:1px solid #f1f5f9">${rows}</tbody>
            </table>
        </div>`;

    // ── Assemble full HTML ────────────────────────────────────────────────────
    return `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Architecture Report — ${esc(proj)}</title>
<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}
body {
  background: #f8fafc;
  font-family:
    system-ui,
    -apple-system,
    sans-serif;
  color: #1e293b;
  line-height: 1.5;
}
a {
  color: inherit;
  text-decoration: none;
}
table tr:nth-child(even) {
  background: #fafbfc;
}
table tr:hover {
  background: #f1f5f9;
}
@media print {
  body {
    background: #fff;
  }
  .no-print {
    display: none;
  }
}
</style>
</head>
<body>

<!-- ═══ HEADER ═══════════════════════════════════════════════════════════ -->
<div style="background:#FF2D20;color:#fff;padding:40px 48px;display:flex;align-items:center;justify-content:space-between;gap:20px">
    <div>
        <p style="font-size:12px;text-transform:uppercase;letter-spacing:0.12em;color:rgba(255,255,255,0.75);margin-bottom:6px">Architecture Report</p>
        <h1 style="font-size:32px;font-weight:800;margin-bottom:8px">${esc(proj)}</h1>
        <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:8px">
            <span style="font-size:13px;color:rgba(255,255,255,0.78)">Laravel ${esc(d.laravel_version ?? '')}</span>
            <span style="font-size:13px;color:rgba(255,255,255,0.78)">PHP ${esc(d.php_version ?? '')}</span>
            <span style="font-size:13px;color:rgba(255,255,255,0.78)">Generated: ${esc(d.generated_at ?? '')}</span>
        </div>
    </div>
    <div style="text-align:center;background:rgba(0,0,0,0.18);border:1px solid rgba(255,255,255,0.3);border-radius:20px;padding:20px 28px;flex-shrink:0">
        <div style="font-size:48px;font-weight:900;line-height:1;color:#fff">${score}</div>
        <div style="font-size:14px;font-weight:700;color:#fff;margin-top:4px">${esc(grade)}</div>
        <div style="font-size:11px;color:rgba(255,255,255,0.6);margin-top:2px">Architecture Score</div>
    </div>
</div>

<!-- ═══ BODY ═════════════════════════════════════════════════════════════ -->
<div style="max-width:1200px;margin:0 auto;padding:40px 32px;display:flex;flex-direction:column;gap:48px">

    <!-- Stat Cards -->
    <section>
        ${secHeader('Component Overview', `${(d.models??[]).length} models · ${(d.controllers??[]).length} controllers · ${rs.total??0} routes`)}
        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:10px">
            ${statCards}
        </div>
    </section>

    <!-- Score -->
    <section>
        ${secHeader('Architecture Score', `${score}/100 — ${esc(grade)}`, '#10b981')}
        <div style="display:grid;grid-template-columns:160px 1fr;gap:24px;align-items:start;background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:24px">
            ${gaugeSvg}
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse;font-family:system-ui,sans-serif">
                    <tbody>${checkRows || '<tr><td colspan="3" style="padding:12px;color:#94a3b8;font-size:13px">No score checks available.</td></tr>'}</tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Routes -->
    <section>
        ${secHeader('Routes', `${rs.total??0} total · ${rs.web??0} web · ${rs.api??0} API · ${rs.named_count??0} named`, '#10b981')}
        <div style="display:grid;grid-template-columns:280px 1fr;gap:24px;align-items:start">
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:20px">
                <p style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px">By Method</p>
                ${routeBarsSvg}
            </div>
            <div style="overflow-x:auto">
                ${tableWrap(['Method','URI','Handler','Middleware'], routeRows || '<tr><td colspan="4" style="padding:12px;color:#94a3b8">No routes.</td></tr>')}
            </div>
        </div>
    </section>

    <!-- Dependency Graph -->
    ${depSvg ? `<section>
        ${secHeader('Dependency Graph', `${depNodes.length} nodes · ${depEdges.length} edges`, '#FF2D20')}
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden">
            <div style="padding:20px">${depSvg}</div>
        </div>
    </section>` : ''}

    <!-- Models -->
    <section>
        ${secHeader('Models', `${(d.models??[]).length} Eloquent models detected`, '#FF2D20')}
        ${tableWrap(['Model','Table','Fillable Fields','Relationships'], modelRows || '<tr><td colspan="4" style="padding:12px;color:#94a3b8">No models.</td></tr>')}
    </section>

    <!-- Controllers -->
    <section>
        ${secHeader('Controllers', `${(d.controllers??[]).length} controllers`, '#3b82f6')}
        ${tableWrap(['Controller','Methods','Method List'], ctrlRows || '<tr><td colspan="3" style="padding:12px;color:#94a3b8">No controllers.</td></tr>')}
    </section>

</div>

<!-- ═══ FOOTER ═══════════════════════════════════════════════════════════ -->
<div style="background:#111111;border-top:2px solid #FF2D20;color:#64748b;text-align:center;padding:24px;font-size:12px;font-family:system-ui,sans-serif;margin-top:20px">
    Generated by <strong style="color:#FF2D20">laradar</strong> · ${esc(d.generated_at ?? '')}
</div>

</body>
</html>`;
}

function _buildDepSvg(nodes, edges) {
    if (!nodes.length) return '';

    const layerOrder = ['controller','job','event','listener','service','repository','model'];
    const layerColors = {
        controller: { fill:'#FFF1F0', stroke:'#FF2D20', text:'#172B4D' },
        service:    { fill:'#E3FCEF', stroke:'#00875A', text:'#172B4D' },
        repository: { fill:'#FFFAE6', stroke:'#FF8B00', text:'#172B4D' },
        model:      { fill:'#F3F0FF', stroke:'#6554C0', text:'#172B4D' },
        job:        { fill:'#FFF4E5', stroke:'#FF5630', text:'#172B4D' },
        event:      { fill:'#FFF0FB', stroke:'#BF40BF', text:'#172B4D' },
        listener:   { fill:'#FEE4FA', stroke:'#DA62AC', text:'#172B4D' },
    };

    // Wrap each layer into rows so the SVG stays a reasonable width
    const MAX_PER_ROW = 6;
    const NW = 140, NH = 52, GAP_X = 16, GAP_Y = 68, ROW_GAP = 10, PAD = 28;

    const byLayer = {};
    nodes.forEach(n => {
        const l = n.layer ?? 'model';
        (byLayer[l] = byLayer[l] || []).push(n);
    });
    const lKeys = layerOrder.filter(l => byLayer[l]?.length);

    // Canvas width = widest possible row
    const maxColsAll = Math.max(...lKeys.map(l => Math.min(byLayer[l].length, MAX_PER_ROW)));
    const CW = maxColsAll * NW + (maxColsAll - 1) * GAP_X + PAD * 2;

    // Build positions with row-wrapping per layer
    const nameToPos = {};
    let curY = PAD;
    const bands = [];

    lKeys.forEach(l => {
        const layerNodes = byLayer[l];
        const rows = [];
        for (let i = 0; i < layerNodes.length; i += MAX_PER_ROW) {
            rows.push(layerNodes.slice(i, i + MAX_PER_ROW));
        }
        const bandY1 = curY;
        rows.forEach((row, ri) => {
            const rowW = row.length * NW + (row.length - 1) * GAP_X;
            let x = (CW - rowW) / 2;
            row.forEach(n => {
                nameToPos[n.name] = { x, y: curY, cx: x + NW / 2 };
                x += NW + GAP_X;
            });
            curY += NH + (ri < rows.length - 1 ? ROW_GAP : 0);
        });
        bands.push({ l, y1: bandY1, y2: curY });
        curY += GAP_Y;
    });
    const CH = curY - GAP_Y + PAD;

    // Layer band backgrounds + labels
    const bandsSvg = bands.map(b => {
        const c = layerColors[b.l] ?? { fill:'#F4F5F7', stroke:'#6B778C' };
        const label = b.l.charAt(0).toUpperCase() + b.l.slice(1) + 's';
        const bh = b.y2 - b.y1 + 16;
        return `<rect x="${PAD / 2}" y="${b.y1 - 8}" width="${CW - PAD}" height="${bh}" rx="8" fill="${c.stroke}" opacity="0.06"/>
                <text x="${PAD}" y="${b.y1 - 8 + bh / 2 + 4}" font-size="9" font-weight="700" fill="${c.stroke}" opacity="0.55" font-family="ui-monospace,monospace" letter-spacing="0.08em">${label.toUpperCase()}</text>`;
    }).join('');

    // Edges (skip back-edges for readability in a static render)
    const edgesSvg = edges.slice(0, 200).map(e => {
        const f = nameToPos[e.from], t = nameToPos[e.to];
        if (!f || !t) return '';
        const x1 = f.cx, y1 = f.y + NH, x2 = t.cx, y2 = t.y;
        if (y2 <= y1 + 4) return '';
        const cp = (y2 - y1) * 0.45;
        return `<path d="M${x1},${y1} C${x1},${y1+cp} ${x2},${y2-cp} ${x2},${y2}" fill="none" stroke="rgba(255,45,32,0.15)" stroke-width="1.4" marker-end="url(#dep-arr)"/>`;
    }).join('');

    // Nodes
    const nodesSvg = nodes.map(n => {
        const p = nameToPos[n.name]; if (!p) return '';
        const c  = layerColors[n.layer ?? ''] ?? { fill:'#F4F5F7', stroke:'#6B778C', text:'#172B4D' };
        const nm = n.name.length > 17 ? n.name.slice(0, 16) + '…' : n.name;
        const lb = (n.layer ?? '').toUpperCase();
        return `<g>
            <rect x="${p.x}" y="${p.y}" width="${NW}" height="${NH}" rx="8" fill="${c.fill}" stroke="${c.stroke}" stroke-width="1.5"/>
            <text x="${p.cx}" y="${p.y + 18}" text-anchor="middle" font-size="8.5" font-weight="700" fill="${c.stroke}" font-family="ui-monospace,monospace" letter-spacing="0.07em">${lb}</text>
            <text x="${p.cx}" y="${p.y + 36}" text-anchor="middle" font-size="11" font-weight="600" fill="${c.text}" font-family="ui-monospace,monospace">${nm}</text>
        </g>`;
    }).join('');

    // viewBox + max-width:100% + height:auto ensures the SVG scales to any container
    return `<svg viewBox="0 0 ${CW} ${CH}" width="${CW}" height="${CH}" style="display:block;max-width:100%;height:auto">
        <defs>
            <marker id="dep-arr" markerWidth="7" markerHeight="6" refX="6" refY="3" orient="auto">
                <polygon points="0 0,7 3,0 6" fill="#94a3b8"/>
            </marker>
        </defs>
        <rect width="${CW}" height="${CH}" fill="#F7F8F9" rx="12"/>
        ${bandsSvg}
        ${edgesSvg}
        ${nodesSvg}
    </svg>`;
}

// ── AI Insights ───────────────────────────────────────────────────────────────

const AI_ENDPOINT        = '{{ route("laradar.ai.analyze") }}';
const AI_REPORT_ENDPOINT = '{{ route("laradar.ai.report") }}';
const AI_CSRF            = '{{ csrf_token() }}';
const AI_JOB_ENDPOINT    = '{{ route("laradar.ai.job.status", ["id" => "__ID__"]) }}';

function _aiJobUrl(id) { return AI_JOB_ENDPOINT.replace('__ID__', id); }

let _aiAbortCtrl = null;
let _aiPollTimer = null;

function _aiPoll(jobId, onDone, onFail) {
    const timerId = setInterval(async () => {
        try {
            const res  = await fetch(_aiJobUrl(jobId), { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            if (data.status === 'done') {
                clearInterval(timerId);
                onDone(data.result);
            } else if (data.status === 'failed' || data.status === 'not_found') {
                clearInterval(timerId);
                onFail(data.error || 'Job failed.');
            } else if (data.status === 'running') {
                const loadingText = document.getElementById('ai-loading-text');
                if (loadingText) loadingText.textContent = 'AI is thinking…';
            }
        } catch (e) {
            clearInterval(timerId);
            onFail(e.message);
        }
    }, 3000);
    return timerId;
}

function aiCancel() {
    if (_aiAbortCtrl) { _aiAbortCtrl.abort(); _aiAbortCtrl = null; }
    if (_aiPollTimer) { clearInterval(_aiPollTimer); _aiPollTimer = null; }
    document.getElementById('ai-loading').style.display = 'none';
    const trigEl = document.getElementById('ai-trigger');
    if (trigEl) { trigEl.style.opacity = ''; trigEl.style.pointerEvents = ''; }
}

async function aiAnalyze() {
    document.getElementById('ai-loading').style.display = 'block';
    document.getElementById('ai-error').style.display = 'none';
    const loadingText = document.getElementById('ai-loading-text');
    if (loadingText) loadingText.textContent = 'Analyzing architecture…';
    const trigEl = document.getElementById('ai-trigger');
    if (trigEl) { trigEl.style.opacity = '0.5'; trigEl.style.pointerEvents = 'none'; }

    _aiAbortCtrl = new AbortController();

    const finish = () => {
        document.getElementById('ai-loading').style.display = 'none';
        if (trigEl) { trigEl.style.opacity = ''; trigEl.style.pointerEvents = ''; }
        _aiAbortCtrl = null;
        _aiPollTimer = null;
    };

    try {
        const res  = await fetch(AI_ENDPOINT, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': AI_CSRF },
            body:    JSON.stringify({ report: APP }),
            signal:  _aiAbortCtrl.signal,
        });
        const json = await res.json();
        if (!res.ok || json.error) throw new Error(json.error || 'Server returned ' + res.status);

        if (json.job_id) {
            if (loadingText) loadingText.textContent = 'Queued — waiting for result…';
            _aiPollTimer = _aiPoll(json.job_id,
                (result) => { finish(); aiRenderResults(result); },
                (err)    => { finish(); document.getElementById('ai-error-msg').textContent = err; document.getElementById('ai-error').style.display = 'block'; }
            );
        } else {
            finish();
            aiRenderResults(json);
        }
    } catch (err) {
        if (err.name === 'AbortError') return;
        finish();
        document.getElementById('ai-error-msg').textContent = err.message;
        document.getElementById('ai-error').style.display = 'block';
    }
}

function aiRenderResults(data) {
    // Summary
    document.getElementById('ai-summary').textContent = data.summary || 'No summary available.';

    // Score
    const score = data.score || 0;
    const scoreNumEl = document.getElementById('ai-score-num');
    scoreNumEl.textContent = score;
    scoreNumEl.style.color = '#FF2D20';
    scoreNumEl.style.fontSize = '26px';
    setTimeout(() => {
        const ring = document.getElementById('ai-score-ring');
        if (ring) ring.style.strokeDashoffset = 226 - (score / 100 * 226);
    }, 50);

    // SOLID
    const solidEl = document.getElementById('ai-solid');
    solidEl.innerHTML = '';
    const solidNames = { S: 'Single Resp.', O: 'Open/Closed', L: 'Liskov Sub.', I: 'Interface Seg.', D: 'Dep. Inversion' };
    Object.entries(data.solid_review || {}).forEach(([key, val], idx) => {
        const color = val.status === 'pass' ? 'green' : val.status === 'warn' ? 'amber' : 'red';
        const icon  = val.status === 'pass' ? '✔' : val.status === 'warn' ? '⚠' : '✘';
        solidEl.insertAdjacentHTML('beforeend', `
            <div class="flex flex-col items-center text-center p-3 rounded-xl bg-${color}-50 border border-${color}-200" style="animation:fadeUp .35s var(--ease) both;animation-delay:${idx * 70}ms;">
                <span class="text-xl font-bold text-${color}-600">${key}</span>
                <span class="text-xs font-medium text-${color}-700 mt-0.5">${solidNames[key] || ''}</span>
                <span class="text-lg mt-2">${icon}</span>
                <p class="text-xs text-${color}-600 mt-1 leading-tight">${_esc(val.note || '')}</p>
            </div>
        `);
    });

    // Problems
    const problemsEl = document.getElementById('ai-problems');
    problemsEl.innerHTML = '';
    if (!(data.problems || []).length) {
        problemsEl.innerHTML = '<p class="text-sm text-slate-400 italic">No problems detected.</p>';
    }
    (data.problems || []).forEach((p, idx) => {
        const sev   = p.severity || 'info';
        const color = sev === 'error' ? 'red' : sev === 'warning' ? 'amber' : 'blue';
        problemsEl.insertAdjacentHTML('beforeend', `
            <div class="flex gap-3 p-3 rounded-lg bg-${color}-50 border border-${color}-100" style="animation:fadeUp .3s var(--ease) both;animation-delay:${idx * 55}ms;">
                <div class="shrink-0 mt-0.5">
                    <span class="inline-block px-1.5 py-0.5 text-xs font-bold rounded uppercase bg-${color}-100 text-${color}-700">${sev}</span>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-800">${_esc(p.title || '')}</p>
                    ${p.location ? `<p class="text-xs text-slate-500 mt-0.5">📍 ${_esc(p.location)}</p>` : ''}
                    <p class="text-sm text-slate-600 mt-1">${_esc(p.description || '')}</p>
                </div>
            </div>
        `);
    });

    // Suggestions
    const suggEl = document.getElementById('ai-suggestions');
    suggEl.innerHTML = '';
    if (!(data.suggestions || []).length) {
        suggEl.innerHTML = '<p class="text-sm text-slate-400 italic">No suggestions.</p>';
    }
    (data.suggestions || []).forEach((s, idx) => {
        const pri   = s.priority || 'medium';
        const color = pri === 'high' ? 'red' : pri === 'medium' ? 'amber' : 'slate';
        suggEl.insertAdjacentHTML('beforeend', `
            <div class="flex gap-3 p-3 rounded-lg border border-slate-100 bg-slate-50" style="animation:fadeUp .3s var(--ease) both;animation-delay:${idx * 55}ms;">
                <span class="shrink-0 mt-0.5 inline-block px-1.5 py-0.5 h-fit text-xs font-bold rounded uppercase bg-${color}-100 text-${color}-700">${pri}</span>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-800">${_esc(s.title || '')}</p>
                    <p class="text-sm text-slate-600 mt-0.5">${_esc(s.description || '')}</p>
                    ${s.example ? `<code class="block mt-1.5 text-xs bg-slate-800 text-green-300 px-2 py-1 rounded">${_esc(s.example)}</code>` : ''}
                </div>
            </div>
        `);
    });

    // Laravel Best Practices
    const laravelEl = document.getElementById('ai-laravel-practices');
    laravelEl.innerHTML = '';
    (data.laravel_best_practices || []).forEach(p => {
        const icon  = p.status === 'pass' ? '✔' : p.status === 'warn' ? '⚠' : '✘';
        const color = p.status === 'pass' ? 'green' : p.status === 'warn' ? 'amber' : 'red';
        laravelEl.insertAdjacentHTML('beforeend', `
            <div class="flex items-start gap-2.5 py-1.5 border-b border-slate-100 last:border-0">
                <span class="text-${color}-600 font-bold mt-0.5 shrink-0">${icon}</span>
                <div class="min-w-0">
                    <span class="text-sm font-medium text-slate-700">${_esc(p.name || '')}</span>
                    ${p.note ? `<span class="text-xs text-slate-500 ml-2">${_esc(p.note)}</span>` : ''}
                </div>
            </div>
        `);
    });

    // Already followed best practices
    const bpEl = document.getElementById('ai-best-practices');
    bpEl.innerHTML = '';
    (data.best_practices || []).forEach(bp => {
        bpEl.insertAdjacentHTML('beforeend', `
            <li class="flex items-start gap-2 text-sm text-slate-600">
                <span class="text-green-500 shrink-0 mt-0.5">✔</span>
                <span>${_esc(bp)}</span>
            </li>
        `);
    });

    // Provider badge
    document.getElementById('ai-provider-badge').textContent =
        'Analyzed by ' + (data.provider || 'AI') + ' · ' + (data.model || '');

    const reanalyzeEl = document.getElementById('ai-reanalyze');
    if (reanalyzeEl) reanalyzeEl.style.display = 'flex';
}

// ── Doc preview helpers ───────────────────────────────────────────────────────

function _mdExcerpt(md, maxLen) {
    const plain = md
        .replace(/```[\s\S]*?```/g, '')
        .replace(/^#{1,6}\s+/gm, '')
        .replace(/\*\*([^*]+)\*\*/g, '$1')
        .replace(/\*([^*]+)\*/g, '$1')
        .replace(/^\|.+\|$/gm, '')
        .replace(/^[-*]\s+/gm, '• ')
        .replace(/`[^`]+`/g, '')
        .replace(/\n{2,}/g, ' ')
        .trim();
    return plain.slice(0, maxLen) + (plain.length > maxLen ? '…' : '');
}

function docsPreview(type) {
    const doc = _docsContent[type];
    if (!doc) return;
    document.getElementById('doc-modal-title').textContent = doc.filename;
    document.getElementById('doc-modal-body').innerHTML    = _mdToHtml(doc.content);
    document.getElementById('doc-modal-dl-md').onclick    = () => docsDownload(type);
    document.getElementById('doc-modal-dl-html').onclick  = () => docsDownloadHtml(type);

    const modal = document.getElementById('doc-modal');
    const box   = modal.querySelector('.doc-modal-box');
    modal.style.display  = 'flex';
    modal.style.opacity  = '0';
    box.style.animation  = 'none';
    void box.offsetWidth; // reflow
    modal.style.animation = 'modalBdIn .25s var(--ease) forwards';
    box.style.animation   = 'modalScaleIn .3s cubic-bezier(.34,1.56,.64,1) forwards';
    modal.style.opacity   = '';
    document.body.style.overflow = 'hidden';
}

function closeDocModal() {
    const modal = document.getElementById('doc-modal');
    const box   = modal.querySelector('.doc-modal-box');
    box.style.animation   = 'modalScaleOut .2s var(--ease) forwards';
    modal.style.animation = 'modalBdIn .2s var(--ease) reverse forwards';
    setTimeout(() => {
        modal.style.display   = 'none';
        modal.style.animation = '';
        box.style.animation   = '';
        document.body.style.overflow = '';
    }, 200);
}

function docsDownloadHtml(type) {
    const doc = _docsContent[type];
    if (!doc) return;
    const html = `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>${doc.filename.replace('.md','')}</title>
<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}
body {
  font-family:
    system-ui,
    -apple-system,
    sans-serif;
  background: #f8fafc;
  color: #1e293b;
  padding: 40px 20px;
  line-height: 1.6;
}
.wrap {
  max-width: 820px;
  margin: 0 auto;
  background: #fff;
  border-radius: 16px;
  padding: 40px 48px;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07);
  border: 1px solid #e2e8f0;
}
h1 {
  font-size: 28px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 20px;
  padding-bottom: 12px;
  border-bottom: 3px solid #e2e8f0;
}
h2 {
  font-size: 20px;
  font-weight: 700;
  color: #0f172a;
  margin: 32px 0 10px;
  padding-bottom: 6px;
  border-bottom: 2px solid #f1f5f9;
}
h3 {
  font-size: 16px;
  font-weight: 700;
  color: #1e293b;
  margin: 20px 0 8px;
}
p {
  font-size: 14px;
  color: #334155;
  line-height: 1.75;
  margin-bottom: 12px;
}
ul,
ol {
  padding-left: 22px;
  margin: 8px 0 14px;
}
li {
  font-size: 14px;
  color: #334155;
  margin-bottom: 5px;
  line-height: 1.65;
}
strong {
  color: #0f172a;
  font-weight: 700;
}
em {
  font-style: italic;
}
code {
  font-family: ui-monospace, monospace;
  font-size: 12.5px;
  background: #f1f5f9;
  color: #0052cc;
  padding: 2px 7px;
  border-radius: 4px;
  border: 1px solid #e2e8f0;
}
pre {
  background: #1e293b;
  color: #e2e8f0;
  border-radius: 10px;
  padding: 18px;
  overflow-x: auto;
  font-family: ui-monospace, monospace;
  font-size: 13px;
  line-height: 1.6;
  margin: 14px 0;
}
hr {
  border: none;
  border-top: 1px solid #e2e8f0;
  margin: 24px 0;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin: 14px 0;
}
th {
  background: #f8fafc;
  padding: 10px 14px;
  text-align: left;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.07em;
  color: #64748b;
  border-bottom: 2px solid #e2e8f0;
}
td {
  padding: 10px 14px;
  border-bottom: 1px solid #f1f5f9;
  color: #334155;
  font-size: 13.5px;
}
tr:last-child td {
  border-bottom: none;
}
.footer {
  margin-top: 40px;
  padding-top: 16px;
  border-top: 1px solid #e2e8f0;
  font-size: 11px;
  color: #94a3b8;
  text-align: center;
}
</style>
</head>
<body>
<div class="wrap">
${_mdToHtml(doc.content)}
<div class="footer">Generated by Laradar &middot; ${new Date().toLocaleDateString()}</div>
</div>
</body>
</html>`;
    _downloadBlob(html, doc.filename.replace(/\.md$/i, '.html'), 'text/html;charset=utf-8');
}

function _esc(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function _downloadBlob(content, filename, mime, isBlob = false) {
    const blob = isBlob ? content : new Blob([content], { type: mime });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    setTimeout(() => URL.revokeObjectURL(url), 2000);
}

// ── Card 3D tilt on hover ────────────────────────────────────────────────────
(function() {
    const MAX = 6; // max degrees

    function _tilt(card, e) {
        const r = card.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width  - 0.5;
        const y = (e.clientY - r.top)  / r.height - 0.5;
        card.style.transition = 'box-shadow .25s, border-color .25s';
        card.style.transform  = `perspective(800px) rotateX(${(-y * MAX * 2).toFixed(2)}deg) rotateY(${(x * MAX * 2).toFixed(2)}deg) translateY(-4px)`;
    }

    function _initTilt() {
        document.querySelectorAll('.mds-card, .ctrl-card, .pkg-card').forEach(card => {
            let raf = null;
            card.addEventListener('mousemove', e => {
                if (raf) return;
                raf = requestAnimationFrame(() => { raf = null; _tilt(card, e); });
            });
            card.addEventListener('mouseleave', () => {
                if (raf) { cancelAnimationFrame(raf); raf = null; }
                card.style.transition = 'transform 0.45s ease-out, box-shadow .25s, border-color .25s';
                card.style.transform  = '';
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', _initTilt);
    } else {
        _initTilt();
    }
})();


// Score bar fill: animate mds-rel-bar segments when model card enters view
(function() {
    function _fillRelBar(card) {
        card.querySelectorAll('.mds-rel-seg[data-flex]').forEach(seg => {
            seg.style.flex = seg.dataset.flex;
        });
    }
    if (!window.IntersectionObserver) {
        document.querySelectorAll('.mds-card').forEach(_fillRelBar);
    } else {
        const io = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (!e.isIntersecting) return;
                io.unobserve(e.target);
                _fillRelBar(e.target);
            });
        }, { threshold: 0.15 });
        document.querySelectorAll('.mds-card').forEach(c => io.observe(c));
    }
})();

</script>

@stack('scripts')

{{-- Shared format picker panel (position:fixed, viewport-aware) --}}
<div id="fmt-shared-panel" style="display:none;position:fixed;z-index:9999;background:var(--bg-elevated);border:1px solid #FF2D20;border-radius:10px;overflow:hidden;min-width:80px;box-shadow:0 4px 20px rgba(0,0,0,0.15);">
    <div onclick="selectFmtOption('md')" style="padding:10px 14px;font-size:12px;font-family:var(--font-mono);color:#FF2D20;cursor:pointer;" onmouseover="this.style.background='rgba(255,45,32,0.08)'" onmouseout="this.style.background=''">.md</div>
    <div onclick="selectFmtOption('html')" style="padding:10px 14px;font-size:12px;font-family:var(--font-mono);color:#FF2D20;cursor:pointer;" onmouseover="this.style.background='rgba(255,45,32,0.08)'" onmouseout="this.style.background=''">.html</div>
</div>

</body>
</html>
