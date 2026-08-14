<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $data['project']['name'] }} — Architecture Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<style>
body{font-family:system-ui,sans-serif}
.sidebar::-webkit-scrollbar{width:4px}.sidebar::-webkit-scrollbar-thumb{background:#334155;border-radius:2px}
.content::-webkit-scrollbar{width:6px}.content::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:3px}
.method-get{background:#dcfce7;color:#166534}.method-post{background:#dbeafe;color:#1e40af}
.method-put,.method-patch{background:#fef9c3;color:#854d0e}.method-delete{background:#fee2e2;color:#991b1b}
.method-head,.method-options{background:#f1f5f9;color:#475569}
.grade-a{background:#dcfce7;color:#166534}.grade-b{background:#dbeafe;color:#1e40af}
.grade-c{background:#fef9c3;color:#854d0e}.grade-d{background:#fed7aa;color:#9a3412}.grade-f{background:#fee2e2;color:#991b1b}
.card{transition:box-shadow .15s,transform .15s}.card:hover{box-shadow:0 4px 14px rgba(0,0,0,.09);transform:translateY(-1px)}
.nav-item{transition:background .12s,color .12s}.nav-active{background:#4f46e5!important;color:#fff!important}
.mermaid svg{max-width:100%;height:auto}

/* ── Relation Graph ── */
.g-node{transition:opacity .15s ease}
.diag-tab{transition:background .15s,color .15s,border-color .15s}
</style>
</head>
<body class="bg-slate-50 text-slate-800 flex h-screen overflow-hidden">

@php
$score   = $data['score'] ?? [];
$summary = $data['summary'] ?? [];
$rs      = $data['route_summary'] ?? [];
$grade   = $score['grade'] ?? 'N/A';
$gradeClass = match(strtoupper($grade[0] ?? 'F')) {
    'A' => 'grade-a', 'B' => 'grade-b', 'C' => 'grade-c', 'D' => 'grade-d', default => 'grade-f',
};
@endphp

{{-- ══ SIDEBAR ══ --}}
<aside class="sidebar w-60 min-w-60 bg-slate-900 text-slate-100 flex flex-col overflow-y-auto shrink-0">
    <div class="px-4 py-4 border-b border-slate-700">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center font-bold text-sm">L</div>
            <div class="min-w-0">
                <p class="text-xs text-slate-400">Architecture</p>
                <p class="font-semibold text-sm truncate">{{ $data['project']['name'] }}</p>
            </div>
        </div>
        <p class="text-xs text-slate-500 mt-2">v{{ $data['package_version'] }} · PHP {{ $data['php_version'] }}</p>
    </div>

    @if(!empty($score))
    <div class="px-4 py-3 border-b border-slate-700">
        <p class="text-xs text-slate-400 uppercase tracking-wider mb-2">Score</p>
        <div class="flex items-center gap-2">
            <span class="text-2xl font-bold">{{ $score['score'] }}<span class="text-slate-400 text-sm">/{{ $score['max'] }}</span></span>
            <span class="px-2 py-0.5 rounded text-xs font-bold {{ $gradeClass }}">{{ $grade }}</span>
        </div>
        <div class="mt-2 bg-slate-700 rounded-full h-1.5">
            <div class="bg-indigo-500 h-1.5 rounded-full" style="width:{{ round(($score['score']/max(1,$score['max']))*100) }}%"></div>
        </div>
    </div>
    @endif

    <nav class="flex-1 px-2 py-3 space-y-0.5">
        <button onclick="navigate('overview')" id="nav-overview" class="nav-item w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            Overview
        </button>
        @if(($summary['modules']??0)>0)
        <button onclick="navigate('modules')" id="nav-modules" class="nav-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Modules
            </span>
            <span class="text-xs bg-indigo-700 text-indigo-200 px-1.5 py-0.5 rounded-full">{{ $summary['modules'] }}</span>
        </button>
        @endif
        <button onclick="navigate('packages')" id="nav-packages" class="nav-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                Packages
            </span>
            @if(($summary['packages']??0)>0)<span class="text-xs bg-slate-700 px-1.5 py-0.5 rounded-full">{{ $summary['packages'] }}</span>@endif
        </button>

        <p class="text-xs text-slate-500 uppercase tracking-wider px-3 pt-3 pb-1">Core</p>

        <button onclick="navigate('models')" id="nav-models" class="nav-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
            <span class="flex items-center gap-2"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>Models</span>
            @if(($summary['models']??0)>0)<span class="text-xs bg-slate-700 px-1.5 py-0.5 rounded-full">{{ $summary['models'] }}</span>@endif
        </button>
        <button onclick="navigate('modelmap')" id="nav-modelmap" class="nav-item w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            Relation Graph
        </button>
        <button onclick="navigate('controllers')" id="nav-controllers" class="nav-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
            <span class="flex items-center gap-2"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>Controllers</span>
            @if(($summary['controllers']??0)>0)<span class="text-xs bg-slate-700 px-1.5 py-0.5 rounded-full">{{ $summary['controllers'] }}</span>@endif
        </button>
        <button onclick="navigate('routes')" id="nav-routes" class="nav-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
            <span class="flex items-center gap-2"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>Routes</span>
            @if(($rs['total']??0)>0)<span class="text-xs bg-slate-700 px-1.5 py-0.5 rounded-full">{{ $rs['total'] }}</span>@endif
        </button>
        <button onclick="navigate('apidocs')" id="nav-apidocs" class="nav-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                API Docs
            </span>
            @php $apiDocCount = count($data['api_docs'] ?? []); @endphp
            @if($apiDocCount > 0)<span class="text-xs bg-slate-700 px-1.5 py-0.5 rounded-full">{{ $apiDocCount }}</span>@endif
        </button>

        <p class="text-xs text-slate-500 uppercase tracking-wider px-3 pt-3 pb-1">Components</p>

        <button onclick="navigate('jobs')" id="nav-jobs" class="nav-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
            <span class="flex items-center gap-2"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>Jobs</span>
            @if(($summary['jobs']??0)>0)<span class="text-xs bg-slate-700 px-1.5 py-0.5 rounded-full">{{ $summary['jobs'] }}</span>@endif
        </button>
        <button onclick="navigate('events')" id="nav-events" class="nav-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
            <span class="flex items-center gap-2"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>Events</span>
            @if(($summary['events']??0)>0)<span class="text-xs bg-slate-700 px-1.5 py-0.5 rounded-full">{{ $summary['events'] }}</span>@endif
        </button>
        <button onclick="navigate('services')" id="nav-services" class="nav-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
            <span class="flex items-center gap-2"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>Services</span>
            @if(($summary['services']??0)>0)<span class="text-xs bg-slate-700 px-1.5 py-0.5 rounded-full">{{ $summary['services'] }}</span>@endif
        </button>
        <button onclick="navigate('repositories')" id="nav-repositories" class="nav-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
            <span class="flex items-center gap-2"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>Repositories</span>
            @if(($summary['repositories']??0)>0)<span class="text-xs bg-slate-700 px-1.5 py-0.5 rounded-full">{{ $summary['repositories'] }}</span>@endif
        </button>
        <button onclick="navigate('observers')" id="nav-observers" class="nav-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
            <span class="flex items-center gap-2"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>Observers</span>
            @if(($summary['observers']??0)>0)<span class="text-xs bg-slate-700 px-1.5 py-0.5 rounded-full">{{ $summary['observers'] }}</span>@endif
        </button>
        <button onclick="navigate('policies')" id="nav-policies" class="nav-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
            <span class="flex items-center gap-2"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>Policies</span>
            @if(($summary['policies']??0)>0)<span class="text-xs bg-slate-700 px-1.5 py-0.5 rounded-full">{{ $summary['policies'] }}</span>@endif
        </button>

        <p class="text-xs text-slate-500 uppercase tracking-wider px-3 pt-3 pb-1">Architecture</p>

        <button onclick="navigate('dependencies')" id="nav-dependencies" class="nav-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
            <span class="flex items-center gap-2"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>Dependencies</span>
            @if(!empty($data['dependencies']['edges']))<span class="text-xs bg-slate-700 px-1.5 py-0.5 rounded-full">{{ count($data['dependencies']['edges']) }}</span>@endif
        </button>
        <button onclick="navigate('export')" id="nav-export" class="nav-item w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export
        </button>

        <p class="text-xs text-slate-500 uppercase tracking-wider px-3 pt-3 pb-1">AI</p>

        <button onclick="navigate('ai')" id="nav-ai" class="nav-item w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            AI Insights
            @if(config('architecture-discovery.ai.enabled', false))
            <span class="ml-auto w-2 h-2 rounded-full bg-green-400 shrink-0"></span>
            @endif
        </button>
        <button onclick="navigate('chat')" id="nav-chat" class="nav-item w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            AI Chat
        </button>
        <button onclick="navigate('aidocs')" id="nav-aidocs" class="nav-item w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            AI Docs
        </button>
    </nav>

    <div class="px-4 py-3 border-t border-slate-700">
        <p class="text-xs text-slate-500">Generated {{ \Carbon\Carbon::parse($data['generated_at'])->format('M d, Y H:i') }}</p>
    </div>
</aside>

{{-- ══ MAIN ══ --}}
<main class="content flex-1 overflow-y-auto">

{{-- Overview --}}
<section id="sec-overview" class="p-6">
    <h1 class="text-2xl font-bold mb-1">Overview</h1>
    <p class="text-slate-500 text-sm mb-6">{{ $data['project']['name'] }} · Laravel {{ $data['laravel_version'] }}</p>

    @php
    $stats = [
        ['Models',       $summary['models']??0,       '#6366f1'],
        ['Controllers',  $summary['controllers']??0,   '#3b82f6'],
        ['Routes',       $rs['total']??0,              '#10b981'],
        ['Jobs',         $summary['jobs']??0,          '#f59e0b'],
        ['Events',       $summary['events']??0,        '#ec4899'],
        ['Services',     $summary['services']??0,      '#8b5cf6'],
        ['Repositories', $summary['repositories']??0,  '#06b6d4'],
        ['Observers',    $summary['observers']??0,     '#f97316'],
        ['Policies',     $summary['policies']??0,      '#64748b'],
        ['Modules',      $summary['modules']??0,       '#4f46e5'],
        ['Dep. Edges',   count($data['dependencies']['edges']??[]), '#334155'],
    ];
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-8">
        @foreach($stats as [$label,$count,$color])
        <div class="card bg-white rounded-xl p-4 shadow-sm border border-slate-100">
            <p class="text-xs text-slate-500 mb-2">{{ $label }}</p>
            <p class="text-3xl font-bold" style="color:{{ $color }}">{{ $count }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Route breakdown --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
            <h3 class="font-semibold mb-4">Route Breakdown</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-slate-500">Total</span><span class="font-medium">{{ $rs['total']??0 }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Web</span><span class="font-medium">{{ $rs['web']??0 }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">API</span><span class="font-medium">{{ $rs['api']??0 }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Named</span><span class="font-medium">{{ $rs['named_count']??0 }} / {{ $rs['total']??0 }}</span></div>
                @if(!empty($rs['api_versions']))
                <div class="flex justify-between"><span class="text-slate-500">API Versions</span><span class="font-medium">{{ implode(', ', array_keys($rs['api_versions'])) }}</span></div>
                @endif
            </div>
            @if(!empty($rs['by_method']))
            <div class="mt-4 pt-3 border-t border-slate-100">
                <p class="text-xs text-slate-400 mb-2">By Method</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($rs['by_method'] as $method => $cnt)
                    <span class="text-xs px-2 py-0.5 rounded font-semibold method-{{ strtolower($method) }}">{{ strtoupper($method) }} {{ $cnt }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Performance --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
            <h3 class="font-semibold mb-4">Performance</h3>
            @php $perf = $data['performance']??[]; @endphp
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1"><span class="text-slate-500">Scan Time</span><span class="font-medium">{{ $perf['execution_time_ms']??0 }} ms</span></div>
                    <div class="bg-slate-100 rounded-full h-1.5"><div class="bg-indigo-500 h-1.5 rounded-full" style="width:{{ min(100,($perf['execution_time_ms']??0)/50) }}%"></div></div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1"><span class="text-slate-500">Memory</span><span class="font-medium">{{ $perf['memory_usage_mb']??0 }} MB</span></div>
                    <div class="bg-slate-100 rounded-full h-1.5"><div class="bg-green-500 h-1.5 rounded-full" style="width:{{ min(100,($perf['memory_usage_mb']??0)/1.28) }}%"></div></div>
                </div>
            </div>
        </div>

        {{-- Score checks --}}
        @if(!empty($score['checks']))
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
            <h3 class="font-semibold mb-4">Score Checks</h3>
            <div class="space-y-2">
                @foreach($score['checks'] as $check)
                @php $ic = match($check['status']??'fail'){'pass'=>['✔','text-green-600'],'warn'=>['⚠','text-yellow-500'],default=>['✘','text-red-500']}; @endphp
                <div class="flex items-start gap-2">
                    <span class="font-bold text-sm {{ $ic[1] }} mt-0.5">{{ $ic[0] }}</span>
                    <div><p class="text-sm">{{ $check['label'] }}</p>@if(!empty($check['note']))<p class="text-xs text-slate-400">{{ $check['note'] }}</p>@endif</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>

{{-- Models --}}
<section id="sec-models" class="p-6" style="display:none">
    <div id="models-list">
        <div class="flex items-center justify-between mb-6">
            <div><h1 class="text-2xl font-bold">Models</h1><p class="text-slate-500 text-sm">{{ count($data['models']) }} Eloquent models</p></div>
            <input id="models-search" oninput="filterGrid('models')" type="search" placeholder="Search…" class="border border-slate-200 rounded-lg px-3 py-2 text-sm w-44 focus:outline-none focus:ring-2 focus:ring-indigo-300">
        </div>
        <div id="models-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($data['models'] as $i => $model)
            <div class="card bg-white rounded-xl shadow-sm border border-slate-100 p-4 cursor-pointer" onclick="showDetail('models',{{$i}})" data-name="{{ strtolower($model['name']) }}">
                <div class="flex items-start justify-between mb-2">
                    <div><p class="font-semibold">{{ $model['name'] }}</p><p class="text-xs text-slate-400 font-mono">{{ $model['table'] }}</p></div>
                    <span class="text-xs bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-full shrink-0">{{ count($model['relationships']??[]) }} rels</span>
                </div>
                @if(!empty($model['fillable']))
                <div class="flex flex-wrap gap-1 mb-1">
                    @foreach(array_slice($model['fillable'],0,4) as $f)<span class="text-xs bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded font-mono">{{ $f }}</span>@endforeach
                    @if(count($model['fillable'])>4)<span class="text-xs text-slate-400">+{{ count($model['fillable'])-4 }}</span>@endif
                </div>
                @endif
                @if(!empty($model['traits']))
                <div class="flex flex-wrap gap-1">
                    @foreach($model['traits'] as $t)<span class="text-xs bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded">{{ $t }}</span>@endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    <div id="models-detail" style="display:none">
        <button onclick="showList('models')" class="flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Back to Models
        </button>
        <div id="models-detail-content"></div>
    </div>
</section>

{{-- Controllers --}}
<section id="sec-controllers" class="p-6" style="display:none">
    <div id="controllers-list">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold">Controllers</h1>
                <p class="text-slate-500 text-sm">{{ count($data['controllers']) }} controllers discovered</p>
            </div>
            <input id="controllers-search" oninput="filterGrid('controllers')" type="search" placeholder="Search…" class="border border-slate-200 rounded-lg px-3 py-2 text-sm w-44 focus:outline-none focus:ring-2 focus:ring-indigo-300">
        </div>
        <div id="controllers-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($data['controllers'] as $i => $ctrl)
            <div class="card bg-white rounded-xl shadow-sm border border-slate-100 p-4 cursor-pointer" onclick="showDetail('controllers',{{$i}})" data-name="{{ strtolower($ctrl['name']) }}">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm shrink-0">{{ strtoupper(substr($ctrl['name'],0,1)) }}</div>
                        <p class="font-semibold text-slate-800">{{ $ctrl['name'] }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        <span class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full">{{ $ctrl['method_count']??0 }} methods</span>
                        @if(!empty($ctrl['is_resource']))<span class="text-xs bg-green-50 text-green-600 px-2 py-0.5 rounded-full">Resource</span>@endif
                    </div>
                </div>
                <p class="text-xs text-slate-400 font-mono mb-3 truncate">{{ $ctrl['namespace'] }}</p>
                @if(!empty($ctrl['dependencies']))
                <div class="flex flex-wrap gap-1 mb-2">
                    @foreach($ctrl['dependencies'] as $dep)
                    <span class="text-xs bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded font-mono">{{ $dep['type'] }}</span>
                    @endforeach
                </div>
                @endif
                <div class="flex flex-wrap gap-1">
                    @foreach(array_slice($ctrl['methods']??[],0,4) as $m)<span class="text-xs bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded font-mono">{{ $m }}</span>@endforeach
                    @if(count($ctrl['methods']??[])>4)<span class="text-xs text-slate-400">+{{ count($ctrl['methods'])-4 }} more</span>@endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div id="controllers-detail" style="display:none">
        <button onclick="showList('controllers')" class="flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Back to Controllers
        </button>
        <div id="controllers-detail-content"></div>
    </div>
</section>

{{-- Model Relationships Map --}}
<section id="sec-modelmap" class="p-6" style="display:none">

    @php
    $mmErPairs = [];
    foreach ($data['models'] as $mmModel) {
        foreach ($mmModel['relationships'] ?? [] as $mmRel) {
            $mmTo = class_basename($mmRel['related'] ?? '');
            if (!$mmTo || $mmTo === $mmModel['name']) continue;
            $mmPair = $mmModel['name'] . ':' . $mmTo;
            $mmRev  = $mmTo . ':' . $mmModel['name'];
            if (!isset($mmErPairs[$mmPair]) && !isset($mmErPairs[$mmRev])) {
                $mmType = $mmRel['type'];
                if (str_contains($mmType, 'BelongsToMany') || str_contains($mmType, 'MorphToMany')) { $mmL = '}o'; $mmR = 'o{'; }
                elseif (str_contains($mmType, 'BelongsTo') || str_contains($mmType, 'MorphTo'))     { $mmL = '}o'; $mmR = '||'; }
                elseif (str_contains($mmType, 'HasOne') || str_contains($mmType, 'MorphOne'))        { $mmL = '||'; $mmR = 'o|'; }
                else                                                                                  { $mmL = '||'; $mmR = 'o{'; }
                $mmErPairs[$mmPair] = "    {$mmModel['name']} {$mmL}--{$mmR} {$mmTo} : \"{$mmRel['method']}\"";
            }
        }
    }
    $mmMentioned = [];
    foreach (array_keys($mmErPairs) as $mmPk) { [$mmA,$mmB]=explode(':',$mmPk); $mmMentioned[$mmA]=true; $mmMentioned[$mmB]=true; }
    $mmStandalone = [];
    foreach ($data['models'] as $mmModel) {
        if (!isset($mmMentioned[$mmModel['name']])) {
            $mmStandalone[] = "    {$mmModel['name']} {"; $mmStandalone[] = "        string table \"{$mmModel['table']}\""; $mmStandalone[] = "    }";
        }
    }
    $mmErCode = "erDiagram\n".implode("\n",$mmStandalone).(!empty($mmStandalone)?"\n":'').implode("\n",$mmErPairs);
    @endphp

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div>
            <h1 class="text-2xl font-bold">Relation Graph</h1>
            <p class="text-slate-500 text-sm">{{ count($data['models']) }} models · {{ count($mmErPairs) }} relationships</p>
        </div>
        <div class="flex bg-slate-100 rounded-lg p-1 gap-0.5">
            <button id="map-tab-graph" onclick="setMapTab('graph')" class="px-3 py-1.5 rounded-md text-sm font-medium bg-white shadow-sm text-slate-700">Relation Graph</button>
            <button id="map-tab-tree"  onclick="setMapTab('tree')"  class="px-3 py-1.5 rounded-md text-sm font-medium text-slate-500">Tree View</button>
            <button id="map-tab-er"    onclick="setMapTab('er')"    class="px-3 py-1.5 rounded-md text-sm font-medium text-slate-500">ER Diagram</button>
        </div>
    </div>

    {{-- ── TAB: Relation Graph (force-directed SVG) ── --}}
    <div id="map-graph">

        {{-- Controls row --}}
        <div class="flex items-center gap-2 mb-3 min-h-9">
            <input id="rg-search-input" type="text" placeholder="Search model…" oninput="graphSearch(this.value)"
                class="text-xs bg-white border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300 transition w-44">
            <button id="rg-clear-btn" onclick="rgDiagClear()" style="display:none"
                class="text-xs text-gray-400 hover:text-gray-700 px-3 py-1.5 rounded-xl border border-gray-200 hover:border-gray-300 transition-colors">
                ✕ Clear
            </button>
            {{-- Legend --}}
            <div id="rg-legend" class="ml-auto flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-400">
                <span class="flex items-center gap-1.5"><span class="inline-block w-5 h-px bg-indigo-400"></span>hasMany</span>
                <span class="flex items-center gap-1.5"><span class="inline-block w-5 h-px bg-teal-400"></span>hasOne</span>
                <span class="flex items-center gap-1.5"><span class="inline-block w-5 h-px bg-emerald-400"></span>belongsTo</span>
                <span class="flex items-center gap-1.5"><span class="inline-block w-5 h-px bg-violet-400"></span>M:M</span>
            </div>
            {{-- Selected node info --}}
            <div id="rg-info-row" class="hidden ml-auto flex items-center gap-2">
                <span id="rg-info-name"  class="font-black text-indigo-900 text-xs"></span>
                <span id="rg-info-table" class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-lg font-mono"></span>
                <button id="rg-rels-btn" onclick="rgToggleRels()"
                    class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-xl border bg-white border-indigo-200 text-indigo-600 hover:bg-indigo-50 transition-colors shadow-sm">
                    <span id="rg-info-count"></span>
                    <svg id="rg-rels-chevron" class="w-3 h-3 transition-transform" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 4l4 4 4-4"/></svg>
                </button>
            </div>
        </div>

        {{-- Relationship cards panel --}}
        <div id="rg-rels-panel" class="hidden mb-3 bg-white border border-indigo-100 rounded-2xl p-4 shadow-sm">
            <p id="rg-rels-title" class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3"></p>
            <div id="rg-rels-cards" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2"></div>
        </div>

        {{-- Canvas --}}
        <div class="relative rounded-2xl border border-gray-200 shadow-sm overflow-hidden" style="background:#f8fafc">
            <svg id="rg-canvas" xmlns="http://www.w3.org/2000/svg"
                 style="width:100%;height:600px;display:block;cursor:grab;user-select:none">
                <defs>
                    <pattern id="rg-dot-grid" width="24" height="24" patternUnits="userSpaceOnUse">
                        <circle cx="1" cy="1" r="1" fill="#cbd5e1" opacity="0.5"/>
                    </pattern>
                    <marker id="rg-arr-many"      viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#818cf8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <marker id="rg-arr-one"       viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#2dd4bf" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <marker id="rg-arr-belongs"   viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#34d399" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <marker id="rg-arr-mm"        viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#c084fc" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <marker id="rg-arr-many-a"    viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <marker id="rg-arr-one-a"     viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#14b8a6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <marker id="rg-arr-belongs-a" viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <marker id="rg-arr-mm-a"      viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#a855f7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <filter id="rg-f-node"     x="-20%" y="-30%" width="140%" height="160%"><feDropShadow dx="0" dy="2" stdDeviation="4"  flood-color="rgba(15,23,42,0.10)"/></filter>
                    <filter id="rg-f-node-sel" x="-20%" y="-30%" width="140%" height="160%"><feDropShadow dx="0" dy="4" stdDeviation="10" flood-color="rgba(99,102,241,0.30)"/></filter>
                    <filter id="rg-f-node-rel" x="-20%" y="-30%" width="140%" height="160%"><feDropShadow dx="0" dy="3" stdDeviation="7"  flood-color="rgba(16,185,129,0.25)"/></filter>
                </defs>
                <rect width="100%" height="100%" fill="url(#rg-dot-grid)"/>
                <g id="rg-vp">
                    <g id="rg-edges-g"></g>
                    <g id="rg-nodes-g"></g>
                </g>
            </svg>

            {{-- Zoom controls --}}
            <div class="absolute top-3 right-3 flex items-center gap-1">
                <button onclick="graphZoom(1.25)" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 shadow-sm font-bold transition-colors text-base leading-none">+</button>
                <button onclick="graphZoom(0.8)"  class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 shadow-sm font-bold transition-colors text-base leading-none">−</button>
                <button onclick="graphFit()"      class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 shadow-sm transition-colors text-sm" title="Fit to screen">⊡</button>
                <button onclick="graphReset()"    class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 shadow-sm transition-colors" title="Reset">⟳</button>
            </div>

            {{-- Minimap --}}
            <div class="absolute bottom-3 right-3 rounded-xl border border-gray-200 bg-white/90 backdrop-blur shadow-md overflow-hidden" style="width:160px;height:100px">
                <svg id="rg-minimap" width="160" height="100" style="display:block"></svg>
            </div>

            {{-- Hint --}}
            <div class="absolute bottom-3 left-3 text-xs text-slate-400 bg-white/80 backdrop-blur px-2.5 py-1 rounded-lg border border-gray-100 shadow-sm pointer-events-none">
                Click node · Drag to pan · Scroll to zoom
            </div>
        </div>

    </div>

    {{-- ── TAB: Tree View ── --}}
    <div id="map-tree" style="display:none">
        <div class="mb-4">
            <input id="map-search" oninput="filterModelTree()" type="search" placeholder="Filter models…"
                class="border border-slate-200 rounded-lg px-3 py-2 text-sm w-52 focus:outline-none focus:ring-2 focus:ring-indigo-300">
        </div>
        <div id="map-tree-content" class="space-y-4"></div>
    </div>

    {{-- ── TAB: ER Diagram ── --}}
    <div id="map-er" style="display:none">
        @if(empty($mmErPairs) && empty($mmStandalone))
        <div class="bg-white rounded-xl p-12 text-center border border-slate-100">
            <p class="text-slate-400">No relationships found across models.</p>
        </div>
        @else
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 overflow-auto">
            <div class="flex flex-wrap gap-4 mb-4 text-xs text-slate-500">
                <span><code>||--o{</code> hasMany</span><span><code>||--o|</code> hasOne</span>
                <span><code>}o--||</code> belongsTo</span><span><code>}o--o{</code> belongsToMany</span>
            </div>
            <div class="mermaid" id="er-diagram">{{ $mmErCode }}</div>
        </div>
        @endif
    </div>

</section>

{{-- Routes --}}
<section id="sec-routes" class="p-6" style="display:none">

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
    @endphp

    {{-- List view --}}
    <div id="routes-list">

        {{-- Header --}}
        <div class="flex flex-wrap items-start justify-between gap-4 mb-5">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Route Explorer</h1>
                <p class="text-slate-500 text-sm mt-0.5">{{ $rs['total']??0 }} routes · click any row to explore its full pipeline</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input id="routes-search" oninput="filterRoutes()" type="search" placeholder="Search URI or handler…"
                        class="border border-slate-200 rounded-xl pl-8 pr-3 py-2 text-xs w-52 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300">
                </div>
                <select id="routes-method-filter" onchange="filterRoutes()"
                    class="border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white">
                    <option value="">All Methods</option>
                    @foreach(array_keys($rs['by_method']??[]) as $m)<option value="{{ strtoupper($m) }}">{{ strtoupper($m) }}</option>@endforeach
                </select>
                <select id="routes-mw-filter" onchange="filterRoutes()"
                    class="border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white max-w-[180px]">
                    <option value="">All Middleware</option>
                    @foreach(array_keys($rs['middleware_usage']??[]) as $mw)
                    <option value="{{ $mw }}">{{ class_basename($mw) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Method stats pills --}}
        @if(!empty($routeMethodCounts))
        <div class="flex flex-wrap gap-2 mb-5">
            @foreach($routeMethodCounts as $method => $cnt)
            @php $ms = $routeMethodStyle[$method] ?? 'bg-slate-500 text-white'; @endphp
            <button onclick="document.getElementById('routes-method-filter').value='{{ $method }}'; filterRoutes();"
                class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-3 py-1.5 shadow-sm hover:shadow-md transition-shadow cursor-pointer">
                <span class="text-xs font-bold px-2 py-0.5 rounded-md {{ $ms }}">{{ $method }}</span>
                <span class="text-sm font-semibold text-slate-700">{{ $cnt }}</span>
            </button>
            @endforeach
            <button onclick="document.getElementById('routes-method-filter').value=''; filterRoutes();"
                class="flex items-center gap-2 bg-slate-800 border border-slate-700 rounded-xl px-3 py-1.5 shadow-sm hover:shadow-md transition-shadow cursor-pointer">
                <span class="text-xs font-bold text-slate-300">ALL</span>
                <span class="text-sm font-semibold text-white">{{ $rs['total']??0 }}</span>
            </button>
        </div>
        @endif

        {{-- Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider w-24">Method</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">URI</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider w-52">Handler</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider w-44">Middleware</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider w-52">Route Name</th>
                        <th class="w-8"></th>
                    </tr>
                </thead>
                <tbody id="routes-tbody" class="divide-y divide-slate-100">
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
                    <tr class="route-row hover:bg-slate-50 cursor-pointer transition-colors group"
                        onclick="showRouteDetail({{ $i }})"
                        data-uri="{{ strtolower($route['uri']??'') }}"
                        data-methods="{{ implode(',',array_map('strtoupper',$methods)) }}"
                        data-mw="{{ $mwsRaw }}">

                        {{-- Method --}}
                        <td class="px-4 py-2.5">
                            <div class="flex flex-wrap gap-1">
                                @foreach($methods as $m)
                                <span class="text-xs px-2 py-0.5 rounded-md font-bold method-{{ strtolower($m) }}">{{ $m }}</span>
                                @endforeach
                            </div>
                        </td>

                        {{-- URI --}}
                        <td class="px-4 py-2.5">
                            <code class="text-xs font-mono text-slate-700">{{ $route['uri'] }}</code>
                        </td>

                        {{-- Handler --}}
                        <td class="px-4 py-2.5 text-xs">
                            @if($ctrl)
                                <span class="font-semibold text-slate-800">{{ $ctrl }}</span>
                                @if(!$isInvokable)
                                    <span class="text-slate-300 mx-0.5">@</span><span class="text-slate-500">{{ $action }}</span>
                                @endif
                            @else
                                <span class="text-slate-400 italic">Closure</span>
                            @endif
                        </td>

                        {{-- Middleware — compact single line --}}
                        <td class="px-4 py-2.5">
                            @if($mwCount === 0)
                                <span class="text-xs text-slate-300">—</span>
                            @else
                                <div class="flex items-center gap-1.5" title="{{ $fullMwTitle }}">
                                    <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md font-mono border border-slate-200 truncate max-w-[100px]" title="{{ $allMws[0] }}">{{ $primaryMw }}</span>
                                    @if($mwCount > 1)
                                    <span class="text-xs text-slate-400 bg-slate-50 border border-slate-200 px-1.5 py-0.5 rounded-md whitespace-nowrap shrink-0" title="{{ $fullMwTitle }}">+{{ $mwCount - 1 }}</span>
                                    @endif
                                </div>
                            @endif
                        </td>

                        {{-- Route name --}}
                        <td class="px-4 py-2.5 text-xs text-slate-400 font-mono">
                            @if($routeName)
                                <span class="truncate block max-w-[200px]" title="{{ $routeName }}">{{ $routeName }}</span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>

                        {{-- Chevron --}}
                        <td class="pr-4 py-2.5 text-right">
                            <svg class="w-4 h-4 text-slate-300 group-hover:text-slate-500 transition-colors inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Detail view --}}
    <div id="routes-detail" style="display:none">
        <button onclick="showList('routes')" class="flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Route Explorer
        </button>
        <div id="routes-detail-content"></div>
    </div>

</section>

{{-- ══ API DOCUMENTATION ══ --}}
<section id="sec-apidocs" class="p-6" style="display:none">

    @php
    $apiDocs   = $data['api_docs'] ?? [];
    $apiGroups = [];
    foreach ($apiDocs as $ep) {
        $apiGroups[$ep['group']][] = $ep;
    }
    ksort($apiGroups);

    $methodStyle = [
        'GET'    => ['pill' => 'bg-emerald-500 text-white', 'border' => 'border-l-emerald-500', 'glow' => 'bg-emerald-50'],
        'POST'   => ['pill' => 'bg-blue-500 text-white',    'border' => 'border-l-blue-500',    'glow' => 'bg-blue-50'],
        'PUT'    => ['pill' => 'bg-amber-500 text-white',   'border' => 'border-l-amber-500',   'glow' => 'bg-amber-50'],
        'PATCH'  => ['pill' => 'bg-orange-500 text-white',  'border' => 'border-l-orange-500',  'glow' => 'bg-orange-50'],
        'DELETE' => ['pill' => 'bg-red-500 text-white',     'border' => 'border-l-red-500',     'glow' => 'bg-red-50'],
    ];
    $defaultStyle = ['pill' => 'bg-slate-500 text-white', 'border' => 'border-l-slate-400', 'glow' => 'bg-slate-50'];

    $statusCls = fn(int $code) => match(true) {
        $code < 300 => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
        $code < 500 => 'bg-red-100 text-red-700 border border-red-200',
        default     => 'bg-orange-100 text-orange-700 border border-orange-200',
    };

    $typeColor = [
        'string'  => 'text-sky-600 bg-sky-50',
        'integer' => 'text-violet-600 bg-violet-50',
        'number'  => 'text-violet-600 bg-violet-50',
        'boolean' => 'text-amber-600 bg-amber-50',
        'array'   => 'text-teal-600 bg-teal-50',
        'file'    => 'text-pink-600 bg-pink-50',
        'email'   => 'text-sky-600 bg-sky-50',
        'url'     => 'text-sky-600 bg-sky-50',
        'date'    => 'text-orange-600 bg-orange-50',
        'uuid'    => 'text-slate-600 bg-slate-100',
        'enum'    => 'text-purple-600 bg-purple-50',
    ];

    $groupLabel = fn(string $g) => ucwords(str_replace(['-', '_'], ' ', $g));

    $methodCounts = array_fill_keys(['GET','POST','PUT','PATCH','DELETE'], 0);
    foreach ($apiDocs as $ep) {
        $m = strtoupper($ep['method'] ?? '');
        if (isset($methodCounts[$m])) $methodCounts[$m]++;
    }
    @endphp

    {{-- Page header --}}
    <div class="mb-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">API Documentation</h1>
                <p class="text-slate-500 text-sm mt-0.5">
                    {{ count($apiDocs) }} endpoint{{ count($apiDocs) !== 1 ? 's' : '' }} across
                    {{ count($apiGroups) }} resource{{ count($apiGroups) !== 1 ? 's' : '' }} · auto-generated from routes
                </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input id="api-search" type="text" placeholder="Search endpoints…" oninput="apiSearch(this.value)"
                        class="text-xs bg-white border border-slate-200 rounded-xl pl-8 pr-3 py-2 w-52 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300">
                </div>
                <div class="flex gap-1" id="api-method-filters">
                    @foreach(['ALL','GET','POST','PUT','PATCH','DELETE'] as $m)
                    <button onclick="apiFilter('{{ $m }}')" data-method="{{ $m }}"
                        class="api-filter-btn text-xs px-2.5 py-1.5 rounded-lg border font-bold tracking-wide transition-all
                        {{ $m === 'ALL' ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-500 border-slate-200 hover:border-slate-400 hover:text-slate-700' }}">
                        {{ $m }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Method stats bar --}}
        @if(!empty($apiDocs))
        <div class="flex flex-wrap gap-3 mt-4">
            @foreach($methodCounts as $method => $cnt)
            @if($cnt > 0)
            @php $ms = $methodStyle[$method] ?? $defaultStyle; @endphp
            <div class="flex items-center gap-2 bg-white border border-slate-100 rounded-xl px-3 py-2 shadow-sm">
                <span class="text-xs font-bold px-2 py-0.5 rounded-md {{ $ms['pill'] }}">{{ $method }}</span>
                <span class="text-sm font-semibold text-slate-700">{{ $cnt }}</span>
            </div>
            @endif
            @endforeach
        </div>
        @endif
    </div>

    @if(empty($apiDocs))
    <div class="bg-white rounded-2xl p-16 text-center border border-slate-100 shadow-sm">
        <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <p class="font-semibold text-slate-600 mb-2">No API routes found</p>
        <p class="text-sm text-slate-400">Define routes under the <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs">api/</code> prefix or apply the <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs">api</code> middleware group.</p>
    </div>
    @else

    {{-- Two-column layout: sticky nav + endpoint list --}}
    <div class="flex gap-6 items-start">

        {{-- Left: group navigation --}}
        <nav class="hidden lg:flex flex-col gap-1 w-48 shrink-0 sticky top-6">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1 px-2">Resources</p>
            @foreach($apiGroups as $groupName => $endpoints)
            <a href="#api-group-{{ $groupName }}" onclick="apiScrollTo('{{ $groupName }}')"
               class="api-nav-item flex items-center justify-between px-3 py-2 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors cursor-pointer">
                <span class="truncate">{{ $groupLabel($groupName) }}</span>
                <span class="text-xs bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded-full shrink-0 ml-1">{{ count($endpoints) }}</span>
            </a>
            @endforeach
        </nav>

        {{-- Right: endpoint groups --}}
        <div id="api-groups-container" class="flex-1 min-w-0 space-y-5">
        @foreach($apiGroups as $groupName => $endpoints)
        <div class="api-group" id="api-group-{{ $groupName }}" data-group="{{ $groupName }}">

            {{-- Group header --}}
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-base leading-tight">{{ $groupLabel($groupName) }}</h3>
                        <p class="text-xs text-slate-400 font-mono">/{{ $groupName }}</p>
                    </div>
                </div>
                <span class="text-xs bg-slate-100 text-slate-500 px-2.5 py-0.5 rounded-full font-medium">
                    {{ count($endpoints) }} endpoint{{ count($endpoints) !== 1 ? 's' : '' }}
                </span>
            </div>

            {{-- Endpoint cards --}}
            <div class="rounded-2xl border border-slate-200 overflow-hidden shadow-sm bg-white">
            @foreach($endpoints as $epIdx => $ep)
            @php
                $ms      = $methodStyle[$ep['method']] ?? $defaultStyle;
                $hasBody = !empty($ep['body_params']);
                $hasPath = !empty($ep['path_params']);
                $uid     = $groupName . '_' . $epIdx;
                $handler = $ep['controller'] . '@' . $ep['action'];
            @endphp
            <div class="api-endpoint-wrap border-b border-slate-100 last:border-0"
                 data-method="{{ $ep['method'] }}" data-uri="{{ strtolower($ep['uri']) }}">

                {{-- Collapsed row --}}
                <div onclick="apiToggle('{{ $uid }}')"
                     class="flex items-center gap-3 px-4 py-3.5 cursor-pointer hover:{{ $ms['glow'] }} transition-colors select-none group border-l-4 {{ $ms['border'] }}">

                    {{-- Method badge --}}
                    <span class="shrink-0 text-xs font-bold w-[68px] text-center py-1.5 rounded-lg {{ $ms['pill'] }} tracking-wide shadow-sm">
                        {{ $ep['method'] }}
                    </span>

                    {{-- URI + description --}}
                    <div class="flex-1 min-w-0">
                        <code class="text-sm font-mono text-slate-800 font-semibold">{{ $ep['uri'] }}</code>
                        @if($ep['name'])
                        <p class="text-xs text-slate-400 font-mono mt-0.5 truncate">{{ $ep['name'] }}</p>
                        @endif
                    </div>

                    {{-- Badges --}}
                    <div class="flex items-center gap-2 shrink-0">
                        @if($ep['auth_required'])
                        <span class="flex items-center gap-1 text-xs text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200 font-medium">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Auth
                        </span>
                        @endif
                        @if($hasBody)
                        <span class="text-xs text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-100 font-medium hidden sm:inline">Body</span>
                        @endif
                        @if($hasPath)
                        <span class="text-xs text-teal-600 bg-teal-50 px-2 py-0.5 rounded-full border border-teal-100 font-medium hidden sm:inline">Params</span>
                        @endif
                        <span class="text-xs text-slate-400 font-mono hidden xl:block truncate max-w-48" title="{{ $handler }}">{{ $handler }}</span>
                        <svg id="chevron-{{ $uid }}" class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                {{-- Expanded detail panel --}}
                <div id="detail-{{ $uid }}" class="hidden border-t border-slate-100">

                    {{-- Detail header bar --}}
                    <div class="flex items-center gap-3 px-5 py-3 bg-slate-900 text-slate-300 text-xs font-mono">
                        <span class="font-bold text-xs px-2 py-0.5 rounded {{ $ms['pill'] }}">{{ $ep['method'] }}</span>
                        <span class="text-slate-200 font-semibold">{{ $ep['uri'] }}</span>
                        <span class="ml-auto text-slate-500">{{ $handler }}</span>
                    </div>

                    <div class="px-5 py-5 bg-slate-50">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                            {{-- Left column: parameters --}}
                            <div class="space-y-5">

                                {{-- Path parameters --}}
                                @if($hasPath)
                                <div>
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="w-1.5 h-4 rounded-full bg-teal-500 shrink-0"></span>
                                        <p class="text-xs font-bold text-slate-700 uppercase tracking-widest">Path Parameters</p>
                                    </div>
                                    <div class="rounded-xl border border-slate-200 overflow-hidden bg-white">
                                        <table class="w-full text-xs">
                                            <thead>
                                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500">
                                                    <th class="px-3 py-2 text-left font-semibold">Name</th>
                                                    <th class="px-3 py-2 text-left font-semibold">Type</th>
                                                    <th class="px-3 py-2 text-left font-semibold">Required</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                            @foreach($ep['path_params'] as $pp)
                                            <tr class="hover:bg-slate-50">
                                                <td class="px-3 py-2.5 font-mono font-bold text-slate-800">{{ $pp['name'] }}</td>
                                                <td class="px-3 py-2.5">
                                                    <span class="font-mono text-xs px-1.5 py-0.5 rounded {{ $typeColor[$pp['type']] ?? 'text-slate-600 bg-slate-100' }}">{{ $pp['type'] }}</span>
                                                </td>
                                                <td class="px-3 py-2.5">
                                                    @if($pp['required'])
                                                    <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-semibold">required</span>
                                                    @else
                                                    <span class="bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full text-xs">optional</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endif

                                {{-- Body parameters --}}
                                @if($hasBody)
                                <div>
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="w-1.5 h-4 rounded-full bg-indigo-500 shrink-0"></span>
                                        <p class="text-xs font-bold text-slate-700 uppercase tracking-widest">Request Body</p>
                                        @if($ep['request_class'])
                                        <span class="ml-1 font-mono text-xs text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-100">{{ $ep['request_class'] }}</span>
                                        @endif
                                    </div>
                                    <div class="rounded-xl border border-slate-200 overflow-hidden bg-white">
                                        <table class="w-full text-xs">
                                            <thead>
                                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500">
                                                    <th class="px-3 py-2 text-left font-semibold">Field</th>
                                                    <th class="px-3 py-2 text-left font-semibold">Type</th>
                                                    <th class="px-3 py-2 text-left font-semibold">Required</th>
                                                    <th class="px-3 py-2 text-left font-semibold">Rules</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                            @foreach($ep['body_params'] as $bp)
                                            <tr class="hover:bg-slate-50">
                                                <td class="px-3 py-2.5 font-mono font-bold text-slate-800">{{ $bp['field'] }}</td>
                                                <td class="px-3 py-2.5">
                                                    <span class="font-mono text-xs px-1.5 py-0.5 rounded {{ $typeColor[$bp['type']] ?? 'text-slate-600 bg-slate-100' }}">{{ $bp['type'] }}</span>
                                                </td>
                                                <td class="px-3 py-2.5">
                                                    @if($bp['required'])
                                                    <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-semibold">required</span>
                                                    @elseif($bp['nullable'])
                                                    <span class="bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full text-xs">nullable</span>
                                                    @else
                                                    <span class="bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full text-xs">optional</span>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2.5 font-mono text-slate-400 text-xs truncate max-w-0" title="{{ $bp['rules'] }}">{{ $bp['rules'] }}</td>
                                            </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endif

                                @if(!$hasPath && !$hasBody)
                                <div class="flex items-center gap-3 p-4 bg-white rounded-xl border border-dashed border-slate-200">
                                    <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-xs text-slate-400">No parameters detected for this endpoint.</p>
                                </div>
                                @endif
                            </div>

                            {{-- Right column: responses + meta --}}
                            <div class="space-y-5">

                                {{-- Responses --}}
                                <div>
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="w-1.5 h-4 rounded-full bg-emerald-500 shrink-0"></span>
                                        <p class="text-xs font-bold text-slate-700 uppercase tracking-widest">Responses</p>
                                    </div>
                                    <div class="space-y-1.5">
                                        @foreach($ep['responses'] as $code => $label)
                                        <div class="flex items-center gap-3 bg-white rounded-xl border border-slate-200 px-3 py-2.5">
                                            <span class="text-xs font-mono font-bold px-2 py-1 rounded-lg {{ $statusCls((int)$code) }}">{{ $code }}</span>
                                            <span class="text-xs text-slate-600">{{ $label }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Middleware --}}
                                @if(!empty($ep['middleware']))
                                <div>
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="w-1.5 h-4 rounded-full bg-slate-400 shrink-0"></span>
                                        <p class="text-xs font-bold text-slate-700 uppercase tracking-widest">Middleware</p>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($ep['middleware'] as $mw)
                                        <span class="text-xs bg-white border border-slate-200 text-slate-700 px-2.5 py-1 rounded-xl font-mono shadow-sm">{{ $mw }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                {{-- Route name --}}
                                @if($ep['name'])
                                <div>
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="w-1.5 h-4 rounded-full bg-violet-400 shrink-0"></span>
                                        <p class="text-xs font-bold text-slate-700 uppercase tracking-widest">Route Name</p>
                                    </div>
                                    <code class="text-xs text-violet-700 bg-violet-50 border border-violet-100 px-3 py-1.5 rounded-xl inline-block">{{ $ep['name'] }}</code>
                                </div>
                                @endif

                            </div>{{-- /right --}}
                        </div>{{-- /grid --}}
                    </div>{{-- /bg-slate-50 --}}

                    {{-- Request Flow graph --}}
                    <div id="api-flow-{{ $uid }}"
                         class="px-5 pb-5 bg-white"
                         data-controller="{{ $ep['controller'] }}"
                         data-action="{{ $ep['action'] }}"
                         data-method="{{ $ep['method'] }}"
                         data-uri="{{ $ep['uri'] }}"
                         data-rname="{{ $ep['name'] ?? '' }}"
                         data-mws="{{ json_encode($ep['middleware'] ?? []) }}">
                    </div>

                </div>{{-- /expanded panel --}}

            </div>{{-- /endpoint-wrap --}}
            @endforeach
            </div>{{-- /endpoint cards --}}
        </div>{{-- /api-group --}}
        @endforeach
        </div>{{-- /api-groups-container --}}

    </div>{{-- /two-column --}}
    @endif

</section>

{{-- Jobs --}}
<section id="sec-jobs" class="p-6" style="display:none">
    <div id="jobs-list">
        <div class="flex items-center justify-between mb-6">
            <div><h1 class="text-2xl font-bold">Jobs</h1><p class="text-slate-500 text-sm">{{ count($data['jobs']) }} queued jobs</p></div>
            <input id="jobs-search" oninput="filterGrid('jobs')" type="search" placeholder="Search…" class="border border-slate-200 rounded-lg px-3 py-2 text-sm w-44 focus:outline-none focus:ring-2 focus:ring-indigo-300">
        </div>
        @if(empty($data['jobs']))
        <div class="bg-white rounded-xl p-12 text-center border border-slate-100"><p class="text-slate-400">No jobs found in <code class="bg-slate-100 px-1 rounded">app/Jobs</code></p></div>
        @else
        <div id="jobs-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($data['jobs'] as $i => $job)
            <div class="card bg-white rounded-xl shadow-sm border border-slate-100 p-4 cursor-pointer" onclick="showDetail('jobs',{{$i}})" data-name="{{ strtolower($job['name']) }}">
                <div class="flex items-start justify-between mb-2">
                    <p class="font-semibold">{{ $job['name'] }}</p>
                    @if($job['queued']??false)<span class="text-xs bg-amber-50 text-amber-600 px-2 py-0.5 rounded-full">Queued</span>@endif
                </div>
                <p class="text-xs text-slate-500 mb-2">Queue: <span class="font-medium text-slate-700">{{ $job['queue']??'default' }}</span></p>
                <div class="flex gap-3 text-xs text-slate-500">
                    @if($job['tries']??null)<span>Tries: {{ $job['tries'] }}</span>@endif
                    @if($job['timeout']??null)<span>Timeout: {{ $job['timeout'] }}s</span>@endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="jobs-detail" style="display:none">
        <button onclick="showList('jobs')" class="flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Back to Jobs
        </button>
        <div id="jobs-detail-content"></div>
    </div>
</section>

{{-- Events --}}
<section id="sec-events" class="p-6" style="display:none">
    <div id="events-list">
        <div class="flex items-center justify-between mb-6">
            <div><h1 class="text-2xl font-bold">Events</h1><p class="text-slate-500 text-sm">{{ count($data['events']) }} events</p></div>
            <input id="events-search" oninput="filterGrid('events')" type="search" placeholder="Search…" class="border border-slate-200 rounded-lg px-3 py-2 text-sm w-44 focus:outline-none focus:ring-2 focus:ring-indigo-300">
        </div>
        @if(empty($data['events']))
        <div class="bg-white rounded-xl p-12 text-center border border-slate-100"><p class="text-slate-400">No events found in <code class="bg-slate-100 px-1 rounded">app/Events</code></p></div>
        @else
        <div id="events-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($data['events'] as $i => $evt)
            <div class="card bg-white rounded-xl shadow-sm border border-slate-100 p-4 cursor-pointer" onclick="showDetail('events',{{$i}})" data-name="{{ strtolower($evt['name']) }}">
                <div class="flex items-start justify-between mb-2">
                    <p class="font-semibold">{{ $evt['name'] }}</p>
                    @if($evt['broadcasts']??false)<span class="text-xs bg-pink-50 text-pink-600 px-2 py-0.5 rounded-full">Broadcast</span>@endif
                </div>
                <p class="text-xs text-slate-400 font-mono truncate">{{ $evt['namespace'] }}</p>
                @if(!empty($evt['properties']))<p class="text-xs text-slate-500 mt-2">{{ count($evt['properties']) }} payload props</p>@endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="events-detail" style="display:none">
        <button onclick="showList('events')" class="flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Back to Events
        </button>
        <div id="events-detail-content"></div>
    </div>
</section>

{{-- Services --}}
<section id="sec-services" class="p-6" style="display:none">
    <div id="services-list">
        <div class="flex items-center justify-between mb-6">
            <div><h1 class="text-2xl font-bold">Services</h1><p class="text-slate-500 text-sm">{{ count($data['services']) }} service classes</p></div>
            <input id="services-search" oninput="filterGrid('services')" type="search" placeholder="Search…" class="border border-slate-200 rounded-lg px-3 py-2 text-sm w-44 focus:outline-none focus:ring-2 focus:ring-indigo-300">
        </div>
        @if(empty($data['services']))
        <div class="bg-white rounded-xl p-12 text-center border border-slate-100"><p class="text-slate-400">No services found in <code class="bg-slate-100 px-1 rounded">app/Services</code></p></div>
        @else
        <div id="services-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($data['services'] as $i => $svc)
            <div class="card bg-white rounded-xl shadow-sm border border-slate-100 p-4 cursor-pointer" onclick="showDetail('services',{{$i}})" data-name="{{ strtolower($svc['name']) }}">
                <p class="font-semibold mb-1">{{ $svc['name'] }}</p>
                <p class="text-xs text-slate-400 font-mono truncate mb-2">{{ $svc['namespace'] }}</p>
                <div class="flex gap-3 text-xs text-slate-500">
                    <span>{{ count($svc['methods']??[]) }} methods</span>
                    @if(!empty($svc['dependencies']))<span>{{ count($svc['dependencies']) }} deps</span>@endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="services-detail" style="display:none">
        <button onclick="showList('services')" class="flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Back to Services
        </button>
        <div id="services-detail-content"></div>
    </div>
</section>

{{-- Repositories --}}
<section id="sec-repositories" class="p-6" style="display:none">
    <div id="repositories-list">
        <div class="flex items-center justify-between mb-6">
            <div><h1 class="text-2xl font-bold">Repositories</h1><p class="text-slate-500 text-sm">{{ count($data['repositories']) }} repositories</p></div>
            <input id="repositories-search" oninput="filterGrid('repositories')" type="search" placeholder="Search…" class="border border-slate-200 rounded-lg px-3 py-2 text-sm w-44 focus:outline-none focus:ring-2 focus:ring-indigo-300">
        </div>
        @if(empty($data['repositories']))
        <div class="bg-white rounded-xl p-12 text-center border border-slate-100"><p class="text-slate-400">No repositories found in <code class="bg-slate-100 px-1 rounded">app/Repositories</code></p></div>
        @else
        <div id="repositories-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($data['repositories'] as $i => $repo)
            <div class="card bg-white rounded-xl shadow-sm border border-slate-100 p-4 cursor-pointer" onclick="showDetail('repositories',{{$i}})" data-name="{{ strtolower($repo['name']) }}">
                <p class="font-semibold mb-1">{{ $repo['name'] }}</p>
                <p class="text-xs text-slate-400 font-mono truncate mb-2">{{ $repo['namespace'] }}</p>
                <div class="flex gap-3 text-xs text-slate-500">
                    <span>{{ count($repo['methods']??[]) }} methods</span>
                    @if(!empty($repo['dependencies']))<span>{{ count($repo['dependencies']) }} deps</span>@endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="repositories-detail" style="display:none">
        <button onclick="showList('repositories')" class="flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Back to Repositories
        </button>
        <div id="repositories-detail-content"></div>
    </div>
</section>

{{-- Observers --}}
<section id="sec-observers" class="p-6" style="display:none">
    <div id="observers-list">
        <div class="flex items-center justify-between mb-6">
            <div><h1 class="text-2xl font-bold">Observers</h1><p class="text-slate-500 text-sm">{{ count($data['observers']) }} observers</p></div>
            <input id="observers-search" oninput="filterGrid('observers')" type="search" placeholder="Search…" class="border border-slate-200 rounded-lg px-3 py-2 text-sm w-44 focus:outline-none focus:ring-2 focus:ring-indigo-300">
        </div>
        @if(empty($data['observers']))
        <div class="bg-white rounded-xl p-12 text-center border border-slate-100"><p class="text-slate-400">No observers found in <code class="bg-slate-100 px-1 rounded">app/Observers</code></p></div>
        @else
        <div id="observers-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($data['observers'] as $i => $obs)
            <div class="card bg-white rounded-xl shadow-sm border border-slate-100 p-4 cursor-pointer" onclick="showDetail('observers',{{$i}})" data-name="{{ strtolower($obs['name']) }}">
                <p class="font-semibold mb-1">{{ $obs['name'] }}</p>
                <p class="text-xs text-slate-500 mb-2">Observes: <span class="font-medium text-slate-700">{{ $obs['observes']??'Unknown' }}</span></p>
                <div class="flex flex-wrap gap-1">
                    @foreach($obs['events']??[] as $e)<span class="text-xs bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded">{{ $e }}</span>@endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="observers-detail" style="display:none">
        <button onclick="showList('observers')" class="flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Back to Observers
        </button>
        <div id="observers-detail-content"></div>
    </div>
</section>

{{-- Policies --}}
<section id="sec-policies" class="p-6" style="display:none">
    <div id="policies-list">
        <div class="flex items-center justify-between mb-6">
            <div><h1 class="text-2xl font-bold">Policies</h1><p class="text-slate-500 text-sm">{{ count($data['policies']) }} policies</p></div>
            <input id="policies-search" oninput="filterGrid('policies')" type="search" placeholder="Search…" class="border border-slate-200 rounded-lg px-3 py-2 text-sm w-44 focus:outline-none focus:ring-2 focus:ring-indigo-300">
        </div>
        @if(empty($data['policies']))
        <div class="bg-white rounded-xl p-12 text-center border border-slate-100"><p class="text-slate-400">No policies found in <code class="bg-slate-100 px-1 rounded">app/Policies</code></p></div>
        @else
        <div id="policies-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($data['policies'] as $i => $pol)
            <div class="card bg-white rounded-xl shadow-sm border border-slate-100 p-4 cursor-pointer" onclick="showDetail('policies',{{$i}})" data-name="{{ strtolower($pol['name']) }}">
                <p class="font-semibold mb-1">{{ $pol['name'] }}</p>
                <p class="text-xs text-slate-500 mb-2">Guards: <span class="font-medium text-slate-700">{{ $pol['model']??'Unknown' }}</span></p>
                <div class="flex flex-wrap gap-1">
                    @foreach($pol['actions']??[] as $a)<span class="text-xs bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded font-mono">{{ $a }}</span>@endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="policies-detail" style="display:none">
        <button onclick="showList('policies')" class="flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Back to Policies
        </button>
        <div id="policies-detail-content"></div>
    </div>
</section>

{{-- Dependencies --}}
<section id="sec-dependencies" class="p-6" style="display:none">
    <h1 class="text-2xl font-bold mb-1">Dependency Graph</h1>
    <p class="text-slate-500 text-sm mb-6">{{ count($data['dependencies']['nodes']??[]) }} nodes · {{ count($data['dependencies']['edges']??[]) }} edges — how your classes connect across layers</p>

    @php
    $depNodes = $data['dependencies']['nodes'] ?? [];
    $depEdges = $data['dependencies']['edges'] ?? [];

    // Layer order (top → bottom in the TD diagram)
    $lOrder  = ['controller','job','event','listener','service','repository','model','database'];
    $lLabels = [
        'controller' => 'Controllers',
        'job'        => 'Jobs',
        'event'      => 'Events',
        'listener'   => 'Listeners',
        'service'    => 'Services',
        'repository' => 'Repositories',
        'model'      => 'Models',
        'database'   => 'Database',
    ];
    $byLayer = array_fill_keys($lOrder, []);
    foreach ($depNodes as $n) {
        $l = $n['layer'] ?? 'model';
        if (isset($byLayer[$l])) $byLayer[$l][] = $n['name'];
    }

    // Edge label mapping
    $edgeLabel = ['injects' => '', 'uses' => 'uses', 'triggers' => 'triggers', 'persists' => 'persists'];

    // Build flowchart TD
    $fLines = ['flowchart TD'];

    foreach ($lOrder as $l) {
        if (empty($byLayer[$l])) continue;
        if ($l === 'database') {
            // Database uses cylinder shape — defined inline in edges, not in subgraph
            continue;
        }
        $fLines[] = '    subgraph ' . $lLabels[$l];
        foreach ($byLayer[$l] as $nm) { $fLines[] = '        ' . $nm; }
        $fLines[] = '    end';
    }

    // Database node (cylinder shape) — add outside subgraphs
    if (!empty($byLayer['database'])) {
        $fLines[] = '    Database[("Database")]';
    }

    // Edges with optional labels
    foreach ($depEdges as $e) {
        $label = $edgeLabel[$e['type'] ?? ''] ?? '';
        $arrow = $label ? "-->|\"{$label}\"|" : '-->';
        $fLines[] = "    {$e['from']} {$arrow} {$e['to']}";
    }

    // Class styles for each layer
    foreach ($depNodes as $n) {
        $fLines[] = "    class {$n['name']} {$n['layer']}";
    }

    $fLines[] = '    classDef controller fill:#dbeafe,stroke:#3b82f6,color:#1e3a8a';
    $fLines[] = '    classDef service    fill:#d1fae5,stroke:#10b981,color:#064e3b';
    $fLines[] = '    classDef repository fill:#fef3c7,stroke:#f59e0b,color:#78350f';
    $fLines[] = '    classDef model      fill:#ede9fe,stroke:#8b5cf6,color:#4c1d95';
    $fLines[] = '    classDef job        fill:#fef9c3,stroke:#ca8a04,color:#713f12';
    $fLines[] = '    classDef event      fill:#fdf4ff,stroke:#a855f7,color:#581c87';
    $fLines[] = '    classDef listener   fill:#fce7f3,stroke:#ec4899,color:#831843';
    $fLines[] = '    classDef database   fill:#f1f5f9,stroke:#64748b,color:#1e293b';

    $depCode = implode("\n", $fLines);

    // Layer counts for legend
    $lCounts = [];
    foreach ($depNodes as $n) { $lCounts[$n['layer']] = ($lCounts[$n['layer']] ?? 0) + 1; }

    $legendItems = [
        'controller' => ['Controllers', '#3b82f6', '#dbeafe'],
        'service'    => ['Services',    '#10b981', '#d1fae5'],
        'repository' => ['Repositories','#f59e0b', '#fef3c7'],
        'model'      => ['Models',      '#8b5cf6', '#ede9fe'],
        'job'        => ['Jobs',        '#ca8a04', '#fef9c3'],
        'event'      => ['Events',      '#a855f7', '#fdf4ff'],
        'listener'   => ['Listeners',   '#ec4899', '#fce7f3'],
        'database'   => ['Database',    '#64748b', '#f1f5f9'],
    ];
    @endphp

    @if(empty($depEdges))
    <div class="bg-white rounded-xl p-12 text-center border border-slate-100">
        <p class="text-slate-400 font-medium">No dependency edges found yet.</p>
        <p class="text-slate-300 text-sm mt-2">Add classes like <code class="bg-slate-100 px-1 rounded">ProductService</code>, <code class="bg-slate-100 px-1 rounded">ProductRepository</code> with constructor injection to see the graph.</p>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        {{-- Legend + controls --}}
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 px-4 py-3 border-b border-slate-100 bg-slate-50">
            @foreach($legendItems as $layer => [$label, $border, $bg])
            @if(isset($lCounts[$layer]))
            <span class="flex items-center gap-1.5 text-xs text-slate-600">
                <span class="w-3 h-3 rounded border inline-block" style="background:{{ $bg }};border-color:{{ $border }}"></span>
                {{ $label }} <span class="font-semibold">{{ $lCounts[$layer] }}</span>
            </span>
            @endif
            @endforeach
            <div class="ml-auto flex items-center gap-1">
                <button onclick="depZoom(0.15)" class="w-7 h-7 border border-slate-200 rounded-lg bg-white hover:bg-slate-50 text-slate-600 text-base leading-none flex items-center justify-center">+</button>
                <button onclick="depZoom(-0.15)" class="w-7 h-7 border border-slate-200 rounded-lg bg-white hover:bg-slate-50 text-slate-600 text-base leading-none flex items-center justify-center">−</button>
                <button onclick="depFit()" class="w-7 h-7 border border-slate-200 rounded-lg bg-white hover:bg-slate-50 text-slate-500 flex items-center justify-center" title="Fit all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                </button>
                <button onclick="depClearHighlight()" class="w-7 h-7 border border-slate-200 rounded-lg bg-white hover:bg-slate-50 text-slate-500 flex items-center justify-center" title="Clear selection">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Hint bar --}}
        <div class="px-4 py-1.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <p class="text-xs text-slate-400">Scroll to zoom · Drag to pan · Click a node to highlight connections</p>
            <span id="dep-sel-label" class="text-xs text-indigo-600 font-medium hidden"></span>
        </div>

        {{-- Custom SVG graph --}}
        <div class="relative" style="height:600px">
            <svg id="dep-canvas" width="100%" height="100%" style="cursor:grab;background:#fafafa">
                <defs>
                    <marker id="dep-arr" markerWidth="7" markerHeight="7" refX="5" refY="3" orient="auto">
                        <path d="M0,0 L0,6 L7,3 z" fill="#94a3b8"/>
                    </marker>
                    <marker id="dep-arr-hi" markerWidth="7" markerHeight="7" refX="5" refY="3" orient="auto">
                        <path d="M0,0 L0,6 L7,3 z" fill="#6366f1"/>
                    </marker>
                    <filter id="dep-shadow" x="-20%" y="-20%" width="140%" height="140%">
                        <feDropShadow dx="0" dy="2" stdDeviation="3" flood-opacity="0.12"/>
                    </filter>
                </defs>
                <g id="dep-vp">
                    <g id="dep-bands-g"></g>
                    <g id="dep-edges-g"></g>
                    <g id="dep-nodes-g"></g>
                </g>
            </svg>
        </div>
    </div>
    @endif
</section>

{{-- ══ MODULE EXPLORER ══ --}}
<section id="sec-modules" class="p-6" style="display:none">

    @php $modules = $data['modules'] ?? []; @endphp

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Module Explorer</h1>
            <p class="text-slate-500 text-sm">
                {{ count($modules) }} module{{ count($modules) !== 1 ? 's' : '' }} detected
                @if(count($modules) > 0)
                · {{ array_sum(array_column($modules, 'controllers')) }} controllers
                · {{ array_sum(array_column($modules, 'models')) }} models
                · {{ array_sum(array_column($modules, 'routes')) }} routes
                @endif
            </p>
        </div>
    </div>

    @if(empty($modules))
    <div class="bg-white rounded-xl p-16 text-center border border-slate-100 shadow-sm">
        <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <p class="font-semibold text-slate-600 mb-1">No modules detected</p>
        <p class="text-sm text-slate-400">Create a <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs">Modules/</code> directory at your project root with subfolders per module.</p>
        <p class="text-xs text-slate-400 mt-2">Compatible with <a class="underline" href="https://nwidart.com/laravel-modules" target="_blank">nwidart/laravel-modules</a> structure.</p>
    </div>
    @else

    {{-- Summary bar --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        @php
        $totalCtrl  = array_sum(array_column($modules, 'controllers'));
        $totalModel = array_sum(array_column($modules, 'models'));
        $totalRoute = array_sum(array_column($modules, 'routes'));
        $totalSvc   = array_sum(array_column($modules, 'services'));
        @endphp
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm px-5 py-4">
            <p class="text-2xl font-bold text-indigo-600">{{ $totalCtrl }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Total Controllers</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm px-5 py-4">
            <p class="text-2xl font-bold text-violet-600">{{ $totalModel }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Total Models</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm px-5 py-4">
            <p class="text-2xl font-bold text-emerald-600">{{ $totalRoute }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Total Routes</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm px-5 py-4">
            <p class="text-2xl font-bold text-sky-600">{{ $totalSvc }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Total Services</p>
        </div>
    </div>

    {{-- Module cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @php
        $modColors = [
            'indigo','violet','emerald','sky','rose','amber','teal','orange','pink','cyan',
        ];
        @endphp
        @foreach($modules as $i => $mod)
        @php
        $col    = $modColors[$i % count($modColors)];
        $initial = strtoupper(substr($mod['name'], 0, 1));
        $hasExtras = $mod['jobs'] > 0 || $mod['events'] > 0 || $mod['services'] > 0;
        @endphp
        <div class="card bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
            {{-- Header --}}
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-50">
                <div class="w-11 h-11 bg-{{ $col }}-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shrink-0 shadow-sm">
                    {{ $initial }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-slate-800 text-base leading-tight">{{ $mod['name'] }}</p>
                    <p class="text-xs text-slate-400 font-mono truncate mt-0.5">{{ $mod['path'] }}</p>
                </div>
            </div>
            {{-- Core stats --}}
            <div class="px-5 py-4 grid grid-cols-3 gap-2 border-b border-slate-50">
                <div class="text-center py-2 rounded-xl bg-indigo-50">
                    <p class="text-xl font-bold text-indigo-600">{{ $mod['controllers'] }}</p>
                    <p class="text-xs text-indigo-400 mt-0.5 font-medium">Controllers</p>
                </div>
                <div class="text-center py-2 rounded-xl bg-violet-50">
                    <p class="text-xl font-bold text-violet-600">{{ $mod['models'] }}</p>
                    <p class="text-xs text-violet-400 mt-0.5 font-medium">Models</p>
                </div>
                <div class="text-center py-2 rounded-xl bg-emerald-50">
                    <p class="text-xl font-bold text-emerald-600">{{ $mod['routes'] }}</p>
                    <p class="text-xs text-emerald-400 mt-0.5 font-medium">Routes</p>
                </div>
            </div>
            {{-- Extra components --}}
            @if($hasExtras)
            <div class="px-5 py-3 flex flex-wrap gap-2">
                @if($mod['jobs'] > 0)
                <span class="inline-flex items-center gap-1 text-xs bg-yellow-50 text-yellow-700 px-2.5 py-1 rounded-full border border-yellow-100 font-medium">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9" stroke-width="2"/></svg>
                    {{ $mod['jobs'] }} Jobs
                </span>
                @endif
                @if($mod['events'] > 0)
                <span class="inline-flex items-center gap-1 text-xs bg-purple-50 text-purple-700 px-2.5 py-1 rounded-full border border-purple-100 font-medium">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    {{ $mod['events'] }} Events
                </span>
                @endif
                @if($mod['services'] > 0)
                <span class="inline-flex items-center gap-1 text-xs bg-sky-50 text-sky-700 px-2.5 py-1 rounded-full border border-sky-100 font-medium">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    {{ $mod['services'] }} Services
                </span>
                @endif
            </div>
            @else
            <div class="px-5 py-3">
                <p class="text-xs text-slate-300 italic">No jobs, events, or services</p>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

</section>

{{-- ══ PACKAGE DETECTION ══ --}}
<section id="sec-packages" class="p-6" style="display:none">

    @php
    $packages   = $data['packages'] ?? [];
    $byCategory = [];
    foreach ($packages as $pkg) {
        $byCategory[$pkg['category']][] = $pkg;
    }
    ksort($byCategory);

    $categoryColors = [
        'Admin Panel'      => ['bg' => 'bg-amber-50',   'text' => 'text-amber-700',   'border' => 'border-amber-200'],
        'API Authentication'=> ['bg' => 'bg-blue-50',   'text' => 'text-blue-700',    'border' => 'border-blue-200'],
        'Architecture'     => ['bg' => 'bg-indigo-50',  'text' => 'text-indigo-700',  'border' => 'border-indigo-200'],
        'Audit'            => ['bg' => 'bg-rose-50',    'text' => 'text-rose-700',    'border' => 'border-rose-200'],
        'Auth Scaffolding' => ['bg' => 'bg-pink-50',    'text' => 'text-pink-700',    'border' => 'border-pink-200'],
        'Authorization'    => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200'],
        'Backup'           => ['bg' => 'bg-green-50',   'text' => 'text-green-700',   'border' => 'border-green-200'],
        'Debug'            => ['bg' => 'bg-slate-100',  'text' => 'text-slate-600',   'border' => 'border-slate-200'],
        'Import / Export'  => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200'],
        'Media'            => ['bg' => 'bg-cyan-50',    'text' => 'text-cyan-700',    'border' => 'border-cyan-200'],
        'Payments'         => ['bg' => 'bg-violet-50',  'text' => 'text-violet-700',  'border' => 'border-violet-200'],
        'PDF'              => ['bg' => 'bg-red-50',     'text' => 'text-red-700',     'border' => 'border-red-200'],
        'Queue Monitoring' => ['bg' => 'bg-teal-50',    'text' => 'text-teal-700',    'border' => 'border-teal-200'],
        'Search'           => ['bg' => 'bg-sky-50',     'text' => 'text-sky-700',     'border' => 'border-sky-200'],
        'UI Framework'     => ['bg' => 'bg-purple-50',  'text' => 'text-purple-700',  'border' => 'border-purple-200'],
    ];
    $defaultCatColor = ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'border' => 'border-slate-200'];

    $dotColors = [
        'pink'=>'bg-pink-400','purple'=>'bg-purple-400','red'=>'bg-red-400','blue'=>'bg-blue-400',
        'orange'=>'bg-orange-400','pink'=>'bg-pink-400','violet'=>'bg-violet-400','amber'=>'bg-amber-400',
        'sky'=>'bg-sky-400','blue'=>'bg-blue-400','emerald'=>'bg-emerald-400','green'=>'bg-green-400',
        'teal'=>'bg-teal-400','slate'=>'bg-slate-400','cyan'=>'bg-cyan-400','indigo'=>'bg-indigo-400',
        'rose'=>'bg-rose-400',
    ];
    @endphp

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Packages</h1>
            <p class="text-slate-500 text-sm">
                {{ count($packages) }} known package{{ count($packages) !== 1 ? 's' : '' }} detected
                · {{ count($byCategory) }} {{ count($byCategory) !== 1 ? 'categories' : 'category' }}
            </p>
        </div>
    </div>

    @if(empty($packages))
    <div class="bg-white rounded-xl p-16 text-center border border-slate-100 shadow-sm">
        <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
        </div>
        <p class="font-semibold text-slate-600 mb-1">No known packages detected</p>
        <p class="text-sm text-slate-400">None of the tracked packages appear in your <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs">composer.json</code>.</p>
    </div>
    @else

    @foreach($byCategory as $category => $pkgs)
    @php $catColor = $categoryColors[$category] ?? $defaultCatColor; @endphp
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-3">
            <span class="text-xs font-bold uppercase tracking-widest text-slate-400">{{ $category }}</span>
            <span class="text-xs {{ $catColor['bg'] }} {{ $catColor['text'] }} px-2 py-0.5 rounded-full border {{ $catColor['border'] }} font-medium">{{ count($pkgs) }}</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
            @foreach($pkgs as $pkg)
            @php $dot = $dotColors[$pkg['color']] ?? 'bg-slate-400'; @endphp
            <div class="card bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">
                <div class="flex items-start gap-3 p-4 flex-1">
                    <div class="mt-0.5 w-2.5 h-2.5 rounded-full shrink-0 {{ $dot }}"></div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-semibold text-slate-800 text-sm leading-tight">{{ $pkg['name'] }}</p>
                            @if($pkg['version'])
                            <span class="text-xs bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded font-mono">v{{ $pkg['version'] }}</span>
                            @endif
                            @if($pkg['dev'])
                            <span class="text-xs bg-yellow-50 text-yellow-600 px-1.5 py-0.5 rounded border border-yellow-100">dev</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">{{ $pkg['description'] }}</p>
                        <p class="text-xs text-slate-300 font-mono mt-1.5">{{ $pkg['key'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    @endif

</section>

{{-- Export --}}
<section id="sec-export" class="p-6" style="display:none">
    <h1 class="text-2xl font-bold mb-1">Export Architecture</h1>
    <p class="text-slate-500 text-sm mb-8">Download your architecture report in multiple formats for sharing, documentation, or archiving.</p>

    @php
    $exportPath = rtrim(request()->getSchemeAndHttpHost() . request()->getBasePath(), '/') . '/' . ltrim(config('architecture-discovery.dashboard.path', 'architecture'), '/');
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 max-w-4xl">

        {{-- JSON --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex flex-col gap-3 card">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-800">JSON</p>
                    <p class="text-xs text-slate-500">architecture.json</p>
                </div>
            </div>
            <p class="text-sm text-slate-600">Full raw report data — all components, routes, dependencies, and scores in machine-readable format. Useful for CI pipelines and tooling integrations.</p>
            <div class="flex gap-2 mt-auto pt-2">
                <button onclick="exportJson()" class="flex-1 flex items-center justify-center gap-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download
                </button>
                <button onclick="copyJson()" id="copy-json-btn" class="px-3 py-2 border border-slate-200 hover:bg-slate-50 rounded-lg text-slate-600 text-sm transition-colors" title="Copy to clipboard">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </button>
            </div>
        </div>

        {{-- Markdown --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex flex-col gap-3 card">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-800">Markdown</p>
                    <p class="text-xs text-slate-500">architecture.md</p>
                </div>
            </div>
            <p class="text-sm text-slate-600">Human-readable report with summary tables, model relationships, and a Mermaid dependency graph. Renders beautifully on GitHub and Notion.</p>
            <button onclick="exportMarkdown()" class="mt-auto pt-2 flex items-center justify-center gap-2 bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download
            </button>
        </div>

        {{-- HTML --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex flex-col gap-3 card">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-orange-50 border border-orange-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-800">HTML</p>
                    <p class="text-xs text-slate-500">architecture.html</p>
                </div>
            </div>
            <p class="text-sm text-slate-600">Fully self-contained HTML report. Open in any browser, attach to Jira tickets, or share with stakeholders with no server required.</p>
            <a href="{{ $exportPath }}/export/html" download="architecture.html" class="mt-auto pt-2 flex items-center justify-center gap-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors no-underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download
            </a>
        </div>

        {{-- SVG --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex flex-col gap-3 card">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 border border-indigo-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-800">SVG</p>
                    <p class="text-xs text-slate-500">architecture.svg</p>
                </div>
            </div>
            <p class="text-sm text-slate-600">Vector diagram of your architecture overview — all components with counts in a circular layout. Scalable, embed in wikis or slide decks.</p>
            <div class="flex gap-2 mt-auto pt-2">
                <a href="{{ $exportPath }}/export/svg" download="architecture.svg" class="flex-1 flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors no-underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download
                </a>
                <button onclick="previewSvg()" class="px-3 py-2 border border-slate-200 hover:bg-slate-50 rounded-lg text-slate-600 text-sm transition-colors" title="Preview SVG">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
            </div>
        </div>

        {{-- PNG --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex flex-col gap-3 card">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-pink-50 border border-pink-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-800">PNG</p>
                    <p class="text-xs text-slate-500">architecture.png</p>
                </div>
            </div>
            <p class="text-sm text-slate-600">Raster image exported via canvas — paste directly into Slack, email, or slide decks without needing an SVG viewer.</p>
            <button onclick="exportPng()" id="export-png-btn" class="mt-auto pt-2 flex items-center justify-center gap-2 bg-pink-600 hover:bg-pink-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span id="export-png-label">Download</span>
            </button>
        </div>

        {{-- PDF --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex flex-col gap-3 card">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-red-50 border border-red-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-6 4h4"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-800">PDF</p>
                    <p class="text-xs text-slate-500">architecture.pdf</p>
                </div>
            </div>
            <p class="text-sm text-slate-600">Print the current dashboard to PDF via your browser's print dialog. Use "Save as PDF" as the destination for a professional report.</p>
            <button onclick="exportPdf()" class="mt-auto pt-2 flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print to PDF
            </button>
        </div>

        {{-- Graphic Report --}}
        <div class="bg-white rounded-xl border-2 border-violet-200 p-5 flex flex-col gap-3 card relative overflow-hidden" style="grid-column:span 1">
            <div class="absolute top-0 right-0 bg-violet-600 text-white text-xs font-bold px-2.5 py-1 rounded-bl-xl tracking-wide">NEW</div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-violet-50 border border-violet-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-800">Graphic Report</p>
                    <p class="text-xs text-slate-500">architecture-report.html</p>
                </div>
            </div>
            <p class="text-sm text-slate-600">Beautiful standalone HTML report with SVG charts, score gauge, route distribution, dependency graph, and full component tables. No server required.</p>
            <button onclick="exportGraphicHTML()" id="graphic-report-btn"
                class="mt-auto pt-2 flex items-center justify-center gap-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span id="graphic-report-label">Generate &amp; Download</span>
            </button>
        </div>

    </div>

    {{-- CLI hint --}}
    <div class="mt-8 max-w-2xl bg-slate-900 rounded-xl p-5">
        <p class="text-sm text-slate-400 mb-3 font-medium">Export from the command line</p>
        <div class="space-y-2 font-mono text-sm">
            <div class="flex items-center gap-2">
                <span class="text-slate-500">$</span>
                <span class="text-green-400">php artisan architecture:discover</span>
                <span class="text-slate-400 text-xs ml-2">— exports json + html (configured formats)</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500">$</span>
                <span class="text-green-400">php artisan architecture:discover <span class="text-amber-400">--format=svg</span></span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500">$</span>
                <span class="text-green-400">php artisan architecture:discover <span class="text-amber-400">--format=markdown --output=docs/architecture.md</span></span>
            </div>
        </div>
    </div>

    {{-- SVG Preview Modal --}}
    <div id="svg-preview-modal" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-6" style="display:none!important">
        <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-200">
                <p class="font-semibold text-slate-800">Architecture SVG Preview</p>
                <button onclick="closeSvgPreview()" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="overflow-auto flex-1 p-4 bg-slate-50 flex items-center justify-center" id="svg-preview-content">
                <p class="text-slate-400 text-sm">Loading…</p>
            </div>
        </div>
    </div>

</section>

{{-- AI Insights --}}
<section id="sec-ai" class="p-6" style="display:none">
    <div class="flex items-center justify-between mb-1">
        <h1 class="text-2xl font-bold">AI Insights</h1>
        @if(config('architecture-discovery.ai.enabled', false))
        <span class="flex items-center gap-1.5 text-xs text-green-700 bg-green-50 border border-green-200 px-2.5 py-1 rounded-full font-medium">
            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
            AI Ready · {{ config('architecture-discovery.ai.model', 'gemini-2.5-flash') }}
        </span>
        @else
        <span class="flex items-center gap-1.5 text-xs text-slate-500 bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-full">
            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
            AI Disabled
        </span>
        @endif
    </div>
    <p class="text-slate-500 text-sm mb-6">AI-powered architecture review — score, SOLID analysis, code smells, and actionable suggestions.</p>

    @if(!config('architecture-discovery.ai.enabled', false))
    {{-- Setup card --}}
    <div class="max-w-xl bg-amber-50 border border-amber-200 rounded-xl p-6 mb-6">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <p class="font-semibold text-amber-800 mb-2">AI is not enabled</p>
                <p class="text-sm text-amber-700 mb-3">To enable AI insights, add the following to your <code class="bg-amber-100 px-1 rounded">.env</code> file and publish the config:</p>
                <div class="bg-amber-900 rounded-lg p-3 font-mono text-xs text-amber-100 space-y-1">
                    <div>GEMINI_API_KEY=your_api_key_here</div>
                </div>
                <p class="text-sm text-amber-700 mt-3">Then in <code class="bg-amber-100 px-1 rounded">config/architecture-discovery.php</code>:</p>
                <div class="bg-amber-900 rounded-lg p-3 font-mono text-xs text-amber-100 mt-1">
                    <div>'ai' => [</div>
                    <div class="pl-4">'enabled' => <span class="text-green-400">true</span>,</div>
                    <div class="pl-4">'provider' => 'gemini',</div>
                    <div class="pl-4">'model' => 'gemini-2.5-flash',</div>
                    <div>]</div>
                </div>
                <p class="text-xs text-amber-600 mt-3">Get a free API key at <span class="font-medium">aistudio.google.com</span></p>
            </div>
        </div>
    </div>
    @endif

    {{-- Analyze button --}}
    <div id="ai-trigger" class="mb-6">
        <button onclick="aiAnalyze()"
            {{ !config('architecture-discovery.ai.enabled', false) ? 'disabled' : '' }}
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-medium rounded-xl text-sm transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            Analyze with AI
        </button>
        <p class="text-xs text-slate-400 mt-2">Sends your architecture data to {{ config('architecture-discovery.ai.model', 'gemini-2.5-flash') }} for analysis. Takes 10–30 seconds.</p>
    </div>

    {{-- Loading state --}}
    <div id="ai-loading" class="hidden mb-6">
        <div class="flex items-center gap-3 text-indigo-600">
            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span class="text-sm font-medium">Analyzing architecture with AI…</span>
        </div>
        <p class="text-xs text-slate-400 mt-1 ml-8">This usually takes 10–30 seconds.</p>
    </div>

    {{-- Error state --}}
    <div id="ai-error" class="hidden mb-6 max-w-xl bg-red-50 border border-red-200 rounded-xl p-4">
        <p class="text-sm font-semibold text-red-700 mb-1">Analysis failed</p>
        <p id="ai-error-msg" class="text-sm text-red-600"></p>
    </div>

    {{-- Results --}}
    <div id="ai-results" class="hidden space-y-6 max-w-4xl">

        {{-- Summary + AI Score --}}
        <div class="flex gap-4 flex-col sm:flex-row">
            <div class="flex-1 bg-white rounded-xl border border-slate-200 p-5">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">AI Summary</p>
                <p id="ai-summary" class="text-slate-700 text-sm leading-relaxed"></p>
            </div>
            <div class="sm:w-36 bg-white rounded-xl border border-slate-200 p-5 flex flex-col items-center justify-center gap-1 shrink-0">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">AI Score</p>
                <p id="ai-score-num" class="text-4xl font-bold text-indigo-600"></p>
                <p class="text-xs text-slate-400">/ 100</p>
                <div class="w-full mt-2 bg-slate-100 rounded-full h-1.5">
                    <div id="ai-score-bar" class="bg-indigo-500 h-1.5 rounded-full transition-all duration-700" style="width:0%"></div>
                </div>
            </div>
        </div>

        {{-- SOLID Review --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">SOLID Principles</p>
            <div id="ai-solid" class="grid grid-cols-1 sm:grid-cols-5 gap-3"></div>
        </div>

        {{-- Problems --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Problems Detected</p>
            <div id="ai-problems" class="space-y-3"></div>
        </div>

        {{-- Suggestions --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Suggestions</p>
            <div id="ai-suggestions" class="space-y-3"></div>
        </div>

        {{-- Laravel Best Practices --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Laravel Best Practices</p>
            <div id="ai-laravel-practices" class="space-y-2"></div>
        </div>

        {{-- Best Practices (followed) --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Practices Already Followed</p>
            <ul id="ai-best-practices" class="space-y-1.5"></ul>
        </div>

        {{-- Re-analyze --}}
        <div class="flex items-center gap-3 pt-2">
            <button onclick="aiAnalyze()" class="inline-flex items-center gap-2 px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Re-analyze
            </button>
            <span id="ai-provider-badge" class="text-xs text-slate-400"></span>
        </div>

    </div>

</section>

{{-- ══ AI CHAT ══ --}}
<section id="sec-chat" class="flex flex-col h-full p-6" style="display:none">
    <div class="flex items-center justify-between mb-1">
        <h1 class="text-2xl font-bold">AI Chat</h1>
        @if(config('architecture-discovery.ai.enabled', false))
        <span class="flex items-center gap-1.5 text-xs text-green-700 bg-green-50 border border-green-200 px-2.5 py-1 rounded-full font-medium">
            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
            {{ config('architecture-discovery.ai.model') }}
        </span>
        @endif
    </div>
    <p class="text-slate-500 text-sm mb-5">Ask anything. The package finds the relevant controllers, models, and routes in your architecture — then sends only that to AI.</p>

    @if(!config('architecture-discovery.ai.enabled', false))
    <div class="max-w-xl bg-amber-50 border border-amber-200 rounded-xl p-4 mb-5">
        <p class="text-sm text-amber-800 font-medium">AI is not enabled. Set <code class="bg-amber-100 px-1 rounded">ai.enabled = true</code> and <code class="bg-amber-100 px-1 rounded">GEMINI_API_KEY</code> in your .env.</p>
    </div>
    @endif

    {{-- Suggestion pills --}}
    <div class="flex flex-wrap gap-2 mb-5" id="chat-suggestions">
        <button onclick="chatSuggest('Which controller has the most methods?')" class="px-3 py-1.5 text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-full transition-colors">Which controller is largest?</button>
        <button onclick="chatSuggest('Explain the checkout flow from route to model.')" class="px-3 py-1.5 text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-full transition-colors">Explain checkout flow</button>
        <button onclick="chatSuggest('Are there any SOLID principle violations?')" class="px-3 py-1.5 text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-full transition-colors">SOLID violations?</button>
        <button onclick="chatSuggest('Which models have the most relationships?')" class="px-3 py-1.5 text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-full transition-colors">Models with most relationships</button>
        <button onclick="chatSuggest('What services should I extract from my controllers?')" class="px-3 py-1.5 text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-full transition-colors">Suggest service extractions</button>
        <button onclick="chatSuggest('Explain the overall architecture and data flow.')" class="px-3 py-1.5 text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-full transition-colors">Overall architecture</button>
    </div>

    {{-- Messages --}}
    <div id="chat-messages" class="flex-1 overflow-y-auto space-y-4 mb-4 max-h-[calc(100vh-420px)] min-h-48 pr-1">
        <div id="chat-empty" class="flex flex-col items-center justify-center h-48 text-slate-400">
            <svg class="w-10 h-10 mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <p class="text-sm">Ask a question about your architecture</p>
        </div>
    </div>

    {{-- Input --}}
    <div class="border border-slate-200 rounded-xl bg-white shadow-sm overflow-hidden">
        <textarea id="chat-input" rows="2"
            placeholder="e.g. Explain the checkout flow  •  Which controller is too large?  •  Where should I add a service?"
            {{ !config('architecture-discovery.ai.enabled', false) ? 'disabled' : '' }}
            oninput="chatPreviewContext(this.value)"
            onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();chatSend();}"
            class="w-full px-4 pt-3 pb-1 text-sm text-slate-800 placeholder-slate-400 resize-none outline-none disabled:bg-slate-50 disabled:cursor-not-allowed"></textarea>
        <div class="flex items-center justify-between px-4 pb-3 pt-1">
            <span id="chat-context-hint" class="text-xs text-slate-400 truncate max-w-xs"></span>
            <button onclick="chatSend()" id="chat-send-btn"
                {{ !config('architecture-discovery.ai.enabled', false) ? 'disabled' : '' }}
                class="flex items-center gap-1.5 px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg transition-colors shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Send
            </button>
        </div>
    </div>
</section>

{{-- ══ AI DOCS ══ --}}
<section id="sec-aidocs" class="p-6" style="display:none">
    <div class="flex items-center justify-between mb-1">
        <h1 class="text-2xl font-bold">AI Documentation</h1>
        @if(config('architecture-discovery.ai.enabled', false))
        <span class="flex items-center gap-1.5 text-xs text-green-700 bg-green-50 border border-green-200 px-2.5 py-1 rounded-full font-medium">
            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
            {{ config('architecture-discovery.ai.model') }}
        </span>
        @endif
    </div>
    <p class="text-slate-500 text-sm mb-6">AI writes full markdown docs for each layer of your architecture. One click per file — or generate all at once.</p>

    @if(!config('architecture-discovery.ai.enabled', false))
    <div class="max-w-xl bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
        <p class="text-sm text-amber-800 font-medium">AI is not enabled. Set <code class="bg-amber-100 px-1 rounded">ai.enabled = true</code> and <code class="bg-amber-100 px-1 rounded">GEMINI_API_KEY</code> in your .env.</p>
    </div>
    @endif

    <div class="flex flex-wrap gap-3 mb-6">
        <button onclick="docsGenerateAll()"
            {{ !config('architecture-discovery.ai.enabled', false) ? 'disabled' : '' }}
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Generate All Docs
        </button>
        <button onclick="docsDownloadAll()" id="docs-download-all-btn" class="hidden inline-flex items-center gap-2 px-5 py-2.5 border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-medium rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Download All
        </button>

        {{-- AI Graphic Report button --}}
        <button onclick="generateAIGraphicReport()" id="ai-report-btn"
            {{ !config('architecture-discovery.ai.enabled', false) ? 'disabled' : '' }}
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-violet-600 hover:bg-violet-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span id="ai-report-btn-label">Generate AI Graphic Report</span>
        </button>
    </div>

    {{-- AI Report progress panel --}}
    <div id="ai-report-progress" class="hidden max-w-lg mb-8 bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="flex items-center gap-3 px-5 py-3.5 bg-violet-600 text-white">
            <svg class="w-4 h-4 animate-spin shrink-0" fill="none" viewBox="0 0 24 24" id="ai-report-spinner">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <p class="text-sm font-semibold" id="ai-report-progress-title">Generating AI Report…</p>
        </div>
        <div class="px-5 py-4 space-y-2" id="ai-report-steps">
            <div class="ai-step flex items-center gap-3 text-sm" data-step="analyze">
                <span class="step-icon w-4 h-4 rounded-full border-2 border-slate-300 flex-shrink-0"></span>
                <span class="text-slate-500">Analyzing architecture with AI</span>
            </div>
            <div class="ai-step flex items-center gap-3 text-sm" data-step="architecture">
                <span class="step-icon w-4 h-4 rounded-full border-2 border-slate-300 flex-shrink-0"></span>
                <span class="text-slate-500">Generating Architecture documentation</span>
            </div>
            <div class="ai-step flex items-center gap-3 text-sm" data-step="models">
                <span class="step-icon w-4 h-4 rounded-full border-2 border-slate-300 flex-shrink-0"></span>
                <span class="text-slate-500">Generating Models documentation</span>
            </div>
            <div class="ai-step flex items-center gap-3 text-sm" data-step="controllers">
                <span class="step-icon w-4 h-4 rounded-full border-2 border-slate-300 flex-shrink-0"></span>
                <span class="text-slate-500">Generating Controllers documentation</span>
            </div>
            <div class="ai-step flex items-center gap-3 text-sm" data-step="routes">
                <span class="step-icon w-4 h-4 rounded-full border-2 border-slate-300 flex-shrink-0"></span>
                <span class="text-slate-500">Generating Routes documentation</span>
            </div>
            <div class="ai-step flex items-center gap-3 text-sm" data-step="services">
                <span class="step-icon w-4 h-4 rounded-full border-2 border-slate-300 flex-shrink-0"></span>
                <span class="text-slate-500">Generating Services documentation</span>
            </div>
            <div class="ai-step flex items-center gap-3 text-sm" data-step="modules">
                <span class="step-icon w-4 h-4 rounded-full border-2 border-slate-300 flex-shrink-0"></span>
                <span class="text-slate-500">Generating Modules documentation</span>
            </div>
            <div class="ai-step flex items-center gap-3 text-sm" data-step="build">
                <span class="step-icon w-4 h-4 rounded-full border-2 border-slate-300 flex-shrink-0"></span>
                <span class="text-slate-500">Building graphic report</span>
            </div>
        </div>
        <div id="ai-report-error" class="hidden px-5 pb-4 text-sm text-red-600 font-medium"></div>
    </div>

    {{-- Doc cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 max-w-4xl" id="docs-grid">

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
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex flex-col gap-3 card" id="doc-card-{{ $type }}">
            <div class="flex items-start justify-between gap-2">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0" style="background:{{ $color }}18;border:1px solid {{ $color }}40">
                        <svg class="w-4 h-4" style="color:{{ $color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800 text-sm">{{ $filename }}</p>
                    </div>
                </div>
                <span id="doc-status-{{ $type }}" class="text-xs text-slate-400 shrink-0">Pending</span>
            </div>
            <p class="text-xs text-slate-500 leading-relaxed">{{ $desc }}</p>
            <div class="flex gap-2 mt-auto pt-1">
                <button onclick="docsGenerate('{{ $type }}')"
                    {{ !config('architecture-discovery.ai.enabled', false) ? 'disabled' : '' }}
                    id="doc-gen-btn-{{ $type }}"
                    class="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium py-2 px-3 rounded-lg border border-slate-200 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed text-slate-600 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    Generate
                </button>
                <button onclick="docsDownload('{{ $type }}')"
                    id="doc-dl-btn-{{ $type }}"
                    class="hidden flex items-center justify-center gap-1.5 text-xs font-medium py-2 px-3 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-600 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download
                </button>
            </div>
        </div>
        @endforeach

    </div>

</section>

</main>

<script>
const APP = @json($data);
const SECTIONS = ['overview','modules','packages','models','modelmap','controllers','routes','apidocs','jobs','events','services','repositories','observers','policies','dependencies','export','ai','chat','aidocs'];
let depRendered     = false;
let mapTreeRendered = false;
let erRendered      = false;
let graphRendered   = false;

function navigate(s) {
    SECTIONS.forEach(id => {
        const sec = document.getElementById('sec-' + id);
        if (sec) sec.style.display = id === s ? 'block' : 'none';
        const nav = document.getElementById('nav-' + id);
        if (nav) {
            nav.classList.toggle('nav-active', id === s);
            nav.classList.toggle('text-slate-300', id !== s);
        }
    });
    if (s === 'dependencies' && !depRendered) {
        depRendered = true;
        setTimeout(initDepGraph, 60);
    }
    if (s === 'modelmap' && !graphRendered) {
        setTimeout(initRelGraph, 50);
    }
}

function showDetail(type, idx) {
    document.getElementById(type + '-list').style.display = 'none';
    document.getElementById(type + '-detail').style.display = 'block';
    document.getElementById(type + '-detail-content').innerHTML = renderDetail(type, APP[type][idx]);
}

function showList(type) {
    document.getElementById(type + '-list').style.display = 'block';
    document.getElementById(type + '-detail').style.display = 'none';
}

function filterGrid(type) {
    const q = document.getElementById(type + '-search').value.toLowerCase();
    document.querySelectorAll('#' + type + '-grid [data-name]').forEach(el => {
        el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
}

function filterRoutes() {
    const q   = document.getElementById('routes-search').value.toLowerCase();
    const mf  = document.getElementById('routes-method-filter').value;
    const mwf = document.getElementById('routes-mw-filter').value.toLowerCase();
    document.querySelectorAll('.route-row').forEach(row => {
        const handler  = (row.querySelector('td:nth-child(3)')?.textContent || '').toLowerCase();
        const textOk   = !q || row.dataset.uri.includes(q) || handler.includes(q);
        const methodOk = !mf  || row.dataset.methods.includes(mf);
        const mwOk     = !mwf || row.dataset.mw.includes(mwf);
        row.style.display = textOk && methodOk && mwOk ? '' : 'none';
    });
}

// ── Route Explorer ────────────────────────────────────────────────────────────

const MW_DESC = {
    web: 'Web Session / CSRF', api: 'Stateless API', auth: 'Requires Authentication',
    'auth:sanctum': 'Sanctum Token Auth', 'auth:api': 'API Token Auth',
    guest: 'Redirect if Logged In', verified: 'Email Verified Required',
    signed: 'Requires Signed URL', throttle: 'Rate Limiting',
    can: 'Authorization Gate', bindings: 'Route Model Binding',
    cache: 'Response Caching', cors: 'CORS Headers',
};

function mwDesc(mw) {
    return MW_DESC[mw] || MW_DESC[mw.split(':')[0]] || null;
}

function buildDepGraph() {
    const nodeMap = {}, edgeMap = {};
    for (const n of (APP.dependencies?.nodes || [])) nodeMap[n.name] = n;
    for (const e of (APP.dependencies?.edges || [])) {
        if (!edgeMap[e.from]) edgeMap[e.from] = [];
        edgeMap[e.from].push(e);
    }
    return { nodeMap, edgeMap };
}

function traceChain(startName) {
    const { nodeMap, edgeMap } = buildDepGraph();
    const chain = [], visited = new Set();

    function dfs(name, edgeType) {
        if (visited.has(name)) return;
        visited.add(name);
        chain.push({ name, layer: nodeMap[name]?.layer || 'unknown', edgeType });
        for (const e of (edgeMap[name] || [])) dfs(e.to, e.type);
    }
    dfs(startName, null);

    // ── Smart inference fallback ──────────────────────────────────────────────
    // Runs whenever the chain has no service/repo/model.
    // Works for any naming pattern: UserController, PaymentAnalytics, OrderPage, etc.
    if (!chain.some(n => ['service','repository','model'].includes(n.layer))) {
        // Strip common non-domain suffixes to get the entity base words
        const stripped = startName
            .replace(/Controller|Page|Resource|Component|Widget|Action|Form|Table|Livewire|Panel$/g, '');

        // Split PascalCase → individual words: "PaymentAnalytics" → ["Payment","Analytics"]
        const words = stripped.match(/[A-Z][a-z0-9]*/g) || [stripped];
        // Build a ranked candidate list: [full, longest-word, each-word]
        const candidates = [stripped, ...words.sort((a,b) => b.length - a.length)];

        const depNodes = APP.dependencies?.nodes || [];
        const models   = APP.models || [];

        // Score helper: how well does a node name match the candidate keywords?
        const score = (nodeName, layer) => {
            const n = nodeName.toLowerCase().replace(/(service|repository|repo)$/i,'');
            for (let i = 0; i < candidates.length; i++) {
                const c = candidates[i].toLowerCase();
                if (n === c)           return 100 - i;   // exact match
                if (n.startsWith(c))   return  80 - i;   // prefix match
                if (n.includes(c))     return  60 - i;   // contains
                if (c.includes(n) && n.length >= 3) return 40 - i; // reverse contains
            }
            return 0;
        };

        // Find best-scoring service
        if (!chain.some(n => n.layer === 'service')) {
            const best = depNodes
                .filter(n => n.layer === 'service')
                .map(n => ({ n, s: score(n.name, 'service') }))
                .filter(x => x.s > 0)
                .sort((a,b) => b.s - a.s)[0];
            if (best && !visited.has(best.n.name)) {
                chain.push({ name: best.n.name, layer: 'service', edgeType: 'injects', _inferred: true });
                visited.add(best.n.name);
                for (const e of (edgeMap[best.n.name] || [])) dfs(e.to, e.type);
            }
        }

        // Find best-scoring repository
        if (!chain.some(n => n.layer === 'repository')) {
            const best = depNodes
                .filter(n => n.layer === 'repository')
                .map(n => ({ n, s: score(n.name, 'repository') }))
                .filter(x => x.s > 0)
                .sort((a,b) => b.s - a.s)[0];
            if (best && !visited.has(best.n.name)) {
                chain.push({ name: best.n.name, layer: 'repository', edgeType: 'uses', _inferred: true });
                visited.add(best.n.name);
                for (const e of (edgeMap[best.n.name] || [])) dfs(e.to, e.type);
            }
        }

        // Find best-scoring model
        if (!chain.some(n => n.layer === 'model')) {
            const best = models
                .map(m => ({ m, s: score(m.name, 'model') }))
                .filter(x => x.s > 0)
                .sort((a,b) => b.s - a.s)[0];
            if (best && !visited.has(best.m.name)) {
                chain.push({ name: best.m.name, layer: 'model', edgeType: 'uses', _inferred: true });
                visited.add(best.m.name);
            }
        }
    }

    return chain;
}

const LAYER_STYLE = {
    controller: ['bg-blue-50',    'border-blue-300',   'text-blue-800',   'bg-blue-500'],
    service:    ['bg-green-50',   'border-green-300',  'text-green-800',  'bg-green-500'],
    repository: ['bg-yellow-50',  'border-yellow-300', 'text-yellow-800', 'bg-yellow-500'],
    model:      ['bg-purple-50',  'border-purple-300', 'text-purple-800', 'bg-purple-500'],
    job:        ['bg-amber-50',   'border-amber-300',  'text-amber-800',  'bg-amber-500'],
    event:      ['bg-fuchsia-50', 'border-fuchsia-300','text-fuchsia-800','bg-fuchsia-500'],
    listener:   ['bg-pink-50',    'border-pink-300',   'text-pink-800',   'bg-pink-500'],
    database:   ['bg-slate-100',  'border-slate-300',  'text-slate-700',  'bg-slate-400'],
    unknown:    ['bg-slate-50',   'border-slate-200',  'text-slate-600',  'bg-slate-300'],
};

const EDGE_LABEL = { injects: 'injects', uses: 'uses', triggers: 'triggers', persists: 'persists' };

// ── Route Graph Explorer ───────────────────────────────────────────────────────

const RF_COLOR = {
    request:    { bg:'#eef2ff', border:'#6366f1', type:'#4f46e5', name:'#1e1b4b', sub:'#6366f1',  dot:'#6366f1' },
    middleware: { bg:'#fffbeb', border:'#d97706', type:'#b45309', name:'#451a03', sub:'#d97706',  dot:'#f59e0b' },
    controller: { bg:'#eff6ff', border:'#3b82f6', type:'#1d4ed8', name:'#1e3a5f', sub:'#3b82f6',  dot:'#3b82f6' },
    service:    { bg:'#f0fdf4', border:'#10b981', type:'#047857', name:'#052e16', sub:'#10b981',  dot:'#10b981' },
    repository: { bg:'#fffbeb', border:'#f59e0b', type:'#b45309', name:'#451a03', sub:'#f59e0b',  dot:'#f59e0b' },
    model:      { bg:'#f5f3ff', border:'#8b5cf6', type:'#5b21b6', name:'#2e1065', sub:'#8b5cf6',  dot:'#8b5cf6' },
    database:   { bg:'#f8fafc', border:'#64748b', type:'#475569', name:'#0f172a', sub:'#64748b',  dot:'#64748b' },
    job:        { bg:'#fff7ed', border:'#f97316', type:'#c2410c', name:'#431407', sub:'#f97316',  dot:'#f97316' },
    event:      { bg:'#fdf4ff', border:'#d946ef', type:'#a21caf', name:'#4a044e', sub:'#d946ef',  dot:'#d946ef' },
    listener:   { bg:'#fdf2f8', border:'#ec4899', type:'#be185d', name:'#500724', sub:'#ec4899',  dot:'#ec4899' },
    unknown:    { bg:'#f9fafb', border:'#9ca3af', type:'#6b7280', name:'#111827', sub:'#9ca3af',  dot:'#9ca3af' },
};
const RF_LAYER_ORDER = ['request','middleware','controller','service','repository','model','database','job','event','listener','unknown'];
const RF_TYPE_LABEL  = { request:'HTTP Request', middleware:'Middleware', controller:'Controller', service:'Service', repository:'Repository', model:'Model', database:'Database', job:'Job', event:'Event', listener:'Listener', unknown:'Component' };

let _rfNodes = {}, _rfEdges = [], _rfSelected = null, _rfTab = 'info', _rfRoute = null, _rfMws = [];

function showRouteDetail(idx) {
    const route   = APP.routes[idx];
    const methods = (route.methods || []).filter(m => m !== 'HEAD');
    const ctrl    = route.controller?.class ? route.controller.class.split('\\').pop() : null;
    const action  = route.controller?.method || null;
    const mws     = route.middleware || [];
    const chain   = ctrl ? traceChain(ctrl) : [];

    _rfRoute = route; _rfMws = mws; _rfNodes = {}; _rfEdges = []; _rfSelected = null; _rfTab = 'info';

    document.getElementById('routes-list').style.display   = 'none';
    document.getElementById('routes-detail').style.display = 'block';

    // ── Build graph nodes + edges ──────────────────────────────────────────────
    let uid = 0;
    const mkNode = (type, name, sub, meta) => {
        const id = 'rg' + (uid++);
        _rfNodes[id] = { id, type, name, sub: sub || '', meta: meta || {} };
        return id;
    };
    const mkEdge = (from, to, label) => _rfEdges.push({ from, to, label: label || '' });

    const routeId = mkNode('request', methods.join('|') + ' /' + (route.uri || ''), route.name || '', { route });
    let prevId = routeId;

    if (mws.length) {
        const mwId = mkNode('middleware', 'Middleware Stack', mws.length + ' layers', { mws });
        mkEdge(prevId, mwId, 'enters');
        prevId = mwId;
    }

    if (chain.length > 0) {
        let lastId = prevId, firstEdge = ctrl ? 'dispatches' : null;
        chain.forEach((node, i) => {
            const isCtrl  = i === 0;
            const sub     = isCtrl ? (action && action !== '__invoke' ? '@' + action : 'Invokable') : (RF_TYPE_LABEL[node.layer] || node.layer);
            const nid     = mkNode(node.layer, node.name, sub, { node, action: isCtrl ? action : null, inferred: !!node._inferred });
            const label   = i === 0 ? firstEdge : (EDGE_LABEL[node.edgeType] || node.edgeType || 'calls');
            mkEdge(lastId, nid, label);
            lastId = nid;
        });
        // DB Table node — find the model node and look up its table
        const modelNode = chain.find(n => n.layer === 'model');
        if (modelNode) {
            const md = (APP.models || []).find(m => m.name === modelNode.name);
            if (md?.table) {
                const dbId     = mkNode('database', md.table, 'DB Table', { table: md.table, model: md });
                const modelNid = Object.values(_rfNodes).find(n => n.name === modelNode.name && n.type === 'model')?.id;
                if (modelNid) mkEdge(modelNid, dbId, 'queries');
            }
        }
    } else if (ctrl) {
        // Controller exists but has no dep-graph edges and inference found nothing
        const cid = mkNode('controller', ctrl, action && action !== '__invoke' ? '@' + action : 'Invokable', {});
        mkEdge(prevId, cid, 'dispatches');
    }

    // ── Layout: group nodes into layers, position each ─────────────────────────
    const NW = 224, NH = 74, GAP_Y = 100, GAP_X = 28, PAD = 40;
    const layers = {};
    Object.values(_rfNodes).forEach(n => {
        const li = RF_LAYER_ORDER.indexOf(n.type);
        const k  = li >= 0 ? li : 99;
        (layers[k] = layers[k] || []).push(n);
    });
    const layerKeys   = Object.keys(layers).map(Number).sort((a, b) => a - b);
    const maxRowW     = Math.max(...layerKeys.map(k => layers[k].length * NW + (layers[k].length - 1) * GAP_X));
    const CANVAS_W    = Math.max(maxRowW + PAD * 2, 480);
    const CANVAS_H    = layerKeys.length * (NH + GAP_Y) + PAD * 2 - GAP_Y + PAD;
    const pos = {};
    layerKeys.forEach((lk, li) => {
        const row = layers[lk];
        const totalW = row.length * NW + (row.length - 1) * GAP_X;
        let x = (CANVAS_W - totalW) / 2;
        const y = PAD + li * (NH + GAP_Y);
        row.forEach(n => {
            pos[n.id] = { x, y, cx: x + NW / 2, cy: y + NH / 2 };
            x += NW + GAP_X;
        });
    });

    // ── SVG defs ───────────────────────────────────────────────────────────────
    const defs = `<defs>
        <pattern id="rf-dot" x="0" y="0" width="22" height="22" patternUnits="userSpaceOnUse">
            <circle cx="1" cy="1" r="0.8" fill="#cbd5e1" opacity="0.6"/>
        </pattern>
        <marker id="rf-arr" markerWidth="9" markerHeight="7" refX="8" refY="3.5" orient="auto">
            <polygon points="0 0,9 3.5,0 7" fill="#94a3b8"/>
        </marker>
        <marker id="rf-arr-hi" markerWidth="9" markerHeight="7" refX="8" refY="3.5" orient="auto">
            <polygon points="0 0,9 3.5,0 7" fill="#6366f1"/>
        </marker>
        <filter id="rf-glow" x="-20%" y="-20%" width="140%" height="140%">
            <feGaussianBlur stdDeviation="3" result="blur"/>
            <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
        </filter>
    </defs>`;

    // ── Edges ──────────────────────────────────────────────────────────────────
    const edgesSvg = _rfEdges.map(e => {
        const f = pos[e.from], t = pos[e.to];
        if (!f || !t) return '';
        const x1 = f.cx, y1 = f.y + NH, x2 = t.cx, y2 = t.y;
        const cp = Math.abs(y2 - y1) * 0.45;
        const d  = `M${x1},${y1} C${x1},${y1+cp} ${x2},${y2-cp} ${x2},${y2}`;
        const mx = (x1+x2)/2, my = (y1+y2)/2;
        const lw = (e.label.length * 6) + 14;
        return `<g>
            <path d="${d}" fill="none" stroke="#cbd5e1" stroke-width="1.5" marker-end="url(#rf-arr)"/>
            ${e.label ? `<rect x="${mx-lw/2}" y="${my-8}" width="${lw}" height="16" rx="8" fill="#ffffff" stroke="#e2e8f0" stroke-width="1"/>
            <text x="${mx}" y="${my+4}" fill="#64748b" font-size="9" font-family="ui-monospace,monospace" text-anchor="middle" font-weight="600" letter-spacing="0.04em">${_esc(e.label)}</text>` : ''}
        </g>`;
    }).join('');

    // ── Nodes ──────────────────────────────────────────────────────────────────
    const nodesSvg = Object.values(_rfNodes).map(n => {
        const p = pos[n.id]; if (!p) return '';
        const c          = RF_COLOR[n.type] || RF_COLOR.unknown;
        const typeLabel  = RF_TYPE_LABEL[n.type] || n.type;
        const shortName  = n.name.length > 24 ? n.name.slice(0, 23) + '…' : n.name;
        const shortSub   = n.sub.length  > 28 ? n.sub.slice(0, 27)  + '…' : n.sub;
        const isInferred = n.meta?.inferred;
        // Inferred nodes get a dashed border + slightly dimmer opacity to show they are convention-based
        const strokeDash = isInferred ? 'stroke-dasharray="5,3"' : '';
        const opacity    = isInferred ? 'opacity="0.88"' : '';
        return `<g class="rf-node" data-id="${n.id}" onclick="rfClick('${n.id}')" style="cursor:pointer" ${opacity}>
            <rect x="${p.x}" y="${p.y}" width="${NW}" height="${NH}" rx="12" fill="${c.bg}" stroke="${c.border}" stroke-width="${isInferred ? 1.5 : 1.5}" ${strokeDash} id="rf-rect-${n.id}"/>
            <rect x="${p.x+1.5}" y="${p.y+1.5}" width="${NW-3}" height="20" rx="10" fill="${c.border}" fill-opacity="${isInferred ? 0.08 : 0.15}"/>
            <circle cx="${p.x+14}" cy="${p.y+11}" r="3.5" fill="${c.dot}"/>
            <text x="${p.x+23}" y="${p.y+15}" fill="${c.type}" font-size="8.5" font-family="ui-monospace,monospace" font-weight="700" letter-spacing="0.1em">${typeLabel.toUpperCase()}${isInferred ? ' ~' : ''}</text>
            <text x="${p.x+12}" y="${p.y+43}" fill="${c.name}" font-size="12.5" font-family="ui-monospace,monospace" font-weight="700">${_esc(shortName)}</text>
            ${shortSub ? `<text x="${p.x+12}" y="${p.y+60}" fill="${c.sub}" font-size="10" font-family="ui-monospace,monospace">${_esc(shortSub)}</text>` : ''}
        </g>`;
    }).join('');

    // ── Method badges (HTML) ───────────────────────────────────────────────────
    const methodBadges = methods.map(m =>
        `<span class="text-xs font-bold px-2.5 py-1 rounded-lg method-${m.toLowerCase()}">${m}</span>`
    ).join('');

    // ── Right panel: default shows route props ─────────────────────────────────
    const firstNode = Object.values(_rfNodes)[0];
    _rfSelected = firstNode;

    document.getElementById('routes-detail-content').innerHTML = `
    <div style="display:flex;height:580px;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0;background:#f8fafc;box-shadow:0 4px 24px rgba(0,0,0,0.08)">

        <!-- ── Graph canvas ── -->
        <div style="flex:1;display:flex;flex-direction:column;min-width:0;overflow:hidden">

            <!-- Toolbar -->
            <div style="display:flex;align-items:center;gap:8px;padding:10px 16px;background:#ffffff;border-bottom:1px solid #e2e8f0;flex-shrink:0">
                <div style="display:flex;gap:4px">
                    <span style="background:#f1f5f9;color:#475569;border-radius:8px;padding:4px 11px;font-size:10px;font-family:ui-monospace,monospace;font-weight:700;letter-spacing:0.05em;border:1px solid #e2e8f0">TOP-DOWN</span>
                </div>
                <div style="width:1px;height:14px;background:#e2e8f0"></div>
                <span style="font-size:10px;color:#94a3b8;font-family:ui-monospace,monospace">${Object.keys(_rfNodes).length} nodes · ${_rfEdges.length} edges</span>
                <div style="margin-left:auto;display:flex;gap:6px;align-items:center">
                    ${methodBadges}
                    <code style="font-size:11px;color:#94a3b8;font-family:ui-monospace,monospace">/${_esc(route.uri||'')}</code>
                </div>
            </div>

            <!-- SVG scroll area -->
            <div style="flex:1;overflow:auto;padding:0;background:#f8fafc" id="rf-canvas-wrap">
                <svg width="${CANVAS_W}" height="${CANVAS_H}" style="display:block;min-width:${CANVAS_W}px">
                    ${defs}
                    <rect width="${CANVAS_W}" height="${CANVAS_H}" fill="#f8fafc"/>
                    <rect width="${CANVAS_W}" height="${CANVAS_H}" fill="url(#rf-dot)"/>
                    ${edgesSvg}
                    ${nodesSvg}
                </svg>
            </div>
        </div>

        <!-- ── Right panel ── -->
        <div style="width:256px;flex-shrink:0;background:#ffffff;border-left:1px solid #e2e8f0;display:flex;flex-direction:column;overflow:hidden">

            <!-- Route identity -->
            <div style="padding:14px 16px;border-bottom:1px solid #f1f5f9;background:#f8fafc">
                <div style="display:flex;gap:5px;flex-wrap:wrap;align-items:center;margin-bottom:4px">
                    ${methodBadges}
                </div>
                <code style="font-size:10px;color:#475569;font-family:ui-monospace,monospace;word-break:break-all;display:block;margin-top:2px;font-weight:600">${_esc(route.uri||'')}</code>
                ${route.name ? `<p style="font-size:9px;color:#94a3b8;font-family:ui-monospace,monospace;margin:2px 0 0">${_esc(route.name)}</p>` : ''}
            </div>

            <!-- Tabs -->
            <div style="display:flex;border-bottom:1px solid #f1f5f9;padding:0 4px;flex-shrink:0;background:#ffffff">
                <button id="rftab-info"  onclick="rfTab('info')"  style="flex:1;padding:8px 0;font-size:10px;color:#6366f1;background:none;border:none;border-bottom:2px solid #6366f1;cursor:pointer;font-family:inherit;font-weight:700;letter-spacing:0.04em">INFO</button>
                <button id="rftab-flow"  onclick="rfTab('flow')"  style="flex:1;padding:8px 0;font-size:10px;color:#94a3b8;background:none;border:none;border-bottom:2px solid transparent;cursor:pointer;font-family:inherit;font-weight:600;letter-spacing:0.04em">FLOW</button>
                <button id="rftab-edges" onclick="rfTab('edges')" style="flex:1;padding:8px 0;font-size:10px;color:#94a3b8;background:none;border:none;border-bottom:2px solid transparent;cursor:pointer;font-family:inherit;font-weight:600;letter-spacing:0.04em">EDGES</button>
            </div>

            <!-- Panel body -->
            <div id="rf-panel" style="flex:1;overflow-y:auto;padding:14px">
                ${rfNodeProps(firstNode)}
            </div>
        </div>

    </div>`;

    // Highlight first node
    setTimeout(() => rfHighlight(firstNode?.id), 10);
}

function rfProp(label, val) {
    if (!val && val !== 0) return '';
    return `<div style="margin-bottom:10px">
        <p style="font-size:8.5px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 2px;font-family:ui-monospace,monospace">${label}</p>
        <p style="font-size:11px;color:#1e293b;font-family:ui-monospace,monospace;margin:0;word-break:break-all;line-height:1.4;font-weight:500">${val}</p>
    </div>`;
}

function rfNodeProps(node) {
    if (!node) return '';
    const route = _rfRoute, mws = _rfMws;
    const c = RF_COLOR[node.type] || RF_COLOR.unknown;
    const header = `<div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid #f1f5f9">
        <div style="width:8px;height:8px;border-radius:50%;background:${c.dot};flex-shrink:0"></div>
        <div>
            <p style="font-size:8px;color:${c.type};text-transform:uppercase;letter-spacing:0.1em;margin:0;font-family:ui-monospace,monospace;font-weight:700">${RF_TYPE_LABEL[node.type]||node.type}</p>
            <p style="font-size:11px;color:${c.name};font-family:ui-monospace,monospace;margin:0;font-weight:700;word-break:break-all">${_esc(node.name)}</p>
        </div>
    </div>`;

    const inferredBadge = node.meta?.inferred
        ? `<div style="margin-bottom:10px;padding:5px 8px;background:#fefce8;border:1px solid #fde68a;border-radius:6px;font-size:9px;color:#92400e;font-family:ui-monospace,monospace">~ inferred by naming convention</div>`
        : '';

    let body = inferredBadge;
    if (node.type === 'request') {
        const methods = (route.methods||[]).filter(m=>m!=='HEAD');
        body += rfProp('Method', methods.join(', ')) + rfProp('URI', '/' + (route.uri||'')) + rfProp('Route Name', route.name||null) + rfProp('Middleware', mws.length ? mws.length + ' layers' : 'None');
    } else if (node.type === 'middleware') {
        body += mws.map((mw, i) => rfProp('#' + (i+1), mw.split('\\').pop())).join('');
    } else if (node.type === 'controller') {
        const cc = route.controller || {};
        body += rfProp('Class', cc.class || node.name) + rfProp('Method', (node.meta?.action && node.meta.action !== '__invoke') ? node.meta.action : null) + rfProp('Type', (!node.meta?.action || node.meta.action === '__invoke') ? 'Invokable Controller' : 'Resource Method');
    } else if (node.type === 'model') {
        const md = (APP.models||[]).find(m => m.name === node.name);
        if (md) body += rfProp('Class', md.name) + rfProp('Table', md.table) + rfProp('Fillable', md.fillable?.length ? md.fillable.length + ' fields' : null) + rfProp('Relationships', md.relationships?.length ? md.relationships.length + ' defined' : null);
    } else if (node.type === 'database') {
        body += rfProp('Table', node.name) + rfProp('Model', node.meta?.model?.name || null);
    } else if (node.type === 'service') {
        const svc = (APP.services||[]).find(s => s.name === node.name);
        body += rfProp('Class', node.name) + rfProp('Methods', svc?.method_count ? svc.method_count + ' methods' : null);
    } else if (node.type === 'repository') {
        body += rfProp('Class', node.name) + rfProp('Layer', 'Repository Pattern');
    } else {
        body += rfProp('Name', node.name) + rfProp('Layer', node.type);
    }
    return header + body;
}

function rfFlowList() {
    return Object.values(_rfNodes).map(n => {
        const c = RF_COLOR[n.type] || RF_COLOR.unknown;
        return `<div onclick="rfClick('${n.id}')" style="display:flex;align-items:center;gap:8px;padding:7px 8px;border-radius:8px;cursor:pointer;margin-bottom:4px;border:1px solid #e2e8f0;background:#f8fafc;transition:background 0.15s">
            <div style="width:7px;height:7px;border-radius:50%;background:${c.dot};flex-shrink:0"></div>
            <div style="flex:1;min-width:0">
                <p style="font-size:10px;color:#1e293b;font-family:ui-monospace,monospace;font-weight:700;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${_esc(n.name)}</p>
                <p style="font-size:8px;color:${c.type};text-transform:uppercase;letter-spacing:0.08em;margin:0;font-family:ui-monospace,monospace;font-weight:600">${RF_TYPE_LABEL[n.type]||n.type}</p>
            </div>
        </div>`;
    }).join('');
}

function rfEdgeList() {
    return _rfEdges.map(e => {
        const from = _rfNodes[e.from], to = _rfNodes[e.to];
        if (!from || !to) return '';
        return `<div style="margin-bottom:8px;padding:8px;border-radius:8px;border:1px solid #e2e8f0;background:#f8fafc">
            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">
                <span style="font-size:9px;color:#3b82f6;font-family:ui-monospace,monospace;font-weight:700">${_esc(from.name)}</span>
                <span style="font-size:8px;color:#94a3b8">→</span>
                <span style="font-size:9px;color:#8b5cf6;font-family:ui-monospace,monospace;font-weight:700">${_esc(to.name)}</span>
            </div>
            ${e.label ? `<span style="font-size:8px;color:#94a3b8;font-family:ui-monospace,monospace;font-style:italic">${_esc(e.label)}</span>` : ''}
        </div>`;
    }).join('') || '<p style="font-size:11px;color:#94a3b8;text-align:center;margin-top:20px">No edges</p>';
}

function rfClick(nid) {
    _rfSelected = _rfNodes[nid];
    rfHighlight(nid);
    rfRefreshPanel();
}

function rfHighlight(nid) {
    document.querySelectorAll('.rf-node rect:first-child').forEach(r => {
        r.setAttribute('stroke-width', '1.5');
        r.style.filter = '';
    });
    if (!nid) return;
    const rect = document.getElementById('rf-rect-' + nid);
    if (rect) { rect.setAttribute('stroke-width', '3'); rect.style.filter = 'brightness(1.5) drop-shadow(0 0 6px currentColor)'; }
}

function rfTab(tab) {
    _rfTab = tab;
    ['info','flow','edges'].forEach(t => {
        const btn = document.getElementById('rftab-' + t);
        if (!btn) return;
        const active = t === tab;
        btn.style.color        = active ? '#6366f1' : '#334155';
        btn.style.borderBottom = active ? '2px solid #6366f1' : '2px solid transparent';
    });
    rfRefreshPanel();
}

function rfRefreshPanel() {
    const el = document.getElementById('rf-panel');
    if (!el) return;
    if (_rfTab === 'info')  el.innerHTML = rfNodeProps(_rfSelected || Object.values(_rfNodes)[0]);
    if (_rfTab === 'flow')  el.innerHTML = rfFlowList();
    if (_rfTab === 'edges') el.innerHTML = rfEdgeList();
}

// ── Detail renderers ──────────────────────────────────────────────────────────

function renderDetail(type, item) {
    const map = {
        models: renderModel, controllers: renderController,
        jobs: renderJob, events: renderEvent,
        services: x => renderService(x, 'Service'),
        repositories: x => renderService(x, 'Repository'),
        observers: renderObserver, policies: renderPolicy,
    };
    return (map[type] || (() => ''))(item);
}

function detailCard(title, body) {
    return `<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 mb-4"><h3 class="font-semibold text-slate-800 mb-3">${title}</h3>${body}</div>`;
}

function pill(text, cls = 'bg-slate-100 text-slate-600') {
    return `<span class="text-xs ${cls} px-2 py-0.5 rounded font-mono">${text}</span>`;
}

function avatar(letter, bg, fg) {
    return `<div class="w-12 h-12 ${bg} rounded-xl flex items-center justify-center ${fg} text-lg font-bold">${letter}</div>`;
}

function renderModel(m) {
    const relColors = {
        belongsTo:'bg-blue-50 text-blue-700', hasMany:'bg-green-50 text-green-700',
        hasOne:'bg-teal-50 text-teal-700', belongsToMany:'bg-purple-50 text-purple-700',
        morphTo:'bg-pink-50 text-pink-700', morphMany:'bg-orange-50 text-orange-700',
    };
    let h = `<div class="flex items-center gap-3 mb-6">${avatar(m.name[0],'bg-indigo-100','text-indigo-600')}
        <div><h2 class="text-xl font-bold">${m.name}</h2><p class="text-sm text-slate-400 font-mono">${m.namespace}</p></div></div>`;

    let meta = `<div class="grid grid-cols-2 gap-3 text-sm">
        <div><p class="text-xs text-slate-400 mb-1">Table</p><code class="bg-slate-100 px-2 py-0.5 rounded">${m.table}</code></div>`;
    if (m.observer) meta += `<div><p class="text-xs text-slate-400 mb-1">Observer</p><code class="bg-orange-50 text-orange-700 px-2 py-0.5 rounded">${m.observer}</code></div>`;
    meta += '</div>';
    h += detailCard('Details', meta);

    if (m.fillable?.length) h += detailCard('Fillable', `<div class="flex flex-wrap gap-2">${m.fillable.map(f => pill(f)).join('')}</div>`);
    if (m.hidden?.length) h += detailCard('Hidden', `<div class="flex flex-wrap gap-2">${m.hidden.map(f => pill(f, 'bg-red-50 text-red-600')).join('')}</div>`);
    if (m.traits?.length) h += detailCard('Traits', `<div class="flex flex-wrap gap-2">${m.traits.map(t => pill(t, 'bg-purple-50 text-purple-600')).join('')}</div>`);

    if (m.relationships?.length) {
        const rows = m.relationships.map(r => {
            const cls = relColors[r.type] || 'bg-slate-100 text-slate-600';
            const rel = r.related ? r.related.split('\\').pop() : '—';
            return `<tr class="border-b border-slate-50 last:border-0">
                <td class="py-2 pr-4 font-mono text-sm">${r.method}</td>
                <td class="py-2 pr-4"><span class="text-xs px-2 py-0.5 rounded ${cls}">${r.type}</span></td>
                <td class="py-2 text-sm text-slate-600">${rel}</td></tr>`;
        }).join('');
        h += detailCard(`Relationships (${m.relationships.length})`,
            `<table class="w-full"><thead><tr class="text-xs text-slate-400 border-b border-slate-100">
            <th class="text-left py-1.5 pr-4">Method</th><th class="text-left py-1.5 pr-4">Type</th><th class="text-left py-1.5">Related</th>
            </tr></thead><tbody>${rows}</tbody></table>`);
    }
    return h;
}

function renderController(c) {
    // Sanitize a string for safe HTML injection (XSS prevention)
    const esc = s => String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    // Sanitize a string for safe use inside Mermaid node labels (strip quotes/brackets/newlines)
    const mEsc = s => String(s ?? '').replace(/["[\]\n\r]/g, '').replace(/&/g, 'and').trim();
    // Safe first-letter avatar — never undefined
    const initial = s => (String(s || '?')[0] || '?').toUpperCase();

    const RESOURCE_VERBS = {
        index:   {verb:'GET',         color:'method-get'},
        create:  {verb:'GET',         color:'method-get'},
        store:   {verb:'POST',        color:'method-post'},
        show:    {verb:'GET',         color:'method-get'},
        edit:    {verb:'GET',         color:'method-get'},
        update:  {verb:'PUT / PATCH', color:'method-patch'},
        destroy: {verb:'DELETE',      color:'method-delete'},
    };

    // ── linked routes for this controller ─────────────────────────
    const ctrlBase = c.name;
    const linkedRoutes = (APP.routes || []).filter(r => {
        const cls = (r.controller?.class || r.action || '');
        return cls.split('\\').pop() === ctrlBase || cls === ctrlBase;
    });

    // ── dependency graph nodes from dep analyser ───────────────────
    const depEdges = (APP.dependencies?.edges || []).filter(e => e.from === ctrlBase);
    const usedModels   = depEdges.filter(e => e.type === 'uses' && (APP.dependencies?.nodes||[]).find(n=>n.name===e.to&&n.layer==='model')).map(e=>e.to);
    const usedServices = depEdges.filter(e => e.type === 'injects' || e.type === 'uses').map(e=>e.to).filter(t=>!usedModels.includes(t));

    // ── Header ─────────────────────────────────────────────────────
    let h = `
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-2xl shrink-0">${esc(initial(c.name))}</div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 flex-wrap">
                    <h2 class="text-2xl font-bold text-slate-800">${esc(c.name)}</h2>
                    ${c.is_resource ? '<span class="text-xs bg-green-100 text-green-700 border border-green-200 px-2.5 py-1 rounded-full font-medium">Resource Controller</span>' : ''}
                </div>
                <p class="text-sm text-slate-400 font-mono mt-1 truncate">${esc(c.namespace)}&#92;${esc(c.name)}</p>
                <p class="text-xs text-slate-300 font-mono mt-0.5">${esc(c.path || '')}</p>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-blue-50 rounded-xl p-3 text-center">
                <p class="text-2xl font-bold text-blue-600">${c.method_count || 0}</p>
                <p class="text-xs text-blue-400 mt-0.5">Methods</p>
            </div>
            <div class="bg-purple-50 rounded-xl p-3 text-center">
                <p class="text-2xl font-bold text-purple-600">${(c.dependencies||[]).length}</p>
                <p class="text-xs text-purple-400 mt-0.5">Dependencies</p>
            </div>
            <div class="bg-indigo-50 rounded-xl p-3 text-center">
                <p class="text-2xl font-bold text-indigo-600">${linkedRoutes.length}</p>
                <p class="text-xs text-indigo-400 mt-0.5">Routes</p>
            </div>
            <div class="bg-emerald-50 rounded-xl p-3 text-center">
                <p class="text-2xl font-bold text-emerald-600">${usedModels.length}</p>
                <p class="text-xs text-emerald-400 mt-0.5">Models Used</p>
            </div>
        </div>
    </div>`;

    // ── Request Flow Diagram ───────────────────────────────────────
    const flowId = 'ctrl-flow-' + c.name.replace(/\W/g,'');
    let flowLines = ['flowchart LR'];

    // Route node(s) — sanitize URIs for Mermaid labels
    if (linkedRoutes.length > 0) {
        const routeLabels = linkedRoutes.slice(0,3).map(r => {
            const ms = (r.methods||[]).filter(m=>m!=='HEAD');
            return mEsc((ms[0]||'?') + ' ' + (r.uri||''));
        });
        flowLines.push(`    Route["🌐 Route\\n${routeLabels.join('\\n')}"]`);
    } else {
        flowLines.push(`    Route["🌐 Route"]`);
    }

    // Middleware — sanitize labels for Mermaid
    const mwList = c.middleware || [];
    const routeMw = linkedRoutes.length > 0
        ? [...new Set(linkedRoutes.flatMap(r => r.middleware || []))]
        : [];
    const allMw = [...new Set([...mwList, ...routeMw])].filter(Boolean);
    if (allMw.length > 0) {
        flowLines.push(`    Middleware["🔒 Middleware\\n${allMw.slice(0,3).map(mEsc).join('\\n')}"]`);
        flowLines.push(`    Route --> Middleware`);
        flowLines.push(`    Middleware --> Controller["🎮 ${mEsc(c.name)}"]`);
    } else {
        flowLines.push(`    Route --> Controller["🎮 ${mEsc(c.name)}"]`);
    }

    // Dependencies (services/repos) — sanitize type names
    const deps = c.dependencies || [];
    deps.forEach((dep, i) => {
        const nodeId = 'Dep' + i;
        flowLines.push(`    ${nodeId}["⚙️ ${mEsc(dep.type)}"]`);
        flowLines.push(`    Controller --> ${nodeId}`);
    });

    // Models — sanitize model names
    usedModels.forEach((m, i) => {
        const nodeId = 'Mdl' + i;
        flowLines.push(`    ${nodeId}["📦 ${mEsc(m)}"]`);
        const parentId = deps.length > 0 ? 'Dep0' : 'Controller';
        flowLines.push(`    ${parentId} --> ${nodeId}`);
        flowLines.push(`    ${nodeId} --> DB[(Database)]`);
    });
    if (usedModels.length === 0 && deps.length === 0) {
        flowLines.push(`    Controller --> DB[(Database)]`);
    }

    // Styles
    flowLines.push(`    classDef ctrl fill:#dbeafe,stroke:#3b82f6,color:#1e40af`);
    flowLines.push(`    classDef mw  fill:#f3e8ff,stroke:#a855f7,color:#6b21a8`);
    flowLines.push(`    classDef dep fill:#ede9fe,stroke:#7c3aed,color:#4c1d95`);
    flowLines.push(`    classDef mdl fill:#d1fae5,stroke:#10b981,color:#064e3b`);
    flowLines.push(`    classDef db  fill:#fef3c7,stroke:#f59e0b,color:#78350f`);
    flowLines.push(`    classDef rt  fill:#e0f2fe,stroke:#0284c7,color:#0c4a6e`);
    flowLines.push(`    class Controller ctrl`);
    flowLines.push(`    class Route rt`);
    if (allMw.length > 0) flowLines.push(`    class Middleware mw`);
    deps.forEach((_, i) => flowLines.push(`    class Dep${i} dep`));
    usedModels.forEach((_, i) => flowLines.push(`    class Mdl${i} mdl`));
    if (usedModels.length > 0 || deps.length === 0) flowLines.push(`    class DB db`);

    h += `<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">
        <h3 class="text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Request Flow
        </h3>
        <div class="mermaid" id="${esc(flowId)}">${flowLines.join('\n')}</div>
    </div>`;

    // ── Methods Table ──────────────────────────────────────────────
    if ((c.method_details || c.methods || []).length) {
        const methods = c.method_details || (c.methods||[]).map(m=>({method:m, params:[]}));
        const RESOURCE_SET = new Set(['index','create','store','show','edit','update','destroy']);

        let rows = methods.map(md => {
            const mName  = md.method || md;
            const params = md.params || [];
            const rv     = RESOURCE_VERBS[mName];
            const isRes  = RESOURCE_SET.has(mName);

            // Find matching route(s)
            const matchedRoutes = linkedRoutes.filter(r => {
                const act = r.controller?.method || r.action || '';
                return act === mName || act.split('@')[1] === mName;
            });

            const httpBadge = rv
                ? `<span class="text-xs font-bold px-1.5 py-0.5 rounded ${rv.color}">${esc(rv.verb)}</span>`
                : (matchedRoutes.length > 0
                    ? matchedRoutes.slice(0,2).map(r => {
                        const ms = (r.methods||[]).filter(m=>m!=='HEAD');
                        return `<span class="text-xs font-bold px-1.5 py-0.5 rounded method-${esc((ms[0]||'get').toLowerCase())}">${esc(ms[0]||'?')}</span>`;
                      }).join(' ')
                    : `<span class="text-xs text-slate-300 italic">—</span>`);

            const routeUri = matchedRoutes.length > 0
                ? `<span class="text-xs text-slate-400 font-mono">${esc(matchedRoutes[0].uri||'')}</span>`
                : '';

            const paramBadges = params.length
                ? params.map(p => `<span class="text-xs bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded font-mono">${p.type ? esc(p.type) + ' ' : ''}<span class="text-indigo-500">$${esc(p.var)}</span></span>`).join(' ')
                : '<span class="text-xs text-slate-300 italic">no params</span>';

            const mwOnMethod = matchedRoutes.length > 0
                ? [...new Set(matchedRoutes.flatMap(r => r.middleware||[]))].slice(0,2)
                    .map(m => `<span class="text-xs bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded">${esc(m)}</span>`).join(' ')
                : '';

            return `<tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                <td class="py-3 px-4">
                    <div class="flex items-center gap-2">
                        <span class="font-mono font-semibold text-slate-700 text-sm">${esc(mName)}</span>
                        ${isRes ? '<span class="text-xs bg-blue-50 text-blue-500 px-1.5 py-0.5 rounded">resource</span>' : ''}
                    </div>
                </td>
                <td class="py-3 px-4">${httpBadge}</td>
                <td class="py-3 px-4">${routeUri}</td>
                <td class="py-3 px-4"><div class="flex flex-wrap gap-1">${paramBadges}</div></td>
                <td class="py-3 px-4"><div class="flex flex-wrap gap-1">${mwOnMethod}</div></td>
            </tr>`;
        }).join('');

        h += `<div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                <h3 class="text-sm font-semibold text-slate-700">Methods (${methods.length})</h3>
            </div>
            <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-xs text-slate-400 uppercase tracking-wide border-b border-slate-100 bg-slate-50">
                    <th class="text-left py-2.5 px-4 font-medium">Method</th>
                    <th class="text-left py-2.5 px-4 font-medium">HTTP</th>
                    <th class="text-left py-2.5 px-4 font-medium">Route URI</th>
                    <th class="text-left py-2.5 px-4 font-medium">Parameters</th>
                    <th class="text-left py-2.5 px-4 font-medium">Middleware</th>
                </tr></thead>
                <tbody>${rows}</tbody>
            </table>
            </div>
        </div>`;
    }

    // ── Dependencies Section ───────────────────────────────────────
    if ((c.dependencies||[]).length) {
        const depCards = c.dependencies.map(d => `
            <div class="flex items-center gap-3 p-3 bg-purple-50 border border-purple-100 rounded-xl">
                <div class="w-8 h-8 rounded-lg bg-purple-200 flex items-center justify-center text-purple-700 font-bold text-sm shrink-0">${esc(initial(d.type))}</div>
                <div>
                    <p class="font-semibold text-slate-700 text-sm">${esc(d.type)}</p>
                    <p class="text-xs text-slate-400 font-mono">$${esc(d.var)}</p>
                </div>
            </div>`).join('');

        h += `<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                <h3 class="text-sm font-semibold text-slate-700">Constructor Dependencies (${c.dependencies.length})</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">${depCards}</div>
        </div>`;
    }

    // ── Linked Routes ──────────────────────────────────────────────
    if (linkedRoutes.length) {
        const routeRows = linkedRoutes.map(r => {
            const ms     = (r.methods||[]).filter(m=>m!=='HEAD');
            const method = ms[0] || '?';
            const mwBadges = (r.middleware||[]).slice(0,3).map(m =>
                `<span class="text-xs bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded">${esc(m)}</span>`).join(' ');
            const name = r.name ? `<span class="text-xs text-slate-400 font-mono">${esc(r.name)}</span>` : '';
            return `<tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                <td class="py-3 px-4"><span class="text-xs font-bold px-1.5 py-0.5 rounded method-${esc(method.toLowerCase())}">${esc(method)}</span></td>
                <td class="py-3 px-4 font-mono text-sm text-slate-700">${esc(r.uri||'')}</td>
                <td class="py-3 px-4 font-mono text-xs text-slate-400">${esc(r.controller?.method || '')}</td>
                <td class="py-3 px-4">${name}</td>
                <td class="py-3 px-4"><div class="flex gap-1 flex-wrap">${mwBadges}</div></td>
            </tr>`;
        }).join('');

        h += `<div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                <h3 class="text-sm font-semibold text-slate-700">Linked Routes (${linkedRoutes.length})</h3>
            </div>
            <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-xs text-slate-400 uppercase tracking-wide border-b border-slate-100 bg-slate-50">
                    <th class="text-left py-2.5 px-4 font-medium">Method</th>
                    <th class="text-left py-2.5 px-4 font-medium">URI</th>
                    <th class="text-left py-2.5 px-4 font-medium">Action</th>
                    <th class="text-left py-2.5 px-4 font-medium">Name</th>
                    <th class="text-left py-2.5 px-4 font-medium">Middleware</th>
                </tr></thead>
                <tbody>${routeRows}</tbody>
            </table>
            </div>
        </div>`;
    }

    // ── Used Models & Services ─────────────────────────────────────
    if (usedModels.length || usedServices.length) {
        let grid = '';
        usedModels.forEach(m => {
            grid += `<div class="flex items-center gap-2 p-3 bg-emerald-50 border border-emerald-100 rounded-xl">
                <div class="w-7 h-7 rounded-lg bg-emerald-200 flex items-center justify-center text-emerald-700 font-bold text-xs">${esc(initial(m))}</div>
                <div><p class="text-sm font-semibold text-slate-700">${esc(m)}</p><p class="text-xs text-emerald-500">Model</p></div>
            </div>`;
        });
        usedServices.forEach(s => {
            grid += `<div class="flex items-center gap-2 p-3 bg-violet-50 border border-violet-100 rounded-xl">
                <div class="w-7 h-7 rounded-lg bg-violet-200 flex items-center justify-center text-violet-700 font-bold text-xs">${esc(initial(s))}</div>
                <div><p class="text-sm font-semibold text-slate-700">${esc(s)}</p><p class="text-xs text-violet-500">Service / Repo</p></div>
            </div>`;
        });

        h += `<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <h3 class="text-sm font-semibold text-slate-700">Used Models & Services</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">${grid}</div>
        </div>`;
    }

    // re-init mermaid for the new diagram
    setTimeout(() => {
        if (window.mermaid) {
            try { mermaid.init(undefined, document.querySelectorAll('.mermaid:not([data-processed])')) } catch(e){}
        }
    }, 50);

    return h;
}

function renderJob(j) {
    let h = `<div class="flex items-center gap-3 mb-6">${avatar(j.name[0],'bg-amber-100','text-amber-600')}
        <div><h2 class="text-xl font-bold">${j.name}</h2><p class="text-sm text-slate-400 font-mono">${j.namespace}</p></div></div>`;
    let meta = `<div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div><p class="text-xs text-slate-400 mb-1">Queue</p><p class="font-medium">${j.queue || 'default'}</p></div>`;
    if (j.tries)   meta += `<div><p class="text-xs text-slate-400 mb-1">Tries</p><p class="font-medium">${j.tries}</p></div>`;
    if (j.timeout) meta += `<div><p class="text-xs text-slate-400 mb-1">Timeout</p><p class="font-medium">${j.timeout}s</p></div>`;
    if (j.delay)   meta += `<div><p class="text-xs text-slate-400 mb-1">Delay</p><p class="font-medium">${j.delay}s</p></div>`;
    meta += '</div>';
    const flags = [j.queued && pill('ShouldQueue','bg-amber-50 text-amber-700'), j.unique && pill('ShouldBeUnique','bg-cyan-50 text-cyan-700'), j.encrypted && pill('ShouldBeEncrypted','bg-purple-50 text-purple-700')].filter(Boolean);
    if (flags.length) meta += `<div class="flex gap-2 mt-3">${flags.join('')}</div>`;
    h += detailCard('Queue Config', meta);
    if (j.dependencies?.length) h += detailCard('Dependencies', `<div class="flex flex-wrap gap-2">${j.dependencies.map(d => pill(d.split('\\').pop())).join('')}</div>`);
    return h;
}

function renderEvent(e) {
    let h = `<div class="flex items-center gap-3 mb-6">${avatar(e.name[0],'bg-pink-100','text-pink-600')}
        <div><h2 class="text-xl font-bold">${e.name}</h2><p class="text-sm text-slate-400 font-mono">${e.namespace}</p></div></div>`;
    const flags = [e.broadcasts && pill('ShouldBroadcast','bg-pink-50 text-pink-700'), e.broadcastNow && pill('ShouldBroadcastNow','bg-rose-50 text-rose-700')].filter(Boolean);
    if (flags.length) h += detailCard('Broadcast', `<div class="flex gap-2">${flags.join('')}</div>`);
    if (e.properties?.length) h += detailCard('Payload Properties', `<div class="flex flex-wrap gap-2">${e.properties.map(p => pill(p)).join('')}</div>`);
    return h;
}

function renderService(s, type) {
    const [bg, fg] = type === 'Repository' ? ['bg-cyan-100','text-cyan-600'] : ['bg-purple-100','text-purple-600'];
    let h = `<div class="flex items-center gap-3 mb-6">${avatar(s.name[0], bg, fg)}
        <div><h2 class="text-xl font-bold">${s.name}</h2><p class="text-sm text-slate-400 font-mono">${s.namespace}</p></div></div>`;
    if (s.dependencies?.length) h += detailCard('Dependencies', `<div class="flex flex-wrap gap-2">${s.dependencies.map(d => pill(d.split('\\').pop())).join('')}</div>`);
    if (s.methods?.length) h += detailCard(`Public Methods (${s.methods.length})`, `<div class="flex flex-wrap gap-2">${s.methods.map(m => pill(m, 'bg-slate-100 text-slate-700')).join('')}</div>`);
    return h;
}

function renderObserver(o) {
    const colors = {created:'bg-green-50 text-green-700',updated:'bg-blue-50 text-blue-700',deleted:'bg-red-50 text-red-700',saved:'bg-teal-50 text-teal-700',creating:'bg-emerald-50 text-emerald-700',updating:'bg-sky-50 text-sky-700',deleting:'bg-rose-50 text-rose-700',saving:'bg-cyan-50 text-cyan-700'};
    let h = `<div class="flex items-center gap-3 mb-6">${avatar(o.name[0],'bg-orange-100','text-orange-600')}
        <div><h2 class="text-xl font-bold">${o.name}</h2><p class="text-sm text-slate-400 font-mono">${o.namespace}</p></div></div>`;
    h += detailCard('Observes', `<p class="font-medium text-slate-700">${o.observes || 'Unknown'}</p>`);
    if (o.events?.length) h += detailCard('Lifecycle Events', `<div class="flex flex-wrap gap-2">${o.events.map(e => pill(e, colors[e] || 'bg-slate-100 text-slate-600')).join('')}</div>`);
    return h;
}

function renderPolicy(p) {
    const colors = {viewAny:'bg-blue-50 text-blue-700',view:'bg-sky-50 text-sky-700',create:'bg-green-50 text-green-700',update:'bg-yellow-50 text-yellow-700',delete:'bg-red-50 text-red-700',restore:'bg-teal-50 text-teal-700',forceDelete:'bg-rose-50 text-rose-700',before:'bg-purple-50 text-purple-700'};
    let h = `<div class="flex items-center gap-3 mb-6">${avatar(p.name[0],'bg-slate-200','text-slate-600')}
        <div><h2 class="text-xl font-bold">${p.name}</h2><p class="text-sm text-slate-400 font-mono">${p.namespace}</p></div></div>`;
    h += detailCard('Guards Model', `<p class="font-medium text-slate-700">${p.model || 'Unknown'}</p>`);
    if (p.actions?.length) h += detailCard('Policy Actions', `<div class="flex flex-wrap gap-2">${p.actions.map(a => pill(a, colors[a] || 'bg-slate-100 text-slate-600')).join('')}</div>`);
    return h;
}

// ── Model Relationship Map ────────────────────────────────────────────────────

const REL_COLORS = {
    hasMany:        'bg-green-50   text-green-700  border-green-200',
    hasOne:         'bg-teal-50    text-teal-700   border-teal-200',
    belongsTo:      'bg-blue-50    text-blue-700   border-blue-200',
    belongsToMany:  'bg-purple-50  text-purple-700 border-purple-200',
    morphMany:      'bg-orange-50  text-orange-700 border-orange-200',
    morphOne:       'bg-amber-50   text-amber-700  border-amber-200',
    morphTo:        'bg-pink-50    text-pink-700   border-pink-200',
    morphToMany:    'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200',
    hasManyThrough: 'bg-cyan-50    text-cyan-700   border-cyan-200',
    hasOneThrough:  'bg-sky-50     text-sky-700    border-sky-200',
};

function buildRelBranch(modelName, depth, visited) {
    if (depth > 3 || visited.has(modelName)) return '';
    const model = (APP.models || []).find(m => m.name === modelName);
    if (!model) return '';

    const rels = model.relationships || [];
    if (rels.length === 0) return '';

    const newVis = new Set(visited);
    newVis.add(modelName);

    let html = '<div style="margin-left:20px;padding-left:12px;border-left:2px solid #e2e8f0;margin-top:6px">';

    rels.forEach((rel, i) => {
        const relName = rel.related ? rel.related.split('\\').pop() : null;
        const cls     = REL_COLORS[rel.type] || 'bg-slate-50 text-slate-600 border-slate-200';
        const isLast  = i === rels.length - 1;
        const hasSub  = relName && depth < 2 && !visited.has(relName) &&
                        (APP.models || []).some(m => m.name === relName && (m.relationships||[]).length > 0);

        html += `<div class="relative" style="padding-top:6px">
            <div class="flex items-center gap-2 py-1 group">
                <span class="text-slate-300 select-none text-xs font-mono">${isLast ? '└──' : '├──'}</span>
                <span class="text-xs px-1.5 py-0.5 rounded border font-mono ${cls}">${rel.type}</span>
                <span class="text-sm font-medium text-slate-700">${relName || '<em class=\'text-slate-400\'>unknown</em>'}</span>
                <span class="text-xs text-slate-400 font-mono opacity-0 group-hover:opacity-100 transition-opacity">${rel.method}()</span>
                ${newVis.has(relName) ? '<span class="text-xs text-slate-300 italic ml-1">↩ circular</span>' : ''}
            </div>
            ${hasSub ? buildRelBranch(relName, depth + 1, newVis) : ''}
        </div>`;
    });

    html += '</div>';
    return html;
}

function renderModelTree() {
    const search  = (document.getElementById('map-search')?.value || '').toLowerCase();
    const models  = (APP.models || []).filter(m => !search || m.name.toLowerCase().includes(search));
    const container = document.getElementById('map-tree-content');

    if (!models.length) {
        container.innerHTML = '<p class="text-slate-400 text-sm">No models match your search.</p>';
        return;
    }

    let html = '';
    for (const model of models) {
        const rels  = model.relationships || [];
        const total = rels.length;

        const relTypeCounts = {};
        rels.forEach(r => { relTypeCounts[r.type] = (relTypeCounts[r.type] || 0) + 1; });
        const relSummary = Object.entries(relTypeCounts)
            .map(([t, n]) => `<span class="text-xs px-1.5 py-0.5 rounded border ${REL_COLORS[t] || 'bg-slate-50 text-slate-500 border-slate-200'}">${n} ${t}</span>`)
            .join('');

        html += `<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden model-tree-card" data-name="${model.name.toLowerCase()}">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-50 bg-slate-50/60">
                <div class="w-9 h-9 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-700 font-bold">${model.name[0]}</div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="font-semibold text-slate-800">${model.name}</p>
                        <code class="text-xs text-slate-400 font-mono">${model.table}</code>
                    </div>
                    <div class="flex flex-wrap gap-1 mt-1">${relSummary}</div>
                </div>
                <span class="text-xs bg-slate-200 text-slate-500 px-2 py-0.5 rounded-full shrink-0">${total} rel${total !== 1 ? 's' : ''}</span>
            </div>
            <div class="px-5 py-3">
                ${total === 0
                    ? '<p class="text-xs text-slate-400 py-1 italic">No relationships defined</p>'
                    : buildRelBranch(model.name, 0, new Set([model.name]))
                }
            </div>
        </div>`;
    }

    container.innerHTML = html;
}

function filterModelTree() {
    if (!mapTreeRendered) return;
    renderModelTree();
}

function setMapTab(tab) {
    ['graph','tree','er'].forEach(t => {
        document.getElementById('map-' + t).style.display = t === tab ? 'block' : 'none';
        const btn = document.getElementById('map-tab-' + t);
        if (btn) btn.className = `px-3 py-1.5 rounded-md text-sm font-medium ${t === tab ? 'bg-white shadow-sm text-slate-700' : 'text-slate-500'}`;
    });

    if (tab === 'graph' && !graphRendered) {
        setTimeout(initRelGraph, 50);
    }
    if (tab === 'tree' && !mapTreeRendered) {
        mapTreeRendered = true;
        renderModelTree();
    }
    if (tab === 'er' && !erRendered) {
        erRendered = true;
        const el = document.getElementById('er-diagram');
        if (el && typeof mermaid !== 'undefined') mermaid.run({ nodes: [el] });
    }
}


// ── Relation Graph (force-directed SVG) ───────────────────────────────────────

const RG_NW = 150, RG_NH = 60;
let _rgNodes = [], _rgSel = null, _rgAdj = {};
let _rgVp = { x: 0, y: 0, z: 1 };
let _rgW = 0, _rgH = 0;
let _rgMmParams = null;

function rgEdgeTheme(type) {
    if (type.includes('BelongsToMany') || type.includes('MorphToMany'))
        return { stroke:'#c084fc', marker:'url(#rg-arr-mm)',      markerA:'url(#rg-arr-mm-a)',      dash:'7,3' };
    if (type.includes('BelongsTo') || type.includes('MorphTo'))
        return { stroke:'#34d399', marker:'url(#rg-arr-belongs)', markerA:'url(#rg-arr-belongs-a)', dash:'none' };
    if (type.includes('Many'))
        return { stroke:'#818cf8', marker:'url(#rg-arr-many)',    markerA:'url(#rg-arr-many-a)',    dash:'none' };
    return     { stroke:'#2dd4bf', marker:'url(#rg-arr-one)',     markerA:'url(#rg-arr-one-a)',     dash:'5,3' };
}

function initRelGraph() {
    if (graphRendered) return;
    graphRendered = true;

    const models = APP.models || [];
    const svg    = document.getElementById('rg-canvas');
    if (!svg) return;
    _rgW = svg.clientWidth  || 900;
    _rgH = svg.clientHeight || 600;
    const W = _rgW, H = _rgH;
    const edgesG = document.getElementById('rg-edges-g');
    const nodesG = document.getElementById('rg-nodes-g');

    if (!models.length) {
        const t = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        t.setAttribute('x', W/2); t.setAttribute('y', H/2);
        t.setAttribute('text-anchor', 'middle'); t.setAttribute('fill', '#94a3b8');
        t.setAttribute('font-size', '14'); t.setAttribute('font-family', 'system-ui');
        t.textContent = 'No models found';
        nodesG.appendChild(t);
        return;
    }

    // Build node data from APP.models
    const nById = {};
    const nodes = models.map((m, i) => {
        const angle = (i / Math.max(models.length, 1)) * 2 * Math.PI - Math.PI / 2;
        const r     = Math.min(W, H) * 0.32;
        const node  = {
            id:    m.name,
            table: m.table || m.name.toLowerCase() + 's',
            rels:  (m.relationships || []).length,
            x: W/2 + r * Math.cos(angle),
            y: H/2 + r * Math.sin(angle),
            vx: 0, vy: 0,
        };
        nById[m.name] = node;
        return node;
    });

    // Build deduplicated edge list
    const edgeSet = new Map();
    models.forEach(m => {
        (m.relationships || []).forEach(rel => {
            const toName = rel.related ? rel.related.split('\\').pop() : null;
            if (!toName || !nById[toName] || toName === m.name) return;
            const key = m.name + '|' + toName + '|' + rel.type;
            if (!edgeSet.has(key)) edgeSet.set(key, { from: m.name, to: toName, type: rel.type });
        });
    });
    const edges = [...edgeSet.values()];

    // Force simulation (same constants as report.blade.php)
    const REPEL = 7000, IDEAL = 200, SPRING = 0.06, GRAV = 0.003, DAMP = 0.78;
    for (let it = 0; it < 350; it++) {
        for (let a = 0; a < nodes.length; a++) {
            for (let b = a + 1; b < nodes.length; b++) {
                const na = nodes[a], nb = nodes[b];
                const dx = na.x - nb.x, dy = na.y - nb.y;
                const d2 = Math.max(dx*dx + dy*dy, 100), d = Math.sqrt(d2), f = REPEL / d2;
                na.vx += dx/d*f; na.vy += dy/d*f;
                nb.vx -= dx/d*f; nb.vy -= dy/d*f;
            }
        }
        edges.forEach(e => {
            const na = nById[e.from], nb = nById[e.to];
            if (!na || !nb) return;
            const dx = nb.x - na.x, dy = nb.y - na.y;
            const d  = Math.sqrt(dx*dx + dy*dy) || 1, f = (d - IDEAL) * SPRING;
            na.vx += dx/d*f; na.vy += dy/d*f;
            nb.vx -= dx/d*f; nb.vy -= dy/d*f;
        });
        nodes.forEach(n => {
            n.vx += (W/2 - n.x) * GRAV; n.vy += (H/2 - n.y) * GRAV;
            n.vx *= DAMP; n.vy *= DAMP;
            n.x = Math.max(RG_NW/2 + 20, Math.min(W - RG_NW/2 - 20, n.x + n.vx));
            n.y = Math.max(RG_NH/2 + 20, Math.min(H - RG_NH/2 - 20, n.y + n.vy));
        });
    }
    _rgNodes = nodes;

    // Draw edges via createElementNS (same as report.blade.php approach)
    edges.forEach(e => {
        const na = nById[e.from], nb = nById[e.to];
        if (!na || !nb) return;
        const th   = rgEdgeTheme(e.type);
        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        path.setAttribute('class', 'rg-edge-path g-edge');
        path.setAttribute('data-from', e.from);
        path.setAttribute('data-to',   e.to);
        path.setAttribute('data-type', e.type);
        path.setAttribute('fill',           'none');
        path.setAttribute('stroke',         th.stroke);
        path.setAttribute('stroke-width',   '1.5');
        path.setAttribute('stroke-opacity', '0.4');
        path.setAttribute('marker-end',     th.marker);
        if (th.dash !== 'none') path.setAttribute('stroke-dasharray', th.dash);
        _rgSetEdgePath(path, na, nb);
        edgesG.appendChild(path);
    });

    // Draw nodes via createElementNS
    nodes.forEach(n => {
        const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        g.setAttribute('class',     'rg-node-g g-node');
        g.setAttribute('data-id',   n.id);
        g.style.cursor = 'pointer';
        g.setAttribute('transform', 'translate(' + (n.x - RG_NW/2) + ',' + (n.y - RG_NH/2) + ')');

        const bg = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        bg.setAttribute('class',        'rg-node-bg g-node-bg');
        bg.setAttribute('width',        RG_NW);
        bg.setAttribute('height',       RG_NH);
        bg.setAttribute('rx',           '10');
        bg.setAttribute('fill',         'white');
        bg.setAttribute('stroke',       '#e2e8f0');
        bg.setAttribute('stroke-width', '1.5');
        bg.setAttribute('filter',       'url(#rg-f-node)');

        const bar = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        bar.setAttribute('class',  'rg-node-bar g-node-bar');
        bar.setAttribute('width',  RG_NW);
        bar.setAttribute('height', '5');
        bar.setAttribute('rx',     '5');
        bar.setAttribute('fill',   '#6366f1');

        const nm = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        nm.setAttribute('x',           RG_NW/2);
        nm.setAttribute('y',           '26');
        nm.setAttribute('text-anchor', 'middle');
        nm.setAttribute('font-family', 'ui-sans-serif,system-ui,sans-serif');
        nm.setAttribute('font-size',   '13');
        nm.setAttribute('font-weight', '800');
        nm.setAttribute('fill',        '#1e293b');
        nm.textContent = n.id.length > 17 ? n.id.slice(0, 16) + '…' : n.id;

        const tb = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        tb.setAttribute('x',           RG_NW/2);
        tb.setAttribute('y',           '40');
        tb.setAttribute('text-anchor', 'middle');
        tb.setAttribute('font-family', 'ui-monospace,monospace');
        tb.setAttribute('font-size',   '10');
        tb.setAttribute('fill',        '#94a3b8');
        tb.textContent = n.table.length > 20 ? n.table.slice(0, 19) + '…' : n.table;

        g.appendChild(bg); g.appendChild(bar); g.appendChild(nm); g.appendChild(tb);

        if (n.rels > 0) {
            const rb = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            rb.setAttribute('x',           RG_NW - 8);
            rb.setAttribute('y',           '56');
            rb.setAttribute('text-anchor', 'end');
            rb.setAttribute('font-size',   '9');
            rb.setAttribute('font-weight', '700');
            rb.setAttribute('fill',        '#a5b4fc');
            rb.textContent = n.rels + 'r';
            g.appendChild(rb);
        }
        g.addEventListener('click', ev => { ev.stopPropagation(); rgSelect(n.id); });
        nodesG.appendChild(g);
    });

    // Build adjacency map
    _rgAdj = {};
    nodes.forEach(n => { _rgAdj[n.id] = new Set(); });
    edges.forEach(e => {
        if (nById[e.from] && nById[e.to]) {
            _rgAdj[e.from].add(e.to);
            _rgAdj[e.to].add(e.from);
        }
    });

    // Pan & zoom interaction
    let isPan = false, panOrigin = { x: 0, y: 0 };
    const vpEl = document.getElementById('rg-vp');

    function applyVp() {
        vpEl.setAttribute('transform',
            'translate(' + (-_rgVp.x * _rgVp.z) + ',' + (-_rgVp.y * _rgVp.z) + ') scale(' + _rgVp.z + ')');
        _rgUpdateMinimap();
    }

    svg.addEventListener('mousedown', e => {
        if (!e.target.closest('.g-node')) {
            isPan = true; panOrigin = { x: e.clientX, y: e.clientY }; svg.style.cursor = 'grabbing';
        }
    });
    window.addEventListener('mousemove', e => {
        if (!isPan) return;
        _rgVp.x -= (e.clientX - panOrigin.x) / _rgVp.z;
        _rgVp.y -= (e.clientY - panOrigin.y) / _rgVp.z;
        panOrigin = { x: e.clientX, y: e.clientY };
        applyVp();
    });
    window.addEventListener('mouseup', () => { isPan = false; svg.style.cursor = 'grab'; });
    svg.addEventListener('wheel', e => {
        e.preventDefault();
        const rect   = svg.getBoundingClientRect();
        const mouseX = e.clientX - rect.left, mouseY = e.clientY - rect.top;
        const dataX  = _rgVp.x + mouseX / _rgVp.z, dataY = _rgVp.y + mouseY / _rgVp.z;
        _rgVp.z = Math.max(0.25, Math.min(4, _rgVp.z * (e.deltaY > 0 ? 0.88 : 1.14)));
        _rgVp.x = dataX - mouseX / _rgVp.z;
        _rgVp.y = dataY - mouseY / _rgVp.z;
        applyVp();
    }, { passive: false });

    svg.addEventListener('click', e => { if (e.target === svg) rgDiagClear(); });

    _rgInitMinimap(nodes);
    applyVp();
    graphFit();
}

function _rgSetEdgePath(path, na, nb) {
    const dx = nb.x - na.x, dy = nb.y - na.y;
    const d  = Math.sqrt(dx*dx + dy*dy) || 1, nx = dx/d, ny = dy/d;
    const x1 = na.x + nx*(RG_NW/2), y1 = na.y + ny*(RG_NH/2);
    const x2 = nb.x - nx*(RG_NW/2 + 6), y2 = nb.y - ny*(RG_NH/2 + 6);
    const mx = (x1+x2)/2 - ny*28, my = (y1+y2)/2 + nx*28;
    path.setAttribute('d',
        'M' + x1.toFixed(1) + ',' + y1.toFixed(1) +
        ' Q' + mx.toFixed(1) + ',' + my.toFixed(1) +
        ' ' + x2.toFixed(1) + ',' + y2.toFixed(1));
}

function _rgInitMinimap(nodes) {
    const mm = document.getElementById('rg-minimap');
    if (!mm) return;
    const W = _rgW, H = _rgH, mmW = 160, mmH = 100;
    const scale = Math.min(mmW / W, mmH / H) * 0.88;
    const offX  = (mmW - W * scale) / 2, offY = (mmH - H * scale) / 2;

    const bg = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
    bg.setAttribute('width', mmW); bg.setAttribute('height', mmH); bg.setAttribute('fill', '#f8fafc');
    mm.appendChild(bg);

    nodes.forEach(n => {
        const dot = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        dot.setAttribute('x',       offX + (n.x - RG_NW/2) * scale);
        dot.setAttribute('y',       offY + (n.y - RG_NH/2) * scale);
        dot.setAttribute('width',   Math.max(4, RG_NW * scale));
        dot.setAttribute('height',  Math.max(3, RG_NH * scale));
        dot.setAttribute('rx',      '2');
        dot.setAttribute('fill',    '#6366f1');
        dot.setAttribute('opacity', '0.45');
        mm.appendChild(dot);
    });

    const vr = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
    vr.setAttribute('id',           'rg-mm-vp');
    vr.setAttribute('fill',         'rgba(99,102,241,0.08)');
    vr.setAttribute('stroke',       '#6366f1');
    vr.setAttribute('stroke-width', '1.5');
    vr.setAttribute('rx',           '2');
    mm.appendChild(vr);

    _rgMmParams = { scale, offX, offY, mmW, mmH };
}

function _rgUpdateMinimap() {
    const vr = document.getElementById('rg-mm-vp');
    if (!vr || !_rgMmParams) return;
    const { scale, offX, offY, mmW, mmH } = _rgMmParams;
    const W = _rgW, H = _rgH;
    const vpW = W / _rgVp.z, vpH = H / _rgVp.z;
    vr.setAttribute('x',      Math.max(0, offX + _rgVp.x * scale));
    vr.setAttribute('y',      Math.max(0, offY + _rgVp.y * scale));
    vr.setAttribute('width',  Math.min(mmW, vpW * scale));
    vr.setAttribute('height', Math.min(mmH, vpH * scale));
}

function rgSelect(id) {
    if (_rgSel === id) { rgDiagClear(); return; }
    _rgSel = id;
    const conn = _rgAdj[id] || new Set();

    document.querySelectorAll('.g-node').forEach(g => {
        const nid = g.getAttribute('data-id');
        const bg  = g.querySelector('.g-node-bg'), bar = g.querySelector('.g-node-bar');
        if (!bg || !bar) return;
        if (nid === id) {
            bg.setAttribute('stroke',       '#6366f1');
            bg.setAttribute('stroke-width', '2.5');
            bg.setAttribute('filter',       'url(#rg-f-node-sel)');
            bar.setAttribute('fill', '#4f46e5');
            g.setAttribute('opacity', '1');
        } else if (conn.has(nid)) {
            bg.setAttribute('stroke',       '#6ee7b7');
            bg.setAttribute('stroke-width', '2');
            bg.setAttribute('filter',       'url(#rg-f-node-rel)');
            bar.setAttribute('fill', '#10b981');
            g.setAttribute('opacity', '1');
        } else {
            bg.setAttribute('stroke',       '#e2e8f0');
            bg.setAttribute('stroke-width', '1.5');
            bg.setAttribute('filter',       'url(#rg-f-node)');
            bar.setAttribute('fill', '#6366f1');
            g.setAttribute('opacity', '0.2');
        }
    });

    document.querySelectorAll('.g-edge').forEach(p => {
        const from = p.getAttribute('data-from'), to = p.getAttribute('data-to');
        const type = p.getAttribute('data-type');
        if (from === id || to === id) {
            const th = rgEdgeTheme(type);
            p.setAttribute('stroke-width',   '2.5');
            p.setAttribute('stroke-opacity', '0.95');
            p.setAttribute('marker-end',     th.markerA);
        } else {
            p.setAttribute('stroke-width',   '1');
            p.setAttribute('stroke-opacity', '0.07');
        }
    });

    // Update info strip
    const n     = _rgNodes.find(n => n.id === id);
    const model = (APP.models || []).find(m => m.name === id);
    const rels  = model ? (model.relationships || []) : [];
    document.getElementById('rg-info-name').textContent  = id;
    document.getElementById('rg-info-table').textContent = n ? n.table : '';
    document.getElementById('rg-info-count').textContent =
        rels.length + ' relationship' + (rels.length !== 1 ? 's' : '');

    // Populate relationship cards
    const cardsEl = document.getElementById('rg-rels-cards');
    cardsEl.innerHTML = '';
    document.getElementById('rg-rels-title').textContent = id + ' relationships';
    rels.forEach(e => {
        const other = e.related ? e.related.split('\\').pop() : '?';
        const th    = rgEdgeTheme(e.type);
        const card  = document.createElement('div');
        card.className = 'flex flex-col gap-1 px-3 py-2.5 rounded-xl border bg-gray-50 hover:bg-white transition-colors shadow-sm';
        card.style.borderLeftWidth = '3px';
        card.style.borderLeftColor = th.stroke;
        card.innerHTML =
            '<div class="flex items-center justify-between gap-2">' +
                '<span class="text-xs font-bold truncate" style="color:#1e293b">' + other + '</span>' +
                '<span class="text-xs font-mono px-1.5 py-0.5 rounded" style="background:' + th.stroke + '22;color:' + th.stroke + '">→</span>' +
            '</div>' +
            '<span class="text-xs font-semibold" style="color:' + th.stroke + '">' + e.type + '</span>' +
            '<span class="text-xs text-gray-400 font-mono">' + (e.method || '') + '()</span>';
        cardsEl.appendChild(card);
    });

    document.getElementById('rg-rels-panel').classList.add('hidden');
    document.getElementById('rg-rels-chevron').style.transform = '';
    document.getElementById('rg-info-row').classList.remove('hidden');
    document.getElementById('rg-legend').classList.add('hidden');
    document.getElementById('rg-clear-btn').style.display = '';
}

function rgDiagClear() {
    _rgSel = null;
    const si = document.getElementById('rg-search-input');
    if (si) si.value = '';
    document.querySelectorAll('.g-node').forEach(g => {
        g.setAttribute('opacity', '1');
        const bg  = g.querySelector('.g-node-bg'), bar = g.querySelector('.g-node-bar');
        if (bg)  { bg.setAttribute('stroke', '#e2e8f0'); bg.setAttribute('stroke-width', '1.5'); bg.setAttribute('filter', 'url(#rg-f-node)'); }
        if (bar) bar.setAttribute('fill', '#6366f1');
    });
    document.querySelectorAll('.g-edge').forEach(p => {
        const th = rgEdgeTheme(p.getAttribute('data-type'));
        p.setAttribute('stroke',         th.stroke);
        p.setAttribute('stroke-width',   '1.5');
        p.setAttribute('stroke-opacity', '0.4');
        p.setAttribute('marker-end',     th.marker);
    });
    document.getElementById('rg-info-row').classList.add('hidden');
    document.getElementById('rg-rels-panel').classList.add('hidden');
    document.getElementById('rg-rels-chevron').style.transform = '';
    document.getElementById('rg-legend').classList.remove('hidden');
    document.getElementById('rg-clear-btn').style.display = 'none';
}

function rgToggleRels() {
    const panel   = document.getElementById('rg-rels-panel');
    const chevron = document.getElementById('rg-rels-chevron');
    const open    = panel.classList.toggle('hidden');
    chevron.style.transform = open ? '' : 'rotate(180deg)';
}

function graphZoom(factor) {
    if (!_rgW) return;
    const W = _rgW, H = _rgH;
    const cx = _rgVp.x + W / (2 * _rgVp.z);
    const cy = _rgVp.y + H / (2 * _rgVp.z);
    _rgVp.z  = Math.max(0.25, Math.min(4, _rgVp.z * factor));
    _rgVp.x  = cx - W / (2 * _rgVp.z);
    _rgVp.y  = cy - H / (2 * _rgVp.z);
    const vpEl = document.getElementById('rg-vp');
    if (vpEl) vpEl.setAttribute('transform',
        'translate(' + (-_rgVp.x * _rgVp.z) + ',' + (-_rgVp.y * _rgVp.z) + ') scale(' + _rgVp.z + ')');
    _rgUpdateMinimap();
}

function graphFit() {
    if (!_rgNodes.length || !_rgW) return;
    const W = _rgW, H = _rgH;
    const xs = _rgNodes.map(n => n.x), ys = _rgNodes.map(n => n.y);
    const minX = Math.min(...xs) - RG_NW/2 - 20, maxX = Math.max(...xs) + RG_NW/2 + 20;
    const minY = Math.min(...ys) - RG_NH/2 - 20, maxY = Math.max(...ys) + RG_NH/2 + 20;
    _rgVp.z = Math.max(0.25, Math.min(4, Math.min(W / (maxX - minX), H / (maxY - minY))));
    _rgVp.x = minX - (W/_rgVp.z - (maxX - minX)) / 2;
    _rgVp.y = minY - (H/_rgVp.z - (maxY - minY)) / 2;
    const vpEl = document.getElementById('rg-vp');
    if (vpEl) vpEl.setAttribute('transform',
        'translate(' + (-_rgVp.x * _rgVp.z) + ',' + (-_rgVp.y * _rgVp.z) + ') scale(' + _rgVp.z + ')');
    _rgUpdateMinimap();
}

function graphReset() {
    _rgVp = { x: 0, y: 0, z: 1 };
    const vpEl = document.getElementById('rg-vp');
    if (vpEl) vpEl.setAttribute('transform', 'translate(0,0) scale(1)');
    _rgUpdateMinimap();
    rgDiagClear();
}

function graphSearch(query) {
    query = (query || '').toLowerCase().trim();
    if (!query) { rgDiagClear(); return; }
    document.querySelectorAll('.g-node').forEach(g => {
        const nid   = g.getAttribute('data-id');
        const match = nid.toLowerCase().includes(query);
        g.setAttribute('opacity', match ? '1' : '0.12');
        const bg = g.querySelector('.g-node-bg'), bar = g.querySelector('.g-node-bar');
        if (bg) {
            bg.setAttribute('stroke',       match ? '#6366f1' : '#e2e8f0');
            bg.setAttribute('stroke-width', match ? '2.5'     : '1.5');
            bg.setAttribute('filter',       match ? 'url(#rg-f-node-sel)' : 'url(#rg-f-node)');
        }
        if (bar) bar.setAttribute('fill', match ? '#4f46e5' : '#6366f1');
    });
    document.querySelectorAll('.g-edge').forEach(p => {
        const from  = p.getAttribute('data-from'), to = p.getAttribute('data-to');
        const match = from.toLowerCase().includes(query) || to.toLowerCase().includes(query);
        p.setAttribute('stroke-opacity', match ? '0.7'  : '0.04');
        p.setAttribute('stroke-width',   match ? '2'    : '1');
    });
    document.getElementById('rg-clear-btn').style.display = '';
}


// ── API Docs ──────────────────────────────────────────────────────────────────

let _apiActiveMethod = 'ALL';

function apiToggle(uid) {
    const detail  = document.getElementById('detail-'  + uid);
    const chevron = document.getElementById('chevron-' + uid);
    if (!detail) return;
    const isHidden = detail.classList.toggle('hidden');
    chevron.style.transform = isHidden ? '' : 'rotate(180deg)';
    if (!isHidden) apiRenderFlow(uid);
}

function apiRenderFlow(uid) {
    const el = document.getElementById('api-flow-' + uid);
    if (!el || el.dataset.rendered) return;
    el.dataset.rendered = '1';

    const ctrl   = el.dataset.controller ? el.dataset.controller.split('\\').pop() : null;
    const action = el.dataset.action || null;
    const method = el.dataset.method || '';
    const uri    = el.dataset.uri || '';
    const rname  = el.dataset.rname || '';
    let mws = [];
    try { mws = JSON.parse(el.dataset.mws || '[]'); } catch(e) { mws = []; }
    const chain = ctrl ? traceChain(ctrl) : [];

    // No chain and no ctrl: nothing to show
    if (!ctrl && !chain.length) return;

    // Build local graph (use unique prefix to avoid DOM id collisions)
    let cnt = 0;
    const pfx    = 'af' + uid.replace(/[^a-z0-9]/gi, '');
    const lNodes = {}, lEdges = [];
    const mkN = (type, name, sub) => {
        const id = pfx + (cnt++);
        lNodes[id] = { id, type, name: name || '', sub: sub || '' };
        return id;
    };
    const mkE = (f, t, label) => lEdges.push({ from: f, to: t, label: label || '' });

    const rId = mkN('request', (method ? method + ' ' : '') + '/' + uri, rname);
    let prev = rId;

    if (mws.length) {
        const mId = mkN('middleware', 'Middleware Stack', mws.length + ' layer' + (mws.length > 1 ? 's' : ''));
        mkE(prev, mId, 'enters');
        prev = mId;
    }

    if (chain.length) {
        let last = prev;
        chain.forEach((node, i) => {
            const sub = i === 0
                ? (action && action !== '__invoke' ? '@' + action : 'Invokable')
                : (RF_TYPE_LABEL[node.layer] || node.layer);
            const nid = mkN(node.layer, node.name, sub);
            mkE(last, nid, i === 0 ? 'dispatches' : (EDGE_LABEL[node.edgeType] || node.edgeType || 'calls'));
            last = nid;
        });
        const mn = chain.find(n => n.layer === 'model');
        if (mn) {
            const md = (APP.models || []).find(m => m.name === mn.name);
            if (md?.table) {
                const dbId  = mkN('database', md.table, 'DB Table');
                const mnId  = Object.values(lNodes).find(n => n.name === mn.name && n.type === 'model')?.id;
                if (mnId) mkE(mnId, dbId, 'queries');
            }
        }
    } else if (ctrl) {
        const cid = mkN('controller', ctrl, action && action !== '__invoke' ? '@' + action : 'Invokable');
        mkE(prev, cid, 'dispatches');
    }

    // Horizontal layout (left → right, single row)
    const NW = 196, NH = 66, GAP_X = 72, PAD = 20;
    const nodeList = Object.values(lNodes);
    const CW = nodeList.length * NW + (nodeList.length - 1) * GAP_X + PAD * 2;
    const CH = NH + PAD * 2;
    const pos = {};
    nodeList.forEach((n, i) => {
        const x = PAD + i * (NW + GAP_X);
        pos[n.id] = { x, y: PAD, cx: x + NW / 2, cy: PAD + NH / 2 };
    });

    // Unique SVG ids
    const dotId = pfx + 'dot', arrId = pfx + 'arr';

    const defs = `<defs>
        <pattern id="${dotId}" x="0" y="0" width="22" height="22" patternUnits="userSpaceOnUse">
            <circle cx="1" cy="1" r="0.8" fill="#cbd5e1" opacity="0.6"/>
        </pattern>
        <marker id="${arrId}" markerWidth="9" markerHeight="7" refX="8" refY="3.5" orient="auto">
            <polygon points="0 0,9 3.5,0 7" fill="#94a3b8"/>
        </marker>
    </defs>`;

    const edgesSvg = lEdges.map(e => {
        const f = pos[e.from], t = pos[e.to];
        if (!f || !t) return '';
        const x1 = f.x + NW, y1 = f.cy, x2 = t.x, y2 = t.cy;
        const cp = (x2 - x1) * 0.45;
        const d  = `M${x1},${y1} C${x1+cp},${y1} ${x2-cp},${y2} ${x2},${y2}`;
        const mx = (x1 + x2) / 2, my = (y1 + y2) / 2;
        const lw = (e.label.length * 6) + 14;
        return `<g>
            <path d="${d}" fill="none" stroke="#cbd5e1" stroke-width="1.5" marker-end="url(#${arrId})"/>
            ${e.label ? `<rect x="${mx-lw/2}" y="${my-8}" width="${lw}" height="16" rx="8" fill="#ffffff" stroke="#e2e8f0" stroke-width="1"/>
            <text x="${mx}" y="${my+4}" fill="#64748b" font-size="9" font-family="ui-monospace,monospace" text-anchor="middle" font-weight="600">${_esc(e.label)}</text>` : ''}
        </g>`;
    }).join('');

    const nodesSvg = nodeList.map(n => {
        const p = pos[n.id]; if (!p) return '';
        const c  = RF_COLOR[n.type] || RF_COLOR.unknown;
        const tl = RF_TYPE_LABEL[n.type] || n.type;
        const sn = n.name.length > 21 ? n.name.slice(0, 20) + '…' : n.name;
        const ss = n.sub.length  > 24 ? n.sub.slice(0, 23)  + '…' : n.sub;
        return `<g>
            <rect x="${p.x}" y="${p.y}" width="${NW}" height="${NH}" rx="12" fill="${c.bg}" stroke="${c.border}" stroke-width="1.5"/>
            <rect x="${p.x+1.5}" y="${p.y+1.5}" width="${NW-3}" height="18" rx="10" fill="${c.border}" fill-opacity="0.15"/>
            <circle cx="${p.x+12}" cy="${p.y+10}" r="3" fill="${c.dot}"/>
            <text x="${p.x+21}" y="${p.y+14}" fill="${c.type}" font-size="7.5" font-family="ui-monospace,monospace" font-weight="700" letter-spacing="0.1em">${tl.toUpperCase()}</text>
            <text x="${p.x+10}" y="${p.y+40}" fill="${c.name}" font-size="11.5" font-family="ui-monospace,monospace" font-weight="700">${_esc(sn)}</text>
            ${ss ? `<text x="${p.x+10}" y="${p.y+56}" fill="${c.sub}" font-size="9.5" font-family="ui-monospace,monospace">${_esc(ss)}</text>` : ''}
        </g>`;
    }).join('');

    el.innerHTML = `
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:10px;padding-top:4px">
            <div style="width:3px;height:14px;border-radius:2px;background:#6366f1;flex-shrink:0"></div>
            <p style="font-size:10px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:0.1em;margin:0;font-family:ui-monospace,monospace">Request Flow</p>
            <span style="font-size:9px;color:#94a3b8;font-family:ui-monospace,monospace">${nodeList.length} nodes · ${lEdges.length} edges</span>
        </div>
        <div style="overflow-x:auto;border-radius:12px;border:1px solid #e2e8f0;background:#f8fafc">
            <svg width="${CW}" height="${CH}" style="display:block;min-width:${CW}px">
                ${defs}
                <rect width="${CW}" height="${CH}" fill="#f8fafc"/>
                <rect width="${CW}" height="${CH}" fill="url(#${dotId})"/>
                ${edgesSvg}
                ${nodesSvg}
            </svg>
        </div>`;
}

function apiScrollTo(groupName) {
    const el = document.getElementById('api-group-' + groupName);
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        document.querySelectorAll('.api-nav-item').forEach(a => a.classList.remove('bg-indigo-50','text-indigo-700','font-semibold'));
        const nav = document.querySelector(`.api-nav-item[onclick*="${groupName}"]`);
        if (nav) nav.classList.add('bg-indigo-50','text-indigo-700','font-semibold');
    }
}

function apiFilter(method) {
    _apiActiveMethod = method;
    document.querySelectorAll('.api-filter-btn').forEach(btn => {
        const active = btn.dataset.method === method;
        btn.className = btn.className
            .replace(/bg-slate-800 text-white border-slate-800|bg-white text-slate-500 border-slate-200 hover:border-slate-400/g, '');
        btn.classList.add(...(active
            ? ['bg-slate-800', 'text-white', 'border-slate-800']
            : ['bg-white', 'text-slate-500', 'border-slate-200', 'hover:border-slate-400']));
    });
    _apiApplyFilters();
}

function apiSearch(q) {
    _apiApplyFilters(q.toLowerCase().trim());
}

function _apiApplyFilters(q) {
    const query  = q ?? (document.getElementById('api-search')?.value || '').toLowerCase().trim();
    const method = _apiActiveMethod;

    document.querySelectorAll('.api-endpoint-wrap').forEach(ep => {
        const matchMethod = method === 'ALL' || ep.dataset.method === method;
        const matchQuery  = !query || ep.dataset.uri.includes(query);
        ep.style.display  = matchMethod && matchQuery ? '' : 'none';
    });

    // Hide group headers if all their endpoints are hidden
    document.querySelectorAll('.api-group').forEach(grp => {
        const visible = [...grp.querySelectorAll('.api-endpoint-wrap')].some(ep => ep.style.display !== 'none');
        grp.style.display = visible ? '' : 'none';
    });
}

// ── Boot ──────────────────────────────────────────────────────────────────────
mermaid.initialize({
    startOnLoad: false,
    theme: 'base',
    themeVariables: { primaryColor:'#e0e7ff', primaryBorderColor:'#6366f1', primaryTextColor:'#1e1b4b' },
    flowchart: { rankSpacing:80, nodeSpacing:40, curve:'basis', padding:20 }
});

navigate('overview');

// ── Dependency Graph (custom layered SVG) ────────────────────────────────────

const _DEP_NW  = 114;   // node width
const _DEP_NH  = 32;    // node height
const _DEP_HG  = 10;    // horizontal gap between nodes
const _DEP_MR  = 9;     // max nodes per row within a layer
const _DEP_RG  = 14;    // gap between rows within a layer
const _DEP_LG  = 80;    // gap between layers

const _DEP_CFG = {
    controller: { label:'Controllers', color:'#3b82f6', bg:'#dbeafe', order:0 },
    job:        { label:'Jobs',        color:'#ca8a04', bg:'#fef9c3', order:1 },
    event:      { label:'Events',      color:'#a855f7', bg:'#fdf4ff', order:1 },
    listener:   { label:'Listeners',   color:'#ec4899', bg:'#fce7f3', order:2 },
    service:    { label:'Services',    color:'#10b981', bg:'#d1fae5', order:2 },
    repository: { label:'Repositories',color:'#f59e0b', bg:'#fef3c7', order:3 },
    model:      { label:'Models',      color:'#8b5cf6', bg:'#ede9fe', order:4 },
    database:   { label:'Database',    color:'#64748b', bg:'#f1f5f9', order:5 },
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
        rect.setAttribute('fill', cfg.bg || '#f8fafc');
        rect.setAttribute('opacity', '0.35');
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
        path.setAttribute('stroke', '#94a3b8');
        path.setAttribute('stroke-width', '1.5');
        path.setAttribute('marker-end', 'url(#dep-arr)');
        path.setAttribute('opacity', '0.6');
        path.dataset.from = e.from;
        path.dataset.to   = e.to;
        edgesG.appendChild(path);
    });

    // Draw nodes
    nodes.forEach(n => {
        const pos = _depPos[n.name];
        if (!pos) return;
        const cfg = _DEP_CFG[n.layer] || { color:'#64748b', bg:'#f1f5f9' };

        const g = document.createElementNS(NS, 'g');
        g.style.cursor = 'pointer';
        g.dataset.name = n.name;

        const rect = document.createElementNS(NS, 'rect');
        rect.setAttribute('x', pos.x);
        rect.setAttribute('y', pos.y);
        rect.setAttribute('width', _DEP_NW);
        rect.setAttribute('height', _DEP_NH);
        rect.setAttribute('rx', '7');
        rect.setAttribute('fill', cfg.bg);
        rect.setAttribute('stroke', cfg.color);
        rect.setAttribute('stroke-width', '1.5');
        rect.setAttribute('filter', 'url(#dep-shadow)');

        // Truncate display name: strip suffix, add ellipsis
        const suffixes = /Controller$|Service$|Repository$|Observer$|Policy$|Listener$/;
        const short = n.name.replace(suffixes, '');
        const display = short.length > 13 ? short.substring(0, 12) + '…' : short;

        const text = document.createElementNS(NS, 'text');
        text.setAttribute('x', pos.x + _DEP_NW / 2);
        text.setAttribute('y', pos.y + _DEP_NH / 2 + 4);
        text.setAttribute('text-anchor', 'middle');
        text.setAttribute('font-size', '10.5');
        text.setAttribute('font-family', 'system-ui,sans-serif');
        text.setAttribute('font-weight', '500');
        text.setAttribute('fill', cfg.color);
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
    const cW = canvas.clientWidth, cH = canvas.clientHeight;
    const pad = 48;

    _depT.s  = Math.min((cW - pad*2) / gW, (cH - pad*2) / gH, 1.4);
    _depT.tx = cW/2 - _depT.s * (minX + gW/2);
    _depT.ty = cH/2 - _depT.s * (minY + gH/2);
    _depApplyT();
}

function depZoom(delta) {
    const canvas = document.getElementById('dep-canvas');
    const cW = canvas?.clientWidth || 800;
    const cH = canvas?.clientHeight || 600;
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
        document.getElementById('dep-sel-label').classList.add('hidden');
    } else {
        _depSel = name;
        depHighlight(name);
        const lbl = document.getElementById('dep-sel-label');
        lbl.textContent = name;
        lbl.classList.remove('hidden');
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
        p.setAttribute('stroke',       on ? '#6366f1' : '#e2e8f0');
        p.setAttribute('stroke-width', on ? '2'       : '1.5');
        p.setAttribute('opacity',      on ? '1'       : '0.15');
        p.setAttribute('marker-end',   on ? 'url(#dep-arr-hi)' : 'url(#dep-arr)');
    });

    document.querySelectorAll('#dep-nodes-g g[data-name]').forEach(g => {
        g.style.opacity = connected.has(g.dataset.name) ? '1' : '0.18';
    });
}

function depClearHighlight(resetSel = true) {
    if (resetSel) _depSel = null;
    document.querySelectorAll('#dep-edges-g path').forEach(p => {
        p.setAttribute('stroke',       '#94a3b8');
        p.setAttribute('stroke-width', '1.5');
        p.setAttribute('opacity',      '0.6');
        p.setAttribute('marker-end',   'url(#dep-arr)');
    });
    document.querySelectorAll('#dep-nodes-g g[data-name]').forEach(g => {
        g.style.opacity = '1';
    });
    const lbl = document.getElementById('dep-sel-label');
    if (lbl && resetSel) lbl.classList.add('hidden');
}

// ── AI Chat ───────────────────────────────────────────────────────────────────

const CHAT_ENDPOINT = '{{ route("architecture.ai.chat") }}';
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
        ? `<p class="text-xs text-slate-400 mt-2 pt-2 border-t border-slate-100">Context used: ${contextLabels.map(_esc).join(' · ')}</p>`
        : '';

    const bodyHtml = text === null
        ? `<span class="inline-flex gap-1 items-center text-slate-400 text-sm"><span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay:0s"></span><span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay:.15s"></span><span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay:.3s"></span></span>`
        : `<div class="prose-sm text-sm text-slate-700 leading-relaxed">${chatMarkdown(text)}</div>`;

    wrap.insertAdjacentHTML('beforeend', `
        <div class="flex ${isAI ? 'justify-start' : 'justify-end'} gap-2.5" id="${id}">
            ${isAI ? `<div class="w-7 h-7 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold shrink-0 mt-0.5">AI</div>` : ''}
            <div class="max-w-[80%] rounded-2xl px-4 py-3 ${isAI ? 'bg-white border border-slate-200 rounded-tl-sm' : 'bg-indigo-600 text-white rounded-tr-sm'}">
                ${bodyHtml}
                ${ctxHtml}
            </div>
            ${!isAI ? `<div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 text-xs font-bold shrink-0 mt-0.5">You</div>` : ''}
        </div>
    `);

    wrap.scrollTop = wrap.scrollHeight;
    return id;
}

function chatReplaceBubble(id, text, contextLabels = [], isError = false) {
    const el = document.getElementById(id);
    if (!el) return;
    const inner = el.querySelector('div.max-w-\\[80\\%\\]');
    if (!inner) return;

    const ctxHtml = contextLabels.length
        ? `<p class="text-xs text-slate-400 mt-2 pt-2 border-t border-slate-100">Context used: ${contextLabels.map(_esc).join(' · ')}</p>`
        : '';

    inner.innerHTML = `<div class="prose-sm text-sm ${isError ? 'text-red-600' : 'text-slate-700'} leading-relaxed">${chatMarkdown(text)}</div>${ctxHtml}`;

    const wrap = document.getElementById('chat-messages');
    wrap.scrollTop = wrap.scrollHeight;
}

function chatMarkdown(text) {
    return text
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/```(\w*)\n?([\s\S]*?)```/g, (_, lang, code) =>
            `<pre class="bg-slate-800 text-green-300 rounded-lg p-3 mt-2 mb-2 text-xs overflow-x-auto"><code>${code.trim()}</code></pre>`)
        .replace(/`([^`]+)`/g, '<code class="bg-slate-100 text-indigo-700 px-1 rounded text-xs">$1</code>')
        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.+?)\*/g, '<em>$1</em>')
        .replace(/^### (.+)$/gm, '<p class="font-semibold text-slate-800 mt-3 mb-1">$1</p>')
        .replace(/^## (.+)$/gm,  '<p class="font-bold text-slate-800 mt-3 mb-1 text-base">$1</p>')
        .replace(/^# (.+)$/gm,   '<p class="font-bold text-slate-900 mt-3 mb-2 text-lg">$1</p>')
        .replace(/^- (.+)$/gm,   '<li class="ml-4 list-disc">$1</li>')
        .replace(/\n\n/g, '<br>')
        .replace(/\n/g, ' ');
}

// ── AI Docs ────────────────────────────────────────────────────────────────────

const DOCS_ENDPOINT = '{{ route("architecture.ai.documentation") }}';
const _docsContent  = {};
const DOC_TYPES     = ['architecture','models','controllers','routes','services','modules'];

async function docsGenerate(type) {
    const btn    = document.getElementById('doc-gen-btn-' + type);
    const status = document.getElementById('doc-status-' + type);
    const dlBtn  = document.getElementById('doc-dl-btn-' + type);

    btn.disabled = true;
    btn.innerHTML = `<svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Generating…`;
    status.textContent = 'Generating…';
    status.className = 'text-xs text-indigo-500 shrink-0';

    try {
        const res  = await fetch(DOCS_ENDPOINT, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': AI_CSRF },
            body:    JSON.stringify({ type }),
        });
        const json = await res.json();
        if (!res.ok || json.error) throw new Error(json.error || 'Server error');

        _docsContent[type] = { content: json.content, filename: json.filename };

        status.textContent = '✔ Ready';
        status.className = 'text-xs text-green-600 shrink-0 font-medium';
        btn.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Re-generate`;
        btn.disabled = false;
        dlBtn.classList.remove('hidden');
        dlBtn.classList.add('flex');

        // Show "Download All" if at least one doc is ready
        document.getElementById('docs-download-all-btn').classList.remove('hidden');

    } catch (err) {
        status.textContent = '✘ Failed';
        status.className = 'text-xs text-red-500 shrink-0';
        btn.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Retry`;
        btn.disabled = false;
    }
}

async function docsGenerateAll() {
    for (const type of DOC_TYPES) {
        await docsGenerate(type);
    }
}

function docsDownload(type) {
    const doc = _docsContent[type];
    if (!doc) return;
    _downloadBlob(doc.content, doc.filename, 'text/markdown');
}

function docsDownloadAll() {
    const ready = DOC_TYPES.filter(t => _docsContent[t]);
    ready.forEach((type, i) => {
        setTimeout(() => docsDownload(type), i * 300);
    });
}

// ── AI Graphic Report ─────────────────────────────────────────────────────────

async function generateAIGraphicReport() {
    const btn    = document.getElementById('ai-report-btn');
    const label  = document.getElementById('ai-report-btn-label');
    const panel  = document.getElementById('ai-report-progress');
    const errEl  = document.getElementById('ai-report-error');
    const spinner = document.getElementById('ai-report-spinner');
    const title  = document.getElementById('ai-report-progress-title');

    btn.disabled  = true;
    label.textContent = 'Generating…';
    panel.classList.remove('hidden');
    errEl.classList.add('hidden');
    errEl.textContent = '';

    // Reset all step icons
    document.querySelectorAll('#ai-report-steps .step-icon').forEach(el => {
        el.className = 'step-icon w-4 h-4 rounded-full border-2 border-slate-300 flex-shrink-0';
    });

    const _stepDone  = (step) => {
        const el = document.querySelector(`[data-step="${step}"] .step-icon`);
        if (el) { el.className = 'step-icon w-4 h-4 rounded-full bg-green-500 flex-shrink-0'; el.innerHTML = '<svg viewBox="0 0 16 16" fill="white"><path d="M13 4L6.5 11 3 7.5"/><path stroke="white" stroke-width="1.8" stroke-linecap="round" fill="none" d="M13 4L6.5 11 3 7.5"/></svg>'; }
    };
    const _stepActive = (step) => {
        const el = document.querySelector(`[data-step="${step}"] .step-icon`);
        if (el) { el.className = 'step-icon w-4 h-4 rounded-full border-2 border-violet-500 bg-violet-100 flex-shrink-0 animate-pulse'; }
    };
    const _stepFail  = (step) => {
        const el = document.querySelector(`[data-step="${step}"] .step-icon`);
        if (el) { el.className = 'step-icon w-4 h-4 rounded-full bg-amber-400 flex-shrink-0'; }
    };

    let aiAnalysis = null;
    const aiDocs   = {};

    try {
        // ── Step 1: AI analyze ──────────────────────────────────────────────
        _stepActive('analyze');
        const analyzeRes  = await fetch(AI_ENDPOINT, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': AI_CSRF },
        });
        const analyzeJson = await analyzeRes.json();
        if (!analyzeRes.ok || analyzeJson.error) throw new Error(analyzeJson.error || 'AI analysis failed');
        aiAnalysis = analyzeJson;
        _stepDone('analyze');

        // ── Steps 2–7: AI docs per section ─────────────────────────────────
        for (const type of DOC_TYPES) {
            _stepActive(type);
            try {
                const res  = await fetch(DOCS_ENDPOINT, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': AI_CSRF },
                    body:    JSON.stringify({ type }),
                });
                const json = await res.json();
                if (!res.ok || json.error) throw new Error(json.error);
                aiDocs[type] = json.content;
                _stepDone(type);
            } catch(e) {
                // Non-fatal: skip section, mark with warning
                _stepFail(type);
                aiDocs[type] = null;
            }
        }

        // ── Step 8: Build & download ────────────────────────────────────────
        _stepActive('build');
        const html = _buildAIGraphicReport(APP, aiAnalysis, aiDocs);
        _downloadBlob(html, 'ai-architecture-report.html', 'text/html;charset=utf-8');
        _stepDone('build');

        title.textContent = 'Report ready — downloading!';
        spinner.classList.add('hidden');

    } catch(err) {
        errEl.textContent = 'Error: ' + err.message;
        errEl.classList.remove('hidden');
        title.textContent = 'Generation failed';
        spinner.classList.add('hidden');
    } finally {
        btn.disabled = false;
        label.textContent = 'Generate AI Graphic Report';
    }
}

function _mdToHtml(md) {
    if (!md) return '';
    let html = _esc(md);
    // Code blocks (must be before inline code)
    html = html.replace(/```[\w]*\n?([\s\S]*?)```/g, (_, code) =>
        `<pre style="background:#1e293b;color:#e2e8f0;border-radius:10px;padding:16px;overflow-x:auto;font-family:ui-monospace,monospace;font-size:13px;line-height:1.6;margin:12px 0">${code.trim()}</pre>`
    );
    // Inline code
    html = html.replace(/`([^`]+)`/g, '<code style="background:#f1f5f9;color:#0f172a;padding:2px 6px;border-radius:4px;font-family:ui-monospace,monospace;font-size:0.9em">$1</code>');
    // Headings
    html = html.replace(/^### (.+)$/gm, '<h3 style="font-size:16px;font-weight:700;color:#1e293b;margin:20px 0 8px">$1</h3>');
    html = html.replace(/^## (.+)$/gm,  '<h2 style="font-size:20px;font-weight:800;color:#0f172a;margin:28px 0 10px;padding-bottom:6px;border-bottom:2px solid #e2e8f0">$1</h2>');
    html = html.replace(/^# (.+)$/gm,   '<h1 style="font-size:26px;font-weight:900;color:#0f172a;margin:0 0 16px">$1</h1>');
    // Bold / italic
    html = html.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
    html = html.replace(/\*([^*]+)\*/g,     '<em>$1</em>');
    // Unordered lists
    html = html.replace(/((?:^- .+\n?)+)/gm, (block) => {
        const items = block.trim().split('\n').map(l => `<li style="margin-bottom:4px">${l.replace(/^- /,'')}</li>`).join('');
        return `<ul style="list-style:disc;padding-left:20px;margin:8px 0">${items}</ul>`;
    });
    // Ordered lists
    html = html.replace(/((?:^\d+\. .+\n?)+)/gm, (block) => {
        const items = block.trim().split('\n').map(l => `<li style="margin-bottom:4px">${l.replace(/^\d+\. /,'')}</li>`).join('');
        return `<ol style="list-style:decimal;padding-left:20px;margin:8px 0">${items}</ol>`;
    });
    // Horizontal rule
    html = html.replace(/^---+$/gm, '<hr style="border:none;border-top:1px solid #e2e8f0;margin:20px 0"/>');
    // Paragraphs
    html = html.split(/\n\n+/).map(block => {
        if (block.match(/^<(h[1-3]|ul|ol|pre|hr)/)) return block;
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
        ['Models',       s.models??0,        '#8b5cf6','#f5f3ff','#ede9fe'],
        ['Controllers',  s.controllers??0,   '#3b82f6','#eff6ff','#dbeafe'],
        ['Routes',       s.routes??0,        '#10b981','#f0fdf4','#d1fae5'],
        ['Services',     s.services??0,      '#06b6d4','#ecfeff','#cffafe'],
        ['Repositories', s.repositories??0,  '#f59e0b','#fffbeb','#fef3c7'],
        ['Jobs',         s.jobs??0,          '#f97316','#fff7ed','#ffedd5'],
        ['Events',       s.events??0,        '#d946ef','#fdf4ff','#fae8ff'],
        ['Policies',     s.policies??0,      '#64748b','#f8fafc','#f1f5f9'],
        ['API Routes',   rs.api??0,          '#0891b2','#ecfeff','#cffafe'],
        ['Named Routes', rs.named_count??0,  '#7c3aed','#f5f3ff','#ede9fe'],
    ];
    const statCards = stats.map(([name, count, color, bg, border]) =>
        `<div style="background:${bg};border:1px solid ${border};border-radius:14px;padding:16px 18px">
            <div style="font-size:26px;font-weight:800;color:${color};font-family:system-ui,sans-serif">${count}</div>
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
    const sec = (title, color, content) =>
        `<section style="margin-bottom:52px">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:22px">
                <div style="width:4px;height:34px;border-radius:2px;background:${color};flex-shrink:0"></div>
                <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;font-family:system-ui,sans-serif">${esc(title)}</h2>
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
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#f8fafc;font-family:system-ui,-apple-system,sans-serif;color:#1e293b;line-height:1.5}
@media print{body{background:#fff}.no-print{display:none!important}}
</style>
</head>
<body>

<!-- HEADER -->
<div style="background:linear-gradient(135deg,#1e1b4b 0%,#312e81 60%,#4c1d95 100%);padding:48px;display:flex;align-items:center;justify-content:space-between;gap:24px;flex-wrap:wrap">
    <div>
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:20px;padding:4px 14px;margin-bottom:16px">
            <span style="font-size:11px;color:#a5b4fc;font-weight:700;letter-spacing:0.1em">AI-POWERED ARCHITECTURE REPORT</span>
        </div>
        <h1 style="font-size:36px;font-weight:900;color:#fff;margin-bottom:10px">${esc(proj)}</h1>
        <div style="display:flex;gap:20px;flex-wrap:wrap">
            <span style="font-size:13px;color:#a5b4fc">Laravel ${esc(d.laravel_version ?? '')}</span>
            <span style="font-size:13px;color:#a5b4fc">PHP ${esc(d.php_version ?? '')}</span>
            <span style="font-size:13px;color:#a5b4fc">Generated ${esc(d.generated_at ?? '')}</span>
            <span style="font-size:13px;color:#a5b4fc">Provider: ${esc(ai?.provider ?? d.ai_provider ?? 'AI')}</span>
        </div>
    </div>
    <div style="text-align:center;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:20px;padding:24px 32px;flex-shrink:0">
        <div style="font-size:52px;font-weight:900;line-height:1;color:#fff">${score}</div>
        <div style="font-size:15px;font-weight:700;color:#a78bfa;margin-top:6px">${esc(grade)}</div>
        <div style="font-size:11px;color:#6d6d9a;margin-top:4px">Architecture Score</div>
    </div>
</div>

<!-- AI SUMMARY BANNER -->
${ai?.summary ? `<div style="background:#f0f9ff;border-bottom:2px solid #bfdbfe;padding:24px 48px">
    <p style="font-size:11px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:8px">AI Executive Summary</p>
    <p style="font-size:15px;color:#1e3a5f;line-height:1.7;max-width:900px">${esc(ai.summary)}</p>
</div>` : ''}

<!-- BODY -->
<div style="max-width:1200px;margin:0 auto;padding:48px 32px">

    <!-- Stats Grid -->
    ${sec('Component Overview', '#4f46e5',
        `<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:12px">${statCards}</div>`
    )}

    <!-- Score -->
    ${sec('Architecture Score', '#10b981',
        `<div style="display:grid;grid-template-columns:160px 1fr;gap:24px;background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:24px;align-items:start">
            ${gaugeSvg}
            <table style="width:100%;border-collapse:collapse;font-family:system-ui,sans-serif"><tbody>${checkRows}</tbody></table>
        </div>`
    )}

    <!-- SOLID Review -->
    ${solidCards ? sec('SOLID Principles', '#6366f1',
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
    ${depSvg ? sec('Dependency Graph', '#6366f1',
        `<div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden"><div style="overflow-x:auto;padding:20px">${depSvg}</div></div>`
    ) : ''}

    <!-- AI Documentation sections -->
    ${docSection('architecture', 'Architecture Overview', '#4f46e5')}
    ${docSection('models',       'Models Documentation',  '#8b5cf6')}
    ${docSection('controllers',  'Controllers Documentation', '#3b82f6')}
    ${docSection('routes',       'Routes Documentation',  '#10b981')}
    ${docSection('services',     'Services Documentation','#f59e0b')}
    ${docSection('modules',      'Modules Documentation', '#06b6d4')}

</div>

<!-- FOOTER -->
<div style="background:#1e293b;color:#64748b;text-align:center;padding:28px;font-size:12px;font-family:system-ui,sans-serif">
    AI Architecture Report · Generated by <strong style="color:#94a3b8">laravel-architecture-discovery</strong> · ${esc(d.generated_at ?? '')}
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
    out.push('> Laravel ' + d.laravel_version + ' · PHP ' + d.php_version + ' · laravel-architecture-discovery v' + d.package_version);
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
        ['Jobs', s.jobs], ['Events', s.events], ['Services', s.services],
        ['Repositories', s.repositories], ['Observers', s.observers],
        ['Policies', s.policies], ['Modules', s.modules], ['Packages', s.packages],
        ['Dep. Edges', (d.dependencies?.edges || []).length],
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

function exportPdf() {
    navigate('overview');
    setTimeout(() => window.print(), 300);
}

function exportPng() {
    const btn   = document.getElementById('export-png-btn');
    const label = document.getElementById('export-png-label');
    label.textContent = 'Generating…';
    btn.disabled = true;

    const exportUrl = window.location.pathname.replace(/\/$/, '') + '/export/svg';

    fetch(exportUrl)
        .then(r => r.text())
        .then(svgText => {
            const blob = new Blob([svgText], { type: 'image/svg+xml' });
            const url  = URL.createObjectURL(blob);
            const img  = new Image();

            img.onload = () => {
                const canvas = document.createElement('canvas');
                canvas.width  = img.naturalWidth  || 1000;
                canvas.height = img.naturalHeight || 720;
                const ctx = canvas.getContext('2d');
                ctx.fillStyle = '#f8fafc';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0);
                URL.revokeObjectURL(url);

                canvas.toBlob(pngBlob => {
                    _downloadBlob(pngBlob, 'architecture.png', 'image/png', true);
                    label.textContent = 'Download';
                    btn.disabled = false;
                }, 'image/png');
            };

            img.onerror = () => {
                URL.revokeObjectURL(url);
                label.textContent = 'Download';
                btn.disabled = false;
                alert('PNG export failed. Try downloading the SVG and converting it with an image editor.');
            };

            img.src = url;
        })
        .catch(() => {
            label.textContent = 'Download';
            btn.disabled = false;
            alert('Could not fetch SVG from server. Make sure the export route is accessible.');
        });
}

function previewSvg() {
    const modal   = document.getElementById('svg-preview-modal');
    const content = document.getElementById('svg-preview-content');
    modal.style.removeProperty('display');

    const exportUrl = window.location.pathname.replace(/\/$/, '') + '/export/svg';
    content.innerHTML = '<p class="text-slate-400 text-sm">Loading…</p>';

    fetch(exportUrl)
        .then(r => r.text())
        .then(svgText => { content.innerHTML = svgText; })
        .catch(() => { content.innerHTML = '<p class="text-red-500 text-sm">Failed to load SVG preview.</p>'; });
}

function closeSvgPreview() {
    document.getElementById('svg-preview-modal').style.display = 'none';
}

// ── Graphic Report ────────────────────────────────────────────────────────────

function exportGraphicHTML() {
    const btn   = document.getElementById('graphic-report-btn');
    const label = document.getElementById('graphic-report-label');
    btn.disabled = true;
    label.textContent = 'Building…';

    try {
        const html = _buildGraphicReport(APP);
        const blob = new Blob([html], { type: 'text/html;charset=utf-8' });
        const a    = Object.assign(document.createElement('a'), {
            href:     URL.createObjectURL(blob),
            download: 'architecture-report.html',
        });
        a.click();
        URL.revokeObjectURL(a.href);
    } catch(e) {
        alert('Report generation failed: ' + e.message);
    } finally {
        btn.disabled     = false;
        label.textContent = 'Generate & Download';
    }
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
        ['Models',       s.models       ?? 0, '#8b5cf6', '#f5f3ff', '#ede9fe'],
        ['Controllers',  s.controllers  ?? 0, '#3b82f6', '#eff6ff', '#dbeafe'],
        ['Routes',       s.routes       ?? 0, '#10b981', '#f0fdf4', '#d1fae5'],
        ['Services',     s.services     ?? 0, '#06b6d4', '#ecfeff', '#cffafe'],
        ['Repositories', s.repositories ?? 0, '#f59e0b', '#fffbeb', '#fef3c7'],
        ['Jobs',         s.jobs         ?? 0, '#f97316', '#fff7ed', '#ffedd5'],
        ['Events',       s.events       ?? 0, '#d946ef', '#fdf4ff', '#fae8ff'],
        ['Observers',    s.observers    ?? 0, '#ec4899', '#fdf2f8', '#fce7f3'],
        ['Policies',     s.policies     ?? 0, '#64748b', '#f8fafc', '#f1f5f9'],
        ['Modules',      s.modules      ?? 0, '#4f46e5', '#eef2ff', '#e0e7ff'],
        ['API Routes',   rs.api         ?? 0, '#0891b2', '#ecfeff', '#cffafe'],
        ['Named Routes', rs.named_count ?? 0, '#7c3aed', '#f5f3ff', '#ede9fe'],
    ];
    const statCards = stats.map(([name, count, color, bg, border]) =>
        `<div style="background:${bg};border:1px solid ${border};border-radius:14px;padding:16px 20px;display:flex;flex-direction:column;gap:4px">
            <span style="font-size:24px;font-weight:800;color:${color};font-family:system-ui,sans-serif">${count}</span>
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
    const secHeader = (title, sub, color = '#4f46e5') =>
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
*{box-sizing:border-box;margin:0;padding:0}
body{background:#f8fafc;font-family:system-ui,-apple-system,sans-serif;color:#1e293b;line-height:1.5}
a{color:inherit;text-decoration:none}
table tr:nth-child(even){background:#fafbfc}
table tr:hover{background:#f1f5f9}
@media print{body{background:#fff}.no-print{display:none}}
</style>
</head>
<body>

<!-- ═══ HEADER ═══════════════════════════════════════════════════════════ -->
<div style="background:linear-gradient(135deg,#1e293b 0%,#312e81 100%);color:#fff;padding:40px 48px;display:flex;align-items:center;justify-content:space-between;gap:20px">
    <div>
        <p style="font-size:12px;text-transform:uppercase;letter-spacing:0.12em;color:#94a3b8;margin-bottom:6px">Architecture Report</p>
        <h1 style="font-size:32px;font-weight:800;margin-bottom:8px">${esc(proj)}</h1>
        <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:8px">
            <span style="font-size:13px;color:#94a3b8">Laravel ${esc(d.laravel_version ?? '')}</span>
            <span style="font-size:13px;color:#94a3b8">PHP ${esc(d.php_version ?? '')}</span>
            <span style="font-size:13px;color:#94a3b8">Generated: ${esc(d.generated_at ?? '')}</span>
        </div>
    </div>
    <div style="text-align:center;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:20px;padding:20px 28px;flex-shrink:0">
        <div style="font-size:48px;font-weight:900;line-height:1;color:#fff">${score}</div>
        <div style="font-size:14px;font-weight:700;color:#a78bfa;margin-top:4px">${esc(grade)}</div>
        <div style="font-size:11px;color:#64748b;margin-top:2px">Architecture Score</div>
    </div>
</div>

<!-- ═══ BODY ═════════════════════════════════════════════════════════════ -->
<div style="max-width:1200px;margin:0 auto;padding:40px 32px;display:flex;flex-direction:column;gap:48px">

    <!-- Stat Cards -->
    <section>
        ${secHeader('Component Overview', `${(d.models??[]).length} models · ${(d.controllers??[]).length} controllers · ${rs.total??0} routes`)}
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px">
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
        ${secHeader('Dependency Graph', `${depNodes.length} nodes · ${depEdges.length} edges`, '#6366f1')}
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden">
            <div style="overflow-x:auto;padding:20px">${depSvg}</div>
        </div>
    </section>` : ''}

    <!-- Models -->
    <section>
        ${secHeader('Models', `${(d.models??[]).length} Eloquent models detected`, '#8b5cf6')}
        ${tableWrap(['Model','Table','Fillable Fields','Relationships'], modelRows || '<tr><td colspan="4" style="padding:12px;color:#94a3b8">No models.</td></tr>')}
    </section>

    <!-- Controllers -->
    <section>
        ${secHeader('Controllers', `${(d.controllers??[]).length} controllers`, '#3b82f6')}
        ${tableWrap(['Controller','Methods','Method List'], ctrlRows || '<tr><td colspan="3" style="padding:12px;color:#94a3b8">No controllers.</td></tr>')}
    </section>

</div>

<!-- ═══ FOOTER ═══════════════════════════════════════════════════════════ -->
<div style="background:#1e293b;color:#64748b;text-align:center;padding:24px;font-size:12px;font-family:system-ui,sans-serif;margin-top:20px">
    Generated by <strong style="color:#94a3b8">laravel-architecture-discovery</strong> · ${esc(d.generated_at ?? '')}
</div>

</body>
</html>`;
}

function _buildDepSvg(nodes, edges) {
    if (!nodes.length) return '';

    const layerOrder = ['controller','job','event','listener','service','repository','model'];
    const layerColors = {
        controller: { fill:'#dbeafe', stroke:'#3b82f6', text:'#1e3a8a' },
        service:    { fill:'#d1fae5', stroke:'#10b981', text:'#064e3b' },
        repository: { fill:'#fef3c7', stroke:'#f59e0b', text:'#78350f' },
        model:      { fill:'#ede9fe', stroke:'#8b5cf6', text:'#4c1d95' },
        job:        { fill:'#fef9c3', stroke:'#ca8a04', text:'#713f12' },
        event:      { fill:'#fdf4ff', stroke:'#a855f7', text:'#581c87' },
        listener:   { fill:'#fce7f3', stroke:'#ec4899', text:'#831843' },
    };

    const NW = 140, NH = 56, GAP_X = 18, GAP_Y = 90, PAD = 30;
    const byLayer = {};
    nodes.forEach(n => {
        const l = n.layer ?? 'model';
        (byLayer[l] = byLayer[l] || []).push(n);
    });

    const lKeys = layerOrder.filter(l => byLayer[l]?.length);
    const maxW  = Math.max(...lKeys.map(l => byLayer[l].length * NW + (byLayer[l].length - 1) * GAP_X));
    const CW    = Math.max(maxW + PAD * 2, 480);
    const CH    = lKeys.length * (NH + GAP_Y) - GAP_Y + PAD * 2;

    const nameToPos = {};
    lKeys.forEach((l, li) => {
        const row  = byLayer[l];
        const rowW = row.length * NW + (row.length - 1) * GAP_X;
        let x = (CW - rowW) / 2;
        const y = PAD + li * (NH + GAP_Y);
        row.forEach(n => {
            nameToPos[n.name] = { x, y, cx: x + NW / 2, cy: y + NH / 2 };
            x += NW + GAP_X;
        });
    });

    const edgesSvg = edges.slice(0, 120).map(e => {
        const f = nameToPos[e.from], t = nameToPos[e.to];
        if (!f || !t) return '';
        const x1 = f.cx, y1 = f.y + NH, x2 = t.cx, y2 = t.y;
        const cp = Math.abs(y2 - y1) * 0.4;
        return `<path d="M${x1},${y1} C${x1},${y1+cp} ${x2},${y2-cp} ${x2},${y2}"
            fill="none" stroke="#cbd5e1" stroke-width="1.5" marker-end="url(#dep-arr)"/>`;
    }).join('');

    const nodesSvg = nodes.map(n => {
        const p = nameToPos[n.name]; if (!p) return '';
        const c  = layerColors[n.layer ?? ''] ?? { fill:'#f1f5f9', stroke:'#94a3b8', text:'#1e293b' };
        const nm = n.name.length > 18 ? n.name.slice(0, 17) + '…' : n.name;
        const lb = (n.layer ?? '').toUpperCase();
        return `<g>
            <rect x="${p.x}" y="${p.y}" width="${NW}" height="${NH}" rx="10" fill="${c.fill}" stroke="${c.stroke}" stroke-width="1.5"/>
            <text x="${p.cx}" y="${p.y + 22}" text-anchor="middle" font-size="10" font-weight="700" fill="${c.stroke}" font-family="ui-monospace,monospace">${lb}</text>
            <text x="${p.cx}" y="${p.y + 40}" text-anchor="middle" font-size="11" font-weight="600" fill="${c.text}" font-family="ui-monospace,monospace">${nm}</text>
        </g>`;
    }).join('');

    return `<svg width="${CW}" height="${CH}" style="display:block;max-width:100%">
        <defs>
            <pattern id="dep-dot" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                <circle cx="1" cy="1" r="0.7" fill="#e2e8f0"/>
            </pattern>
            <marker id="dep-arr" markerWidth="8" markerHeight="6" refX="7" refY="3" orient="auto">
                <polygon points="0 0,8 3,0 6" fill="#94a3b8"/>
            </marker>
        </defs>
        <rect width="${CW}" height="${CH}" fill="#f8fafc"/>
        <rect width="${CW}" height="${CH}" fill="url(#dep-dot)"/>
        ${edgesSvg}
        ${nodesSvg}
    </svg>`;
}

// ── AI Insights ───────────────────────────────────────────────────────────────

const AI_ENDPOINT = '{{ route("architecture.ai.analyze") }}';
const AI_CSRF     = '{{ csrf_token() }}';

async function aiAnalyze() {
    document.getElementById('ai-loading').classList.remove('hidden');
    document.getElementById('ai-error').classList.add('hidden');
    document.getElementById('ai-results').classList.add('hidden');
    document.getElementById('ai-trigger').classList.add('opacity-50', 'pointer-events-none');

    try {
        const res = await fetch(AI_ENDPOINT, {
            method:  'POST',
            headers: {
                'Content-Type':  'application/json',
                'Accept':        'application/json',
                'X-CSRF-TOKEN':  AI_CSRF,
            },
        });

        const json = await res.json();

        if (!res.ok || json.error) {
            throw new Error(json.error || 'Server returned ' + res.status);
        }

        aiRenderResults(json);
    } catch (err) {
        document.getElementById('ai-error-msg').textContent = err.message;
        document.getElementById('ai-error').classList.remove('hidden');
    } finally {
        document.getElementById('ai-loading').classList.add('hidden');
        document.getElementById('ai-trigger').classList.remove('opacity-50', 'pointer-events-none');
    }
}

function aiRenderResults(data) {
    // Summary
    document.getElementById('ai-summary').textContent = data.summary || 'No summary available.';

    // Score
    const score = data.score || 0;
    document.getElementById('ai-score-num').textContent = score;
    setTimeout(() => {
        document.getElementById('ai-score-bar').style.width = score + '%';
    }, 50);

    // SOLID
    const solidEl = document.getElementById('ai-solid');
    solidEl.innerHTML = '';
    const solidNames = { S: 'Single Resp.', O: 'Open/Closed', L: 'Liskov Sub.', I: 'Interface Seg.', D: 'Dep. Inversion' };
    Object.entries(data.solid_review || {}).forEach(([key, val]) => {
        const color = val.status === 'pass' ? 'green' : val.status === 'warn' ? 'amber' : 'red';
        const icon  = val.status === 'pass' ? '✔' : val.status === 'warn' ? '⚠' : '✘';
        solidEl.insertAdjacentHTML('beforeend', `
            <div class="flex flex-col items-center text-center p-3 rounded-xl bg-${color}-50 border border-${color}-200">
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
    (data.problems || []).forEach(p => {
        const sev   = p.severity || 'info';
        const color = sev === 'error' ? 'red' : sev === 'warning' ? 'amber' : 'blue';
        problemsEl.insertAdjacentHTML('beforeend', `
            <div class="flex gap-3 p-3 rounded-lg bg-${color}-50 border border-${color}-100">
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
    (data.suggestions || []).forEach(s => {
        const pri   = s.priority || 'medium';
        const color = pri === 'high' ? 'red' : pri === 'medium' ? 'amber' : 'slate';
        suggEl.insertAdjacentHTML('beforeend', `
            <div class="flex gap-3 p-3 rounded-lg border border-slate-100 bg-slate-50">
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

    document.getElementById('ai-results').classList.remove('hidden');
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
</script>
</body>
</html>
