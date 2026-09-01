<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laradar Report — {{ $data['project']['name'] }}</title>
<link rel="icon" type="image/x-icon" href="{{ route('laradar.asset', ['filename' => 'favicon.ico']) }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wdth,wght@0,75..100,400..700;1,75..100,400..700&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#FFFFFF;--bg-elevated:#FFFFFF;--bg-sunken:#F9F6EF;--bg-hover:#F9FAFB;
  --border:#E5E7EB;--border-strong:#D1D5DB;
  --text:#1D1D1F;--text-dim:#374151;--text-faint:#6B7280;
  --brand:#FF2D20;--brand-bg:rgba(255,45,32,0.08);--brand-border:rgba(255,45,32,0.20);
  --emerald:#16A34A;--amber:#D97706;--rose:#DC2626;
  --shadow:0 1px 3px rgba(0,0,0,0.08),0 4px 16px rgba(0,0,0,0.06);
  --font-sans:'Instrument Sans',sans-serif;--font-mono:'JetBrains Mono',monospace;
  --ease:cubic-bezier(.22,.61,.36,1);
}
*{box-sizing:border-box;margin:0;padding:0;}
body{background:var(--bg);color:var(--text);font-family:var(--font-sans);font-size:14px;-webkit-font-smoothing:antialiased;}
::-webkit-scrollbar{width:4px;height:4px;}
::-webkit-scrollbar-track{background:transparent;}
::-webkit-scrollbar-thumb{background:var(--border-strong);border-radius:4px;}

/* Layout */
.rp-layout{display:grid;grid-template-columns:260px 1fr;min-height:100vh;}

/* Sidebar */
.rp-sidebar{background:var(--bg-elevated);border-right:1px solid var(--border);position:sticky;top:0;height:100vh;overflow-y:auto;display:flex;flex-direction:column;scrollbar-width:none;}
.rp-sidebar::-webkit-scrollbar{display:none;}
.rp-brand{display:flex;align-items:center;gap:10px;padding:20px 20px 16px;border-bottom:1px solid var(--border);}
.rp-brand-mark{width:34px;height:34px;border-radius:8px;background:transparent;border:none;display:flex;align-items:center;justify-content:center;flex:none;overflow:hidden;}
.rp-brand-mark img{display:block;width:34px;height:34px;object-fit:cover;border-radius:8px;}
.rp-brand strong{font-size:15px;font-weight:800;letter-spacing:.04em;color:var(--text);text-transform:uppercase;}
.rp-project{padding:12px 20px;background:var(--bg-sunken);border-bottom:1px solid var(--border);}
.rp-project p{font-size:11px;color:var(--text-faint);}
.rp-project strong{font-size:13px;font-weight:700;color:var(--text);display:block;margin-bottom:2px;}
.rp-project span{font-family:var(--font-mono);font-size:10px;color:var(--text-faint);display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.rp-nav{flex:1;padding:12px 12px;display:flex;flex-direction:column;gap:2px;}
.rp-nav-label{font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--text-faint);padding:8px 8px 4px;}
.rp-nav-btn{display:flex;align-items:center;justify-content:space-between;width:100%;padding:9px 12px;border-radius:10px;border:none;background:transparent;cursor:pointer;font-family:var(--font-sans);font-size:13px;font-weight:500;color:var(--text-dim);transition:background .15s,color .15s;text-align:left;}
.rp-nav-btn:hover{background:var(--bg-hover);color:var(--text);}
.rp-nav-btn.active{background:var(--brand-bg);color:var(--brand);font-weight:700;}
.rp-nav-btn .left{display:flex;align-items:center;gap:8px;}
.rp-nav-badge{font-size:10px;font-weight:700;font-family:var(--font-mono);background:var(--brand-bg);color:var(--brand);border:1px solid var(--brand-border);padding:1px 7px;border-radius:20px;}
.rp-sidebar-footer{padding:16px 20px;border-top:1px solid var(--border);font-size:11px;color:var(--text-faint);}
.rp-sidebar-footer div{display:flex;justify-content:space-between;padding:3px 0;}
.rp-sidebar-footer span:last-child{color:var(--text-dim);font-family:var(--font-mono);}

/* Main */
.rp-main{display:flex;flex-direction:column;min-height:100vh;}
.rp-topbar{position:sticky;top:0;z-index:10;background:rgba(255,255,255,.92);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);padding:12px 28px;display:flex;align-items:center;justify-between;}
.rp-topbar-left h2{font-size:15px;font-weight:800;color:var(--text);}
.rp-topbar-left p{font-size:11px;color:var(--text-faint);margin-top:1px;}
.rp-section-chip{display:inline-block;font-size:11px;font-weight:600;background:var(--bg-sunken);color:var(--text-faint);padding:2px 10px;border-radius:20px;margin-left:8px;border:1px solid var(--border);}
.rp-content{flex:1;padding:28px;}

/* Sections */
.rp-section{display:none;}
.rp-section.active{display:block;}
.rp-section-hd{display:flex;align-items:center;gap:10px;margin-bottom:20px;}
.rp-section-hd h3{font-size:13px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--text-faint);}
.rp-section-hd .count{font-size:10px;font-weight:700;font-family:var(--font-mono);background:var(--brand-bg);color:var(--brand);border:1px solid var(--brand-border);padding:1px 8px;border-radius:20px;}

/* Cards */
.rp-card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;overflow:hidden;transition:box-shadow .15s;}
.rp-card:hover{box-shadow:var(--shadow);}
.rp-card-hd{padding:14px 18px;background:var(--bg-sunken);border-bottom:1px solid var(--border);display:flex;align-items:flex-start;justify-content:space-between;gap:10px;}
.rp-card-hd h4{font-size:13.5px;font-weight:700;color:var(--text);}
.rp-card-hd p{font-size:11px;color:var(--text-faint);font-family:var(--font-mono);margin-top:2px;}
.rp-card-body{padding:14px 18px;}
.rp-pill{display:inline-block;font-size:10px;font-weight:600;font-family:var(--font-mono);padding:2px 8px;border-radius:6px;border:1px solid;}

/* KPI grid */
.rp-kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;}
.rp-kpi{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;padding:18px;border-left:3px solid var(--brand);}
.rp-kpi-num{font-size:30px;font-weight:900;color:var(--text);line-height:1;}
.rp-kpi-lbl{font-size:11px;color:var(--text-faint);margin-bottom:6px;}
.rp-kpi-sub{font-size:11px;color:var(--brand);margin-top:4px;}

/* Grid layouts */
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;}
.grid-4{display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:14px;}
@media(max-width:1100px){.grid-4{grid-template-columns:1fr 1fr;}.rp-kpi-grid{grid-template-columns:repeat(2,1fr);}}
@media(max-width:800px){.grid-3,.grid-2{grid-template-columns:1fr;}}

/* Table */
.rp-table-wrap{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;overflow:hidden;}
.rp-table{width:100%;border-collapse:collapse;font-size:12px;}
.rp-table th{background:var(--bg-sunken);padding:10px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-faint);border-bottom:1px solid var(--border);white-space:nowrap;}
.rp-table td{padding:10px 16px;border-bottom:1px solid var(--border);color:var(--text-dim);vertical-align:top;}
.rp-table tr:last-child td{border-bottom:none;}
.rp-table tr:hover td{background:var(--bg-hover);}

/* Score ring */
.score-ring-wrap{position:relative;width:88px;height:88px;}
.score-ring-wrap svg{transform:rotate(-90deg);}
.score-ring-inner{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;}

/* Method badges */
.method-get{background:#EFF6FF;color:#1D4ED8;border:1px solid #BFDBFE;}
.method-post{background:#F0FDF4;color:#15803D;border:1px solid #BBF7D0;}
.method-put{background:#FFFBEB;color:#B45309;border:1px solid #FDE68A;}
.method-patch{background:#FFF7ED;color:#C2410C;border:1px solid #FED7AA;}
.method-delete{background:#FEF2F2;color:#B91C1C;border:1px solid #FECACA;}

/* Bar chart */
.rp-bar-row{margin-bottom:10px;}
.rp-bar-row-top{display:flex;justify-content:space-between;margin-bottom:4px;font-size:11px;}
.rp-bar-track{height:5px;background:var(--bg-sunken);border-radius:3px;overflow:hidden;}
.rp-bar-fill{height:5px;border-radius:3px;background:var(--brand);}

/* Toolbar */
.rp-toolbar{display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap;}
.rp-search{border:1px solid var(--border);border-radius:8px;padding:7px 12px;font-size:12px;font-family:var(--font-mono);background:var(--bg);color:var(--text);outline:none;flex:1;max-width:260px;}
.rp-search:focus{border-color:var(--brand);}
.rp-filter-btn{font-size:11px;font-weight:600;padding:5px 12px;border-radius:20px;border:1px solid var(--border);background:var(--bg);color:var(--text-dim);cursor:pointer;transition:all .15s;font-family:var(--font-sans);}
.rp-filter-btn.active,.rp-filter-btn:hover{background:var(--brand);color:#fff;border-color:var(--brand);}

/* Item list card (jobs/events/services etc) */
.rp-item-card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;padding:16px;transition:box-shadow .15s;}
.rp-item-card:hover{box-shadow:var(--shadow);}
.rp-item-av{width:38px;height:38px;border-radius:10px;background:var(--brand-bg);border:1px solid var(--brand-border);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:var(--brand);flex:none;}

/* Footer */
.rp-footer{text-align:center;font-size:11px;color:var(--text-faint);border-top:1px solid var(--border);padding:16px 28px;}

/* KPI grid */
.ov-kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;}
.ov-kpi-card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;padding:20px;transition:transform .22s,box-shadow .22s,border-color .22s;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 14px rgba(0,0,0,.05);}
.ov-kpi-card:hover{transform:translateY(-2px);box-shadow:0 4px 18px rgba(0,0,0,.10);border-color:var(--border-strong);}
.ov-kpi-icon{width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;}
.ov-kpi-icon svg{width:17px;height:17px;stroke:currentColor;fill:none;}
.ov-kpi-label{font-family:var(--font-mono);font-size:10px;letter-spacing:.07em;text-transform:uppercase;color:var(--text-faint);display:block;}
.ov-kpi-num{font-family:var(--font-mono);font-size:28px;letter-spacing:-.01em;margin-top:4px;display:block;color:var(--brand);}

/* Atlas cards */
.ov-cards-row{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;margin-top:6px;}
.ov-card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;padding:22px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 14px rgba(0,0,0,.05);}
.ov-card-hd{font-size:14px;font-weight:700;color:var(--text);margin-bottom:18px;}
.ov-score-bar{height:4px;border-radius:2px;background:var(--bg-sunken);overflow:hidden;margin-top:6px;}
.ov-score-fill{height:100%;border-radius:2px;}

/* Score ring */
.ov-score-wrap{display:flex;align-items:flex-start;gap:24px;flex-wrap:wrap;margin-bottom:24px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;padding:22px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 14px rgba(0,0,0,.05);}
.ov-ring-box{position:relative;width:88px;height:88px;flex:none;}
.ov-ring-box svg{transform:rotate(-90deg);}
.ov-ring-inner{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;}
</style>
</head>
<body>

@php
$rs       = $data['route_summary'] ?? [];
$sc       = $data['score'] ?? [];
$namedPct = ($rs['total'] ?? 0) > 0 ? round(($rs['named_count'] / $rs['total']) * 100) : 0;
if (!empty($sc)) {
    $circumf   = 251.2;
    $ringPct   = $sc['score'] / $sc['max'];
    $offset    = round($circumf * (1 - $ringPct), 2);
    $scorePct  = $sc['max'] > 0 ? ($sc['score'] / $sc['max']) * 100 : 0;
    $ringColor = $scorePct >= 90 ? '#16A34A' : ($scorePct >= 70 ? '#D97706' : ($scorePct >= 50 ? '#EA580C' : '#DC2626'));
    $gradeColor= $scorePct >= 90 ? '#16A34A' : ($scorePct >= 70 ? '#D97706' : ($scorePct >= 50 ? '#EA580C' : '#DC2626'));
    $gradeBg   = $scorePct >= 90 ? 'rgba(22,163,74,.10)' : ($scorePct >= 70 ? 'rgba(217,119,6,.10)' : ($scorePct >= 50 ? 'rgba(234,88,12,.10)' : 'rgba(220,38,38,.10)'));
    $gradeBdr  = $scorePct >= 90 ? 'rgba(22,163,74,.3)' : ($scorePct >= 70 ? 'rgba(217,119,6,.3)' : ($scorePct >= 50 ? 'rgba(234,88,12,.3)' : 'rgba(220,38,38,.3)'));
}
@endphp

<div class="rp-layout">

{{-- ── SIDEBAR ── --}}
<aside class="rp-sidebar">
    <div class="rp-brand">
        <div class="rp-brand-mark">
            <img src="{{ route('laradar.asset', ['filename' => 'laradar-icon.svg']) }}" alt="Laradar" width="34" height="34" style="display:block;object-fit:cover;border-radius:8px;">
        </div>
        <strong>Laradar</strong>
    </div>

    <div class="rp-project">
        <p>Project</p>
        <strong>{{ $data['project']['name'] }}</strong>
        <span>{{ $data['project']['base_path'] }}</span>
    </div>

    <nav class="rp-nav">
        <div class="rp-nav-label">Sections</div>

        <button class="rp-nav-btn active" data-sec="overview" onclick="showSec('overview')">
            <span class="left">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Overview
            </span>
        </button>

        <button class="rp-nav-btn" data-sec="models" onclick="showSec('models')">
            <span class="left">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v4c0 1.66 4.03 3 9 3s9-1.34 9-3V5"/><path d="M3 9v4c0 1.66 4.03 3 9 3s9-1.34 9-3V9"/><path d="M3 13v4c0 1.66 4.03 3 9 3s9-1.34 9-3v-4"/></svg>
                Models
            </span>
            <span class="rp-nav-badge">{{ $data['summary']['models'] }}</span>
        </button>

        <button class="rp-nav-btn" data-sec="controllers" onclick="showSec('controllers')">
            <span class="left">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                Controllers
            </span>
            <span class="rp-nav-badge">{{ $data['summary']['controllers'] }}</span>
        </button>

        <button class="rp-nav-btn" data-sec="routes" onclick="showSec('routes')">
            <span class="left">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
                Routes
            </span>
            <span class="rp-nav-badge">{{ $data['summary']['routes'] }}</span>
        </button>

        @if($data['summary']['jobs'] > 0)
        <button class="rp-nav-btn" data-sec="jobs" onclick="showSec('jobs')">
            <span class="left">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                Jobs
            </span>
            <span class="rp-nav-badge">{{ $data['summary']['jobs'] }}</span>
        </button>
        @endif

        @if($data['summary']['events'] > 0)
        <button class="rp-nav-btn" data-sec="events" onclick="showSec('events')">
            <span class="left">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8 19.79 19.79 0 01.22 1.17 2 2 0 012.2 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                Events
            </span>
            <span class="rp-nav-badge">{{ $data['summary']['events'] }}</span>
        </button>
        @endif

        @if($data['summary']['services'] > 0)
        <button class="rp-nav-btn" data-sec="services" onclick="showSec('services')">
            <span class="left">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M5.34 18.66l-1.41 1.41M21 12h-2M5 12H3M19.07 19.07l-1.41-1.41M5.34 5.34L3.93 3.93"/></svg>
                Services
            </span>
            <span class="rp-nav-badge">{{ $data['summary']['services'] }}</span>
        </button>
        @endif

        @if($data['summary']['repositories'] > 0)
        <button class="rp-nav-btn" data-sec="repositories" onclick="showSec('repositories')">
            <span class="left">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                Repositories
            </span>
            <span class="rp-nav-badge">{{ $data['summary']['repositories'] }}</span>
        </button>
        @endif

        @if($data['summary']['observers'] > 0)
        <button class="rp-nav-btn" data-sec="observers" onclick="showSec('observers')">
            <span class="left">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                Observers
            </span>
            <span class="rp-nav-badge">{{ $data['summary']['observers'] }}</span>
        </button>
        @endif

        @if($data['summary']['policies'] > 0)
        <button class="rp-nav-btn" data-sec="policies" onclick="showSec('policies')">
            <span class="left">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Policies
            </span>
            <span class="rp-nav-badge">{{ $data['summary']['policies'] }}</span>
        </button>
        @endif

        @if($data['summary']['modules'] > 0)
        <button class="rp-nav-btn" data-sec="modules" onclick="showSec('modules')">
            <span class="left">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>
                Modules
            </span>
            <span class="rp-nav-badge">{{ $data['summary']['modules'] }}</span>
        </button>
        @endif

        @if($data['summary']['packages'] > 0)
        <button class="rp-nav-btn" data-sec="packages" onclick="showSec('packages')">
            <span class="left">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Packages
            </span>
            <span class="rp-nav-badge">{{ $data['summary']['packages'] }}</span>
        </button>
        @endif

        @if(($data['summary']['migrations']??0) > 0)
        <button class="rp-nav-btn" data-sec="migrations" onclick="showSec('migrations')">
            <span class="left">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/><path d="M4 12c0 2.21 3.582 4 8 4s8-1.79 8-4"/></svg>
                Migrations
            </span>
            <span class="rp-nav-badge">{{ $data['summary']['migrations'] }}</span>
        </button>
        @endif

        @php $mwNavCount = count(collect($data['routes'] ?? [])->flatMap(fn($r) => $r['middleware'] ?? [])->unique()->values()->all()); @endphp
        @if($mwNavCount > 0)
        <button class="rp-nav-btn" data-sec="middleware" onclick="showSec('middleware')">
            <span class="left">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                Middleware
            </span>
            <span class="rp-nav-badge">{{ $mwNavCount }}</span>
        </button>
        @endif

        @if(!empty($data['errors']))
        <button class="rp-nav-btn" data-sec="errors" onclick="showSec('errors')">
            <span class="left">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Errors
            </span>
            <span class="rp-nav-badge">{{ count($data['errors']) }}</span>
        </button>
        @endif
    </nav>

    <div class="rp-sidebar-footer">
        <div><span>Scan time</span><span>{{ $data['performance']['execution_time_ms'] }}ms</span></div>
        <div><span>Memory</span><span>{{ $data['performance']['memory_usage_mb'] }}MB</span></div>
        <div><span>Generated</span><span>{{ \Carbon\Carbon::parse($data['generated_at'])->format('d M Y') }}</span></div>
    </div>
</aside>

{{-- ── MAIN ── --}}
<main class="rp-main">

    <header class="rp-topbar">
        <div class="rp-topbar-left">
            <h2>
                {{ $data['project']['name'] }}
                <span class="rp-section-chip" id="sec-chip">Overview</span>
            </h2>
            <p>Laradar Report &nbsp;·&nbsp; {{ \Carbon\Carbon::parse($data['generated_at'])->format('d M Y \a\t H:i') }}</p>
        </div>
    </header>

    <div class="rp-content">

        {{-- ── OVERVIEW ── --}}
        <div id="sec-overview" class="rp-section active">
        @php
        $ovIcons = [
            'Models'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>',
            'Controllers'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>',
            'Routes'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
            'Jobs'         => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>',
            'Events'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>',
            'Services'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
            'Repositories' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>',
            'Observers'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>',
            'Policies'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
            'Modules'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
            'Middleware'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>',
            'Migrations'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12c0 2.21 3.582 4 8 4s8-1.79 8-4"/>',
            'Packages'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11l0 .01"/>',
        ];
        $ovStats = [
            ['Models',       $data['summary']['models']??0,       'models'],
            ['Controllers',  $data['summary']['controllers']??0,   'controllers'],
            ['Routes',       $rs['total']??0,                      'routes'],
            ['Migrations',   $data['summary']['migrations']??0,   'migrations'],
            ['Jobs',         $data['summary']['jobs']??0,          'jobs'],
            ['Events',       $data['summary']['events']??0,        'events'],
            ['Services',     $data['summary']['services']??0,      'services'],
            ['Repositories', $data['summary']['repositories']??0,  'repositories'],
            ['Observers',    $data['summary']['observers']??0,     'observers'],
            ['Policies',     $data['summary']['policies']??0,      'policies'],
            ['Modules',      $data['summary']['modules']??0,       'modules'],
            ['Middleware',   count($rs['middleware_usage']??[]),   'middleware'],
            ['Packages',     $data['summary']['packages']??0,      'packages'],
        ];
        $perf = $data['performance'] ?? [];
        @endphp

        {{-- Architecture score --}}
        @if(!empty($sc))
        <div class="ov-score-wrap">
            <div class="ov-ring-box">
                <svg viewBox="0 0 100 100" width="88" height="88">
                    <circle cx="50" cy="50" r="40" fill="none" stroke="var(--border)" stroke-width="10"/>
                    <circle cx="50" cy="50" r="40" fill="none" stroke="{{ $ringColor }}"
                        stroke-width="10" stroke-linecap="round"
                        stroke-dasharray="{{ $circumf }}" stroke-dashoffset="{{ $offset }}"/>
                </svg>
                <div class="ov-ring-inner">
                    <span style="font-size:22px;font-weight:800;color:var(--text);line-height:1;">{{ $sc['score'] }}</span>
                    <span style="font-size:10px;color:var(--text-faint);font-family:var(--font-mono);">/{{ $sc['max'] }}</span>
                </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:4px;justify-content:center;">
                <p style="font-size:11px;color:var(--text-faint);">Architecture Score</p>
                <span style="font-size:15px;font-weight:800;color:{{ $gradeColor }};background:{{ $gradeBg }};border:1px solid {{ $gradeBdr }};padding:3px 14px;border-radius:8px;display:inline-block;width:fit-content;">{{ $sc['grade'] }}</span>
                <p style="font-size:11px;color:var(--text-faint);margin-top:4px;">{{ $data['project']['name'] }} &mdash; {{ \Carbon\Carbon::parse($data['generated_at'])->format('d M Y') }}</p>
            </div>
            <div style="flex:1;min-width:220px;display:grid;grid-template-columns:1fr 1fr;gap:6px 24px;align-content:start;">
                @foreach($sc['checks'] as $check)
                @php [$ick,$icc] = match($check['status']??'fail'){'pass'=>['✔','var(--emerald)'],'warn'=>['⚠','var(--amber)'],default=>['✘','var(--rose)']}; @endphp
                <div style="display:flex;align-items:flex-start;gap:6px;">
                    <span style="font-weight:700;color:{{ $icc }};flex:none;margin-top:1px;">{{ $ick }}</span>
                    <div>
                        <span style="font-size:12px;color:var(--text-dim);">{{ $check['label'] }}</span>
                        @if(!empty($check['note']))<span style="font-size:10.5px;color:var(--text-faint);display:block;font-family:var(--font-mono);">{{ $check['note'] }}</span>@endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- KPI cards --}}
        <div class="ov-kpi-grid">
            @foreach($ovStats as [$label, $count, $sec])
            <div class="ov-kpi-card" onclick="showSec('{{ $sec }}')">
                <div class="ov-kpi-icon" style="background:var(--brand-bg);color:var(--brand);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">{!! $ovIcons[$label] ?? '' !!}</svg>
                </div>
                <span class="ov-kpi-label">{{ $label }}</span>
                <span class="ov-kpi-num">{{ $count }}</span>
            </div>
            @endforeach
        </div>

        {{-- Charts row --}}
        <div class="ov-cards-row">

            {{-- Route breakdown --}}
            <div class="ov-card">
                <p class="ov-card-hd">Route Breakdown</p>
                <div style="display:flex;flex-direction:column;gap:10px;font-size:13px;">
                    <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-faint);">Total</span><span style="font-family:var(--font-mono);font-weight:600;color:var(--text);">{{ $rs['total']??0 }}</span></div>
                    @foreach($rs['by_group']??[] as $group => $cnt)
                    <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-faint);">{{ ucfirst($group) }}</span><span style="font-family:var(--font-mono);font-weight:600;color:var(--text);">{{ $cnt }}</span></div>
                    @endforeach
                    <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-faint);">Named</span><span style="font-family:var(--font-mono);font-weight:600;color:var(--text);">{{ $rs['named_count']??0 }} / {{ $rs['total']??0 }}</span></div>
                    @if(!empty($rs['api_versions']))
                    <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-faint);">API Versions</span><span style="font-family:var(--font-mono);font-weight:600;color:var(--text);">{{ implode(', ', array_keys($rs['api_versions'])) }}</span></div>
                    @endif
                </div>
                @if(!empty($rs['by_method']))
                <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--border);">
                    <p style="font-family:var(--font-mono);font-size:10px;letter-spacing:.1em;text-transform:uppercase;color:var(--text-faint);margin-bottom:10px;">By Method</p>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;">
                        @foreach($rs['by_method'] as $method => $cnt)
                        @php $mc = strtolower($method); @endphp
                        <span class="rp-pill method-{{ $mc }}" style="font-family:var(--font-mono);font-size:11px;padding:3px 9px;">{{ strtoupper($method) }} {{ $cnt }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Performance --}}
            <div class="ov-card">
                <p class="ov-card-hd">Scan Performance</p>
                <div style="display:flex;flex-direction:column;gap:20px;">
                    <div>
                        <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:8px;">
                            <span style="color:var(--text-faint);">Scan Time</span>
                            <span style="font-family:var(--font-mono);color:var(--brand);">{{ $perf['execution_time_ms']??0 }} ms</span>
                        </div>
                        @php $timePct = min(100, ($perf['execution_time_ms']??0) / 50); @endphp
                        <div class="ov-score-bar"><div class="ov-score-fill" style="width:{{ $timePct }}%;background:linear-gradient(90deg,var(--brand),#FF5349);"></div></div>
                    </div>
                    <div>
                        <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:8px;">
                            <span style="color:var(--text-faint);">Memory</span>
                            <span style="font-family:var(--font-mono);color:var(--emerald);">{{ $perf['memory_usage_mb']??0 }} MB</span>
                        </div>
                        @php $memPct = min(100, ($perf['memory_usage_mb']??0) / 1.28); @endphp
                        <div class="ov-score-bar"><div class="ov-score-fill" style="width:{{ $memPct }}%;background:var(--emerald);"></div></div>
                    </div>
                    <div style="padding-top:16px;border-top:1px solid var(--border);display:flex;flex-direction:column;gap:8px;font-size:12px;">
                        <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-faint);">Laravel</span><span style="font-family:var(--font-mono);color:var(--text);">{{ $data['laravel_version'] }}</span></div>
                        <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-faint);">PHP</span><span style="font-family:var(--font-mono);color:var(--text);">{{ $data['php_version'] }}</span></div>
                        <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-faint);">Laradar</span><span style="font-family:var(--font-mono);color:var(--text);">v{{ $data['package_version'] }}</span></div>
                    </div>
                </div>
            </div>

            {{-- Score checks --}}
            @if(!empty($sc['checks']))
            <div class="ov-card">
                <p class="ov-card-hd">Score Checks</p>
                <div style="display:flex;flex-direction:column;gap:12px;">
                    @foreach($sc['checks'] as $check)
                    @php [$ick,$icc] = match($check['status']??'fail'){'pass'=>['✔','var(--emerald)'],'warn'=>['⚠','var(--amber)'],default=>['✘','var(--rose)']}; @endphp
                    <div style="display:flex;align-items:flex-start;gap:10px;">
                        <span style="font-weight:700;font-size:13px;color:{{ $icc }};flex:none;margin-top:1px;">{{ $ick }}</span>
                        <div>
                            <p style="font-size:13px;color:var(--text);">{{ $check['label'] }}</p>
                            @if(!empty($check['note']))<p style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);margin-top:2px;">{{ $check['note'] }}</p>@endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
        </div>

        {{-- ── MODELS ── --}}
        <div id="sec-models" class="rp-section">
            <div class="rp-section-hd">
                <h3>Models</h3>
                <span class="count">{{ $data['summary']['models'] }}</span>
            </div>
            <div class="rp-table-wrap">
                <table class="rp-table">
                    <thead><tr><th>Model</th><th>Table</th><th>Relationships</th><th>Fillable</th><th>Traits</th><th>Hidden</th></tr></thead>
                    <tbody>
                        @foreach($data['models'] as $model)
                        <tr>
                            <td>
                                <div style="font-weight:700;color:var(--text);">{{ $model['name'] }}</div>
                                <div style="font-size:10px;color:var(--text-faint);font-family:var(--font-mono);">{{ $model['namespace'] ?? '' }}</div>
                            </td>
                            <td><span class="rp-pill" style="color:var(--brand);background:var(--brand-bg);border-color:var(--brand-border);">{{ $model['table'] }}</span></td>
                            <td>
                                @if(!empty($model['relationships']))
                                <div style="display:flex;flex-direction:column;gap:3px;">
                                    @foreach($model['relationships'] as $rel)
                                    <div style="display:flex;align-items:center;gap:5px;font-size:11px;">
                                        <span class="rp-pill" style="color:var(--brand);background:var(--brand-bg);border-color:var(--brand-border);">{{ $rel['type'] }}</span>
                                        <span style="color:var(--text-dim);font-weight:600;">{{ class_basename($rel['related'] ?? '—') }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                @else<span style="color:var(--text-faint);">—</span>@endif
                            </td>
                            <td>
                                @if(!empty($model['fillable']))
                                <div style="display:flex;flex-wrap:wrap;gap:3px;">
                                    @foreach($model['fillable'] as $f)
                                    <span style="font-family:var(--font-mono);font-size:10px;background:var(--bg-sunken);color:var(--text-dim);padding:1px 6px;border-radius:4px;border:1px solid var(--border);">{{ $f }}</span>
                                    @endforeach
                                </div>
                                @else<span style="color:var(--text-faint);">—</span>@endif
                            </td>
                            <td style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);">{{ count($model['traits'] ?? []) }}</td>
                            <td style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);">{{ count($model['hidden'] ?? []) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── CONTROLLERS ── --}}
        <div id="sec-controllers" class="rp-section">
            <div class="rp-section-hd">
                <h3>Controllers</h3>
                <span class="count">{{ $data['summary']['controllers'] }}</span>
            </div>
            <div class="rp-table-wrap">
                <table class="rp-table">
                    <thead><tr><th>Controller</th><th>Namespace</th><th>Methods</th><th>Method Names</th></tr></thead>
                    <tbody>
                        @foreach($data['controllers'] as $ctrl)
                        <tr>
                            <td style="font-weight:700;color:var(--text);">{{ $ctrl['name'] }}</td>
                            <td style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);">{{ $ctrl['namespace'] ?? '' }}</td>
                            <td><span class="rp-pill" style="color:var(--brand);background:var(--brand-bg);border-color:var(--brand-border);">{{ $ctrl['method_count'] }}</span></td>
                            <td>
                                @if(!empty($ctrl['methods']))
                                <div style="display:flex;flex-wrap:wrap;gap:4px;">
                                    @foreach($ctrl['methods'] as $m)
                                    <span style="font-family:var(--font-mono);font-size:10px;background:var(--bg-sunken);color:var(--text-dim);padding:1px 6px;border-radius:4px;border:1px solid var(--border);">{{ $m }}</span>
                                    @endforeach
                                </div>
                                @else<span style="color:var(--text-faint);">—</span>@endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── ROUTES ── --}}
        <div id="sec-routes" class="rp-section">
            <div class="rp-section-hd">
                <h3>Routes</h3>
                <span class="count">{{ $data['summary']['routes'] }}</span>
            </div>
            <div class="rp-toolbar">
                <button class="rp-filter-btn active" data-method="all" onclick="filterRoutes('all',this)">All</button>
                @foreach(array_keys($rs['by_method'] ?? []) as $m)
                <button class="rp-filter-btn" data-method="{{ strtoupper($m) }}" onclick="filterRoutes('{{ strtoupper($m) }}',this)">{{ strtoupper($m) }}</button>
                @endforeach
                <input id="route-search" class="rp-search" type="search" placeholder="Search URI, name, controller…" oninput="filterRoutes(null,null)">
                <span style="margin-left:auto;font-size:11px;color:var(--text-faint);font-family:var(--font-mono);">{{ $data['summary']['routes'] }} routes</span>
            </div>
            <div class="rp-table-wrap" style="overflow-x:auto;">
                <table class="rp-table" style="min-width:820px;">
                    <thead>
                        <tr>
                            <th>Method</th><th>URI</th><th>Controller</th><th>Action</th><th>Name</th><th>Middleware</th>
                        </tr>
                    </thead>
                    <tbody id="routes-body">
                        @foreach($data['routes'] as $route)
                        @php
                        $methods = array_values(array_filter($route['methods'] ?? [], fn($m) => $m !== 'HEAD'));
                        $primary = strtolower($methods[0] ?? 'get');
                        $mws = $route['middleware'] ?? [];
                        @endphp
                        <tr class="route-row" data-methods="{{ implode(',', array_map('strtoupper', $methods)) }}" data-uri="{{ strtolower($route['uri']) }}" data-name="{{ strtolower($route['name'] ?? '') }}" data-ctrl="{{ strtolower(class_basename($route['controller']['class'] ?? '')) }}">
                            <td><span class="rp-pill method-{{ $primary }}" style="font-size:10px;font-weight:700;">{{ strtoupper($primary) }}</span></td>
                            <td style="font-family:var(--font-mono);color:var(--text);">{{ $route['uri'] }}</td>
                            <td>{{ class_basename($route['controller']['class'] ?? '—') }}</td>
                            <td style="font-family:var(--font-mono);color:var(--text-faint);">{{ $route['controller']['method'] ?? '—' }}</td>
                            <td style="color:var(--text-faint);">{{ $route['name'] ?? '—' }}</td>
                            <td>
                                @if(empty($mws))<span style="color:var(--text-faint);">—</span>
                                @else
                                <div style="display:flex;flex-wrap:wrap;gap:4px;">
                                    @foreach($mws as $mw)
                                    <span style="font-size:10px;background:var(--bg-sunken);color:var(--text-dim);padding:1px 6px;border-radius:4px;border:1px solid var(--border);">{{ $mw }}</span>
                                    @endforeach
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="routes-empty" style="display:none;text-align:center;padding:40px;color:var(--text-faint);font-size:13px;">No routes match your search.</div>
            </div>
        </div>

        {{-- ── JOBS ── --}}
        @if($data['summary']['jobs'] > 0)
        <div id="sec-jobs" class="rp-section">
            <div class="rp-section-hd"><h3>Jobs</h3><span class="count">{{ $data['summary']['jobs'] }}</span></div>
            <div class="rp-table-wrap">
                <table class="rp-table">
                    <thead><tr><th>Job</th><th>Namespace</th><th>Queue</th><th>Tries</th><th>Timeout</th></tr></thead>
                    <tbody>
                        @foreach($data['jobs'] as $item)
                        <tr>
                            <td style="font-weight:700;color:var(--text);">{{ $item['name'] }}</td>
                            <td style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);">{{ $item['namespace'] ?? '—' }}</td>
                            <td><span class="rp-pill" style="color:var(--brand);background:var(--brand-bg);border-color:var(--brand-border);">{{ $item['queue'] ?? 'default' }}</span></td>
                            <td style="font-family:var(--font-mono);color:var(--text-faint);">{{ $item['tries'] ?? '—' }}</td>
                            <td style="font-family:var(--font-mono);color:var(--text-faint);">{{ $item['timeout'] ? $item['timeout'].'s' : '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ── EVENTS ── --}}
        @if($data['summary']['events'] > 0)
        <div id="sec-events" class="rp-section">
            <div class="rp-section-hd"><h3>Events</h3><span class="count">{{ $data['summary']['events'] }}</span></div>
            <div class="rp-table-wrap">
                <table class="rp-table">
                    <thead><tr><th>Event</th><th>Namespace</th><th>Listeners</th><th>Properties</th></tr></thead>
                    <tbody>
                        @foreach($data['events'] as $item)
                        <tr>
                            <td style="font-weight:700;color:var(--text);">{{ $item['name'] }}</td>
                            <td style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);">{{ $item['namespace'] ?? '—' }}</td>
                            <td style="font-family:var(--font-mono);color:var(--text-faint);">{{ count($item['listeners'] ?? []) }}</td>
                            <td style="font-family:var(--font-mono);color:var(--text-faint);">{{ count($item['properties'] ?? []) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ── SERVICES ── --}}
        @if($data['summary']['services'] > 0)
        <div id="sec-services" class="rp-section">
            <div class="rp-section-hd"><h3>Services</h3><span class="count">{{ $data['summary']['services'] }}</span></div>
            <div class="rp-table-wrap">
                <table class="rp-table">
                    <thead><tr><th>Service</th><th>Namespace</th><th>Methods</th></tr></thead>
                    <tbody>
                        @foreach($data['services'] as $item)
                        <tr>
                            <td style="font-weight:700;color:var(--text);">{{ $item['name'] }}</td>
                            <td style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);">{{ $item['namespace'] ?? '—' }}</td>
                            <td><span class="rp-pill" style="color:var(--brand);background:var(--brand-bg);border-color:var(--brand-border);">{{ count($item['methods'] ?? []) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ── REPOSITORIES ── --}}
        @if($data['summary']['repositories'] > 0)
        <div id="sec-repositories" class="rp-section">
            <div class="rp-section-hd"><h3>Repositories</h3><span class="count">{{ $data['summary']['repositories'] }}</span></div>
            <div class="rp-table-wrap">
                <table class="rp-table">
                    <thead><tr><th>Repository</th><th>Namespace</th><th>Methods</th></tr></thead>
                    <tbody>
                        @foreach($data['repositories'] as $item)
                        <tr>
                            <td style="font-weight:700;color:var(--text);">{{ $item['name'] }}</td>
                            <td style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);">{{ $item['namespace'] ?? '—' }}</td>
                            <td><span class="rp-pill" style="color:var(--brand);background:var(--brand-bg);border-color:var(--brand-border);">{{ count($item['methods'] ?? []) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ── OBSERVERS ── --}}
        @if($data['summary']['observers'] > 0)
        <div id="sec-observers" class="rp-section">
            <div class="rp-section-hd"><h3>Observers</h3><span class="count">{{ $data['summary']['observers'] }}</span></div>
            <div class="rp-table-wrap">
                <table class="rp-table">
                    <thead><tr><th>Observer</th><th>Namespace</th><th>Observed Model</th><th>Events</th></tr></thead>
                    <tbody>
                        @foreach($data['observers'] as $item)
                        <tr>
                            <td style="font-weight:700;color:var(--text);">{{ $item['name'] }}</td>
                            <td style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);">{{ $item['namespace'] ?? '—' }}</td>
                            <td><span class="rp-pill" style="color:var(--brand);background:var(--brand-bg);border-color:var(--brand-border);">{{ class_basename($item['model'] ?? '—') }}</span></td>
                            <td style="font-family:var(--font-mono);color:var(--text-faint);">{{ implode(', ', $item['events'] ?? []) ?: '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ── POLICIES ── --}}
        @if($data['summary']['policies'] > 0)
        <div id="sec-policies" class="rp-section">
            <div class="rp-section-hd"><h3>Policies</h3><span class="count">{{ $data['summary']['policies'] }}</span></div>
            <div class="rp-table-wrap">
                <table class="rp-table">
                    <thead><tr><th>Policy</th><th>Namespace</th><th>Model</th><th>Actions</th></tr></thead>
                    <tbody>
                        @foreach($data['policies'] as $item)
                        <tr>
                            <td style="font-weight:700;color:var(--text);">{{ $item['name'] }}</td>
                            <td style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);">{{ $item['namespace'] ?? '—' }}</td>
                            <td><span class="rp-pill" style="color:var(--text-dim);background:var(--bg-sunken);border-color:var(--border);">{{ class_basename($item['model'] ?? '—') }}</span></td>
                            <td>
                                <div style="display:flex;flex-wrap:wrap;gap:4px;">
                                    @foreach(array_slice($item['actions'] ?? [], 0, 6) as $a)
                                    <span style="font-family:var(--font-mono);font-size:10px;background:var(--bg-sunken);color:var(--text-faint);padding:1px 6px;border-radius:4px;border:1px solid var(--border);">{{ $a }}</span>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ── MODULES ── --}}
        @if($data['summary']['modules'] > 0)
        <div id="sec-modules" class="rp-section">
            <div class="rp-section-hd"><h3>Modules</h3><span class="count">{{ $data['summary']['modules'] }}</span></div>
            <div class="rp-table-wrap">
                <table class="rp-table">
                    <thead><tr><th>Module</th><th>Path</th><th>Routes</th></tr></thead>
                    <tbody>
                        @foreach($data['modules'] as $item)
                        <tr>
                            <td style="font-weight:700;color:var(--text);">{{ $item['name'] }}</td>
                            <td style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);">{{ $item['path'] ?? '—' }}</td>
                            <td style="font-family:var(--font-mono);color:var(--text-faint);">{{ $item['routes'] ?? 0 }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ── MIGRATIONS ── --}}
        @if(!empty($data['migrations']))
        <div id="sec-migrations" class="rp-section">
            <div class="rp-section-hd"><h3>Migrations</h3><span class="count">{{ count($data['migrations']) }}</span></div>
            @php
            $rpOpMeta = [
                'create'  => ['label'=>'CREATE', 'color'=>'#ffffff', 'bg'=>'#FF2D20',             'border'=>'#FF2D20'],
                'modify'  => ['label'=>'MODIFY', 'color'=>'#FF2D20', 'bg'=>'rgba(255,45,32,.10)', 'border'=>'rgba(255,45,32,.35)'],
                'drop'    => ['label'=>'DROP',   'color'=>'#FF2D20', 'bg'=>'rgba(255,45,32,.10)', 'border'=>'rgba(255,45,32,.35)'],
                'rename'  => ['label'=>'RENAME', 'color'=>'#FF2D20', 'bg'=>'rgba(255,45,32,.10)', 'border'=>'rgba(255,45,32,.35)'],
                'unknown' => ['label'=>'?',      'color'=>'#94A3B8', 'bg'=>'rgba(148,163,184,.1)','border'=>'rgba(148,163,184,.3)'],
            ];
            @endphp
            @foreach($data['migrations'] as $mg)
            @php $opm = $rpOpMeta[$mg['operation']??'unknown'] ?? $rpOpMeta['unknown']; $cols = $mg['columns']??[]; $fks = $mg['foreign_keys']??[]; @endphp
            <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:10px;margin-bottom:10px;overflow:hidden;">
                <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;flex-wrap:wrap;">
                    <span style="font-size:10px;font-weight:700;font-family:var(--font-mono);padding:3px 9px;border-radius:5px;white-space:nowrap;flex:none;color:{{ $opm['color'] }};background:{{ $opm['bg'] }};border:1px solid {{ $opm['border'] }};">{{ $opm['label'] }}</span>
                    @if($mg['table'])<span style="font-family:var(--font-mono);font-size:13px;font-weight:700;color:var(--text);">{{ $mg['table'] }}</span>@endif
                    <span style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $mg['filename']??'' }}</span>
                    @if($mg['date'])<span style="font-size:11px;font-family:var(--font-mono);color:var(--text-faint);background:var(--bg-sunken);border:1px solid var(--border);padding:2px 8px;border-radius:5px;white-space:nowrap;">{{ $mg['date'] }}</span>@endif
                    @if(count($cols)>0)<span style="font-size:11px;font-family:var(--font-mono);color:#FF2D20;background:rgba(255,45,32,.08);border:1px solid rgba(255,45,32,.2);padding:2px 8px;border-radius:5px;white-space:nowrap;">{{ count($cols) }} cols</span>@endif
                    @if(count($fks)>0)<span style="font-size:11px;font-family:var(--font-mono);color:#FF2D20;background:rgba(255,45,32,.08);border:1px solid rgba(255,45,32,.2);padding:2px 8px;border-radius:5px;white-space:nowrap;">{{ count($fks) }} FKs</span>@endif
                </div>
                @if(count($cols)>0)
                <div style="overflow-x:auto;border-top:1px solid var(--border);">
                    <table class="rp-table" style="font-size:11px;">
                        <thead><tr><th>#</th><th>Column</th><th>Type</th><th>Nullable</th><th>Modifiers</th></tr></thead>
                        <tbody>
                        @foreach($cols as $ci => $col)
                        <tr>
                            <td style="color:var(--text-faint);width:32px;">{{ $ci+1 }}</td>
                            <td style="font-family:var(--font-mono);font-weight:600;color:var(--text);">
                                {{ $col['name'] }}
                                @if($col['type']==='id'||str_contains($col['type'],'Increments')||in_array('primary',$col['modifiers']??[]))<span style="font-size:9px;font-weight:700;padding:1px 5px;border-radius:3px;background:rgba(255,45,32,.1);color:#FF2D20;border:1px solid rgba(255,45,32,.2);margin-left:4px;">PK</span>@endif
                            </td>
                            <td><code style="font-family:var(--font-mono);font-size:10px;color:#FF2D20;background:rgba(255,45,32,.07);padding:1px 6px;border-radius:3px;border:1px solid rgba(255,45,32,.18);">{{ $col['type'] }}</code></td>
                            <td style="color:{{ $col['nullable'] ? '#FF2D20' : 'var(--text-faint)' }};">{{ $col['nullable'] ? '✓' : '—' }}</td>
                            <td style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);">
                                @php $dm = array_filter($col['modifiers']??[], fn($m)=>$m!=='nullable'); @endphp
                                {{ implode(', ', $dm) ?: '—' }}
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
                @if(count($fks)>0)
                <div style="padding:10px 16px;border-top:1px solid var(--border);display:flex;flex-wrap:wrap;gap:8px;align-items:center;">
                    <span style="font-size:10px;font-weight:700;font-family:var(--font-mono);color:var(--text-faint);text-transform:uppercase;letter-spacing:.05em;">FK:</span>
                    @foreach($fks as $fk)
                    <span style="font-family:var(--font-mono);font-size:11px;background:var(--bg-sunken);border:1px solid var(--border);border-radius:6px;padding:3px 10px;">
                        <span style="color:var(--text);font-weight:600;">{{ $fk['column'] }}</span>
                        <span style="color:#FF2D20;margin:0 4px;">→</span>
                        <span style="color:#FF2D20;">{{ $fk['on'] }}.{{ $fk['references'] }}</span>
                    </span>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        {{-- ── MIDDLEWARE ── --}}
        @php
        $middlewareList = [];
        $seenMw = [];
        foreach(($data['routes'] ?? []) as $route) {
            foreach(($route['middleware'] ?? []) as $mw) {
                if (!isset($seenMw[$mw])) {
                    $seenMw[$mw] = true;
                    $type = in_array($mw, ['web', 'api']) ? 'group'
                          : (str_starts_with($mw, 'auth') ? 'auth'
                          : (str_contains($mw, ':') ? 'parametric' : 'named'));
                    $middlewareList[] = ['name' => explode(':', $mw)[0], 'full' => $mw, 'type' => $type];
                }
            }
        }
        usort($middlewareList, fn($a,$b) => strcmp($a['name'], $b['name']));
        @endphp
        @if(count($middlewareList) > 0)
        <div id="sec-middleware" class="rp-section">
            <div class="rp-section-hd"><h3>Middleware</h3><span class="count">{{ count($middlewareList) }}</span></div>
            <div class="rp-table-wrap">
                <table class="rp-table">
                    <thead><tr><th>Alias</th><th>Full Value</th><th>Type</th></tr></thead>
                    <tbody>
                        @foreach($middlewareList as $mw)
                        <tr>
                            <td style="font-weight:700;color:var(--text);font-family:var(--font-mono);">{{ $mw['name'] }}</td>
                            <td style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);">{{ $mw['full'] }}</td>
                            <td><span class="rp-pill" style="color:var(--brand);background:var(--brand-bg);border-color:var(--brand-border);">{{ $mw['type'] }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ── PACKAGES ── --}}
        @if($data['summary']['packages'] > 0)
        <div id="sec-packages" class="rp-section">
            <div class="rp-section-hd"><h3>Packages</h3><span class="count">{{ $data['summary']['packages'] }}</span></div>
            <div class="rp-table-wrap">
                <table class="rp-table">
                    <thead><tr><th>Package</th><th>Version</th><th>Type</th><th>Description</th></tr></thead>
                    <tbody>
                        @foreach($data['packages'] as $pkg)
                        @php $pkgType = $pkg['type'] ?? 'library'; @endphp
                        <tr>
                            <td style="font-weight:700;color:var(--text);">{{ $pkg['name'] }}</td>
                            <td style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);">{{ $pkg['version'] ?? '—' }}</td>
                            <td><span class="rp-pill" style="{{ $pkgType === 'laravel-package' ? 'color:var(--brand);background:var(--brand-bg);border-color:var(--brand-border);' : 'color:var(--text-faint);background:var(--bg-sunken);border-color:var(--border);' }}">{{ $pkgType }}</span></td>
                            <td style="font-size:11.5px;color:var(--text-faint);">{{ $pkg['description'] ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ── ERRORS ── --}}
        @if(!empty($data['errors']))
        <div id="sec-errors" class="rp-section">
            <div class="rp-section-hd"><h3>Errors</h3><span class="count">{{ count($data['errors']) }}</span></div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($data['errors'] as $err)
                <div class="rp-card rp-card-body" style="border-left:3px solid var(--rose);">
                    <p style="font-size:13px;color:var(--text);">{{ is_string($err) ? $err : ($err['message'] ?? json_encode($err)) }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <footer class="rp-footer" style="margin-top:40px;">
            Generated by <strong>Laradar</strong> &nbsp;·&nbsp; {{ $data['performance']['execution_time_ms'] }}ms &nbsp;·&nbsp; {{ $data['performance']['memory_usage_mb'] }}MB
        </footer>

    </div>
</main>
</div>

<script>
const SEC_LABELS = {
    overview:'Overview', models:'Models', controllers:'Controllers', routes:'Routes',
    jobs:'Jobs', events:'Events', services:'Services', repositories:'Repositories',
    observers:'Observers', policies:'Policies', modules:'Modules', migrations:'Migrations', middleware:'Middleware',
    packages:'Packages', errors:'Errors'
};

function showSec(id) {
    document.querySelectorAll('.rp-section').forEach(s => s.classList.remove('active'));
    const t = document.getElementById('sec-' + id);
    if (t) t.classList.add('active');

    document.querySelectorAll('.rp-nav-btn').forEach(b => b.classList.remove('active'));
    const nb = document.querySelector(`[data-sec="${id}"]`);
    if (nb) nb.classList.add('active');

    const chip = document.getElementById('sec-chip');
    if (chip) chip.textContent = SEC_LABELS[id] || id;
}

/* ── Route filtering ── */
let _activeMethod = 'all';
function filterRoutes(method, btn) {
    if (method !== null) {
        _activeMethod = method;
        document.querySelectorAll('.rp-filter-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
    }
    const q = (document.getElementById('route-search')?.value || '').toLowerCase();
    let visible = 0;
    document.querySelectorAll('.route-row').forEach(row => {
        const methodOk = _activeMethod === 'all' || row.dataset.methods.includes(_activeMethod);
        const textOk   = !q || row.dataset.uri.includes(q) || row.dataset.name.includes(q) || row.dataset.ctrl.includes(q);
        const show = methodOk && textOk;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    const empty = document.getElementById('routes-empty');
    if (empty) empty.style.display = visible === 0 ? '' : 'none';
}
</script>

</body>
</html>
