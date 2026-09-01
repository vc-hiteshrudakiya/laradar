    <div id="jobs-list">
        @php
            $jobsQueued  = count(array_filter($data['jobs'], fn($j) => $j['should_queue']??false));
            $jobsTimeout = count(array_filter($data['jobs'], fn($j) => !empty($j['timeout'])));
        @endphp
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ count($data['jobs']) }}</span>
                <span class="mds-top-stat-lbl">Total Jobs</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $jobsQueued }}</span>
                <span class="mds-top-stat-lbl">ShouldQueue</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $jobsTimeout }}</span>
                <span class="mds-top-stat-lbl">With Timeout</span>
            </div>
        </div>
        <div class="mds-toolbar">
            <input id="jobs-search" oninput="filterGrid('jobs')" type="search" placeholder="Search jobs…" style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;flex:1;max-width:240px;font-family:var(--font-mono);">
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ count($data['jobs']) }} jobs</span>
        </div>
        @if(empty($data['jobs']))
        <div class="atlas-card" style="text-align:center;padding:64px;">
            <div style="width:56px;height:56px;background:var(--bg-hover);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg style="width:28px;height:28px;color:var(--text-faint);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <p style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:6px;">No jobs detected</p>
            <p style="font-size:13px;color:var(--text-faint);">Create job classes in <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">app/Jobs/</code> and dispatch them via queues.</p>
        </div>
        @else
        <div id="jobs-grid" style="background:var(--bg-elevated);border-radius:12px;border:1px solid var(--border);overflow:hidden;">
            <div class="mds-list-head" style="grid-template-columns:40px 1fr 110px 80px 100px;">
                <span></span><span>Job</span><span>Queue</span><span>Tries</span><span>Timeout</span>
            </div>
            @foreach($data['jobs'] as $i => $job)
            <div class="mds-list-row" style="grid-template-columns:40px 1fr 110px 80px 100px;" data-name="{{ strtolower($job['name']) }}">
                <div class="mds-list-av" style="background:rgba(255,45,32,.10);color:#FF2D20;border-color:rgba(255,45,32,.25);">{{ strtoupper(substr($job['name'],0,1)) }}</div>
                <div style="min-width:0;">
                    <div style="font-weight:700;font-size:13.5px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $job['name'] }}</div>
                    @if($job['should_queue']??false)<span style="font-size:9px;padding:1px 6px;border-radius:4px;background:rgba(255,45,32,.08);color:#FF2D20;border:1px solid rgba(255,45,32,.2);font-family:var(--font-mono);">queued</span>@endif
                </div>
                <div style="font-family:var(--font-mono);font-size:11.5px;color:var(--text-faint);">{{ $job['queue']??'default' }}</div>
                <div style="font-family:var(--font-mono);font-size:13px;font-weight:700;color:var(--text-dim);">{{ $job['tries']??'—' }}</div>
                <div style="font-family:var(--font-mono);font-size:13px;font-weight:700;color:var(--text-dim);">{{ $job['timeout'] ? $job['timeout'].'s' : '—' }}</div>
            </div>
            @endforeach
        </div>
        @endif
    </div>


