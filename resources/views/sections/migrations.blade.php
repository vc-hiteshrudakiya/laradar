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
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
    </svg>
    <p style="font-size:14px;font-weight:600;margin:0 0 6px;">No migrations found</p>
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
<div class="mg-card" data-op="{{ $op }}" data-table="{{ strtolower($mg['table'] ?? '') }}" data-file="{{ strtolower($mg['filename'] ?? '') }}">
    {{-- Card Header --}}
    <div class="mg-card-head" onclick="mgToggle({{ $idx }})">
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

            {{-- Chevron --}}
            <svg id="mg-chevron-{{ $idx }}" style="width:16px;height:16px;color:var(--text-faint);transition:transform .2s;flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </div>

    {{-- Card Body (collapsed by default) --}}
    <div id="mg-body-{{ $idx }}" style="display:none;">
        @if($op === 'drop' || $op === 'rename')
        <div style="padding:16px 20px;font-size:13px;color:var(--text-faint);font-family:var(--font-mono);">
            {{ $op === 'drop' ? 'Drops the table — no column schema.' : 'Renames the table — no column schema.' }}
        </div>
        @elseif(empty($cols))
        <div style="padding:16px 20px;font-size:13px;color:var(--text-faint);font-family:var(--font-mono);">
            No column definitions detected (raw SQL or complex builder).
        </div>
        @else

        {{-- Columns Table --}}
        <div style="overflow-x:auto;padding:0 0 4px;">
            <table class="mg-col-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Column</th>
                        <th>Type</th>
                        <th>Group</th>
                        <th>Nullable</th>
                        <th>Modifiers</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($cols as $ci => $col)
                @php
                    $grp = $typeGroup($col['type']);
                    $gm  = $groupMeta[$grp] ?? $groupMeta['other'];
                @endphp
                <tr>
                    <td style="color:var(--text-faint);font-size:11px;">{{ $ci + 1 }}</td>
                    <td>
                        <span class="mg-col-name">{{ $col['name'] }}</span>
                        @if(in_array('primary', $col['modifiers'] ?? []) || $col['type'] === 'id' || str_contains($col['type'], 'Increments'))
                        <span class="mg-badge" style="color:#FF2D20;background:rgba(255,45,32,.08);border-color:rgba(255,45,32,.2);">PK</span>
                        @endif
                    </td>
                    <td>
                        <code class="mg-type-tag">{{ $col['type'] }}</code>
                    </td>
                    <td>
                        <span class="mg-badge" style="color:{{ $gm['color'] }};background:{{ $gm['bg'] }};border-color:{{ $gm['border'] }};">{{ $gm['label'] }}</span>
                    </td>
                    <td>
                        @if($col['nullable'])
                        <span style="display:inline-flex;align-items:center;gap:4px;font-size:11px;color:#FF2D20;">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            yes
                        </span>
                        @else
                        <span style="font-size:11px;color:var(--text-faint);">—</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $displayMods = array_filter($col['modifiers'] ?? [], fn($mod) => $mod !== 'nullable');
                        @endphp
                        @foreach($displayMods as $mod)
                        <span class="mg-badge" style="color:var(--text-dim);background:var(--bg-sunken);border-color:var(--border);">{{ $mod }}</span>
                        @endforeach
                        @if(empty($displayMods)) <span style="font-size:11px;color:var(--text-faint);">—</span> @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Foreign Keys --}}
        @if(count($fks) > 0)
        <div style="padding:12px 20px 16px;border-top:1px solid var(--border);">
            <p style="font-size:11px;font-weight:700;font-family:var(--font-mono);color:var(--text-faint);text-transform:uppercase;letter-spacing:.05em;margin:0 0 10px;">Foreign Keys</p>
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                @foreach($fks as $fk)
                <div style="display:inline-flex;align-items:center;gap:6px;font-family:var(--font-mono);font-size:12px;background:var(--bg-sunken);border:1px solid var(--border);border-radius:8px;padding:6px 12px;">
                    <span style="color:var(--text);font-weight:600;">{{ $fk['column'] }}</span>
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#FF2D20;flex:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    <span style="color:#FF2D20;">{{ $fk['on'] }}.{{ $fk['references'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endforeach
</div>
@endif

{{-- ── Styles ────────────────────────────────────────────────────── --}}
<style>

.mg-card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;margin-bottom:10px;overflow:hidden;transition:border-color .2s,box-shadow .2s;}
.mg-card:hover{border-color:var(--border-strong);box-shadow:var(--shadow-hover);}

.mg-card-head{display:flex;align-items:center;gap:12px;padding:14px 18px;cursor:pointer;user-select:none;}
.mg-card-head:hover{background:var(--bg-sunken);}

.mg-col-table{width:100%;border-collapse:collapse;font-size:12px;}
.mg-col-table th{font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-faint);padding:10px 16px;border-bottom:1px solid var(--border);text-align:left;white-space:nowrap;}
.mg-col-table td{padding:9px 16px;border-bottom:1px solid var(--border);vertical-align:middle;}
.mg-col-table tbody tr:last-child td{border-bottom:none;}
.mg-col-table tbody tr:hover td{background:var(--bg-sunken);}

.mg-col-name{font-family:var(--font-mono);font-size:13px;font-weight:600;color:var(--text);}
.mg-type-tag{font-family:var(--font-mono);font-size:11px;color:#FF2D20;background:rgba(255,45,32,.07);padding:2px 7px;border-radius:4px;border:1px solid rgba(255,45,32,.2);}
.mg-badge{font-size:10px;font-weight:700;font-family:var(--font-mono);padding:2px 7px;border-radius:4px;border:1px solid;margin-left:4px;white-space:nowrap;}
</style>

<script>
function mgToggle(idx) {
    const body    = document.getElementById('mg-body-' + idx);
    const chevron = document.getElementById('mg-chevron-' + idx);
    const open    = body.style.display !== 'none';
    body.style.display      = open ? 'none' : 'block';
    chevron.style.transform = open ? '' : 'rotate(180deg)';
}

function mgFilter() {
    const q = (document.getElementById('mg-search')?.value || '').toLowerCase();
    document.querySelectorAll('#mg-list .mg-card').forEach(card => {
        const match = !q || card.dataset.table.includes(q) || card.dataset.file.includes(q);
        card.style.display = match ? '' : 'none';
    });
}
</script>
