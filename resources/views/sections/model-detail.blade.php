@extends('laradar::layouts.laradar')
@section('content')
@php
$p = ['color'=>'#FF2D20', 'rgb'=>'255,45,32'];

$relColors = [
    'hasMany'       => ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.28)'],
    'hasOne'        => ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.28)'],
    'belongsTo'     => ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.28)'],
    'belongsToMany' => ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.28)'],
    'morphMany'     => ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.28)'],
    'morphTo'       => ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.28)'],
    'morphOne'      => ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.28)'],
    'hasManyThrough'=> ['color'=>'#FF2D20','bg'=>'rgba(255,45,32,.10)','border'=>'rgba(255,45,32,.28)'],
];

// Column types from migration
$columnTypes = [];
$modelTable  = $model['table'] ?? null;
if ($modelTable) {
    foreach ($data['migrations'] ?? [] as $migration) {
        if (($migration['table'] ?? '') === $modelTable) {
            foreach ($migration['columns'] ?? [] as $col) {
                $columnTypes[$col['name']] = $col['type'];
            }
        }
    }
}

// Field map
$fieldMap = [];
foreach ($model['fillable'] ?? [] as $f) {
    $fieldMap[$f] = ['fillable' => true, 'hidden' => false, 'cast' => null, 'db_type' => $columnTypes[$f] ?? null];
}
foreach ($model['hidden'] ?? [] as $f) {
    if (!isset($fieldMap[$f])) $fieldMap[$f] = ['fillable' => false, 'hidden' => false, 'cast' => null, 'db_type' => $columnTypes[$f] ?? null];
    $fieldMap[$f]['hidden'] = true;
}
foreach ($model['casts'] ?? [] as $f => $type) {
    if (!isset($fieldMap[$f])) $fieldMap[$f] = ['fillable' => false, 'hidden' => false, 'cast' => null, 'db_type' => $columnTypes[$f] ?? null];
    $fieldMap[$f]['cast'] = $type;
}

$fillCnt   = count($model['fillable'] ?? []);
$hideCnt   = count($model['hidden'] ?? []);
$castCnt   = count($model['casts'] ?? []);
$relCnt    = count($model['relationships'] ?? []);
$traits    = array_map(fn($t) => class_basename($t), $model['traits'] ?? []);
$scopes    = $model['scopes'] ?? [];
$accessors = $model['accessors'] ?? [];
$mutators  = $model['mutators'] ?? [];

// Model index map for relationship navigation
$modelIndexMap = [];
foreach ($data['models'] as $mi => $m) {
    $modelIndexMap[$m['name']] = $mi;
}

// Controllers that use this model
$usedBy = [];
foreach ($data['dependencies']['edges'] ?? [] as $edge) {
    if ($edge['to'] === $model['name'] || $edge['to'] === class_basename($model['name'])) {
        $ctrl = collect($data['controllers'])->firstWhere('name', $edge['from']);
        if ($ctrl && !collect($usedBy)->firstWhere('name', $ctrl['name'])) {
            $routeCount = count(array_filter($data['routes'], fn($r) => str_contains($r['controller']['class'] ?? '', $ctrl['name'])));
            $usedBy[] = ['name' => $ctrl['name'], 'routes' => $routeCount];
        }
    }
}

// Derived flags
$hasSoftDeletes = collect($traits)->contains(fn($t) => str_contains($t, 'SoftDeletes'));
$hasTimestamps  = $model['timestamps'] !== false;

// Extra model metadata
$primaryKey   = $model['primary_key'] ?? 'id';
$keyType      = $model['key_type'] ?? 'int';
$incrementing = $model['incrementing'] ?? true;
$connection   = $model['connection'] ?? null;
$appends      = $model['appends'] ?? [];
$eagerLoads   = $model['with'] ?? [];
$observerRef  = $model['observer'] ?? null;
$relatedPolicy = collect($data['policies'] ?? [])->first(fn($pol) => strtolower($pol['model'] ?? '') === strtolower($model['name']));
$observerData  = $observerRef
    ? collect($data['observers'] ?? [])->first(fn($o) => strtolower($o['name']) === strtolower($observerRef))
    : null;

// Related migration (create operation for this table)
$relatedMigration = collect($data['migrations'] ?? [])
    ->filter(fn($m) => ($m['table'] ?? '') === ($model['table'] ?? '') && ($m['operation'] ?? '') === 'create')
    ->first();

// Route info for first controller
$firstCtrl = $usedBy[0] ?? null;
$ctrlRoutes = [];
if ($firstCtrl) {
    $ctrlRoutes = array_values(array_filter($data['routes'] ?? [], fn($r) =>
        str_contains($r['controller']['class'] ?? '', $firstCtrl['name'])
    ));
}
$routePrefix = '';
if (!empty($ctrlRoutes)) {
    $parts = explode('/', trim($ctrlRoutes[0]['uri'] ?? '', '/'));
    $routePrefix = '/' . implode('/', array_slice($parts, 0, count($parts) > 1 ? 2 : 1));
}

@endphp

{{-- Breadcrumb --}}
<div class="nmd-breadcrumb">
    <a href="{{ route('laradar.models') }}" class="nmd-bread-link"
       onclick="event.preventDefault(); showList('models', false)">Models</a>
    <svg class="nmd-bread-sep" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
    <span class="nmd-bread-curr">{{ $model['name'] }}</span>
</div>

{{-- Layout --}}
<div class="nmd-wrap">

    {{-- ── SIDEBAR ── --}}
    <aside class="nmd-sidebar">

        {{-- Identity --}}
        <div class="nmd-identity">
            <div class="nmd-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <ellipse cx="12" cy="5" rx="9" ry="3"/>
                    <path d="M3 5v14c0 1.657 4.03 3 9 3s9-1.343 9-3V5"/>
                    <path d="M3 12c0 1.657 4.03 3 9 3s9-1.343 9-3"/>
                </svg>
            </div>
            <p class="nmd-model-name">{{ $model['name'] }}</p>
            <div class="nmd-table-name">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                {{ $model['table'] }}
            </div>
            <div class="nmd-namespace">{{ $model['namespace'] }}</div>
        </div>

        {{-- Stats --}}
        <div class="nmd-stats">
            @if($fillCnt)
            <div class="nmd-stat-row" onclick="mdsTab('fields')" style="cursor:pointer;" title="View fields">
                <span class="nmd-stat-lbl">Fillable</span>
                <span class="nmd-stat-val">{{ $fillCnt }}</span>
            </div>
            @endif
            @if($hideCnt)
            <div class="nmd-stat-row" onclick="mdsTab('fields')" style="cursor:pointer;" title="View fields">
                <span class="nmd-stat-lbl">Hidden</span>
                <span class="nmd-stat-val">{{ $hideCnt }}</span>
            </div>
            @endif
            @if($castCnt)
            <div class="nmd-stat-row" onclick="mdsTab('fields')" style="cursor:pointer;" title="View fields">
                <span class="nmd-stat-lbl">Casts</span>
                <span class="nmd-stat-val">{{ $castCnt }}</span>
            </div>
            @endif
            @if($relCnt)
            <div class="nmd-stat-row" onclick="mdsTab('relations')" style="cursor:pointer;" title="View relationships">
                <span class="nmd-stat-lbl">Relationships</span>
                <span class="nmd-stat-val">{{ $relCnt }}</span>
            </div>
            @endif
            @if(count($scopes))
            <div class="nmd-stat-row" onclick="mdsTab('scopes')" style="cursor:pointer;" title="View scopes">
                <span class="nmd-stat-lbl">Scopes</span>
                <span class="nmd-stat-val">{{ count($scopes) }}</span>
            </div>
            @endif
            @if(count($accessors) || count($mutators))
            <div class="nmd-stat-row" onclick="mdsTab('accessors')" style="cursor:pointer;" title="View accessors & mutators">
                <span class="nmd-stat-lbl">Accessors / Mutators</span>
                <span class="nmd-stat-val">{{ count($accessors) + count($mutators) }}</span>
            </div>
            @endif
            <div class="nmd-stat-row">
                <span class="nmd-stat-lbl">Soft deletes</span>
                <span class="nmd-stat-val" style="color:{{ $hasSoftDeletes ? 'var(--emerald)' : 'var(--text-faint)' }};font-size:12px;">
                    {{ $hasSoftDeletes ? 'Enabled' : '—' }}
                </span>
            </div>
            <div class="nmd-stat-row">
                <span class="nmd-stat-lbl">Primary key</span>
                <span class="nmd-stat-val" style="font-family:var(--font-mono);font-size:11px;color:var(--text-dim);">
                    {{ $primaryKey }}<span style="color:var(--text-faint);font-size:10px;"> ({{ $keyType }})</span>
                </span>
            </div>
            @if($connection)
            <div class="nmd-stat-row">
                <span class="nmd-stat-lbl">DB connection</span>
                <span class="nmd-stat-val" style="font-family:var(--font-mono);font-size:11px;color:var(--text-dim);">{{ $connection }}</span>
            </div>
            @endif
            @if(!empty($eagerLoads))
            <div class="nmd-stat-row">
                <span class="nmd-stat-lbl">Eager loads ($with)</span>
                <span class="nmd-stat-val" style="color:#ff2d20;font-size:12px;">{{ count($eagerLoads) }} relation{{ count($eagerLoads) !== 1 ? 's' : '' }}</span>
            </div>
            @endif
            @if(!empty($appends))
            <div class="nmd-stat-row">
                <span class="nmd-stat-lbl">Appended attrs</span>
                <span class="nmd-stat-val" style="color:#ff2d20;font-size:12px;">{{ count($appends) }}</span>
            </div>
            @endif
        </div>

        {{-- Traits --}}
        @if(count($traits))
        <div class="nmd-section">
            <div class="nmd-section-lbl">Traits</div>
            <div class="nmd-chips">
                @foreach($traits as $trait)
                <span class="nmd-chip">{{ $trait }}</span>
                @endforeach
                @if($hasTimestamps && !in_array('timestamps', $traits))
                <span class="nmd-chip">timestamps</span>
                @endif
            </div>
        </div>
        @endif

        {{-- Appended virtual attributes --}}
        @if(!empty($appends))
        <div class="nmd-section">
            <div class="nmd-section-lbl">Appended (virtual)</div>
            <div class="nmd-chips">
                @foreach($appends as $app)
                <span class="nmd-chip">{{ $app }}</span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Always eager-loaded relations --}}
        @if(!empty($eagerLoads))
        <div class="nmd-section">
            <div class="nmd-section-lbl">Always eager ($with)</div>
            <div class="nmd-chips">
                @foreach($eagerLoads as $rel)
                <span class="nmd-chip" style="color:#ff2d20;border-color:rgba(255,45,32,.25);">{{ $rel }}</span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Used By --}}
        @if($relatedMigration || count($usedBy) || $observerData || $relatedPolicy)
        <div class="nmd-section">
            <div class="nmd-section-lbl">Used By</div>

            @if($relatedMigration)
            <a class="nmd-usedby-item"
               href="{{ route('laradar.migration.detail', $relatedMigration['filename']) }}"
               onclick="event.preventDefault(); openDetailAjax('migrations', this.href)">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#FF2D20" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span title="{{ $relatedMigration['filename'] }}">{{ $relatedMigration['filename'] }}</span>
            </a>
            @endif
            @if($observerData)
            <a class="nmd-usedby-item"
               href="{{ route('laradar.observer.detail', $observerData['name']) }}"
               onclick="event.preventDefault(); openDetailAjax('observers', this.href)">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#FF2D20" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span>{{ $observerData['name'] }}</span>
            </a>
            @endif
            @if($relatedPolicy)
            <a class="nmd-usedby-item"
               href="{{ route('laradar.policy.detail', $relatedPolicy['name']) }}"
               onclick="event.preventDefault(); openDetailAjax('policies', this.href)">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#FF2D20" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>{{ $relatedPolicy['name'] }}</span>
            </a>
            @endif

            @foreach($usedBy as $u)
            @php $ctrlExists = collect($data['controllers'])->firstWhere('name', $u['name']); @endphp
            @if($ctrlExists)
            <a class="nmd-usedby-item"
               href="{{ route('laradar.controller.detail', $u['name']) }}"
               onclick="event.preventDefault(); openDetailAjax('controllers', this.href)">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#FF2D20" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                <span>{{ $u['name'] }}</span>
            </a>
            @if($routePrefix && $firstCtrl && $firstCtrl['name'] === $u['name'])
            <div class="nmd-usedby-item" style="cursor:default;pointer-events:none;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;color:var(--text-faint);"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span style="color:var(--text-faint);">{{ $routePrefix }} · {{ count($ctrlRoutes) }} route{{ count($ctrlRoutes) !== 1 ? 's' : '' }}</span>
            </div>
            @endif
            @endif
            @endforeach
        </div>
        @endif

        {{-- Actions --}}
        <div class="nmd-actions">
            <a href="{{ route('laradar.models') }}" class="nmd-btn-outline"
               onclick="event.preventDefault(); showList('models', false)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                Back to models
            </a>
        </div>

    </aside>

    {{-- ── MAIN CONTENT ── --}}
    <div class="nmd-main">

        {{-- Tabs --}}
        <div class="mds-tabs" id="mds-tabs-row">
            <button class="mds-tab-btn active" id="mds-tab-fields" onclick="mdsTab('fields')">
                Fields
                @if(count($fieldMap))<span class="nmd-tab-count">{{ count($fieldMap) }}</span>@endif
            </button>
            @if($relCnt)
            <button class="mds-tab-btn" id="mds-tab-relations" onclick="mdsTab('relations')">
                Relationships
                <span class="nmd-tab-count">{{ $relCnt }}</span>
            </button>
            @endif
            @if(count($scopes))
            <button class="mds-tab-btn" id="mds-tab-scopes" onclick="mdsTab('scopes')">
                Scopes
                <span class="nmd-tab-count">{{ count($scopes) }}</span>
            </button>
            @endif
            @if(count($accessors) || count($mutators))
            <button class="mds-tab-btn" id="mds-tab-accessors" onclick="mdsTab('accessors')">
                Accessors / Mutators
                <span class="nmd-tab-count">{{ count($accessors) + count($mutators) }}</span>
            </button>
            @endif
        </div>

        {{-- Fields Tab --}}
        <div class="mds-tab-pane active" id="mds-pane-fields">
            <div class="dp-sec-lbl">
                <span class="dp-sec-lbl-text">Fields &middot; {{ count($fieldMap) }}</span>
                <span class="dp-sec-lbl-line"></span>
            </div>
            @if(count($fieldMap))
            <div style="overflow-x:auto;">
                <table class="nmd-tbl">
                    <thead>
                        <tr>
                            <th>Attribute</th>
                            <th>Type</th>
                            <th>Cast</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fieldMap as $fname => $info)
                        <tr class="{{ $info['hidden'] ? 'nmd-field-hidden' : '' }}">
                            <td><span class="nmd-field-name">{{ $fname }}</span>
                                @if($info['hidden'])<span style="font-family:var(--font-mono);font-size:9px;color:var(--text-faint);margin-left:6px;background:var(--bg-sunken);border:1px solid var(--border);padding:1px 5px;border-radius:4px;">hidden</span>@endif
                            </td>
                            <td>
                                @if($info['db_type'])
                                <span class="nmd-type-val">{{ $info['db_type'] }}</span>
                                @else
                                <span class="nmd-dash">—</span>
                                @endif
                            </td>
                            <td>
                                @if($info['cast'])
                                <span class="nmd-cast-val">{{ $info['cast'] }}</span>
                                @else
                                <span class="nmd-dash">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="nmd-empty">No fillable, hidden or cast fields detected.</div>
            @endif
        </div>

        {{-- Relationships Tab --}}
        @if($relCnt)
        <div class="mds-tab-pane" id="mds-pane-relations">
            <div class="dp-sec-lbl">
                <span class="dp-sec-lbl-text">Relationships &middot; {{ $relCnt }}</span>
                <span class="dp-sec-lbl-line"></span>
            </div>
            <div class="nmd-rel-list">
                @foreach($model['relationships'] as $rel)
                @php
                    $relName = class_basename($rel['related'] ?? '—');
                    $navIdx  = $modelIndexMap[$relName] ?? -1;
                    $fk      = $rel['foreign_key'] ?? null;
                @endphp
                <div class="nmd-rel-row">
                    <span class="nmd-rel-method">{{ $rel['method'] }}()</span>
                    <span class="nmd-rel-type-badge">{{ $rel['type'] }}</span>
                    <span class="nmd-rel-arrow">→</span>
                    <span class="nmd-rel-target">{{ $relName }}</span>
                    @if($fk)
                    <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);margin-left:auto;padding-right:8px;">{{ $fk }}</span>
                    @endif
                    @if($navIdx >= 0)
                    <a href="{{ route('laradar.model.detail', $relName) }}" class="nmd-nav-btn"
                       onclick="event.preventDefault(); openDetailAjax('models', this.href)">View →</a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Scopes Tab --}}
        @if(count($scopes))
        <div class="mds-tab-pane" id="mds-pane-scopes">
            <div class="dp-sec-lbl">
                <span class="dp-sec-lbl-text">Query Scopes &middot; {{ count($scopes) }}</span>
                <span class="dp-sec-lbl-line"></span>
            </div>
            <div class="nmd-rel-list">
                @foreach($scopes as $scope)
                <div class="nmd-rel-row">
                    <span class="nmd-rel-method">{{ $scope['name'] ?? $scope }}</span>
                    @php $scopeType = ($scope['type'] ?? 'local') === 'global' ? 'Global' : 'Local'; @endphp
                    <span class="nmd-rel-type-badge">{{ $scopeType }}</span>
                    @if(($scope['type'] ?? 'local') === 'local')
                    <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);margin-left:auto;">scope{{ ucfirst($scope['name'] ?? $scope) }}()</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Accessors / Mutators Tab --}}
        @if(count($accessors) || count($mutators))
        <div class="mds-tab-pane" id="mds-pane-accessors">
            @if(count($accessors))
            <div style="margin-bottom:{{ count($mutators) ? '8px' : '0' }}">
                <div class="dp-sec-lbl">
                    <span class="dp-sec-lbl-text">Accessors &middot; {{ count($accessors) }}</span>
                    <span class="dp-sec-lbl-line"></span>
                </div>
                <div class="nmd-rel-list">
                    @foreach($accessors as $accessor)
                    <div class="nmd-rel-row">
                        <span class="nmd-rel-method">{{ $accessor }}</span>
                        <span class="nmd-rel-type-badge">get</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @if(count($mutators))
            <div>
                <div class="dp-sec-lbl">
                    <span class="dp-sec-lbl-text">Mutators &middot; {{ count($mutators) }}</span>
                    <span class="dp-sec-lbl-line"></span>
                </div>
                <div class="nmd-rel-list">
                    @foreach($mutators as $mutator)
                    <div class="nmd-rel-row">
                        <span class="nmd-rel-method">{{ $mutator }}</span>
                        <span class="nmd-rel-type-badge">set</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif

    </div>{{-- .nmd-main --}}

</div>{{-- .nmd-wrap --}}
@endsection
