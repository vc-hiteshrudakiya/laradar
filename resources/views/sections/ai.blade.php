    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;flex-wrap:wrap;gap:12px;">
        <div class="sec-header" style="margin-bottom:0;">
            <div class="sec-header__icon" style="background:rgba(255,45,32,.08);border:1px solid rgba(255,45,32,.18);color:#FF2D20;">
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
        <div style="display:inline-flex;align-items:center;gap:14px;background:rgba(255,45,32,.06);border:1px solid rgba(255,45,32,.18);border-radius:12px;padding:14px 20px;">
            <svg style="width:22px;height:22px;color:#FF2D20;animation:aiSpin 1s linear infinite;flex:none;" fill="none" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="40 20" opacity=".3"></circle>
                <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" opacity=".8"></path>
            </svg>
            <div>
                <p style="font-size:13px;font-weight:700;color:#FF2D20;font-family:var(--font-mono);margin-bottom:2px;">Analyzing architecture…</p>
                <p style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);">Usually takes 10–30 seconds</p>
            </div>
        </div>
    </div>

    {{-- Error state --}}
    <div id="ai-error" style="display:none;margin-bottom:24px;max-width:560px;background:rgba(248,113,113,0.08);border:1px solid rgba(248,113,113,0.3);border-radius:12px;padding:16px;">
        <p style="font-size:13px;font-weight:600;color:var(--rose);margin-bottom:4px;">Analysis failed</p>
        <p id="ai-error-msg" style="font-size:12px;color:var(--rose);font-family:var(--font-mono);"></p>
    </div>

    {{-- Results (always visible — JS populates content after analysis) --}}
    <div id="ai-results" style="max-width:900px;display:flex;flex-direction:column;gap:16px;">

        {{-- Summary + AI Score --}}
        <div style="display:flex;gap:16px;flex-wrap:wrap;">
            <div style="flex:1;min-width:240px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
                <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:10px;">AI Summary</p>
                <p id="ai-summary" style="font-size:13px;color:var(--text-dim);line-height:1.65;">
                    <span class="ai-placeholder" style="color:var(--text-faint);font-style:italic;">Run analysis to see an AI-generated summary of your architecture.</span>
                </p>
            </div>
            <div style="width:160px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;flex:none;">
                <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);">AI Score</p>
                <div style="position:relative;width:90px;height:90px;">
                    <svg width="90" height="90" viewBox="0 0 90 90">
                        <circle cx="45" cy="45" r="36" fill="none" stroke="var(--bg-sunken)" stroke-width="7"/>
                        <circle id="ai-score-ring" class="ai-score-ring" cx="45" cy="45" r="36" fill="none"
                            stroke="#FF2D20" stroke-width="7" stroke-linecap="round"
                            stroke-dasharray="226" stroke-dashoffset="226"
                            style="transition:stroke-dashoffset .9s cubic-bezier(.4,0,.2,1),stroke .4s;"/>
                    </svg>
                    <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                        <span id="ai-score-num" style="font-size:26px;font-weight:800;color:var(--text-faint);font-family:var(--font-sans);line-height:1;">—</span>
                        <span style="font-size:10px;color:var(--text-faint);font-family:var(--font-mono);">/100</span>
                    </div>
                </div>
                <div id="ai-score-bar" style="display:none;"></div>
            </div>
        </div>

        {{-- SOLID Review --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
            <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:14px;">SOLID Principles</p>
            <div id="ai-solid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;">
                <p class="ai-placeholder" style="font-size:12px;color:var(--text-faint);font-style:italic;grid-column:1/-1;">Run analysis to evaluate SOLID principle adherence.</p>
            </div>
        </div>

        {{-- Problems --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
            <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:14px;">Problems Detected</p>
            <div id="ai-problems" style="display:flex;flex-direction:column;gap:10px;">
                <p class="ai-placeholder" style="font-size:12px;color:var(--text-faint);font-style:italic;">No analysis run yet — click Analyze with AI to detect problems.</p>
            </div>
        </div>

        {{-- Suggestions --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
            <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:14px;">Suggestions</p>
            <div id="ai-suggestions" style="display:flex;flex-direction:column;gap:10px;">
                <p class="ai-placeholder" style="font-size:12px;color:var(--text-faint);font-style:italic;">Suggestions will appear here after analysis.</p>
            </div>
        </div>

        {{-- Laravel Best Practices --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
            <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:14px;">Laravel Best Practices</p>
            <div id="ai-laravel-practices" style="display:flex;flex-direction:column;gap:8px;">
                <p class="ai-placeholder" style="font-size:12px;color:var(--text-faint);font-style:italic;">Laravel-specific recommendations will appear here.</p>
            </div>
        </div>

        {{-- Best Practices (followed) --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
            <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:12px;">Practices Already Followed</p>
            <ul id="ai-best-practices" style="display:flex;flex-direction:column;gap:6px;">
                <p class="ai-placeholder" style="font-size:12px;color:var(--text-faint);font-style:italic;">Practices you already follow will be listed here.</p>
            </ul>
        </div>

        {{-- Re-analyze --}}
        <div id="ai-reanalyze" style="display:none;align-items:center;gap:12px;padding-top:4px;">
            <button onclick="aiAnalyze()" class="ai-analyze-btn" style="padding:9px 18px;font-size:12px;">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Re-analyze
            </button>
            <span id="ai-provider-badge" style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);"></span>
        </div>

    </div>


