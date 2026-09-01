@php $rs = $data['route_summary'] ?? []; @endphp
    @php
        $mwUsage   = $rs['middleware_usage'] ?? [];
        $mwTotal   = count($mwUsage);
        $mwTopName = $mwTotal > 0 ? array_key_first($mwUsage) : '—';
        $mwTopCnt  = $mwTotal > 0 ? reset($mwUsage) : 0;
        $mwAuthCnt = $mwUsage['auth'] ?? 0;
    @endphp
    <div class="mds-top-stats">
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $mwTotal }}</span>
            <span class="mds-top-stat-lbl">Total Middleware</span>
        </div>
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $mwTopCnt }}</span>
            <span class="mds-top-stat-lbl">Most Used Routes</span>
        </div>
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $mwAuthCnt }}</span>
            <span class="mds-top-stat-lbl">Auth Protected</span>
        </div>
    </div>
    <div class="mds-toolbar">
        <input id="middleware-search" oninput="filterGrid('middleware')" type="search" placeholder="Search middleware…" style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;flex:1;max-width:240px;font-family:var(--font-mono);">
        <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ $mwTotal }} middleware</span>
    </div>
    @if(empty($mwUsage))
    <div class="atlas-card" style="text-align:center;padding:64px;">
        <div style="width:56px;height:56px;background:var(--bg-hover);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg style="width:28px;height:28px;color:var(--text-faint);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
        </div>
        <p style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:6px;">No middleware detected</p>
        <p style="font-size:13px;color:var(--text-faint);">Assign middleware to your routes or route groups in <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">routes/web.php</code> or <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">routes/api.php</code>.</p>
    </div>
    @else
    @php $mwMax = max(array_values($mwUsage) ?: [1]); @endphp
    <div class="atlas-card" style="padding:0;overflow:hidden;">
        <div style="display:grid;grid-template-columns:40px 1fr 180px 80px;gap:10px;padding:10px 16px;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--text-faint);font-family:var(--font-mono);background:var(--bg-sunken);border-bottom:1px solid var(--border);">
            <div></div>
            <div>Middleware</div>
            <div>Usage</div>
            <div style="text-align:right;">Routes</div>
        </div>
        <div id="middleware-grid">
            @foreach($mwUsage as $mwName => $mwCnt)
            <div class="mw-row" data-name="{{ strtolower($mwName) }}">
                <div style="width:32px;height:32px;border-radius:9px;background:rgba(255,45,32,.10);border:1px solid rgba(255,45,32,.20);color:#FF2D20;display:flex;align-items:center;justify-content:center;flex:none;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div style="font-family:var(--font-mono);font-size:13px;font-weight:600;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $mwName }}</div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="flex:1;background:var(--bg-hover);border-radius:4px;height:6px;overflow:hidden;">
                        <div style="height:100%;width:{{ round($mwCnt / $mwMax * 100) }}%;background:#FF2D20;border-radius:4px;"></div>
                    </div>
                    <span style="font-size:10px;color:var(--text-faint);font-family:var(--font-mono);white-space:nowrap;">{{ round($mwCnt / $mwMax * 100) }}%</span>
                </div>
                <div style="text-align:right;font-family:var(--font-mono);font-size:13px;font-weight:700;color:#FF2D20;">{{ $mwCnt }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif


