    <div id="controllers-list">

        @php
            $ctrlTotal      = count($data['controllers']);
            $ctrlTotalMeth  = array_sum(array_column($data['controllers'], 'method_count'));
            $ctrlWithMw     = count(array_filter($data['controllers'], fn($c) => !empty($c['middleware'])));
            $ctrlResource   = count(array_filter($data['controllers'], fn($c) => !empty($c['is_resource'])));
            $ctrlMaxMethods = max(array_column($data['controllers'], 'method_count') ?: [1]);
        @endphp
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $ctrlTotal }}</span>
                <span class="mds-top-stat-lbl">Controllers</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $ctrlTotalMeth }}</span>
                <span class="mds-top-stat-lbl">Total Methods</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $ctrlResource }}</span>
                <span class="mds-top-stat-lbl">Resource</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $ctrlWithMw }}</span>
                <span class="mds-top-stat-lbl">With Middleware</span>
            </div>
        </div>

        <div class="mds-toolbar">
            <input id="controllers-search" oninput="filterGrid('controllers')" type="search" placeholder="Search controllers…" style="border:1px solid var(--border);border-radius:8px;padding:8px 14px;font-size:13px;flex:1;max-width:240px;font-family:var(--font-mono);">
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ $ctrlTotal }} controllers</span>
        </div>

        <div id="controllers-grid" style="background:var(--bg-elevated);border-radius:12px;border:1px solid var(--border);overflow:hidden;">
            <div class="mds-list-head" style="grid-template-columns:36px 1fr 80px 70px 70px 90px;">
                <span></span><span>Controller</span><span>Methods</span><span>Routes</span><span>Deps</span><span>Complexity</span>
            </div>
            @foreach($data['controllers'] as $i => $ctrl)
            @php
                $ctrlRouteCount = count(array_filter($data['routes'] ?? [], fn($r) => class_basename($r['controller']['class'] ?? '') === $ctrl['name']));
                $ctrlComplexity = $ctrlMaxMethods > 0 ? round(($ctrl['method_count']??0) / $ctrlMaxMethods * 100) : 0;
            @endphp
            <div class="mds-list-row" style="grid-template-columns:36px 1fr 80px 70px 70px 90px;" data-name="{{ strtolower($ctrl['name']) }}">
                <div class="mds-list-av" style="background:rgba(255,45,32,0.10);color:#FF2D20;border-color:rgba(255,45,32,0.25);">{{ substr($ctrl['name'],0,1) }}</div>
                <div style="min-width:0;">
                    <div style="font-weight:700;font-size:13.5px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $ctrl['name'] }}
                        @if(!empty($ctrl['is_resource']))<span style="font-family:var(--font-mono);font-size:9px;padding:2px 6px;border-radius:10px;background:rgba(255,45,32,0.08);color:#FF2D20;border:1px solid rgba(255,45,32,0.2);margin-left:6px;">Resource</span>@endif
                    </div>
                    <div style="font-family:var(--font-mono);font-size:10.5px;color:var(--text-faint);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $ctrl['namespace'] }}</div>
                </div>
                <div style="font-family:var(--font-mono);font-size:13px;font-weight:700;color:var(--text);text-align:center;">{{ $ctrl['method_count']??0 }}</div>
                <div style="font-family:var(--font-mono);font-size:13px;font-weight:700;color:#FF2D20;text-align:center;">{{ $ctrlRouteCount }}</div>
                <div style="font-family:var(--font-mono);font-size:13px;font-weight:700;color:#FF2D20;text-align:center;">{{ count($ctrl['dependencies']??[]) }}</div>
                <div style="padding-right:8px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                        <span style="font-family:var(--font-mono);font-size:10px;color:#FF2D20;font-weight:700;">{{ $ctrlComplexity }}%</span>
                    </div>
                    <div class="ctrl-complexity-track">
                        <div class="ctrl-complexity-fill" style="width:0;" data-target="{{ $ctrlComplexity }}"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>


