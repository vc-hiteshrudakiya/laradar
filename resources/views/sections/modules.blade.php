
    @php
        $modules    = $data['modules'] ?? [];
        $modCtrl    = count($modules) > 0 ? array_sum(array_column($modules, 'controllers')) : 0;
        $modModel   = count($modules) > 0 ? array_sum(array_column($modules, 'models'))      : 0;
        $modRoute   = count($modules) > 0 ? array_sum(array_column($modules, 'routes'))      : 0;
    @endphp
    <div class="mds-top-stats">
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ count($modules) }}</span>
            <span class="mds-top-stat-lbl">Total Modules</span>
        </div>
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $modCtrl }}</span>
            <span class="mds-top-stat-lbl">Controllers</span>
        </div>
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $modModel }}</span>
            <span class="mds-top-stat-lbl">Models</span>
        </div>
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $modRoute }}</span>
            <span class="mds-top-stat-lbl">Routes</span>
        </div>
    </div>
    <div class="mds-toolbar">
        <input id="modules-search" oninput="filterGrid('modules')" type="search" placeholder="Search modules…" style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;flex:1;max-width:240px;font-family:var(--font-mono);">
        <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ count($modules) }} modules</span>
    </div>

    @if(empty($modules))
    <div class="atlas-card" style="text-align:center;padding:64px;">
        <div style="width:56px;height:56px;background:var(--bg-hover);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg style="width:28px;height:28px;color:var(--text-faint);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <p style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:6px;">No modules detected</p>
        <p style="font-size:13px;color:var(--text-faint);">Create a <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">Modules/</code> directory at your project root with subfolders per module.</p>
    </div>
    @else

    {{-- Module cards --}}
    <div id="modules-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
        @php
        $modPalette = [
            ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.25)'],
            ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.25)'],
            ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.25)'],
            ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.25)'],
            ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.25)'],
            ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.25)'],
            ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.25)'],
            ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.25)'],
            ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.25)'],
            ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.25)'],
        ];
        @endphp
        @foreach($modules as $i => $mod)
        @php
        $mp      = $modPalette[$i % count($modPalette)];
        $initial = strtoupper(substr($mod['name'], 0, 1));
        $hasExtras = $mod['jobs'] > 0 || $mod['events'] > 0 || $mod['services'] > 0;
        @endphp
        <div data-name="{{ strtolower($mod['name']) }}" style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:16px;overflow:hidden;display:flex;flex-direction:column;transition:border-color .2s,transform .2s;" onmouseenter="this.style.borderColor='{{ $mp['border'] }}';this.style.transform='translateY(-3px)'" onmouseleave="this.style.borderColor='var(--border)';this.style.transform=''">
            {{-- Top glow bar --}}
            <div style="height:3px;background:linear-gradient(90deg,{{ $mp['color'] }},transparent);"></div>
            {{-- Header --}}
            <div style="display:flex;align-items:center;gap:12px;padding:16px 18px;border-bottom:1px solid var(--border);">
                <div style="width:42px;height:42px;border-radius:12px;background:{{ $mp['bg'] }};color:{{ $mp['color'] }};border:1px solid {{ $mp['border'] }};display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800;flex:none;">{{ $initial }}</div>
                <div style="min-width:0;flex:1;">
                    <p style="font-weight:700;font-size:14px;color:var(--text);line-height:1.25;">{{ $mod['name'] }}</p>
                    <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-top:2px;">{{ $mod['path'] }}</p>
                </div>
            </div>
            {{-- Core stats --}}
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;border-bottom:1px solid var(--border);">
                <div style="text-align:center;padding:12px 8px;border-right:1px solid var(--border);">
                    <p style="font-size:20px;font-weight:800;color:#FF2D20;font-family:var(--font-sans);">{{ $mod['controllers'] }}</p>
                    <p style="font-size:10px;color:var(--text-faint);font-family:var(--font-mono);margin-top:2px;">Controllers</p>
                </div>
                <div style="text-align:center;padding:12px 8px;border-right:1px solid var(--border);">
                    <p style="font-size:20px;font-weight:800;color:#FF2D20;font-family:var(--font-sans);">{{ $mod['models'] }}</p>
                    <p style="font-size:10px;color:var(--text-faint);font-family:var(--font-mono);margin-top:2px;">Models</p>
                </div>
                <div style="text-align:center;padding:12px 8px;">
                    <p style="font-size:20px;font-weight:800;color:#FF2D20;font-family:var(--font-sans);">{{ $mod['routes'] }}</p>
                    <p style="font-size:10px;color:var(--text-faint);font-family:var(--font-mono);margin-top:2px;">Routes</p>
                </div>
            </div>
            {{-- Extra chips --}}
            <div style="padding:12px 18px;display:flex;flex-wrap:wrap;gap:6px;">
                @if($mod['jobs'] > 0)
                <span style="display:inline-flex;align-items:center;gap:4px;font-size:10px;font-family:var(--font-mono);color:#FF2D20;background:rgba(255,45,32,.08);padding:3px 9px;border-radius:20px;border:1px solid rgba(255,45,32,.2);">
                    <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9" stroke-width="2"/></svg>
                    {{ $mod['jobs'] }} Jobs
                </span>
                @endif
                @if($mod['events'] > 0)
                <span style="display:inline-flex;align-items:center;gap:4px;font-size:10px;font-family:var(--font-mono);color:#FF2D20;background:rgba(255,45,32,.08);padding:3px 9px;border-radius:20px;border:1px solid rgba(255,45,32,.2);">
                    <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    {{ $mod['events'] }} Events
                </span>
                @endif
                @if($mod['services'] > 0)
                <span style="display:inline-flex;align-items:center;gap:4px;font-size:10px;font-family:var(--font-mono);color:#FF2D20;background:rgba(255,45,32,.08);padding:3px 9px;border-radius:20px;border:1px solid rgba(255,45,32,.2);">
                    <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    {{ $mod['services'] }} Services
                </span>
                @endif
                @if(!$hasExtras)
                <span style="font-size:11px;color:var(--text-faint);font-style:italic;">No jobs, events, or services</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif


