@php
$migrations   = $data['migrations'] ?? [];
$totalCreate  = count(array_filter($migrations, fn($m) => $m['operation'] === 'create'));
$totalModify  = count(array_filter($migrations, fn($m) => $m['operation'] === 'modify'));
$totalDrop    = count(array_filter($migrations, fn($m) => $m['operation'] === 'drop'));
$totalFk      = array_sum(array_map(fn($m) => count($m['foreign_keys'] ?? []), $migrations));
$totalColumns = array_sum(array_map(fn($m) => count($m['columns'] ?? []), $migrations));

/* Column type → group */
$typeGroup = function(string $type): string {
    return match(true) {
        in_array($type, ['id','bigIncrements','increments','mediumIncrements','smallIncrements','tinyIncrements'])
            => 'auto',
        in_array($type, ['string','char','text','mediumText','longText','tinyText','enum','set','ipAddress','macAddress'])
            => 'string',
        in_array($type, ['integer','bigInteger','decimal','double','float','mediumInteger','smallInteger','tinyInteger',
                         'unsignedBigInteger','unsignedDecimal','unsignedInteger','unsignedMediumInteger',
                         'unsignedSmallInteger','unsignedTinyInteger','year'])
            => 'number',
        in_array($type, ['date','dateTime','dateTimeTz','time','timeTz','timestamp','timestampTz','timestamps','nullableTimestamps'])
            => 'date',
        $type === 'boolean' => 'bool',
        in_array($type, ['json','jsonb','binary']) => 'json',
        in_array($type, ['uuid','ulid','foreignId','foreignUlid','foreignUuid','ulidMorphs','uuidMorphs']) => 'uuid',
        default => 'other',
    };
};

/* Group label + color — all Laravel red */
$groupMeta = [
    'auto'   => ['label'=>'AUTO',  'color'=>'#FF2D20', 'bg'=>'rgba(255,45,32,.10)', 'border'=>'rgba(255,45,32,.28)'],
    'string' => ['label'=>'STR',   'color'=>'#FF2D20', 'bg'=>'rgba(255,45,32,.10)', 'border'=>'rgba(255,45,32,.28)'],
    'number' => ['label'=>'NUM',   'color'=>'#FF2D20', 'bg'=>'rgba(255,45,32,.10)', 'border'=>'rgba(255,45,32,.28)'],
    'date'   => ['label'=>'DATE',  'color'=>'#FF2D20', 'bg'=>'rgba(255,45,32,.10)', 'border'=>'rgba(255,45,32,.28)'],
    'bool'   => ['label'=>'BOOL',  'color'=>'#FF2D20', 'bg'=>'rgba(255,45,32,.10)', 'border'=>'rgba(255,45,32,.28)'],
    'json'   => ['label'=>'JSON',  'color'=>'#FF2D20', 'bg'=>'rgba(255,45,32,.10)', 'border'=>'rgba(255,45,32,.28)'],
    'uuid'   => ['label'=>'UUID',  'color'=>'#FF2D20', 'bg'=>'rgba(255,45,32,.10)', 'border'=>'rgba(255,45,32,.28)'],
    'other'  => ['label'=>'OTHER', 'color'=>'#FF2D20', 'bg'=>'rgba(255,45,32,.10)', 'border'=>'rgba(255,45,32,.28)'],
];

/* Operation badge colors — CREATE solid red, others outlined red */
$opMeta = [
    'create'  => ['label'=>'CREATE', 'color'=>'#ffffff', 'bg'=>'#FF2D20',             'border'=>'#FF2D20'],
    'modify'  => ['label'=>'MODIFY', 'color'=>'#FF2D20', 'bg'=>'rgba(255,45,32,.10)', 'border'=>'rgba(255,45,32,.35)'],
    'drop'    => ['label'=>'DROP',   'color'=>'#FF2D20', 'bg'=>'rgba(255,45,32,.10)', 'border'=>'rgba(255,45,32,.35)'],
    'rename'  => ['label'=>'RENAME', 'color'=>'#FF2D20', 'bg'=>'rgba(255,45,32,.10)', 'border'=>'rgba(255,45,32,.35)'],
    'unknown' => ['label'=>'UNKNOWN','color'=>'#FF2D20', 'bg'=>'rgba(255,45,32,.07)', 'border'=>'rgba(255,45,32,.2)'],
];
@endphp

<div id="migrations-list">

{{-- ── Stats ─────────────────────────────────────────────────────── --}}
<div class="mds-top-stats">
    <div class="mds-top-stat">
        <span class="mds-top-stat-num" style="color:#FF2D20;">{{ count($migrations) }}</span>
        <span class="mds-top-stat-lbl">Total Migrations</span>
    </div>
    <div class="mds-top-stat">
        <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $totalCreate }}</span>
        <span class="mds-top-stat-lbl">Tables Created</span>
    </div>
    <div class="mds-top-stat">
        <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $totalModify }}</span>
        <span class="mds-top-stat-lbl">Tables Modified</span>
    </div>
    <div class="mds-top-stat">
        <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $totalFk }}</span>
        <span class="mds-top-stat-lbl">Foreign Keys</span>
    </div>
</div>

{{-- ── Toolbar ───────────────────────────────────────────────────── --}}
<div class="mds-toolbar" style="margin-bottom:20px;">
    <div style="position:relative;">
        <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--text-faint);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input id="mg-search" type="search" placeholder="Search table or file…"
            oninput="mgFilter()"
            style="border:1px solid var(--border);border-radius:8px;padding:8px 12px 8px 32px;font-size:12px;width:220px;font-family:var(--font-mono);background:var(--bg-elevated);color:var(--text);">
    </div>
    <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ count($migrations) }} migrations</span>
</div>

{{-- ── Migration List ───────────────────────────────────────────── --}}
@if(empty($migrations))
<div style="text-align:center;padding:60px 20px;color:var(--text-faint);">
    <svg style="width:48px;height:48px;margin:0 auto 16px;display:block;opacity:.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
    </svg>
    <p style="font-size:15px;font-weight:700;margin:0 0 6px;">No migrations found</p>
    <p style="font-size:12px;margin:0;">No migration files detected in <code style="font-family:var(--font-mono);">database/migrations/</code></p>
</div>
@else
<div id="mg-list">
@foreach($migrations as $idx => $mg)
@php
    $op   = $mg['operation'] ?? 'unknown';
    $opm  = $opMeta[$op] ?? $opMeta['unknown'];
    $cols = $mg['columns'] ?? [];
    $fks  = $mg['foreign_keys'] ?? [];
@endphp
<div class="mg-card" data-op="{{ $op }}" data-table="{{ strtolower($mg['table'] ?? '') }}" data-file="{{ strtolower($mg['filename'] ?? '') }}"
     style="cursor:pointer;" onclick="openDetailAjax('migrations', '{{ route('laradar.migration.detail', $mg['filename'] ?? '') }}')">
    {{-- Card Header --}}
    <div class="mg-card-head" style="pointer-events:none;">
        <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:0;">
            {{-- Operation badge --}}
            <span style="font-size:10px;font-weight:700;font-family:var(--font-mono);padding:3px 9px;border-radius:5px;white-space:nowrap;flex:none;color:{{ $opm['color'] }};background:{{ $opm['bg'] }};border:1px solid {{ $opm['border'] }};">
                {{ $opm['label'] }}
            </span>

            {{-- Table name --}}
            @if($mg['table'])
            <span style="font-family:var(--font-mono);font-size:14px;font-weight:700;color:var(--text);white-space:nowrap;">
                {{ $mg['table'] }}
            </span>
            @endif

            {{-- Filename (faint) --}}
            <span style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                {{ $mg['filename'] ?? '' }}
            </span>
        </div>

        <div style="display:flex;align-items:center;gap:8px;flex:none;">
            {{-- Date badge --}}
            @if($mg['date'])
            <span style="font-size:11px;font-family:var(--font-mono);color:var(--text-faint);background:var(--bg-sunken);border:1px solid var(--border);padding:2px 8px;border-radius:5px;">
                {{ $mg['date'] }}
            </span>
            @endif

            {{-- Column count --}}
            @if(count($cols) > 0)
            <span style="font-size:11px;font-family:var(--font-mono);color:#FF2D20;background:rgba(255,45,32,.08);border:1px solid rgba(255,45,32,.2);padding:2px 8px;border-radius:5px;">
                {{ count($cols) }} col{{ count($cols) !== 1 ? 's' : '' }}
            </span>
            @endif

            {{-- FK badge --}}
            @if(count($fks) > 0)
            <span style="font-size:11px;font-family:var(--font-mono);color:#FF2D20;background:rgba(255,45,32,.10);border:1px solid rgba(255,45,32,.35);padding:2px 8px;border-radius:5px;">
                {{ count($fks) }} FK{{ count($fks) !== 1 ? 's' : '' }}
            </span>
            @endif

            {{-- Arrow --}}
            <svg style="width:15px;height:15px;color:var(--text-faint);flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </div>
    </div>

</div>
@endforeach
</div>
@endif

</div>{{-- #migrations-list --}}
<div id="migrations-detail" style="display:none"><div id="migrations-detail-content"></div></div>

{{-- ── Styles ────────────────────────────────────────────────────── --}}

<script>

function mgFilter() {
    const q = (document.getElementById('mg-search')?.value || '').toLowerCase();
    document.querySelectorAll('#mg-list .mg-card').forEach(card => {
        const match = !q || card.dataset.table.includes(q) || card.dataset.file.includes(q);
        card.style.display = match ? '' : 'none';
    });
}
</script>
