@extends('laradar::layouts.laradar')
@section('content')
@php
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

$mg        = $migrationData;
$filename  = $mg['filename'] ?? '';
$table     = $mg['table'] ?? null;
$operation = $mg['operation'] ?? 'unknown';
$date      = $mg['date'] ?? null;
$columns   = $mg['columns'] ?? [];
$fks       = $mg['foreign_keys'] ?? [];
$colCount  = count($columns);
$fkCount   = count($fks);

// Human-readable title: strip date+time prefix
$humanName = preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', $filename);

// DB run info (batch, ran_at)
$batch  = null;
$ranAt  = null;
$hasRun = false;
try {
    $record = DB::table('migrations')->where('migration', $filename)->first();
    if ($record) {
        $hasRun = true;
        $batch  = $record->batch ?? null;
        $ranAt  = null; // migrations table has no timestamp column by default
    }
} catch (\Throwable) {}

// DB connection name
$connection = config('database.default', 'mysql');

// Related model: infer from table name
$relatedModel = null;
$modelSingular = Str::studly(Str::singular($table ?? ''));
foreach ($data['models'] ?? [] as $m) {
    if (strtolower($m['name']) === strtolower($modelSingular)) {
        $relatedModel = $m['name'];
        break;
    }
}

// Column key detection
$primaryCols = [];
$uniqueCols  = [];
$indexCols   = [];
$foreignCols = array_column($fks, 'column');
$autoIdTypes = ['id','bigIncrements','increments','mediumIncrements','smallIncrements','tinyIncrements'];
foreach ($columns as $col) {
    $mods = $col['modifiers'] ?? [];
    if (in_array('primary', $mods) || in_array($col['type'], $autoIdTypes)) {
        $primaryCols[] = $col['name'];
    }
    if (in_array('unique', $mods))  $uniqueCols[] = $col['name'];
    if (in_array('index', $mods))   $indexCols[]  = $col['name'];
}

// Default value extraction from modifiers
$defaultMap = [];
foreach ($columns as $col) {
    foreach ($col['modifiers'] ?? [] as $mod) {
        if (str_starts_with($mod, 'default:')) {
            $defaultMap[$col['name']] = substr($mod, 8);
        }
    }
}

$isReversible = $operation === 'create'; // only create is guaranteed reversible via Schema::drop()

$opMeta = [
    'create'  => ['label' => 'CREATE', 'color' => '#fff',    'bg' => '#FF2D20',             'border' => '#FF2D20'],
    'modify'  => ['label' => 'MODIFY', 'color' => '#FF2D20', 'bg' => 'rgba(255,45,32,.10)', 'border' => 'rgba(255,45,32,.35)'],
    'drop'    => ['label' => 'DROP',   'color' => '#FF2D20', 'bg' => 'rgba(255,45,32,.10)', 'border' => 'rgba(255,45,32,.35)'],
    'rename'  => ['label' => 'RENAME', 'color' => '#FF2D20', 'bg' => 'rgba(255,45,32,.10)', 'border' => 'rgba(255,45,32,.35)'],
    'unknown' => ['label' => 'UNKNOWN','color' => '#FF2D20', 'bg' => 'rgba(255,45,32,.07)', 'border' => 'rgba(255,45,32,.2)'],
];
$opm = $opMeta[$operation] ?? $opMeta['unknown'];

// Type group labels
$typeGroup = function(string $type): array {
    return match(true) {
        in_array($type, ['id','bigIncrements','increments','mediumIncrements','smallIncrements','tinyIncrements'])
            => ['AUTO', '#FF2D20', 'rgba(255,45,32,.10)', 'rgba(255,45,32,.28)'],
        in_array($type, ['string','char','text','mediumText','longText','tinyText','enum','set','ipAddress','macAddress'])
            => ['STR',  '#FF2D20', 'rgba(255,45,32,.10)', 'rgba(255,45,32,.28)'],
        in_array($type, ['integer','bigInteger','decimal','double','float','mediumInteger','smallInteger','tinyInteger',
                         'unsignedBigInteger','unsignedDecimal','unsignedInteger','unsignedMediumInteger',
                         'unsignedSmallInteger','unsignedTinyInteger','year'])
            => ['NUM',  '#FF2D20', 'rgba(255,45,32,.10)', 'rgba(255,45,32,.28)'],
        in_array($type, ['date','dateTime','dateTimeTz','time','timeTz','timestamp','timestampTz','timestamps','nullableTimestamps'])
            => ['DATE', '#FF2D20', 'rgba(255,45,32,.10)', 'rgba(255,45,32,.28)'],
        $type === 'boolean'
            => ['BOOL', '#FF2D20', 'rgba(255,45,32,.10)', 'rgba(255,45,32,.28)'],
        in_array($type, ['json','jsonb','binary'])
            => ['JSON', '#FF2D20', 'rgba(255,45,32,.10)', 'rgba(255,45,32,.28)'],
        in_array($type, ['uuid','ulid','foreignId','foreignUlid','foreignUuid','ulidMorphs','uuidMorphs'])
            => ['UUID', '#FF2D20', 'rgba(255,45,32,.10)', 'rgba(255,45,32,.28)'],
        default
            => ['OTHER','#FF2D20', 'rgba(255,45,32,.10)', 'rgba(255,45,32,.28)'],
    };
};
@endphp

<div class="detail-page">

{{-- Header --}}
<div class="detail-header reveal">
    <div class="detail-header-top">
        <div class="detail-header-left">
            <div class="detail-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <div style="min-width:0">
                <div class="detail-title-row">
                    <h1 class="detail-title">{{ $humanName }}</h1>
                    <span style="font-size:10px;font-weight:700;font-family:var(--font-mono);padding:3px 9px;border-radius:5px;white-space:nowrap;color:{{ $opm['color'] }};background:{{ $opm['bg'] }};border:1px solid {{ $opm['border'] }};">{{ $opm['label'] }}</span>
                    @if($hasRun)
                    <span style="font-size:10px;font-weight:700;font-family:var(--font-mono);padding:3px 9px;border-radius:5px;color:#FF2D20;background:rgba(255,45,32,.08);border:1px solid rgba(255,45,32,.2);">BATCH {{ $batch }}</span>
                    @endif
                </div>
                <span class="detail-ns">{{ $filename }}</span>
            </div>
        </div>
        <div class="detail-stats">
            <div class="detail-stat"><span class="detail-stat-val">{{ $colCount }}</span><span class="detail-stat-lbl">Columns</span></div>
            <div class="detail-stat"><span class="detail-stat-val">{{ $fkCount }}</span><span class="detail-stat-lbl">Foreign Keys</span></div>
            @if($table)
            <div class="detail-stat"><span class="detail-stat-val" style="font-size:14px;padding-top:3px;">{{ $table }}</span><span class="detail-stat-lbl">Table</span></div>
            @endif
        </div>
    </div>
</div>

{{-- Glance chips --}}
<div class="detail-glance reveal">
    <div class="detail-chip">
        <div style="color:#FF2D20;flex-shrink:0;margin-top:1px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </div>
        <div>
            <div class="detail-chip-lbl">Operation</div>
            <div class="detail-chip-val" style="color:#FF2D20;">{{ ucfirst($operation) }}</div>
        </div>
    </div>
    <div class="detail-chip">
        <div style="color:{{ $hasRun ? '#FF2D20' : 'var(--text-faint)' }};flex-shrink:0;margin-top:1px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="detail-chip-lbl">Status</div>
            <div class="detail-chip-val" style="color:{{ $hasRun ? '#FF2D20' : 'var(--text-faint)' }};">{{ $hasRun ? 'Ran' : 'Pending' }}</div>
        </div>
    </div>
    <div class="detail-chip">
        <div style="color:{{ $isReversible ? '#FF2D20' : 'var(--text-faint)' }};flex-shrink:0;margin-top:1px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
        </div>
        <div>
            <div class="detail-chip-lbl">Reversible</div>
            <div class="detail-chip-val" style="color:{{ $isReversible ? '#FF2D20' : 'var(--text-faint)' }};">{{ $isReversible ? 'Yes' : 'No' }}</div>
        </div>
    </div>
    <div class="detail-chip">
        <div style="color:{{ $relatedModel ? '#FF2D20' : 'var(--text-faint)' }};flex-shrink:0;margin-top:1px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14c0 1.657 4.03 3 9 3s9-1.343 9-3V5"/><path d="M3 12c0 1.657 4.03 3 9 3s9-1.343 9-3"/></svg>
        </div>
        <div>
            <div class="detail-chip-lbl">Model</div>
            <div class="detail-chip-val" style="color:{{ $relatedModel ? '#FF2D20' : 'var(--text-faint)' }};">{{ $relatedModel ?? 'Unknown' }}</div>
        </div>
    </div>
</div>

{{-- Body --}}
<div class="detail-body">

    {{-- Main --}}
    <div class="detail-main">

        {{-- Details --}}
        <div class="detail-card reveal">
            <div class="detail-card-head">
                <p class="detail-card-title">Migration Details</p>
            </div>
            <div class="mgd-details-grid">
                @if($table)
                <span class="mgd-det-lbl">Table</span>
                <span class="mgd-det-val mgd-det-mono">{{ $table }}</span>
                @endif

                <span class="mgd-det-lbl">Operation</span>
                <span class="mgd-det-val">{{ ucfirst($operation) }}</span>

                <span class="mgd-det-lbl">Connection</span>
                <span class="mgd-det-val mgd-det-mono">{{ $connection }}</span>

                @if($date)
                <span class="mgd-det-lbl">Created</span>
                <span class="mgd-det-val mgd-det-mono">{{ $date }}</span>
                @endif

                <span class="mgd-det-lbl">Status</span>
                <span class="mgd-det-val">
                    @if($hasRun)
                        <span style="color:#FF2D20;font-weight:600;">Ran</span>
                        @if($batch) <span style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);margin-left:6px;">batch {{ $batch }}</span> @endif
                    @else
                        <span style="color:var(--text-faint);">Pending</span>
                    @endif
                </span>

                <span class="mgd-det-lbl">Reversible</span>
                <span class="mgd-det-val" style="color:{{ $isReversible ? '#FF2D20' : 'var(--text-faint)' }};">{{ $isReversible ? 'Yes' : 'No' }}</span>
            </div>
        </div>

        {{-- Schema --}}
        @if(!empty($columns))
        <div class="detail-card reveal">
            <div class="detail-card-head">
                <p class="detail-card-title">Schema{{ $table ? ' · ' . $table : '' }}</p>
                <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);">{{ $colCount }} column{{ $colCount !== 1 ? 's' : '' }}</span>
            </div>
            <div style="overflow-x:auto">
                <table class="mgd-table">
                    <thead>
                        <tr>
                            <th class="mgd-th">#</th>
                            <th class="mgd-th">Column</th>
                            <th class="mgd-th">Type</th>
                            <th class="mgd-th">Nullable</th>
                            <th class="mgd-th">Default</th>
                            <th class="mgd-th">Key</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($columns as $ci => $col)
                        @php
                            [$tLabel, $tColor, $tBg, $tBorder] = $typeGroup($col['type']);
                            $isPrimary = in_array($col['name'], $primaryCols) || in_array($col['type'], $autoIdTypes);
                            $isUnique  = in_array($col['name'], $uniqueCols);
                            $isIndex   = in_array($col['name'], $indexCols);
                            $isFk      = in_array($col['name'], $foreignCols);
                            $default   = $defaultMap[$col['name']] ?? null;
                        @endphp
                        <tr class="mgd-tr">
                            <td class="mgd-td" style="color:var(--text-faint);font-size:11px;font-family:var(--font-mono);width:30px;">{{ $ci + 1 }}</td>
                            <td class="mgd-td">
                                <span class="mgd-col-name">{{ $col['name'] }}</span>
                            </td>
                            <td class="mgd-td">
                                <code class="mgd-type-tag">{{ $col['type'] }}</code>
                                <span class="detail-badge" style="margin-left:4px;color:{{ $tColor }};background:{{ $tBg }};border-color:{{ $tBorder }};">{{ $tLabel }}</span>
                                @if($col['type'] === 'enum' && !empty($col['enum_values']))
                                <div style="margin-top:4px;display:flex;flex-wrap:wrap;gap:3px;">
                                    @foreach($col['enum_values'] as $ev)
                                    <span style="font-family:var(--font-mono);font-size:9px;padding:1px 5px;border-radius:3px;background:var(--bg-sunken);border:1px solid var(--border);color:var(--text-dim);">{{ $ev }}</span>
                                    @endforeach
                                </div>
                                @endif
                            </td>
                            <td class="mgd-td" style="font-size:12px;">
                                @if($col['nullable'])
                                    <span style="color:#FF2D20;">Yes</span>
                                @else
                                    <span style="color:var(--text-faint);">No</span>
                                @endif
                            </td>
                            <td class="mgd-td" style="font-family:var(--font-mono);font-size:11px;color:var(--text-dim);">
                                {{ $default ?? '—' }}
                            </td>
                            <td class="mgd-td">
                                @if($isPrimary)
                                    <span class="detail-badge" style="color:#FF2D20;background:rgba(255,45,32,.08);border-color:rgba(255,45,32,.2);">PRIMARY</span>
                                @endif
                                @if($isUnique)
                                    <span class="detail-badge" style="color:#FF2D20;background:rgba(255,45,32,.08);border-color:rgba(255,45,32,.2);margin-left:2px;">UNIQUE</span>
                                @endif
                                @if($isFk)
                                    <span class="detail-badge" style="color:#FF2D20;background:rgba(255,45,32,.08);border-color:rgba(255,45,32,.2);margin-left:2px;">FOREIGN</span>
                                @endif
                                @if($isIndex && !$isPrimary && !$isUnique)
                                    <span class="detail-badge" style="color:var(--text-dim);background:var(--bg-sunken);border-color:var(--border);margin-left:2px;">INDEX</span>
                                @endif
                                @if(!$isPrimary && !$isUnique && !$isFk && !$isIndex)
                                    <span style="color:var(--text-faint);font-size:11px;">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Foreign Keys & Indexes --}}
        @if(!empty($fks) || !empty($uniqueCols) || !empty($primaryCols))
        <div class="detail-card reveal">
            <div class="detail-card-head">
                <p class="detail-card-title">Indexes &amp; Foreign Keys</p>
            </div>
            <div style="padding:14px 16px;display:flex;flex-wrap:wrap;gap:6px;">
                @foreach($primaryCols as $pk)
                <div class="mgd-fk-item">
                    <span class="detail-badge" style="color:#FF2D20;background:rgba(255,45,32,.08);border-color:rgba(255,45,32,.2);">PRIMARY</span>
                    <span style="color:var(--text);font-weight:600;">{{ $pk }}</span>
                </div>
                @endforeach
                @foreach($uniqueCols as $uq)
                <div class="mgd-fk-item">
                    <span class="detail-badge" style="color:#FF2D20;background:rgba(255,45,32,.08);border-color:rgba(255,45,32,.2);">UNIQUE</span>
                    <span style="color:var(--text);font-weight:600;">{{ $uq }}</span>
                </div>
                @endforeach
                @foreach($fks as $fk)
                <div class="mgd-fk-item">
                    <span class="detail-badge" style="color:#FF2D20;background:rgba(255,45,32,.08);border-color:rgba(255,45,32,.2);">FOREIGN</span>
                    <span style="color:var(--text);font-weight:600;">{{ $fk['column'] }}</span>
                    <svg width="11" height="11" fill="none" stroke="#FF2D20" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    <span style="color:#FF2D20;">{{ $fk['on'] }}.{{ $fk['references'] }}</span>
                    @if(!empty($fk['on_delete']))
                    <span style="font-family:var(--font-mono);font-size:9px;padding:1px 5px;border-radius:3px;background:rgba(255,45,32,.06);border:1px solid rgba(255,45,32,.18);color:#ff2d20;">ON DELETE {{ strtoupper($fk['on_delete']) }}</span>
                    @endif
                    @if(!empty($fk['on_update']))
                    <span style="font-family:var(--font-mono);font-size:9px;padding:1px 5px;border-radius:3px;background:var(--bg-sunken);border:1px solid var(--border);color:var(--text-dim);">ON UPDATE {{ strtoupper($fk['on_update']) }}</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($operation === 'drop' || $operation === 'rename')
        <div class="detail-card reveal">
            <div style="padding:20px;font-size:13px;color:var(--text-faint);font-family:var(--font-mono);">
                {{ $operation === 'drop' ? 'This migration drops the table — no column schema available.' : 'This migration renames the table — no column schema available.' }}
            </div>
        </div>
        @endif

        @if(empty($columns) && !in_array($operation, ['drop', 'rename']))
        <div class="detail-card reveal">
            <div style="padding:20px;font-size:13px;color:var(--text-faint);font-family:var(--font-mono);">
                No column definitions detected (raw SQL or complex Schema builder).
            </div>
        </div>
        @endif

    </div>{{-- .detail-main --}}

    {{-- Sidebar --}}
    <div class="detail-sidebar">

        {{-- Quick Actions --}}
        <div class="detail-scard reveal">
            <div class="detail-scard-head"><p class="detail-scard-title">Quick Actions</p></div>
            <div class="detail-scard-body">
                @if($relatedModel)
                <a href="{{ route('laradar.model.detail', $relatedModel) }}" class="detail-act"
                   onclick="event.preventDefault(); openDetailAjax('models', this.href)">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14c0 1.657 4.03 3 9 3s9-1.343 9-3V5"/><path d="M3 12c0 1.657 4.03 3 9 3s9-1.343 9-3"/></svg>
                    View {{ $relatedModel }} model
                </a>
                @endif
                <a href="{{ route('laradar.migrations') }}" class="detail-act"
                   onclick="event.preventDefault(); showList('migrations', true)">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    All migrations
                </a>
            </div>
        </div>

        {{-- Info --}}
        <div class="detail-scard reveal">
            <div class="detail-scard-head"><p class="detail-scard-title">Info</p></div>
            <div class="detail-scard-body">
                @if($table)
                <div class="detail-ir">
                    <span class="detail-il">Table</span>
                    <span class="detail-iv" style="font-family:var(--font-mono);font-size:11px;">{{ $table }}</span>
                </div>
                @endif
                <div class="detail-ir">
                    <span class="detail-il">Columns</span>
                    <span class="detail-iv" style="font-weight:600;color:#FF2D20;">{{ $colCount }}</span>
                </div>
                @if($fkCount > 0)
                <div class="detail-ir">
                    <span class="detail-il">Foreign Keys</span>
                    <span class="detail-iv" style="font-weight:600;color:#FF2D20;">{{ $fkCount }}</span>
                </div>
                @endif
                @if($date)
                <div class="detail-ir">
                    <span class="detail-il">Date</span>
                    <span class="detail-iv" style="font-family:var(--font-mono);font-size:11px;">{{ $date }}</span>
                </div>
                @endif
                @if($hasRun && $batch)
                <div class="detail-ir">
                    <span class="detail-il">Batch</span>
                    <span class="detail-iv" style="font-weight:600;color:#FF2D20;">{{ $batch }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Foreign Key Targets --}}
        @if(!empty($fks))
        <div class="detail-scard reveal">
            <div class="detail-scard-head"><p class="detail-scard-title">References</p></div>
            <div class="detail-scard-body">
                @foreach($fks as $fk)
                <div class="detail-ir" style="flex-direction:column;align-items:flex-start;gap:3px;">
                    <div style="display:flex;justify-content:space-between;width:100%;">
                        <span class="detail-il">{{ $fk['column'] }}</span>
                        <span class="detail-iv" style="font-family:var(--font-mono);font-size:11px;color:#FF2D20;">{{ $fk['on'] }}.{{ $fk['references'] }}</span>
                    </div>
                    @if(!empty($fk['on_delete']) || !empty($fk['on_update']))
                    <div style="display:flex;gap:4px;flex-wrap:wrap;">
                        @if(!empty($fk['on_delete']))
                        <span style="font-family:var(--font-mono);font-size:9px;padding:1px 5px;border-radius:3px;background:rgba(255,45,32,.06);border:1px solid rgba(255,45,32,.18);color:#ff2d20;">DEL {{ strtoupper($fk['on_delete']) }}</span>
                        @endif
                        @if(!empty($fk['on_update']))
                        <span style="font-family:var(--font-mono);font-size:9px;padding:1px 5px;border-radius:3px;background:var(--bg-sunken);border:1px solid var(--border);color:var(--text-dim);">UPD {{ strtoupper($fk['on_update']) }}</span>
                        @endif
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>{{-- .detail-sidebar --}}

</div>{{-- .detail-body --}}

</div>{{-- .detail-page --}}
@endsection
