    <div id="services-list">
        @php
            $svcTotal   = count($data['services']);
            $svcMethods = $svcTotal > 0 ? round(array_sum(array_map(fn($s) => count($s['methods']??[]), $data['services'])) / $svcTotal) : 0;
        @endphp
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $svcTotal }}</span>
                <span class="mds-top-stat-lbl">Total Services</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $svcMethods }}</span>
                <span class="mds-top-stat-lbl">Avg Methods</span>
            </div>
        </div>
        <div class="mds-toolbar">
            <input id="services-search" oninput="filterGrid('services')" type="search" placeholder="Search services…" style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;flex:1;max-width:240px;font-family:var(--font-mono);">
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ $svcTotal }} services</span>
        </div>
        @if(empty($data['services']))
        <div class="atlas-card" style="text-align:center;padding:64px;">
            <div style="width:56px;height:56px;background:var(--bg-hover);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg style="width:28px;height:28px;color:var(--text-faint);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
            </div>
            <p style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:6px;">No services detected</p>
            <p style="font-size:13px;color:var(--text-faint);">Create service classes in <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">app/Services/</code> to encapsulate your business logic.</p>
        </div>
        @else
        <div id="services-grid" style="background:var(--bg-elevated);border-radius:12px;border:1px solid var(--border);overflow:hidden;">
            <div class="mds-list-head" style="grid-template-columns:40px 1fr 90px 80px;">
                <span></span><span>Service</span><span>Methods</span><span>Deps</span>
            </div>
            @foreach($data['services'] as $i => $svc)
            <div class="mds-list-row" style="grid-template-columns:40px 1fr 90px 80px;" data-name="{{ strtolower($svc['name']) }}">
                <div class="mds-list-av" style="background:rgba(255,45,32,.10);color:#FF2D20;border-color:rgba(255,45,32,.25);">{{ strtoupper(substr($svc['name'],0,1)) }}</div>
                <div style="min-width:0;">
                    <div style="font-weight:700;font-size:13.5px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $svc['name'] }}</div>
                    <div style="font-family:var(--font-mono);font-size:10.5px;color:var(--text-faint);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $svc['namespace'] }}</div>
                </div>
                <div style="font-family:var(--font-mono);font-size:14px;font-weight:800;color:#FF2D20;text-align:center;">{{ count($svc['methods']??[]) }}</div>
                <div style="font-family:var(--font-mono);font-size:14px;font-weight:800;color:var(--text-dim);text-align:center;">{{ count($svc['dependencies']??[]) ?: '—' }}</div>
            </div>
            @endforeach
        </div>
        @endif
    </div>


