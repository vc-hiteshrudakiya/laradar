    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div class="sec-header" style="margin-bottom:0;">
            <div class="sec-header__icon" style="background:rgba(255,45,32,.08);border:1px solid rgba(255,45,32,.18);color:var(--cyan);">
                <svg viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <div>
                <h1 class="sec-header__title">AI Chat</h1>
                <p class="sec-header__sub">Ask anything about your architecture — only relevant context is sent to AI.</p>
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
    <div style="max-width:560px;background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.25);border-radius:10px;padding:16px;margin-bottom:20px;">
        <p style="font-size:13px;color:var(--amber);">AI is not enabled. Set <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">ai.enabled = true</code> and <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">GEMINI_API_KEY</code> in your .env.</p>
    </div>
    @endif

    {{-- Suggestion chips --}}
    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px;" id="chat-suggestions">
        <button onclick="chatSuggest('Which controller has the most methods?')" class="chat-suggestion-chip">
            <svg style="width:12px;height:12px;color:#FF2D20;flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
            Which controller is largest?
        </button>
        <button onclick="chatSuggest('Trace the main request flow from route through controller to model.')" class="chat-suggestion-chip">
            <svg style="width:12px;height:12px;color:#FF2D20;flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
            Trace request flow
        </button>
        <button onclick="chatSuggest('Are there any SOLID principle violations?')" class="chat-suggestion-chip">
            <svg style="width:12px;height:12px;color:var(--rose);flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            SOLID violations?
        </button>
        <button onclick="chatSuggest('Which models have the most relationships?')" class="chat-suggestion-chip">
            <svg style="width:12px;height:12px;color:#FF2D20;flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
            Model relationships
        </button>
        <button onclick="chatSuggest('What services should I extract from my controllers?')" class="chat-suggestion-chip">
            <svg style="width:12px;height:12px;color:#2DD4BF;flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Suggest service extractions
        </button>
        <button onclick="chatSuggest('Explain the overall architecture and data flow.')" class="chat-suggestion-chip">
            <svg style="width:12px;height:12px;color:#FF2D20;flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Overall architecture
        </button>
    </div>

    {{-- Messages --}}
    <div id="chat-messages" style="flex:1;overflow-y:auto;display:flex;flex-direction:column;gap:16px;margin-bottom:16px;max-height:calc(100vh - 420px);min-height:200px;padding-right:4px;">
        <div id="chat-empty" style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:200px;color:var(--text-faint);">
            <div style="width:56px;height:56px;border-radius:16px;background:rgba(255,45,32,.08);border:1px solid rgba(255,45,32,.18);display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                <svg style="width:26px;height:26px;color:#FF2D20;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <p style="font-size:13px;font-weight:600;color:var(--text-dim);">Ask anything about your architecture</p>
            <p style="font-size:11px;color:var(--text-faint);margin-top:4px;">Use a suggestion above or type your own question</p>
        </div>
    </div>

    {{-- Input --}}
    <div style="border:1px solid var(--border);border-radius:12px;background:var(--bg-elevated);overflow:hidden;transition:border-color .2s,box-shadow .2s;" onfocusin="this.style.borderColor='rgba(255,45,32,.4)';this.style.boxShadow='0 0 0 3px rgba(255,45,32,.08)'" onfocusout="this.style.borderColor='var(--border)';this.style.boxShadow=''">
        <textarea id="chat-input" rows="2"
            placeholder="e.g. Trace the main request flow  •  Which controller is too large?  •  Where should I add a service?"
            {{ !config('laradar.ai.enabled', false) ? 'disabled' : '' }}
            oninput="chatPreviewContext(this.value)"
            onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();chatSend();}"
            style="width:100%;padding:14px 16px 6px;font-size:13px;color:var(--text);background:transparent;resize:none;outline:none;border:none;font-family:var(--font-sans);box-sizing:border-box;line-height:1.6;"></textarea>
        <div style="display:flex;align-items:center;justify-content:space-between;padding:6px 12px 10px;">
            <span id="chat-context-hint" style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:280px;"></span>
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:10px;color:var(--text-faint);font-family:var(--font-mono);">Enter to send</span>
                <button onclick="chatSend()" id="chat-send-btn" class="chat-send-btn" {{ !config('laradar.ai.enabled', false) ? 'disabled' : '' }}>
                    <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </div>
        </div>
    </div>


