    <div id="repositories-list">
        @php
            $repoTotal    = count($data['repositories']);
            $repoMethods  = $repoTotal > 0 ? round(array_sum(array_map(fn($r) => count($r['methods']??[]), $data['repositories'])) / $repoTotal) : 0;
            $repoDeps     = array_sum(array_map(fn($r) => count($r['dependencies']??[]), $data['repositories']));
        @endphp
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $repoTotal }}</span>
                <span class="mds-top-stat-lbl">Repositories</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $repoMethods }}</span>
                <span class="mds-top-stat-lbl">Avg Methods</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $repoDeps }}</span>
                <span class="mds-top-stat-lbl">Total Deps</span>
            </div>
        </div>
        <div class="mds-toolbar">
            <input id="repositories-search" oninput="filterGrid('repositories')" type="search" placeholder="Search repositories…" style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;flex:1;max-width:240px;font-family:var(--font-mono);">
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ $repoTotal }} repositories</span>
        </div>
        @if(empty($data['repositories']))
        <div class="atlas-card" style="text-align:center;padding:64px;">
            <div style="width:56px;height:56px;background:var(--bg-hover);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg style="width:28px;height:28px;color:var(--text-faint);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            </div>
            <p style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:6px;">No repositories detected</p>
            <p style="font-size:13px;color:var(--text-faint);">Create repository classes in <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">app/Repositories/</code> to abstract your data layer.</p>
        </div>
        @else
        <div id="repositories-grid" style="background:var(--bg-elevated);border-radius:12px;border:1px solid var(--border);overflow:hidden;">
            <div class="mds-list-head" style="grid-template-columns:40px 1fr 90px 80px;">
                <span></span><span>Repository</span><span>Methods</span><span>Deps</span>
            </div>
            @foreach($data['repositories'] as $i => $repo)
            <div class="mds-list-row" style="grid-template-columns:40px 1fr 90px 80px;" data-name="{{ strtolower($repo['name']) }}">
                <div class="mds-list-av" style="background:rgba(255,45,32,.10);color:#FF2D20;border-color:rgba(255,45,32,.25);">{{ strtoupper(substr($repo['name'],0,1)) }}</div>
                <div style="min-width:0;">
                    <div style="font-weight:700;font-size:13.5px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $repo['name'] }}</div>
                    <div style="font-family:var(--font-mono);font-size:10.5px;color:var(--text-faint);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $repo['namespace'] }}</div>
                </div>
                <div style="font-family:var(--font-mono);font-size:14px;font-weight:800;color:#FF2D20;text-align:center;">{{ count($repo['methods']??[]) }}</div>
                <div style="font-family:var(--font-mono);font-size:14px;font-weight:800;color:var(--text-dim);text-align:center;">{{ count($repo['dependencies']??[]) ?: '—' }}</div>
            </div>
            @endforeach
        </div>
        @endif
    </div>


