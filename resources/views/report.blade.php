<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Architecture Report — {{ $data['project']['name'] }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
    <style>
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }

        /* Section show/hide */
        .section-panel { display: none; }
        .section-panel.active { display: block; }

        /* Active nav item */
        .nav-item.nav-active { background: rgba(51,65,85,0.75); color: #fff; }

        /* Diagram SVG node state transitions */
        .g-node { transition: opacity 0.15s ease; }

        /* Sidebar slide transition */
        #sidebar {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #sidebar.sidebar-hidden {
            transform: translateX(-100%);
        }
        #main-content {
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #main-content.sidebar-hidden {
            margin-left: 0;
        }
    </style>
</head>
<body class="bg-slate-100 font-sans antialiased overflow-x-hidden">

<div class="min-h-screen">

    {{-- ============================================================ --}}
    {{-- SIDEBAR                                                       --}}
    {{-- ============================================================ --}}
    <aside id="sidebar" class="w-64 bg-slate-900 text-white fixed inset-y-0 left-0 flex flex-col overflow-y-auto z-20">

        {{-- Brand --}}
        <div class="px-6 py-5 border-b border-slate-700/60">
            <p class="text-xs text-slate-500 uppercase tracking-widest mb-1">Laravel</p>
            <h1 class="text-base font-bold leading-tight">Architecture Report</h1>
            <span class="inline-block mt-1 text-xs bg-slate-700 text-slate-300 px-2 py-0.5 rounded">v{{ $data['package_version'] }}</span>
        </div>

        {{-- Project --}}
        <div class="px-6 py-4 bg-slate-800/50 border-b border-slate-700/60">
            <p class="text-xs text-slate-500 mb-0.5">Project</p>
            <p class="text-sm font-semibold text-white">{{ $data['project']['name'] }}</p>
            <p class="text-xs text-slate-500 mt-1 truncate font-mono">{{ $data['project']['base_path'] }}</p>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-5 space-y-1">
            <p class="text-xs text-slate-600 uppercase tracking-widest px-3 mb-3">Sections</p>

            <button onclick="showSection('overview')" data-nav="overview"
                class="nav-item nav-active w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:bg-slate-700/60 hover:text-white transition-all text-sm">
                <span class="flex items-center gap-2.5">
                    <span class="w-6 text-center">📊</span> Overview
                </span>
            </button>

            <button onclick="showSection('diagram')" data-nav="diagram"
                class="nav-item w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:bg-slate-700/60 hover:text-white transition-all text-sm">
                <span class="flex items-center gap-2.5">
                    <span class="w-6 text-center">🔷</span> Diagram
                </span>
                <span class="bg-indigo-600/80 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $data['summary']['models'] }}</span>
            </button>

            <button onclick="showSection('dependencies')" data-nav="dependencies"
                class="nav-item w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:bg-slate-700/60 hover:text-white transition-all text-sm">
                <span class="flex items-center gap-2.5">
                    <span class="w-6 text-center">🔗</span> Dependencies
                </span>
                <span class="bg-amber-600/80 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ count($data['dependencies']['edges']) }}</span>
            </button>

            <button onclick="showSection('models')" data-nav="models"
                class="nav-item w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:bg-slate-700/60 hover:text-white transition-all text-sm">
                <span class="flex items-center gap-2.5">
                    <span class="w-6 text-center">🗃️</span> Models
                </span>
                <span class="bg-blue-600/80 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $data['summary']['models'] }}</span>
            </button>

            <button onclick="showSection('controllers')" data-nav="controllers"
                class="nav-item w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:bg-slate-700/60 hover:text-white transition-all text-sm">
                <span class="flex items-center gap-2.5">
                    <span class="w-6 text-center">⚙️</span> Controllers
                </span>
                <span class="bg-emerald-600/80 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $data['summary']['controllers'] }}</span>
            </button>

            <button onclick="showSection('routes')" data-nav="routes"
                class="nav-item w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:bg-slate-700/60 hover:text-white transition-all text-sm">
                <span class="flex items-center gap-2.5">
                    <span class="w-6 text-center">🛣️</span> Routes
                </span>
                <span class="bg-violet-600/80 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $data['summary']['routes'] }}</span>
            </button>

            @if(!empty($data['errors']))
            <button onclick="showSection('errors')" data-nav="errors"
                class="nav-item w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-red-300 hover:bg-red-900/40 hover:text-red-200 transition-all text-sm">
                <span class="flex items-center gap-2.5">
                    <span class="w-6 text-center">⚠️</span> Errors
                </span>
                <span class="bg-red-600/80 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ count($data['errors']) }}</span>
            </button>
            @endif
        </nav>

        {{-- System Info --}}
        <div class="px-6 py-4 border-t border-slate-700/60 space-y-2 text-xs">
            <p class="text-slate-500 uppercase tracking-widest text-xs mb-2">System</p>
            <div class="flex justify-between text-slate-400">
                <span>Laravel</span> <span class="text-slate-200">{{ $data['laravel_version'] }}</span>
            </div>
            <div class="flex justify-between text-slate-400">
                <span>PHP</span> <span class="text-slate-200">{{ $data['php_version'] }}</span>
            </div>
            <div class="flex justify-between text-slate-400">
                <span>Scan time</span> <span class="text-slate-200">{{ $data['performance']['execution_time_ms'] }}ms</span>
            </div>
            <div class="flex justify-between text-slate-400">
                <span>Memory</span> <span class="text-slate-200">{{ $data['performance']['memory_usage_mb'] }}MB</span>
            </div>
        </div>

    </aside>

    {{-- ============================================================ --}}
    {{-- MAIN CONTENT                                                  --}}
    {{-- ============================================================ --}}
    <main id="main-content" class="ml-64 min-h-screen flex flex-col">

        {{-- Top bar --}}
        <header class="sticky top-0 z-10 bg-white/90 backdrop-blur border-b border-gray-200 px-4 py-3 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                {{-- Sidebar toggle button --}}
                <button onclick="toggleSidebar()" id="sidebar-toggle"
                    class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition-colors focus:outline-none"
                    title="Toggle sidebar">
                    <svg id="icon-menu" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg id="icon-close" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-bold text-gray-800 leading-tight">{{ $data['project']['name'] }}</h2>
                        <span id="section-label" class="text-xs bg-slate-100 text-slate-500 px-2.5 py-0.5 rounded-full font-semibold">Overview</span>
                    </div>
                    <p class="text-xs text-gray-400">Generated on {{ \Carbon\Carbon::parse($data['generated_at'])->format('d M Y \a\t H:i') }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <span class="text-xs bg-slate-100 text-slate-600 px-3 py-1 rounded-full">Laravel {{ $data['laravel_version'] }}</span>
                <span class="text-xs bg-slate-100 text-slate-600 px-3 py-1 rounded-full">PHP {{ $data['php_version'] }}</span>
            </div>
        </header>

        <div class="flex-1 px-8 py-8 space-y-14">

            {{-- ====================================================== --}}
            {{-- OVERVIEW                                                --}}
            {{-- ====================================================== --}}
            <section id="overview" class="section-panel active">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-5">Overview</p>

                {{-- ── Architecture Score Card ──────────────────────────── --}}
                @if(!empty($data['score']))
                @php
                    $sc        = $data['score'];
                    $ringPct   = $sc['score'] / $sc['max'];
                    $circumf   = 251.2;
                    $offset    = round($circumf * (1 - $ringPct), 2);
                    $ringColor = match($sc['color']) {
                        'emerald' => '#10b981', 'blue' => '#3b82f6',
                        'amber'   => '#f59e0b', default => '#ef4444',
                    };
                    $gradeBg   = match($sc['color']) {
                        'emerald' => 'bg-emerald-50 border-emerald-200',
                        'blue'    => 'bg-blue-50 border-blue-200',
                        'amber'   => 'bg-amber-50 border-amber-200',
                        default   => 'bg-red-50 border-red-200',
                    };
                    $gradeText = match($sc['color']) {
                        'emerald' => 'text-emerald-700', 'blue' => 'text-blue-700',
                        'amber'   => 'text-amber-700',   default => 'text-red-700',
                    };
                @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="flex flex-col lg:flex-row gap-6">

                        {{-- Ring + grade --}}
                        <div class="flex items-center gap-5 shrink-0">
                            <div class="relative w-24 h-24">
                                <svg viewBox="0 0 100 100" class="-rotate-90 w-24 h-24">
                                    <circle cx="50" cy="50" r="40" fill="none" stroke="#e2e8f0" stroke-width="10"/>
                                    <circle cx="50" cy="50" r="40" fill="none" stroke="{{ $ringColor }}"
                                        stroke-width="10" stroke-linecap="round"
                                        stroke-dasharray="{{ $circumf }}"
                                        stroke-dashoffset="{{ $offset }}"/>
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-2xl font-black text-gray-800 leading-none">{{ $sc['score'] }}</span>
                                    <span class="text-xs text-gray-400">/ {{ $sc['max'] }}</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Architecture Score</p>
                                <span class="inline-block text-lg font-black {{ $gradeText }} {{ $gradeBg }} border px-3 py-0.5 rounded-xl">
                                    {{ $sc['grade'] }}
                                </span>
                            </div>
                        </div>

                        {{-- Checks grid --}}
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-2">
                            @foreach($sc['checks'] as $check)
                            @php
                                [$icon, $cls] = match($check['status']) {
                                    'pass'  => ['✔', 'text-emerald-600'],
                                    'warn'  => ['⚠', 'text-amber-500'],
                                    default => ['✘', 'text-red-500'],
                                };
                            @endphp
                            <div class="flex items-start gap-2">
                                <span class="mt-0.5 text-sm font-bold {{ $cls }} shrink-0">{{ $icon }}</span>
                                <div class="min-w-0">
                                    <span class="text-sm text-gray-700">{{ $check['label'] }}</span>
                                    @if($check['note'])
                                    <span class="text-xs text-gray-400 ml-1">{{ $check['note'] }}</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>

                    </div>
                </div>
                @endif

                {{-- ── 4 Stat Cards ─────────────────────────────────────── --}}
                @php
                    $rs          = $data['route_summary'];
                    $namedPct    = $rs['total'] > 0 ? round(($rs['named_count'] / $rs['total']) * 100) : 0;
                    $apiVersions = $rs['api_versions'] ?? [];
                @endphp
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

                    <div class="bg-white rounded-2xl p-5 shadow-sm border-l-4 border-blue-500">
                        <p class="text-xs text-gray-400 mb-1">Models</p>
                        <p class="text-3xl font-black text-gray-800">{{ $data['summary']['models'] }}</p>
                        <p class="text-xs text-blue-500 mt-1">{{ array_sum($data['summary']['relationship_summary'] ?? []) }} relationships</p>
                    </div>

                    <div class="bg-white rounded-2xl p-5 shadow-sm border-l-4 border-emerald-500">
                        <p class="text-xs text-gray-400 mb-1">Controllers</p>
                        <p class="text-3xl font-black text-gray-800">{{ $data['summary']['controllers'] }}</p>
                        <p class="text-xs text-emerald-500 mt-1">{{ count($data['dependencies']['edges']) }} dep edges</p>
                    </div>

                    <div class="bg-white rounded-2xl p-5 shadow-sm border-l-4 border-violet-500">
                        <p class="text-xs text-gray-400 mb-1">Routes</p>
                        <p class="text-3xl font-black text-gray-800">{{ $rs['total'] }}</p>
                        <p class="text-xs text-violet-500 mt-1">{{ $rs['web'] }} web · {{ $rs['api'] }} api</p>
                    </div>

                    <div class="bg-white rounded-2xl p-5 shadow-sm border-l-4 border-indigo-500">
                        <p class="text-xs text-gray-400 mb-1">Named Routes</p>
                        <p class="text-3xl font-black text-gray-800">{{ $namedPct }}<span class="text-lg font-bold text-gray-400">%</span></p>
                        <p class="text-xs text-indigo-500 mt-1">
                            {{ $rs['named_count'] }}/{{ $rs['total'] }} named
                            @if(!empty($apiVersions))
                                · {{ implode(', ', array_keys($apiVersions)) }}
                            @endif
                        </p>
                    </div>

                </div>

                {{-- ── 3 Charts ──────────────────────────────────────────── --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                    {{-- Routes by HTTP method --}}
                    @if(!empty($rs['by_method']))
                    <div class="bg-white rounded-2xl p-5 shadow-sm">
                        <p class="text-sm font-bold text-gray-700 mb-4">By HTTP Method</p>
                        <div class="space-y-3">
                            @php
                                $methodColors = ['get'=>['bar'=>'bg-blue-500','text'=>'text-blue-600'],
                                                 'post'=>['bar'=>'bg-emerald-500','text'=>'text-emerald-600'],
                                                 'put'=>['bar'=>'bg-yellow-500','text'=>'text-yellow-600'],
                                                 'patch'=>['bar'=>'bg-orange-500','text'=>'text-orange-600'],
                                                 'delete'=>['bar'=>'bg-red-500','text'=>'text-red-600']];
                            @endphp
                            @foreach($rs['by_method'] as $method => $count)
                            @php
                                $c   = $methodColors[$method] ?? ['bar'=>'bg-gray-400','text'=>'text-gray-500'];
                                $pct = $rs['total'] > 0 ? round(($count / $rs['total']) * 100) : 0;
                            @endphp
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-xs font-bold uppercase {{ $c['text'] }}">{{ $method }}</span>
                                    <span class="text-xs text-gray-500">{{ $count }} / {{ $pct }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5">
                                    <div class="{{ $c['bar'] }} h-1.5 rounded-full" style="width:{{ $pct }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Middleware Usage --}}
                    @if(!empty($rs['middleware_usage']))
                    <div class="bg-white rounded-2xl p-5 shadow-sm">
                        <p class="text-sm font-bold text-gray-700 mb-4">Middleware Usage</p>
                        <div class="space-y-3">
                            @php
                                $topMw    = array_slice($rs['middleware_usage'], 0, 6, true);
                                $maxMwCnt = max(array_values($topMw));
                            @endphp
                            @foreach($topMw as $mw => $count)
                            @php $pct = $maxMwCnt > 0 ? round(($count / $maxMwCnt) * 100) : 0; @endphp
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-xs font-semibold text-slate-700">{{ $mw }}</span>
                                    <span class="text-xs text-gray-500">{{ $count }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5">
                                    <div class="bg-slate-500 h-1.5 rounded-full" style="width:{{ $pct }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Relationship Types --}}
                    @if(!empty($data['summary']['relationship_summary']))
                    <div class="bg-white rounded-2xl p-5 shadow-sm">
                        <p class="text-sm font-bold text-gray-700 mb-4">Relationship Types</p>
                        <div class="space-y-3">
                            @php $totalRels = array_sum($data['summary']['relationship_summary']); @endphp
                            @foreach($data['summary']['relationship_summary'] as $type => $count)
                            @php $pct = $totalRels > 0 ? round(($count / $totalRels) * 100) : 0; @endphp
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-xs font-semibold text-amber-700">{{ $type }}</span>
                                    <span class="text-xs text-gray-500">{{ $count }} / {{ $pct }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5">
                                    <div class="bg-amber-400 h-1.5 rounded-full" style="width:{{ $pct }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </section>

            {{-- ====================================================== --}}
            {{-- DIAGRAM                                                 --}}
            {{-- ====================================================== --}}
            <section id="diagram" class="section-panel">

                @php
                    $graphNodes = [];
                    $graphEdges = [];
                    $erPairs    = [];

                    foreach ($data['models'] as $model) {
                        $graphNodes[] = [
                            'id'    => $model['name'],
                            'table' => $model['table'],
                            'rels'  => count($model['relationships'] ?? []),
                            'fields'=> count($model['fillable'] ?? []),
                        ];
                        foreach ($model['relationships'] ?? [] as $rel) {
                            $to = class_basename($rel['related'] ?? '');
                            if (!$to || $to === $model['name']) continue;

                            $graphEdges[] = ['from' => $model['name'], 'to' => $to, 'type' => $rel['type']];

                            $pairKey = $model['name'] . ':' . $to;
                            $revKey  = $to . ':' . $model['name'];
                            if (!isset($erPairs[$pairKey]) && !isset($erPairs[$revKey])) {
                                $type = $rel['type'];
                                if (str_contains($type, 'BelongsToMany') || str_contains($type, 'MorphToMany')) {
                                    $lhs = '}o'; $rhs = 'o{';
                                } elseif (str_contains($type, 'BelongsTo') || str_contains($type, 'MorphTo')) {
                                    $lhs = '}o'; $rhs = '||';
                                } elseif (str_contains($type, 'HasOne') || str_contains($type, 'MorphOne') || str_contains($type, 'HasOneThrough')) {
                                    $lhs = '||'; $rhs = 'o|';
                                } else {
                                    $lhs = '||'; $rhs = 'o{';
                                }
                                $erPairs[$pairKey] = "    {$model['name']} {$lhs}--{$rhs} {$to} : \"{$rel['method']}\"";
                            }
                        }
                    }

                    // Collect every model name that already appears in a relationship line
                    $erMentioned = [];
                    foreach (array_keys($erPairs) as $pk) {
                        [$a, $b] = explode(':', $pk);
                        $erMentioned[$a] = true;
                        $erMentioned[$b] = true;
                    }

                    // Declare standalone entity blocks for models with no relationships
                    $erStandalone = [];
                    foreach ($data['models'] as $model) {
                        if (!isset($erMentioned[$model['name']])) {
                            $erStandalone[] = "    {$model['name']} {";
                            $erStandalone[] = "        string table \"{$model['table']}\"";
                            $erStandalone[] = "    }";
                        }
                    }

                    $erCode     = "erDiagram\n"
                                . implode("\n", $erStandalone)
                                . (!empty($erStandalone) ? "\n" : '')
                                . implode("\n", $erPairs);
                    $modelCount = count($graphNodes);
                @endphp

                {{-- ── Tab bar ────────────────────────────────────────────────────────────────── --}}
                <div class="flex items-center gap-2 mb-5">
                    <button id="diag-tab-er" onclick="switchDiagTab('er')"
                        class="diag-tab text-xs font-bold px-4 py-1.5 rounded-xl border transition-all">
                        🔗 ER Diagram
                    </button>
                    <button id="diag-tab-map" onclick="switchDiagTab('map')"
                        class="diag-tab text-xs font-bold px-4 py-1.5 rounded-xl border transition-all">
                        🫧 Relation Graph
                    </button>
                    <div class="ml-auto flex items-center gap-2 text-xs">
                        <span class="bg-indigo-50 text-indigo-600 px-2.5 py-1 rounded-full font-semibold">{{ $modelCount }} models</span>
                        <span class="bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full font-semibold">{{ count($graphEdges) }} relationships</span>
                    </div>
                </div>

                {{-- ── ER Diagram panel ──────────────────────────────────────────── --}}
                <div id="diag-panel-er" class="diag-panel overflow-auto rounded-2xl border border-gray-100 bg-white" style="min-height:420px;padding:2rem;">
                    @if(count($erPairs) > 0)
                        @if($modelCount > 18)
                        <div class="mb-4 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-xs text-amber-700">
                            ⚠ Large project ({{ $modelCount }} models) — the ER diagram may be dense. Switch to <strong>Dependency Map</strong> for interactive exploration.
                        </div>
                        @endif
                        <div class="mermaid">{{ $erCode }}</div>
                    @else
                        <div class="flex flex-col items-center justify-center py-20 text-gray-300">
                            <div class="text-5xl mb-3">🔗</div>
                            <p class="text-sm font-medium text-gray-400">No model relationships detected</p>
                            <p class="text-xs mt-1 text-gray-300">Define Eloquent relationships to see them here.</p>
                        </div>
                    @endif
                </div>

                {{-- ── Dependency Map panel ────────────────────────────────────────── --}}
                <div id="diag-panel-map" class="diag-panel hidden">

                    {{-- Controls row --}}
                    <div class="flex items-center gap-2 mb-3 min-h-[36px]">
                        <input id="diag-search" type="text" placeholder="Search model…" oninput="diagSearch(this.value)"
                            class="text-xs bg-white border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300 transition w-48">
                        <button id="diag-clear-btn" onclick="diagClear()"
                            class="hidden text-xs text-gray-400 hover:text-gray-700 px-3 py-1.5 rounded-xl border border-gray-200 hover:border-gray-300 transition-colors">
                            ✕ Clear
                        </button>

                        {{-- Legend — hidden when a node is selected --}}
                        <div id="diag-legend" class="ml-auto flex items-center gap-3 text-xs text-gray-400">
                            <span class="flex items-center gap-1.5"><span class="inline-block w-6 h-px bg-indigo-400"></span>hasMany</span>
                            <span class="flex items-center gap-1.5"><span class="inline-block w-6 h-px bg-teal-400"></span>hasOne</span>
                            <span class="flex items-center gap-1.5"><span class="inline-block w-6 h-px bg-emerald-400"></span>belongsTo</span>
                            <span class="flex items-center gap-1.5"><span class="inline-block w-6 h-px bg-violet-400"></span>M:M</span>
                        </div>

                        {{-- Selected model info — shown in-row when a node is selected --}}
                        <div id="diag-info" class="hidden ml-auto flex items-center gap-2">
                            <span id="diag-info-name"  class="font-black text-indigo-900 text-xs"></span>
                            <span id="diag-info-table" class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-lg font-mono"></span>
                            <button id="diag-rels-btn" onclick="toggleDiagRels()"
                                class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-xl border bg-white border-indigo-200 text-indigo-600 hover:bg-indigo-50 transition-colors shadow-sm">
                                <span id="diag-info-count"></span>
                                <svg id="diag-rels-chevron" class="w-3 h-3 transition-transform" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 4l4 4 4-4"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Relationship cards panel — expands below controls when button is clicked --}}
                    <div id="diag-rels-panel" class="hidden mb-3 bg-white border border-indigo-100 rounded-2xl p-4 shadow-sm">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3" id="diag-rels-title"></p>
                        <div id="diag-info-rels" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2"></div>
                    </div>

                    {{-- SVG canvas --}}
                    <div class="relative rounded-2xl border border-gray-200 shadow-sm overflow-hidden" style="background:#f8fafc;">
                        <svg id="diag-canvas" xmlns="http://www.w3.org/2000/svg"
                             style="width:100%;height:580px;display:block;cursor:grab;user-select:none;">
                            <defs>
                                <pattern id="dot-grid" width="24" height="24" patternUnits="userSpaceOnUse">
                                    <circle cx="1" cy="1" r="1" fill="#cbd5e1" opacity="0.5"/>
                                </pattern>
                                <marker id="g-arr-many"      viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#818cf8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></marker>
                                <marker id="g-arr-one"       viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#2dd4bf" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></marker>
                                <marker id="g-arr-belongs"   viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#34d399" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></marker>
                                <marker id="g-arr-mm"        viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#c084fc" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></marker>
                                <marker id="g-arr-many-a"    viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></marker>
                                <marker id="g-arr-one-a"     viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#14b8a6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></marker>
                                <marker id="g-arr-belongs-a" viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></marker>
                                <marker id="g-arr-mm-a"      viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#a855f7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></marker>
                                <filter id="f-node"     x="-20%" y="-30%" width="140%" height="160%"><feDropShadow dx="0" dy="2" stdDeviation="4"  flood-color="rgba(15,23,42,0.10)"/></filter>
                                <filter id="f-node-sel" x="-20%" y="-30%" width="140%" height="160%"><feDropShadow dx="0" dy="4" stdDeviation="10" flood-color="rgba(99,102,241,0.30)"/></filter>
                                <filter id="f-node-rel" x="-20%" y="-30%" width="140%" height="160%"><feDropShadow dx="0" dy="3" stdDeviation="7"  flood-color="rgba(16,185,129,0.25)"/></filter>
                            </defs>
                            <rect width="100%" height="100%" fill="url(#dot-grid)"/>
                            <g id="diag-vp">
                                <g id="diag-edges-g"></g>
                                <g id="diag-nodes-g"></g>
                            </g>
                        </svg>

                        {{-- Zoom controls (top-right) --}}
                        <div class="absolute top-3 right-3 flex items-center gap-1">
                            <button onclick="diagZoom(1.25)"  class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 shadow-sm font-bold transition-colors text-base leading-none">+</button>
                            <button onclick="diagZoom(0.8)"   class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 shadow-sm font-bold transition-colors text-base leading-none">−</button>
                            <button onclick="diagFitView()"   class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 shadow-sm transition-colors text-sm" title="Fit to screen">⊡</button>
                            <button onclick="diagResetView()" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 shadow-sm transition-colors text-sm" title="Reset view">⟳</button>
                        </div>

                        {{-- Minimap (bottom-right) --}}
                        <div class="absolute bottom-3 right-3 rounded-xl border border-gray-200 bg-white/90 backdrop-blur shadow-md overflow-hidden" style="width:160px;height:100px;">
                            <svg id="diag-mm" width="160" height="100" style="display:block;"></svg>
                        </div>

                        {{-- Hint (bottom-left) --}}
                        <div class="absolute bottom-3 left-3 text-xs text-slate-400 bg-white/80 backdrop-blur px-2.5 py-1 rounded-lg border border-gray-100 shadow-sm pointer-events-none">
                            Click node · Drag to pan · Scroll to zoom
                        </div>
                    </div>

                </div>

            </section>

            {{-- ====================================================== --}}
            {{-- DEPENDENCIES                                            --}}
            {{-- ====================================================== --}}
            <section id="dependencies" class="section-panel">
                <div class="flex items-center gap-3 mb-5">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Dependency Graph</p>
                    <span class="bg-amber-100 text-amber-700 text-xs font-bold px-2 py-0.5 rounded-full">Layer Architecture</span>
                </div>

                @php
                    $depNodes = $data['dependencies']['nodes'] ?? [];
                    $depEdges = $data['dependencies']['edges'] ?? [];

                    $layerOrder  = ['controller','job','event','listener','service','repository','model','database'];
                    $layerLabels = [
                        'controller' => 'Controllers', 'job' => 'Jobs', 'event' => 'Events',
                        'listener'   => 'Listeners', 'service' => 'Services',
                        'repository' => 'Repositories', 'model' => 'Models', 'database' => 'Database',
                    ];
                    $byLayer = array_fill_keys($layerOrder, []);
                    foreach ($depNodes as $node) {
                        $l = $node['layer'] ?? 'model';
                        if (isset($byLayer[$l])) $byLayer[$l][] = $node['name'];
                    }

                    $edgeLabel = ['injects' => '', 'uses' => 'uses', 'triggers' => 'triggers', 'persists' => 'persists'];

                    $flowLines = ['flowchart TD'];
                    foreach ($layerOrder as $layer) {
                        if (empty($byLayer[$layer])) continue;
                        if ($layer === 'database') continue; // cylinder defined inline
                        $flowLines[] = "    subgraph {$layerLabels[$layer]}";
                        foreach ($byLayer[$layer] as $name) { $flowLines[] = "        {$name}"; }
                        $flowLines[] = "    end";
                    }
                    if (!empty($byLayer['database'])) {
                        $flowLines[] = '    Database[("Database")]';
                    }
                    foreach ($depEdges as $edge) {
                        $lbl   = $edgeLabel[$edge['type'] ?? ''] ?? '';
                        $arrow = $lbl ? "-->|\"{$lbl}\"|" : '-->';
                        $flowLines[] = "    {$edge['from']} {$arrow} {$edge['to']}";
                    }
                    foreach ($depNodes as $node) {
                        $flowLines[] = "    class {$node['name']} {$node['layer']}";
                    }
                    $flowLines[] = '    classDef controller fill:#dbeafe,stroke:#3b82f6,color:#1e3a8a';
                    $flowLines[] = '    classDef service    fill:#d1fae5,stroke:#10b981,color:#064e3b';
                    $flowLines[] = '    classDef repository fill:#fef3c7,stroke:#f59e0b,color:#78350f';
                    $flowLines[] = '    classDef model      fill:#ede9fe,stroke:#8b5cf6,color:#4c1d95';
                    $flowLines[] = '    classDef job        fill:#fef9c3,stroke:#ca8a04,color:#713f12';
                    $flowLines[] = '    classDef event      fill:#fdf4ff,stroke:#a855f7,color:#581c87';
                    $flowLines[] = '    classDef listener   fill:#fce7f3,stroke:#ec4899,color:#831843';
                    $flowLines[] = '    classDef database   fill:#f1f5f9,stroke:#64748b,color:#1e293b';
                    $depCode = implode("\n", $flowLines);

                    $layerCounts = [];
                    foreach ($depNodes as $node) { $layerCounts[$node['layer']] = ($layerCounts[$node['layer']] ?? 0) + 1; }
                @endphp

                @if(empty($depEdges))
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
                    <p class="text-gray-400 text-sm">No dependencies detected.</p>
                    <p class="text-gray-300 text-xs mt-1">Add classes ending in <code>Service</code> or <code>Repository</code> with constructor injection.</p>
                </div>
                @else
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                    {{-- Toolbar --}}
                    <div class="flex items-center justify-between px-6 py-3 border-b border-gray-100 bg-gray-50/80">
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
                            @php
                            $dotColors = [
                                'controller'=>'bg-blue-500','service'=>'bg-emerald-500',
                                'repository'=>'bg-amber-500','model'=>'bg-violet-500',
                                'job'=>'bg-yellow-500','event'=>'bg-purple-500',
                                'listener'=>'bg-pink-500','database'=>'bg-slate-500',
                            ];
                            @endphp
                            @foreach($layerOrder as $layer)
                            @if(isset($layerCounts[$layer]) && $layer !== 'database')
                            <span class="flex items-center gap-1.5 text-xs text-gray-500">
                                <span class="w-2 h-2 rounded-full {{ $dotColors[$layer] ?? 'bg-gray-400' }}"></span>
                                <span class="font-semibold capitalize">{{ $layer }}</span>
                                <span class="text-gray-400">({{ $layerCounts[$layer] }})</span>
                            </span>
                            @endif
                            @endforeach
                            @if(isset($layerCounts['database']))
                            <span class="flex items-center gap-1.5 text-xs text-gray-500">
                                <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                                <span class="font-semibold">Database</span>
                            </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button onclick="copyDepCode()" id="dep-copy-btn"
                                class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-amber-600 px-3 py-1.5 rounded-lg hover:bg-amber-50 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                Copy Code
                            </button>
                            <button onclick="toggleDepCode()"
                                class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-amber-600 px-3 py-1.5 rounded-lg hover:bg-amber-50 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                </svg>
                                View Code
                            </button>
                            <button onclick="toggleDepFullscreen()"
                                class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-amber-600 px-3 py-1.5 rounded-lg hover:bg-amber-50 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                </svg>
                                Fullscreen
                            </button>
                        </div>
                    </div>

                    {{-- Mermaid canvas --}}
                    <div id="dep-canvas" class="p-6 overflow-auto bg-slate-50/40">
                        <pre class="mermaid">{{ $depCode }}</pre>
                    </div>

                    {{-- Raw code (hidden by default) --}}
                    <div id="dep-code-block" class="hidden border-t border-gray-100">
                        <pre id="dep-raw" class="text-xs text-amber-800 bg-amber-50 p-6 overflow-x-auto font-mono leading-6 whitespace-pre">{{ $depCode }}</pre>
                    </div>

                </div>
                @endif
            </section>

            {{-- ====================================================== --}}
            {{-- MODELS                                                  --}}
            {{-- ====================================================== --}}
            <section id="models" class="section-panel">
                <div class="flex items-center gap-3 mb-5">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Models</p>
                    <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $data['summary']['models'] }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($data['models'] as $model)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all overflow-hidden">

                        {{-- Model Header --}}
                        <div class="px-5 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-blue-50">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <h3 class="font-bold text-gray-800 text-base truncate">{{ $model['name'] }}</h3>
                                    <p class="text-xs text-gray-400 font-mono truncate mt-0.5">{{ $model['namespace'] }}</p>
                                </div>
                                <span class="shrink-0 text-xs bg-blue-100 text-blue-700 px-2.5 py-1 rounded-lg font-mono font-semibold">
                                    {{ $model['table'] }}
                                </span>
                            </div>
                        </div>

                        <div class="px-5 py-4 space-y-4">

                            {{-- Relationships --}}
                            @if(!empty($model['relationships']))
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Relationships</p>
                                <div class="space-y-1.5">
                                    @foreach($model['relationships'] as $rel)
                                    <div class="flex items-center gap-2 text-xs">
                                        <span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded font-semibold shrink-0">{{ $rel['type'] }}</span>
                                        <span class="text-gray-300">→</span>
                                        <span class="font-semibold text-gray-700">{{ $rel['related'] }}</span>
                                        <span class="text-gray-400 font-mono">({{ $rel['method'] }})</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- Fillable --}}
                            @if(!empty($model['fillable']))
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Fillable</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($model['fillable'] as $field)
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md font-mono">{{ $field }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- Hidden --}}
                            @if(!empty($model['hidden']))
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Hidden</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($model['hidden'] as $field)
                                    <span class="text-xs bg-red-50 text-red-400 border border-red-100 px-2 py-0.5 rounded-md font-mono">{{ $field }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if(empty($model['relationships']) && empty($model['fillable']) && empty($model['hidden']))
                            <p class="text-xs text-gray-300 italic text-center py-2">No details detected.</p>
                            @endif

                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ====================================================== --}}
            {{-- CONTROLLERS                                             --}}
            {{-- ====================================================== --}}
            <section id="controllers" class="section-panel">
                <div class="flex items-center gap-3 mb-5">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Controllers</p>
                    <span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $data['summary']['controllers'] }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($data['controllers'] as $controller)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all overflow-hidden">

                        {{-- Controller Header --}}
                        <div class="px-5 py-4 bg-gradient-to-r from-emerald-50 to-white border-b border-emerald-50">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <h3 class="font-bold text-gray-800 truncate">{{ $controller['name'] }}</h3>
                                    <p class="text-xs text-gray-400 font-mono truncate mt-0.5">{{ $controller['namespace'] }}</p>
                                </div>
                                <span class="shrink-0 text-xs bg-emerald-100 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-lg font-semibold">
                                    {{ $controller['method_count'] }} methods
                                </span>
                            </div>
                        </div>

                        {{-- Methods --}}
                        <div class="px-5 py-4">
                            @if(!empty($controller['methods']))
                            @php
                                $mc = ['index'=>'bg-blue-50 text-blue-700 border-blue-100',
                                       'show'=>'bg-cyan-50 text-cyan-700 border-cyan-100',
                                       'create'=>'bg-teal-50 text-teal-700 border-teal-100',
                                       'store'=>'bg-green-50 text-green-700 border-green-100',
                                       'edit'=>'bg-orange-50 text-orange-700 border-orange-100',
                                       'update'=>'bg-yellow-50 text-yellow-700 border-yellow-100',
                                       'destroy'=>'bg-red-50 text-red-700 border-red-100'];
                            @endphp
                            <div class="flex flex-wrap gap-2">
                                @foreach($controller['methods'] as $method)
                                @php $color = $mc[$method] ?? 'bg-gray-50 text-gray-600 border-gray-100'; @endphp
                                <span class="text-xs border px-2.5 py-1 rounded-lg font-mono font-medium {{ $color }}">{{ $method }}</span>
                                @endforeach
                            </div>
                            @else
                            <p class="text-xs text-gray-300 italic">No public methods detected.</p>
                            @endif
                        </div>

                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ====================================================== --}}
            {{-- ROUTES                                                  --}}
            {{-- ====================================================== --}}
            <section id="routes" class="section-panel">
                <div class="flex items-center gap-3 mb-5">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Routes</p>
                    <span class="bg-violet-100 text-violet-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $data['summary']['routes'] }}</span>
                    <span class="text-xs text-gray-400">{{ $data['route_summary']['web'] }} web · {{ $data['route_summary']['api'] }} api</span>
                </div>

                {{-- Filter + Search --}}
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <div class="flex flex-wrap gap-2">
                        <button onclick="filterRoutes('all', this)" class="route-filter text-xs px-4 py-1.5 rounded-full font-semibold border bg-slate-800 text-white border-slate-800">All</button>
                        @foreach(array_keys($data['route_summary']['by_method'] ?? []) as $m)
                        @php $bc=['get'=>'blue','post'=>'green','put'=>'yellow','patch'=>'orange','delete'=>'red'][$m]??'gray'; @endphp
                        <button onclick="filterRoutes('{{ strtoupper($m) }}', this)" class="route-filter text-xs px-4 py-1.5 rounded-full font-semibold border bg-{{ $bc }}-50 text-{{ $bc }}-700 border-{{ $bc }}-200 hover:bg-{{ $bc }}-100 uppercase transition">{{ $m }}</button>
                        @endforeach
                    </div>
                    <input id="route-search" type="text" onkeyup="searchRoutes()" placeholder="Search URI, controller, name…"
                           class="flex-1 min-w-48 text-xs bg-white border border-gray-200 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-violet-300 transition">
                </div>

                {{-- Table --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                    <table class="w-full text-xs" style="min-width:800px">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-gray-400 uppercase tracking-wider">
                                <th class="text-left px-5 py-3 w-20 whitespace-nowrap">Method</th>
                                <th class="text-left px-5 py-3 whitespace-nowrap">URI</th>
                                <th class="text-left px-5 py-3 whitespace-nowrap">Controller</th>
                                <th class="text-left px-5 py-3 whitespace-nowrap">Action</th>
                                <th class="text-left px-5 py-3 whitespace-nowrap">Name</th>
                                <th class="text-left px-5 py-3 whitespace-nowrap">Middleware</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50" id="routes-body">
                            @foreach($data['routes'] as $route)
                            @php
                                $httpMethods = array_values(array_filter($route['methods'], fn($m) => $m !== 'HEAD'));
                                $primary = $httpMethods[0] ?? 'GET';
                                $badge = ['GET'=>'bg-blue-100 text-blue-700','POST'=>'bg-green-100 text-green-700',
                                          'PUT'=>'bg-yellow-100 text-yellow-700','PATCH'=>'bg-orange-100 text-orange-700',
                                          'DELETE'=>'bg-red-100 text-red-700'][$primary] ?? 'bg-gray-100 text-gray-600';
                                $middlewares = $route['middleware'] ?? [];
                            @endphp
                            <tr class="route-row hover:bg-slate-50 transition" data-methods="{{ implode(',', $httpMethods) }}">
                                <td class="px-5 py-3">
                                    <span class="font-bold px-2 py-0.5 rounded text-xs {{ $badge }}">{{ $primary }}</span>
                                </td>
                                <td class="px-5 py-3 font-mono text-gray-800 break-all">{{ $route['uri'] }}</td>
                                <td class="px-5 py-3 text-gray-600 whitespace-nowrap">{{ class_basename($route['controller']['class']) }}</td>
                                <td class="px-5 py-3 text-gray-400 font-mono whitespace-nowrap">{{ $route['controller']['method'] ?? '—' }}</td>
                                <td class="px-5 py-3 text-gray-400 whitespace-nowrap">{{ $route['name'] ?? '—' }}</td>
                                <td class="px-5 py-3">
                                    @if(empty($middlewares))
                                        <span class="text-gray-300">—</span>
                                    @else
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($middlewares as $mw)
                                            <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-xs whitespace-nowrap">{{ $mw }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    <p id="no-results" class="hidden text-center text-gray-400 py-10 text-sm">No routes match your search.</p>
                </div>
            </section>

            {{-- ====================================================== --}}
            {{-- ERRORS                                                  --}}
            {{-- ====================================================== --}}
            @if(!empty($data['errors']))
            <section id="errors" class="section-panel">
                <div class="flex items-center gap-3 mb-5">
                    <p class="text-xs font-bold text-red-400 uppercase tracking-widest">Errors</p>
                    <span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ count($data['errors']) }}</span>
                </div>
                <div class="space-y-3">
                    @foreach($data['errors'] as $error)
                    <div class="flex gap-4 bg-red-50 border border-red-200 rounded-2xl px-5 py-4">
                        <span class="text-xl shrink-0">⚠️</span>
                        <div>
                            <p class="text-sm font-mono font-semibold text-red-700">{{ $error['file'] }}</p>
                            <p class="text-xs text-red-500 mt-1">{{ $error['message'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- Footer --}}
            <footer class="text-center text-xs text-gray-300 border-t border-gray-100 pt-6 pb-2">
                Generated by <strong class="text-gray-400">Laravel Architecture Discovery</strong> v{{ $data['package_version'] }}
                &nbsp;·&nbsp; {{ $data['performance']['execution_time_ms'] }}ms &nbsp;·&nbsp; {{ $data['performance']['memory_usage_mb'] }}MB
            </footer>

        </div>
    </main>
</div>

<script>
    /* ── Mermaid init ───────────────────────────────────────────── */
    mermaid.initialize({
        startOnLoad: false,
        theme: 'base',
        themeVariables: {
            primaryColor: '#eff6ff',
            primaryTextColor: '#1e3a8a',
            primaryBorderColor: '#60a5fa',
            lineColor: '#94a3b8',
            secondaryColor: '#f0fdf4',
            tertiaryColor: '#faf5ff',
            edgeLabelBackground: '#f8fafc',
            fontFamily: 'ui-sans-serif, system-ui, -apple-system, sans-serif',
            fontSize: '14px',
        },
        flowchart: { rankSpacing: 80, nodeSpacing: 40, curve: 'basis', padding: 20 },
        classDiagram: { diagramPadding: 30 },
        securityLevel: 'loose',
    });

    /* ── Section switching ──────────────────────────────────────── */
    const mermaidDone = new Set();
    const sectionNames = {
        overview: 'Overview', diagram: 'Model Diagram', dependencies: 'Dependencies',
        models: 'Models', controllers: 'Controllers', routes: 'Routes', errors: 'Errors',
    };

    function showSection(id) {
        // Hide all, show target
        document.querySelectorAll('.section-panel').forEach(s => s.classList.remove('active'));
        const target = document.getElementById(id);
        if (target) target.classList.add('active');

        // Update nav active highlight
        document.querySelectorAll('.nav-item').forEach(b => b.classList.remove('nav-active'));
        const navBtn = document.querySelector(`[data-nav="${id}"]`);
        if (navBtn) navBtn.classList.add('nav-active');

        // Update header breadcrumb chip
        const label = document.getElementById('section-label');
        if (label) label.textContent = sectionNames[id] || id;

        // Init diagram section on first visit — default to ER tab
        if (id === 'diagram' && !diagInited) { diagInited = true; switchDiagTab('er'); }

        // Lazy Mermaid render — only on first visit to dependencies
        if (target && !mermaidDone.has(id) && id === 'dependencies') {
            const nodes = Array.from(target.querySelectorAll('.mermaid'));
            if (nodes.length) mermaid.run({ nodes });
            mermaidDone.add(id);
        }
    }

    /* ── Diagram tabs + data ───────────────────────────────────── */
    const _gNodes = @json($graphNodes);
    const _gEdges = @json($graphEdges);
    let diagInited = false, _erDone = false, _mapDone = false;
    const NW = 150, NH = 60;
    let _diagNodes = [], _diagSel = null, _diagAdj = {};

    function switchDiagTab(tab) {
        document.querySelectorAll('.diag-panel').forEach(p => p.classList.add('hidden'));
        document.getElementById('diag-panel-' + tab).classList.remove('hidden');

        document.querySelectorAll('.diag-tab').forEach(t => {
            t.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600', 'shadow-sm');
            t.classList.add('bg-white', 'text-gray-500', 'border-gray-200');
        });
        const activeBtn = document.getElementById('diag-tab-' + tab);
        if (activeBtn) {
            activeBtn.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600', 'shadow-sm');
            activeBtn.classList.remove('bg-white', 'text-gray-500', 'border-gray-200');
        }

        if (tab === 'er' && !_erDone) {
            _erDone = true;
            const els = Array.from(document.querySelectorAll('#diag-panel-er .mermaid'));
            if (els.length) mermaid.run({ nodes: els });
        }
        if (tab === 'map' && !_mapDone) {
            _mapDone = true;
            setTimeout(initDiagram, 20);
        }
    }

    function edgeTheme(type) {
        if (type.includes('BelongsToMany') || type.includes('MorphToMany'))
            return { stroke:'#c084fc', marker:'url(#g-arr-mm)',      markerA:'url(#g-arr-mm-a)',      dash:'7,3' };
        if (type.includes('BelongsTo') || type.includes('MorphTo'))
            return { stroke:'#34d399', marker:'url(#g-arr-belongs)', markerA:'url(#g-arr-belongs-a)', dash:'none' };
        if (type.includes('Many'))
            return { stroke:'#818cf8', marker:'url(#g-arr-many)',    markerA:'url(#g-arr-many-a)',    dash:'none' };
        return     { stroke:'#2dd4bf', marker:'url(#g-arr-one)',     markerA:'url(#g-arr-one-a)',     dash:'5,3' };
    }

    function initDiagram() {
        const svg    = document.getElementById('diag-canvas');
        const W      = svg.clientWidth  || 900;
        const H      = svg.clientHeight || 580;
        const edgesG = document.getElementById('diag-edges-g');
        const nodesG = document.getElementById('diag-nodes-g');

        const nodes = _gNodes.map((n, i) => {
            const angle = (i / Math.max(_gNodes.length, 1)) * 2 * Math.PI - Math.PI / 2;
            const r     = Math.min(W, H) * 0.32;
            return { ...n, x: W/2 + r*Math.cos(angle), y: H/2 + r*Math.sin(angle), vx: 0, vy: 0 };
        });
        const nById = {};
        nodes.forEach(n => nById[n.id] = n);

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
            _gEdges.forEach(e => {
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
                n.x = Math.max(NW/2 + 20, Math.min(W - NW/2 - 20, n.x + n.vx));
                n.y = Math.max(NH/2 + 20, Math.min(H - NH/2 - 20, n.y + n.vy));
            });
        }
        _diagNodes = nodes;

        _gEdges.forEach(e => {
            const na = nById[e.from], nb = nById[e.to];
            if (!na || !nb) return;
            const th   = edgeTheme(e.type);
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('class', 'g-edge');
            path.setAttribute('data-from', e.from); path.setAttribute('data-to', e.to); path.setAttribute('data-type', e.type);
            path.setAttribute('fill', 'none'); path.setAttribute('stroke', th.stroke);
            path.setAttribute('stroke-width', '1.5'); path.setAttribute('stroke-opacity', '0.4');
            path.setAttribute('marker-end', th.marker);
            if (th.dash !== 'none') path.setAttribute('stroke-dasharray', th.dash);
            _setEdgePath(path, na, nb);
            edgesG.appendChild(path);
        });

        nodes.forEach(n => {
            const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            g.setAttribute('class', 'g-node'); g.setAttribute('data-id', n.id);
            g.style.cursor = 'pointer';
            g.setAttribute('transform', `translate(${n.x - NW/2},${n.y - NH/2})`);

            const bg = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
            bg.setAttribute('class', 'g-node-bg'); bg.setAttribute('width', NW); bg.setAttribute('height', NH);
            bg.setAttribute('rx', '10'); bg.setAttribute('fill', 'white');
            bg.setAttribute('stroke', '#e2e8f0'); bg.setAttribute('stroke-width', '1.5'); bg.setAttribute('filter', 'url(#f-node)');

            const bar = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
            bar.setAttribute('class', 'g-node-bar'); bar.setAttribute('width', NW); bar.setAttribute('height', '5');
            bar.setAttribute('rx', '5'); bar.setAttribute('fill', '#6366f1');

            const nm = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            nm.setAttribute('x', NW/2); nm.setAttribute('y', '26'); nm.setAttribute('text-anchor', 'middle');
            nm.setAttribute('font-family', 'ui-sans-serif,system-ui,sans-serif');
            nm.setAttribute('font-size', '13'); nm.setAttribute('font-weight', '800'); nm.setAttribute('fill', '#1e293b');
            nm.textContent = n.id.length > 17 ? n.id.slice(0, 16) + '…' : n.id;

            const tb = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            tb.setAttribute('x', NW/2); tb.setAttribute('y', '40'); tb.setAttribute('text-anchor', 'middle');
            tb.setAttribute('font-family', 'ui-monospace,monospace'); tb.setAttribute('font-size', '10'); tb.setAttribute('fill', '#94a3b8');
            tb.textContent = n.table.length > 20 ? n.table.slice(0, 19) + '…' : n.table;

            g.appendChild(bg); g.appendChild(bar); g.appendChild(nm); g.appendChild(tb);

            if (n.rels > 0) {
                const rb = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                rb.setAttribute('x', NW - 8); rb.setAttribute('y', '56'); rb.setAttribute('text-anchor', 'end');
                rb.setAttribute('font-size', '9'); rb.setAttribute('font-weight', '700'); rb.setAttribute('fill', '#a5b4fc');
                rb.textContent = n.rels + 'r';
                g.appendChild(rb);
            }
            g.addEventListener('click', ev => { ev.stopPropagation(); diagSelect(n.id); });
            nodesG.appendChild(g);
        });

        _diagAdj = {};
        nodes.forEach(n => _diagAdj[n.id] = new Set());
        _gEdges.forEach(e => {
            if (nById[e.from] && nById[e.to]) { _diagAdj[e.from].add(e.to); _diagAdj[e.to].add(e.from); }
        });

        let isPan = false, panOrigin = { x: 0, y: 0 }, vp = { x: 0, y: 0, z: 1 };
        const vpEl = document.getElementById('diag-vp');

        function applyVp() {
            vpEl.setAttribute('transform', `translate(${-vp.x * vp.z},${-vp.y * vp.z}) scale(${vp.z})`);
            updateMinimap(W, H, vp);
        }

        svg.addEventListener('mousedown', e => {
            if (!e.target.closest('.g-node')) { isPan = true; panOrigin = { x: e.clientX, y: e.clientY }; svg.style.cursor = 'grabbing'; }
        });
        window.addEventListener('mousemove', e => {
            if (!isPan) return;
            vp.x -= (e.clientX - panOrigin.x) / vp.z; vp.y -= (e.clientY - panOrigin.y) / vp.z;
            panOrigin = { x: e.clientX, y: e.clientY }; applyVp();
        });
        window.addEventListener('mouseup', () => { isPan = false; svg.style.cursor = 'grab'; });
        svg.addEventListener('wheel', e => {
            e.preventDefault();
            const rect   = svg.getBoundingClientRect();
            const mouseX = e.clientX - rect.left;
            const mouseY = e.clientY - rect.top;
            // Pin the data-space point under the cursor so it doesn't drift
            const dataX  = vp.x + mouseX / vp.z;
            const dataY  = vp.y + mouseY / vp.z;
            vp.z = Math.max(0.25, Math.min(4, vp.z * (e.deltaY > 0 ? 0.88 : 1.14)));
            vp.x = dataX - mouseX / vp.z;
            vp.y = dataY - mouseY / vp.z;
            applyVp();
        }, { passive: false });

        window.diagZoom = f => {
            // Zoom centered on the visible mid-point, not the origin
            const cx = vp.x + W / (2 * vp.z);
            const cy = vp.y + H / (2 * vp.z);
            vp.z = Math.max(0.25, Math.min(4, vp.z * f));
            vp.x = cx - W / (2 * vp.z);
            vp.y = cy - H / (2 * vp.z);
            applyVp();
        };
        window.diagResetView = () => { vp = { x: 0, y: 0, z: 1 }; applyVp(); };
        window.diagFitView = () => {
            if (!_diagNodes.length) return;
            const xs = _diagNodes.map(n => n.x), ys = _diagNodes.map(n => n.y);
            const minX = Math.min(...xs) - NW/2 - 20, maxX = Math.max(...xs) + NW/2 + 20;
            const minY = Math.min(...ys) - NH/2 - 20, maxY = Math.max(...ys) + NH/2 + 20;
            vp.z = Math.max(0.25, Math.min(4, Math.min(W / (maxX - minX), H / (maxY - minY))));
            vp.x = minX - (W/vp.z - (maxX - minX)) / 2;
            vp.y = minY - (H/vp.z - (maxY - minY)) / 2;
            applyVp();
        };
        svg.addEventListener('click', e => { if (e.target === svg) diagClear(); });

        initMinimap(nodes, W, H);
        applyVp();
    }

    function initMinimap(nodes, W, H) {
        const mm = document.getElementById('diag-mm');
        if (!mm) return;
        const mmW = 160, mmH = 100;
        const scale = Math.min(mmW / W, mmH / H) * 0.88;
        const offX  = (mmW - W * scale) / 2;
        const offY  = (mmH - H * scale) / 2;

        const bg = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        bg.setAttribute('width', mmW); bg.setAttribute('height', mmH); bg.setAttribute('fill', '#f8fafc');
        mm.appendChild(bg);

        nodes.forEach(n => {
            const dot = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
            dot.setAttribute('x', offX + (n.x - NW/2) * scale);
            dot.setAttribute('y', offY + (n.y - NH/2) * scale);
            dot.setAttribute('width',  Math.max(4, NW * scale));
            dot.setAttribute('height', Math.max(3, NH * scale));
            dot.setAttribute('rx', '2'); dot.setAttribute('fill', '#6366f1'); dot.setAttribute('opacity', '0.45');
            mm.appendChild(dot);
        });

        const vr = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        vr.setAttribute('id', 'diag-mm-vp'); vr.setAttribute('fill', 'rgba(99,102,241,0.08)');
        vr.setAttribute('stroke', '#6366f1'); vr.setAttribute('stroke-width', '1.5'); vr.setAttribute('rx', '2');
        mm.appendChild(vr);

        window._mmParams = { scale, offX, offY, mmW, mmH };
    }

    function updateMinimap(W, H, vp) {
        const vr = document.getElementById('diag-mm-vp');
        if (!vr || !window._mmParams) return;
        const { scale, offX, offY, mmW, mmH } = window._mmParams;
        const vpW = W / vp.z, vpH = H / vp.z;
        vr.setAttribute('x',      Math.max(0, offX + vp.x * scale));
        vr.setAttribute('y',      Math.max(0, offY + vp.y * scale));
        vr.setAttribute('width',  Math.min(mmW, vpW * scale));
        vr.setAttribute('height', Math.min(mmH, vpH * scale));
    }

    function _setEdgePath(path, na, nb) {
        const dx = nb.x - na.x, dy = nb.y - na.y;
        const d  = Math.sqrt(dx*dx + dy*dy) || 1, nx = dx/d, ny = dy/d;
        const x1 = na.x + nx*(NW/2), y1 = na.y + ny*(NH/2);
        const x2 = nb.x - nx*(NW/2 + 6), y2 = nb.y - ny*(NH/2 + 6);
        const mx = (x1+x2)/2 - ny*28, my = (y1+y2)/2 + nx*28;
        path.setAttribute('d', `M${x1.toFixed(1)},${y1.toFixed(1)} Q${mx.toFixed(1)},${my.toFixed(1)} ${x2.toFixed(1)},${y2.toFixed(1)}`);
    }

    function diagSearch(query) {
        query = query.toLowerCase().trim();
        if (!query) { diagClear(); return; }
        document.querySelectorAll('.g-node').forEach(g => {
            const nid   = g.getAttribute('data-id');
            const match = nid.toLowerCase().includes(query);
            g.setAttribute('opacity', match ? '1' : '0.12');
            const bg = g.querySelector('.g-node-bg'), bar = g.querySelector('.g-node-bar');
            if (bg) {
                bg.setAttribute('stroke', match ? '#6366f1' : '#e2e8f0');
                bg.setAttribute('stroke-width', match ? '2.5' : '1.5');
                bg.setAttribute('filter', match ? 'url(#f-node-sel)' : 'url(#f-node)');
            }
            if (bar) bar.setAttribute('fill', match ? '#4f46e5' : '#6366f1');
        });
        document.querySelectorAll('.g-edge').forEach(p => {
            const from  = p.getAttribute('data-from'), to = p.getAttribute('data-to');
            const match = from.toLowerCase().includes(query) || to.toLowerCase().includes(query);
            p.setAttribute('stroke-opacity', match ? '0.7' : '0.04');
            p.setAttribute('stroke-width', match ? '2' : '1');
        });
        document.getElementById('diag-clear-btn').classList.remove('hidden');
    }

    function toggleDiagRels() {
        const panel   = document.getElementById('diag-rels-panel');
        const chevron = document.getElementById('diag-rels-chevron');
        const open    = panel.classList.toggle('hidden');
        chevron.style.transform = open ? '' : 'rotate(180deg)';
    }

    function diagSelect(id) {
        if (_diagSel === id) { diagClear(); return; }
        _diagSel = id;
        const conn = _diagAdj[id] || new Set();

        document.querySelectorAll('.g-node').forEach(g => {
            const nid = g.getAttribute('data-id');
            const bg  = g.querySelector('.g-node-bg'), bar = g.querySelector('.g-node-bar');
            if (nid === id) {
                bg.setAttribute('stroke', '#6366f1'); bg.setAttribute('stroke-width', '2.5');
                bg.setAttribute('filter', 'url(#f-node-sel)'); bar.setAttribute('fill', '#4f46e5');
                g.setAttribute('opacity', '1');
            } else if (conn.has(nid)) {
                bg.setAttribute('stroke', '#6ee7b7'); bg.setAttribute('stroke-width', '2');
                bg.setAttribute('filter', 'url(#f-node-rel)'); bar.setAttribute('fill', '#10b981');
                g.setAttribute('opacity', '1');
            } else {
                bg.setAttribute('stroke', '#e2e8f0'); bg.setAttribute('stroke-width', '1.5');
                bg.setAttribute('filter', 'url(#f-node)'); bar.setAttribute('fill', '#6366f1');
                g.setAttribute('opacity', '0.2');
            }
        });

        document.querySelectorAll('.g-edge').forEach(p => {
            const from = p.getAttribute('data-from'), to = p.getAttribute('data-to'), type = p.getAttribute('data-type');
            if (from === id || to === id) {
                const th = edgeTheme(type);
                p.setAttribute('stroke-width', '2.5'); p.setAttribute('stroke-opacity', '0.95');
                p.setAttribute('marker-end', th.markerA);
            } else {
                p.setAttribute('stroke-width', '1'); p.setAttribute('stroke-opacity', '0.07');
            }
        });

        const n    = _gNodes.find(n => n.id === id);
        const rels = _gEdges.filter(e => e.from === id || e.to === id);
        document.getElementById('diag-info-name').textContent  = id;
        document.getElementById('diag-info-table').textContent = n?.table || '';
        document.getElementById('diag-info-count').textContent = rels.length + ' relationship' + (rels.length !== 1 ? 's' : '');

        // Populate relationship cards (panel hidden until button clicked)
        const el = document.getElementById('diag-info-rels');
        el.innerHTML = '';
        document.getElementById('diag-rels-title').textContent = id + ' relationships';
        rels.forEach(e => {
            const other     = e.from === id ? e.to : e.from;
            const direction = e.from === id ? 'out' : 'in';
            const th        = edgeTheme(e.type);
            const card      = document.createElement('div');
            card.className  = 'flex flex-col gap-1 px-3 py-2.5 rounded-xl border bg-gray-50 hover:bg-white transition-colors shadow-sm';
            card.style.borderLeftWidth = '3px';
            card.style.borderLeftColor = th.stroke;
            card.innerHTML  =
                `<div class="flex items-center justify-between gap-2">` +
                    `<span class="text-xs font-bold truncate" style="color:#1e293b">${other}</span>` +
                    `<span class="text-xs font-mono px-1.5 py-0.5 rounded" style="background:${th.stroke}22;color:${th.stroke}">${direction === 'out' ? '→' : '←'}</span>` +
                `</div>` +
                `<span class="text-xs font-semibold" style="color:${th.stroke}">${e.type}</span>` +
                `<span class="text-xs text-gray-400 font-mono">${e.from === id ? e.from : e.to} → ${other}</span>`;
            el.appendChild(card);
        });

        // Close rels panel when switching nodes
        document.getElementById('diag-rels-panel').classList.add('hidden');
        document.getElementById('diag-rels-chevron').style.transform = '';

        document.getElementById('diag-info').classList.remove('hidden');
        document.getElementById('diag-legend').classList.add('hidden');
        document.getElementById('diag-clear-btn').classList.remove('hidden');
    }

    function diagClear() {
        _diagSel = null;
        document.getElementById('diag-search') && (document.getElementById('diag-search').value = '');
        document.querySelectorAll('.g-node').forEach(g => {
            g.setAttribute('opacity', '1');
            const bg = g.querySelector('.g-node-bg'), bar = g.querySelector('.g-node-bar');
            if (bg)  { bg.setAttribute('stroke', '#e2e8f0'); bg.setAttribute('stroke-width', '1.5'); bg.setAttribute('filter', 'url(#f-node)'); }
            if (bar) bar.setAttribute('fill', '#6366f1');
        });
        document.querySelectorAll('.g-edge').forEach(p => {
            const th = edgeTheme(p.getAttribute('data-type'));
            p.setAttribute('stroke', th.stroke); p.setAttribute('stroke-width', '1.5');
            p.setAttribute('stroke-opacity', '0.4'); p.setAttribute('marker-end', th.marker);
        });
        document.getElementById('diag-info').classList.add('hidden');
        document.getElementById('diag-rels-panel').classList.add('hidden');
        document.getElementById('diag-rels-chevron').style.transform = '';
        document.getElementById('diag-legend').classList.remove('hidden');
        document.getElementById('diag-clear-btn').classList.add('hidden');
    }

    /* ── Dep graph toolbar ──────────────────────────────────────── */
    function copyDepCode() {
        const raw = document.getElementById('dep-raw');
        if (!raw) return;
        navigator.clipboard.writeText(raw.textContent).then(() => {
            const btn = document.getElementById('dep-copy-btn');
            const orig = btn.innerHTML;
            btn.innerHTML = '<svg class="w-3.5 h-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Copied!';
            btn.classList.add('text-green-600');
            setTimeout(() => { btn.innerHTML = orig; btn.classList.remove('text-green-600'); }, 2000);
        });
    }

    function toggleDepCode() {
        document.getElementById('dep-code-block').classList.toggle('hidden');
    }

    function toggleDepFullscreen() {
        const el = document.getElementById('dep-canvas');
        if (!document.fullscreenElement) {
            el.requestFullscreen().then(() => { el.classList.add('bg-white', 'p-10'); });
        } else {
            document.exitFullscreen();
        }
    }

    /* ── Sidebar toggle ─────────────────────────────────────────── */
    let sidebarOpen = true;

    function toggleSidebar() {
        const sidebar   = document.getElementById('sidebar');
        const main      = document.getElementById('main-content');
        const iconMenu  = document.getElementById('icon-menu');
        const iconClose = document.getElementById('icon-close');

        sidebarOpen = !sidebarOpen;

        sidebar.classList.toggle('sidebar-hidden', !sidebarOpen);
        main.classList.toggle('sidebar-hidden', !sidebarOpen);
        iconMenu.classList.toggle('hidden', !sidebarOpen);
        iconClose.classList.toggle('hidden', sidebarOpen);
    }

    /* ── Route filters ──────────────────────────────────────────── */
    let activeMethod = 'all';

    function filterRoutes(method, btn) {
        activeMethod = method;
        document.querySelectorAll('.route-filter').forEach(b => {
            b.classList.remove('bg-slate-800','text-white','border-slate-800');
        });
        btn.classList.add('bg-slate-800','text-white','border-slate-800');
        applyFilters();
    }

    function searchRoutes() {
        applyFilters();
    }

    function applyFilters() {
        const query = document.getElementById('route-search').value.toLowerCase();
        let visible = 0;

        document.querySelectorAll('.route-row').forEach(row => {
            const matchMethod = activeMethod === 'all' || row.dataset.methods.includes(activeMethod);
            const matchSearch = !query || row.textContent.toLowerCase().includes(query);
            const show = matchMethod && matchSearch;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        document.getElementById('no-results').classList.toggle('hidden', visible > 0);
    }
</script>

</body>
</html>
