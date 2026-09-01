
    @php
    $packages   = $data['packages'] ?? [];
    $byCategory = [];
    foreach ($packages as $pkg) {
        $byCategory[$pkg['category']][] = $pkg;
    }
    ksort($byCategory);

    $devCount = count(array_filter($packages, fn($p) => $p['dev']));

    $categoryMeta = [
        'Admin Panel'       => ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.08)','border'=>'rgba(255,45,32,.25)','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>'],
        'API Authentication'=> ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.08)','border'=>'rgba(255,45,32,.25)','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>'],
        'Architecture'      => ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.08)','border'=>'rgba(255,45,32,.25)','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>'],
        'Audit'             => ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.08)','border'=>'rgba(255,45,32,.25)','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'],
        'Auth Scaffolding'  => ['color'=>'#E879F9','bg'=>'rgba(232,121,249,.1)','border'=>'#E879F9','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
        'Authorization'     => ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.08)','border'=>'rgba(255,45,32,.25)','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'],
        'Backup'            => ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.08)','border'=>'rgba(255,45,32,.25)','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>'],
        'Debug'             => ['color'=>'#94A3B8','bg'=>'rgba(148,163,184,.1)','border'=>'#94A3B8','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>'],
        'Import / Export'   => ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.08)','border'=>'rgba(255,45,32,.25)','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>'],
        'Media'             => ['color'=>'#818CF8','bg'=>'rgba(129,140,248,.1)','border'=>'#818CF8','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>'],
        'Payments'          => ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.08)','border'=>'rgba(255,45,32,.25)','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>'],
        'PDF'               => ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.08)','border'=>'rgba(255,45,32,.25)','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>'],
        'Queue Monitoring'  => ['color'=>'#2DD4BF','bg'=>'rgba(45,212,191,.1)','border'=>'#2DD4BF','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'],
        'Search'            => ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.08)','border'=>'rgba(255,45,32,.25)','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>'],
        'UI Framework'      => ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.08)','border'=>'rgba(255,45,32,.25)','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>'],
    ];
    $defaultCatMeta = ['color'=>'#94A3B8','bg'=>'rgba(148,163,184,.1)','border'=>'#94A3B8','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>'];

    $dotHexColors = [
        'pink'=>'#F472B6','purple'=>'#C084FC','red'=>'#F87171','blue'=>'#60A5FA',
        'orange'=>'#FB923C','violet'=>'#A78BFA','amber'=>'#FBBF24',
        'sky'=>'#38BDF8','emerald'=>'#34D399','green'=>'#4ADE80',
        'teal'=>'#2DD4BF','slate'=>'#94A3B8','cyan'=>'#818CF8','indigo'=>'#818CF8',
        'rose'=>'#FB7185',
    ];
    @endphp

    @if(empty($packages))
    <div class="atlas-card" style="text-align:center;padding:64px;">
        <div style="width:56px;height:56px;background:var(--bg-hover);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg style="width:28px;height:28px;color:var(--text-faint);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
        </div>
        <p style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:6px;">No known packages detected</p>
        <p style="font-size:13px;color:var(--text-faint);">None of the tracked packages appear in your <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">composer.json</code>.</p>
    </div>
    @else

    {{-- Stats --}}
    <div class="mds-top-stats">
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ count($packages) }}</span>
            <span class="mds-top-stat-lbl">Total Packages</span>
        </div>
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ count($byCategory) }}</span>
            <span class="mds-top-stat-lbl">Categories</span>
        </div>
        <div class="mds-top-stat">
            <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $devCount }}</span>
            <span class="mds-top-stat-lbl">Dev Only</span>
        </div>
    </div>
    <div class="mds-toolbar">
        <input id="packages-search" oninput="filterPackages()" type="search" placeholder="Search packages…" style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;flex:1;max-width:240px;font-family:var(--font-mono);">
        <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ count($packages) }} packages</span>
    </div>

    {{-- Categories --}}
    <div id="packages-categories">
    @php $globalPkgIdx = 0; @endphp
    @foreach($byCategory as $category => $pkgs)
    @php $catMeta = $categoryMeta[$category] ?? $defaultCatMeta; @endphp
    <div style="margin-bottom:32px;">

        {{-- Category Header --}}
        <div class="pkg-cat-header" style="background:{{ $catMeta['bg'] }};border-left-color:{{ $catMeta['border'] }};">
            <div style="width:32px;height:32px;border-radius:8px;background:{{ $catMeta['bg'] }};border:1px solid {{ $catMeta['border'] }}40;display:flex;align-items:center;justify-content:center;flex:none;">
                <svg style="width:16px;height:16px;color:{{ $catMeta['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $catMeta['icon'] !!}</svg>
            </div>
            <span style="font-weight:700;font-size:13px;color:{{ $catMeta['color'] }};flex:1;">{{ $category }}</span>
            <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:{{ $catMeta['color'] }}22;color:{{ $catMeta['color'] }};border:1px solid {{ $catMeta['color'] }}44;">{{ count($pkgs) }} pkg{{ count($pkgs) !== 1 ? 's' : '' }}</span>
        </div>

        {{-- Package list --}}
        <div style="background:var(--bg-elevated);border-radius:12px;border:1px solid var(--border);overflow:hidden;">
            <div class="mds-list-head" style="grid-template-columns:40px 1fr 180px 90px 90px;">
                <span></span><span>Package</span><span>Composer Key</span><span>Version</span><span>Docs</span>
            </div>
            @foreach($pkgs as $pkg)
            @php
                $dotHex   = $dotHexColors[$pkg['color']] ?? '#94A3B8';
                $initials = strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $pkg['name']), 0, 2))));
                $hasDoc   = !empty($pkg['docs']);
            @endphp
            <div class="mds-list-row pkg-card" style="--pkg-i:{{ $globalPkgIdx++ }};grid-template-columns:40px 1fr 180px 90px 90px;" data-name="{{ strtolower($pkg['name'] . ' ' . $pkg['key']) }}">
                <div class="mds-list-av" style="background:{{ $dotHex }}18;color:{{ $dotHex }};border-color:{{ $dotHex }}44;font-size:11px;">{{ $initials }}</div>
                <div style="min-width:0;">
                    <div style="font-weight:700;font-size:13.5px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $pkg['name'] }}</div>
                    <div style="font-size:11px;color:var(--text-dim);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $pkg['description'] }}</div>
                </div>
                <div style="display:flex;align-items:center;gap:6px;min-width:0;">
                    <span style="font-family:var(--font-mono);font-size:10.5px;color:var(--text-faint);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $pkg['key'] }}</span>
                    <button onclick="copyPkgKey(this,'{{ $pkg['key'] }}')" title="Copy" style="flex:none;background:transparent;border:none;color:var(--text-faint);cursor:pointer;padding:2px;border-radius:4px;display:flex;" onmouseenter="this.style.color='var(--text)'" onmouseleave="this.style.color='var(--text-faint)'">
                        <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:4px;align-items:center;">
                    @if($pkg['version'])<span style="font-family:var(--font-mono);font-size:10px;background:{{ $dotHex }}15;color:{{ $dotHex }};padding:2px 7px;border-radius:5px;border:1px solid {{ $dotHex }}33;font-weight:600;">v{{ $pkg['version'] }}</span>@endif
                    @if($pkg['dev'])<span style="font-family:var(--font-mono);font-size:10px;color:#FF2D20;background:rgba(255,45,32,.08);padding:2px 7px;border-radius:5px;border:1px solid rgba(255,45,32,.2);font-weight:600;">dev</span>@endif
                </div>
                <div>
                    @if($hasDoc)
                    <a href="{{ $pkg['docs'] }}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;color:{{ $dotHex }};background:{{ $dotHex }}10;border:1px solid {{ $dotHex }}33;border-radius:6px;padding:3px 10px;text-decoration:none;" onmouseenter="this.style.background='{{ $dotHex }}22'" onmouseleave="this.style.background='{{ $dotHex }}10'">
                        Docs <svg style="width:10px;height:10px;opacity:.7;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    @else
                    <span style="font-size:12px;color:var(--text-faint);">—</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
    </div>{{-- /packages-categories --}}

    @endif


