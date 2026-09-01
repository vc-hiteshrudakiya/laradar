    <div id="observers-list">
        @php
            $obsModels = count(array_unique(array_filter(array_column($data['observers'], 'model'))));
        @endphp
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ count($data['observers']) }}</span>
                <span class="mds-top-stat-lbl">Total Observers</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $obsModels }}</span>
                <span class="mds-top-stat-lbl">Models Covered</span>
            </div>
        </div>
        <div class="mds-toolbar">
            <input id="observers-search" oninput="filterGrid('observers')" type="search" placeholder="Search observers…" style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;flex:1;max-width:240px;font-family:var(--font-mono);">
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ count($data['observers']) }} observers</span>
        </div>
        @if(empty($data['observers']))
        <div class="atlas-card" style="text-align:center;padding:64px;">
            <div style="width:56px;height:56px;background:var(--bg-hover);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg style="width:28px;height:28px;color:var(--text-faint);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </div>
            <p style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:6px;">No observers detected</p>
            <p style="font-size:13px;color:var(--text-faint);">Create observer classes in <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">app/Observers/</code> and register them in your <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">AppServiceProvider</code>.</p>
        </div>
        @else
        <div id="observers-grid" style="background:var(--bg-elevated);border-radius:12px;border:1px solid var(--border);overflow:hidden;">
            <div class="mds-list-head" style="grid-template-columns:40px 1fr 160px 1fr;">
                <span></span><span>Observer</span><span>Observes</span><span>Events</span>
            </div>
            @foreach($data['observers'] as $i => $obs)
            <div class="mds-list-row" style="grid-template-columns:40px 1fr 160px 1fr;" data-name="{{ strtolower($obs['name']) }}">
                <div class="mds-list-av" style="background:rgba(255,45,32,.10);color:#FF2D20;border-color:rgba(255,45,32,.25);">{{ strtoupper(substr($obs['name'],0,1)) }}</div>
                <div style="font-weight:700;font-size:13.5px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $obs['name'] }}</div>
                <div style="font-family:var(--font-mono);font-size:11.5px;color:var(--text-faint);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $obs['model']??'Unknown' }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:4px;">
                    @foreach($obs['events']??[] as $e)
                    <span style="font-size:10px;font-family:var(--font-mono);color:#FF2D20;background:rgba(255,45,32,.08);padding:2px 8px;border-radius:20px;border:1px solid rgba(255,45,32,.2);">{{ $e }}</span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>


