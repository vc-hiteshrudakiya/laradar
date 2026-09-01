    <div id="policies-list">
        @php
            $polActions = array_sum(array_map(fn($p) => count($p['actions']??[]), $data['policies']));
        @endphp
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ count($data['policies']) }}</span>
                <span class="mds-top-stat-lbl">Total Policies</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $polActions }}</span>
                <span class="mds-top-stat-lbl">Total Actions</span>
            </div>
        </div>
        <div class="mds-toolbar">
            <input id="policies-search" oninput="filterGrid('policies')" type="search" placeholder="Search policies…" style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;flex:1;max-width:240px;font-family:var(--font-mono);">
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ count($data['policies']) }} policies</span>
        </div>
        @if(empty($data['policies']))
        <div class="atlas-card" style="text-align:center;padding:64px;">
            <div style="width:56px;height:56px;background:var(--bg-hover);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg style="width:28px;height:28px;color:var(--text-faint);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <p style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:6px;">No policies detected</p>
            <p style="font-size:13px;color:var(--text-faint);">Create policy classes in <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">app/Policies/</code> and register them in your <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">AuthServiceProvider</code>.</p>
        </div>
        @else
        <div id="policies-grid" style="background:var(--bg-elevated);border-radius:12px;border:1px solid var(--border);overflow:hidden;">
            <div class="mds-list-head" style="grid-template-columns:40px 1fr 120px 1fr;">
                <span></span><span>Policy</span><span>Model</span><span>Actions</span>
            </div>
            @foreach($data['policies'] as $i => $pol)
            <div class="mds-list-row" style="grid-template-columns:40px 1fr 120px 1fr;" data-name="{{ strtolower($pol['name']) }}">
                <div class="mds-list-av" style="background:rgba(255,45,32,.10);color:#FF2D20;border-color:rgba(255,45,32,.25);">{{ strtoupper(substr($pol['name'],0,1)) }}</div>
                <div style="font-weight:700;font-size:13.5px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $pol['name'] }}</div>
                <div style="font-family:var(--font-mono);font-size:11.5px;color:var(--text-faint);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $pol['model']??'Unknown' }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:4px;">
                    @foreach($pol['actions']??[] as $a)
                    <span style="font-size:10px;font-family:var(--font-mono);color:#FF2D20;background:rgba(255,45,32,.08);padding:2px 8px;border-radius:20px;border:1px solid rgba(255,45,32,.2);">{{ $a }}</span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>


