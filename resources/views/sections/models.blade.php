    @php
    $mTotalRels    = collect($data['models'])->sum(fn($m) => count($m['relationships']??[]));
    $mWithObs      = collect($data['models'])->filter(fn($m) => !empty($m['observer']))->count();
    $mSoftDel      = collect($data['models'])->filter(fn($m) => collect($m['traits']??[])->contains(fn($t)=>str_contains($t,'SoftDeletes')))->count();
    $mPalette = [
        ['color'=>'#FF2D20', 'bg'=>'rgba(255,45,32,.10)', 'border'=>'rgba(255,45,32,.25)', 'hex'=>'#FF2D20'],
        ['color'=>'#FF2D20', 'bg'=>'rgba(255,45,32,.10)', 'border'=>'rgba(255,45,32,.25)', 'hex'=>'#FF2D20'],
        ['color'=>'#FF2D20', 'bg'=>'rgba(255,45,32,.10)', 'border'=>'rgba(255,45,32,.25)', 'hex'=>'#FF2D20'],
        ['color'=>'#FF2D20', 'bg'=>'rgba(255,45,32,.10)', 'border'=>'rgba(255,45,32,.25)', 'hex'=>'#FF2D20'],
        ['color'=>'#FF2D20', 'bg'=>'rgba(255,45,32,.10)', 'border'=>'rgba(255,45,32,.25)', 'hex'=>'#FF2D20'],
        ['color'=>'#FF2D20', 'bg'=>'rgba(255,45,32,.10)', 'border'=>'rgba(255,45,32,.25)', 'hex'=>'#FF2D20'],
    ];
    $mRelColors = ['hasMany'=>'#34D399','hasOne'=>'#6366F1','belongsTo'=>'#60A5FA','belongsToMany'=>'#A78BFA','morphMany'=>'#F87171','morphTo'=>'#F87171','morphOne'=>'#F87171','hasManyThrough'=>'#FBBF24'];
    @endphp

    <div id="models-list">
        {{-- Top stats --}}
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ count($data['models']) }}</span>
                <span class="mds-top-stat-lbl">Total Models</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $mTotalRels }}</span>
                <span class="mds-top-stat-lbl">Relationships</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $mWithObs }}</span>
                <span class="mds-top-stat-lbl">With Observer</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $mSoftDel }}</span>
                <span class="mds-top-stat-lbl">Soft Deletes</span>
            </div>
        </div>

        {{-- Toolbar --}}
        <div class="mds-toolbar">
            <input id="models-search" oninput="filterGrid('models')" type="search" placeholder="Search models…" style="border:1px solid var(--border);border-radius:8px;padding:8px 14px;font-size:13px;flex:1;max-width:240px;font-family:var(--font-mono);">
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ count($data['models']) }} models</span>
        </div>

        {{-- Grid view --}}
        <div id="mds-grid-view" style="display:none;">
            <div class="mds-grid" id="models-grid">
                @foreach($data['models'] as $i => $model)
                @php
                    $mp      = $mPalette[$i % count($mPalette)];
                    $mRels   = $model['relationships'] ?? [];
                    $mRelCnt = count($mRels);
                    $mFillCnt= count($model['fillable'] ?? []);
                    $mTrCnt  = count($model['traits'] ?? []);
                    $mRelGrp = collect($mRels)->groupBy('type');
                    $mTotalFieldsInCard = $mRelCnt + $mFillCnt;
                @endphp
                <div class="mds-card" onclick="showDetail('models',{{$i}})" data-name="{{ strtolower($model['name']) }}" style="cursor:pointer;--card-hover-border:{{ $mp['border'] }};">
                    <div class="mds-card-glow" style="background:linear-gradient(90deg,{{ $mp['color'] }},transparent);"></div>
                    <div class="mds-card-head">
                        <div class="mds-card-av" style="background:{{ $mp['bg'] }};color:{{ $mp['color'] }};border-color:{{ $mp['border'] }};">{{ substr($model['name'],0,1) }}</div>
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:6px;">
                                <p class="mds-card-title">{{ $model['name'] }}</p>
                                @if(!empty($model['observer']))<span class="mds-card-obs">obs</span>@endif
                            </div>
                            <p class="mds-card-table">{{ $model['table'] }}</p>
                            <p class="mds-card-ns">{{ $model['namespace'] ?? '' }}</p>
                        </div>
                    </div>
                    <div class="mds-card-sep"></div>
                    <div class="mds-card-body">
                        {{-- Stats row --}}
                        <div class="mds-card-stats" style="background:var(--bg-hover);border-radius:10px;border:1px solid var(--border);margin-bottom:12px;">
                            <div class="mds-card-stat-item">
                                <div class="mds-card-stat-num" style="color:{{ $mp['color'] }};">{{ $mRelCnt }}</div>
                                <div class="mds-card-stat-lbl">Relations</div>
                            </div>
                            <div class="mds-card-stat-item">
                                <div class="mds-card-stat-num" style="color:#FF2D20;">{{ $mFillCnt }}</div>
                                <div class="mds-card-stat-lbl">Fillable</div>
                            </div>
                            <div class="mds-card-stat-item">
                                <div class="mds-card-stat-num" style="color:var(--text-dim);">{{ $mTrCnt }}</div>
                                <div class="mds-card-stat-lbl">Traits</div>
                            </div>
                        </div>

                        {{-- Relationship bar --}}
                        @if($mRelCnt > 0)
                        <div class="mds-rel-bar">
                            @foreach($mRelGrp as $rType => $rItems)
                            @php $rw = round(count($rItems) / $mRelCnt * 100); $rCol = $mRelColors[$rType] ?? '#6B778C'; @endphp
                            <div class="mds-rel-seg" data-flex="{{ $rw }}" style="flex:0;min-width:0;background:{{ $rCol }};opacity:.75;"></div>
                            @endforeach
                        </div>
                        <div class="mds-rel-legend">
                            @foreach($mRelGrp as $rType => $rItems)
                            @php $rCol = $mRelColors[$rType] ?? '#6B778C'; @endphp
                            <span class="mds-rel-dot"><i style="background:{{ $rCol }};"></i>{{ $rType }} ·{{ count($rItems) }}</span>
                            @endforeach
                        </div>
                        @endif

                        {{-- Traits --}}
                        @if(!empty($model['traits']))
                        <div class="mds-trait-row">
                            @foreach($model['traits'] as $tr)
                            <span class="mds-trait-pip">{{ $tr }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- List view --}}
        <div id="mds-list-view" style="background:var(--bg-elevated);border-radius:12px;border:1px solid var(--border);overflow:hidden;">
            <div class="mds-list-head">
                <span></span><span>Model</span><span>Table</span><span>Rels</span><span>Fillable</span><span>Traits</span>
            </div>
            @foreach($data['models'] as $i => $model)
            @php $mp = $mPalette[$i % count($mPalette)]; @endphp
            <div class="mds-list-row" onclick="showDetail('models',{{$i}})" data-name="{{ strtolower($model['name']) }}" style="cursor:pointer;">
                <div class="mds-list-av" style="background:{{ $mp['bg'] }};color:{{ $mp['color'] }};border-color:{{ $mp['border'] }};">{{ substr($model['name'],0,1) }}</div>
                <div>
                    <span style="font-weight:700;font-size:13.5px;color:var(--text);">{{ $model['name'] }}</span>
                    @if(!empty($model['observer']))<span style="font-size:9px;padding:2px 6px;border-radius:4px;background:rgba(255,45,32,.08);color:#FF2D20;border:1px solid rgba(255,45,32,.2);font-family:var(--font-mono);margin-left:8px;">obs</span>@endif
                </div>
                <span style="font-family:var(--font-mono);font-size:11.5px;color:var(--text-faint);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $model['table'] }}</span>
                <span style="font-family:var(--font-mono);font-size:14px;font-weight:800;" style="color:{{ $mp['color'] }}">{{ count($model['relationships']??[]) }}</span>
                <span style="font-family:var(--font-mono);font-size:14px;font-weight:800;color:#FF2D20;">{{ count($model['fillable']??[]) }}</span>
                <span style="font-family:var(--font-mono);font-size:14px;font-weight:800;color:var(--text-dim);">{{ count($model['traits']??[]) }}</span>
            </div>
            @endforeach
        </div>
    </div>
    <div id="models-detail" style="display:none"><div id="models-detail-content"></div></div>



