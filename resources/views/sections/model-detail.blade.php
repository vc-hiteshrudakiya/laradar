@extends('laradar::layouts.laradar')

@section('content')
@php
$pal = [
    ['color'=>'#FF2D20', 'rgb'=>'255,45,32', 'hex'=>'#FF2D20'],
    ['color'=>'#FF2D20', 'rgb'=>'255,45,32', 'hex'=>'#FF2D20'],
    ['color'=>'#FF2D20', 'rgb'=>'255,45,32', 'hex'=>'#FF2D20'],
    ['color'=>'#FF2D20', 'rgb'=>'255,45,32', 'hex'=>'#FF2D20'],
    ['color'=>'#FF2D20', 'rgb'=>'255,45,32', 'hex'=>'#FF2D20'],
    ['color'=>'#FF2D20', 'rgb'=>'255,45,32', 'hex'=>'#FF2D20'],
];
$p = $pal[max(0, (ord($model['name'][0] ?? 'A') - 65)) % count($pal)];

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

// Build field map
$fieldMap = [];
foreach ($model['fillable'] ?? [] as $f) {
    $fieldMap[$f] = ['fillable' => true, 'hidden' => false, 'cast' => null];
}
foreach ($model['hidden'] ?? [] as $f) {
    if (!isset($fieldMap[$f])) $fieldMap[$f] = ['fillable' => false, 'hidden' => false, 'cast' => null];
    $fieldMap[$f]['hidden'] = true;
}
foreach ($model['casts'] ?? [] as $f => $type) {
    if (!isset($fieldMap[$f])) $fieldMap[$f] = ['fillable' => false, 'hidden' => false, 'cast' => null];
    $fieldMap[$f]['cast'] = $type;
}

$fillCnt  = count($model['fillable'] ?? []);
$hideCnt  = count($model['hidden'] ?? []);
$castCnt  = count($model['casts'] ?? []);
$relCnt   = count($model['relationships'] ?? []);
$traitCnt = count($model['traits'] ?? []);
$traits   = array_map(fn($t) => class_basename($t), $model['traits'] ?? []);

// Build model name→index map for relationship navigation
$modelIndexMap = [];
foreach ($data['models'] as $mi => $m) {
    $modelIndexMap[$m['name']] = $mi;
}

// Find controllers that use this model via dependency edges
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

// Feature flags
$flags = [];
if (collect($traits)->contains(fn($t) => str_contains($t, 'SoftDeletes'))) $flags[] = ['label'=>'SoftDeletes','color'=>'#FF2D20','bg'=>'rgba(255,45,32,.08)','border'=>'rgba(255,45,32,.2)'];
if (collect($traits)->contains(fn($t) => str_contains($t, 'HasFactory')))  $flags[] = ['label'=>'HasFactory','color'=>'#FF2D20','bg'=>'rgba(255,45,32,.08)','border'=>'rgba(255,45,32,.2)'];
if (collect($traits)->contains(fn($t) => str_contains($t, 'Searchable')))  $flags[] = ['label'=>'Searchable','color'=>'#FF2D20','bg'=>'rgba(255,45,32,.08)','border'=>'rgba(255,45,32,.2)'];
if ($model['timestamps'] !== false) $flags[] = ['label'=>'$timestamps = true','color'=>'#FF2D20','bg'=>'rgba(255,45,32,.06)','border'=>'rgba(255,45,32,.15)'];

$prevIndex = $index - 1;
$nextIndex = $index + 1;
$hasPrev   = $prevIndex >= 0;
$hasNext   = $nextIndex < count($data['models']);
@endphp

{{-- Prev / Next nav --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div style="display:flex;align-items:center;gap:10px;">
        @if($hasPrev)
        <a href="{{ route('laradar.model.detail', $data['models'][$prevIndex]['name']) }}" style="display:inline-flex;align-items:center;gap:7px;font-size:12px;font-weight:700;font-family:var(--font-mono);color:var(--text-dim);background:var(--bg-elevated);border:1px solid var(--border);border-radius:9px;padding:6px 13px;text-decoration:none;transition:border-color .15s,color .15s;" onmouseenter="this.style.borderColor='rgba(255,45,32,.4)';this.style.color='#FF2D20'" onmouseleave="this.style.borderColor='var(--border)';this.style.color='var(--text-dim)'">
            <svg style="width:13px;height:13px;flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            {{ $data['models'][$prevIndex]['name'] }}
        </a>
        @endif
        @if($hasNext)
        <a href="{{ route('laradar.model.detail', $data['models'][$nextIndex]['name']) }}" style="display:inline-flex;align-items:center;gap:7px;font-size:12px;font-weight:700;font-family:var(--font-mono);color:var(--text-dim);background:var(--bg-elevated);border:1px solid var(--border);border-radius:9px;padding:6px 13px;text-decoration:none;transition:border-color .15s,color .15s;" onmouseenter="this.style.borderColor='rgba(255,45,32,.4)';this.style.color='#FF2D20'" onmouseleave="this.style.borderColor='var(--border)';this.style.color='var(--text-dim)'">
            {{ $data['models'][$nextIndex]['name'] }}
            <svg style="width:13px;height:13px;flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
        @endif
    </div>
    <span style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);">{{ $index + 1 }} / {{ count($data['models']) }}</span>
</div>

{{-- Two-column layout --}}
<div class="mds-det-wrap">

    {{-- ── LEFT SIDEBAR ── --}}
    <aside class="mds-sidebar">
        <div class="mds-side-card">
            <div class="mds-side-top">
                <div class="mds-side-av" style="background:rgba({{ $p['rgb'] }},.18);color:{{ $p['color'] }};border-color:rgba({{ $p['rgb'] }},.4);">
                    {{ strtoupper($model['name'][0] ?? '?') }}
                </div>
                <p class="mds-side-name">{{ $model['name'] }}</p>
                <p class="mds-side-tbl">
                    <svg style="width:10px;height:10px;display:inline;margin-right:4px;vertical-align:middle;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0018 0V5"/></svg>
                    {{ $model['table'] }}
                </p>
                <p class="mds-side-ns">{{ $model['namespace'] }}</p>
            </div>

            <div class="mds-side-stats">
                @if($fillCnt)
                <div class="mds-side-stat">
                    <span class="mds-side-stat-lbl">Fillable</span>
                    <span class="mds-side-stat-val" style="color:#FF2D20;">{{ $fillCnt }}</span>
                </div>
                @endif
                @if($hideCnt)
                <div class="mds-side-stat">
                    <span class="mds-side-stat-lbl">Hidden</span>
                    <span class="mds-side-stat-val" style="color:#FF2D20;">{{ $hideCnt }}</span>
                </div>
                @endif
                @if($castCnt)
                <div class="mds-side-stat">
                    <span class="mds-side-stat-lbl">Casts</span>
                    <span class="mds-side-stat-val" style="color:#FF2D20;">{{ $castCnt }}</span>
                </div>
                @endif
                @if($relCnt)
                <div class="mds-side-stat">
                    <span class="mds-side-stat-lbl">Relationships</span>
                    <span class="mds-side-stat-val" style="color:#FF2D20;">{{ $relCnt }}</span>
                </div>
                @endif
                @if(count($usedBy))
                <div class="mds-side-stat">
                    <span class="mds-side-stat-lbl">Used by</span>
                    <span class="mds-side-stat-val" style="color:#FF2D20;">{{ count($usedBy) }}</span>
                </div>
                @endif
                @if(!$fillCnt && !$relCnt && !count($usedBy))
                <p style="font-size:12px;color:var(--text-faint);text-align:center;padding:8px 0;">No data available</p>
                @endif
            </div>

            <div class="mds-side-meta">
                @if(!empty($model['observer']))
                <p style="font-size:10.5px;color:var(--text-faint);font-family:var(--font-mono);margin-bottom:6px;text-transform:uppercase;letter-spacing:.06em;font-weight:700;">Observer</p>
                <span class="mds-side-chip" style="background:rgba(255,45,32,.08);color:#FF2D20;border-color:rgba(255,45,32,.25);">{{ class_basename($model['observer']) }}</span>
                @endif
                @if(count($traits))
                <p style="font-size:10.5px;color:var(--text-faint);font-family:var(--font-mono);margin:{{ !empty($model['observer']) ? '12' : '0' }}px 0 8px;text-transform:uppercase;letter-spacing:.06em;font-weight:700;">Traits</p>
                <div style="display:flex;flex-wrap:wrap;gap:5px;">
                    @foreach($traits as $trait)
                    <span class="mds-side-chip" style="background:rgba(255,45,32,.08);color:#FF2D20;border-color:rgba(255,45,32,.25);">{{ $trait }}</span>
                    @endforeach
                </div>
                @endif
                @if($model['timestamps'] !== false)
                <span class="mds-side-chip" style="background:rgba(255,45,32,.06);color:#FF2D20;border-color:rgba(255,45,32,.2);margin-top:8px;display:inline-block;">timestamps</span>
                @endif

                {{-- Config meta --}}
                <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--border);display:flex;flex-direction:column;gap:7px;">
                    <div style="display:flex;justify-content:space-between;font-size:11px;font-family:var(--font-mono);">
                        <span style="color:var(--text-faint);">Primary Key</span>
                        <span style="color:var(--text-dim);">{{ $model['primary_key'] ?? 'id' }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:11px;font-family:var(--font-mono);">
                        <span style="color:var(--text-faint);">Key Type</span>
                        <span style="color:var(--text-dim);">{{ $model['key_type'] ?? 'int' }}</span>
                    </div>
                    @if(!empty($model['connection']))
                    <div style="display:flex;justify-content:space-between;font-size:11px;font-family:var(--font-mono);">
                        <span style="color:var(--text-faint);">Connection</span>
                        <span style="color:var(--text-dim);">{{ $model['connection'] }}</span>
                    </div>
                    @endif
                    <div style="display:flex;justify-content:space-between;font-size:11px;font-family:var(--font-mono);">
                        <span style="color:var(--text-faint);">Path</span>
                        <span style="color:var(--text-dim);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:140px;" title="{{ $model['path'] }}">{{ $model['path'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    {{-- ── RIGHT MAIN CONTENT ── --}}
    <div style="min-width:0;flex:1;">

        {{-- Tabs --}}
        <div class="mds-tabs" id="mds-tabs-row">
            <button class="mds-tab-btn active" id="mds-tab-fields" onclick="mdsTab('fields')">
                Fields
                @if(count($fieldMap))
                <span style="font-size:10px;padding:1px 6px;border-radius:4px;background:rgba({{ $p['rgb'] }},.15);color:{{ $p['color'] }};margin-left:5px;font-family:var(--font-mono);">{{ count($fieldMap) }}</span>
                @endif
            </button>
            @if($relCnt)
            <button class="mds-tab-btn" id="mds-tab-relations" onclick="mdsTab('relations')">
                Relationships
                <span style="font-size:10px;padding:1px 6px;border-radius:4px;background:rgba(255,45,32,.12);color:#FF2D20;margin-left:5px;font-family:var(--font-mono);">{{ $relCnt }}</span>
            </button>
            @endif
            @if(count($usedBy))
            <button class="mds-tab-btn" id="mds-tab-usedby" onclick="mdsTab('usedby')">
                Used By
                <span style="font-size:10px;padding:1px 6px;border-radius:4px;background:rgba(255,45,32,.12);color:#FF2D20;margin-left:5px;font-family:var(--font-mono);">{{ count($usedBy) }}</span>
            </button>
            @endif
        </div>

        {{-- ── Fields Tab ── --}}
        <div class="mds-tab-pane active" id="mds-pane-fields">
            @if(count($fieldMap))
            <div class="mds-schema-wrap">
                <table class="mds-schema-tbl">
                    <thead>
                        <tr>
                            <th>Field</th>
                            <th>Status</th>
                            <th>Cast Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fieldMap as $fname => $info)
                        <tr>
                            <td><span class="mds-field-name">{{ $fname }}</span></td>
                            <td>
                                @if($info['fillable'])<span class="mds-fbadge fill">FILLABLE</span>@endif
                                @if($info['hidden'])<span class="mds-fbadge hide">HIDDEN</span>@endif
                            </td>
                            <td>
                                @if($info['cast'])
                                <span class="mds-cast-val">{{ $info['cast'] }}</span>
                                @else
                                <span style="color:var(--text-faint);font-size:12px;">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            @if(count($flags))
            <div class="mds-flag-row">
                @foreach($flags as $flag)
                <span class="mds-flag" style="color:{{ $flag['color'] }};background:{{ $flag['bg'] }};border-color:{{ $flag['border'] }};">{{ $flag['label'] }}</span>
                @endforeach
            </div>
            @endif

            @if(!count($fieldMap) && !count($flags))
            <p style="color:var(--text-faint);font-size:13px;text-align:center;padding:40px 0;">No fillable, hidden or cast fields detected.</p>
            @endif
        </div>

        {{-- ── Relationships Tab ── --}}
        @if($relCnt)
        <div class="mds-tab-pane" id="mds-pane-relations">
            @foreach($model['relationships'] as $rel)
            @php
                $rc      = $relColors[$rel['type']] ?? ['color'=>'var(--text-dim)','bg'=>'rgba(91,103,133,.1)','border'=>'var(--border)'];
                $relName = class_basename($rel['related'] ?? '—');
                $navIdx  = $modelIndexMap[$relName] ?? -1;
            @endphp
            <div class="mds-rel-card" style="border-color:var(--border);"
                 onmouseenter="this.style.borderColor='{{ $rc['border'] }}'"
                 onmouseleave="this.style.borderColor='var(--border)'">
                <span class="mds-rel-method">{{ $rel['method'] }}()</span>
                <span class="mds-rel-type" style="color:{{ $rc['color'] }};background:{{ $rc['bg'] }};border-color:{{ $rc['border'] }};">{{ $rel['type'] }}</span>
                <span class="mds-rel-arrow">→</span>
                <span class="mds-rel-target">{{ $relName }}</span>
                @if($navIdx >= 0)
                <a href="{{ route('laradar.model.detail', $relName) }}" class="mds-nav-btn" style="color:{{ $rc['color'] }};background:{{ $rc['bg'] }};border-color:{{ $rc['border'] }};">View →</a>
                @else
                <span></span>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        {{-- ── Used By Tab ── --}}
        @if(count($usedBy))
        <div class="mds-tab-pane" id="mds-pane-usedby">
            @foreach($usedBy as $u)
            @php $ci = collect($data['controllers'])->search(fn($c) => $c['name'] === $u['name']); @endphp
            <div class="mds-usedby-card">
                <div>
                    <span style="font-size:14px;font-weight:700;color:var(--text);">{{ $u['name'] }}</span>
                    <span style="font-size:11px;color:var(--text-faint);margin-left:10px;font-family:var(--font-mono);">{{ $u['routes'] }} route{{ $u['routes'] !== 1 ? 's' : '' }}</span>
                </div>
                @if($ci !== false)
                <a href="{{ route('laradar.controllers') }}" class="mds-nav-btn" style="color:#FF2D20;background:rgba(255,45,32,.08);border-color:rgba(255,45,32,.2);">View controller →</a>
                @endif
            </div>
            @endforeach
        </div>
        @endif

    </div>
</div>
@endsection
