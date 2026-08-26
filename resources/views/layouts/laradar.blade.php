<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $data['project']['name'] }} — Laradar</title>
<link rel="icon" type="image/x-icon" href="{{ route('laradar.asset', ['filename' => 'favicon.ico']) }}">
<script>if(localStorage.getItem('laradar_sidebar')==='0'){document.documentElement.classList.add('sidebar-pre-collapsed');}</script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<style>
/* ── Laravel Theme CSS Variables ── */
:root{
  --bg:#FFFFFF; --bg-elevated:#FFFFFF; --bg-sunken:#F9F6EF; --bg-hover:#F9FAFB;
  --border:#E5E7EB; --border-strong:#D1D5DB; --grid-line:transparent;
  --text:#1D1D1F; --text-dim:#374151; --text-faint:#6B7280;
  --brand:#FF2D20; --brand-bg:rgba(255,45,32,0.08); --brand-border:rgba(255,45,32,0.20);
  --cyan:#FF2D20; --cyan-bright:#FF5349; --emerald:#16A34A; --amber:#D97706; --rose:#DC2626; --violet:#7C3AED; --sky:#2563EB;
  --red:#FF2D20; --red-dim:rgba(255,45,32,0.08); --red-border:rgba(255,45,32,0.25);
  --shadow:0 1px 3px rgba(0,0,0,0.08),0 4px 16px rgba(0,0,0,0.06);
  --shadow-hover:0 4px 24px rgba(0,0,0,0.12),0 1px 4px rgba(0,0,0,0.08);
  --laravel-red:#FF2D20; --font-sans:'Instrument Sans',sans-serif; --font-mono:'JetBrains Mono',monospace;
  --ease:cubic-bezier(.22,.61,.36,1);
}

/* ── Body ── */
body{
  margin:0;background:var(--bg);color:var(--text);font-family:var(--font-sans);font-size:14.5px;-webkit-font-smoothing:antialiased;
}

/* ── Layout ── */
.atlas-layout{display:grid;grid-template-columns:264px 1fr;height:100vh;overflow:hidden;transition:grid-template-columns .3s cubic-bezier(.4,0,.2,1);}
.atlas-layout.sidebar-collapsed,.sidebar-pre-collapsed .atlas-layout{grid-template-columns:64px 1fr;}

/* ── Sidebar collapse toggle button (fixed on sidebar right edge) ── */
#sidebar-collapse-btn{position:fixed;left:252px;top:52px;width:24px;height:24px;border-radius:50%;background:var(--bg-elevated);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:95;box-shadow:0 2px 8px rgba(0,0,0,.12);transition:left .3s cubic-bezier(.4,0,.2,1),background .15s,border-color .15s;color:#FF2D20;}
#sidebar-collapse-btn:hover{background:var(--bg-hover);border-color:rgba(255,45,32,.4);}
.atlas-layout.sidebar-collapsed #sidebar-collapse-btn,
.sidebar-pre-collapsed #sidebar-collapse-btn{left:52px;}
.atlas-layout.sidebar-collapsed #sidebar-collapse-btn svg,
.sidebar-pre-collapsed #sidebar-collapse-btn svg{transform:scaleX(-1);}

/* ── Sidebar collapsed: icon-only mode ── */
.atlas-layout.sidebar-collapsed .sidebar,
.sidebar-pre-collapsed .sidebar{padding:20px 8px;}
.atlas-layout.sidebar-collapsed .sidebar__brand,
.sidebar-pre-collapsed .sidebar__brand{justify-content:center;padding-bottom:16px;}
.atlas-layout.sidebar-collapsed .sidebar__brand div:last-child,
.sidebar-pre-collapsed .sidebar__brand div:last-child{display:none;}
.atlas-layout.sidebar-collapsed .score-spin-border,
.sidebar-pre-collapsed .score-spin-border{display:none;}
.atlas-layout.sidebar-collapsed .nav-group__label,
.sidebar-pre-collapsed .nav-group__label{display:none;}
.atlas-layout.sidebar-collapsed .nav-label,
.sidebar-pre-collapsed .nav-label{display:none;}
.atlas-layout.sidebar-collapsed .nav-badge,
.sidebar-pre-collapsed .nav-badge{display:none;}
.atlas-layout.sidebar-collapsed .nav-item,
.sidebar-pre-collapsed .nav-item{justify-content:center;padding:10px;gap:0;}
.content{overflow-y:auto;scrollbar-width:none;}
.content::-webkit-scrollbar{display:none;}
.section-pane{flex:1;display:flex;flex-direction:column;}

/* ── Radar Animation ── */
.radar{position:relative;width:18px;height:18px;display:inline-block;flex:none;}
.radar__ring{position:absolute;inset:0;border:1px solid var(--cyan);border-radius:50%;animation:radarPulse 2.4s var(--ease) infinite;}
.radar__ring--delay{animation-delay:1.2s;}
.radar__dot{position:absolute;inset:0;margin:auto;width:4px;height:4px;border-radius:50%;background:var(--cyan);}
.radar__sweep{position:absolute;inset:0;border-radius:50%;background:conic-gradient(from 0deg,rgba(255,45,32,0.45),transparent 40%);animation:radarSpin 2.2s linear infinite;}
@keyframes radarPulse{0%{transform:scale(.5);opacity:.7;}100%{transform:scale(1.9);opacity:0;}}
@keyframes radarSpin{to{transform:rotate(360deg);}}
@keyframes spin{to{transform:rotate(360deg);}}
@keyframes pulse{0%,100%{opacity:1;}50%{opacity:.4;}}
@keyframes fadeUp{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:none;}}
.p-6>*{animation:fadeUp .35s var(--ease) both;}

/* ── Sidebar ── */
.sidebar{background:var(--bg-elevated);border-right:1px solid var(--border);padding:24px 16px;display:flex;flex-direction:column;position:sticky;top:0;height:100vh;z-index:90;transition:transform .35s var(--ease);overflow-y:auto;scrollbar-width:none;}
.sidebar::-webkit-scrollbar{display:none;}
.atlas-layout.sidebar-collapsed .sidebar,
.atlas-layout.sidebar-collapsed .sidebar nav,
.sidebar-pre-collapsed .sidebar,
.sidebar-pre-collapsed .sidebar nav{scrollbar-width:none;}
.atlas-layout.sidebar-collapsed .sidebar::-webkit-scrollbar,
.atlas-layout.sidebar-collapsed .sidebar nav::-webkit-scrollbar,
.sidebar-pre-collapsed .sidebar::-webkit-scrollbar,
.sidebar-pre-collapsed .sidebar nav::-webkit-scrollbar{display:none;}
.sidebar__brand{display:flex;align-items:center;gap:10px;padding:6px 8px 22px;}
.sidebar__brand .mark{width:34px;height:34px;border-radius:8px;background:transparent;border:none;display:flex;align-items:center;justify-content:center;flex:none;overflow:hidden;}
.sidebar__brand div{line-height:1;}
.sidebar__brand strong{font-size:16px;font-weight:800;letter-spacing:0.04em;color:var(--text);text-transform:uppercase;}
.sidebar nav{flex:1;overflow-y:auto;scrollbar-width:none;}
.sidebar nav::-webkit-scrollbar{display:none;}
.nav-group{margin-bottom:20px;}
.nav-group__label{font-family:var(--font-mono);font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:var(--text-faint);padding:0 10px;margin-bottom:9px;display:block;}
.nav-item{display:flex;align-items:center;gap:11px;padding:9px 10px;border-radius:7px;margin-bottom:2px;color:var(--text-dim);font-size:13.5px;font-weight:600;position:relative;transition:background .2s,color .2s;cursor:pointer;border:none;background:none;width:100%;text-align:left;}
.nav-item svg{width:17px;height:17px;flex:none;stroke:currentColor;}
.nav-item:hover{background:rgba(255,45,32,0.04);color:var(--cyan);}
.nav-item.nav-active{background:rgba(255,45,32,0.06);color:var(--cyan);box-shadow:inset 3px 0 0 var(--cyan),inset 4px 0 12px rgba(255,45,32,0.06);}
#nav-indicator{position:fixed;left:0;width:3px;background:linear-gradient(180deg,transparent 0%,var(--cyan) 30%,var(--cyan) 70%,transparent 100%);border-radius:0 3px 3px 0;pointer-events:none;z-index:200;transition:top .38s cubic-bezier(.34,1.56,.64,1),height .22s var(--ease);box-shadow:1px 0 10px rgba(255,45,32,0.5),2px 0 4px rgba(255,45,32,0.2);}
#nav-indicator::after{content:'';position:absolute;left:3px;top:50%;transform:translateY(-50%);width:6px;height:6px;background:var(--cyan);border-radius:50%;box-shadow:0 0 8px rgba(255,45,32,0.8);}
.nav-badge{margin-left:auto;font-family:var(--font-mono);font-size:10px;background:rgba(255,45,32,0.08);color:#FF2D20;padding:2px 7px;border-radius:20px;border:1px solid rgba(255,45,32,0.15);}

/* ── Topbar ── */
.topbar{position:sticky;top:0;z-index:60;background:rgba(255,255,255,0.92);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);display:flex;align-items:center;gap:16px;padding:16px 30px;box-shadow:0 1px 0 var(--border);}
.breadcrumb{font-family:var(--font-sans);font-size:12px;color:var(--text-faint);display:flex;align-items:center;gap:8px;}
.breadcrumb b{color:var(--text);font-weight:800;font-size:20px;letter-spacing:-0.01em;}
.sync-pill{display:flex;align-items:center;gap:8px;font-family:var(--font-mono);font-size:11.5px;color:var(--text-dim);border:1px solid var(--border);border-radius:20px;padding:6px 12px 6px 10px;margin-left:auto;}
.sync-dot{width:6px;height:6px;border-radius:50%;background:var(--emerald);box-shadow:0 0 0 3px rgba(52,211,153,0.18);flex:none;}

/* ── Atlas Cards ── */
.atlas-card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;padding:22px;box-shadow:var(--shadow);}
.atlas-card__head{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:10px;}
.atlas-card__head h3{font-size:15.5px;font-weight:700;margin:0;}

/* ── KPI Cards ── */
.kpi-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:16px;margin-bottom:22px;}
.kpi-card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;padding:20px;transition:transform .25s var(--ease),box-shadow .25s var(--ease),border-color .25s;cursor:default;box-shadow:var(--shadow);}
.kpi-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-hover);border-color:var(--border-strong);}
.kpi-card__icon{width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex:none;margin-bottom:12px;}
.kpi-card__icon svg{width:17px;height:17px;stroke:currentColor;fill:none;}
.kpi-card__label{font-family:var(--font-mono);font-size:10.5px;letter-spacing:0.06em;text-transform:uppercase;color:var(--text-faint);display:block;}
.kpi-card__num{font-family:var(--font-mono);font-size:28px;letter-spacing:-0.01em;margin-top:4px;display:block;}

/* ── Back Button ── */
.back-btn{display:inline-flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:var(--cyan);background:rgba(255,45,32,0.06);border:1px solid rgba(255,45,32,0.22);border-radius:8px;padding:8px 14px;cursor:pointer;margin-bottom:24px;font-family:var(--font-sans);transition:background .18s,border-color .18s,transform .18s;}
.back-btn:hover{background:rgba(255,45,32,0.12);border-color:rgba(255,45,32,0.4);transform:translateX(-2px);}
.back-btn svg{width:15px;height:15px;flex:none;transition:transform .18s;}
.back-btn:hover svg{transform:translateX(-2px);}

/* ── Controller Cards ── */
.ctrl-card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;padding:22px;cursor:pointer;transition:box-shadow .25s var(--ease),border-color .25s;box-shadow:var(--shadow);}
.ctrl-card:hover{box-shadow:var(--shadow-hover);border-color:rgba(255,139,0,0.4);}
.ctrl-card__icon{width:38px;height:38px;border-radius:9px;background:rgba(255,139,0,0.10);color:var(--amber);display:flex;align-items:center;justify-content:center;flex:none;}
.ctrl-card__icon svg{width:18px;height:18px;stroke:currentColor;fill:none;}
.ctrl-card__name{font-family:var(--font-mono);font-size:15px;font-weight:600;color:var(--text);}
.ctrl-card__ns{font-family:var(--font-mono);font-size:10.5px;color:var(--text-faint);margin-top:2px;}
.ctrl-stat{text-align:center;background:var(--bg-sunken);border-radius:8px;padding:10px 4px;}
.ctrl-stat b{font-family:var(--font-mono);font-size:17px;display:block;color:var(--text);}
.ctrl-stat span{font-family:var(--font-mono);font-size:9.5px;text-transform:uppercase;letter-spacing:.05em;color:var(--text-faint);}
.ctrl-chip{font-family:var(--font-mono);font-size:10px;padding:3px 7px;border-radius:5px;background:var(--bg-sunken);color:var(--text-dim);border:1px solid var(--border);}

/* ── Method Badges ── */
.method-get,.method-post,.method-put,.method-patch,.method-delete{background:rgba(255,45,32,.10)!important;color:#FF2D20!important;}
.method-head,.method-options{background:rgba(107,119,140,0.12)!important;color:var(--text-faint)!important;}

/* ── Grade Badges ── */
.grade-a{background:rgba(0,135,90,0.10);color:var(--emerald);border:1px solid rgba(0,135,90,0.25);}
.grade-b{background:rgba(37,99,235,0.10);color:var(--sky);border:1px solid rgba(37,99,235,0.25);}
.grade-c{background:rgba(255,139,0,0.10);color:var(--amber);border:1px solid rgba(255,139,0,0.25);}
.grade-d{background:rgba(255,139,0,0.12);color:#c05c00;border:1px solid rgba(255,139,0,0.3);}
.grade-f{background:rgba(222,53,11,0.10);color:var(--rose);border:1px solid rgba(222,53,11,0.25);}

/* ── Generic section card ── */
.card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;transition:transform .25s var(--ease),box-shadow .25s var(--ease),border-color .25s;box-shadow:var(--shadow);}
.card:hover{transform:translateY(-2px);box-shadow:var(--shadow-hover);border-color:var(--border-strong);}

/* ── Mermaid / Graph canvas bg overrides ── */
.mermaid svg{max-width:100%;height:auto;}
.g-node{transition:opacity .15s ease;}
.diag-tab{transition:background .15s,color .15s,border-color .15s;}

/* ── Tailwind overrides for light theme ── */
.bg-white{background:#FFFFFF!important;}
.bg-slate-50,.bg-slate-100{background:var(--bg-sunken)!important;}
.bg-slate-900,.bg-slate-800{background:#172B4D!important;}
.text-slate-800,.text-slate-900{color:var(--text)!important;}
.text-slate-500,.text-slate-400,.text-slate-600{color:var(--text-dim)!important;}
.text-slate-300{color:var(--text-faint)!important;}
.border-slate-100,.border-slate-200,.border-slate-700{border-color:var(--border)!important;}
.divide-slate-100>*+*{border-color:var(--border)!important;}

/* ── Form inputs ── */
input[type="search"],input[type="text"],select,textarea{
  background:#FFFFFF!important;
  border-color:var(--border)!important;
  color:var(--text)!important;
}
input[type="search"]::placeholder,input[type="text"]::placeholder{color:var(--text-faint)!important;}
select option{background:#FFFFFF;color:var(--text);}

/* ── Table rows ── */
tr.route-row:hover{background:rgba(255,45,32,0.04)!important;}
thead tr{background:var(--bg-sunken)!important;}

/* ── Buttons ── */
.atlas-btn{display:inline-flex;align-items:center;gap:8px;padding:8px 16px;border-radius:8px;font-family:var(--font-mono);font-size:12px;font-weight:600;border:1px solid var(--border);background:#FFFFFF;color:var(--text-dim);cursor:pointer;transition:border-color .2s,color .2s,background .2s,box-shadow .2s,transform .1s;}
.atlas-btn:hover{border-color:var(--border-strong);color:var(--text);box-shadow:var(--shadow);}
.atlas-btn:active{transform:scale(0.96);}
.atlas-btn--cyan{border-color:rgba(255,45,32,0.35);color:var(--cyan);background:rgba(255,45,32,0.06);}
.atlas-btn--cyan:hover{border-color:var(--cyan);background:rgba(255,45,32,0.10);}

/* ── Score bar ── */
.atlas-score-bar{height:4px;border-radius:2px;background:var(--bg-sunken);overflow:hidden;}
.atlas-score-fill{height:100%;border-radius:2px;background:linear-gradient(90deg,#FF2D20,#FF5349);}

/* ── Section headings ── */
.sec-title{font-size:22px;font-weight:800;color:var(--text);letter-spacing:-0.01em;}
.sec-sub{font-size:13px;color:var(--text-faint);font-family:var(--font-mono);margin-top:4px;}

/* ── Shared Section Header ── */
.sec-header{display:flex;align-items:center;gap:14px;padding-left:16px;border-left:3px solid var(--cyan);margin-bottom:24px;}
.sec-header__icon{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex:none;}
.sec-header__icon svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;}
.sec-header__title{font-size:22px;font-weight:800;color:var(--text);letter-spacing:-0.01em;margin:0;}
.sec-header__sub{font-size:13px;color:var(--text-faint);font-family:var(--font-mono);margin-top:3px;}

/* ── Relation graph canvas ── */
#rg-canvas{background:var(--bg-sunken)!important;}
.relative.rounded-2xl.border{background:var(--bg-elevated)!important;border-color:var(--border)!important;}

/* ── Tailwind color overrides for JS-rendered detail panels ── */
.bg-indigo-50{background:rgba(255,45,32,0.06)!important;}
.bg-indigo-100{background:rgba(255,45,32,0.10)!important;}
.bg-blue-50{background:rgba(59,130,246,0.12)!important;}
.bg-blue-100{background:rgba(59,130,246,0.18)!important;}
.bg-green-50{background:rgba(34,197,94,0.12)!important;}
.bg-green-100{background:rgba(34,197,94,0.18)!important;}
.bg-teal-50{background:rgba(20,184,166,0.12)!important;}
.bg-purple-50{background:rgba(168,85,247,0.12)!important;}
.bg-purple-100{background:rgba(168,85,247,0.18)!important;}
.bg-pink-50{background:rgba(236,72,153,0.12)!important;}
.bg-orange-50{background:rgba(251,146,60,0.12)!important;}
.bg-orange-100{background:rgba(251,146,60,0.18)!important;}
.bg-amber-50{background:rgba(245,158,11,0.12)!important;}
.bg-amber-100{background:rgba(245,158,11,0.18)!important;}
.bg-red-50{background:rgba(239,68,68,0.12)!important;}
.bg-red-100{background:rgba(239,68,68,0.18)!important;}
.bg-emerald-50{background:rgba(16,185,129,0.12)!important;}
.bg-violet-50{background:rgba(139,92,246,0.12)!important;}
.bg-sky-50{background:rgba(14,165,233,0.12)!important;}

/* color overrides for text on these light backgrounds */
.text-indigo-600,.text-indigo-700{color:#FF2D20!important;}
.text-blue-600,.text-blue-700{color:var(--sky)!important;}
.text-green-600,.text-green-700{color:var(--emerald)!important;}
.text-teal-600,.text-teal-700{color:#2dd4bf!important;}
.text-purple-600,.text-purple-700{color:var(--violet)!important;}
.text-pink-600,.text-pink-700{color:#f472b6!important;}
.text-orange-600,.text-orange-700{color:#fb923c!important;}
.text-amber-600,.text-amber-700,.text-amber-800{color:var(--amber)!important;}
.text-red-600,.text-red-700{color:var(--rose)!important;}
.text-emerald-600,.text-emerald-700{color:var(--emerald)!important;}
.text-violet-600,.text-violet-700{color:var(--violet)!important;}
.text-green-800{color:var(--emerald)!important;}
.text-amber-800{color:var(--amber)!important;}
.text-red-800,.text-red-900{color:var(--rose)!important;}

/* border color overrides */
.border-indigo-200{border-color:rgba(255,45,32,0.25)!important;}
.border-blue-200{border-color:rgba(59,130,246,0.3)!important;}
.border-green-200{border-color:rgba(34,197,94,0.3)!important;}
.border-amber-200{border-color:rgba(245,158,11,0.3)!important;}
.border-red-200{border-color:rgba(239,68,68,0.3)!important;}
.border-orange-200{border-color:rgba(251,146,60,0.3)!important;}
.border-purple-200{border-color:rgba(168,85,247,0.3)!important;}
.border-emerald-200{border-color:rgba(52,211,153,0.3)!important;}
.border-violet-200{border-color:rgba(139,92,246,0.3)!important;}

/* hover state overrides */
.hover\:bg-slate-50:hover{background:var(--bg-hover)!important;}
.hover\:bg-slate-100:hover{background:var(--bg-hover)!important;}
.hover\:bg-indigo-50:hover{background:rgba(255,45,32,0.08)!important;}
.hover\:bg-indigo-100:hover{background:rgba(255,45,32,0.12)!important;}

/* focused ring overrides (Tailwind focus:ring) */
.focus\:ring-indigo-300:focus{--tw-ring-color:rgba(255,45,32,0.35);}

/* ── Tailwind layout/spacing utility shims (for JS-rendered HTML) ── */
.flex{display:flex!important;}.inline-flex{display:inline-flex!important;}
.grid{display:grid!important;}.grid-cols-1{grid-template-columns:1fr!important;}
.flex-wrap{flex-wrap:wrap!important;}
.items-center{align-items:center!important;}.items-start{align-items:flex-start!important;}
.justify-center{justify-content:center!important;}.justify-start{justify-content:flex-start!important;}.justify-end{justify-content:flex-end!important;}
.gap-1{gap:4px!important;}.gap-2{gap:8px!important;}.gap-2\.5{gap:10px!important;}.gap-3{gap:12px!important;}.gap-4{gap:16px!important;}
.shrink-0{flex-shrink:0!important;}.min-w-0{min-width:0!important;}
.text-xs{font-size:11px!important;}.text-sm{font-size:13px!important;}.text-base{font-size:15px!important;}.text-lg{font-size:17px!important;}
.font-medium{font-weight:500!important;}.font-semibold{font-weight:600!important;}.font-bold{font-weight:700!important;}
.leading-relaxed{line-height:1.6!important;}.uppercase{text-transform:uppercase!important;}.tracking-wide{letter-spacing:.05em!important;}
.italic{font-style:italic!important;}.list-disc{list-style-type:disc!important;}
.p-3{padding:12px!important;}.p-5{padding:20px!important;}
.px-1\.5{padding-left:6px!important;padding-right:6px!important;}.px-2{padding-left:8px!important;padding-right:8px!important;}
.px-4{padding-left:16px!important;padding-right:16px!important;}.px-5{padding-left:20px!important;padding-right:20px!important;}
.py-0\.5{padding-top:2px!important;padding-bottom:2px!important;}.py-1\.5{padding-top:6px!important;padding-bottom:6px!important;}
.py-2\.5{padding-top:10px!important;padding-bottom:10px!important;}.py-3{padding-top:12px!important;padding-bottom:12px!important;}
.py-4{padding-top:16px!important;padding-bottom:16px!important;}
.mt-0\.5{margin-top:2px!important;}.mt-1{margin-top:4px!important;}.mt-2{margin-top:8px!important;}.mt-3{margin-top:12px!important;}
.mb-1{margin-bottom:4px!important;}.mb-2{margin-bottom:8px!important;}.mb-4{margin-bottom:16px!important;}.mb-6{margin-bottom:24px!important;}
.ml-2{margin-left:8px!important;}.ml-4{margin-left:16px!important;}.pt-2{padding-top:8px!important;}
.w-4{width:16px!important;}.w-7{width:28px!important;}.w-8{width:32px!important;}.w-full{width:100%!important;}
.h-4{height:16px!important;}.h-7{height:28px!important;}.h-8{height:32px!important;}.h-fit{height:fit-content!important;}
.max-w-\[80\%\]{max-width:80%!important;}
.rounded{border-radius:4px!important;}.rounded-lg{border-radius:8px!important;}.rounded-xl{border-radius:12px!important;}
.rounded-2xl{border-radius:16px!important;}.rounded-full{border-radius:9999px!important;}
.rounded-tl-sm{border-top-left-radius:2px!important;}.rounded-tr-sm{border-top-right-radius:2px!important;}
.border{border:1px solid var(--border)!important;}.border-t{border-top:1px solid var(--border)!important;}
.border-b{border-bottom:1px solid var(--border)!important;}.last\:border-0:last-child{border:0!important;}
.border-purple-100{border-color:rgba(168,85,247,.2)!important;}.border-emerald-100{border-color:rgba(16,185,129,.2)!important;}
.border-violet-100{border-color:rgba(139,92,246,.2)!important;}.border-red-100{border-color:rgba(239,68,68,.2)!important;}
.border-amber-100{border-color:rgba(245,158,11,.2)!important;}.border-blue-100{border-color:rgba(59,130,246,.2)!important;}
.border-slate-50{border-color:rgba(148,163,184,.1)!important;}
.bg-indigo-600{background:#4f46e5!important;}.bg-purple-200{background:rgba(168,85,247,.35)!important;}
.bg-emerald-200{background:rgba(16,185,129,.35)!important;}.bg-violet-200{background:rgba(139,92,246,.35)!important;}
.text-white{color:#fff!important;}.text-slate-700{color:#334155!important;}
.text-green-300{color:#86efac!important;}.text-green-500{color:#22c55e!important;}
.text-emerald-500{color:var(--emerald)!important;}.text-violet-500{color:var(--violet)!important;}
.text-purple-700{color:var(--violet)!important;}.text-emerald-700{color:var(--emerald)!important;}
.flex-col{flex-direction:column!important;}.text-center{text-align:center!important;}.text-left{text-align:left!important;}
.inline-block{display:inline-block!important;}.block{display:block!important;}
.mt-1\.5{margin-top:6px!important;}.leading-tight{line-height:1.25!important;}
.text-xl{font-size:19px!important;}.font-mono{font-family:var(--font-mono)!important;}
.py-1{padding-top:4px!important;padding-bottom:4px!important;}
.text-blue-500{color:var(--sky)!important;}
.border-green-200{border-color:rgba(0,135,90,.25)!important;}
/* border-color overrides after .border so they win the cascade */
.border-amber-200{border-color:rgba(245,158,11,0.3)!important;}.border-red-200{border-color:rgba(239,68,68,0.3)!important;}
.border-indigo-200{border-color:rgba(255,45,32,0.25)!important;}.border-blue-200{border-color:rgba(59,130,246,0.3)!important;}
.border-orange-200{border-color:rgba(251,146,60,0.3)!important;}.border-purple-200{border-color:rgba(168,85,247,0.3)!important;}
.border-emerald-200{border-color:rgba(52,211,153,0.3)!important;}.border-violet-200{border-color:rgba(139,92,246,0.3)!important;}
@media(min-width:640px){.sm\:grid-cols-2{grid-template-columns:repeat(2,1fr)!important;}}
@media(min-width:768px){.md\:grid-cols-3{grid-template-columns:repeat(3,1fr)!important;}}
.overflow-hidden{overflow:hidden!important;}.overflow-x-auto{overflow-x:auto!important;}
.transition-colors{transition:background .15s,color .15s,border-color .15s!important;}
.shadow-sm{box-shadow:0 1px 3px rgba(0,0,0,.06)!important;}

/* rounded bg code elements */
code{background:var(--bg-sunken)!important;color:var(--cyan)!important;border:1px solid var(--border);border-radius:4px;padding:1px 5px;font-size:.9em;}

/* ── Models Page (redesigned) ── */
.mds-top-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:22px;}
.mds-top-stat{background:var(--bg-elevated);border:1px solid var(--border);border-radius:13px;padding:16px 18px;display:flex;flex-direction:column;gap:6px;box-shadow:var(--shadow);}
.mds-top-stat-num{font-size:28px;font-weight:800;font-family:var(--font-sans);line-height:1;}
.mds-top-stat-lbl{font-size:11px;color:var(--text-faint);font-family:var(--font-mono);}
.mds-toolbar{display:flex;align-items:center;gap:10px;margin-bottom:20px;flex-wrap:wrap;}
.mds-view-grp{display:flex;background:var(--bg-sunken);border:1px solid var(--border);border-radius:8px;padding:3px;gap:2px;}
.mds-view-btn{padding:6px 11px;border-radius:5px;border:none;cursor:pointer;background:none;color:var(--text-faint);transition:all .2s;display:flex;align-items:center;}
.mds-view-btn.active{background:var(--bg-elevated);color:var(--text);border:1px solid var(--border);box-shadow:var(--shadow);}
/* Card grid */
.mds-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:16px;}
.mds-card{border-radius:16px;border:1px solid var(--border);background:var(--bg-elevated);cursor:pointer;overflow:hidden;transition:border-color .25s,box-shadow .25s;position:relative;box-shadow:var(--shadow);}
.mds-card:hover{box-shadow:var(--shadow-hover);border-color:var(--border-strong);}
.mds-card-glow{position:absolute;top:0;left:0;right:0;height:3px;border-radius:16px 16px 0 0;}
.mds-card-head{padding:20px 20px 14px;display:flex;align-items:flex-start;gap:14px;}
.mds-card-av{width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:21px;font-weight:800;flex:none;border:1px solid;}
.mds-card-title{font-size:15.5px;font-weight:700;color:var(--text);line-height:1.25;margin-bottom:3px;}
.mds-card-table{font-size:11px;font-family:var(--font-mono);color:var(--text-faint);}
.mds-card-ns{font-size:10px;font-family:var(--font-mono);color:var(--text-faint);opacity:.6;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:170px;}
.mds-card-obs{font-size:9px;padding:2px 7px;border-radius:4px;background:rgba(255,139,0,.10);color:var(--amber);border:1px solid rgba(255,139,0,.2);font-family:var(--font-mono);flex:none;}
.mds-card-sep{height:1px;background:var(--border);margin:0 20px;}
.mds-card-body{padding:14px 20px 16px;}
.mds-card-stats{display:flex;align-items:center;gap:0;margin-bottom:12px;}
.mds-card-stat-item{flex:1;text-align:center;padding:8px 0;}
.mds-card-stat-item:not(:last-child){border-right:1px solid var(--border);}
.mds-card-stat-num{font-size:18px;font-weight:800;font-family:var(--font-sans);line-height:1;}
.mds-card-stat-lbl{font-size:9.5px;font-family:var(--font-mono);color:var(--text-faint);margin-top:3px;text-transform:uppercase;letter-spacing:.06em;}
.mds-rel-bar{height:4px;border-radius:4px;display:flex;gap:1px;overflow:hidden;margin-bottom:10px;}
.mds-rel-seg{border-radius:4px;transition:flex .6s ease-out;}
.mds-rel-legend{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px;}
.mds-rel-dot{display:inline-flex;align-items:center;gap:4px;font-size:10px;font-family:var(--font-mono);color:var(--text-faint);}
.mds-rel-dot i{width:6px;height:6px;border-radius:50%;display:inline-block;flex:none;}
.mds-trait-row{display:flex;flex-wrap:wrap;gap:5px;}
.mds-trait-pip{font-size:10px;padding:2px 8px;border-radius:5px;background:rgba(101,84,192,.08);color:var(--violet);border:1px solid rgba(101,84,192,.2);font-family:var(--font-mono);}
/* List view */
.mds-list-head{display:grid;grid-template-columns:40px 1fr 140px 60px 80px 60px;gap:10px;padding:10px 16px;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--text-faint);font-family:var(--font-mono);margin-bottom:0;background:var(--bg-sunken);border-bottom:1px solid var(--border);}
.mds-list-row{display:grid;grid-template-columns:40px 1fr 140px 60px 80px 60px;align-items:center;gap:10px;padding:11px 16px;background:var(--bg-elevated);border-bottom:1px solid var(--border);cursor:pointer;transition:background .18s;}.mds-list-row:last-child{border-bottom:none;}.mds-list-row:hover{background:var(--bg-hover);}
.mds-list-row:hover{transform:translateX(4px);box-shadow:var(--shadow-hover);border-color:var(--border-strong);}
.mds-list-av{width:32px;height:32px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex:none;border:1px solid;}
.mw-row{display:grid;grid-template-columns:40px 1fr 180px 80px;align-items:center;gap:10px;padding:11px 16px;border-bottom:1px solid var(--border);transition:background .15s;}.mw-row:last-child{border-bottom:none;}.mw-row:hover{background:var(--bg-hover);}
/* Detail layout */
.mds-det-wrap{display:grid;grid-template-columns:280px 1fr;gap:22px;align-items:start;}
.mds-sidebar{position:sticky;top:16px;}
.mds-side-card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:16px;overflow:hidden;box-shadow:var(--shadow);}
.mds-side-top{padding:26px 22px 22px;border-bottom:1px solid var(--border);text-align:center;}
.mds-side-av{width:80px;height:80px;border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:34px;font-weight:800;margin:0 auto 16px;border:2px solid;}
.mds-side-name{font-size:17px;font-weight:800;color:var(--text);margin-bottom:5px;word-break:break-word;}
.mds-side-tbl{font-size:11.5px;font-family:var(--font-mono);color:var(--text-faint);margin-bottom:4px;}
.mds-side-ns{font-size:10.5px;font-family:var(--font-mono);color:var(--text-faint);opacity:.65;word-break:break-all;line-height:1.5;}
.mds-side-stats{padding:14px 22px;border-bottom:1px solid var(--border);}
.mds-side-stat{display:flex;align-items:center;justify-content:space-between;padding:7px 8px;border-radius:8px;cursor:pointer;transition:background .15s;}
.mds-side-stat:hover{background:rgba(255,45,32,.04);}
.mds-side-stat-lbl{font-size:13px;color:var(--text-dim);}
.mds-side-stat-val{font-size:15px;font-weight:800;font-family:var(--font-mono);}
.mds-side-meta{padding:16px 22px;display:flex;flex-direction:column;gap:7px;}
.mds-side-chip{font-size:10.5px;padding:3px 9px;border-radius:6px;font-family:var(--font-mono);border:1px solid;display:inline-block;}
/* Main tabs */
.mds-tabs{display:flex;gap:3px;background:var(--bg-sunken);border-radius:11px;padding:4px;border:1px solid var(--border);margin-bottom:18px;}
.mds-tab-btn{flex:1;padding:9px 12px;border-radius:8px;font-size:13px;font-weight:600;border:1px solid transparent;cursor:pointer;background:none;color:var(--text-faint);transition:all .2s;font-family:var(--font-sans);}
.mds-tab-btn.active{background:var(--bg-elevated);color:var(--text);border-color:var(--border);box-shadow:var(--shadow);}
.mds-tab-pane{display:none;}
.mds-tab-pane.active{display:block;}
.mds-section-lbl{font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--text-faint);font-family:var(--font-mono);margin:0 0 12px;}
/* Schema table */
.mds-schema-wrap{background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;overflow:hidden;margin-bottom:14px;box-shadow:var(--shadow);}
.mds-schema-tbl{width:100%;border-collapse:collapse;}
.mds-schema-tbl th{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-faint);font-family:var(--font-mono);padding:10px 14px;text-align:left;border-bottom:1px solid var(--border);background:var(--bg-sunken);}
.mds-schema-tbl td{padding:10px 14px;border-bottom:1px solid var(--border);vertical-align:middle;}
.mds-schema-tbl tr:last-child td{border-bottom:none;}
.mds-schema-tbl tr:hover td{background:rgba(255,45,32,.02);}
.mds-field-name{font-family:var(--font-mono);font-weight:600;font-size:13px;color:var(--text);}
.mds-fbadge{font-size:9px;font-weight:700;padding:2px 7px;border-radius:4px;font-family:var(--font-mono);border:1px solid;margin-right:4px;white-space:nowrap;display:inline-block;}
.mds-fbadge.fill{background:rgba(255,45,32,.08);color:var(--cyan);border-color:rgba(255,45,32,.2);}
.mds-fbadge.hide{background:rgba(255,45,32,.08);color:#FF2D20;border-color:rgba(255,45,32,.2);}
.mds-cast-val{font-family:var(--font-mono);font-size:11px;color:#FF2D20;}
/* Relationship cards */
.mds-rel-card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;padding:14px 18px;margin-bottom:9px;display:flex;align-items:center;gap:14px;transition:border-color .2s,transform .15s,box-shadow .2s;box-shadow:var(--shadow);}
.mds-rel-card:hover{transform:translateX(3px);box-shadow:var(--shadow-hover);border-color:var(--border-strong);}
.mds-rel-method{font-family:var(--font-mono);font-size:13px;color:var(--cyan);font-weight:600;min-width:150px;}
.mds-rel-type{font-size:10px;font-weight:700;padding:3px 10px;border-radius:6px;font-family:var(--font-mono);border:1px solid;white-space:nowrap;min-width:120px;text-align:center;}
.mds-rel-arrow{font-size:14px;color:var(--text-faint);flex:none;}
.mds-rel-target{font-size:14px;font-weight:700;color:var(--text);flex:1;}
.mds-nav-btn{font-size:11px;padding:5px 12px;border-radius:7px;border:1px solid;cursor:pointer;font-family:var(--font-sans);white-space:nowrap;transition:all .2s;flex:none;}
/* Used by */
.mds-usedby-card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;padding:14px 18px;margin-bottom:9px;display:flex;align-items:center;justify-content:space-between;gap:14px;transition:border-color .2s,box-shadow .2s;box-shadow:var(--shadow);}
.mds-usedby-card:hover{border-color:rgba(255,45,32,.25);box-shadow:var(--shadow-hover);}
.mds-flag-row{display:flex;flex-wrap:wrap;gap:8px;margin-top:14px;}
.mds-flag{font-size:11px;padding:5px 13px;border-radius:7px;font-family:var(--font-mono);font-weight:600;border:1px solid;}

/* Architecture Explorer */
.ov-panel{background:var(--bg-elevated);border:1px solid var(--border);border-radius:16px;position:relative;overflow:hidden;box-shadow:var(--shadow);}
.ov-panel-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:18px 22px;border-bottom:1px solid var(--border);}
.ov-panel-head h3{font-size:14.5px;font-weight:700;font-family:var(--font-sans);margin:0;color:var(--text);}
.ov-panel-head p{font-size:11.5px;color:var(--text-faint);margin:3px 0 0;}
.ov-panel-body{padding:20px 22px;}
.ov-diag-shell{overflow-x:auto;border-radius:10px;background:var(--bg-sunken);border:1px solid var(--border);}
#ovArchPanel:fullscreen{border-radius:0;display:flex;flex-direction:column;background:var(--bg);}
#ovArchPanel:-webkit-full-screen{border-radius:0;display:flex;flex-direction:column;background:var(--bg);}
#ovArchPanel:fullscreen #ovArchPanelBody{flex:1;overflow:auto;display:flex;flex-direction:column;}
#ovArchPanel:-webkit-full-screen #ovArchPanelBody{flex:1;overflow:auto;display:flex;flex-direction:column;}
#ovArchPanel:fullscreen #ovDiagShell{flex:1;border-radius:0;border:none;overflow:auto;}
#ovArchPanel:-webkit-full-screen #ovDiagShell{flex:1;border-radius:0;border:none;overflow:auto;}
#ovArchPanel:fullscreen #ovArchDiagram{min-height:100%;}
#ovArchPanel:-webkit-full-screen #ovArchDiagram{min-height:100%;}
.ov-arch-node rect{transition:filter .3s;}
.ov-arch-node:hover rect{filter:drop-shadow(0 0 10px rgba(255,45,32,.35));}
.ov-arch-node:focus{outline:none;}
.ov-btn-icon{width:34px;height:34px;border-radius:9px;display:inline-flex;align-items:center;justify-content:center;background:var(--bg-sunken);border:1px solid var(--border);color:var(--text-dim);cursor:pointer;transition:all .25s;flex-shrink:0;font-size:16px;line-height:1;}
.ov-btn-icon:hover{background:rgba(255,45,32,.06);border-color:rgba(255,45,32,.25);color:var(--cyan);}
.ov-reveal{opacity:0;transform:translateY(14px);transition:opacity .55s var(--ease),transform .55s var(--ease);}
.ov-reveal.ov-in{opacity:1;transform:none;}
@keyframes secIn{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:none}}
@keyframes secOut{from{opacity:1;transform:none}to{opacity:0;transform:translateY(-10px) scale(0.98)}}
.sec-fade{animation:secIn .30s cubic-bezier(.22,1,.36,1) both;}
.sec-out{animation:secOut .18s ease-in both;pointer-events:none;}

@property --rg-angle{syntax:'<angle>';inherits:false;initial-value:0deg;}
@keyframes borderSpin{to{--rg-angle:360deg;}}
.score-spin-border{border:2px solid transparent;background:var(--bg-hover) padding-box,conic-gradient(from var(--rg-angle),#FF2D20,#FF5349,#FFBAB6,#FF5349,#FF2D20) border-box;animation:borderSpin 3s linear infinite;}
@keyframes hcIn{from{opacity:0;transform:translateX(-20px)}to{opacity:1;transform:none}}
@keyframes pkgBadgePulse{0%,100%{opacity:1;}50%{opacity:0.3;}}
.pkg-ver-badge{animation:pkgBadgePulse 2s ease-in-out infinite;}
@keyframes pkgCardIn{from{opacity:0;transform:translateY(18px);}to{opacity:1;transform:none;}}
.pkg-card{animation:pkgCardIn .38s var(--ease,cubic-bezier(.4,0,.2,1)) both;animation-delay:calc(var(--pkg-i,0)*60ms);}
.pkg-card:hover{transform:translateY(-3px);box-shadow:0 8px 28px rgba(0,0,0,.1);}
.pkg-docs-btn{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;padding:5px 10px;border-radius:7px;border:1px solid;cursor:pointer;text-decoration:none;transition:background .15s,opacity .15s;}
.pkg-copy-btn{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;padding:5px 10px;border-radius:7px;border:1px solid var(--border);background:var(--bg-hover);color:var(--text-dim);cursor:pointer;font-family:var(--font-mono);transition:background .15s;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px;}
.pkg-copy-btn:hover{background:var(--border);}
.pkg-stat-card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;padding:16px 20px;display:flex;align-items:center;gap:14px;}
.pkg-cat-header{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;margin-bottom:14px;border-left:4px solid;}
.hc-row{opacity:0;}
.ov-in .hc-row{animation:hcIn .32s var(--ease) both;animation-delay:calc(var(--hc-i,0) * 80ms);}
/* ── Controller Flow Diagram ── */
@keyframes cfNodeIn{from{opacity:0;transform:scale(0.82) translateY(12px);}to{opacity:1;transform:scale(1) translateY(0);}}
.cf-node{animation:cfNodeIn 0.45s cubic-bezier(.34,1.56,.64,1) both;}
/* ── Phase 4: AI / Chat / Export ── */
@keyframes aiPulseGlow{0%,100%{box-shadow:0 0 0 0 rgba(255,45,32,0.45);}50%{box-shadow:0 0 0 8px rgba(255,45,32,0);}}
@keyframes aiSpin{to{transform:rotate(360deg);}}
@keyframes typeCursor{0%,100%{opacity:1;}50%{opacity:0;}}
@keyframes exportCardIn{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:none;}}
@keyframes chatBounce{0%,80%,100%{transform:translateY(0);}40%{transform:translateY(-5px);}}
@keyframes chatBubbleIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:none;}}
@keyframes deadCardIn{from{opacity:0;transform:translateY(14px);}to{opacity:1;transform:none;}}
@keyframes severityPulse{0%,100%{opacity:1;}50%{opacity:.55;}}
@keyframes checkDraw{from{stroke-dashoffset:100;}to{stroke-dashoffset:0;}}
@keyframes modalScaleIn{from{opacity:0;transform:scale(.93) translateY(16px);}to{opacity:1;transform:none;}}
@keyframes modalBdIn{from{opacity:0;backdrop-filter:blur(0px);}to{opacity:1;backdrop-filter:blur(4px);}}
@keyframes modalScaleOut{from{opacity:1;transform:none;}to{opacity:0;transform:scale(.95) translateY(8px);}}
@keyframes deadStatIn{from{opacity:0;transform:translateY(10px) scale(.96);}to{opacity:1;transform:none;}}
@keyframes shimmerSweep{0%{background-position:-200% 0;}100%{background-position:200% 0;}}
@keyframes tabPop{0%{transform:scale(1);}40%{transform:scale(0.93);}100%{transform:scale(1);}}
.ai-analyze-btn{display:inline-flex;align-items:center;gap:8px;padding:8px 18px;background:var(--cyan);border:none;border-radius:10px;color:#fff;font-weight:700;font-size:13px;cursor:pointer;font-family:var(--font-mono);transition:background .2s,transform .15s,box-shadow .2s;box-shadow:0 4px 16px rgba(255,45,32,0.35);}
.ai-analyze-btn:hover{background:#DC1F13;transform:translateY(-1px);box-shadow:0 6px 24px rgba(255,45,32,0.5);}
.ai-analyze-btn:disabled{background:var(--border-strong);box-shadow:none;cursor:not-allowed;transform:none;color:var(--text-faint);}
.ai-analyze-btn:not(:disabled):hover{animation:aiPulseGlow 1.5s ease infinite;}
.ai-analyze-btn:not(:disabled):active{transform:translateY(1px) scale(0.97)!important;}
.chat-send-btn{display:flex;align-items:center;justify-content:center;width:36px;height:36px;background:var(--cyan);border:none;border-radius:9px;color:#fff;cursor:pointer;transition:background .2s,transform .15s,box-shadow .2s;box-shadow:0 2px 8px rgba(255,45,32,0.3);flex:none;}
.chat-send-btn:hover{background:#DC1F13;transform:scale(1.08);box-shadow:0 4px 14px rgba(255,45,32,0.5);}
.chat-send-btn:disabled{background:var(--border);box-shadow:none;cursor:not-allowed;transform:none;}
.chat-suggestion-chip{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:20px;font-size:12px;font-weight:500;color:var(--text-dim);cursor:pointer;transition:background .15s,border-color .15s,color .15s;font-family:var(--font-sans);}
.chat-suggestion-chip:hover{background:rgba(255,45,32,.06);border-color:rgba(255,45,32,.3);color:var(--cyan);}
.export-card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;display:flex;flex-direction:column;gap:14px;animation:exportCardIn .4s var(--ease) both;animation-delay:calc(var(--ei,0)*80ms);transition:border-color .2s,box-shadow .2s;}
.export-card:hover{border-color:var(--border-strong);box-shadow:var(--shadow-hover);}
/* ── Dead Code legacy (keep for is-hiding transition) ── */
.dead-item{background:var(--bg-elevated);border:1px solid var(--border);border-radius:10px;padding:14px 16px;margin-bottom:8px;transition:border-color .2s,box-shadow .2s,opacity .22s,transform .22s;}
.dead-item:hover{border-color:var(--border-strong);box-shadow:var(--shadow-hover);}
.dead-item.is-hiding{opacity:0;transform:scale(0.95) translateY(-4px);pointer-events:none;}

/* ── Dead Code — dc-* ── */
.dc-sev-bar{height:6px;border-radius:3px;overflow:hidden;display:flex;gap:2px;margin-top:8px;width:100%;}
.dc-sev-bar__seg{height:100%;border-radius:3px;}
/* Type grid */
.dc-type-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px;margin-bottom:20px;}
.dc-type-card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;padding:16px;cursor:pointer;transition:border-color .2s,box-shadow .2s,transform .2s,background .2s;text-align:center;display:flex;flex-direction:column;align-items:center;gap:6px;box-shadow:var(--shadow);}
.dc-type-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-hover);border-color:var(--border-strong);}
.dc-type-card:active{transform:scale(0.96);}
.dc-type-card .tc-emoji{font-size:22px;line-height:1;}
.dc-type-card .tc-label{font-size:11.5px;font-weight:600;color:var(--text-dim);}
.dc-type-card .tc-count{font-family:var(--font-mono);font-size:20px;font-weight:800;color:var(--text);line-height:1;}
.dc-type-card.dc-type-active{border-color:var(--cyan);background:rgba(255,45,32,0.06);box-shadow:0 0 0 2px rgba(255,45,32,0.15),var(--shadow);}
.dc-type-card.dc-type-active .tc-label{color:var(--cyan);}
.dc-type-card.dc-type-active .tc-count{color:var(--cyan);}
.dc-type-card.dc-type-zero{opacity:.5;}
.dc-type-card.dc-type-zero:hover{opacity:1;}
/* Severity filter row */
.dc-filter-row{display:flex;align-items:center;gap:8px;margin-bottom:20px;flex-wrap:wrap;}
.dc-sev-tab{padding:7px 16px;border-radius:20px;font-size:12.5px;font-weight:600;border:1px solid var(--border);background:var(--bg-elevated);color:var(--text-dim);cursor:pointer;transition:background .15s,border-color .15s,color .15s,transform .1s;display:inline-flex;align-items:center;gap:5px;}
.dc-sev-tab:hover{background:var(--bg-hover);border-color:var(--border-strong);}
.dc-sev-tab:active{animation:tabPop .2s var(--ease);}
.dc-sev-tab--active{background:rgba(255,45,32,0.08);border-color:rgba(255,45,32,0.35);color:var(--cyan);box-shadow:0 2px 8px rgba(255,45,32,0.12);}
.dc-sev-tab--high.dc-sev-tab--active{background:rgba(220,38,38,0.08);border-color:rgba(220,38,38,0.35);color:#DC2626;box-shadow:0 2px 8px rgba(220,38,38,0.12);}
.dc-sev-tab--medium.dc-sev-tab--active{background:rgba(217,119,6,0.10);border-color:rgba(217,119,6,0.4);color:#D97706;box-shadow:0 2px 8px rgba(217,119,6,0.12);}
.dc-sev-tab--low.dc-sev-tab--active{background:rgba(37,99,235,0.08);border-color:rgba(37,99,235,0.3);color:var(--sky);box-shadow:0 2px 8px rgba(37,99,235,0.10);}
/* Item cards */
.dc-item{display:flex;background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;margin-bottom:10px;overflow:hidden;transition:border-color .2s,box-shadow .2s,opacity .22s,transform .22s;animation:deadCardIn .35s var(--ease) both;animation-delay:calc(var(--di,0)*30ms);}
.dc-item:hover{border-color:var(--border-strong);box-shadow:var(--shadow-hover);}
.dc-item.is-hiding{opacity:0;transform:scale(0.96) translateY(-4px);pointer-events:none;}
.dc-item__accent{width:4px;flex:none;}
.dc-item__body{flex:1;padding:14px 16px;min-width:0;}
.dc-item__head{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:8px;}
.dc-item__badges{display:flex;align-items:center;gap:6px;flex-wrap:wrap;}
.dc-item__name{font-family:var(--font-mono);font-size:14px;font-weight:800;color:var(--text);margin-bottom:4px;word-break:break-all;}
.dc-item__loc{display:flex;align-items:center;gap:5px;margin-bottom:5px;}
.dc-item__loc svg{width:11px;height:11px;flex:none;color:var(--text-faint);}
.dc-item__loc span{font-family:var(--font-mono);font-size:11px;color:var(--cyan);word-break:break-all;}
.dc-item__detail{font-size:12px;color:var(--text-faint);margin-bottom:6px;}
.dc-item__snippet{margin:0;background:var(--bg-sunken);border:1px solid var(--border);border-left:3px solid;border-radius:0 6px 6px 0;padding:8px 12px;font-family:var(--font-mono);font-size:11px;color:var(--text-dim);overflow-x:auto;white-space:pre-wrap;word-break:break-all;max-height:80px;overflow-y:auto;}
.dc-copy-btn{flex:none;display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:7px;border:1px solid var(--border);background:var(--bg-sunken);color:var(--text-faint);font-size:10.5px;font-family:var(--font-mono);cursor:pointer;transition:background .15s,color .15s,border-color .15s;white-space:nowrap;}
.dc-copy-btn:hover{background:rgba(255,45,32,0.06);border-color:rgba(255,45,32,0.25);color:var(--cyan);}
.dc-copy-btn.copied{background:rgba(16,185,129,0.10);border-color:rgba(16,185,129,0.4);color:#10B981;}
@keyframes dcTypeIn{from{opacity:0;transform:scale(0.92) translateY(8px);}to{opacity:1;transform:none;}}
.ai-score-ring{transform:rotate(-90deg);transform-origin:50% 50%;}
.type-cursor::after{content:'|';animation:typeCursor .7s step-end infinite;color:var(--cyan);}
/* ── Phase 3: Controllers / Repositories / Routes ── */
@keyframes ctrlCardIn{from{opacity:0;transform:translateX(-18px);}to{opacity:1;transform:none;}}
.ctrl-card{animation:ctrlCardIn .38s var(--ease) both;animation-delay:calc(var(--ci,0)*55ms);}
.ctrl-complexity-track{height:4px;background:var(--bg-sunken);border-radius:2px;overflow:hidden;margin-top:10px;}
.ctrl-complexity-fill{height:100%;border-radius:2px;background:linear-gradient(90deg,var(--amber),#fb923c);transition:width .8s cubic-bezier(.4,0,.2,1);}
@keyframes routeRowIn{from{opacity:0;transform:translateX(-12px);}to{opacity:1;transform:none;}}
.route-row-anim{animation:routeRowIn .3s var(--ease) both;animation-delay:calc(var(--ri,0)*18ms);}
.method-dist-bar{display:flex;height:10px;border-radius:6px;overflow:hidden;gap:1px;margin-bottom:20px;}
.method-dist-seg{height:100%;transition:opacity .2s;cursor:pointer;}
.method-dist-seg:hover{opacity:.75;}
@keyframes repoCardIn{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:none;}}
.repo-card{animation:repoCardIn .38s var(--ease) both;animation-delay:calc(var(--ci,0)*55ms);}
.repo-dep-dot{width:8px;height:8px;border-radius:50%;background:var(--cyan);opacity:.7;}
/* ── Phase 2: Section card shared styles ── */
.sec-stats-banner{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px;margin-bottom:24px;}
.sec-stat-card{background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;padding:14px 18px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow);}
.sec-stat-icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;flex:none;}
.sec-stat-icon svg{width:18px;height:18px;stroke:currentColor;fill:none;}
.sec-stat-num{font-size:22px;font-weight:800;line-height:1;color:var(--text);}
.sec-stat-lbl{font-size:11px;color:var(--text-faint);margin-top:2px;font-family:var(--font-mono);}
.sec2-card{background:var(--bg-elevated);border:1px solid var(--border);border-left-width:3px;border-radius:0 12px 12px 0;padding:18px;cursor:pointer;transition:box-shadow .2s,border-color .2s,transform .2s;box-shadow:var(--shadow);animation:pkgCardIn .38s var(--ease) both;animation-delay:calc(var(--ci,0)*55ms);}
.sec2-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-hover);}
.sec2-icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;flex:none;}
.sec2-icon svg{width:18px;height:18px;stroke:currentColor;fill:none;}
.sec2-name{font-weight:700;font-size:14px;color:var(--text);margin:0 0 3px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.sec2-sub{font-size:11px;color:var(--text-faint);font-family:var(--font-mono);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.sec2-chip{font-family:var(--font-mono);font-size:10px;padding:3px 8px;border-radius:5px;border:1px solid;}
@keyframes backBtnIn{from{opacity:0;transform:translateX(-14px) scale(.94);}to{opacity:1;transform:none;}}
@keyframes backBtnPulse{0%{box-shadow:0 0 0 0 rgba(255,45,32,0.4);}60%{box-shadow:0 0 0 7px rgba(255,45,32,0);}100%{box-shadow:0 0 0 0 rgba(255,45,32,0);}}
.topbar-back-btn{display:none;align-items:center;gap:8px;background:rgba(255,45,32,0.08);border:1px solid rgba(255,45,32,0.25);border-radius:9px;padding:6px 13px 6px 10px;cursor:pointer;font-family:var(--font-mono);font-size:12px;font-weight:700;color:#FF2D20;transition:background .15s,border-color .15s,transform .15s;}
.topbar-back-btn:hover{background:rgba(255,45,32,0.14);border-color:rgba(255,45,32,0.45);transform:translateX(-2px);}
.topbar-back-btn.is-visible{display:inline-flex;animation:backBtnIn .26s var(--ease) both,backBtnPulse 1.4s ease .26s infinite;}
/* ── Doc Preview Modal ── */
.doc-modal-ov{position:fixed;inset:0;z-index:300;background:rgba(23,43,77,0.52);backdrop-filter:blur(4px);display:flex;align-items:flex-start;justify-content:center;padding:32px 20px;overflow-y:auto;}
.doc-modal-box{background:var(--bg-elevated);border-radius:20px;width:100%;max-width:860px;box-shadow:0 24px 80px rgba(23,43,77,0.22);border:1px solid var(--border);overflow:hidden;margin:auto;}
.doc-modal-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px 24px;border-bottom:1px solid var(--border);background:var(--bg-sunken);}
.doc-modal-body{padding:32px 36px;max-height:72vh;overflow-y:auto;}
.doc-modal-body::-webkit-scrollbar{width:5px}.doc-modal-body::-webkit-scrollbar-thumb{background:var(--border-strong);border-radius:3px}
/* Doc rendered typography */
.doc-r h1{font-size:23px;font-weight:800;color:var(--text);margin:0 0 18px;line-height:1.2;}
.doc-r h2{font-size:17px;font-weight:700;color:var(--text);margin:26px 0 10px;padding-bottom:7px;border-bottom:2px solid var(--border);}
.doc-r h3{font-size:14.5px;font-weight:700;color:var(--text);margin:18px 0 7px;}
.doc-r p{font-size:13.5px;color:var(--text-dim);line-height:1.75;margin:0 0 12px;}
.doc-r ul,.doc-r ol{padding-left:22px;margin:8px 0 12px;}
.doc-r li{font-size:13.5px;color:var(--text-dim);margin-bottom:5px;line-height:1.65;}
.doc-r strong{color:var(--text);font-weight:700;}
.doc-r em{font-style:italic;}
.doc-r code{font-family:var(--font-mono);font-size:12px;background:var(--bg-sunken);color:var(--cyan);padding:2px 6px;border-radius:4px;border:1px solid var(--border);}
.doc-r pre{background:#172B4D;color:#e2e8f0;border-radius:10px;padding:16px;overflow-x:auto;font-family:var(--font-mono);font-size:12.5px;line-height:1.6;margin:12px 0;}
.doc-r hr{border:none;border-top:1px solid var(--border);margin:22px 0;}
.doc-r table{width:100%;border-collapse:collapse;margin:12px 0;font-size:13px;}
.doc-r th{background:var(--bg-sunken);padding:9px 12px;text-align:left;font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-faint);border-bottom:2px solid var(--border);}
.doc-r td{padding:9px 12px;border-bottom:1px solid var(--border);color:var(--text-dim);}
.doc-r tr:last-child td{border-bottom:none;}
.doc-r tr:hover td{background:rgba(255,45,32,.02);}
/* ── Responsive Hamburger ── */
#menu-toggle{display:none;align-items:center;justify-content:center;width:36px;height:36px;border-radius:9px;border:1px solid var(--border);background:var(--bg-hover);cursor:pointer;flex:none;transition:background .15s;}
#menu-toggle:hover{background:var(--border);}
#sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(15,23,42,.4);z-index:150;backdrop-filter:blur(2px);}
#sidebar-overlay.is-open{display:block;}

/* ── Responsive Breakpoints ── */
@media(max-width:1280px){
    .atlas-layout{grid-template-columns:220px 1fr;}
    .mds-det-wrap{grid-template-columns:220px 1fr;}
    #sidebar-collapse-btn{left:208px;}
    .atlas-layout.sidebar-collapsed #sidebar-collapse-btn{left:52px;}
}
@media(max-width:1060px){
    .atlas-layout{grid-template-columns:178px 1fr;}
    #sidebar-collapse-btn{left:166px;}
    .atlas-layout.sidebar-collapsed #sidebar-collapse-btn{left:52px;}
    .sidebar{padding:20px 10px;}
    .nav-item{font-size:12.5px;gap:8px;padding:8px 8px;}
    .mds-top-stats{grid-template-columns:repeat(2,1fr);}
    .mds-det-wrap{grid-template-columns:1fr;}
    .mds-list-head,.mds-list-row{grid-template-columns:36px 1fr 60px 80px;}
    .mds-list-head>:nth-child(3),.mds-list-row>:nth-child(3){display:none;}
}
@media(max-width:860px){
    .atlas-layout{grid-template-columns:1fr;}
    .sidebar{position:fixed;left:0;top:0;bottom:0;width:248px;transform:translateX(-260px);z-index:200;box-shadow:6px 0 28px rgba(0,0,0,.14);}
    .sidebar.is-open{transform:translateX(0);}
    #menu-toggle{display:flex;}
    #sidebar-collapse-btn{display:none;}
    .kpi-grid{grid-template-columns:repeat(2,1fr);}
    .mds-top-stats{grid-template-columns:repeat(2,1fr);}
    .sec-stats-banner{grid-template-columns:repeat(2,1fr);}
    .topbar{padding:12px 16px;gap:10px;}
    .resp-grid-4{grid-template-columns:repeat(2,1fr)!important;}
    .resp-grid-3{grid-template-columns:repeat(2,1fr)!important;}
}
@media(max-width:560px){
    .kpi-grid{grid-template-columns:1fr 1fr;}
    .mds-grid{grid-template-columns:1fr;}
    .mds-top-stats{grid-template-columns:repeat(2,1fr);}
    .sec-stats-banner{grid-template-columns:repeat(2,1fr);}
    .resp-grid-4{grid-template-columns:repeat(2,1fr)!important;}
    .resp-grid-3{grid-template-columns:1fr!important;}
    .resp-grid-2{grid-template-columns:1fr!important;}
    .topbar{padding:10px 14px;}
}
</style>
</head>
<body class="atlas-layout">

@php
$score   = $data['score'] ?? [];
$summary = $data['summary'] ?? [];
$rs      = $data['route_summary'] ?? [];
$grade   = $score['grade'] ?? 'N/A';
$gradeClass = match(strtoupper($grade[0] ?? 'F')) {
    'A' => 'grade-a', 'B' => 'grade-b', 'C' => 'grade-c', 'D' => 'grade-d', default => 'grade-f',
};
$section ??= 'overview';
$sectionLabel = [
    'overview'     => 'Overview',
    'models'       => 'Models',
    'controllers'  => 'Controllers',
    'routes'       => 'Routes',
    'jobs'         => 'Jobs',
    'events'       => 'Events',
    'services'     => 'Services',
    'repositories' => 'Repositories',
    'observers'    => 'Observers',
    'policies'     => 'Policies',
    'modules'      => 'Modules',
    'middleware'   => 'Middleware',
    'packages'     => 'Packages',
    'ai'           => 'AI Insights',
    'chat'         => 'AI Chat',
    'aidocs'       => 'AI Docs',
][$section] ?? 'Overview';
$nav = fn(string $s) => $section === $s ? 'nav-item nav-active' : 'nav-item';
@endphp

{{-- ══ SIDEBAR ══ --}}
<aside class="sidebar" id="sidebar">
    <div id="nav-indicator"></div>
    <div class="sidebar__brand">
        <div class="mark">
            <img src="{{ route('laradar.asset', ['filename' => 'laradar-icon.svg']) }}" alt="Laradar" width="34" height="34" style="display:block;object-fit:cover;border-radius:8px;">
        </div>
        <div><strong>Laradar</strong></div>
    </div>

    @if(!empty($score))
    @php
        $scorePct   = $score['max'] > 0 ? ($score['score'] / $score['max']) * 100 : 0;
        $scoreColor = $scorePct >= 90 ? 'var(--emerald)' : ($scorePct >= 70 ? '#FBBF24' : ($scorePct >= 50 ? '#FB923C' : 'var(--rose)'));
        $scoreBg    = $scorePct >= 90 ? 'rgba(52,211,153,0.12)' : ($scorePct >= 70 ? 'rgba(251,191,36,0.12)' : ($scorePct >= 50 ? 'rgba(251,146,60,0.12)' : 'rgba(244,63,94,0.12)'));
        $scoreBorder= $scorePct >= 90 ? 'rgba(52,211,153,0.3)' : ($scorePct >= 70 ? 'rgba(251,191,36,0.3)' : ($scorePct >= 50 ? 'rgba(251,146,60,0.3)' : 'rgba(244,63,94,0.3)'));
    @endphp
    <div class="score-spin-border" style="margin-bottom:20px;padding:14px 10px;border-radius:10px;">
        <span style="font-family:var(--font-mono);font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:var(--text-faint);display:block;margin-bottom:8px;">Score</span>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <span style="font-family:var(--font-mono);font-size:24px;font-weight:700;color:var(--text);">{{ $score['score'] }}<span style="font-size:13px;color:var(--text-faint);">/{{ $score['max'] }}</span></span>
            <span style="font-family:var(--font-mono);font-size:11px;font-weight:700;padding:2px 8px;border-radius:6px;color:{{ $scoreColor }};background:{{ $scoreBg }};border:1px solid {{ $scoreBorder }};">{{ $grade }}</span>
        </div>
        <div class="atlas-score-bar"><div class="atlas-score-fill" id="sidebar-score-bar" data-score-w="{{ round(($score['score']/max(1,$score['max']))*100) }}" style="width:0;background:{{ $scoreColor }};"></div></div>
    </div>
    @endif

    <nav>
        <div class="nav-group">
            <button onclick="navigate('overview')" id="nav-overview" class="{{ $nav('overview') }}" title="Overview">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span class="nav-label">Overview</span>
            </button>
            <button onclick="navigate('ai')" id="nav-ai" class="{{ $nav('ai') }}" title="AI Insights">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                <span class="nav-label">AI Insights
                @if(config('laradar.ai.enabled', false))
                <span style="margin-left:6px;width:8px;height:8px;border-radius:50%;background:var(--emerald);box-shadow:0 0 0 3px rgba(52,211,153,0.18);display:inline-block;vertical-align:middle;"></span>
                @endif
                </span>
            </button>
            <button onclick="navigate('chat')" id="nav-chat" class="{{ $nav('chat') }}" title="AI Chat">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <span class="nav-label">AI Chat</span>
            </button>
            <button onclick="navigate('aidocs')" id="nav-aidocs" class="{{ $nav('aidocs') }}" title="AI Docs">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span class="nav-label">AI Docs</span>
            </button>
        </div>

        <div class="nav-group">
            <span class="nav-group__label">Core</span>
            <button onclick="navigate('models')" id="nav-models" class="{{ $nav('models') }}" title="Models">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                <span class="nav-label">Models</span>
                @if(($summary['models']??0)>0)<span class="nav-badge">{{ $summary['models'] }}</span>@endif
            </button>
            <button onclick="navigate('controllers')" id="nav-controllers" class="{{ $nav('controllers') }}" title="Controllers">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                <span class="nav-label">Controllers</span>
                @if(($summary['controllers']??0)>0)<span class="nav-badge">{{ $summary['controllers'] }}</span>@endif
            </button>
            <button onclick="navigate('routes')" id="nav-routes" class="{{ $nav('routes') }}" title="Routes">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span class="nav-label">Routes</span>
                @if(($rs['total']??0)>0)<span class="nav-badge">{{ $rs['total'] }}</span>@endif
            </button>
        </div>

        <div class="nav-group">
            <span class="nav-group__label">Components</span>
            <button onclick="navigate('jobs')" id="nav-jobs" class="{{ $nav('jobs') }}" title="Jobs">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span class="nav-label">Jobs</span>
                @if(($summary['jobs']??0)>0)<span class="nav-badge">{{ $summary['jobs'] }}</span>@endif
            </button>
            <button onclick="navigate('events')" id="nav-events" class="{{ $nav('events') }}" title="Events">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span class="nav-label">Events</span>
                @if(($summary['events']??0)>0)<span class="nav-badge">{{ $summary['events'] }}</span>@endif
            </button>
            <button onclick="navigate('services')" id="nav-services" class="{{ $nav('services') }}" title="Services">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="nav-label">Services</span>
                @if(($summary['services']??0)>0)<span class="nav-badge">{{ $summary['services'] }}</span>@endif
            </button>
            <button onclick="navigate('repositories')" id="nav-repositories" class="{{ $nav('repositories') }}" title="Repositories">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                <span class="nav-label">Repositories</span>
                @if(($summary['repositories']??0)>0)<span class="nav-badge">{{ $summary['repositories'] }}</span>@endif
            </button>
            <button onclick="navigate('observers')" id="nav-observers" class="{{ $nav('observers') }}" title="Observers">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span class="nav-label">Observers</span>
                @if(($summary['observers']??0)>0)<span class="nav-badge">{{ $summary['observers'] }}</span>@endif
            </button>
            <button onclick="navigate('policies')" id="nav-policies" class="{{ $nav('policies') }}" title="Policies">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span class="nav-label">Policies</span>
                @if(($summary['policies']??0)>0)<span class="nav-badge">{{ $summary['policies'] }}</span>@endif
            </button>
            <button onclick="navigate('modules')" id="nav-modules" class="{{ $nav('modules') }}" title="Modules">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span class="nav-label">Modules</span>
                @if(($summary['modules']??0)>0)<span class="nav-badge">{{ $summary['modules'] }}</span>@endif
            </button>
            <button onclick="navigate('middleware')" id="nav-middleware" class="{{ $nav('middleware') }}" title="Middleware">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span class="nav-label">Middleware</span>
                @php $mwCount = count($rs['middleware_usage']??[]); @endphp
                @if($mwCount > 0)<span class="nav-badge">{{ $mwCount }}</span>@endif
            </button>
            <button onclick="navigate('packages')" id="nav-packages" class="{{ $nav('packages') }}" title="Packages">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                <span class="nav-label">Packages</span>
                @if(($summary['packages']??0)>0)<span class="nav-badge">{{ $summary['packages'] }}</span>@endif
            </button>
        </div>


    </nav>

</aside>

{{-- Sidebar collapse toggle (sits on right edge of sidebar) --}}
<button id="sidebar-collapse-btn" onclick="toggleSidebarDesktop()" title="Toggle sidebar" aria-label="Toggle sidebar">
    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
</button>

{{-- ══ MAIN ══ --}}
<main class="content" style="display:flex;flex-direction:column;">

{{-- ══ TOPBAR ══ --}}
<div id="sidebar-overlay" onclick="toggleSidebar()"></div>
<header class="topbar">
    <button id="menu-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
    <div class="breadcrumb">
        <b id="topbar-section">{{ $sectionLabel }}</b>
        <button id="topbar-back-btn" onclick="topbarGoBack()" class="topbar-back-btn">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span id="topbar-back-label">Back</span>
        </button>
    </div>
    <div style="display:flex;align-items:center;gap:10px;margin-left:auto;">
        <div class="sync-pill">
            <span class="sync-dot"></span>
            Laravel {{ $data['laravel_version'] }} · {{ $data['project']['name'] }}
        </div>
    </div>
</header>

{{-- PAGE CONTENT --}}
@isset($model)
    <div class="p-6 section-pane">
        @yield('content')
    </div>
@else
    @foreach(['overview','models','controllers','routes','jobs','events','services','repositories','observers','policies','modules','middleware','packages','ai','chat','aidocs'] as $__sec)
    <div id="sec-{{ $__sec }}" class="p-6 section-pane" @if($__sec !== $section) style="display:none" @endif>
        @include('laradar::sections.' . $__sec)
    </div>
    @endforeach
@endisset

</main>

{{-- ── Doc Preview Modal ──────────────────────────────────────────────────── --}}
<div id="doc-modal" class="doc-modal-ov" style="display:none;" onclick="if(event.target===this)closeDocModal()">
    <div class="doc-modal-box">
        <div class="doc-modal-head">
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="width:8px;height:8px;border-radius:50%;background:var(--cyan);flex:none;"></span>
                <h3 id="doc-modal-title" style="font-family:var(--font-mono);font-size:14px;font-weight:700;color:var(--text);margin:0;"></h3>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <button id="doc-modal-dl-md" class="atlas-btn" style="font-size:11px;padding:5px 12px;border-radius:7px;gap:5px;">
                    <svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    .md
                </button>
                <button id="doc-modal-dl-html" class="atlas-btn atlas-btn--cyan" style="font-size:11px;padding:5px 12px;border-radius:7px;gap:5px;">
                    <svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    .html
                </button>
                <button onclick="closeDocModal()" style="width:30px;height:30px;border-radius:8px;border:1px solid var(--border);background:var(--bg-sunken);color:var(--text-dim);font-size:18px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;flex:none;">&#x2715;</button>
            </div>
        </div>
        <div class="doc-modal-body doc-r" id="doc-modal-body"></div>
    </div>
</div>

<script>
const APP = @json($data);
const SECTIONS = ['overview','models','controllers','routes','jobs','events','services','repositories','observers','policies','modules','middleware','packages','ai','chat','aidocs'];

let mapTreeRendered = false;
let _navTimer = null;
function _moveNavIndicator(s) {
    const ind  = document.getElementById('nav-indicator');
    const item = document.getElementById('nav-' + s);
    if (!ind || !item) return;
    const r = item.getBoundingClientRect();
    ind.style.top    = r.top + 'px';
    ind.style.height = r.height + 'px';
}

const LARADAR_ROUTES = {
    overview:     '{{ route("laradar.dashboard") }}',
    models:       '{{ route("laradar.models") }}',
    controllers:  '{{ route("laradar.controllers") }}',
    routes:       '{{ route("laradar.routes") }}',
    jobs:         '{{ route("laradar.jobs") }}',
    events:       '{{ route("laradar.events") }}',
    services:     '{{ route("laradar.services") }}',
    repositories: '{{ route("laradar.repositories") }}',
    observers:    '{{ route("laradar.observers") }}',
    policies:     '{{ route("laradar.policies") }}',
    modules:      '{{ route("laradar.modules") }}',
    middleware:   '{{ route("laradar.middleware") }}',
    packages:     '{{ route("laradar.packages") }}',
    ai:           '{{ route("laradar.ai") }}',
    chat:         '{{ route("laradar.chat") }}',
    aidocs:       '{{ route("laradar.aidocs") }}',
};

const _ALL_SECTION_LABELS = {
    overview:'Overview', models:'Models', controllers:'Controllers',
    routes:'Route Explorer', jobs:'Jobs', events:'Events', services:'Services',
    repositories:'Repositories', observers:'Observers', policies:'Policies',
    modules:'Modules', middleware:'Middleware', packages:'Packages',
    ai:'AI Insights', chat:'AI Chat', aidocs:'AI Docs',
};

function navigate(s) {
    if (!SECTIONS.includes(s)) return;
    const sb = document.querySelector('.sidebar');
    if (sb && sb.classList.contains('is-open')) toggleSidebar();
    const navEl = sb ? sb.querySelector('nav') : null;
    localStorage.setItem('laradar_sidebar_scroll', navEl ? navEl.scrollTop : (sb ? sb.scrollTop : 0));
    SECTIONS.forEach(sec => {
        const el = document.getElementById('sec-' + sec);
        if (el) el.style.display = 'none';
    });
    const target = document.getElementById('sec-' + s);
    if (!target) {
        // Model detail page — section panes not in DOM, do full navigation
        window.location.href = LARADAR_ROUTES[s];
        return;
    }
    target.style.display = '';
    // If we were in a detail view, reset to list when navigating to a section
    if (_activeDetailType) { showList(_activeDetailType); }
    history.pushState({ section: s }, '', LARADAR_ROUTES[s]);
    document.querySelectorAll('.nav-item').forEach(btn => btn.classList.remove('nav-active'));
    const navBtn = document.getElementById('nav-' + s);
    if (navBtn) navBtn.classList.add('nav-active');
    const labelEl = document.getElementById('topbar-section');
    if (labelEl) labelEl.textContent = _ALL_SECTION_LABELS[s] || s;
    _moveNavIndicator(s);
    const content = document.querySelector('.content');
    if (content) content.scrollTop = 0;
}

history.replaceState({ section: '{{ $section }}' }, '', window.location.href);

window.addEventListener('popstate', (e) => {
    const s = e.state?.section || 'overview';
    SECTIONS.forEach(sec => {
        const el = document.getElementById('sec-' + sec);
        if (el) el.style.display = 'none';
    });
    const target = document.getElementById('sec-' + s);
    if (target) target.style.display = '';
    document.querySelectorAll('.nav-item').forEach(btn => btn.classList.remove('nav-active'));
    const navBtn = document.getElementById('nav-' + s);
    if (navBtn) navBtn.classList.add('nav-active');
    const labelEl = document.getElementById('topbar-section');
    if (labelEl) labelEl.textContent = _ALL_SECTION_LABELS[s] || s;
    _moveNavIndicator(s);
    const content = document.querySelector('.content');
    if (content) content.scrollTop = 0;
});

function _atlasTheme(el) {
    // Light theme: Tailwind's native colours are correct — no post-processing needed.
}

const _SECTION_LABELS = {
    models:'Models', controllers:'Controllers', routes:'Route Explorer',
    events:'Events', services:'Services',
    repositories:'Repositories', observers:'Observers', policies:'Policies',
};
let _activeDetailType = null;

function _showBackBtn(label) {
    document.getElementById('topbar-section').style.display = 'none';
    document.getElementById('topbar-back-label').textContent = label;
    const btn = document.getElementById('topbar-back-btn');
    btn.classList.remove('is-visible');
    void btn.offsetWidth; // force reflow so animation replays
    btn.classList.add('is-visible');
}

function _hideBackBtn() {
    document.getElementById('topbar-section').style.display = '';
    const btn = document.getElementById('topbar-back-btn');
    btn.classList.remove('is-visible');
}

function showDetail(type, idx) {
    document.getElementById(type + '-list').style.display = 'none';
    document.getElementById(type + '-detail').style.display = 'block';
    const contentEl = document.getElementById(type + '-detail-content');
    contentEl.innerHTML = renderDetail(type, APP[type][idx]);
    _atlasTheme(contentEl);
    _activeDetailType = type;
    _showBackBtn('Back to ' + (_SECTION_LABELS[type] || type));
}

function showList(type) {
    document.getElementById(type + '-list').style.display = 'block';
    document.getElementById(type + '-detail').style.display = 'none';
    _activeDetailType = null;
    _hideBackBtn();
}

function topbarGoBack() {
    if (_activeDetailType) showList(_activeDetailType);
}

function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('is-open');
    document.getElementById('sidebar-overlay').classList.toggle('is-open');
}

function toggleSidebarDesktop() {
    const layout = document.querySelector('.atlas-layout');
    const collapsed = layout.classList.toggle('sidebar-collapsed');
    document.documentElement.classList.toggle('sidebar-pre-collapsed', collapsed);
    localStorage.setItem('laradar_sidebar', collapsed ? '0' : '1');
}

// Sync html class → atlas-layout class (html class applied in <head> to prevent flash)
// Also restore sidebar scroll position after navigation
document.addEventListener('DOMContentLoaded', function() {
    if (document.documentElement.classList.contains('sidebar-pre-collapsed')) {
        document.querySelector('.atlas-layout').classList.add('sidebar-collapsed');
    }
    const sb = document.getElementById('sidebar');
    const saved = localStorage.getItem('laradar_sidebar_scroll');
    if (sb && saved) {
        const pos = parseInt(saved, 10);
        const nav = sb.querySelector('nav');
        if (nav) nav.scrollTop = pos;
        else sb.scrollTop = pos;
        localStorage.removeItem('laradar_sidebar_scroll');
    }
});

function filterGrid(type) {
    const q = document.getElementById(type + '-search').value.toLowerCase();
    document.querySelectorAll('#' + type + '-grid [data-name]').forEach(el => {
        el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
    const lv = document.getElementById('mds-list-view');
    if (lv && type === 'models') lv.querySelectorAll('[data-name]').forEach(el => {
        el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
}

function filterPackages() {
    const q = document.getElementById('packages-search').value.toLowerCase();
    document.querySelectorAll('#packages-categories .pkg-card').forEach(el => {
        el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
    document.querySelectorAll('#packages-categories > div').forEach(cat => {
        const visible = [...cat.querySelectorAll('.pkg-card')].some(c => c.style.display !== 'none');
        cat.style.display = visible ? '' : 'none';
    });
}

function filterRoutes() {
    const q   = (document.getElementById('routes-search')?.value || '').toLowerCase();
    const mf  = document.getElementById('routes-method-filter')?.value || '';
    const mwf = (document.getElementById('routes-mw-filter')?.value || '').toLowerCase();
    document.querySelectorAll('.route-row').forEach(row => {
        const handler  = (row.querySelector('td:nth-child(3)')?.textContent || '').toLowerCase();
        const textOk   = !q   || row.dataset.uri.includes(q) || handler.includes(q);
        const methodOk = !mf  || row.dataset.methods.includes(mf);
        const mwOk     = !mwf || row.dataset.mw.includes(mwf);
        row.style.display = textOk && methodOk && mwOk ? '' : 'none';
    });
}

// ── Detail renderers ──────────────────────────────────────────────────────────

function renderDetail(type, item) {
    const map = {
        models: renderModel,
        jobs: renderJob, events: renderEvent,
        services: x => renderService(x, 'Service'),
        repositories: x => renderService(x, 'Repository'),
        observers: renderObserver, policies: renderPolicy,
    };
    return (map[type] || (() => ''))(item);
}

function detailCard(title, body) {
    return `<div style="background:var(--bg-elevated);border-radius:12px;border:1px solid var(--border);padding:20px;margin-bottom:16px;"><h3 style="font-size:14px;font-weight:600;color:var(--text);margin-bottom:12px;margin-top:0;">${title}</h3>${body}</div>`;
}

function pill(text, color) {
    const c = color || '#6B778C';
    return `<span style="font-size:11px;font-family:var(--font-mono);padding:2px 8px;border-radius:5px;background:rgba(142,155,184,.12);color:${c};border:1px solid rgba(142,155,184,.2);">${text}</span>`;
}

function avatar(letter, color) {
    const c = color || '#6B778C';
    const rgb = c.replace('#','').match(/.{2}/g).map(x=>parseInt(x,16)).join(',');
    return `<div style="width:48px;height:48px;border-radius:12px;background:rgba(${rgb},.15);border:1px solid rgba(${rgb},.3);display:flex;align-items:center;justify-content:center;color:${c};font-size:18px;font-weight:700;flex:none;">${letter}</div>`;
}

const MDS_PALETTE = [
    {color:'var(--cyan)',    rgb:'255,45,32',   hex:'#FF2D20'},
    {color:'var(--violet)',  rgb:'167,139,250',  hex:'#A78BFA'},
    {color:'var(--emerald)', rgb:'52,211,153',   hex:'#34D399'},
    {color:'var(--amber)',   rgb:'251,191,36',   hex:'#FBBF24'},
    {color:'var(--rose)',    rgb:'248,113,113',  hex:'#F87171'},
    {color:'var(--sky)',     rgb:'96,165,250',   hex:'#60A5FA'},
];
const MDS_REL_CFG = {
    hasMany:       {hex:'#34D399',color:'var(--emerald)',bg:'rgba(52,211,153,.12)', border:'rgba(52,211,153,.3)'},
    hasOne:        {hex:'#FF2D20',color:'var(--cyan)',   bg:'rgba(255,45,32,.12)',  border:'rgba(255,45,32,.3)'},
    belongsTo:     {hex:'#60A5FA',color:'var(--sky)',    bg:'rgba(96,165,250,.12)', border:'rgba(96,165,250,.3)'},
    belongsToMany: {hex:'#A78BFA',color:'var(--violet)', bg:'rgba(167,139,250,.12)',border:'rgba(167,139,250,.3)'},
    morphMany:     {hex:'#F87171',color:'var(--rose)',   bg:'rgba(248,113,113,.12)',border:'rgba(248,113,113,.3)'},
    morphTo:       {hex:'#F87171',color:'var(--rose)',   bg:'rgba(248,113,113,.12)',border:'rgba(248,113,113,.3)'},
    morphOne:      {hex:'#F87171',color:'var(--rose)',   bg:'rgba(248,113,113,.12)',border:'rgba(248,113,113,.3)'},
    hasManyThrough:{hex:'#FBBF24',color:'var(--amber)',  bg:'rgba(251,191,36,.12)', border:'rgba(251,191,36,.3)'},
};

function _mdsColor(name) {
    const code = Math.abs((name || 'A').charCodeAt(0) - 65);
    return MDS_PALETTE[code % MDS_PALETTE.length];
}

function renderModel(m) {
    const esc = s => String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    const pal = _mdsColor(m.name || 'A');

    // Find which controllers use this model via dep edges
    const depEdges = APP.dependencies?.edges || [];
    const usedBy = [];
    depEdges.forEach(e => {
        if (e.to === m.name || e.to === (m.name||'').split('\\').pop()) {
            const ctrl = (APP.controllers||[]).find(c => c.name === e.from);
            if (ctrl && !usedBy.find(u => u.name === ctrl.name)) {
                const rCnt = (APP.routes||[]).filter(r => (r.controller?.class||r.action||'').includes(ctrl.name)).length;
                usedBy.push({name:ctrl.name, routes:rCnt});
            }
        }
    });

    // Build unified field map from fillable + hidden + casts
    const fieldMap = new Map();
    (m.fillable||[]).forEach(f => fieldMap.set(f, {fillable:true, hidden:false, cast:null}));
    (m.hidden||[]).forEach(f => {
        const ex = fieldMap.get(f) || {fillable:false, hidden:false, cast:null};
        ex.hidden = true; fieldMap.set(f, ex);
    });
    Object.entries(m.casts||{}).forEach(([f,type]) => {
        const ex = fieldMap.get(f) || {fillable:false, hidden:false, cast:null};
        ex.cast = String(type); fieldMap.set(f, ex);
    });

    const fillCnt = m.fillable?.length || 0;
    const hideCnt = m.hidden?.length   || 0;
    const relCnt  = m.relationships?.length || 0;
    const castCnt = Object.keys(m.casts||{}).length;
    const traitCnt= m.traits?.length || 0;
    const initial = (m.name?.[0]||'?').toUpperCase();
    const traits  = (m.traits||[]).map(t => t.split('\\').pop());

    // ── 2-column wrapper ──
    let h = `<div class="mds-det-wrap">`;

    // ── LEFT SIDEBAR ──
    h += `<aside class="mds-sidebar">
      <div class="mds-side-card">
        <div class="mds-side-top">
          <div class="mds-side-av" style="background:rgba(${pal.rgb},.18);color:${pal.color};border-color:rgba(${pal.rgb},.4);">${esc(initial)}</div>
          <p class="mds-side-name">${esc(m.name)}</p>
          <p class="mds-side-tbl">
            <svg style="width:10px;height:10px;display:inline;margin-right:4px;vertical-align:middle;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0018 0V5"/></svg>
            ${esc(m.table)}
          </p>
          <p class="mds-side-ns">${esc(m.namespace||'')}</p>
        </div>

        <div class="mds-side-stats">
          ${fillCnt  ? `<div class="mds-side-stat" onclick="mdsTab('fields')" title="Go to fields"><span class="mds-side-stat-lbl">Fillable</span><span class="mds-side-stat-val" style="color:${pal.color};">${fillCnt}</span></div>` : ''}
          ${hideCnt  ? `<div class="mds-side-stat" onclick="mdsTab('fields')" title="Go to fields"><span class="mds-side-stat-lbl">Hidden</span><span class="mds-side-stat-val" style="color:#FF2D20;">${hideCnt}</span></div>` : ''}
          ${castCnt  ? `<div class="mds-side-stat" onclick="mdsTab('fields')" title="Go to fields"><span class="mds-side-stat-lbl">Casts</span><span class="mds-side-stat-val" style="color:var(--amber);">${castCnt}</span></div>` : ''}
          ${relCnt   ? `<div class="mds-side-stat" onclick="mdsTab('relations')" title="Go to relationships"><span class="mds-side-stat-lbl">Relationships</span><span class="mds-side-stat-val" style="color:var(--violet);">${relCnt}</span></div>` : ''}
          ${usedBy.length ? `<div class="mds-side-stat" onclick="mdsTab('usedby')" title="Go to used by"><span class="mds-side-stat-lbl">Used by</span><span class="mds-side-stat-val" style="color:var(--sky);">${usedBy.length}</span></div>` : ''}
          ${!fillCnt && !relCnt && !usedBy.length ? `<p style="font-size:12px;color:var(--text-faint);text-align:center;padding:8px 0;">No data available</p>` : ''}
        </div>

        <div class="mds-side-meta">
          ${m.observer ? `<p style="font-size:10.5px;color:var(--text-faint);font-family:var(--font-mono);margin-bottom:6px;text-transform:uppercase;letter-spacing:.06em;font-weight:700;">Observer</p>
          <span class="mds-side-chip" style="background:rgba(251,191,36,.1);color:var(--amber);border-color:rgba(251,191,36,.25);">${esc(m.observer.split('\\').pop())}</span>` : ''}
          ${traits.length ? `<p style="font-size:10.5px;color:var(--text-faint);font-family:var(--font-mono);margin:${m.observer?'12':'0'}px 0 8px;text-transform:uppercase;letter-spacing:.06em;font-weight:700;">Traits</p>
          <div style="display:flex;flex-wrap:wrap;gap:5px;">${traits.map(t => `<span class="mds-side-chip" style="background:rgba(167,139,250,.1);color:var(--violet);border-color:rgba(167,139,250,.25);">${esc(t)}</span>`).join('')}</div>` : ''}
          ${m.timestamps !== false ? `<span class="mds-side-chip" style="background:rgba(96,165,250,.08);color:var(--sky);border-color:rgba(96,165,250,.2);margin-top:8px;">timestamps</span>` : ''}
        </div>
      </div>
    </aside>`;

    // ── RIGHT MAIN CONTENT ──
    h += `<div>`;

    // Tabs
    h += `<div class="mds-tabs" id="mds-tabs-row">
      <button class="mds-tab-btn active" id="mds-tab-fields"    onclick="mdsTab('fields')">
        Fields ${fieldMap.size ? `<span style="font-size:10px;padding:1px 6px;border-radius:4px;background:rgba(${pal.rgb},.15);color:${pal.color};margin-left:5px;font-family:var(--font-mono);">${fieldMap.size}</span>` : ''}
      </button>
      ${relCnt ? `<button class="mds-tab-btn" id="mds-tab-relations" onclick="mdsTab('relations')">Relationships <span style="font-size:10px;padding:1px 6px;border-radius:4px;background:rgba(167,139,250,.15);color:var(--violet);margin-left:5px;font-family:var(--font-mono);">${relCnt}</span></button>` : ''}
      ${usedBy.length ? `<button class="mds-tab-btn" id="mds-tab-usedby" onclick="mdsTab('usedby')">Used By <span style="font-size:10px;padding:1px 6px;border-radius:4px;background:rgba(255,45,32,.12);color:var(--cyan);margin-left:5px;font-family:var(--font-mono);">${usedBy.length}</span></button>` : ''}
    </div>`;

    // ── Fields Tab ──
    h += `<div class="mds-tab-pane active" id="mds-pane-fields">`;
    if (fieldMap.size) {
        h += `<div class="mds-schema-wrap">
          <table class="mds-schema-tbl">
            <thead><tr>
              <th>Field</th><th>Status</th><th>Cast Type</th>
            </tr></thead>
            <tbody>`;
        fieldMap.forEach((info, fname) => {
            h += `<tr>
              <td><span class="mds-field-name">${esc(fname)}</span></td>
              <td>
                ${info.fillable ? `<span class="mds-fbadge fill">FILLABLE</span>` : ''}
                ${info.hidden   ? `<span class="mds-fbadge hide">HIDDEN</span>`   : ''}
              </td>
              <td>${info.cast ? `<span class="mds-cast-val">${esc(info.cast)}</span>` : '<span style="color:var(--text-faint);font-size:12px;">—</span>'}</td>
            </tr>`;
        });
        h += `</tbody></table></div>`;
    }

    // Feature flags
    const flags = [];
    if (traits.some(t => t.includes('SoftDeletes'))) flags.push({label:'SoftDeletes', color:'var(--rose)',    bg:'rgba(248,113,113,.1)', border:'rgba(248,113,113,.2)'});
    if (traits.some(t => t.includes('HasFactory')))  flags.push({label:'HasFactory',  color:'var(--emerald)', bg:'rgba(52,211,153,.1)',  border:'rgba(52,211,153,.2)'});
    if (traits.some(t => t.includes('Searchable')))  flags.push({label:'Searchable',  color:'var(--sky)',     bg:'rgba(96,165,250,.1)',  border:'rgba(96,165,250,.2)'});
    if (m.timestamps !== false)                       flags.push({label:'$timestamps = true', color:'var(--sky)', bg:'rgba(96,165,250,.08)', border:'rgba(96,165,250,.15)'});
    if (flags.length) {
        h += `<div class="mds-flag-row">${flags.map(f => `<span class="mds-flag" style="color:${f.color};background:${f.bg};border-color:${f.border};">${esc(f.label)}</span>`).join('')}</div>`;
    }
    if (!fieldMap.size && !flags.length) {
        h += `<p style="color:var(--text-faint);font-size:13px;text-align:center;padding:40px 0;">No fillable, hidden or cast fields detected.</p>`;
    }
    h += `</div>`; // end fields pane

    // ── Relationships Tab ──
    if (relCnt) {
        h += `<div class="mds-tab-pane" id="mds-pane-relations">`;
        (m.relationships||[]).forEach(r => {
            const rc  = MDS_REL_CFG[r.type] || {color:'var(--text-dim)',bg:'rgba(91,103,133,.1)',border:'var(--border)'};
            const rel = r.related ? r.related.split('\\').pop() : '—';
            const navIdx = (APP.models||[]).findIndex(md => md.name === rel);
            h += `<div class="mds-rel-card" style="border-color:var(--border);" onmouseenter="this.style.borderColor='${rc.border}'" onmouseleave="this.style.borderColor='var(--border)'">
              <span class="mds-rel-method">${esc(r.method)}()</span>
              <span class="mds-rel-type" style="color:${rc.color};background:${rc.bg};border-color:${rc.border};">${esc(r.type)}</span>
              <span class="mds-rel-arrow">→</span>
              <span class="mds-rel-target">${esc(rel)}</span>
              ${navIdx >= 0
                ? `<button class="mds-nav-btn" style="color:${rc.color};background:${rc.bg};border-color:${rc.border};" onclick="event.stopPropagation();showDetail('models',${navIdx});">View →</button>`
                : '<span></span>'}
            </div>`;
        });
        h += `</div>`;
    }

    // ── Used By Tab ──
    if (usedBy.length) {
        h += `<div class="mds-tab-pane" id="mds-pane-usedby">`;
        usedBy.forEach(u => {
            const ci = (APP.controllers||[]).findIndex(c => c.name === u.name);
            h += `<div class="mds-usedby-card">
              <div>
                <span style="font-size:14px;font-weight:700;color:var(--text);">${esc(u.name)}</span>
                <span style="font-size:11px;color:var(--text-faint);margin-left:10px;font-family:var(--font-mono);">${u.routes} route${u.routes!==1?'s':''}</span>
              </div>
              ${ci >= 0 ? `<button class="mds-nav-btn" style="color:var(--sky);background:rgba(96,165,250,.08);border-color:rgba(96,165,250,.2);" onclick="event.stopPropagation();navigate('controllers');setTimeout(()=>showDetail('controllers',${ci}),100);">View controller →</button>` : ''}
            </div>`;
        });
        h += `</div>`;
    }

    h += `</div></div>`; // close main + wrap
    return h;
}

function mdsTab(tab) {
    document.querySelectorAll('.mds-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.mds-tab-pane').forEach(p => p.classList.remove('active'));
    const btn  = document.getElementById('mds-tab-' + tab);
    const pane = document.getElementById('mds-pane-' + tab);
    if (btn)  btn.classList.add('active');
    if (pane) pane.classList.add('active');
}

function mdsView(view) {
    document.getElementById('mds-grid-view').style.display = view === 'grid' ? '' : 'none';
    document.getElementById('mds-list-view').style.display = view === 'list' ? '' : 'none';
    document.getElementById('mds-vbtn-grid').classList.toggle('active', view === 'grid');
    document.getElementById('mds-vbtn-list').classList.toggle('active', view === 'list');
}

function renderJob(j) {
    const esc = v => String(v ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    const clr = '#FBBF24', rgb = '251,191,36';
    let h = `
    <div style="background:var(--bg-elevated);border-radius:16px;border:1px solid var(--border);padding:24px;margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
            <div style="width:56px;height:56px;border-radius:14px;background:rgba(${rgb},.13);border:1px solid rgba(${rgb},.3);display:flex;align-items:center;justify-content:center;flex:none;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="${clr}" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px;">
                    <h2 style="font-size:22px;font-weight:700;color:var(--text);margin:0;">${esc(j.name)}</h2>
                    ${j.should_queue ? `<span style="font-size:11px;background:rgba(${rgb},.12);color:${clr};border:1px solid rgba(${rgb},.3);padding:2px 10px;border-radius:20px;font-weight:600;">Queued</span>` : ''}
                </div>
                <p style="font-size:13px;color:var(--text-dim);font-family:var(--font-mono);margin:0 0 2px;">${esc(j.namespace)}</p>
                <p style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);margin:0;">${esc(j.path || '')}</p>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">
            <div style="background:rgba(${rgb},.08);border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(${rgb},.18);">
                <p style="font-size:13px;font-weight:700;color:${clr};margin:0 0 2px;font-family:var(--font-mono);">${esc(j.queue || 'default')}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Queue</p>
            </div>
            <div style="background:rgba(96,165,250,.08);border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(96,165,250,.18);">
                <p style="font-size:22px;font-weight:700;color:#60A5FA;margin:0 0 2px;">${j.tries || '—'}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Max Tries</p>
            </div>
            <div style="background:rgba(167,139,250,.08);border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(167,139,250,.18);">
                <p style="font-size:22px;font-weight:700;color:#A78BFA;margin:0 0 2px;">${j.timeout ? esc(j.timeout)+'s' : '—'}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Timeout</p>
            </div>
            <div style="background:rgba(52,211,153,.08);border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(52,211,153,.18);">
                <p style="font-size:22px;font-weight:700;color:#34D399;margin:0 0 2px;">${j.delay ? esc(j.delay)+'s' : '—'}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Delay</p>
            </div>
        </div>
    </div>`;
    const flags = [
        j.should_queue  && `<span style="font-size:11px;font-weight:600;padding:5px 12px;border-radius:7px;background:rgba(${rgb},.1);color:${clr};">ShouldQueue</span>`,
        j.unique        && `<span style="font-size:11px;font-weight:600;padding:5px 12px;border-radius:7px;background:rgba(255,45,32,.08);color:#FF2D20;">ShouldBeUnique</span>`,
        j.encrypted     && `<span style="font-size:11px;font-weight:600;padding:5px 12px;border-radius:7px;background:rgba(167,139,250,.1);color:#A78BFA;">ShouldBeEncrypted</span>`,
    ].filter(Boolean);
    if (flags.length) h += detailCard('Queue Interfaces', `<div style="display:flex;flex-wrap:wrap;gap:8px;">${flags.join('')}</div>`);
    if (j.dependencies?.length) {
        const depItems = j.dependencies.map(d =>
            `<div style="display:flex;align-items:center;gap:8px;padding:9px 12px;background:rgba(167,139,250,.07);border-radius:9px;border:1px solid rgba(167,139,250,.18);">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2.5" style="flex:none;"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>
                <div><span style="font-size:11px;font-family:var(--font-mono);color:#6D28D9;font-weight:600;">${esc(d.type||d)}</span>${d.name?`<span style="font-size:10px;color:#9CA3AF;margin-left:5px;">$${esc(d.name)}</span>`:''}</div>
            </div>`
        ).join('');
        h += detailCard('Dependencies', `<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:8px;">${depItems}</div>`);
    }
    return h;
}

function renderEvent(e) {
    const esc = v => String(v ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    const clr = '#EC4899', rgb = '236,72,153';
    let h = `
    <div style="background:var(--bg-elevated);border-radius:16px;border:1px solid var(--border);padding:24px;margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
            <div style="width:56px;height:56px;border-radius:14px;background:rgba(${rgb},.13);border:1px solid rgba(${rgb},.3);display:flex;align-items:center;justify-content:center;flex:none;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="${clr}" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px;">
                    <h2 style="font-size:22px;font-weight:700;color:var(--text);margin:0;">${esc(e.name)}</h2>
                    <span style="font-size:11px;background:rgba(${rgb},.1);color:${clr};border:1px solid rgba(${rgb},.25);padding:2px 10px;border-radius:20px;font-weight:600;">Event</span>
                </div>
                <p style="font-size:13px;color:var(--text-dim);font-family:var(--font-mono);margin:0 0 2px;">${esc(e.namespace)}</p>
                <p style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);margin:0;">${esc(e.path || '')}</p>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
            <div style="background:rgba(${rgb},.08);border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(${rgb},.18);">
                <p style="font-size:22px;font-weight:700;color:${clr};margin:0 0 2px;">${(e.properties||[]).length}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Properties</p>
            </div>
            <div style="background:rgba(255,45,32,.07);border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(255,45,32,.15);">
                <p style="font-size:22px;font-weight:700;color:#FF2D20;margin:0 0 2px;">${e.should_broadcast ? 'Yes' : 'No'}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Broadcasts</p>
            </div>
            <div style="background:rgba(248,113,113,.08);border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(248,113,113,.18);">
                <p style="font-size:14px;font-weight:700;color:#F87171;margin:0 0 2px;">${e.broadcastNow ? 'Immediate' : 'Queued'}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Broadcast Mode</p>
            </div>
        </div>
    </div>`;
    if (e.properties?.length) {
        const props = e.properties.map(p =>
            `<span style="font-size:11px;font-family:var(--font-mono);padding:5px 12px;border-radius:7px;background:rgba(${rgb},.08);color:${clr};border:1px solid rgba(${rgb},.2);">${esc(p)}</span>`
        ).join('');
        h += detailCard('Payload Properties', `<div style="display:flex;flex-wrap:wrap;gap:8px;">${props}</div>`);
    }
    if (e.should_broadcast || e.broadcastNow) {
        const bFlags = [
            e.should_broadcast && `<span style="font-size:11px;font-weight:600;padding:5px 14px;border-radius:7px;background:rgba(255,45,32,.08);color:#FF2D20;border:1px solid rgba(255,45,32,.2);">ShouldBroadcast</span>`,
            e.broadcastNow     && `<span style="font-size:11px;font-weight:600;padding:5px 14px;border-radius:7px;background:rgba(248,113,113,.1);color:#F87171;border:1px solid rgba(248,113,113,.2);">ShouldBroadcastNow</span>`,
        ].filter(Boolean);
        h += detailCard('Broadcast Interfaces', `<div style="display:flex;flex-wrap:wrap;gap:8px;">${bFlags.join('')}</div>`);
    }
    return h;
}

function renderService(s, type) {
    const esc = v => String(v ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    const isRepo = type === 'Repository';
    const clr    = isRepo ? '#0EA5E9' : '#8B5CF6';
    const rgb    = isRepo ? '14,165,233' : '139,92,246';
    const typeIcon = isRepo
        ? `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="${clr}" stroke-width="2"><path d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>`
        : `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="${clr}" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>`;
    let h = `
    <div style="background:var(--bg-elevated);border-radius:16px;border:1px solid var(--border);padding:24px;margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
            <div style="width:56px;height:56px;border-radius:14px;background:rgba(${rgb},.13);border:1px solid rgba(${rgb},.3);display:flex;align-items:center;justify-content:center;flex:none;">${typeIcon}</div>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px;">
                    <h2 style="font-size:22px;font-weight:700;color:var(--text);margin:0;">${esc(s.name)}</h2>
                    <span style="font-size:11px;background:rgba(${rgb},.1);color:${clr};border:1px solid rgba(${rgb},.25);padding:2px 10px;border-radius:20px;font-weight:600;">${type}</span>
                </div>
                <p style="font-size:13px;color:var(--text-dim);font-family:var(--font-mono);margin:0 0 2px;">${esc(s.namespace)}</p>
                <p style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);margin:0;">${esc(s.path || '')}</p>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
            <div style="background:rgba(${rgb},.08);border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(${rgb},.18);">
                <p style="font-size:22px;font-weight:700;color:${clr};margin:0 0 2px;">${s.method_count||0}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Public Methods</p>
            </div>
            <div style="background:rgba(167,139,250,.08);border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(167,139,250,.18);">
                <p style="font-size:22px;font-weight:700;color:#A78BFA;margin:0 0 2px;">${(s.dependencies||[]).length}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Dependencies</p>
            </div>
        </div>
    </div>`;
    if (s.dependencies?.length) {
        const depItems = s.dependencies.map(d =>
            `<div style="display:flex;align-items:center;gap:8px;padding:10px 12px;background:rgba(167,139,250,.07);border-radius:9px;border:1px solid rgba(167,139,250,.18);">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2.5" style="flex:none;"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>
                <span style="font-size:11px;font-family:var(--font-mono);color:#6D28D9;font-weight:600;">${esc(d)}</span>
            </div>`
        ).join('');
        h += detailCard('Dependencies', `<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:8px;">${depItems}</div>`);
    }
    if (s.methods?.length) {
        const methodItems = s.methods.map(m =>
            `<span style="font-size:11px;font-family:var(--font-mono);padding:5px 12px;border-radius:7px;background:rgba(${rgb},.08);color:${clr};border:1px solid rgba(${rgb},.18);">${esc(m)}</span>`
        ).join('');
        h += detailCard(`Public Methods (${s.methods.length})`, `<div style="display:flex;flex-wrap:wrap;gap:8px;">${methodItems}</div>`);
    }
    return h;
}

function renderObserver(o) {
    const esc = v => String(v ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    const clr = '#F97316', rgb = '249,115,22';
    const evtClr = {
        created:  {bg:'rgba(16,185,129,.1)',  color:'#059669'},
        creating: {bg:'rgba(16,185,129,.1)',  color:'#059669'},
        updated:  {bg:'rgba(59,130,246,.1)',  color:'#2563EB'},
        updating: {bg:'rgba(59,130,246,.1)',  color:'#2563EB'},
        deleted:  {bg:'rgba(239,68,68,.1)',   color:'#DC2626'},
        deleting: {bg:'rgba(239,68,68,.1)',   color:'#DC2626'},
        saved:    {bg:'rgba(20,184,166,.1)',  color:'#0D9488'},
        saving:   {bg:'rgba(20,184,166,.1)',  color:'#0D9488'},
        restored: {bg:'rgba(255,45,32,.08)',  color:'#FF2D20'},
    };
    let h = `
    <div style="background:var(--bg-elevated);border-radius:16px;border:1px solid var(--border);padding:24px;margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
            <div style="width:56px;height:56px;border-radius:14px;background:rgba(${rgb},.13);border:1px solid rgba(${rgb},.3);display:flex;align-items:center;justify-content:center;flex:none;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="${clr}" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px;">
                    <h2 style="font-size:22px;font-weight:700;color:var(--text);margin:0;">${esc(o.name)}</h2>
                    <span style="font-size:11px;background:rgba(${rgb},.1);color:${clr};border:1px solid rgba(${rgb},.25);padding:2px 10px;border-radius:20px;font-weight:600;">Observer</span>
                </div>
                <p style="font-size:13px;color:var(--text-dim);font-family:var(--font-mono);margin:0 0 2px;">${esc(o.namespace)}</p>
                <p style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);margin:0;">${esc(o.path || '')}</p>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
            <div style="background:rgba(${rgb},.08);border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(${rgb},.18);">
                <p style="font-size:15px;font-weight:700;color:${clr};margin:0 0 2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${esc(o.model || 'Unknown')}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Observes Model</p>
            </div>
            <div style="background:rgba(255,45,32,.07);border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(255,45,32,.15);">
                <p style="font-size:22px;font-weight:700;color:#FF2D20;margin:0 0 2px;">${(o.events||[]).length}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Lifecycle Hooks</p>
            </div>
        </div>
    </div>`;
    h += detailCard('Observes Model', `
        <div style="display:flex;align-items:center;gap:14px;padding:14px 16px;background:rgba(${rgb},.06);border-radius:10px;border:1px solid rgba(${rgb},.15);">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="${clr}" stroke-width="2" style="flex:none;"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
            <div>
                <p style="font-size:15px;font-weight:700;color:var(--text);margin:0 0 2px;">${esc(o.model || 'Unknown')}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Eloquent Model</p>
            </div>
        </div>`);
    if (o.events?.length) {
        const badges = o.events.map(ev => {
            const c = evtClr[ev] || {bg:'rgba(100,116,139,.1)', color:'#64748B'};
            return `<span style="font-size:11px;font-weight:600;padding:5px 13px;border-radius:7px;background:${c.bg};color:${c.color};">${esc(ev)}</span>`;
        }).join('');
        h += detailCard('Lifecycle Events', `<div style="display:flex;flex-wrap:wrap;gap:8px;">${badges}</div>`);
    }
    return h;
}

function renderPolicy(p) {
    const esc = v => String(v ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    const clr = '#FF2D20', rgb = '255,45,32';
    const actClr = {
        viewAny:     {bg:'rgba(59,130,246,.1)',  color:'#2563EB'},
        view:        {bg:'rgba(96,165,250,.1)',   color:'#3B82F6'},
        create:      {bg:'rgba(16,185,129,.1)',   color:'#059669'},
        update:      {bg:'rgba(245,158,11,.1)',   color:'#D97706'},
        delete:      {bg:'rgba(239,68,68,.1)',    color:'#DC2626'},
        restore:     {bg:'rgba(20,184,166,.1)',   color:'#0D9488'},
        forceDelete: {bg:'rgba(244,63,94,.1)',    color:'#E11D48'},
        before:      {bg:'rgba(139,92,246,.1)',   color:'#7C3AED'},
    };
    let h = `
    <div style="background:var(--bg-elevated);border-radius:16px;border:1px solid var(--border);padding:24px;margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
            <div style="width:56px;height:56px;border-radius:14px;background:rgba(${rgb},.13);border:1px solid rgba(${rgb},.3);display:flex;align-items:center;justify-content:center;flex:none;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="${clr}" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px;">
                    <h2 style="font-size:22px;font-weight:700;color:var(--text);margin:0;">${esc(p.name)}</h2>
                    <span style="font-size:11px;background:rgba(${rgb},.1);color:${clr};border:1px solid rgba(${rgb},.25);padding:2px 10px;border-radius:20px;font-weight:600;">Policy</span>
                </div>
                <p style="font-size:13px;color:var(--text-dim);font-family:var(--font-mono);margin:0 0 2px;">${esc(p.namespace)}</p>
                <p style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);margin:0;">${esc(p.path || '')}</p>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
            <div style="background:rgba(${rgb},.08);border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(${rgb},.18);">
                <p style="font-size:15px;font-weight:700;color:${clr};margin:0 0 2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${esc(p.model || 'Unknown')}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Guards Model</p>
            </div>
            <div style="background:rgba(52,211,153,.08);border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(52,211,153,.18);">
                <p style="font-size:22px;font-weight:700;color:#34D399;margin:0 0 2px;">${(p.actions||[]).length}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Policy Actions</p>
            </div>
        </div>
    </div>`;
    h += detailCard('Guards Model', `
        <div style="display:flex;align-items:center;gap:14px;padding:14px 16px;background:rgba(${rgb},.06);border-radius:10px;border:1px solid rgba(${rgb},.15);">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="${clr}" stroke-width="2" style="flex:none;"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
            <div>
                <p style="font-size:15px;font-weight:700;color:var(--text);margin:0 0 2px;">${esc(p.model || 'Unknown')}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Protected Eloquent Model</p>
            </div>
        </div>`);
    if (p.actions?.length) {
        const badges = p.actions.map(a => {
            const c = actClr[a] || {bg:'rgba(100,116,139,.1)', color:'#64748B'};
            return `<span style="font-size:11px;font-weight:600;padding:5px 13px;border-radius:7px;background:${c.bg};color:${c.color};">${esc(a)}</span>`;
        }).join('');
        h += detailCard('Policy Actions', `<div style="display:flex;flex-wrap:wrap;gap:8px;">${badges}</div>`);
    }
    return h;
}

// ── Model Relationship Map ────────────────────────────────────────────────────

const REL_COLORS = {
    hasMany:        { color:'#34D399', bg:'rgba(52,211,153,.13)',  border:'rgba(52,211,153,.3)'  },
    hasOne:         { color:'#FF2D20', bg:'rgba(255,45,32,.12)',   border:'rgba(255,45,32,.3)'   },
    belongsTo:      { color:'#60A5FA', bg:'rgba(96,165,250,.13)',  border:'rgba(96,165,250,.3)'  },
    belongsToMany:  { color:'#A78BFA', bg:'rgba(167,139,250,.13)', border:'rgba(167,139,250,.3)' },
    morphMany:      { color:'#FB923C', bg:'rgba(251,146,60,.13)',  border:'rgba(251,146,60,.3)'  },
    morphOne:       { color:'#FBBF24', bg:'rgba(251,191,36,.13)',  border:'rgba(251,191,36,.3)'  },
    morphTo:        { color:'#F87171', bg:'rgba(248,113,113,.13)', border:'rgba(248,113,113,.3)' },
    morphToMany:    { color:'#E879F9', bg:'rgba(232,121,249,.13)', border:'rgba(232,121,249,.3)' },
    hasManyThrough: { color:'#2DD4BF', bg:'rgba(45,212,191,.13)',  border:'rgba(45,212,191,.3)'  },
    hasOneThrough:  { color:'#38BDF8', bg:'rgba(56,189,248,.13)',  border:'rgba(56,189,248,.3)'  },
};

function buildRelBranch(modelName, depth, visited) {
    if (depth > 2 || visited.has(modelName)) return '';
    const model = (APP.models || []).find(m => m.name === modelName);
    if (!model) return '';

    const rels = model.relationships || [];
    if (rels.length === 0) return '';

    const newVis = new Set(visited);
    newVis.add(modelName);

    let html = `<div style="padding-left:16px;border-left:1px solid rgba(148,178,222,0.18);margin-top:2px;">`;

    rels.forEach((rel, i) => {
        const relName = rel.related ? rel.related.split('\\').pop() : null;
        const cfg     = REL_COLORS[rel.type] || { color:'#6B778C', bg:'rgba(142,155,184,.1)', border:'rgba(142,155,184,.25)' };
        const isLast  = i === rels.length - 1;
        const isCirc  = relName && newVis.has(relName);
        const hasSub  = relName && depth < 1 && !newVis.has(relName) &&
                        (APP.models || []).some(m => m.name === relName && (m.relationships||[]).length > 0);

        html += `<div style="display:flex;align-items:flex-start;gap:0;padding:3px 0;">
            <span style="color:rgba(148,178,222,0.3);font-family:var(--font-mono);font-size:11px;padding-top:2px;margin-right:8px;flex:none;user-select:none;">${isLast ? '└─' : '├─'}</span>
            <div style="flex:1;">
                <div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap;">
                    <span style="font-family:var(--font-mono);font-size:10px;padding:1px 6px;border-radius:4px;border:1px solid ${cfg.border};background:${cfg.bg};color:${cfg.color};flex:none;">${rel.type}</span>
                    <span style="font-size:13px;font-weight:600;color:var(--text);">${relName || '<em style="color:var(--text-faint);font-style:italic;">unknown</em>'}</span>
                    ${rel.method ? `<span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);">.${rel.method}()</span>` : ''}
                    ${isCirc ? '<span style="font-size:10px;color:var(--rose);">↩ circular</span>' : ''}
                </div>
                ${hasSub ? buildRelBranch(relName, depth + 1, newVis) : ''}
            </div>
        </div>`;
    });

    html += '</div>';
    return html;
}

function renderModelTree() {
    const search    = (document.getElementById('map-search')?.value || '').toLowerCase();
    const allModels = APP.models || [];
    const models    = allModels.filter(m => !search || m.name.toLowerCase().includes(search));
    const container = document.getElementById('map-tree-content');

    if (!models.length) {
        container.innerHTML = '<p style="color:var(--text-faint);font-size:13px;padding:20px 0;">No models match your search.</p>';
        return;
    }

    // Sort: models with rels first, then alphabetically
    const sorted = [...models].sort((a, b) => {
        const da = (b.relationships||[]).length - (a.relationships||[]).length;
        return da !== 0 ? da : a.name.localeCompare(b.name);
    });

    const PALETTE = ['#FF2D20','#A78BFA','#34D399','#FBBF24','#F87171','#60A5FA'];

    let html = `<div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;overflow:hidden;">`;

    sorted.forEach((model, idx) => {
        const rels    = model.relationships || [];
        const total   = rels.length;
        const color   = PALETTE[idx % PALETTE.length];
        const domId   = 'tv-body-' + idx;
        const isLast  = idx === sorted.length - 1;
        const hasRels = total > 0;

        html += `
        <div class="model-tree-card" data-name="${model.name.toLowerCase()}"
             style="border-bottom:${isLast ? 'none' : '1px solid var(--border)'};">

            <div onclick="tvToggle('${domId}','${domId}-arrow',${hasRels})"
                 style="display:flex;align-items:center;gap:12px;padding:12px 18px;cursor:${hasRels ? 'pointer' : 'default'};transition:background .18s;user-select:none;"
                 onmouseenter="if(${hasRels})this.style.background='rgba(255,255,255,.03)'" onmouseleave="this.style.background=''">

                <svg id="${domId}-arrow" viewBox="0 0 10 10" style="width:10px;height:10px;flex:none;transition:transform .2s;opacity:${hasRels ? '1' : '.2'};" fill="currentColor" color="var(--text-faint)">
                    <path d="M3 2l4 3-4 3z"/>
                </svg>

                <div style="width:32px;height:32px;border-radius:9px;flex:none;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;background:${color}18;color:${color};border:1px solid ${color}35;">
                    ${model.name[0]}
                </div>

                <span style="font-size:13.5px;font-weight:700;color:var(--text);flex:1;">${model.name}</span>
                <code style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);margin-right:4px;">${model.table || ''}</code>

                ${model.observer ? '<span style="font-size:9px;padding:1px 5px;border-radius:4px;background:rgba(251,191,36,.12);color:var(--amber);border:1px solid rgba(251,191,36,.2);font-family:var(--font-mono);flex:none;">obs</span>' : ''}

                ${hasRels
                    ? `<span style="font-family:var(--font-mono);font-size:10px;padding:2px 8px;border-radius:10px;background:${color}14;color:${color};border:1px solid ${color}30;flex:none;margin-left:6px;">${total} rel${total !== 1 ? 's' : ''}</span>`
                    : `<span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);opacity:.45;margin-left:6px;">no rels</span>`
                }
            </div>

            <div id="${domId}" style="display:none;padding:0 18px 14px 56px;">
                ${hasRels ? buildRelBranch(model.name, 0, new Set()) : ''}
            </div>
        </div>`;
    });

    html += '</div>';
    container.innerHTML = html;
}

function tvToggle(bodyId, arrowId, hasRels) {
    if (!hasRels) return;
    const body  = document.getElementById(bodyId);
    const arrow = document.getElementById(arrowId);
    if (!body) return;
    const open = body.style.display !== 'none';
    body.style.display  = open ? 'none' : 'block';
    arrow.style.transform = open ? '' : 'rotate(90deg)';
}

function filterModelTree() {
    if (!mapTreeRendered) return;
    renderModelTree();
}

// ── Boot ──────────────────────────────────────────────────────────────────────

// ── Architecture Explorer ────────────────────────────────────────────────────
const OV_VIOLET = [255,45,32], OV_CYAN_RGB = [255,83,73];
function _lerpColor(a, b, t) {
    return `rgb(${Math.round(a[0]+(b[0]-a[0])*t)},${Math.round(a[1]+(b[1]-a[1])*t)},${Math.round(a[2]+(b[2]-a[2])*t)})`;
}
function _svgEl(tag, attrs) {
    const el = document.createElementNS('http://www.w3.org/2000/svg', tag);
    Object.entries(attrs || {}).forEach(([k, v]) => el.setAttribute(k, v));
    return el;
}

let _ovArchScale = 1;

function _buildOvArchDiagram() {
    const host = document.getElementById('ovArchDiagram');
    if (!host) return;

    const MAX_NODES = 8;
    const BOX_W = 180, BOX_H = 52;

    const allCtrls    = APP.controllers || [];
    const allModels   = APP.models || [];
    const allServices = APP.services || [];
    const allRepos    = APP.repositories || [];
    const routes      = APP.routes || [];
    const depEdges    = (APP.dependencies?.edges || []);
    const depNodes    = (APP.dependencies?.nodes || []);

    // Build node→layer lookup from dep graph metadata
    const nodeLayerMap = {};
    depNodes.forEach(n => { nodeLayerMap[n.name] = n.layer; });

    // ── Controllers ──
    const seenCtrl = new Set();
    const sortedCtrls = [...allCtrls]
        .sort((a, b) => (b.method_count || 0) - (a.method_count || 0))
        .filter(c => { if (seenCtrl.has(c.name)) return false; seenCtrl.add(c.name); return true; });
    const visibleCtrls = sortedCtrls.slice(0, MAX_NODES);
    const extraCtrlCnt = sortedCtrls.length - visibleCtrls.length;
    const ctrlNodes = visibleCtrls.map((c, i) => ({
        id: 'c-' + i, label: c.name.replace(/Controller$/, ''), sub: (c.method_count || 0) + ' methods',
        rawName: c.name, isMore: false,
    }));
    if (extraCtrlCnt > 0) ctrlNodes.push({ id:'c-more', label:`+ ${extraCtrlCnt} more`, sub:'controllers', rawName:null, isMore:true });
    const ctrlNameToId = {};
    ctrlNodes.forEach(n => { if (n.rawName) ctrlNameToId[n.rawName] = n.id; });

    // ── Services (separate layer) ──
    const visibleSvcs = allServices.slice(0, MAX_NODES);
    const extraSvcCnt = allServices.length - visibleSvcs.length;
    const svcOnlyNodes = visibleSvcs.map((s, i) => ({
        id: 'sv-' + i, label: s.name, sub: 'service', rawName: s.name, isMore: false,
    }));
    if (extraSvcCnt > 0) svcOnlyNodes.push({ id:'sv-more', label:`+ ${extraSvcCnt} more`, sub:'services', rawName:null, isMore:true });
    const svcNameToId = {};
    svcOnlyNodes.forEach(n => { if (n.rawName) svcNameToId[n.rawName] = n.id; });
    const hasServices = svcOnlyNodes.length > 0;

    // ── Repositories (separate layer) ──
    const visibleRepos = allRepos.slice(0, MAX_NODES);
    const extraRepoCnt = allRepos.length - visibleRepos.length;
    const repoOnlyNodes = visibleRepos.map((r, i) => ({
        id: 'rp-' + i, label: r.name, sub: 'repository', rawName: r.name, isMore: false,
    }));
    if (extraRepoCnt > 0) repoOnlyNodes.push({ id:'rp-more', label:`+ ${extraRepoCnt} more`, sub:'repositories', rawName:null, isMore:true });
    const repoNameToId = {};
    repoOnlyNodes.forEach(n => { if (n.rawName) repoNameToId[n.rawName] = n.id; });
    const hasRepos = repoOnlyNodes.length > 0;

    // ── Categorise dep edges by target layer ──
    const ctrlSvcEdgeList  = []; // [ctrlId, svcId]
    const ctrlRepoEdgeList = []; // [ctrlId, repoId]
    const ctrlModelEdgeList = []; // [ctrlId, modelName]
    const referencedModelNames = new Set();
    depEdges.forEach(e => {
        const cId = ctrlNameToId[e.from];
        if (!cId || !e.to) return;
        const layer = nodeLayerMap[e.to] || '';
        if (layer === 'service') {
            const sId = svcNameToId[e.to]; if (sId) ctrlSvcEdgeList.push([cId, sId]);
        } else if (layer === 'repository') {
            const rId = repoNameToId[e.to]; if (rId) ctrlRepoEdgeList.push([cId, rId]);
        } else {
            referencedModelNames.add(e.to);
            ctrlModelEdgeList.push([cId, e.to]);
        }
    });

    // ── Models ──
    const allModelsSorted = [
        ...allModels.filter(m => referencedModelNames.has(m.name)),
        ...allModels.filter(m => !referencedModelNames.has(m.name)),
    ].slice(0, MAX_NODES);
    const extraModelCnt = allModels.length - allModelsSorted.length;
    const modelNodes = allModelsSorted.map((m, i) => ({
        id: 'm-' + i, label: m.name, sub: m.table || 'model', rawName: m.name, isMore: false,
    }));
    if (extraModelCnt > 0) modelNodes.push({ id:'m-more', label:`+ ${extraModelCnt} more`, sub:'models', rawName:null, isMore:true });
    const modelNameToId = {};
    modelNodes.forEach(n => { if (n.rawName) modelNameToId[n.rawName] = n.id; });

    // ── Route nodes ──
    const webCnt   = routes.filter(r => (r.middleware||[]).includes('web')).length;
    const apiCnt   = routes.filter(r => (r.middleware||[]).includes('api')).length;
    const otherCnt = routes.length - webCnt - apiCnt;
    const routeNodes = [];
    if (webCnt > 0)   routeNodes.push({ id:'r-web',   label:'web.php',   sub: webCnt   + ' routes' });
    if (apiCnt > 0)   routeNodes.push({ id:'r-api',   label:'api.php',   sub: apiCnt   + ' routes' });
    if (otherCnt > 0) routeNodes.push({ id:'r-other', label:'routes',    sub: otherCnt + ' routes' });
    if (!routeNodes.length) routeNodes.push({ id:'r-all', label:'Routes', sub: routes.length + ' total' });

    // ── Build layer stack ──
    const LAYERS = [
        { name:'Application',  nodes: [{ id:'app', label:(APP.project?.name)||'Laravel App', sub:'HTTP Kernel', isMore:false }] },
        { name:'Routes',       nodes: routeNodes },
        { name:'Controllers',  nodes: ctrlNodes.length ? ctrlNodes : [{ id:'c-all', label:'Controllers', sub: allCtrls.length + ' total', isMore:true }] },
    ];
    if (hasServices)  LAYERS.push({ name:'Services',     nodes: svcOnlyNodes });
    if (hasRepos)     LAYERS.push({ name:'Repositories', nodes: repoOnlyNodes });
    LAYERS.push({ name:'Models',   nodes: modelNodes.length ? modelNodes : [{ id:'m-all', label:'Models', sub: allModels.length + ' total', isMore:true }] });
    LAYERS.push({ name:'Database', nodes: [{ id:'db', label:'Database', sub: allModels.length + ' model(s)', isMore:false }] });

    // ── Edges ──
    const EDGES = [];
    const realCtrlNodes  = LAYERS[2].nodes.filter(n => !n.isMore);
    const modelsLayerIdx = LAYERS.findIndex(l => l.name === 'Models');
    const realModelNodes = LAYERS[modelsLayerIdx].nodes.filter(n => !n.isMore);
    const realSvcNodes   = svcOnlyNodes.filter(n => !n.isMore);
    const realRepoNodes  = repoOnlyNodes.filter(n => !n.isMore);

    // App → Routes
    LAYERS[1].nodes.forEach(r => EDGES.push(['app', r.id]));

    // Routes → Controllers (representative: up to 3)
    if (realCtrlNodes.length) {
        LAYERS[1].nodes.forEach(r => {
            const picks = realCtrlNodes.length <= 3
                ? realCtrlNodes
                : [realCtrlNodes[0], realCtrlNodes[Math.floor(realCtrlNodes.length / 2)], realCtrlNodes[realCtrlNodes.length - 1]];
            picks.forEach(c => EDGES.push([r.id, c.id]));
        });
    }

    // Track which controllers already have a forward edge from real dep data
    const connectedCtrls = new Set();

    // Controllers → Services (real dep edges)
    ctrlSvcEdgeList.forEach(([cId, sId]) => { EDGES.push([cId, sId]); connectedCtrls.add(cId); });

    // Controllers → Repositories (real dep edges, direct)
    ctrlRepoEdgeList.forEach(([cId, rId]) => { EDGES.push([cId, rId]); connectedCtrls.add(cId); });

    // Any controller with no real forward edge gets a representative connection
    const unconnectedCtrls = realCtrlNodes.filter(c => !connectedCtrls.has(c.id));
    if (unconnectedCtrls.length > 0) {
        if (hasServices && realSvcNodes.length) {
            unconnectedCtrls.forEach((c, i) => EDGES.push([c.id, realSvcNodes[i % realSvcNodes.length].id]));
        } else if (hasRepos && realRepoNodes.length) {
            unconnectedCtrls.forEach((c, i) => EDGES.push([c.id, realRepoNodes[i % realRepoNodes.length].id]));
        } else if (realModelNodes.length) {
            unconnectedCtrls.forEach((c, i) => EDGES.push([c.id, realModelNodes[i % realModelNodes.length].id]));
        }
    }

    // Services → Repositories (always connect when both layers exist)
    if (hasServices && hasRepos && realSvcNodes.length && realRepoNodes.length) {
        realSvcNodes.forEach((sv, i) => EDGES.push([sv.id, realRepoNodes[i % realRepoNodes.length].id]));
    }

    // Repositories → Models (every visible repo gets at least one edge)
    if (hasRepos && realRepoNodes.length && realModelNodes.length) {
        realRepoNodes.forEach((rp, i) => EDGES.push([rp.id, realModelNodes[i % realModelNodes.length].id]));
    }

    // Services → Models (only when no repo layer sits between them)
    if (hasServices && !hasRepos && realSvcNodes.length && realModelNodes.length) {
        realSvcNodes.forEach((sv, i) => EDGES.push([sv.id, realModelNodes[i % realModelNodes.length].id]));
    }

    // Controllers → Models (only when no service or repo layers exist at all)
    if (!hasServices && !hasRepos) {
        if (ctrlModelEdgeList.length > 0) {
            ctrlModelEdgeList.forEach(([cId, mName]) => {
                const mId = modelNameToId[mName]; if (mId) EDGES.push([cId, mId]);
            });
        } else if (realCtrlNodes.length && realModelNodes.length) {
            realCtrlNodes.forEach((c, i) => EDGES.push([c.id, realModelNodes[i % realModelNodes.length].id]));
        }
    }

    // Models → Database (every visible model connects)
    (realModelNodes.length ? realModelNodes : LAYERS[modelsLayerIdx].nodes).forEach(m => EDGES.push([m.id, 'db']));

    // ── Layout ──
    const n = LAYERS.length;
    const maxNodes = Math.max(...LAYERS.map(l => l.nodes.length));
    const NODE_SPACING = 64;
    const BAND_TOP = 60;
    const BAND_BTM = Math.max(420, BAND_TOP + (maxNodes - 1) * NODE_SPACING);
    const VB_W = Math.max(1240, n * 240 + 100), VB_H = BAND_BTM + 70;
    const colX = LAYERS.map((_, i) => 100 + i * ((VB_W - 220) / (n - 1)));
    const positions = {};

    LAYERS.forEach((layer, li) => {
        const cnt = layer.nodes.length;
        layer.nodes.forEach((node, ni) => {
            const y = cnt === 1
                ? (BAND_TOP + BAND_BTM) / 2
                : BAND_TOP + ni * ((BAND_BTM - BAND_TOP) / (cnt - 1));
            positions[node.id] = { x: colX[li], y, layer: li, label: node.label, sub: node.sub, layerName: layer.name, isMore: !!node.isMore };
        });
    });

    // ── SVG ──
    const svg = _svgEl('svg', { viewBox:`0 0 ${VB_W} ${VB_H}`, width:VB_W, height:VB_H, style:'display:block;overflow:visible;' });

    // Drop-shadow filter for node boxes
    const ovDefs = _svgEl('defs');
    const ovFilter = _svgEl('filter', { id:'ov-shadow', x:'-20%', y:'-30%', width:'140%', height:'160%' });
    const ovFds = _svgEl('feDropShadow', { dx:'0', dy:'2', stdDeviation:'4' });
    ovFds.setAttribute('flood-color', 'rgba(23,43,77,0.10)');
    ovFds.setAttribute('flood-opacity', '1');
    ovFilter.appendChild(ovFds);
    ovDefs.appendChild(ovFilter);
    svg.appendChild(ovDefs);

    // Layer header labels
    LAYERS.forEach((layer, li) => {
        const t = li / (n - 1);
        const color = _lerpColor(OV_VIOLET, OV_CYAN_RGB, t);
        const tx = _svgEl('text', { x:colX[li], y:22, 'text-anchor':'middle', 'font-family':'Inter,sans-serif', 'font-size':10, 'font-weight':700, 'letter-spacing':'0.08em', fill:color });
        tx.textContent = layer.name.toUpperCase();
        svg.appendChild(tx);
        // Subtle column separator line
        if (li > 0) {
            const sep = _svgEl('line', { x1: colX[li] - (colX[1]-colX[0])/2, y1: 30, x2: colX[li] - (colX[1]-colX[0])/2, y2: VB_H - 20, stroke:'rgba(23,43,77,0.08)', 'stroke-width':'1' });
            svg.appendChild(sep);
        }
    });

    // Edges with traveling dots
    const edgeGroup = _svgEl('g');
    const edgeEls = [];
    EDGES.forEach(([from, to], i) => {
        const a = positions[from], b = positions[to]; if (!a || !b) return;
        const x1 = a.x + BOX_W/2, y1 = a.y, x2 = b.x - BOX_W/2, y2 = b.y, midX = (x1+x2)/2;
        const d = `M ${x1} ${y1} C ${midX} ${y1}, ${midX} ${y2}, ${x2} ${y2}`;
        const t = (a.layer + b.layer) / (2*(n-1));
        const color = _lerpColor(OV_VIOLET, OV_CYAN_RGB, t);
        const path = _svgEl('path', { d, id:`ov-edge-${i}`, fill:'none', stroke:color, 'stroke-width':'1.5' });
        path.dataset.from = from; path.dataset.to = to;
        path.style.opacity = '0.3';
        edgeGroup.appendChild(path);
        edgeEls.push(path);
        const dot = _svgEl('circle', { r:'2.5', fill:color });
        const anim = _svgEl('animateMotion', { dur:'3s', repeatCount:'indefinite', begin:`${(i*0.3).toFixed(2)}s` });
        const mp = _svgEl('mpath'); mp.setAttributeNS('http://www.w3.org/1999/xlink','href',`#ov-edge-${i}`);
        anim.appendChild(mp); dot.appendChild(anim);
        const opAnim = _svgEl('animate', { attributeName:'opacity', values:'0;1;1;0', keyTimes:'0;0.08;0.9;1', dur:'3s', repeatCount:'indefinite', begin:`${(i*0.3).toFixed(2)}s` });
        dot.appendChild(opAnim); edgeGroup.appendChild(dot);
    });
    svg.appendChild(edgeGroup);

    // Node boxes
    const nodeGroup = _svgEl('g');
    const nodeEls = [];
    Object.entries(positions).forEach(([id, pos]) => {
        const t = pos.layer / (n-1);
        const color = _lerpColor(OV_VIOLET, OV_CYAN_RGB, t);
        const g = _svgEl('g', { class:'ov-arch-node', role:'button', 'aria-label':pos.label });
        g.dataset.id = id;
        const x = pos.x - BOX_W/2, y = pos.y - BOX_H/2;

        // "more" nodes get a dashed, dimmer style
        const rectFill   = pos.isMore ? 'rgba(255,45,32,0.04)' : '#FFFFFF';
        const rectStroke = pos.isMore ? `rgba(255,45,32,0.25)` : color;
        const rectDash   = pos.isMore ? '4,3' : 'none';
        const rect = _svgEl('rect', { x, y, width:BOX_W, height:BOX_H, rx:'10', fill:rectFill, stroke:rectStroke, 'stroke-width':'1.5', 'stroke-dasharray':rectDash, filter:'url(#ov-shadow)' });
        rect.dataset.origStroke = rectStroke;

        const dotR = pos.isMore ? '2.5' : '4';
        const dotEl = _svgEl('circle', { cx:x+16, cy:y+BOX_H/2, r:dotR, fill: pos.isMore ? 'rgba(255,45,32,0.35)' : color });

        // Label: max 22 chars (wider box allows more)
        const lbl = _svgEl('text', { x:x+30, y:y+20, 'font-family':'Inter,sans-serif', 'font-size':'11', 'font-weight': pos.isMore ? '500' : '600', fill: pos.isMore ? '#6B778C' : '#172B4D' });
        lbl.textContent = pos.label.length > 22 ? pos.label.slice(0, 22) + '…' : pos.label;

        const sub = _svgEl('text', { x:x+30, y:y+36, 'font-family':'JetBrains Mono,monospace', 'font-size':'9.5', fill:'#6B778C' });
        sub.textContent = pos.sub || '';

        g.appendChild(rect); g.appendChild(dotEl); g.appendChild(lbl); g.appendChild(sub);
        nodeGroup.appendChild(g); nodeEls.push(g);
    });
    svg.appendChild(nodeGroup);
    host.innerHTML = ''; host.appendChild(svg);

    // Hover + click-to-pin path highlight
    const detail = document.getElementById('ovArchDetail');
    const defaultDetail = detail ? detail.innerHTML : '';
    const neighbors = {};
    EDGES.forEach(([f, t]) => { (neighbors[f]=neighbors[f]||new Set()).add(t); (neighbors[t]=neighbors[t]||new Set()).add(f); });

    let _ovPinnedId = null;

    function ovGetFlowPath(id) {
        // Backward only: find upstream ancestors (never follow forward from ancestors)
        const ancestors = new Set([id]);
        const bwdQ = [id];
        while (bwdQ.length) {
            const curr = bwdQ.shift();
            EDGES.forEach(([f, t]) => {
                if (t === curr && !ancestors.has(f)) { ancestors.add(f); bwdQ.push(f); }
            });
        }
        // Forward only: find downstream descendants (never follow backward from descendants)
        const descendants = new Set([id]);
        const fwdQ = [id];
        while (fwdQ.length) {
            const curr = fwdQ.shift();
            EDGES.forEach(([f, t]) => {
                if (f === curr && !descendants.has(t)) { descendants.add(t); fwdQ.push(t); }
            });
        }
        return new Set([...ancestors, ...descendants]);
    }

    function ovHighlight(id) {
        const rel = neighbors[id] || new Set();
        edgeEls.forEach(p => { const c = p.dataset.from===id||p.dataset.to===id; p.style.opacity=c?'0.9':'0.05'; p.style.strokeWidth=c?'2.2':'1.5'; });
        nodeEls.forEach(nd => { nd.style.opacity=(nd.dataset.id===id||rel.has(nd.dataset.id))?'1':'0.25'; });
        if (detail) { const pos=positions[id]; detail.textContent = `${pos.label} · ${pos.sub||pos.layerName} — ${rel.size} connection${rel.size===1?'':'s'}`; }
    }

    function ovHighlightPath(id) {
        const pathNodes = ovGetFlowPath(id);
        edgeEls.forEach(p => {
            const inPath = pathNodes.has(p.dataset.from) && pathNodes.has(p.dataset.to);
            p.style.opacity = inPath ? '1' : '0.04';
            p.style.strokeWidth = inPath ? '2.5' : '1.5';
        });
        nodeEls.forEach(nd => {
            const inPath = pathNodes.has(nd.dataset.id);
            nd.style.opacity = inPath ? '1' : '0.12';
            const rect = nd.querySelector('rect');
            if (rect) {
                rect.setAttribute('stroke-width', nd.dataset.id === id ? '2.5' : '1.5');
                if (nd.dataset.id === id) rect.setAttribute('stroke', '#FF2D20');
            }
        });
        if (detail) {
            const pos = positions[id];
            detail.innerHTML = `<span style="color:#FF2D20;font-weight:700;">${pos.label}</span> &mdash; flow pinned &middot; click again to clear`;
        }
    }

    function ovReset() {
        edgeEls.forEach(p => { p.style.opacity='0.3'; p.style.strokeWidth='1.5'; });
        nodeEls.forEach(nd => {
            nd.style.opacity = '1';
            const rect = nd.querySelector('rect');
            if (rect) { rect.setAttribute('stroke-width','1.5'); if (rect.dataset.origStroke) rect.setAttribute('stroke', rect.dataset.origStroke); }
        });
        if (detail) detail.innerHTML = defaultDetail;
    }

    nodeEls.forEach(nd => {
        nd.style.cursor = 'pointer';
        nd.addEventListener('mouseenter', () => { if (!_ovPinnedId) ovHighlight(nd.dataset.id); });
        nd.addEventListener('mouseleave', () => { if (!_ovPinnedId) ovReset(); });
        nd.addEventListener('click', () => {
            const id = nd.dataset.id;
            if (_ovPinnedId === id) { _ovPinnedId = null; ovReset(); }
            else { _ovPinnedId = id; ovHighlightPath(id); }
        });
    });

    // Click on SVG background clears pin
    svg.addEventListener('click', e => {
        if (e.target === svg || e.target.tagName === 'svg') { _ovPinnedId = null; ovReset(); }
    });

    // Zoom controls
    const zoomIn  = document.getElementById('ovZoomIn');
    const zoomOut = document.getElementById('ovZoomOut');
    function _applyOvZoom() { svg.setAttribute('width', VB_W*_ovArchScale); svg.setAttribute('height', VB_H*_ovArchScale); }
    if (zoomIn)  zoomIn.addEventListener('click',  () => { _ovArchScale = Math.min(1.7, _ovArchScale+0.15); _applyOvZoom(); });
    if (zoomOut) zoomOut.addEventListener('click', () => { _ovArchScale = Math.max(0.6, _ovArchScale-0.15); _applyOvZoom(); });

    // Fullscreen toggle
    const fsBtn        = document.getElementById('ovFullscreen');
    const fsPanel      = document.getElementById('ovArchPanel');
    const fsIconExp    = document.getElementById('ovFsIconExpand');
    const fsIconCompr  = document.getElementById('ovFsIconCompress');
    function _setFsIcon(isFs) {
        if (fsIconExp)   fsIconExp.style.display   = isFs ? 'none'  : '';
        if (fsIconCompr) fsIconCompr.style.display = isFs ? ''      : 'none';
    }
    if (fsBtn && fsPanel) {
        fsBtn.addEventListener('click', () => {
            if (!document.fullscreenElement && !document.webkitFullscreenElement) {
                (fsPanel.requestFullscreen || fsPanel.webkitRequestFullscreen).call(fsPanel);
            } else {
                (document.exitFullscreen || document.webkitExitFullscreen).call(document);
            }
        });
        document.addEventListener('fullscreenchange',       () => _setFsIcon(!!document.fullscreenElement));
        document.addEventListener('webkitfullscreenchange', () => _setFsIcon(!!document.webkitFullscreenElement));
    }
}

// Reveal animation with count-up and bar wipe
function _countUp(el, target, duration) {
    const start = performance.now();
    (function step(now) {
        const t = Math.min(1, (now - start) / duration);
        const eased = 1 - (1 - t) * (1 - t);
        el.textContent = Math.round(eased * target);
        if (t < 1) requestAnimationFrame(step);
    })(performance.now());
}

(function() {
    function _animateBars(root) {
        root.querySelectorAll('.atlas-score-fill[data-score-w]').forEach(b => {
            b.style.transition = 'width 0.85s var(--ease)';
            requestAnimationFrame(() => { b.style.width = b.dataset.scoreW + '%'; });
        });
    }

    if (!window.IntersectionObserver) {
        document.querySelectorAll('[data-ov-reveal]').forEach(el => {
            el.classList.add('ov-in');
            _animateBars(el);
            el.querySelectorAll('.kpi-card__num[data-count]').forEach(n => {
                _countUp(n, +n.dataset.count, 900);
            });
        });
    } else {
        const io = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (!e.isIntersecting) return;
                e.target.classList.add('ov-in');
                io.unobserve(e.target);
                e.target.querySelectorAll('.kpi-card__num[data-count]').forEach(n => {
                    const target = +n.dataset.count;
                    n.textContent = '0';
                    _countUp(n, target, 900);
                });
                _animateBars(e.target);
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('[data-ov-reveal]').forEach(el => io.observe(el));

        // Without the hero section above them, overview cards are immediately in
        // viewport on load. IO fires async and can miss them — force-reveal once.
        requestAnimationFrame(() => {
            document.querySelectorAll('[data-ov-reveal]').forEach(el => {
                if (el.classList.contains('ov-in')) return;
                el.classList.add('ov-in');
                el.querySelectorAll('.kpi-card__num[data-count]').forEach(n => {
                    _countUp(n, +n.dataset.count, 900);
                });
                _animateBars(el);
            });
        });
    }

    // Sidebar score bar — always visible, animate shortly after load
    const sideBar = document.getElementById('sidebar-score-bar');
    if (sideBar) {
        setTimeout(() => {
            sideBar.style.transition = 'width 1s var(--ease)';
            sideBar.style.width = sideBar.dataset.scoreW + '%';
        }, 500);
    }
})();

_buildOvArchDiagram();

// ── Dependency Graph (custom layered SVG) ────────────────────────────────────

const _DEP_NW  = 114;   // node width
const _DEP_NH  = 32;    // node height
const _DEP_HG  = 10;    // horizontal gap between nodes
const _DEP_MR  = 9;     // max nodes per row within a layer
const _DEP_RG  = 14;    // gap between rows within a layer
const _DEP_LG  = 80;    // gap between layers

const _DEP_CFG = {
    // ATLAS dark theme: dark bg, bright stroke, label is the border/text color
    controller: { label:'Controllers', color:'#FF2D20', bg:'#FFF1F0', order:0 },
    job:        { label:'Jobs',        color:'#FF5630', bg:'#FFF4E5', order:1 },
    event:      { label:'Events',      color:'#BF40BF', bg:'#FFF0FB', order:1 },
    listener:   { label:'Listeners',   color:'#DA62AC', bg:'#FEE4FA', order:2 },
    service:    { label:'Services',    color:'#00875A', bg:'#E3FCEF', order:2 },
    repository: { label:'Repositories',color:'#FF8B00', bg:'#FFFAE6', order:3 },
    model:      { label:'Models',      color:'#6554C0', bg:'#F3F0FF', order:4 },
    database:   { label:'Database',    color:'#6B778C', bg:'#F4F5F7', order:5 },
};

let _depT  = { tx:0, ty:0, s:1 };
let _depDrag = null;
let _depPos  = {};
let _depSel  = null;
const NS = 'http://www.w3.org/2000/svg';

function initDepGraph() {
    const nodes = (APP.dependencies || {}).nodes || [];
    const edges = (APP.dependencies || {}).edges || [];
    if (!nodes.length) return;

    const canvas  = document.getElementById('dep-canvas');
    const bandsG  = document.getElementById('dep-bands-g');
    const edgesG  = document.getElementById('dep-edges-g');
    const nodesG  = document.getElementById('dep-nodes-g');
    if (!canvas) return;

    // Group nodes by layer order
    const byOrder = {};
    nodes.forEach(n => {
        const cfg = _DEP_CFG[n.layer] || { order: 4 };
        (byOrder[cfg.order] = byOrder[cfg.order] || []).push(n);
    });

    // Build positions — layered layout
    let curY = 30;
    const layerBands = []; // { y1, y2, order }

    Object.keys(byOrder).sort((a,b)=>+a-+b).forEach(order => {
        const layerNodes = byOrder[order];
        // Split into rows of _DEP_MR
        const rows = [];
        for (let i = 0; i < layerNodes.length; i += _DEP_MR) {
            rows.push(layerNodes.slice(i, i + _DEP_MR));
        }
        const maxCols = Math.max(...rows.map(r => r.length));
        const bandY1 = curY;

        rows.forEach((row, ri) => {
            const rowW   = row.length * (_DEP_NW + _DEP_HG) - _DEP_HG;
            const maxW   = maxCols * (_DEP_NW + _DEP_HG) - _DEP_HG;
            const startX = -maxW / 2 + (maxW - rowW) / 2;   // center this row

            row.forEach((n, ci) => {
                _depPos[n.name] = {
                    x: startX + ci * (_DEP_NW + _DEP_HG),
                    y: curY,
                    layer: n.layer,
                };
            });
            curY += _DEP_NH + (ri < rows.length - 1 ? _DEP_RG : 0);
        });

        layerBands.push({ y1: bandY1, y2: curY, order: +order });
        curY += _DEP_LG;
    });

    // Draw faint layer bands (background stripes)
    const allX = Object.values(_depPos).map(p => p.x);
    const bandMinX = Math.min(...allX) - 20;
    const bandMaxX = Math.max(...allX) + _DEP_NW + 20;

    layerBands.forEach(band => {
        // Find one node in this order to get layer name
        const repNode = byOrder[band.order]?.[0];
        if (!repNode) return;
        const cfg = _DEP_CFG[repNode.layer] || {};
        const rect = document.createElementNS(NS, 'rect');
        rect.setAttribute('x', bandMinX);
        rect.setAttribute('y', band.y1 - 8);
        rect.setAttribute('width', bandMaxX - bandMinX);
        rect.setAttribute('height', band.y2 - band.y1 + 16);
        rect.setAttribute('rx', '10');
        rect.setAttribute('fill', cfg.color || '#6B778C');
        rect.setAttribute('opacity', '0.08');
        bandsG.appendChild(rect);

        // Layer label on left
        const lbl = document.createElementNS(NS, 'text');
        lbl.setAttribute('x', bandMinX + 6);
        lbl.setAttribute('y', band.y1 + (band.y2 - band.y1) / 2 + 4);
        lbl.setAttribute('font-size', '10');
        lbl.setAttribute('font-family', 'system-ui,sans-serif');
        lbl.setAttribute('fill', cfg.color || '#64748b');
        lbl.setAttribute('font-weight', '600');
        lbl.setAttribute('opacity', '0.7');
        lbl.textContent = cfg.label || '';
        bandsG.appendChild(lbl);
    });

    // Draw edges (bezier curves)
    edges.forEach(e => {
        const fp = _depPos[e.from];
        const tp = _depPos[e.to];
        if (!fp || !tp) return;

        const x1 = fp.x + _DEP_NW / 2;
        const y1 = fp.y + _DEP_NH;
        const x2 = tp.x + _DEP_NW / 2;
        const y2 = tp.y;
        const cy = (y1 + y2) / 2;

        const path = document.createElementNS(NS, 'path');
        path.setAttribute('d', `M${x1},${y1} C${x1},${cy} ${x2},${cy} ${x2},${y2}`);
        path.setAttribute('fill', 'none');
        path.setAttribute('stroke', 'rgba(148,178,222,0.4)');
        path.setAttribute('stroke-width', '1.5');
        path.setAttribute('marker-end', 'url(#dep-arr)');
        path.setAttribute('opacity', '1');
        path.dataset.from = e.from;
        path.dataset.to   = e.to;
        edgesG.appendChild(path);
    });

    // Draw nodes
    nodes.forEach(n => {
        const pos = _depPos[n.name];
        if (!pos) return;
        const cfg = _DEP_CFG[n.layer] || { color:'#6B778C', bg:'#F4F5F7' };

        const g = document.createElementNS(NS, 'g');
        g.style.cursor = 'pointer';
        g.dataset.name = n.name;

        const rect = document.createElementNS(NS, 'rect');
        rect.setAttribute('x', pos.x);
        rect.setAttribute('y', pos.y);
        rect.setAttribute('width', _DEP_NW);
        rect.setAttribute('height', _DEP_NH);
        rect.setAttribute('rx', '7');
        rect.setAttribute('fill', '#FFFFFF');
        rect.setAttribute('stroke', cfg.color);
        rect.setAttribute('stroke-width', '1.5');
        rect.setAttribute('filter', 'url(#dep-shadow)');

        // Truncate display name: strip suffix, add ellipsis
        const suffixes = /Controller$|Service$|Repository$|Observer$|Policy$|Listener$/;
        const short = n.name.replace(suffixes, '') || n.name;
        const display = short.length > 13 ? short.substring(0, 12) + '…' : short;

        const text = document.createElementNS(NS, 'text');
        text.setAttribute('x', pos.x + _DEP_NW / 2);
        text.setAttribute('y', pos.y + _DEP_NH / 2 + 4);
        text.setAttribute('text-anchor', 'middle');
        text.setAttribute('font-size', '10.5');
        text.setAttribute('font-family', 'system-ui,sans-serif');
        text.setAttribute('font-weight', '600');
        text.setAttribute('fill', '#172B4D');
        text.textContent = display;

        const title = document.createElementNS(NS, 'title');
        title.textContent = n.name;

        g.appendChild(rect); g.appendChild(text); g.appendChild(title);

        g.addEventListener('click',       () => depNodeClick(n.name));
        g.addEventListener('mouseenter',  () => depHighlight(n.name));
        g.addEventListener('mouseleave',  () => { if (_depSel !== n.name) depClearHighlight(false); });

        nodesG.appendChild(g);
    });

    // Fit on first render
    depFit();

    // Zoom (scroll wheel)
    canvas.addEventListener('wheel', e => {
        e.preventDefault();
        const rect   = canvas.getBoundingClientRect();
        const mx     = e.clientX - rect.left;
        const my     = e.clientY - rect.top;
        const delta  = e.deltaY > 0 ? -0.1 : 0.1;
        const newS   = Math.max(0.12, Math.min(3, _depT.s + delta));
        _depT.tx    += (mx - _depT.tx) * (1 - newS / _depT.s);
        _depT.ty    += (my - _depT.ty) * (1 - newS / _depT.s);
        _depT.s      = newS;
        _depApplyT();
    }, { passive: false });

    // Pan (drag)
    canvas.addEventListener('mousedown', e => {
        if (e.target.closest('g[data-name]')) return;
        _depDrag = { sx: e.clientX - _depT.tx, sy: e.clientY - _depT.ty };
        canvas.style.cursor = 'grabbing';
    });
    window.addEventListener('mousemove', e => {
        if (!_depDrag) return;
        _depT.tx = e.clientX - _depDrag.sx;
        _depT.ty = e.clientY - _depDrag.sy;
        _depApplyT();
    });
    window.addEventListener('mouseup', () => {
        _depDrag = null;
        if (canvas) canvas.style.cursor = 'grab';
    });
    window.addEventListener('resize', depFit, { passive: true });
}

function _depApplyT() {
    const vp = document.getElementById('dep-vp');
    if (vp) vp.setAttribute('transform', `translate(${_depT.tx},${_depT.ty}) scale(${_depT.s})`);
}

function depFit() {
    const canvas = document.getElementById('dep-canvas');
    if (!canvas || !Object.keys(_depPos).length) return;

    const allX = Object.values(_depPos).map(p => p.x);
    const allY = Object.values(_depPos).map(p => p.y);
    const minX = Math.min(...allX), maxX = Math.max(...allX) + _DEP_NW;
    const minY = Math.min(...allY), maxY = Math.max(...allY) + _DEP_NH;
    const gW = maxX - minX, gH = maxY - minY;
    const par = canvas.parentElement;
    const cW = (par && par.clientWidth  > 0 ? par.clientWidth  : null) || canvas.getBoundingClientRect().width  || 900;
    const cH = (par && par.clientHeight > 0 ? par.clientHeight : null) || canvas.getBoundingClientRect().height || 600;
    const pad = 48;

    _depT.s  = Math.min((cW - pad*2) / gW, (cH - pad*2) / gH, 1.4);
    _depT.tx = cW/2 - _depT.s * (minX + gW/2);
    _depT.ty = cH/2 - _depT.s * (minY + gH/2);
    _depApplyT();
}

function depZoom(delta) {
    const canvas = document.getElementById('dep-canvas');
    const par = canvas?.parentElement;
    const cW = (par && par.clientWidth  > 0 ? par.clientWidth  : null) || canvas?.getBoundingClientRect().width  || 900;
    const cH = (par && par.clientHeight > 0 ? par.clientHeight : null) || canvas?.getBoundingClientRect().height || 600;
    const newS = Math.max(0.12, Math.min(3, _depT.s + delta));
    _depT.tx += (cW/2 - _depT.tx) * (1 - newS / _depT.s);
    _depT.ty += (cH/2 - _depT.ty) * (1 - newS / _depT.s);
    _depT.s   = newS;
    _depApplyT();
}

function depNodeClick(name) {
    if (_depSel === name) {
        _depSel = null;
        depClearHighlight();
        const lbl = document.getElementById('dep-sel-label');
        if (lbl) lbl.style.display = 'none';
    } else {
        _depSel = name;
        depHighlight(name);
        const lbl = document.getElementById('dep-sel-label');
        if (lbl) { lbl.textContent = name; lbl.style.display = 'block'; }
    }
}

function depHighlight(name) {
    const edges = (APP.dependencies || {}).edges || [];
    const connected = new Set([name]);
    edges.forEach(e => {
        if (e.from === name) connected.add(e.to);
        if (e.to   === name) connected.add(e.from);
    });

    document.querySelectorAll('#dep-edges-g path').forEach(p => {
        const on = p.dataset.from === name || p.dataset.to === name;
        p.setAttribute('stroke',       on ? '#FF2D20' : 'rgba(148,178,222,0.15)');
        p.setAttribute('stroke-width', on ? '2'       : '1.5');
        p.setAttribute('opacity',      on ? '1'       : '0.5');
        p.setAttribute('marker-end',   on ? 'url(#dep-arr-hi)' : 'url(#dep-arr)');
    });

    document.querySelectorAll('#dep-nodes-g g[data-name]').forEach(g => {
        g.style.opacity = connected.has(g.dataset.name) ? '1' : '0.18';
    });
}

function depClearHighlight(resetSel = true) {
    if (resetSel) _depSel = null;
    document.querySelectorAll('#dep-edges-g path').forEach(p => {
        p.setAttribute('stroke',       'rgba(148,178,222,0.4)');
        p.setAttribute('stroke-width', '1.5');
        p.setAttribute('opacity',      '1');
        p.setAttribute('marker-end',   'url(#dep-arr)');
    });
    document.querySelectorAll('#dep-nodes-g g[data-name]').forEach(g => {
        g.style.opacity = '1';
    });
    const lbl = document.getElementById('dep-sel-label');
    if (lbl && resetSel) lbl.style.display = 'none';
}

// ── AI Chat ───────────────────────────────────────────────────────────────────

const CHAT_ENDPOINT = '{{ route("laradar.ai.chat") }}';
let _chatBusy = false;

function chatSuggest(text) {
    document.getElementById('chat-input').value = text;
    chatPreviewContext(text);
    chatSend();
}

function chatSend() {
    const input = document.getElementById('chat-input');
    const msg   = input.value.trim();
    if (!msg || _chatBusy) return;

    _chatBusy = true;
    input.value = '';
    document.getElementById('chat-context-hint').textContent = '';
    document.getElementById('chat-empty').style.display = 'none';

    // Extract context
    const {data: ctx, labels} = chatExtractContext(msg);

    // Append user bubble
    chatAppendBubble('user', msg);

    // Append loading AI bubble
    const loadingId = 'chat-loading-' + Date.now();
    chatAppendBubble('ai', null, loadingId, labels);

    fetch(CHAT_ENDPOINT, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': AI_CSRF },
        body:    JSON.stringify({ message: msg, context: ctx }),
    })
    .then(r => r.json())
    .then(json => {
        if (json.error) throw new Error(json.error);
        chatReplaceBubble(loadingId, json.reply, labels);
    })
    .catch(err => {
        chatReplaceBubble(loadingId, '**Error:** ' + err.message, labels, true);
    })
    .finally(() => { _chatBusy = false; });
}

function chatPreviewContext(msg) {
    const {labels} = chatExtractContext(msg);
    const hint = document.getElementById('chat-context-hint');
    hint.textContent = labels.length ? 'Context: ' + labels.join(' · ') : '';
}

function chatExtractContext(question) {
    const q      = question.toLowerCase();
    const words  = (q.match(/\b\w{4,}\b/g) || []);
    const labels = [];
    const data   = {};

    data.project = APP.project;
    data.summary = APP.summary;

    // Fat/large controller queries
    if (/large|fat|big|most method|too many|which.*controller|longest/.test(q)) {
        const sorted = [...(APP.controllers || [])].sort((a, b) => (b.method_count || 0) - (a.method_count || 0));
        data.controllers_by_size = sorted.slice(0, 10);
        labels.push('Controllers sorted by size');
    }

    // Controller keyword match
    const ctrlHits = (APP.controllers || []).filter(c => words.some(w => c.name.toLowerCase().includes(w)));
    if (ctrlHits.length) { data.controllers = ctrlHits; labels.push(ctrlHits.map(c => c.name).join(', ')); }

    // Model keyword match
    const modelHits = (APP.models || []).filter(m => words.some(w => m.name.toLowerCase().includes(w)));
    if (modelHits.length) { data.models = modelHits; labels.push(modelHits.map(m => m.name).join(', ')); }

    // Route keyword match
    const routeHits = (APP.routes || []).filter(r =>
        words.some(w => (r.uri || '').toLowerCase().includes(w) || (r.controller?.class || '').toLowerCase().includes(w))
    );
    if (routeHits.length) { data.routes = routeHits.slice(0, 20); labels.push(routeHits.length + ' routes'); }

    // Service keyword match
    const svcHits = (APP.services || []).filter(s => words.some(w => (s.name || '').toLowerCase().includes(w)));
    if (svcHits.length) { data.services = svcHits; labels.push(svcHits.map(s => s.name).join(', ')); }

    // Score / SOLID / quality
    if (/score|solid|grade|quality|best practice|principle/.test(q)) {
        data.score = APP.score; labels.push('Architecture Score');
    }

    // Dependencies / coupling
    if (/depend|inject|coupl|graph|layer/.test(q)) {
        data.dependencies = APP.dependencies; labels.push('Dependency Graph');
    }

    // Modules
    if (/module/.test(q) && (APP.modules || []).length) {
        data.modules = APP.modules; labels.push('Modules');
    }

    // Jobs / Events
    if (/job|queue|dispatch/.test(q) && (APP.jobs || []).length) {
        data.jobs = APP.jobs; labels.push('Jobs');
    }
    if (/event|listener|broadcast/.test(q) && (APP.events || []).length) {
        data.events = APP.events; labels.push('Events');
    }

    // Fallback — send a compact summary
    if (labels.length === 0) {
        data.controllers = (APP.controllers || []).slice(0, 15);
        data.models      = (APP.models || []).slice(0, 15);
        data.score       = APP.score;
        labels.push('Full architecture summary');
    }

    return { data, labels };
}

function chatAppendBubble(role, text, loadingId = null, contextLabels = []) {
    const wrap = document.getElementById('chat-messages');
    const isAI = role === 'ai';
    const id   = loadingId || ('msg-' + Date.now());

    const ctxHtml = contextLabels.length && isAI
        ? `<p style="font-size:11px;color:#94a3b8;margin-top:8px;padding-top:8px;border-top:1px solid #f1f5f9;">Context used: ${contextLabels.map(_esc).join(' · ')}</p>`
        : '';

    const dot = (delay) => `<span style="width:6px;height:6px;border-radius:50%;background:#94a3b8;display:inline-block;animation:chatBounce 1.2s ease-in-out ${delay} infinite;"></span>`;
    const bodyHtml = text === null
        ? `<span style="display:inline-flex;gap:4px;align-items:center;">${dot('0s')}${dot('.15s')}${dot('.3s')}</span>`
        : `<div style="font-size:13px;color:#334155;line-height:1.65;">${chatMarkdown(text)}</div>`;

    const rowStyle    = `display:flex;align-items:flex-end;gap:10px;${isAI ? 'justify-content:flex-start' : 'justify-content:flex-end'}`;
    const aiAvatar    = isAI  ? `<div style="width:28px;height:28px;border-radius:50%;background:#4f46e5;display:flex;align-items:center;justify-content:center;color:#fff;font-size:10px;font-weight:700;flex-shrink:0;margin-bottom:2px;">AI</div>` : '';
    const userAvatar  = !isAI ? `<div style="width:28px;height:28px;border-radius:50%;background:#e2e8f0;display:flex;align-items:center;justify-content:center;color:#475569;font-size:10px;font-weight:700;flex-shrink:0;margin-bottom:2px;">You</div>` : '';
    const bubbleStyle = isAI
        ? 'background:#fff;border:1px solid #e2e8f0;border-radius:16px;border-top-left-radius:4px;padding:12px 16px;max-width:80%;'
        : 'background:#4f46e5;color:#fff;border-radius:16px;border-top-right-radius:4px;padding:12px 16px;max-width:80%;';

    wrap.insertAdjacentHTML('beforeend', `
        <div style="${rowStyle};animation:chatBubbleIn .22s cubic-bezier(.22,1,.36,1) both;" id="${id}">
            ${aiAvatar}
            <div style="${bubbleStyle}" data-bubble="1">
                ${bodyHtml}
                ${ctxHtml}
            </div>
            ${userAvatar}
        </div>
    `);

    wrap.scrollTop = wrap.scrollHeight;
    return id;
}

function chatReplaceBubble(id, text, contextLabels = [], isError = false) {
    const el = document.getElementById(id);
    if (!el) return;
    const inner = el.querySelector('[data-bubble]');
    if (!inner) return;

    const ctxHtml = contextLabels.length
        ? `<p style="font-size:11px;color:#94a3b8;margin-top:8px;padding-top:8px;border-top:1px solid #f1f5f9;">Context used: ${contextLabels.map(_esc).join(' · ')}</p>`
        : '';

    if (isError) {
        inner.innerHTML = `<div style="font-size:13px;color:#dc2626;line-height:1.65;">${chatMarkdown(text)}</div>${ctxHtml}`;
        document.getElementById('chat-messages').scrollTop = 99999;
        return;
    }

    // Typewriter reveal: type plain text first, then swap to full rendered HTML
    const rendered = chatMarkdown(text);
    const plain    = rendered.replace(/<[^>]+>/g, '').replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>');

    const box = document.createElement('div');
    box.style.cssText = 'font-size:13px;color:#334155;line-height:1.65;';
    box.className = 'type-cursor';
    inner.innerHTML = '';
    inner.appendChild(box);

    const wrap  = document.getElementById('chat-messages');
    const speed = plain.length > 400 ? 6 : plain.length > 150 ? 12 : 18;
    const chunk = plain.length > 400 ? 5 : plain.length > 150 ? 3 : 1;
    let i = 0;

    const tick = () => {
        if (i >= plain.length) {
            inner.innerHTML = `<div style="font-size:13px;color:#334155;line-height:1.65;">${rendered}</div>${ctxHtml}`;
            wrap.scrollTop = wrap.scrollHeight;
            return;
        }
        i = Math.min(i + chunk, plain.length);
        box.textContent = plain.slice(0, i);
        wrap.scrollTop = wrap.scrollHeight;
        setTimeout(tick, speed);
    };
    tick();
}

function chatMarkdown(text) {
    // Escape HTML
    text = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

    // Normalize line endings
    text = text.replace(/\r\n/g, '\n');

    // Code blocks
    text = text.replace(/```(\w*)\n?([\s\S]*?)```/g, (_, lang, code) => {
        code = code.trim();
        // Reformat single-line flow diagrams: "A ↓ B ↓ C" → each step on its own line
        if (/\S[ \t]*↓[ \t]*\S/.test(code)) {
            code = code.split('↓').map(s => s.trim()).filter(Boolean).join('\n    ↓\n');
        }
        return `<pre style="background:#1e293b;color:#86efac;border-radius:8px;padding:12px;margin:8px 0;font-size:11px;white-space:pre-wrap;word-break:break-word;overflow-x:auto;font-family:monospace;"><code style="background:none;border:none;padding:0;color:inherit;">${code}</code></pre>`;
    });

    // Normalize inline tables — AI sometimes returns all rows on one line separated by "| |"
    // e.g. "| Col1 | Col2 | |---|---| | val1 | val2 |" → split each row onto its own line
    text = text.replace(/\|\s*\|/g, '|\n|');

    // Tables — process line by line
    const lines = text.split('\n');
    const out   = [];
    let inTable = false, headerDone = false, tableHtml = '';

    for (const line of lines) {
        const tr = line.trim();
        if (tr.startsWith('|')) {
            // Separator row (e.g. |---|---|)
            if (/^\|[\s\-\|:]+\|?\s*$/.test(tr)) {
                headerDone = true;
                continue;
            }
            const cells = tr.replace(/^\||\|$/g, '').split('|').map(c => c.trim());
            if (!inTable) {
                inTable    = true;
                headerDone = false;
                tableHtml  = '<div style="overflow-x:auto;margin:8px 0"><table style="width:100%;border-collapse:collapse;font-size:11px">';
            }
            if (!headerDone) {
                tableHtml += '<thead><tr>' + cells.map(c =>
                    `<th style="border:1px solid #e2e8f0;background:#f8fafc;padding:5px 10px;text-align:left;font-weight:700;color:#475569;white-space:nowrap">${c}</th>`
                ).join('') + '</tr></thead><tbody>';
                headerDone = true;
            } else {
                tableHtml += '<tr>' + cells.map(c =>
                    `<td style="border:1px solid #e2e8f0;padding:5px 10px;color:#334155;vertical-align:top">${c}</td>`
                ).join('') + '</tr>';
            }
        } else {
            if (inTable) {
                tableHtml += '</tbody></table></div>';
                out.push(tableHtml);
                tableHtml = ''; inTable = false; headerDone = false;
            }
            out.push(line);
        }
    }
    if (inTable) { tableHtml += '</tbody></table></div>'; out.push(tableHtml); }
    text = out.join('\n');

    // Inline formatting
    return text
        .replace(/`([^`]+)`/g, '<code style="background:#f1f5f9;color:#4f46e5;padding:1px 5px;border-radius:3px;font-size:11px;font-family:monospace;border:none;">$1</code>')
        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.+?)\*/g, '<em>$1</em>')
        .replace(/^### (.+)$/gm, '<p style="font-weight:600;color:#1e293b;margin:10px 0 3px;font-size:13px;">$1</p>')
        .replace(/^## (.+)$/gm,  '<p style="font-weight:700;color:#1e293b;margin:12px 0 4px;font-size:14px;">$1</p>')
        .replace(/^# (.+)$/gm,   '<p style="font-weight:700;color:#0f172a;margin:14px 0 6px;font-size:16px;">$1</p>')
        .replace(/^- (.+)$/gm,   '<li style="margin-left:16px;list-style-type:disc;margin-bottom:2px;">$1</li>')
        .replace(/\n\n/g, '<br>')
        .replace(/\n/g, ' ');
}

// ── AI Docs ────────────────────────────────────────────────────────────────────

const DOCS_ENDPOINT = '{{ route("laradar.ai.documentation") }}';
const _docsContent  = {};
const DOC_TYPES     = ['architecture','models','controllers','routes','services','modules'];


async function docsGenerate(type) {
    const btn    = document.getElementById('doc-gen-btn-' + type);
    const status = document.getElementById('doc-status-' + type);

    btn.disabled = true;
    btn.innerHTML = `<svg style="width:12px;height:12px;animation:spin 1s linear infinite;" fill="none" viewBox="0 0 24 24"><circle style="opacity:.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path style="opacity:.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Generating…`;
    status.textContent = 'Generating…';
    status.style.color = 'var(--cyan)';

    try {
        const res  = await fetch(DOCS_ENDPOINT, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': AI_CSRF },
            body:    JSON.stringify({ type }),
        });
        const json = await res.json();
        if (!res.ok || json.error) throw new Error(json.error || 'Server error');

        _docsContent[type] = { content: json.content, filename: json.filename };

        status.textContent = '✔ Ready';
        status.style.color = 'var(--emerald)';
        btn.innerHTML = `<svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Re-generate`;
        btn.disabled = false;

        // Auto-download in the selected format
        const fmt = document.getElementById('doc-fmt-' + type)?.value || 'md';
        if (fmt === 'html') {
            docsDownloadHtml(type);
        } else {
            docsDownload(type);
        }

        document.getElementById('docs-download-all-btn').style.display = 'inline-flex';

    } catch (err) {
        status.textContent = '✘ Failed';
        status.style.color = 'var(--rose)';
        btn.innerHTML = `<svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Retry`;
        btn.disabled = false;
    }
}

let _fmtInputId = null;
let _fmtBtn     = null;

function openFmtPanel(btn, inputId) {
    const panel  = document.getElementById('fmt-shared-panel');
    const isOpen = panel.style.display !== 'none' && _fmtInputId === inputId;

    // Close any open panel first
    panel.style.display = 'none';
    if (_fmtBtn) { const s = _fmtBtn.querySelector('svg'); if (s) s.style.transform = ''; }

    if (!isOpen) {
        _fmtInputId = inputId;
        _fmtBtn     = btn;

        const rect   = btn.getBoundingClientRect();
        const panelW = Math.max(rect.width, 88);
        const panelH = 82; // 2 options

        // Horizontal: left-align with button, clamp inside viewport
        let left = rect.left;
        if (left + panelW > window.innerWidth - 8) left = rect.right - panelW;
        if (left < 8) left = 8;

        // Vertical: open below; flip above if not enough room
        const top = (window.innerHeight - rect.bottom >= panelH + 8)
            ? rect.bottom + 4
            : rect.top - panelH - 4;

        panel.style.left    = left + 'px';
        panel.style.top     = top  + 'px';
        panel.style.width   = panelW + 'px';
        panel.style.display = 'block';

        const svg = btn.querySelector('svg');
        if (svg) svg.style.transform = 'rotate(180deg)';
    }
}

function selectFmtOption(value) {
    if (_fmtInputId) {
        const input = document.getElementById(_fmtInputId);
        if (input) input.value = value;
    }
    if (_fmtBtn) {
        const span = _fmtBtn.querySelector('span');
        if (span) span.textContent = '.' + value;
        const svg = _fmtBtn.querySelector('svg');
        if (svg) svg.style.transform = '';
    }
    document.getElementById('fmt-shared-panel').style.display = 'none';
    _fmtInputId = null;
    _fmtBtn     = null;
}

document.addEventListener('click', (e) => {
    if (!e.target.closest('[data-fmt-btn]') && !e.target.closest('#fmt-shared-panel')) {
        const panel = document.getElementById('fmt-shared-panel');
        if (panel) panel.style.display = 'none';
        if (_fmtBtn) { const s = _fmtBtn.querySelector('svg'); if (s) s.style.transform = ''; }
        _fmtInputId = null;
        _fmtBtn     = null;
    }
});

async function docsGenerateAll() {
    const globalFmt = document.getElementById('docs-fmt-all')?.value || 'md';
    for (const type of DOC_TYPES) {
        const fmtEl = document.getElementById('doc-fmt-' + type);
        if (fmtEl) fmtEl.value = globalFmt;
        const cardBtn = document.getElementById('doc-fmt-btn-' + type);
        if (cardBtn) { const s = cardBtn.querySelector('span'); if (s) s.textContent = '.' + globalFmt; }
        await docsGenerate(type);
    }
}

function _normalizeMdTables(md) {
    const lines  = md.replace(/\r\n/g, '\n').replace(/\r/g, '\n').split('\n');
    const result = [];
    let   block  = [];

    const parseCells = r => r.trim().replace(/^\||\|[ \t]*$/g, '').split('|').map(c => c.trim());
    const isSep      = r => /^\|?[\s|:=-]+\|?$/.test(r.trim());

    // Fit an arbitrary-length cells array into exactly colCount columns.
    // When there are MORE cells than columns, the extras are merged into column 1
    // (the "middle" column) so no data is lost — e.g. per-route columns get
    // joined with ", " into a single Routes cell.
    const fitCells = (cells, colCount) => {
        if (cells.length === colCount) return cells;
        if (cells.length < colCount)  return Array.from({ length: colCount }, (_, i) => cells[i] ?? '');

        // cells.length > colCount: merge the overflow into the second column
        const extraCount  = cells.length - colCount;
        const mergedSlice = cells.slice(1, 2 + extraCount).filter(Boolean).join(', ');
        const tail        = cells.slice(2 + extraCount); // remaining columns after the merge
        return [cells[0], mergedSlice, ...tail];
    };

    const flushBlock = () => {
        if (!block.length) return;
        const tableRows = block.filter(r => r.trim().startsWith('|'));
        if (!tableRows.length) { result.push(...block); block = []; return; }

        const dataRows = tableRows.filter(r => !isSep(r));
        if (dataRows.length < 1) { result.push(...block); block = []; return; }

        // Column count is authoritative from the header row (first data row)
        const colCount = parseCells(dataRows[0]).length;

        tableRows.forEach(row => {
            if (isSep(row)) {
                result.push('| ' + Array(colCount).fill('---').join(' | ') + ' |');
            } else {
                const normalised = fitCells(parseCells(row), colCount);
                result.push('| ' + normalised.join(' | ') + ' |');
            }
        });
        block = [];
    };

    for (const line of lines) {
        if (line.trim().startsWith('|')) {
            block.push(line);
        } else {
            flushBlock();
            result.push(line);
        }
    }
    flushBlock();
    return result.join('\n');
}

function docsDownload(type) {
    const doc = _docsContent[type];
    if (!doc) return;
    _downloadBlob(_normalizeMdTables(doc.content), doc.filename, 'text/markdown');
}

function docsDownloadAll() {
    const ready = DOC_TYPES.filter(t => _docsContent[t]);
    ready.forEach((type, i) => {
        setTimeout(() => docsDownload(type), i * 300);
    });
}

// ── AI Graphic Report ─────────────────────────────────────────────────────────

async function generateAIGraphicReport() {
    const btn    = document.getElementById('ai-report-btn');
    const label  = document.getElementById('ai-report-btn-label');
    const panel  = document.getElementById('ai-report-progress');
    const errEl  = document.getElementById('ai-report-error');
    const spinner = document.getElementById('ai-report-spinner');
    const title  = document.getElementById('ai-report-progress-title');

    btn.disabled  = true;
    label.textContent = 'Generating…';
    panel.style.display = 'block';
    errEl.style.display = 'none';
    errEl.textContent = '';

    // Reset all step icons
    document.querySelectorAll('#ai-report-steps .step-icon').forEach(el => {
        el.style.cssText = 'width:14px;height:14px;border-radius:50%;border:2px solid rgba(148,178,222,0.4);flex-shrink:0;display:inline-block;background:none;';
        el.innerHTML = '';
    });

    const _stepDone  = (step) => {
        const el = document.querySelector(`[data-step="${step}"] .step-icon`);
        if (el) { el.style.cssText = 'width:14px;height:14px;border-radius:50%;flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;background:#34D399;border:none;'; el.innerHTML = '<svg viewBox="0 0 16 16" fill="none" width="10" height="10"><path stroke="white" stroke-width="2" stroke-linecap="round" fill="none" d="M3 8l3.5 3.5 6-7"/></svg>'; }
    };
    const _stepActive = (step) => {
        const el = document.querySelector(`[data-step="${step}"] .step-icon`);
        if (el) { el.style.cssText = 'width:14px;height:14px;border-radius:50%;border:2px solid #A78BFA;background:rgba(167,139,250,0.2);flex-shrink:0;display:inline-block;animation:pulse 1s infinite;'; }
    };
    const _stepFail  = (step) => {
        const el = document.querySelector(`[data-step="${step}"] .step-icon`);
        if (el) { el.style.cssText = 'width:14px;height:14px;border-radius:50%;flex-shrink:0;display:inline-block;background:#FBBF24;border:none;'; }
    };

    let aiAnalysis = null;
    const aiDocs   = {};

    try {
        // ── Step 1: AI analyze ──────────────────────────────────────────────
        _stepActive('analyze');
        const analyzeRes  = await fetch(AI_ENDPOINT, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': AI_CSRF },
        });
        const analyzeJson = await analyzeRes.json();
        if (!analyzeRes.ok || analyzeJson.error) throw new Error(analyzeJson.error || 'AI analysis failed');
        aiAnalysis = analyzeJson;
        _stepDone('analyze');

        // ── Steps 2–7: AI docs per section ──────────────────────────────────
        for (const type of DOC_TYPES) {
            _stepActive(type);
            try {
                const res  = await fetch(DOCS_ENDPOINT, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': AI_CSRF },
                    body:    JSON.stringify({ type }),
                });
                const json = await res.json();
                if (!res.ok || json.error) throw new Error(json.error);
                aiDocs[type] = json.content;
                _stepDone(type);
            } catch(e) {
                _stepFail(type);
                aiDocs[type] = null;
            }
        }

        // ── Step 8: Build & download ────────────────────────────────────────
        _stepActive('build');
        const html = _buildAIGraphicReport(APP, aiAnalysis, aiDocs);
        _downloadBlob(html, 'ai-architecture-report.html', 'text/html;charset=utf-8');
        _stepDone('build');

        title.textContent = 'Report ready — downloading!';
        spinner.style.display = 'none';

    } catch(err) {
        errEl.textContent = 'Error: ' + err.message;
        errEl.style.display = 'block';
        title.textContent = 'Generation failed';
        spinner.style.display = 'none';
    } finally {
        btn.disabled = false;
        label.textContent = 'Generate AI Graphic Report';
    }
}

function _mdToHtml(md) {
    if (!md) return '';
    // Normalize line endings FIRST — AI responses may use \r\n (Windows) or \r (old Mac)
    const normalized = md.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
    let html = _esc(normalized);

    // ── Code blocks (before inline code) ────────────────────────────────────
    html = html.replace(/```[\w]*\n?([\s\S]*?)```/g, (_, code) =>
        `<pre style="background:#1e293b;color:#e2e8f0;border-radius:10px;padding:16px;overflow-x:auto;font-family:ui-monospace,monospace;font-size:13px;line-height:1.6;margin:12px 0">${code.trim()}</pre>`
    );
    // ── Inline code ──────────────────────────────────────────────────────────
    html = html.replace(/`([^`\n]+)`/g,
        '<code style="background:#f1f5f9;color:#0f172a;padding:2px 6px;border-radius:4px;font-family:ui-monospace,monospace;font-size:0.9em">$1</code>'
    );
    // ── Headings ─────────────────────────────────────────────────────────────
    html = html.replace(/^### (.+)$/gm, '<h3 style="font-size:16px;font-weight:700;color:#1e293b;margin:20px 0 8px">$1</h3>');
    html = html.replace(/^## (.+)$/gm,  '<h2 style="font-size:20px;font-weight:800;color:#0f172a;margin:28px 0 10px;padding-bottom:6px;border-bottom:2px solid #e2e8f0">$1</h2>');
    html = html.replace(/^# (.+)$/gm,   '<h1 style="font-size:26px;font-weight:900;color:#0f172a;margin:0 0 16px">$1</h1>');

    // ── Tables (line-by-line approach — immune to \r\n and trailing-pipe bugs) ─
    const tableStyle  = 'width:100%;border-collapse:collapse;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden';
    const thStyle     = 'background:#f8fafc;padding:9px 12px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#64748b;border-bottom:2px solid #e2e8f0;white-space:nowrap';
    const tdStyle     = 'padding:9px 12px;border-bottom:1px solid #f1f5f9;font-size:13px;color:#334155;vertical-align:top';
    const isSep       = r => /^\|?[\s|:=-]+\|?$/.test(r.trim());
    const parseCells  = r => r.trim().replace(/^\||\|[ \t]*$/g, '').split('|').map(c => c.trim());

    // Collect runs of lines that start with | and render each run as a table
    const lines = html.split('\n');
    const out   = [];
    let tableLines = [];

    const flushTable = () => {
        if (!tableLines.length) return;
        const dataRows = tableLines.filter(r => r.trim().startsWith('|') && !isSep(r));
        if (dataRows.length < 1) { out.push(...tableLines); tableLines = []; return; }
        const [hRow, ...bRows] = dataRows;
        const ths = parseCells(hRow).map(h => `<th style="${thStyle}">${h}</th>`).join('');
        const trs = bRows.map(r =>
            `<tr>${parseCells(r).map(c => `<td style="${tdStyle}">${c}</td>`).join('')}</tr>`
        ).join('');
        out.push(`<div style="overflow-x:auto;margin:12px 0"><table style="${tableStyle}"><thead><tr>${ths}</tr></thead><tbody>${trs}</tbody></table></div>`);
        tableLines = [];
    };

    for (const line of lines) {
        if (line.trim().startsWith('|')) {
            tableLines.push(line);
        } else {
            flushTable();
            out.push(line);
        }
    }
    flushTable();
    html = out.join('\n');

    // ── Bold / italic ────────────────────────────────────────────────────────
    html = html.replace(/\*\*([^*\n]+)\*\*/g, '<strong>$1</strong>');
    html = html.replace(/\*([^*\n]+)\*/g,     '<em>$1</em>');
    // ── Unordered lists ──────────────────────────────────────────────────────
    html = html.replace(/((?:^[ \t]*[-*] .+\n?)+)/gm, (block) => {
        const items = block.trim().split('\n')
            .map(l => `<li style="margin-bottom:4px">${l.replace(/^[ \t]*[-*] /, '')}</li>`).join('');
        return `<ul style="list-style:disc;padding-left:20px;margin:8px 0">${items}</ul>`;
    });
    // ── Ordered lists ────────────────────────────────────────────────────────
    html = html.replace(/((?:^\d+\. .+\n?)+)/gm, (block) => {
        const items = block.trim().split('\n')
            .map(l => `<li style="margin-bottom:4px">${l.replace(/^\d+\. /, '')}</li>`).join('');
        return `<ol style="list-style:decimal;padding-left:20px;margin:8px 0">${items}</ol>`;
    });
    // ── Horizontal rule ──────────────────────────────────────────────────────
    html = html.replace(/^---+$/gm, '<hr style="border:none;border-top:1px solid #e2e8f0;margin:20px 0"/>');
    // ── Paragraphs ───────────────────────────────────────────────────────────
    html = html.split(/\n{2,}/).map(block => {
        if (/^<(h[1-3]|ul|ol|pre|hr|div)/.test(block.trimStart())) return block;
        const trimmed = block.trim();
        return trimmed ? `<p style="margin:0 0 12px;color:#334155;line-height:1.7">${trimmed}</p>` : '';
    }).join('\n');

    return html;
}

function _buildAIGraphicReport(d, ai, docs) {
    const esc   = s => String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    const proj  = d.project?.name ?? 'Laravel App';
    const score = d.score?.score  ?? 0;
    const grade = d.score?.grade  ?? '';
    const s     = d.summary       ?? {};
    const rs    = d.route_summary ?? {};

    // ── Score gauge SVG ─────────────────────────────────────────────────────
    const R = 64, CX = 80, CY = 80, SW = 14;
    const circ   = 2 * Math.PI * R;
    const arc    = circ * 0.75;
    const offset = arc - (arc * score / 100);
    const gColor = score >= 80 ? '#10b981' : score >= 60 ? '#f59e0b' : '#ef4444';
    const gaugeSvg = `<svg width="160" height="160" viewBox="0 0 160 160">
        <circle cx="${CX}" cy="${CY}" r="${R}" fill="none" stroke="#f1f5f9" stroke-width="${SW}" stroke-linecap="round" stroke-dasharray="${arc} ${circ}" stroke-dashoffset="0" transform="rotate(135 ${CX} ${CY})"/>
        <circle cx="${CX}" cy="${CY}" r="${R}" fill="none" stroke="${gColor}" stroke-width="${SW}" stroke-linecap="round" stroke-dasharray="${arc} ${circ}" stroke-dashoffset="${offset}" transform="rotate(135 ${CX} ${CY})"/>
        <text x="${CX}" y="${CY - 8}" text-anchor="middle" font-size="28" font-weight="800" fill="#1e293b" font-family="system-ui,sans-serif">${score}</text>
        <text x="${CX}" y="${CY + 14}" text-anchor="middle" font-size="12" font-weight="600" fill="${gColor}" font-family="system-ui,sans-serif">${esc(grade)}</text>
        <text x="${CX}" y="${CY + 30}" text-anchor="middle" font-size="9" fill="#94a3b8" font-family="system-ui,sans-serif">/ 100</text>
    </svg>`;

    // ── Component stat cards ─────────────────────────────────────────────────
    const stats = [
        ['Models',       s.models??0,        '#8b5cf6','#f5f3ff','#ede9fe'],
        ['Controllers',  s.controllers??0,   '#3b82f6','#eff6ff','#dbeafe'],
        ['Routes',       s.routes??0,        '#10b981','#f0fdf4','#d1fae5'],
        ['Services',     s.services??0,      '#06b6d4','#ecfeff','#cffafe'],
        ['Repositories', s.repositories??0,  '#f59e0b','#fffbeb','#fef3c7'],
        ['Jobs',         s.jobs??0,          '#f97316','#fff7ed','#ffedd5'],
        ['Jobs',         $summary['jobs']??0],
        ['Events',       s.events??0,        '#d946ef','#fdf4ff','#fae8ff'],
        ['Policies',     s.policies??0,      '#64748b','#f8fafc','#f1f5f9'],
        ['API Routes',   rs.api??0,          '#0891b2','#ecfeff','#cffafe'],
        ['Named Routes', rs.named_count??0,  '#7c3aed','#f5f3ff','#ede9fe'],
    ];
    const statCards = stats.map(([name, count, color, bg, border]) =>
        `<div style="background:${bg};border:1px solid ${border};border-radius:14px;padding:16px 18px">
            <div style="font-size:26px;font-weight:800;color:${color};font-family:system-ui,sans-serif">${count}</div>
            <div style="font-size:11px;color:#64748b;font-weight:500;margin-top:2px">${esc(name)}</div>
        </div>`
    ).join('');

    // ── Score checks ─────────────────────────────────────────────────────────
    const checkRows = (d.score?.checks ?? []).map(c => {
        const icon  = c.status === 'pass' ? '✔' : c.status === 'warn' ? '⚠' : '✘';
        const color = c.status === 'pass' ? '#10b981' : c.status === 'warn' ? '#f59e0b' : '#ef4444';
        return `<tr style="border-bottom:1px solid #f1f5f9">
            <td style="padding:9px 10px;font-size:15px;color:${color}">${icon}</td>
            <td style="padding:9px 10px;font-size:13px;font-weight:600;color:#1e293b">${esc(c.label)}</td>
            <td style="padding:9px 10px;font-size:12px;color:#64748b">${esc(c.note ?? '')}</td>
        </tr>`;
    }).join('');

    // ── AI problems ──────────────────────────────────────────────────────────
    const sevColor = { error:'#ef4444', warning:'#f59e0b', info:'#3b82f6' };
    const sevBg    = { error:'#fef2f2', warning:'#fffbeb', info:'#eff6ff' };
    const problemCards = (ai?.problems ?? []).map(p => {
        const col = sevColor[p.severity] ?? '#64748b';
        const bg  = sevBg[p.severity]   ?? '#f8fafc';
        return `<div style="background:${bg};border:1px solid ${col}30;border-left:4px solid ${col};border-radius:10px;padding:14px 16px;margin-bottom:10px">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
                <span style="font-size:11px;font-weight:700;color:${col};text-transform:uppercase;letter-spacing:0.06em;background:${col}20;padding:2px 8px;border-radius:20px">${esc(p.severity)}</span>
                <strong style="font-size:13px;color:#1e293b">${esc(p.title)}</strong>
            </div>
            <p style="font-size:13px;color:#475569;margin:0 0 6px;line-height:1.6">${esc(p.description)}</p>
            ${p.location ? `<code style="font-size:11px;color:#64748b;background:#f1f5f9;padding:2px 8px;border-radius:6px">${esc(p.location)}</code>` : ''}
        </div>`;
    }).join('') || '<p style="color:#94a3b8;font-size:13px">No problems detected.</p>';

    // ── AI suggestions ───────────────────────────────────────────────────────
    const priColor = { high:'#ef4444', medium:'#f59e0b', low:'#10b981' };
    const suggCards = (ai?.suggestions ?? []).map(p => {
        const col = priColor[p.priority] ?? '#64748b';
        return `<div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:14px 16px;margin-bottom:10px">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
                <span style="font-size:11px;font-weight:700;color:${col};background:${col}20;padding:2px 8px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em">${esc(p.priority)}</span>
                <strong style="font-size:13px;color:#1e293b">${esc(p.title)}</strong>
            </div>
            <p style="font-size:13px;color:#475569;margin:0 0 6px;line-height:1.6">${esc(p.description)}</p>
            ${p.example ? `<pre style="font-size:12px;color:#1e293b;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:10px;margin:6px 0 0;overflow-x:auto;font-family:ui-monospace,monospace">${esc(p.example)}</pre>` : ''}
        </div>`;
    }).join('') || '<p style="color:#94a3b8;font-size:13px">No suggestions available.</p>';

    // ── SOLID review ─────────────────────────────────────────────────────────
    const solidCards = Object.entries(ai?.solid_review ?? {}).map(([letter, data]) => {
        const col = data.status === 'pass' ? '#10b981' : data.status === 'warn' ? '#f59e0b' : '#ef4444';
        const bg  = data.status === 'pass' ? '#f0fdf4' : data.status === 'warn' ? '#fffbeb' : '#fef2f2';
        const fullName = { S:'Single Responsibility', O:'Open / Closed', L:'Liskov Substitution', I:'Interface Segregation', D:'Dependency Inversion' };
        return `<div style="background:${bg};border:1px solid ${col}30;border-radius:12px;padding:16px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
                <div style="width:36px;height:36px;border-radius:50%;background:${col};display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:900;color:#fff;flex-shrink:0">${esc(letter)}</div>
                <div>
                    <p style="font-size:11px;font-weight:700;color:${col};text-transform:uppercase;margin:0">${data.status}</p>
                    <p style="font-size:12px;font-weight:600;color:#1e293b;margin:0">${esc(fullName[letter] ?? letter)}</p>
                </div>
            </div>
            <p style="font-size:12px;color:#475569;margin:0;line-height:1.6">${esc(data.note ?? '')}</p>
        </div>`;
    }).join('');

    // ── Best practices ───────────────────────────────────────────────────────
    const bpItems = (ai?.laravel_best_practices ?? []).map(bp => {
        const icon  = bp.status === 'pass' ? '✔' : bp.status === 'warn' ? '⚠' : '✘';
        const color = bp.status === 'pass' ? '#10b981' : bp.status === 'warn' ? '#f59e0b' : '#ef4444';
        return `<div style="display:flex;gap:10px;padding:10px 0;border-bottom:1px solid #f1f5f9">
            <span style="font-size:14px;color:${color};flex-shrink:0;width:18px">${icon}</span>
            <div>
                <p style="font-size:13px;font-weight:600;color:#1e293b;margin:0 0 2px">${esc(bp.name)}</p>
                <p style="font-size:12px;color:#64748b;margin:0">${esc(bp.note)}</p>
            </div>
        </div>`;
    }).join('');

    // ── Dependency graph ─────────────────────────────────────────────────────
    const depSvg = _buildDepSvg(d.dependencies?.nodes ?? [], d.dependencies?.edges ?? []);

    // ── Section helper ───────────────────────────────────────────────────────
    const sec = (title, color, content) =>
        `<section style="margin-bottom:52px">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:22px">
                <div style="width:4px;height:34px;border-radius:2px;background:${color};flex-shrink:0"></div>
                <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;font-family:system-ui,sans-serif">${esc(title)}</h2>
            </div>
            ${content}
        </section>`;

    const docSection = (type, label, color) => {
        if (!docs[type]) return '';
        return sec(label, color,
            `<div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:32px">${_mdToHtml(docs[type])}</div>`
        );
    };

    // ── Full HTML ─────────────────────────────────────────────────────────────
    return `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>AI Architecture Report — ${esc(proj)}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#f8fafc;font-family:system-ui,-apple-system,sans-serif;color:#1e293b;line-height:1.5}
@media print{body{background:#fff}.no-print{display:none!important}}
</style>
</head>
<body>

<!-- HEADER -->
<div style="background:linear-gradient(135deg,#1e1b4b 0%,#312e81 60%,#4c1d95 100%);padding:48px;display:flex;align-items:center;justify-content:space-between;gap:24px;flex-wrap:wrap">
    <div>
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:20px;padding:4px 14px;margin-bottom:16px">
            <span style="font-size:11px;color:#a5b4fc;font-weight:700;letter-spacing:0.1em">AI-POWERED ARCHITECTURE REPORT</span>
        </div>
        <h1 style="font-size:36px;font-weight:900;color:#fff;margin-bottom:10px">${esc(proj)}</h1>
        <div style="display:flex;gap:20px;flex-wrap:wrap">
            <span style="font-size:13px;color:#a5b4fc">Laravel ${esc(d.laravel_version ?? '')}</span>
            <span style="font-size:13px;color:#a5b4fc">PHP ${esc(d.php_version ?? '')}</span>
            <span style="font-size:13px;color:#a5b4fc">Generated ${esc(d.generated_at ?? '')}</span>
            <span style="font-size:13px;color:#a5b4fc">Provider: ${esc(ai?.provider ?? d.ai_provider ?? 'AI')}</span>
        </div>
    </div>
    <div style="text-align:center;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:20px;padding:24px 32px;flex-shrink:0">
        <div style="font-size:52px;font-weight:900;line-height:1;color:#fff">${score}</div>
        <div style="font-size:15px;font-weight:700;color:#a78bfa;margin-top:6px">${esc(grade)}</div>
        <div style="font-size:11px;color:#6d6d9a;margin-top:4px">Architecture Score</div>
    </div>
</div>

<!-- AI SUMMARY BANNER -->
${ai?.summary ? `<div style="background:#f0f9ff;border-bottom:2px solid #bfdbfe;padding:24px 48px">
    <p style="font-size:11px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:8px">AI Executive Summary</p>
    <p style="font-size:15px;color:#1e3a5f;line-height:1.7;max-width:900px">${esc(ai.summary)}</p>
</div>` : ''}

<!-- BODY -->
<div style="max-width:1200px;margin:0 auto;padding:48px 32px">

    <!-- Stats Grid -->
    ${sec('Component Overview', '#4f46e5',
        `<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:12px">${statCards}</div>`
    )}

    <!-- Score -->
    ${sec('Architecture Score', '#10b981',
        `<div style="display:grid;grid-template-columns:160px 1fr;gap:24px;background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:24px;align-items:start">
            ${gaugeSvg}
            <table style="width:100%;border-collapse:collapse;font-family:system-ui,sans-serif"><tbody>${checkRows}</tbody></table>
        </div>`
    )}

    <!-- SOLID Review -->
    ${solidCards ? sec('SOLID Principles', '#6366f1',
        `<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px">${solidCards}</div>`
    ) : ''}

    <!-- Best Practices -->
    ${bpItems ? sec('Laravel Best Practices', '#10b981',
        `<div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:8px 20px">${bpItems}</div>`
    ) : ''}

    <!-- Problems -->
    ${sec('Issues Detected', '#ef4444',
        `<div>${problemCards}</div>`
    )}

    <!-- Suggestions -->
    ${sec('AI Suggestions', '#f59e0b',
        `<div>${suggCards}</div>`
    )}

    <!-- Dependency Graph -->
    ${depSvg ? sec('Dependency Graph', '#6366f1',
        `<div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden"><div style="overflow-x:auto;padding:20px">${depSvg}</div></div>`
    ) : ''}

    <!-- AI Documentation sections -->
    ${docSection('architecture', 'Architecture Overview', '#4f46e5')}
    ${docSection('models',       'Models Documentation',  '#8b5cf6')}
    ${docSection('controllers',  'Controllers Documentation', '#3b82f6')}
    ${docSection('routes',       'Routes Documentation',  '#10b981')}
    ${docSection('services',     'Services Documentation','#f59e0b')}
    ${docSection('modules',      'Modules Documentation', '#06b6d4')}

</div>

<!-- FOOTER -->
<div style="background:#1e293b;color:#64748b;text-align:center;padding:28px;font-size:12px;font-family:system-ui,sans-serif">
    AI Architecture Report · Generated by <strong style="color:#94a3b8">laradar</strong> · ${esc(d.generated_at ?? '')}
</div>

</body>
</html>`;
}

// ── Export helpers ────────────────────────────────────────────────────────────

function exportJson() {
    _downloadBlob(
        JSON.stringify(APP, null, 2),
        'architecture.json',
        'application/json'
    );
}

function copyPkgKey(btn, key) {
    const cmd = 'composer require ' + key;
    navigator.clipboard.writeText(cmd).then(() => {
        const origHTML = btn.innerHTML;
        btn.innerHTML = '<svg style="width:13px;height:13px;color:#34D399;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        setTimeout(() => { btn.innerHTML = origHTML; }, 1800);
    }).catch(() => {});
}

function copyJson() {
    const btn  = document.getElementById('copy-json-btn');
    const text = JSON.stringify(APP, null, 2);
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        setTimeout(() => { btn.innerHTML = orig; }, 1800);
    }).catch(() => {
        btn.title = 'Copy failed — try JSON download instead';
    });
}

function exportMarkdown() {
    const d   = APP;
    const s   = d.summary || {};
    const rs  = d.route_summary || {};
    const sc  = d.score || {};
    const out = [];

    out.push('# Architecture Report — ' + (d.project?.name || 'Laravel Application'));
    out.push('');
    out.push('> Generated: ' + d.generated_at);
    out.push('> Laravel ' + d.laravel_version + ' · PHP ' + d.php_version + ' · laradar v' + d.package_version);
    out.push('');
    out.push('---');
    out.push('');

    if (sc.score !== undefined) {
        out.push('## Architecture Score');
        out.push('');
        out.push('**' + sc.score + ' / ' + sc.max + '** — ' + sc.grade);
        out.push('');
        (sc.checks || []).forEach(c => {
            const icon = c.status === 'pass' ? '✔' : c.status === 'warn' ? '⚠' : '✘';
            out.push(icon + ' ' + c.label + (c.note ? ' — *' + c.note + '*' : ''));
        });
        out.push('');
        out.push('---');
        out.push('');
    }

    out.push('## Summary');
    out.push('');
    out.push('| Component | Count |');
    out.push('|-----------|------:|');
    const rows = [
        ['Models', s.models], ['Controllers', s.controllers], ['Routes', s.routes],
        ['Jobs',         $summary['jobs']??0],
        ['Jobs', s.jobs], ['Events', s.events], ['Services', s.services],
        ['Repositories', s.repositories], ['Observers', s.observers],
        ['Policies', s.policies], ['Modules', s.modules], ['Packages', s.packages],
    ];
    rows.forEach(([label, count]) => { if (count) out.push('| ' + label + ' | ' + count + ' |'); });
    out.push('');

    if ((d.dependencies?.edges || []).length > 0) {
        out.push('---');
        out.push('');
        out.push('## Dependency Graph');
        out.push('');
        out.push('```mermaid');
        out.push('flowchart TD');
        const nodes = d.dependencies.nodes || [];
        const edges = d.dependencies.edges || [];
        const byLayer = {};
        nodes.forEach(n => { const l = n.layer || 'model'; (byLayer[l] = byLayer[l] || []).push(n.name); });
        ['controller','job','event','listener','service','repository','model'].forEach(layer => {
            if (!(byLayer[layer] || []).length) return;
            out.push('    subgraph ' + layer.charAt(0).toUpperCase() + layer.slice(1) + 's');
            byLayer[layer].forEach(nm => out.push('        ' + nm));
            out.push('    end');
        });
        edges.forEach(e => out.push('    ' + e.from + ' --> ' + e.to));
        out.push('```');
        out.push('');
    }

    out.push('---');
    out.push('');
    out.push('## Models');
    out.push('');
    (d.models || []).forEach(m => {
        out.push('### ' + m.name);
        out.push('');
        out.push('**Table:** `' + m.table + '`');
        if ((m.fillable || []).length) out.push('**Fillable:** `' + m.fillable.join('`, `') + '`');
        if ((m.relationships || []).length) {
            out.push('');
            out.push('| Method | Type | Related |');
            out.push('|--------|------|---------|');
            m.relationships.forEach(r => out.push('| `' + r.method + '` | `' + r.type + '` | `' + (r.related || '').split('\\').pop() + '` |'));
        }
        out.push('');
    });

    out.push('---');
    out.push('');
    out.push('## Routes');
    out.push('');
    out.push('| Method | URI | Controller | Name |');
    out.push('|--------|-----|------------|------|');
    (d.routes || []).forEach(r => {
        const methods = (r.methods || []).filter(m => m !== 'HEAD').join(',');
        const ctrl    = (r.controller?.class || '').split('\\').pop() || '—';
        const name    = r.name || '—';
        out.push('| ' + methods + ' | `' + r.uri + '` | ' + ctrl + ' | ' + name + ' |');
    });

    _downloadBlob(out.join('\n'), 'architecture.md', 'text/markdown');
}

// ── Graphic Report ────────────────────────────────────────────────────────────

// Module-level guard — true while a build+download is in progress.
// Any number of clicks during that window are silently dropped.
let _exportingHTML = false;

function exportGraphicHTML() {
    if (_exportingHTML) return;   // hard guard — drop every extra click
    _exportingHTML = true;

    const btn     = document.getElementById('graphic-report-btn');
    const label   = document.getElementById('graphic-report-label');
    const icon    = document.getElementById('graphic-report-icon');
    const spinner = document.getElementById('graphic-report-spinner');

    // Show busy state immediately
    btn.disabled        = true;
    btn.style.opacity   = '0.72';
    btn.style.cursor    = 'not-allowed';
    label.textContent   = 'Building…';
    icon.style.display  = 'none';
    spinner.style.display = '';

    // Defer the heavy work by one paint so the browser renders the loading state
    // before the main thread is blocked by report generation
    setTimeout(() => {
        try {
            const html = _buildGraphicReport(APP);
            const url  = URL.createObjectURL(new Blob([html], { type: 'text/html;charset=utf-8' }));
            const a    = document.createElement('a');
            a.href     = url;
            a.download = 'architecture-report.html';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            setTimeout(() => URL.revokeObjectURL(url), 3000);

            // Brief success confirmation before restoring button
            label.textContent     = 'Downloaded!';
            spinner.style.display = 'none';
            icon.innerHTML        = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>';
            icon.style.display    = '';
            setTimeout(() => _exportGraphicHTMLReset(btn, label, icon, spinner), 2000);

        } catch(e) {
            spinner.style.display = 'none';
            icon.innerHTML        = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
            icon.style.display    = '';
            label.textContent     = 'Failed — try again';
            btn.style.opacity     = '1';
            btn.style.cursor      = 'pointer';
            // Re-enable after showing error so user can retry
            setTimeout(() => _exportGraphicHTMLReset(btn, label, icon, spinner), 2500);
        }
    }, 50);
}

function _exportGraphicHTMLReset(btn, label, icon, spinner) {
    _exportingHTML        = false;
    btn.disabled          = false;
    btn.style.opacity     = '';
    btn.style.cursor      = '';
    spinner.style.display = 'none';
    icon.style.display    = '';
    icon.innerHTML        = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>';
    label.textContent     = 'Generate & Download';
}

function _buildGraphicReport(d) {
    const esc   = s => String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    const proj  = d.project?.name  ?? 'Laravel App';
    const score = d.score?.score   ?? 0;
    const grade = d.score?.grade   ?? '';
    const s     = d.summary        ?? {};
    const rs    = d.route_summary  ?? {};

    // ── Score gauge SVG ──────────────────────────────────────────────────────
    const R = 64, CX = 80, CY = 80, SW = 14;
    const circ     = 2 * Math.PI * R;
    const arc      = circ * 0.75;           // 270° sweep
    const offset   = arc - (arc * score / 100);
    const gColor   = score >= 80 ? '#10b981' : score >= 60 ? '#f59e0b' : '#ef4444';
    const gaugeSvg = `<svg width="160" height="160" viewBox="0 0 160 160">
        <circle cx="${CX}" cy="${CY}" r="${R}" fill="none" stroke="#f1f5f9" stroke-width="${SW}" stroke-linecap="round"
            stroke-dasharray="${arc} ${circ}" stroke-dashoffset="0" transform="rotate(135 ${CX} ${CY})"/>
        <circle cx="${CX}" cy="${CY}" r="${R}" fill="none" stroke="${gColor}" stroke-width="${SW}" stroke-linecap="round"
            stroke-dasharray="${arc} ${circ}" stroke-dashoffset="${offset}" transform="rotate(135 ${CX} ${CY})"/>
        <text x="${CX}" y="${CY - 8}" text-anchor="middle" font-size="28" font-weight="800" fill="#1e293b" font-family="system-ui,sans-serif">${score}</text>
        <text x="${CX}" y="${CY + 14}" text-anchor="middle" font-size="12" font-weight="600" fill="${gColor}" font-family="system-ui,sans-serif">${esc(grade)}</text>
        <text x="${CX}" y="${CY + 30}" text-anchor="middle" font-size="9" fill="#94a3b8" font-family="system-ui,sans-serif">/ 100</text>
    </svg>`;

    // ── Route method bars ────────────────────────────────────────────────────
    const methodColors = { GET:'#10b981', POST:'#3b82f6', PUT:'#f59e0b', PATCH:'#f97316', DELETE:'#ef4444' };
    const byMethod     = rs.by_method ?? {};
    const maxMCount    = Math.max(1, ...Object.values(byMethod));
    const routeBarsSvg = `<svg width="260" height="${Math.max(40, Object.keys(byMethod).length * 38 + 10)}" font-family="system-ui,sans-serif">
        ${Object.entries(byMethod).map(([m, cnt], i) => {
            const bw  = Math.max(4, Math.round((cnt / maxMCount) * 180));
            const col = methodColors[m] ?? '#64748b';
            const y   = i * 38 + 6;
            return `<rect x="0" y="${y}" width="${bw}" height="22" rx="6" fill="${col}" opacity="0.85"/>
                    <text x="${bw + 8}" y="${y + 15}" font-size="12" font-weight="700" fill="${col}">${cnt}</text>
                    <text x="${bw + 34}" y="${y + 15}" font-size="11" fill="#64748b">${esc(m)}</text>`;
        }).join('')}
    </svg>`;

    // ── Dependency graph SVG ─────────────────────────────────────────────────
    const depNodes = d.dependencies?.nodes ?? [];
    const depEdges = d.dependencies?.edges ?? [];
    const depSvg   = _buildDepSvg(depNodes, depEdges);

    // ── Stat cards HTML ──────────────────────────────────────────────────────
    const stats = [
        ['Models',       s.models       ?? 0, '#8b5cf6', '#f5f3ff', '#ede9fe'],
        ['Controllers',  s.controllers  ?? 0, '#3b82f6', '#eff6ff', '#dbeafe'],
        ['Routes',       s.routes       ?? 0, '#10b981', '#f0fdf4', '#d1fae5'],
        ['Services',     s.services     ?? 0, '#06b6d4', '#ecfeff', '#cffafe'],
        ['Repositories', s.repositories ?? 0, '#f59e0b', '#fffbeb', '#fef3c7'],
        ['Jobs',         s.jobs         ?? 0, '#f97316', '#fff7ed', '#ffedd5'],
        ['Jobs',         $summary['jobs']??0],
        ['Events',       s.events       ?? 0, '#d946ef', '#fdf4ff', '#fae8ff'],
        ['Observers',    s.observers    ?? 0, '#ec4899', '#fdf2f8', '#fce7f3'],
        ['Policies',     s.policies     ?? 0, '#64748b', '#f8fafc', '#f1f5f9'],
        ['Modules',      s.modules      ?? 0, '#4f46e5', '#eef2ff', '#e0e7ff'],
        ['API Routes',   rs.api         ?? 0, '#0891b2', '#ecfeff', '#cffafe'],
        ['Named Routes', rs.named_count ?? 0, '#7c3aed', '#f5f3ff', '#ede9fe'],
    ];
    const statCards = stats.map(([name, count, color, bg, border]) =>
        `<div style="background:${bg};border:1px solid ${border};border-radius:14px;padding:16px 20px;display:flex;flex-direction:column;gap:4px">
            <span style="font-size:24px;font-weight:800;color:${color};font-family:system-ui,sans-serif">${count}</span>
            <span style="font-size:12px;color:#64748b;font-family:system-ui,sans-serif;font-weight:500">${esc(name)}</span>
        </div>`
    ).join('');

    // ── Score checks ─────────────────────────────────────────────────────────
    const checkRows = (d.score?.checks ?? []).map(c => {
        const icon  = c.status === 'pass' ? '✔' : c.status === 'warn' ? '⚠' : '✘';
        const color = c.status === 'pass' ? '#10b981' : c.status === 'warn' ? '#f59e0b' : '#ef4444';
        return `<tr>
            <td style="padding:8px 12px;font-size:13px;color:${color};font-weight:700">${icon}</td>
            <td style="padding:8px 12px;font-size:13px;color:#1e293b;font-weight:500">${esc(c.label)}</td>
            <td style="padding:8px 12px;font-size:12px;color:#64748b">${esc(c.note ?? '')}</td>
        </tr>`;
    }).join('');

    // ── Models table ─────────────────────────────────────────────────────────
    const modelRows = (d.models ?? []).slice(0, 40).map(m => {
        const rels = (m.relationships ?? []).map(r => r.type + ':' + (r.related ?? '').split('\\').pop()).join(', ');
        const fill = (m.fillable ?? []).slice(0, 5).join(', ') + ((m.fillable ?? []).length > 5 ? ' …' : '');
        return `<tr>
            <td style="padding:9px 12px;font-weight:700;color:#1e293b;font-family:ui-monospace,monospace;font-size:13px">${esc(m.name)}</td>
            <td style="padding:9px 12px;color:#64748b;font-family:ui-monospace,monospace;font-size:12px">${esc(m.table ?? '')}</td>
            <td style="padding:9px 12px;color:#64748b;font-size:12px">${esc(fill)}</td>
            <td style="padding:9px 12px;color:#8b5cf6;font-size:12px">${esc(rels)}</td>
        </tr>`;
    }).join('');

    // ── Controllers table ────────────────────────────────────────────────────
    const ctrlRows = (d.controllers ?? []).slice(0, 30).map(c => {
        const barW = Math.min(120, Math.round((c.method_count ?? 0) * 8));
        const barColor = (c.method_count ?? 0) > 15 ? '#ef4444' : (c.method_count ?? 0) > 10 ? '#f59e0b' : '#10b981';
        return `<tr>
            <td style="padding:9px 12px;font-weight:700;color:#1e293b;font-family:ui-monospace,monospace;font-size:13px">${esc(c.name)}</td>
            <td style="padding:9px 12px">
                <div style="display:flex;align-items:center;gap:8px">
                    <div style="width:${barW}px;height:8px;background:${barColor};border-radius:4px;opacity:0.8"></div>
                    <span style="font-size:12px;font-weight:700;color:${barColor}">${c.method_count ?? 0}</span>
                </div>
            </td>
            <td style="padding:9px 12px;font-size:12px;color:#64748b;max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${esc((c.methods ?? []).slice(0,8).join(', '))}</td>
        </tr>`;
    }).join('');

    // ── Routes table ─────────────────────────────────────────────────────────
    const routeRows = (d.routes ?? []).slice(0, 50).map(r => {
        const methods = (r.methods ?? []).filter(m => m !== 'HEAD').join('|');
        const ctrl    = (r.controller?.class ?? '—').split('\\').pop();
        const action  = r.controller?.method ?? '—';
        const mwShort = (r.middleware ?? []).map(m => m.split('\\').pop()).slice(0, 2).join(', ');
        const mCol    = methodColors[methods] ?? '#64748b';
        return `<tr>
            <td style="padding:8px 12px"><span style="font-size:11px;font-weight:700;color:${mCol};background:${mCol}18;padding:2px 8px;border-radius:6px;font-family:ui-monospace,monospace">${esc(methods)}</span></td>
            <td style="padding:8px 12px;font-family:ui-monospace,monospace;font-size:12px;color:#1e293b;font-weight:600">${esc(r.uri)}</td>
            <td style="padding:8px 12px;font-family:ui-monospace,monospace;font-size:11px;color:#64748b">${esc(ctrl)}@${esc(action)}</td>
            <td style="padding:8px 12px;font-size:11px;color:#94a3b8">${esc(mwShort)}</td>
        </tr>`;
    }).join('');

    // ── Section header helper ─────────────────────────────────────────────────
    const secHeader = (title, sub, color = '#4f46e5') =>
        `<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
            <div style="width:4px;height:32px;border-radius:2px;background:${color};flex-shrink:0"></div>
            <div>
                <h2 style="margin:0;font-size:20px;font-weight:800;color:#0f172a;font-family:system-ui,sans-serif">${esc(title)}</h2>
                ${sub ? `<p style="margin:2px 0 0;font-size:13px;color:#64748b;font-family:system-ui,sans-serif">${esc(sub)}</p>` : ''}
            </div>
        </div>`;

    const tableWrap = (headers, rows) =>
        `<div style="border-radius:12px;overflow:hidden;border:1px solid #e2e8f0;background:#ffffff">
            <table style="width:100%;border-collapse:collapse;font-family:system-ui,sans-serif">
                <thead><tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0">
                    ${headers.map(h => `<th style="padding:10px 12px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.06em">${esc(h)}</th>`).join('')}
                </tr></thead>
                <tbody style="divide-y:1px solid #f1f5f9">${rows}</tbody>
            </table>
        </div>`;

    // ── Assemble full HTML ────────────────────────────────────────────────────
    return `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Architecture Report — ${esc(proj)}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#f8fafc;font-family:system-ui,-apple-system,sans-serif;color:#1e293b;line-height:1.5}
a{color:inherit;text-decoration:none}
table tr:nth-child(even){background:#fafbfc}
table tr:hover{background:#f1f5f9}
@media print{body{background:#fff}.no-print{display:none}}
</style>
</head>
<body>

<!-- ═══ HEADER ═══════════════════════════════════════════════════════════ -->
<div style="background:linear-gradient(135deg,#1e293b 0%,#312e81 100%);color:#fff;padding:40px 48px;display:flex;align-items:center;justify-content:space-between;gap:20px">
    <div>
        <p style="font-size:12px;text-transform:uppercase;letter-spacing:0.12em;color:#94a3b8;margin-bottom:6px">Architecture Report</p>
        <h1 style="font-size:32px;font-weight:800;margin-bottom:8px">${esc(proj)}</h1>
        <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:8px">
            <span style="font-size:13px;color:#94a3b8">Laravel ${esc(d.laravel_version ?? '')}</span>
            <span style="font-size:13px;color:#94a3b8">PHP ${esc(d.php_version ?? '')}</span>
            <span style="font-size:13px;color:#94a3b8">Generated: ${esc(d.generated_at ?? '')}</span>
        </div>
    </div>
    <div style="text-align:center;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:20px;padding:20px 28px;flex-shrink:0">
        <div style="font-size:48px;font-weight:900;line-height:1;color:#fff">${score}</div>
        <div style="font-size:14px;font-weight:700;color:#a78bfa;margin-top:4px">${esc(grade)}</div>
        <div style="font-size:11px;color:#64748b;margin-top:2px">Architecture Score</div>
    </div>
</div>

<!-- ═══ BODY ═════════════════════════════════════════════════════════════ -->
<div style="max-width:1200px;margin:0 auto;padding:40px 32px;display:flex;flex-direction:column;gap:48px">

    <!-- Stat Cards -->
    <section>
        ${secHeader('Component Overview', `${(d.models??[]).length} models · ${(d.controllers??[]).length} controllers · ${rs.total??0} routes`)}
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px">
            ${statCards}
        </div>
    </section>

    <!-- Score -->
    <section>
        ${secHeader('Architecture Score', `${score}/100 — ${esc(grade)}`, '#10b981')}
        <div style="display:grid;grid-template-columns:160px 1fr;gap:24px;align-items:start;background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:24px">
            ${gaugeSvg}
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse;font-family:system-ui,sans-serif">
                    <tbody>${checkRows || '<tr><td colspan="3" style="padding:12px;color:#94a3b8;font-size:13px">No score checks available.</td></tr>'}</tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Routes -->
    <section>
        ${secHeader('Routes', `${rs.total??0} total · ${rs.web??0} web · ${rs.api??0} API · ${rs.named_count??0} named`, '#10b981')}
        <div style="display:grid;grid-template-columns:280px 1fr;gap:24px;align-items:start">
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:20px">
                <p style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px">By Method</p>
                ${routeBarsSvg}
            </div>
            <div style="overflow-x:auto">
                ${tableWrap(['Method','URI','Handler','Middleware'], routeRows || '<tr><td colspan="4" style="padding:12px;color:#94a3b8">No routes.</td></tr>')}
            </div>
        </div>
    </section>

    <!-- Dependency Graph -->
    ${depSvg ? `<section>
        ${secHeader('Dependency Graph', `${depNodes.length} nodes · ${depEdges.length} edges`, '#6366f1')}
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden">
            <div style="padding:20px">${depSvg}</div>
        </div>
    </section>` : ''}

    <!-- Models -->
    <section>
        ${secHeader('Models', `${(d.models??[]).length} Eloquent models detected`, '#8b5cf6')}
        ${tableWrap(['Model','Table','Fillable Fields','Relationships'], modelRows || '<tr><td colspan="4" style="padding:12px;color:#94a3b8">No models.</td></tr>')}
    </section>

    <!-- Controllers -->
    <section>
        ${secHeader('Controllers', `${(d.controllers??[]).length} controllers`, '#3b82f6')}
        ${tableWrap(['Controller','Methods','Method List'], ctrlRows || '<tr><td colspan="3" style="padding:12px;color:#94a3b8">No controllers.</td></tr>')}
    </section>

</div>

<!-- ═══ FOOTER ═══════════════════════════════════════════════════════════ -->
<div style="background:#1e293b;color:#64748b;text-align:center;padding:24px;font-size:12px;font-family:system-ui,sans-serif;margin-top:20px">
    Generated by <strong style="color:#94a3b8">laradar</strong> · ${esc(d.generated_at ?? '')}
</div>

</body>
</html>`;
}

function _buildDepSvg(nodes, edges) {
    if (!nodes.length) return '';

    const layerOrder = ['controller','job','event','listener','service','repository','model'];
    const layerColors = {
        controller: { fill:'#FFF1F0', stroke:'#FF2D20', text:'#172B4D' },
        service:    { fill:'#E3FCEF', stroke:'#00875A', text:'#172B4D' },
        repository: { fill:'#FFFAE6', stroke:'#FF8B00', text:'#172B4D' },
        model:      { fill:'#F3F0FF', stroke:'#6554C0', text:'#172B4D' },
        job:        { fill:'#FFF4E5', stroke:'#FF5630', text:'#172B4D' },
        event:      { fill:'#FFF0FB', stroke:'#BF40BF', text:'#172B4D' },
        listener:   { fill:'#FEE4FA', stroke:'#DA62AC', text:'#172B4D' },
    };

    // Wrap each layer into rows so the SVG stays a reasonable width
    const MAX_PER_ROW = 6;
    const NW = 140, NH = 52, GAP_X = 16, GAP_Y = 68, ROW_GAP = 10, PAD = 28;

    const byLayer = {};
    nodes.forEach(n => {
        const l = n.layer ?? 'model';
        (byLayer[l] = byLayer[l] || []).push(n);
    });
    const lKeys = layerOrder.filter(l => byLayer[l]?.length);

    // Canvas width = widest possible row
    const maxColsAll = Math.max(...lKeys.map(l => Math.min(byLayer[l].length, MAX_PER_ROW)));
    const CW = maxColsAll * NW + (maxColsAll - 1) * GAP_X + PAD * 2;

    // Build positions with row-wrapping per layer
    const nameToPos = {};
    let curY = PAD;
    const bands = [];

    lKeys.forEach(l => {
        const layerNodes = byLayer[l];
        const rows = [];
        for (let i = 0; i < layerNodes.length; i += MAX_PER_ROW) {
            rows.push(layerNodes.slice(i, i + MAX_PER_ROW));
        }
        const bandY1 = curY;
        rows.forEach((row, ri) => {
            const rowW = row.length * NW + (row.length - 1) * GAP_X;
            let x = (CW - rowW) / 2;
            row.forEach(n => {
                nameToPos[n.name] = { x, y: curY, cx: x + NW / 2 };
                x += NW + GAP_X;
            });
            curY += NH + (ri < rows.length - 1 ? ROW_GAP : 0);
        });
        bands.push({ l, y1: bandY1, y2: curY });
        curY += GAP_Y;
    });
    const CH = curY - GAP_Y + PAD;

    // Layer band backgrounds + labels
    const bandsSvg = bands.map(b => {
        const c = layerColors[b.l] ?? { fill:'#F4F5F7', stroke:'#6B778C' };
        const label = b.l.charAt(0).toUpperCase() + b.l.slice(1) + 's';
        const bh = b.y2 - b.y1 + 16;
        return `<rect x="${PAD / 2}" y="${b.y1 - 8}" width="${CW - PAD}" height="${bh}" rx="8" fill="${c.stroke}" opacity="0.06"/>
                <text x="${PAD}" y="${b.y1 - 8 + bh / 2 + 4}" font-size="9" font-weight="700" fill="${c.stroke}" opacity="0.55" font-family="ui-monospace,monospace" letter-spacing="0.08em">${label.toUpperCase()}</text>`;
    }).join('');

    // Edges (skip back-edges for readability in a static render)
    const edgesSvg = edges.slice(0, 200).map(e => {
        const f = nameToPos[e.from], t = nameToPos[e.to];
        if (!f || !t) return '';
        const x1 = f.cx, y1 = f.y + NH, x2 = t.cx, y2 = t.y;
        if (y2 <= y1 + 4) return '';
        const cp = (y2 - y1) * 0.45;
        return `<path d="M${x1},${y1} C${x1},${y1+cp} ${x2},${y2-cp} ${x2},${y2}" fill="none" stroke="rgba(255,45,32,0.15)" stroke-width="1.4" marker-end="url(#dep-arr)"/>`;
    }).join('');

    // Nodes
    const nodesSvg = nodes.map(n => {
        const p = nameToPos[n.name]; if (!p) return '';
        const c  = layerColors[n.layer ?? ''] ?? { fill:'#F4F5F7', stroke:'#6B778C', text:'#172B4D' };
        const nm = n.name.length > 17 ? n.name.slice(0, 16) + '…' : n.name;
        const lb = (n.layer ?? '').toUpperCase();
        return `<g>
            <rect x="${p.x}" y="${p.y}" width="${NW}" height="${NH}" rx="8" fill="${c.fill}" stroke="${c.stroke}" stroke-width="1.5"/>
            <text x="${p.cx}" y="${p.y + 18}" text-anchor="middle" font-size="8.5" font-weight="700" fill="${c.stroke}" font-family="ui-monospace,monospace" letter-spacing="0.07em">${lb}</text>
            <text x="${p.cx}" y="${p.y + 36}" text-anchor="middle" font-size="11" font-weight="600" fill="${c.text}" font-family="ui-monospace,monospace">${nm}</text>
        </g>`;
    }).join('');

    // viewBox + max-width:100% + height:auto ensures the SVG scales to any container
    return `<svg viewBox="0 0 ${CW} ${CH}" width="${CW}" height="${CH}" style="display:block;max-width:100%;height:auto">
        <defs>
            <marker id="dep-arr" markerWidth="7" markerHeight="6" refX="6" refY="3" orient="auto">
                <polygon points="0 0,7 3,0 6" fill="#94a3b8"/>
            </marker>
        </defs>
        <rect width="${CW}" height="${CH}" fill="#F7F8F9" rx="12"/>
        ${bandsSvg}
        ${edgesSvg}
        ${nodesSvg}
    </svg>`;
}

// ── AI Insights ───────────────────────────────────────────────────────────────

const AI_ENDPOINT = '{{ route("laradar.ai.analyze") }}';
const AI_CSRF     = '{{ csrf_token() }}';

async function aiAnalyze() {
    document.getElementById('ai-loading').style.display = 'block';
    document.getElementById('ai-error').style.display = 'none';
    const trigEl = document.getElementById('ai-trigger');
    if (trigEl) { trigEl.style.opacity = '0.5'; trigEl.style.pointerEvents = 'none'; }

    try {
        const res = await fetch(AI_ENDPOINT, {
            method:  'POST',
            headers: {
                'Content-Type':  'application/json',
                'Accept':        'application/json',
                'X-CSRF-TOKEN':  AI_CSRF,
            },
        });

        const json = await res.json();

        if (!res.ok || json.error) {
            throw new Error(json.error || 'Server returned ' + res.status);
        }

        aiRenderResults(json);
    } catch (err) {
        document.getElementById('ai-error-msg').textContent = err.message;
        document.getElementById('ai-error').style.display = 'block';
    } finally {
        document.getElementById('ai-loading').style.display = 'none';
        if (trigEl) { trigEl.style.opacity = ''; trigEl.style.pointerEvents = ''; }
    }
}

function aiRenderResults(data) {
    // Summary
    document.getElementById('ai-summary').textContent = data.summary || 'No summary available.';

    // Score
    const score = data.score || 0;
    const scoreNumEl = document.getElementById('ai-score-num');
    scoreNumEl.textContent = score;
    scoreNumEl.style.color = '#FF2D20';
    scoreNumEl.style.fontSize = '26px';
    setTimeout(() => {
        const ring = document.getElementById('ai-score-ring');
        if (ring) ring.style.strokeDashoffset = 226 - (score / 100 * 226);
    }, 50);

    // SOLID
    const solidEl = document.getElementById('ai-solid');
    solidEl.innerHTML = '';
    const solidNames = { S: 'Single Resp.', O: 'Open/Closed', L: 'Liskov Sub.', I: 'Interface Seg.', D: 'Dep. Inversion' };
    Object.entries(data.solid_review || {}).forEach(([key, val], idx) => {
        const color = val.status === 'pass' ? 'green' : val.status === 'warn' ? 'amber' : 'red';
        const icon  = val.status === 'pass' ? '✔' : val.status === 'warn' ? '⚠' : '✘';
        solidEl.insertAdjacentHTML('beforeend', `
            <div class="flex flex-col items-center text-center p-3 rounded-xl bg-${color}-50 border border-${color}-200" style="animation:fadeUp .35s var(--ease) both;animation-delay:${idx * 70}ms;">
                <span class="text-xl font-bold text-${color}-600">${key}</span>
                <span class="text-xs font-medium text-${color}-700 mt-0.5">${solidNames[key] || ''}</span>
                <span class="text-lg mt-2">${icon}</span>
                <p class="text-xs text-${color}-600 mt-1 leading-tight">${_esc(val.note || '')}</p>
            </div>
        `);
    });

    // Problems
    const problemsEl = document.getElementById('ai-problems');
    problemsEl.innerHTML = '';
    if (!(data.problems || []).length) {
        problemsEl.innerHTML = '<p class="text-sm text-slate-400 italic">No problems detected.</p>';
    }
    (data.problems || []).forEach((p, idx) => {
        const sev   = p.severity || 'info';
        const color = sev === 'error' ? 'red' : sev === 'warning' ? 'amber' : 'blue';
        problemsEl.insertAdjacentHTML('beforeend', `
            <div class="flex gap-3 p-3 rounded-lg bg-${color}-50 border border-${color}-100" style="animation:fadeUp .3s var(--ease) both;animation-delay:${idx * 55}ms;">
                <div class="shrink-0 mt-0.5">
                    <span class="inline-block px-1.5 py-0.5 text-xs font-bold rounded uppercase bg-${color}-100 text-${color}-700">${sev}</span>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-800">${_esc(p.title || '')}</p>
                    ${p.location ? `<p class="text-xs text-slate-500 mt-0.5">📍 ${_esc(p.location)}</p>` : ''}
                    <p class="text-sm text-slate-600 mt-1">${_esc(p.description || '')}</p>
                </div>
            </div>
        `);
    });

    // Suggestions
    const suggEl = document.getElementById('ai-suggestions');
    suggEl.innerHTML = '';
    if (!(data.suggestions || []).length) {
        suggEl.innerHTML = '<p class="text-sm text-slate-400 italic">No suggestions.</p>';
    }
    (data.suggestions || []).forEach((s, idx) => {
        const pri   = s.priority || 'medium';
        const color = pri === 'high' ? 'red' : pri === 'medium' ? 'amber' : 'slate';
        suggEl.insertAdjacentHTML('beforeend', `
            <div class="flex gap-3 p-3 rounded-lg border border-slate-100 bg-slate-50" style="animation:fadeUp .3s var(--ease) both;animation-delay:${idx * 55}ms;">
                <span class="shrink-0 mt-0.5 inline-block px-1.5 py-0.5 h-fit text-xs font-bold rounded uppercase bg-${color}-100 text-${color}-700">${pri}</span>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-800">${_esc(s.title || '')}</p>
                    <p class="text-sm text-slate-600 mt-0.5">${_esc(s.description || '')}</p>
                    ${s.example ? `<code class="block mt-1.5 text-xs bg-slate-800 text-green-300 px-2 py-1 rounded">${_esc(s.example)}</code>` : ''}
                </div>
            </div>
        `);
    });

    // Laravel Best Practices
    const laravelEl = document.getElementById('ai-laravel-practices');
    laravelEl.innerHTML = '';
    (data.laravel_best_practices || []).forEach(p => {
        const icon  = p.status === 'pass' ? '✔' : p.status === 'warn' ? '⚠' : '✘';
        const color = p.status === 'pass' ? 'green' : p.status === 'warn' ? 'amber' : 'red';
        laravelEl.insertAdjacentHTML('beforeend', `
            <div class="flex items-start gap-2.5 py-1.5 border-b border-slate-100 last:border-0">
                <span class="text-${color}-600 font-bold mt-0.5 shrink-0">${icon}</span>
                <div class="min-w-0">
                    <span class="text-sm font-medium text-slate-700">${_esc(p.name || '')}</span>
                    ${p.note ? `<span class="text-xs text-slate-500 ml-2">${_esc(p.note)}</span>` : ''}
                </div>
            </div>
        `);
    });

    // Already followed best practices
    const bpEl = document.getElementById('ai-best-practices');
    bpEl.innerHTML = '';
    (data.best_practices || []).forEach(bp => {
        bpEl.insertAdjacentHTML('beforeend', `
            <li class="flex items-start gap-2 text-sm text-slate-600">
                <span class="text-green-500 shrink-0 mt-0.5">✔</span>
                <span>${_esc(bp)}</span>
            </li>
        `);
    });

    // Provider badge
    document.getElementById('ai-provider-badge').textContent =
        'Analyzed by ' + (data.provider || 'AI') + ' · ' + (data.model || '');

    const reanalyzeEl = document.getElementById('ai-reanalyze');
    if (reanalyzeEl) reanalyzeEl.style.display = 'flex';
}

// ── Doc preview helpers ───────────────────────────────────────────────────────

function _mdExcerpt(md, maxLen) {
    const plain = md
        .replace(/```[\s\S]*?```/g, '')
        .replace(/^#{1,6}\s+/gm, '')
        .replace(/\*\*([^*]+)\*\*/g, '$1')
        .replace(/\*([^*]+)\*/g, '$1')
        .replace(/^\|.+\|$/gm, '')
        .replace(/^[-*]\s+/gm, '• ')
        .replace(/`[^`]+`/g, '')
        .replace(/\n{2,}/g, ' ')
        .trim();
    return plain.slice(0, maxLen) + (plain.length > maxLen ? '…' : '');
}

function docsPreview(type) {
    const doc = _docsContent[type];
    if (!doc) return;
    document.getElementById('doc-modal-title').textContent = doc.filename;
    document.getElementById('doc-modal-body').innerHTML    = _mdToHtml(doc.content);
    document.getElementById('doc-modal-dl-md').onclick    = () => docsDownload(type);
    document.getElementById('doc-modal-dl-html').onclick  = () => docsDownloadHtml(type);

    const modal = document.getElementById('doc-modal');
    const box   = modal.querySelector('.doc-modal-box');
    modal.style.display  = 'flex';
    modal.style.opacity  = '0';
    box.style.animation  = 'none';
    void box.offsetWidth; // reflow
    modal.style.animation = 'modalBdIn .25s var(--ease) forwards';
    box.style.animation   = 'modalScaleIn .3s cubic-bezier(.34,1.56,.64,1) forwards';
    modal.style.opacity   = '';
    document.body.style.overflow = 'hidden';
}

function closeDocModal() {
    const modal = document.getElementById('doc-modal');
    const box   = modal.querySelector('.doc-modal-box');
    box.style.animation   = 'modalScaleOut .2s var(--ease) forwards';
    modal.style.animation = 'modalBdIn .2s var(--ease) reverse forwards';
    setTimeout(() => {
        modal.style.display   = 'none';
        modal.style.animation = '';
        box.style.animation   = '';
        document.body.style.overflow = '';
    }, 200);
}

function docsDownloadHtml(type) {
    const doc = _docsContent[type];
    if (!doc) return;
    const html = `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>${doc.filename.replace('.md','')}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,-apple-system,sans-serif;background:#f8fafc;color:#1e293b;padding:40px 20px;line-height:1.6}
.wrap{max-width:820px;margin:0 auto;background:#fff;border-radius:16px;padding:40px 48px;box-shadow:0 4px 24px rgba(0,0,0,.07);border:1px solid #e2e8f0}
h1{font-size:28px;font-weight:800;color:#0f172a;margin-bottom:20px;padding-bottom:12px;border-bottom:3px solid #e2e8f0}
h2{font-size:20px;font-weight:700;color:#0f172a;margin:32px 0 10px;padding-bottom:6px;border-bottom:2px solid #f1f5f9}
h3{font-size:16px;font-weight:700;color:#1e293b;margin:20px 0 8px}
p{font-size:14px;color:#334155;line-height:1.75;margin-bottom:12px}
ul,ol{padding-left:22px;margin:8px 0 14px}
li{font-size:14px;color:#334155;margin-bottom:5px;line-height:1.65}
strong{color:#0f172a;font-weight:700}
em{font-style:italic}
code{font-family:ui-monospace,monospace;font-size:12.5px;background:#f1f5f9;color:#0052cc;padding:2px 7px;border-radius:4px;border:1px solid #e2e8f0}
pre{background:#1e293b;color:#e2e8f0;border-radius:10px;padding:18px;overflow-x:auto;font-family:ui-monospace,monospace;font-size:13px;line-height:1.6;margin:14px 0}
hr{border:none;border-top:1px solid #e2e8f0;margin:24px 0}
table{width:100%;border-collapse:collapse;margin:14px 0}
th{background:#f8fafc;padding:10px 14px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#64748b;border-bottom:2px solid #e2e8f0}
td{padding:10px 14px;border-bottom:1px solid #f1f5f9;color:#334155;font-size:13.5px}
tr:last-child td{border-bottom:none}
.footer{margin-top:40px;padding-top:16px;border-top:1px solid #e2e8f0;font-size:11px;color:#94a3b8;text-align:center}
</style>
</head>
<body>
<div class="wrap">
${_mdToHtml(doc.content)}
<div class="footer">Generated by Laradar &middot; ${new Date().toLocaleDateString()}</div>
</div>
</body>
</html>`;
    _downloadBlob(html, doc.filename.replace(/\.md$/i, '.html'), 'text/html;charset=utf-8');
}

function _esc(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function _downloadBlob(content, filename, mime, isBlob = false) {
    const blob = isBlob ? content : new Blob([content], { type: mime });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    setTimeout(() => URL.revokeObjectURL(url), 2000);
}

// ── Card 3D tilt on hover ────────────────────────────────────────────────────
(function() {
    const MAX = 6; // max degrees

    function _tilt(card, e) {
        const r = card.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width  - 0.5;
        const y = (e.clientY - r.top)  / r.height - 0.5;
        card.style.transition = 'box-shadow .25s, border-color .25s';
        card.style.transform  = `perspective(800px) rotateX(${(-y * MAX * 2).toFixed(2)}deg) rotateY(${(x * MAX * 2).toFixed(2)}deg) translateY(-4px)`;
    }

    function _initTilt() {
        document.querySelectorAll('.mds-card, .ctrl-card, .pkg-card').forEach(card => {
            let raf = null;
            card.addEventListener('mousemove', e => {
                if (raf) return;
                raf = requestAnimationFrame(() => { raf = null; _tilt(card, e); });
            });
            card.addEventListener('mouseleave', () => {
                if (raf) { cancelAnimationFrame(raf); raf = null; }
                card.style.transition = 'transform 0.45s ease-out, box-shadow .25s, border-color .25s';
                card.style.transform  = '';
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', _initTilt);
    } else {
        _initTilt();
    }
})();

// Init sidebar indicator on load + keep in sync on resize
window.addEventListener('load',   () => _moveNavIndicator('{{ $section }}'));
window.addEventListener('resize', () => {
    const active = document.querySelector('.nav-item.nav-active');
    if (active) _moveNavIndicator(active.id.replace('nav-', ''));
});

// Score bar fill: animate mds-rel-bar segments when model card enters view
(function() {
    function _fillRelBar(card) {
        card.querySelectorAll('.mds-rel-seg[data-flex]').forEach(seg => {
            seg.style.flex = seg.dataset.flex;
        });
    }
    if (!window.IntersectionObserver) {
        document.querySelectorAll('.mds-card').forEach(_fillRelBar);
    } else {
        const io = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (!e.isIntersecting) return;
                io.unobserve(e.target);
                _fillRelBar(e.target);
            });
        }, { threshold: 0.15 });
        document.querySelectorAll('.mds-card').forEach(c => io.observe(c));
    }
})();

// ── Dead Code: count-up (no-op; numbers are server-rendered, kept for compatibility) ──
function _deadCountUp() { /* numbers rendered server-side */ }

// ── Dead Code: dual-axis filter state ────────────────────────────────────────
let _dcActiveType = 'all';
let _dcActiveSev  = 'all';

function dcTypeFilter(type, btn) {
    _dcActiveType = type;
    document.querySelectorAll('#dc-type-grid .dc-type-card').forEach(c => c.classList.remove('dc-type-active'));
    if (btn) btn.classList.add('dc-type-active');
    _dcApplyFilter();
}

function dcSevFilter(sev, btn) {
    _dcActiveSev = sev;
    document.querySelectorAll('#dc-filter-row .dc-sev-tab').forEach(t => t.classList.remove('dc-sev-tab--active'));
    if (btn) btn.classList.add('dc-sev-tab--active');
    _dcApplyFilter();
}

function _dcApplyFilter() {
    const items  = document.querySelectorAll('#dead-list .dc-item');
    const toShow = [];

    // Pass 1: hide immediately — no transitions, no per-item reflow
    items.forEach(item => {
        const typeOk = _dcActiveType === 'all' || item.dataset.type     === _dcActiveType;
        const sevOk  = _dcActiveSev  === 'all' || item.dataset.severity === _dcActiveSev;
        if (typeOk && sevOk) {
            toShow.push(item);
        } else {
            item.style.display = 'none';
            item.classList.remove('is-hiding');
        }
    });

    // Pass 2: make visible items ready, disable their animation
    toShow.forEach((item, i) => {
        item.style.display = '';
        item.classList.remove('is-hiding');
        item.style.setProperty('--di', String(i));
        item.style.animation = 'none';
    });

    // Single reflow for ALL visible items at once (instead of one per item)
    const list = document.getElementById('dead-list');
    if (list && toShow.length) void list.offsetHeight;

    // Re-enable animation on all at once
    toShow.forEach(item => { item.style.animation = ''; });
}

function dcCopyPath(path, btn) {
    navigator.clipboard.writeText(path).then(() => {
        btn.classList.add('copied');
        const orig = btn.innerHTML;
        btn.innerHTML = '<svg style="width:10px;height:10px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Copied!';
        setTimeout(() => { btn.classList.remove('copied'); btn.innerHTML = orig; }, 1800);
    }).catch(() => {});
}
</script>

@stack('scripts')

{{-- Shared format picker panel (position:fixed, viewport-aware) --}}
<div id="fmt-shared-panel" style="display:none;position:fixed;z-index:9999;background:var(--bg-elevated);border:1px solid #FF2D20;border-radius:10px;overflow:hidden;min-width:80px;box-shadow:0 4px 20px rgba(0,0,0,0.15);">
    <div onclick="selectFmtOption('md')" style="padding:10px 14px;font-size:12px;font-family:var(--font-mono);color:#FF2D20;cursor:pointer;" onmouseover="this.style.background='rgba(255,45,32,0.08)'" onmouseout="this.style.background=''">.md</div>
    <div onclick="selectFmtOption('html')" style="padding:10px 14px;font-size:12px;font-family:var(--font-mono);color:#FF2D20;cursor:pointer;" onmouseover="this.style.background='rgba(255,45,32,0.08)'" onmouseout="this.style.background=''">.html</div>
</div>

</body>
</html>
