    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div class="sec-header" style="margin-bottom:0;">
            <div class="sec-header__icon" style="background:rgba(255,45,32,.08);border:1px solid rgba(255,45,32,.18);color:var(--cyan);">
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

    <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:24px;align-items:center;">
        <button onclick="docsGenerateAll()"
            {{ !config('laradar.ai.enabled', false) ? 'disabled' : '' }}
            class="ai-analyze-btn"
            style="opacity:{{ config('laradar.ai.enabled', false) ? 1 : 0.4 }};cursor:{{ config('laradar.ai.enabled', false) ? 'pointer' : 'not-allowed' }}">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Generate All Docs
        </button>
        <button type="button" data-fmt-btn id="docs-fmt-all-btn" onclick="openFmtPanel(this, 'docs-fmt-all')"
            style="height:38px;border:1px solid #FF2D20;border-radius:10px;padding:0 14px;font-size:12px;font-family:var(--font-mono);background:var(--bg-elevated);color:#FF2D20;cursor:pointer;display:inline-flex;align-items:center;gap:8px;outline:none;">
            <span>.md</span>
            <svg style="width:10px;height:10px;flex:none;transition:transform .2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <input type="hidden" id="docs-fmt-all" value="md">
        <button onclick="docsDownloadAll()" id="docs-download-all-btn" class="atlas-btn" style="display:none;padding:9px 18px;font-size:13px;border-radius:10px;">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Download All
        </button>

        {{-- AI Graphic Report button --}}
        <button onclick="generateAIGraphicReport()" id="ai-report-btn"
            {{ !config('laradar.ai.enabled', false) ? 'disabled' : '' }}
            class="atlas-btn"
            style="padding:9px 18px;font-size:13px;border-radius:10px;border-color:rgba(255,45,32,0.4);color:#FF2D20;background:rgba(255,45,32,0.08);opacity:{{ config('laradar.ai.enabled', false) ? 1 : 0.4 }};cursor:{{ config('laradar.ai.enabled', false) ? 'pointer' : 'not-allowed' }}">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span id="ai-report-btn-label">Generate AI Graphic Report</span>
        </button>
    </div>

    {{-- AI Report progress panel --}}
    <div id="ai-report-progress" style="display:none;max-width:480px;margin-bottom:32px;border-radius:16px;overflow:hidden;border:1px solid rgba(255,45,32,0.3);background:var(--bg-elevated);">
        <div style="display:flex;align-items:center;gap:12px;padding:14px 20px;background:rgba(255,45,32,0.10);border-bottom:1px solid rgba(255,45,32,0.2);">
            <svg style="width:16px;height:16px;animation:spin 1s linear infinite;flex-shrink:0;color:#FF2D20;" fill="none" viewBox="0 0 24 24" id="ai-report-spinner">
                <circle style="opacity:.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path style="opacity:.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <p style="font-size:13px;font-weight:600;color:#FF2D20;font-family:var(--font-mono);" id="ai-report-progress-title">Generating AI Report…</p>
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

            <div style="display:flex;gap:6px;margin-top:auto;padding-top:4px;align-items:center;">
                <button onclick="docsGenerate('{{ $type }}')"
                    {{ !config('laradar.ai.enabled', false) ? 'disabled' : '' }}
                    id="doc-gen-btn-{{ $type }}"
                    class="atlas-btn atlas-btn--cyan"
                    style="flex:1;justify-content:center;font-size:11px;padding:7px 10px;border-radius:8px;opacity:{{ config('laradar.ai.enabled', false) ? 1 : 0.4 }};cursor:{{ config('laradar.ai.enabled', false) ? 'pointer' : 'not-allowed' }}">
                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    Generate
                </button>
                <button type="button" data-fmt-btn id="doc-fmt-btn-{{ $type }}" onclick="openFmtPanel(this, 'doc-fmt-{{ $type }}')"
                    style="height:32px;border:1px solid #FF2D20;border-radius:8px;padding:0 10px;font-size:11px;font-family:var(--font-mono);background:var(--bg-elevated);color:#FF2D20;cursor:pointer;display:inline-flex;align-items:center;gap:5px;outline:none;">
                    <span>.md</span>
                    <svg style="width:9px;height:9px;flex:none;transition:transform .2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <input type="hidden" id="doc-fmt-{{ $type }}" value="md">
            </div>
        </div>
        @endforeach

    </div>


