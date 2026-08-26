    <div id="events-list">
        @php $evtBroadcast = count(array_filter($data['events'], fn($e) => $e['should_broadcast']??false)); @endphp
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ count($data['events']) }}</span>
                <span class="mds-top-stat-lbl">Total Events</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $evtBroadcast }}</span>
                <span class="mds-top-stat-lbl">Broadcast</span>
            </div>
        </div>
        <div class="mds-toolbar">
            <input id="events-search" oninput="filterGrid('events')" type="search" placeholder="Search events…" style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;flex:1;max-width:240px;font-family:var(--font-mono);">
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ count($data['events']) }} events</span>
        </div>
        @if(empty($data['events']))
        <div class="atlas-card" style="text-align:center;padding:64px;">
            <div style="width:56px;height:56px;background:var(--bg-hover);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg style="width:28px;height:28px;color:var(--text-faint);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <p style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:6px;">No events detected</p>
            <p style="font-size:13px;color:var(--text-faint);">Create event classes in <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">app/Events/</code> and their listeners in <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">app/Listeners/</code>.</p>
        </div>
        @else
        <div id="events-grid" style="background:var(--bg-elevated);border-radius:12px;border:1px solid var(--border);overflow:hidden;">
            <div class="mds-list-head" style="grid-template-columns:40px 1fr 100px 80px;">
                <span></span><span>Event</span><span>Broadcast</span><span>Props</span>
            </div>
            @foreach($data['events'] as $i => $evt)
            <div class="mds-list-row" style="grid-template-columns:40px 1fr 100px 80px;" data-name="{{ strtolower($evt['name']) }}">
                <div class="mds-list-av" style="background:rgba(255,45,32,.10);color:#FF2D20;border-color:rgba(255,45,32,.25);">{{ strtoupper(substr($evt['name'],0,1)) }}</div>
                <div style="min-width:0;">
                    <div style="font-weight:700;font-size:13.5px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $evt['name'] }}</div>
                    <div style="font-family:var(--font-mono);font-size:10.5px;color:var(--text-faint);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $evt['namespace'] }}</div>
                </div>
                <div>
                    @if($evt['should_broadcast']??false)
                    <span style="font-size:10px;font-family:var(--font-mono);color:#FF2D20;background:rgba(255,45,32,.08);padding:2px 8px;border-radius:20px;border:1px solid rgba(255,45,32,.2);">broadcast</span>
                    @else
                    <span style="font-size:12px;color:var(--text-faint);">—</span>
                    @endif
                </div>
                <div style="font-family:var(--font-mono);font-size:13px;font-weight:700;color:var(--text-dim);">{{ count($evt['properties']??[]) ?: '—' }}</div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="events-detail" style="display:none"><div id="events-detail-content"></div></div>


