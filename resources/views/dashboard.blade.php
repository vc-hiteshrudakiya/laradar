<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $data['project']['name'] }} — Architecture Dashboard</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<style>
/* ── Laravel Theme CSS Variables ── */
:root{
  --bg:#FFFFFF; --bg-elevated:#FFFFFF; --bg-sunken:#F9F6EF; --bg-hover:#EEF2FF;
  --border:#E5E7EB; --border-strong:#D1D5DB; --grid-line:transparent;
  --text:#1D1D1F; --text-dim:#374151; --text-faint:#6B7280;
  --cyan:#6366F1; --cyan-bright:#818CF8; --emerald:#16A34A; --amber:#D97706; --rose:#DC2626; --violet:#7C3AED; --sky:#2563EB;
  --red:#6366F1; --red-dim:rgba(99,102,241,0.08); --red-border:rgba(99,102,241,0.25);
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
.atlas-layout{display:grid;grid-template-columns:264px 1fr;height:100vh;overflow:hidden;}
.content{overflow-y:auto;}
.content::-webkit-scrollbar{width:6px}.content::-webkit-scrollbar-thumb{background:var(--border-strong);border-radius:3px}

/* ── Radar Animation ── */
.radar{position:relative;width:18px;height:18px;display:inline-block;flex:none;}
.radar__ring{position:absolute;inset:0;border:1px solid var(--cyan);border-radius:50%;animation:radarPulse 2.4s var(--ease) infinite;}
.radar__ring--delay{animation-delay:1.2s;}
.radar__dot{position:absolute;inset:0;margin:auto;width:4px;height:4px;border-radius:50%;background:var(--cyan);}
.radar__sweep{position:absolute;inset:0;border-radius:50%;background:conic-gradient(from 0deg,rgba(99,102,241,0.45),transparent 40%);animation:radarSpin 2.2s linear infinite;}
@keyframes radarPulse{0%{transform:scale(.5);opacity:.7;}100%{transform:scale(1.9);opacity:0;}}
@keyframes radarSpin{to{transform:rotate(360deg);}}
@keyframes spin{to{transform:rotate(360deg);}}
@keyframes pulse{0%,100%{opacity:1;}50%{opacity:.4;}}
@keyframes fadeUp{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:none;}}
.p-6>*{animation:fadeUp .35s var(--ease) both;}

/* ── Sidebar ── */
.sidebar{background:var(--bg-elevated);border-right:1px solid var(--border);padding:24px 16px;display:flex;flex-direction:column;position:sticky;top:0;height:100vh;z-index:90;transition:transform .35s var(--ease);overflow-y:auto;}
.sidebar::-webkit-scrollbar{width:4px}.sidebar::-webkit-scrollbar-thumb{background:var(--border-strong);border-radius:2px}
.sidebar__brand{display:flex;align-items:center;gap:10px;padding:6px 8px 22px;}
.sidebar__brand .mark{width:34px;height:34px;border-radius:8px;background:linear-gradient(155deg,#6366F1,#818CF8);border:1px solid rgba(99,102,241,0.25);display:flex;align-items:center;justify-content:center;flex:none;}
.sidebar__brand div{line-height:1;}
.sidebar__brand strong{font-size:16px;font-weight:800;letter-spacing:0.04em;color:var(--text);text-transform:uppercase;}
.sidebar nav{flex:1;overflow-y:auto;}
.nav-group{margin-bottom:20px;}
.nav-group__label{font-family:var(--font-mono);font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:var(--text-faint);padding:0 10px;margin-bottom:9px;display:block;}
.nav-item{display:flex;align-items:center;gap:11px;padding:9px 10px;border-radius:7px;margin-bottom:2px;color:var(--text-dim);font-size:13.5px;font-weight:600;position:relative;transition:background .2s,color .2s;cursor:pointer;border:none;background:none;width:100%;text-align:left;}
.nav-item svg{width:17px;height:17px;flex:none;stroke:currentColor;}
.nav-item:hover{background:rgba(99,102,241,0.04);color:var(--cyan);}
.nav-item.nav-active{background:rgba(99,102,241,0.06);color:var(--cyan);box-shadow:inset 3px 0 0 var(--cyan),inset 4px 0 12px rgba(99,102,241,0.06);}
#nav-indicator{position:fixed;left:0;width:3px;background:linear-gradient(180deg,transparent 0%,var(--cyan) 30%,var(--cyan) 70%,transparent 100%);border-radius:0 3px 3px 0;pointer-events:none;z-index:200;transition:top .38s cubic-bezier(.34,1.56,.64,1),height .22s var(--ease);box-shadow:1px 0 10px rgba(99,102,241,0.5),2px 0 4px rgba(99,102,241,0.2);}
#nav-indicator::after{content:'';position:absolute;left:3px;top:50%;transform:translateY(-50%);width:6px;height:6px;background:var(--cyan);border-radius:50%;box-shadow:0 0 8px rgba(99,102,241,0.8);}
.nav-badge{margin-left:auto;font-family:var(--font-mono);font-size:10px;background:rgba(0,0,0,0.05);color:var(--text-faint);padding:2px 7px;border-radius:20px;border:1px solid rgba(0,0,0,0.08);}
.sidebar__scan{border-top:1px solid var(--border);padding-top:16px;display:flex;align-items:center;gap:10px;margin-top:auto;}
.sidebar__scan div{line-height:1.3;}
.sidebar__scan strong{font-size:12.5px;display:block;color:var(--text);}
.sidebar__scan span.scan-label{font-size:11px;color:var(--text-faint);font-family:var(--font-mono);}

/* ── Topbar ── */
.topbar{position:sticky;top:0;z-index:60;background:rgba(255,255,255,0.92);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);display:flex;align-items:center;gap:16px;padding:16px 30px;box-shadow:0 1px 0 var(--border);}
.breadcrumb{font-family:var(--font-mono);font-size:12px;color:var(--text-faint);display:flex;align-items:center;gap:8px;}
.breadcrumb b{color:var(--text);font-weight:600;}
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
.back-btn{display:inline-flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:var(--cyan);background:rgba(99,102,241,0.06);border:1px solid rgba(99,102,241,0.22);border-radius:8px;padding:8px 14px;cursor:pointer;margin-bottom:24px;font-family:var(--font-sans);transition:background .18s,border-color .18s,transform .18s;}
.back-btn:hover{background:rgba(99,102,241,0.12);border-color:rgba(99,102,241,0.4);transform:translateX(-2px);}
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
.method-get{background:rgba(37,99,235,0.10)!important;color:var(--sky)!important;}
.method-post{background:rgba(0,135,90,0.10)!important;color:var(--emerald)!important;}
.method-put,.method-patch{background:rgba(101,84,192,0.10)!important;color:var(--violet)!important;}
.method-delete{background:rgba(222,53,11,0.10)!important;color:var(--rose)!important;}
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
tr.route-row:hover{background:rgba(99,102,241,0.04)!important;}
thead tr{background:var(--bg-sunken)!important;}

/* ── Buttons ── */
.atlas-btn{display:inline-flex;align-items:center;gap:8px;padding:8px 16px;border-radius:8px;font-family:var(--font-mono);font-size:12px;font-weight:600;border:1px solid var(--border);background:#FFFFFF;color:var(--text-dim);cursor:pointer;transition:border-color .2s,color .2s,background .2s,box-shadow .2s,transform .1s;}
.atlas-btn:hover{border-color:var(--border-strong);color:var(--text);box-shadow:var(--shadow);}
.atlas-btn:active{transform:scale(0.96);}
.atlas-btn--cyan{border-color:rgba(99,102,241,0.35);color:var(--cyan);background:rgba(99,102,241,0.06);}
.atlas-btn--cyan:hover{border-color:var(--cyan);background:rgba(99,102,241,0.10);}

/* ── Score bar ── */
.atlas-score-bar{height:4px;border-radius:2px;background:var(--bg-sunken);overflow:hidden;}
.atlas-score-fill{height:100%;border-radius:2px;background:linear-gradient(90deg,#6366F1,#818CF8);}

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
.bg-indigo-50{background:rgba(99,102,241,0.06)!important;}
.bg-indigo-100{background:rgba(99,102,241,0.10)!important;}
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
.text-indigo-600,.text-indigo-700{color:#6366F1!important;}
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
.border-indigo-200{border-color:rgba(99,102,241,0.25)!important;}
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
.hover\:bg-indigo-50:hover{background:rgba(99,102,241,0.08)!important;}
.hover\:bg-indigo-100:hover{background:rgba(99,102,241,0.12)!important;}

/* focused ring overrides (Tailwind focus:ring) */
.focus\:ring-indigo-300:focus{--tw-ring-color:rgba(99,102,241,0.35);}

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
.border-indigo-200{border-color:rgba(99,102,241,0.25)!important;}.border-blue-200{border-color:rgba(59,130,246,0.3)!important;}
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
.mds-side-stat:hover{background:rgba(99,102,241,.04);}
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
.mds-schema-tbl tr:hover td{background:rgba(99,102,241,.02);}
.mds-field-name{font-family:var(--font-mono);font-weight:600;font-size:13px;color:var(--text);}
.mds-fbadge{font-size:9px;font-weight:700;padding:2px 7px;border-radius:4px;font-family:var(--font-mono);border:1px solid;margin-right:4px;white-space:nowrap;display:inline-block;}
.mds-fbadge.fill{background:rgba(99,102,241,.08);color:var(--cyan);border-color:rgba(99,102,241,.2);}
.mds-fbadge.hide{background:rgba(222,53,11,.08);color:var(--rose);border-color:rgba(222,53,11,.2);}
.mds-cast-val{font-family:var(--font-mono);font-size:11px;color:var(--amber);}
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
.mds-usedby-card:hover{border-color:rgba(99,102,241,.25);box-shadow:var(--shadow-hover);}
.mds-flag-row{display:flex;flex-wrap:wrap;gap:8px;margin-top:14px;}
.mds-flag{font-size:11px;padding:5px 13px;border-radius:7px;font-family:var(--font-mono);font-weight:600;border:1px solid;}

/* Architecture Explorer */
.ov-panel{background:var(--bg-elevated);border:1px solid var(--border);border-radius:16px;position:relative;overflow:hidden;box-shadow:var(--shadow);}
.ov-panel-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:18px 22px;border-bottom:1px solid var(--border);}
.ov-panel-head h3{font-size:14.5px;font-weight:700;font-family:var(--font-sans);margin:0;color:var(--text);}
.ov-panel-head p{font-size:11.5px;color:var(--text-faint);margin:3px 0 0;}
.ov-panel-body{padding:20px 22px;}
.ov-diag-shell{overflow-x:auto;border-radius:10px;background:var(--bg-sunken);border:1px solid var(--border);}
.ov-arch-node rect{transition:filter .3s;}
.ov-arch-node:hover rect{filter:drop-shadow(0 0 10px rgba(99,102,241,.35));}
.ov-btn-icon{width:34px;height:34px;border-radius:9px;display:inline-flex;align-items:center;justify-content:center;background:var(--bg-sunken);border:1px solid var(--border);color:var(--text-dim);cursor:pointer;transition:all .25s;flex-shrink:0;font-size:16px;line-height:1;}
.ov-btn-icon:hover{background:rgba(99,102,241,.06);border-color:rgba(99,102,241,.25);color:var(--cyan);}
.ov-reveal{opacity:0;transform:translateY(14px);transition:opacity .55s var(--ease),transform .55s var(--ease);}
.ov-reveal.ov-in{opacity:1;transform:none;}
@keyframes secIn{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:none}}
@keyframes secOut{from{opacity:1;transform:none}to{opacity:0;transform:translateY(-10px) scale(0.98)}}
.sec-fade{animation:secIn .30s cubic-bezier(.22,1,.36,1) both;}
.sec-out{animation:secOut .18s ease-in both;pointer-events:none;}

@property --rg-angle{syntax:'<angle>';inherits:false;initial-value:0deg;}
@keyframes borderSpin{to{--rg-angle:360deg;}}
.score-spin-border{border:2px solid transparent;background:var(--bg-hover) padding-box,conic-gradient(from var(--rg-angle),#6366F1,#818CF8,#C7D2FE,#818CF8,#6366F1) border-box;animation:borderSpin 3s linear infinite;}
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
@keyframes aiPulseGlow{0%,100%{box-shadow:0 0 0 0 rgba(99,102,241,0.45);}50%{box-shadow:0 0 0 8px rgba(99,102,241,0);}}
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
.ai-analyze-btn{display:inline-flex;align-items:center;gap:10px;padding:12px 24px;background:var(--cyan);border:none;border-radius:12px;color:#fff;font-weight:700;font-size:14px;cursor:pointer;font-family:var(--font-mono);transition:background .2s,transform .15s,box-shadow .2s;box-shadow:0 4px 16px rgba(99,102,241,0.35);}
.ai-analyze-btn:hover{background:#DC1F13;transform:translateY(-1px);box-shadow:0 6px 24px rgba(99,102,241,0.5);}
.ai-analyze-btn:disabled{background:var(--border-strong);box-shadow:none;cursor:not-allowed;transform:none;color:var(--text-faint);}
.ai-analyze-btn:not(:disabled):hover{animation:aiPulseGlow 1.5s ease infinite;}
.ai-analyze-btn:not(:disabled):active{transform:translateY(1px) scale(0.97)!important;}
.chat-send-btn{display:flex;align-items:center;justify-content:center;width:36px;height:36px;background:var(--cyan);border:none;border-radius:9px;color:#fff;cursor:pointer;transition:background .2s,transform .15s,box-shadow .2s;box-shadow:0 2px 8px rgba(99,102,241,0.3);flex:none;}
.chat-send-btn:hover{background:#DC1F13;transform:scale(1.08);box-shadow:0 4px 14px rgba(99,102,241,0.5);}
.chat-send-btn:disabled{background:var(--border);box-shadow:none;cursor:not-allowed;transform:none;}
.chat-suggestion-chip{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:20px;font-size:12px;font-weight:500;color:var(--text-dim);cursor:pointer;transition:background .15s,border-color .15s,color .15s;font-family:var(--font-sans);}
.chat-suggestion-chip:hover{background:rgba(99,102,241,.06);border-color:rgba(99,102,241,.3);color:var(--cyan);}
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
.dc-type-card.dc-type-active{border-color:var(--cyan);background:rgba(99,102,241,0.06);box-shadow:0 0 0 2px rgba(99,102,241,0.15),var(--shadow);}
.dc-type-card.dc-type-active .tc-label{color:var(--cyan);}
.dc-type-card.dc-type-active .tc-count{color:var(--cyan);}
.dc-type-card.dc-type-zero{opacity:.5;}
.dc-type-card.dc-type-zero:hover{opacity:1;}
/* Severity filter row */
.dc-filter-row{display:flex;align-items:center;gap:8px;margin-bottom:20px;flex-wrap:wrap;}
.dc-sev-tab{padding:7px 16px;border-radius:20px;font-size:12.5px;font-weight:600;border:1px solid var(--border);background:var(--bg-elevated);color:var(--text-dim);cursor:pointer;transition:background .15s,border-color .15s,color .15s,transform .1s;display:inline-flex;align-items:center;gap:5px;}
.dc-sev-tab:hover{background:var(--bg-hover);border-color:var(--border-strong);}
.dc-sev-tab:active{animation:tabPop .2s var(--ease);}
.dc-sev-tab--active{background:rgba(99,102,241,0.08);border-color:rgba(99,102,241,0.35);color:var(--cyan);box-shadow:0 2px 8px rgba(99,102,241,0.12);}
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
.dc-copy-btn:hover{background:rgba(99,102,241,0.06);border-color:rgba(99,102,241,0.25);color:var(--cyan);}
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
@keyframes backBtnPulse{0%{box-shadow:0 0 0 0 rgba(99,102,241,0.4);}60%{box-shadow:0 0 0 7px rgba(99,102,241,0);}100%{box-shadow:0 0 0 0 rgba(99,102,241,0);}}
.topbar-back-btn{display:none;align-items:center;gap:8px;background:rgba(99,102,241,0.08);border:1px solid rgba(99,102,241,0.25);border-radius:9px;padding:6px 13px 6px 10px;cursor:pointer;font-family:var(--font-mono);font-size:12px;font-weight:700;color:#6366F1;transition:background .15s,border-color .15s,transform .15s;}
.topbar-back-btn:hover{background:rgba(99,102,241,0.14);border-color:rgba(99,102,241,0.45);transform:translateX(-2px);}
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
.doc-r tr:hover td{background:rgba(99,102,241,.02);}
/* ── Responsive Hamburger ── */
#menu-toggle{display:none;align-items:center;justify-content:center;width:36px;height:36px;border-radius:9px;border:1px solid var(--border);background:var(--bg-hover);cursor:pointer;flex:none;transition:background .15s;}
#menu-toggle:hover{background:var(--border);}
#sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(15,23,42,.4);z-index:150;backdrop-filter:blur(2px);}
#sidebar-overlay.is-open{display:block;}

/* ── Responsive Breakpoints ── */
@media(max-width:1280px){
    .atlas-layout{grid-template-columns:220px 1fr;}
    .mds-det-wrap{grid-template-columns:220px 1fr;}
}
@media(max-width:1060px){
    .atlas-layout{grid-template-columns:178px 1fr;}
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
@endphp

{{-- ══ SIDEBAR ══ --}}
<aside class="sidebar" id="sidebar">
    <div id="nav-indicator"></div>
    <div class="sidebar__brand">
        <div class="mark">
            <span class="radar"><span class="radar__ring"></span><span class="radar__ring radar__ring--delay"></span><span class="radar__sweep"></span><span class="radar__dot"></span></span>
        </div>
        <div><strong>Laradar</strong></div>
    </div>

    @if(!empty($score))
    <div class="score-spin-border" style="margin-bottom:20px;padding:14px 10px;border-radius:10px;">
        <span style="font-family:var(--font-mono);font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:var(--text-faint);display:block;margin-bottom:8px;">Score</span>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <span style="font-family:var(--font-mono);font-size:24px;font-weight:700;color:var(--text);">{{ $score['score'] }}<span style="font-size:13px;color:var(--text-faint);">/{{ $score['max'] }}</span></span>
            <span class="px-2 py-0.5 rounded text-xs font-bold {{ $gradeClass }}" style="font-family:var(--font-mono);">{{ $grade }}</span>
        </div>
        <div class="atlas-score-bar"><div class="atlas-score-fill" id="sidebar-score-bar" data-score-w="{{ round(($score['score']/max(1,$score['max']))*100) }}" style="width:0;"></div></div>
    </div>
    @endif

    <nav>
        <div class="nav-group">
            <button onclick="navigate('overview')" id="nav-overview" class="nav-item nav-active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Overview
            </button>
            <button onclick="navigate('ai')" id="nav-ai" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                AI Insights
                @if(config('laradar.ai.enabled', false))
                <span style="margin-left:auto;width:8px;height:8px;border-radius:50%;background:var(--emerald);box-shadow:0 0 0 3px rgba(52,211,153,0.18);flex:none;"></span>
                @endif
            </button>
            <button onclick="navigate('chat')" id="nav-chat" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                AI Chat
            </button>
            <button onclick="navigate('aidocs')" id="nav-aidocs" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                AI Docs
            </button>
            @if(($summary['modules']??0)>0)
            <button onclick="navigate('modules')" id="nav-modules" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Modules
                <span class="nav-badge">{{ $summary['modules'] }}</span>
            </button>
            @endif
        </div>

        <div class="nav-group">
            <span class="nav-group__label">Core</span>
            <button onclick="navigate('models')" id="nav-models" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                Models
                @if(($summary['models']??0)>0)<span class="nav-badge">{{ $summary['models'] }}</span>@endif
            </button>
            <button onclick="navigate('modelmap')" id="nav-modelmap" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Relation Graph
            </button>
            <button onclick="navigate('controllers')" id="nav-controllers" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                Controllers
                @if(($summary['controllers']??0)>0)<span class="nav-badge">{{ $summary['controllers'] }}</span>@endif
            </button>
            <button onclick="navigate('routes')" id="nav-routes" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Routes
                @if(($rs['total']??0)>0)<span class="nav-badge">{{ $rs['total'] }}</span>@endif
            </button>
            @php $apiDocCount = count($data['api_docs'] ?? []); @endphp
            <button onclick="navigate('apidocs')" id="nav-apidocs" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                API Docs
                @if($apiDocCount > 0)<span class="nav-badge">{{ $apiDocCount }}</span>@endif
            </button>
        </div>

        <div class="nav-group">
            <span class="nav-group__label">Components</span>
            <button onclick="navigate('services')" id="nav-services" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Services
                @if(($summary['services']??0)>0)<span class="nav-badge">{{ $summary['services'] }}</span>@endif
            </button>
            <button onclick="navigate('repositories')" id="nav-repositories" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                Repositories
                @if(($summary['repositories']??0)>0)<span class="nav-badge">{{ $summary['repositories'] }}</span>@endif
            </button>
            <button onclick="navigate('observers')" id="nav-observers" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Observers
                @if(($summary['observers']??0)>0)<span class="nav-badge">{{ $summary['observers'] }}</span>@endif
            </button>
            <button onclick="navigate('policies')" id="nav-policies" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Policies
                @if(($summary['policies']??0)>0)<span class="nav-badge">{{ $summary['policies'] }}</span>@endif
            </button>
            <button onclick="navigate('packages')" id="nav-packages" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                Packages
                @if(($summary['packages']??0)>0)<span class="nav-badge">{{ $summary['packages'] }}</span>@endif
            </button>
        </div>

        <div class="nav-group">
            <span class="nav-group__label">Architecture</span>
            <button onclick="navigate('dependencies')" id="nav-dependencies" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                Dependencies
                @if(!empty($data['dependencies']['edges']))<span class="nav-badge">{{ count($data['dependencies']['edges']) }}</span>@endif
            </button>
            @php $deadTotal = $data['dead_code']['summary']['total'] ?? 0; @endphp
            <button onclick="navigate('deadcode')" id="nav-deadcode" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                Dead Code
                @if($deadTotal > 0)<span class="nav-badge" style="background:rgba(239,68,68,0.15);color:#EF4444;">{{ $deadTotal }}</span>@endif
            </button>
            <button onclick="navigate('export')" id="nav-export" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export
            </button>
        </div>

    </nav>

    <div class="sidebar__scan">
        <span class="radar"><span class="radar__ring"></span><span class="radar__ring radar__ring--delay"></span><span class="radar__sweep"></span><span class="radar__dot"></span></span>
        <div>
            <strong>{{ $data['project']['name'] }}</strong>
            <span class="scan-label">v{{ $data['package_version'] }} · PHP {{ $data['php_version'] }} · {{ \Carbon\Carbon::parse($data['generated_at'])->format('M d, H:i') }}</span>
        </div>
    </div>
</aside>

{{-- ══ MAIN ══ --}}
<main class="content" style="display:flex;flex-direction:column;">

{{-- ══ TOPBAR ══ --}}
<div id="sidebar-overlay" onclick="toggleSidebar()"></div>
<header class="topbar">
    <button id="menu-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
    <div class="breadcrumb">
        <b id="topbar-section">Overview</b>
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

{{-- Overview --}}
<section id="sec-overview" class="p-6">

    @php
    $kpiIcons = [
        'Models'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>',
        'Controllers'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>',
        'Routes'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
        'Jobs'         => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>',
        'Services'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
        'Repositories' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>',
        'Observers'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>',
        'Policies'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
        'Modules'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
        'Dep. Edges'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/>',
        'Middleware'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>',
    ];
    $kpiColors = [
        'Models'       => ['color'=>'var(--violet)', 'bg'=>'rgba(167,139,250,0.14)'],
        'Controllers'  => ['color'=>'var(--sky)',    'bg'=>'rgba(96,165,250,0.14)'],
        'Routes'       => ['color'=>'var(--emerald)','bg'=>'rgba(52,211,153,0.14)'],
        'Jobs'         => ['color'=>'var(--amber)',  'bg'=>'rgba(251,191,36,0.14)'],
        'Services'     => ['color'=>'var(--violet)', 'bg'=>'rgba(167,139,250,0.14)'],
        'Repositories' => ['color'=>'var(--cyan)',   'bg'=>'rgba(99,102,241,0.10)'],
        'Observers'    => ['color'=>'var(--amber)',  'bg'=>'rgba(251,191,36,0.14)'],
        'Policies'     => ['color'=>'var(--sky)',    'bg'=>'rgba(96,165,250,0.14)'],
        'Modules'      => ['color'=>'var(--cyan)',   'bg'=>'rgba(99,102,241,0.10)'],
        'Dep. Edges'   => ['color'=>'var(--text-dim)','bg'=>'rgba(91,103,133,0.18)'],
        'Middleware'   => ['color'=>'var(--emerald)', 'bg'=>'rgba(52,211,153,0.14)'],
    ];
    $stats = [
        ['Models',       $summary['models']??0],
        ['Controllers',  $summary['controllers']??0],
        ['Routes',       $rs['total']??0],
        ['Services',     $summary['services']??0],
        ['Repositories', $summary['repositories']??0],
        ['Observers',    $summary['observers']??0],
        ['Policies',     $summary['policies']??0],
        ['Modules',      $summary['modules']??0],
        ['Dep. Edges',   count($data['dependencies']['edges']??[])],
        ['Middleware',   count($rs['middleware_usage']??[])],
    ];
    $kpiNav = [
        'Models'       => 'models',
        'Controllers'  => 'controllers',
        'Routes'       => 'routes',
        'Services'     => 'services',
        'Repositories' => 'repositories',
        'Observers'    => 'observers',
        'Policies'     => 'policies',
        'Modules'      => 'modules',
        'Dep. Edges'   => 'dependencies',
    ];
    @endphp

    <div class="kpi-grid" style="margin-bottom:28px;grid-template-columns:repeat(4,1fr);">
        @foreach($stats as [$label,$count])
        @php $kc = $kpiColors[$label] ?? ['color'=>'var(--text-dim)','bg'=>'rgba(91,103,133,0.18)']; $ki = $kpiIcons[$label] ?? ''; $kn = $kpiNav[$label] ?? ''; @endphp
        <div class="kpi-card ov-reveal" data-ov-reveal style="transition-delay:{{ $loop->index * 45 }}ms;{{ $kn ? 'cursor:pointer;' : '' }}" @if($kn) onclick="navigate('{{ $kn }}')" @endif>
            <div class="kpi-card__icon" style="background:{{ $kc['bg'] }};color:{{ $kc['color'] }};">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">{!! $ki !!}</svg>
            </div>
            <span class="kpi-card__label">{{ $label }}</span>
            <span class="kpi-card__num" data-count="{{ $count }}" style="color:{{ $kc['color'] }};">{{ $count }}</span>
        </div>
        @endforeach
    </div>

    {{-- Architecture Explorer --}}
    <div class="ov-panel ov-reveal" data-ov-reveal style="margin-bottom:24px;">
        <div class="ov-panel-head">
            <div>
                <h3>Architecture Explorer</h3>
                <p>Request flow &mdash; from HTTP kernel to your database tables</p>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <span id="ovArchDetail" style="font-size:11.5px;color:var(--text-faint);">Hover a node to trace it</span>
                <button class="ov-btn-icon" id="ovZoomIn" title="Zoom in">+</button>
                <button class="ov-btn-icon" id="ovZoomOut" title="Zoom out">&minus;</button>
            </div>
        </div>
        <div class="ov-panel-body" style="padding:18px 20px;">
            <div class="ov-diag-shell">
                <div id="ovArchDiagram" style="min-width:960px;"></div>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;">
        {{-- Route breakdown --}}
        <div class="atlas-card ov-reveal" data-ov-reveal style="transition-delay:0ms;">
            <div class="atlas-card__head"><h3>Route Breakdown</h3></div>
            <div style="display:flex;flex-direction:column;gap:10px;font-size:13px;">
                <div style="display:flex;justify-content:space-between;align-items:center;"><span style="color:var(--text-faint);">Total</span><span style="font-family:var(--font-mono);font-weight:600;color:var(--text);">{{ $rs['total']??0 }}</span></div>
                @foreach($rs['by_group']??[] as $group => $cnt)
                <div style="display:flex;justify-content:space-between;align-items:center;"><span style="color:var(--text-faint);">{{ ucfirst($group) }}</span><span style="font-family:var(--font-mono);font-weight:600;color:var(--text);">{{ $cnt }}</span></div>
                @endforeach
                <div style="display:flex;justify-content:space-between;align-items:center;"><span style="color:var(--text-faint);">Named</span><span style="font-family:var(--font-mono);font-weight:600;color:var(--text);">{{ $rs['named_count']??0 }} / {{ $rs['total']??0 }}</span></div>
                @if(!empty($rs['api_versions']))
                <div style="display:flex;justify-content:space-between;align-items:center;"><span style="color:var(--text-faint);">API Versions</span><span style="font-family:var(--font-mono);font-weight:600;color:var(--text);">{{ implode(', ', array_keys($rs['api_versions'])) }}</span></div>
                @endif
            </div>
            @if(!empty($rs['by_method']))
            <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--border);">
                <p style="font-family:var(--font-mono);font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--text-faint);margin-bottom:10px;">By Method</p>
                <div style="display:flex;flex-wrap:wrap;gap:6px;">
                    @foreach($rs['by_method'] as $method => $cnt)
                    <span class="text-xs px-2 py-0.5 rounded font-semibold method-{{ strtolower($method) }}" style="font-family:var(--font-mono);">{{ strtoupper($method) }} {{ $cnt }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Performance --}}
        <div class="atlas-card ov-reveal" data-ov-reveal style="transition-delay:80ms;">
            <div class="atlas-card__head"><h3>Performance</h3></div>
            @php $perf = $data['performance']??[]; @endphp
            <div style="display:flex;flex-direction:column;gap:18px;">
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:8px;">
                        <span style="color:var(--text-faint);">Scan Time</span>
                        <span style="font-family:var(--font-mono);color:var(--cyan);">{{ $perf['execution_time_ms']??0 }} ms</span>
                    </div>
                    <div class="atlas-score-bar"><div class="atlas-score-fill" data-score-w="{{ min(100,($perf['execution_time_ms']??0)/50) }}" style="width:0;background:linear-gradient(90deg,#6366F1,#818CF8);"></div></div>
                </div>
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:8px;">
                        <span style="color:var(--text-faint);">Memory</span>
                        <span style="font-family:var(--font-mono);color:var(--emerald);">{{ $perf['memory_usage_mb']??0 }} MB</span>
                    </div>
                    <div class="atlas-score-bar"><div class="atlas-score-fill" data-score-w="{{ min(100,($perf['memory_usage_mb']??0)/1.28) }}" style="width:0;background:var(--emerald);"></div></div>
                </div>
            </div>
        </div>

        {{-- Score checks --}}
        @if(!empty($score['checks']))
        <div class="atlas-card ov-reveal" data-ov-reveal style="transition-delay:160ms;">
            <div class="atlas-card__head"><h3>Score Checks</h3></div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($score['checks'] as $check)
                @php
                    $icColor = match($check['status']??'fail'){'pass'=>'var(--emerald)','warn'=>'var(--amber)',default=>'var(--rose)'};
                    $icSymbol = match($check['status']??'fail'){'pass'=>'✔','warn'=>'⚠',default=>'✘'};
                @endphp
                <div class="hc-row" style="--hc-i:{{ $loop->index }};display:flex;align-items:flex-start;gap:10px;">
                    <span style="font-weight:700;font-size:13px;color:{{ $icColor }};margin-top:1px;flex:none;">{{ $icSymbol }}</span>
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
</section>

{{-- Models --}}
<section id="sec-models" class="p-6" style="display:none">
    @php
    $mTotalRels    = collect($data['models'])->sum(fn($m) => count($m['relationships']??[]));
    $mWithObs      = collect($data['models'])->filter(fn($m) => !empty($m['observer']))->count();
    $mSoftDel      = collect($data['models'])->filter(fn($m) => collect($m['traits']??[])->contains(fn($t)=>str_contains($t,'SoftDeletes')))->count();
    $mPalette = [
        ['color'=>'var(--cyan)',    'bg'=>'rgba(99,102,241,.15)',   'border'=>'rgba(99,102,241,.3)',   'hex'=>'#6366F1'],
        ['color'=>'var(--violet)',  'bg'=>'rgba(167,139,250,.15)', 'border'=>'rgba(167,139,250,.3)', 'hex'=>'#A78BFA'],
        ['color'=>'var(--emerald)', 'bg'=>'rgba(52,211,153,.15)',  'border'=>'rgba(52,211,153,.3)',  'hex'=>'#34D399'],
        ['color'=>'var(--amber)',   'bg'=>'rgba(251,191,36,.15)',  'border'=>'rgba(251,191,36,.3)',  'hex'=>'#FBBF24'],
        ['color'=>'var(--rose)',    'bg'=>'rgba(248,113,113,.15)', 'border'=>'rgba(248,113,113,.3)', 'hex'=>'#F87171'],
        ['color'=>'var(--sky)',     'bg'=>'rgba(96,165,250,.15)',  'border'=>'rgba(96,165,250,.3)',  'hex'=>'#60A5FA'],
    ];
    $mRelColors = ['hasMany'=>'#34D399','hasOne'=>'#6366F1','belongsTo'=>'#60A5FA','belongsToMany'=>'#A78BFA','morphMany'=>'#F87171','morphTo'=>'#F87171','morphOne'=>'#F87171','hasManyThrough'=>'#FBBF24'];
    @endphp

    <div id="models-list">
        {{-- Top stats --}}
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--violet);">{{ count($data['models']) }}</span>
                <span class="mds-top-stat-lbl">Total Models</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--cyan);">{{ $mTotalRels }}</span>
                <span class="mds-top-stat-lbl">Relationships</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--amber);">{{ $mWithObs }}</span>
                <span class="mds-top-stat-lbl">With Observer</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--rose);">{{ $mSoftDel }}</span>
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
                <div class="mds-card" onclick="showDetail('models',{{ $i }})" data-name="{{ strtolower($model['name']) }}" style="--card-hover-border:{{ $mp['border'] }};">
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
                                <div class="mds-card-stat-num" style="color:var(--cyan);">{{ $mFillCnt }}</div>
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
            <div class="mds-list-row" onclick="showDetail('models',{{ $i }})" data-name="{{ strtolower($model['name']) }}">
                <div class="mds-list-av" style="background:{{ $mp['bg'] }};color:{{ $mp['color'] }};border-color:{{ $mp['border'] }};">{{ substr($model['name'],0,1) }}</div>
                <div>
                    <span style="font-weight:700;font-size:13.5px;color:var(--text);">{{ $model['name'] }}</span>
                    @if(!empty($model['observer']))<span style="font-size:9px;padding:2px 6px;border-radius:4px;background:rgba(251,191,36,.12);color:var(--amber);border:1px solid rgba(251,191,36,.2);font-family:var(--font-mono);margin-left:8px;">obs</span>@endif
                </div>
                <span style="font-family:var(--font-mono);font-size:11.5px;color:var(--text-faint);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $model['table'] }}</span>
                <span style="font-family:var(--font-mono);font-size:14px;font-weight:800;" style="color:{{ $mp['color'] }}">{{ count($model['relationships']??[]) }}</span>
                <span style="font-family:var(--font-mono);font-size:14px;font-weight:800;color:var(--cyan);">{{ count($model['fillable']??[]) }}</span>
                <span style="font-family:var(--font-mono);font-size:14px;font-weight:800;color:var(--text-dim);">{{ count($model['traits']??[]) }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Detail --}}
    <div id="models-detail" style="display:none">
        <div id="models-detail-content"></div>
    </div>
</section>

{{-- Controllers --}}
<section id="sec-controllers" class="p-6" style="display:none">
    <div id="controllers-list">

        @php
            $ctrlTotal      = count($data['controllers']);
            $ctrlTotalMeth  = array_sum(array_column($data['controllers'], 'method_count'));
            $ctrlWithMw     = count(array_filter($data['controllers'], fn($c) => !empty($c['middleware'])));
            $ctrlResource   = count(array_filter($data['controllers'], fn($c) => !empty($c['is_resource'])));
            $ctrlMaxMethods = max(array_column($data['controllers'], 'method_count') ?: [1]);
        @endphp
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--amber);">{{ $ctrlTotal }}</span>
                <span class="mds-top-stat-lbl">Controllers</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--cyan);">{{ $ctrlTotalMeth }}</span>
                <span class="mds-top-stat-lbl">Total Methods</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--emerald);">{{ $ctrlResource }}</span>
                <span class="mds-top-stat-lbl">Resource</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--rose);">{{ $ctrlWithMw }}</span>
                <span class="mds-top-stat-lbl">With Middleware</span>
            </div>
        </div>

        <div class="mds-toolbar">
            <input id="controllers-search" oninput="filterGrid('controllers')" type="search" placeholder="Search controllers…" style="border:1px solid var(--border);border-radius:8px;padding:8px 14px;font-size:13px;flex:1;max-width:240px;font-family:var(--font-mono);">
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ $ctrlTotal }} controllers</span>
        </div>

        <div id="controllers-grid" style="background:var(--bg-elevated);border-radius:12px;border:1px solid var(--border);overflow:hidden;">
            <div class="mds-list-head" style="grid-template-columns:36px 1fr 80px 70px 70px 90px;">
                <span></span><span>Controller</span><span>Methods</span><span>Routes</span><span>Deps</span><span>Complexity</span>
            </div>
            @foreach($data['controllers'] as $i => $ctrl)
            @php
                $ctrlRouteCount = count(array_filter($data['routes'] ?? [], fn($r) => class_basename($r['controller']['class'] ?? '') === $ctrl['name']));
                $ctrlComplexity = $ctrlMaxMethods > 0 ? round(($ctrl['method_count']??0) / $ctrlMaxMethods * 100) : 0;
            @endphp
            <div class="mds-list-row" style="grid-template-columns:36px 1fr 80px 70px 70px 90px;" onclick="showDetail('controllers',{{$i}})" data-name="{{ strtolower($ctrl['name']) }}">
                <div class="mds-list-av" style="background:rgba(255,139,0,0.10);color:var(--amber);border-color:rgba(255,139,0,0.25);">{{ substr($ctrl['name'],0,1) }}</div>
                <div style="min-width:0;">
                    <div style="font-weight:700;font-size:13.5px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $ctrl['name'] }}
                        @if(!empty($ctrl['is_resource']))<span style="font-family:var(--font-mono);font-size:9px;padding:2px 6px;border-radius:10px;background:rgba(52,211,153,0.12);color:var(--emerald);border:1px solid rgba(52,211,153,0.25);margin-left:6px;">Resource</span>@endif
                    </div>
                    <div style="font-family:var(--font-mono);font-size:10.5px;color:var(--text-faint);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $ctrl['namespace'] }}</div>
                </div>
                <div style="font-family:var(--font-mono);font-size:13px;font-weight:700;color:var(--text);text-align:center;">{{ $ctrl['method_count']??0 }}</div>
                <div style="font-family:var(--font-mono);font-size:13px;font-weight:700;color:var(--sky);text-align:center;">{{ $ctrlRouteCount }}</div>
                <div style="font-family:var(--font-mono);font-size:13px;font-weight:700;color:var(--violet);text-align:center;">{{ count($ctrl['dependencies']??[]) }}</div>
                <div style="padding-right:8px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                        <span style="font-family:var(--font-mono);font-size:10px;color:var(--amber);font-weight:700;">{{ $ctrlComplexity }}%</span>
                    </div>
                    <div class="ctrl-complexity-track">
                        <div class="ctrl-complexity-fill" style="width:0;" data-target="{{ $ctrlComplexity }}"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div id="controllers-detail" style="display:none">
        <div id="controllers-detail-content"></div>
    </div>
</section>

{{-- Model Relationships Map --}}
<section id="sec-modelmap" class="p-6" style="display:none">

    @php
    $mmErPairs = [];
    foreach ($data['models'] as $mmModel) {
        foreach ($mmModel['relationships'] ?? [] as $mmRel) {
            $mmTo = class_basename($mmRel['related'] ?? '');
            if (!$mmTo || $mmTo === $mmModel['name']) continue;
            $mmPair = $mmModel['name'] . ':' . $mmTo;
            $mmRev  = $mmTo . ':' . $mmModel['name'];
            if (!isset($mmErPairs[$mmPair]) && !isset($mmErPairs[$mmRev])) {
                $mmType = $mmRel['type'];
                if (str_contains($mmType, 'BelongsToMany') || str_contains($mmType, 'MorphToMany')) { $mmL = '}o'; $mmR = 'o{'; }
                elseif (str_contains($mmType, 'BelongsTo') || str_contains($mmType, 'MorphTo'))     { $mmL = '}o'; $mmR = '||'; }
                elseif (str_contains($mmType, 'HasOne') || str_contains($mmType, 'MorphOne'))        { $mmL = '||'; $mmR = 'o|'; }
                else                                                                                  { $mmL = '||'; $mmR = 'o{'; }
                $mmErPairs[$mmPair] = "    {$mmModel['name']} {$mmL}--{$mmR} {$mmTo} : \"{$mmRel['method']}\"";
            }
        }
    }
    $mmMentioned = [];
    foreach (array_keys($mmErPairs) as $mmPk) { [$mmA,$mmB]=explode(':',$mmPk); $mmMentioned[$mmA]=true; $mmMentioned[$mmB]=true; }
    $mmStandalone = [];
    foreach ($data['models'] as $mmModel) {
        if (!isset($mmMentioned[$mmModel['name']])) {
            $mmStandalone[] = "    {$mmModel['name']} {"; $mmStandalone[] = "        string table \"{$mmModel['table']}\""; $mmStandalone[] = "    }";
        }
    }
    $mmErCode = "erDiagram\n".implode("\n",$mmStandalone).(!empty($mmStandalone)?"\n":'').implode("\n",$mmErPairs);

    // Per-model focused ER codes (model + its direct neighbors only)
    $mmFocused = [];
    foreach ($data['models'] as $mmFm) {
        $mmFn  = $mmFm['name'];
        $mmFps = [];
        foreach ($mmErPairs as $mmPk => $mmPl) {
            [$mmPa, $mmPb] = explode(':', $mmPk, 2);
            if ($mmPa === $mmFn || $mmPb === $mmFn) $mmFps[] = $mmPl;
        }
        $mmFocused[$mmFn] = empty($mmFps) ? '' : ("erDiagram\n" . implode("\n", $mmFps));
    }
    $mmFirstFocusModel = '';
    foreach ($data['models'] as $mmFm) {
        if (!empty($mmFm['relationships'])) { $mmFirstFocusModel = $mmFm['name']; break; }
    }
    @endphp

    {{-- Header --}}
    <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#6366F1 0%,#818CF8 100%);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(99,102,241,0.25);">
                <svg viewBox="0 0 20 20" fill="none" style="width:20px;height:20px;" stroke-linecap="round"><circle cx="10" cy="10" r="2.5" fill="white"/><circle cx="3.5" cy="4" r="1.5" fill="white"/><circle cx="16.5" cy="4" r="1.5" fill="white"/><circle cx="3.5" cy="16" r="1.5" fill="white"/><circle cx="16.5" cy="16" r="1.5" fill="white"/><line x1="10" y1="7.5" x2="3.5" y2="4" stroke="white" stroke-width="1.2" opacity="0.7"/><line x1="10" y1="7.5" x2="16.5" y2="4" stroke="white" stroke-width="1.2" opacity="0.7"/><line x1="10" y1="12.5" x2="3.5" y2="16" stroke="white" stroke-width="1.2" opacity="0.7"/><line x1="10" y1="12.5" x2="16.5" y2="16" stroke="white" stroke-width="1.2" opacity="0.7"/></svg>
            </div>
            <div>
                <h1 style="font-size:20px;font-weight:700;color:#111827;margin:0;line-height:1.2;letter-spacing:-0.3px;">Relation Graph</h1>
                <div style="display:flex;align-items:center;gap:6px;margin-top:5px;">
                    <span style="display:inline-flex;align-items:center;background:rgba(99,102,241,.08);color:#6366F1;font-size:11px;font-weight:600;padding:2px 9px;border-radius:20px;">{{ count($data['models']) }} models</span>
                    <span style="display:inline-flex;align-items:center;background:#F0FDF4;color:#16A34A;font-size:11px;font-weight:600;padding:2px 9px;border-radius:20px;">{{ count($mmErPairs) }} relationships</span>
                </div>
            </div>
        </div>
        <div style="display:flex;background:#F3F4F6;border-radius:8px;padding:3px;gap:2px;">
            <button id="map-tab-graph" onclick="setMapTab('graph')" style="padding:7px 16px;border-radius:6px;font-size:13px;font-weight:600;background:#6366F1;color:#FFFFFF;border:none;cursor:pointer;transition:background .15s,color .15s;">Relation Graph</button>
            <button id="map-tab-tree"  onclick="setMapTab('tree')"  style="padding:7px 16px;border-radius:6px;font-size:13px;font-weight:600;background:transparent;color:#6B7280;border:none;cursor:pointer;transition:background .15s,color .15s;">Tree View</button>
            <button id="map-tab-er"    onclick="setMapTab('er')"    style="padding:7px 16px;border-radius:6px;font-size:13px;font-weight:600;background:transparent;color:#6B7280;border:none;cursor:pointer;transition:background .15s,color .15s;">ER Diagram</button>
        </div>
    </div>

    {{-- ── TAB: Relation Graph (force-directed SVG) ── --}}
    <div id="map-graph">

        {{-- Controls row --}}
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;min-height:38px;flex-wrap:wrap;">
            <div style="position:relative;">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;pointer-events:none;" fill="none" stroke="#9CA3AF" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input id="rg-search-input" type="text" placeholder="Search model…" oninput="graphSearch(this.value)"
                    style="font-size:13px;background:#FFFFFF;border:1px solid #E5E7EB;border-radius:8px;padding:8px 12px 8px 32px;color:#111827;outline:none;width:200px;transition:border-color .15s,box-shadow .15s;"
                    onfocus="this.style.borderColor='#6366F1';this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'"
                    onblur="this.style.borderColor='#E5E7EB';this.style.boxShadow='none'">
            </div>
            <button id="rg-clear-btn" onclick="rgDiagClear()" style="display:none;font-size:12px;color:#6B7280;padding:7px 14px;border-radius:8px;border:1px solid #E5E7EB;background:#FFFFFF;cursor:pointer;font-weight:500;">✕ Clear</button>
            {{-- Legend --}}
            <div id="rg-legend" style="margin-left:auto;display:flex;flex-wrap:wrap;align-items:center;gap:8px 14px;">
                <span style="display:flex;align-items:center;gap:6px;font-size:11px;color:#6B7280;font-weight:500;"><span style="display:inline-block;width:18px;height:2px;background:#818cf8;border-radius:2px;"></span>hasMany</span>
                <span style="display:flex;align-items:center;gap:6px;font-size:11px;color:#6B7280;font-weight:500;"><span style="display:inline-block;width:18px;height:2px;background:#2dd4bf;border-radius:2px;"></span>hasOne</span>
                <span style="display:flex;align-items:center;gap:6px;font-size:11px;color:#6B7280;font-weight:500;"><span style="display:inline-block;width:18px;height:2px;background:#34d399;border-radius:2px;"></span>belongsTo</span>
                <span style="display:flex;align-items:center;gap:6px;font-size:11px;color:#6B7280;font-weight:500;"><span style="display:inline-block;width:18px;height:2px;background:#c084fc;border-radius:2px;"></span>M:M</span>
            </div>
            {{-- Selected node info --}}
            <div id="rg-info-row" style="display:none;margin-left:auto;align-items:center;gap:8px;">
                <span id="rg-info-name"  style="font-weight:700;color:#6366F1;font-size:13px;"></span>
                <span id="rg-info-table" style="font-size:11px;background:rgba(99,102,241,.08);color:#6366F1;padding:2px 8px;border-radius:6px;font-weight:500;"></span>
                <button id="rg-rels-btn" onclick="rgToggleRels()"
                    style="font-size:12px;padding:6px 14px;border-radius:8px;border:1px solid #E5E7EB;background:#FFFFFF;color:#374151;cursor:pointer;font-weight:500;display:inline-flex;align-items:center;gap:4px;">
                    <span id="rg-info-count"></span>
                    <svg id="rg-rels-chevron" style="width:10px;height:10px;transition:transform .2s;" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 4l4 4 4-4"/></svg>
                </button>
            </div>
        </div>

        {{-- Relationship cards panel --}}
        <div id="rg-rels-panel" style="display:none;margin-bottom:14px;background:#FFFFFF;border:1px solid #E5E7EB;border-radius:12px;padding:16px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
            <p id="rg-rels-title" style="font-size:10px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:12px;"></p>
            <div id="rg-rels-cards" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:8px;"></div>
        </div>

        {{-- Canvas --}}
        <div style="position:relative;border-radius:16px;border:1px solid #E5E7EB;overflow:hidden;background:#FFFFFF;box-shadow:0 4px 6px -1px rgba(0,0,0,0.07),0 2px 4px -1px rgba(0,0,0,0.04);">
            <svg id="rg-canvas" xmlns="http://www.w3.org/2000/svg"
                 style="width:100%;height:600px;display:block;cursor:grab;user-select:none">
                <defs>
                    <pattern id="rg-dot-grid" width="24" height="24" patternUnits="userSpaceOnUse">
                        <circle cx="1" cy="1" r="0.8" fill="rgba(0,0,0,0.06)" opacity="1"/>
                    </pattern>
                    <marker id="rg-arr-many"      viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#818cf8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <marker id="rg-arr-one"       viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#2dd4bf" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <marker id="rg-arr-belongs"   viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#34d399" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <marker id="rg-arr-mm"        viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#c084fc" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <marker id="rg-arr-many-a"    viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#6366F1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <marker id="rg-arr-one-a"     viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#14b8a6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <marker id="rg-arr-belongs-a" viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <marker id="rg-arr-mm-a"      viewBox="0 0 10 10" markerWidth="7" markerHeight="7" refX="9" refY="5" orient="auto"><path d="M1,1.5 L9,5 L1,8.5" fill="none" stroke="#a855f7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></marker>
                    <filter id="rg-f-node"     x="-20%" y="-30%" width="140%" height="160%"><feDropShadow dx="0" dy="1" stdDeviation="3"  flood-color="rgba(0,0,0,0.08)"/></filter>
                    <filter id="rg-f-node-sel" x="-20%" y="-30%" width="140%" height="160%"><feDropShadow dx="0" dy="4" stdDeviation="10" flood-color="rgba(99,102,241,0.35)"/></filter>
                    <filter id="rg-f-node-rel" x="-20%" y="-30%" width="140%" height="160%"><feDropShadow dx="0" dy="3" stdDeviation="7"  flood-color="rgba(52,211,153,0.30)"/></filter>
                </defs>
                <rect width="100%" height="100%" fill="url(#rg-dot-grid)"/>
                <g id="rg-vp">
                    <g id="rg-edges-g"></g>
                    <g id="rg-nodes-g"></g>
                </g>
            </svg>

            {{-- Zoom controls --}}
            <div style="position:absolute;top:12px;right:12px;display:flex;align-items:center;gap:4px;">
                <button onclick="graphZoom(1.25)" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;background:#FFFFFF;border:1px solid #E5E7EB;border-radius:8px;color:#6B7280;font-weight:700;font-size:16px;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.08);transition:border-color .15s,color .15s;" onmouseenter="this.style.borderColor='#6366F1';this.style.color='#6366F1'" onmouseleave="this.style.borderColor='#E5E7EB';this.style.color='#6B7280'">+</button>
                <button onclick="graphZoom(0.8)"  style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;background:#FFFFFF;border:1px solid #E5E7EB;border-radius:8px;color:#6B7280;font-weight:700;font-size:16px;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.08);transition:border-color .15s,color .15s;" onmouseenter="this.style.borderColor='#6366F1';this.style.color='#6366F1'" onmouseleave="this.style.borderColor='#E5E7EB';this.style.color='#6B7280'">−</button>
                <button onclick="graphFit()"      style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;background:#FFFFFF;border:1px solid #E5E7EB;border-radius:8px;color:#6B7280;font-size:14px;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.08);transition:border-color .15s,color .15s;" title="Fit to screen" onmouseenter="this.style.borderColor='#6366F1';this.style.color='#6366F1'" onmouseleave="this.style.borderColor='#E5E7EB';this.style.color='#6B7280'">⊡</button>
                <button onclick="graphReset()"    style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;background:#FFFFFF;border:1px solid #E5E7EB;border-radius:8px;color:#6B7280;font-size:14px;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.08);transition:border-color .15s,color .15s;" title="Reset" onmouseenter="this.style.borderColor='#6366F1';this.style.color='#6366F1'" onmouseleave="this.style.borderColor='#E5E7EB';this.style.color='#6B7280'">⟳</button>
            </div>

            {{-- Minimap --}}
            <div style="position:absolute;bottom:12px;right:12px;border-radius:10px;border:1px solid #E5E7EB;background:rgba(255,255,255,0.95);overflow:hidden;width:160px;height:100px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                <svg id="rg-minimap" width="160" height="100" style="display:block"></svg>
            </div>

            {{-- Hint --}}
            <div style="position:absolute;bottom:12px;left:12px;font-size:11px;color:#9CA3AF;background:rgba(255,255,255,0.92);padding:4px 10px;border-radius:8px;border:1px solid #F3F4F6;pointer-events:none;font-weight:500;">
                Click node · Drag to pan · Scroll to zoom
            </div>
        </div>

    </div>

    {{-- ── TAB: Tree View ── --}}
    <div id="map-tree" style="display:none">
        <div style="margin-bottom:16px;">
            <input id="map-search" oninput="filterModelTree()" type="search" placeholder="Filter models…"
                style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:10px;padding:8px 14px;font-size:13px;color:var(--text);font-family:var(--font-sans);width:220px;outline:none;">
        </div>
        <div id="map-tree-content" style="display:flex;flex-direction:column;gap:10px;"></div>
    </div>

    {{-- ── TAB: ER Diagram ── --}}
    <div id="map-er" style="display:none">
        @if(empty($mmErPairs) && empty($mmStandalone))
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:48px;text-align:center;">
            <p style="color:var(--text-faint);font-size:13px;">No relationships found across models.</p>
        </div>
        @else

        {{-- Toolbar --}}
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;flex-wrap:wrap;">

            {{-- Focus model selector --}}
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);">Focus:</span>
                <select id="er-focus-select" onchange="erFocus(this.value)"
                    style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:9px;padding:7px 28px 7px 12px;font-size:12px;color:var(--text);font-family:var(--font-mono);cursor:pointer;outline:none;">
                    <option value="__all__">All Models</option>
                    @foreach($data['models'] as $erM)
                    <option value="{{ $erM['name'] }}">{{ $erM['name'] }}{{ count($erM['relationships']??[]) ? ' ('.count($erM['relationships']).' rels)' : '' }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Stats --}}
            <span style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);">{{ count($data['models']) }} models · {{ count($mmErPairs) }} relationships</span>

            {{-- Large-project warning — compact inline badge with tooltip --}}
            @if(count($data['models']) > 20)
            <span title="Large project — auto-focused on a single model. Select All Models to see everything." style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.25);border-radius:20px;font-size:10px;font-weight:600;color:var(--amber);cursor:default;white-space:nowrap;">
                <svg viewBox="0 0 20 20" fill="currentColor" style="width:11px;height:11px;flex:none;"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                Large project
            </span>
            @endif

            {{-- Right-side controls --}}
            <div style="margin-left:auto;display:flex;align-items:center;gap:6px;flex-wrap:wrap;">

                {{-- Layout direction toggle --}}
                <button id="er-layout-btn" onclick="erToggleLayout()"
                    style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;font-size:12px;font-weight:600;color:#374151;background:#F9FAFB;border:1px solid #E5E7EB;border-radius:8px;cursor:pointer;transition:background .15s;font-family:var(--font-sans);"
                    onmouseenter="this.style.background='#F3F4F6'" onmouseleave="this.style.background='#F9FAFB'" title="Toggle layout direction">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;flex:none;"><path d="M8 3H5a2 2 0 00-2 2v3m18 0V5a2 2 0 00-2-2h-3m0 18h3a2 2 0 002-2v-3M3 16v3a2 2 0 002 2h3"/></svg>
                    TB
                </button>

                {{-- Zoom group --}}
                <div style="display:flex;align-items:center;gap:2px;background:#F3F4F6;border:1px solid #E5E7EB;border-radius:8px;padding:3px;">
                    <button onclick="erZoom(1.2)" style="width:26px;height:26px;display:flex;align-items:center;justify-content:center;background:transparent;border:none;border-radius:5px;color:#6B7280;font-weight:700;font-size:15px;cursor:pointer;transition:background .12s;" onmouseenter="this.style.background='#E5E7EB'" onmouseleave="this.style.background='transparent'">+</button>
                    <span id="er-zoom-lbl" style="font-size:10px;color:#6B7280;font-family:ui-monospace,monospace;min-width:36px;text-align:center;line-height:1;">100%</span>
                    <button onclick="erZoom(0.8)" style="width:26px;height:26px;display:flex;align-items:center;justify-content:center;background:transparent;border:none;border-radius:5px;color:#6B7280;font-weight:700;font-size:15px;cursor:pointer;transition:background .12s;" onmouseenter="this.style.background='#E5E7EB'" onmouseleave="this.style.background='transparent'">−</button>
                    <button onclick="erZoomFit()" style="width:26px;height:26px;display:flex;align-items:center;justify-content:center;background:transparent;border:none;border-radius:5px;color:#6B7280;font-size:13px;cursor:pointer;transition:background .12s;" title="Reset zoom" onmouseenter="this.style.background='#E5E7EB'" onmouseleave="this.style.background='transparent'">⊡</button>
                </div>

                {{-- Fullscreen --}}
                <button onclick="erFullScreen()"
                    style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;font-size:12px;font-weight:600;color:#374151;background:#F9FAFB;border:1px solid #E5E7EB;border-radius:8px;cursor:pointer;transition:background .15s;font-family:var(--font-sans);"
                    onmouseenter="this.style.background='#F3F4F6'" onmouseleave="this.style.background='#F9FAFB'" title="Full-screen view">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;flex:none;"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>
                    Full
                </button>

                {{-- Divider --}}
                <div style="width:1px;height:22px;background:#E5E7EB;"></div>

                {{-- Download SVG --}}
                <button id="er-dl-svg" onclick="erDownloadSVG()"
                    style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;font-size:12px;font-weight:600;color:#6366F1;background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,0.25);border-radius:8px;cursor:pointer;transition:background .15s;font-family:var(--font-sans);"
                    onmouseenter="this.style.background='rgba(99,102,241,.14)'" onmouseleave="this.style.background='rgba(99,102,241,.08)'"
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;flex:none;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    SVG
                </button>

                {{-- Download PNG --}}
                <button id="er-dl-png" onclick="erDownloadPNG()"
                    style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;font-size:12px;font-weight:600;color:#374151;background:#F9FAFB;border:1px solid #E5E7EB;border-radius:8px;cursor:pointer;transition:background .15s;font-family:var(--font-sans);"
                    onmouseenter="this.style.background='#F3F4F6'" onmouseleave="this.style.background='#F9FAFB'">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;flex:none;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    PNG
                </button>

            </div>

        </div>

        {{-- Canvas + Model info panel --}}
        <div style="display:flex;gap:14px;align-items:flex-start;">

            {{-- Mermaid ER canvas — dot-grid background --}}
            <div id="er-canvas-wrap" style="flex:1;min-width:0;background-color:#FAFAFA;background-image:radial-gradient(circle,rgba(99,102,241,.12) 1.5px,transparent 1.5px);background-size:24px 24px;border:1px solid var(--border);border-radius:14px;overflow:hidden;height:70vh;cursor:grab;position:relative;user-select:none;">
                <div id="er-transform-wrap" style="padding:20px;display:inline-block;transform-origin:0 0;">
                    <pre class="mermaid" id="er-mermaid">{{ $mmErCode }}</pre>
                </div>
            </div>

            {{-- Model info panel --}}
            <div id="er-info-panel" style="width:256px;flex:none;height:70vh;background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;display:flex;flex-direction:column;overflow:hidden;">
                <div style="padding:13px 16px 11px;border-bottom:1px solid var(--border);flex:none;">
                    <p style="font-size:10px;font-weight:700;color:var(--text-faint);letter-spacing:0.08em;text-transform:uppercase;margin:0;">Model Details</p>
                </div>
                <div id="er-info-content" style="flex:1;padding:14px 16px;overflow-y:auto;">
                    <p style="font-size:12px;color:var(--text-faint);text-align:center;margin-top:40px;">Select a model<br>to see details</p>
                </div>
            </div>

        </div>

        {{-- Fullscreen modal --}}
        <div id="er-fs-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.88);z-index:9999;flex-direction:column;">
            <div style="flex:none;display:flex;align-items:center;justify-content:space-between;padding:12px 20px;background:#0F172A;border-bottom:1px solid rgba(255,255,255,0.08);">
                <span style="color:#E2E8F0;font-size:14px;font-weight:700;display:flex;align-items:center;gap:8px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#6366F1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;"><circle cx="10" cy="10" r="2.5"/><circle cx="3.5" cy="4" r="1.5"/><circle cx="16.5" cy="4" r="1.5"/><circle cx="3.5" cy="16" r="1.5"/><circle cx="16.5" cy="16" r="1.5"/><line x1="10" y1="7.5" x2="3.5" y2="4"/><line x1="10" y1="7.5" x2="16.5" y2="4"/><line x1="10" y1="12.5" x2="3.5" y2="16"/><line x1="10" y1="12.5" x2="16.5" y2="16"/></svg>
                    ER Diagram — Full View
                </span>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="font-size:11px;color:#475569;font-family:ui-monospace,monospace;">Scroll to zoom · Drag to pan · Esc to close</span>
                    <button onclick="erCloseFullScreen()"
                        style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;font-size:12px;font-weight:600;color:#E2E8F0;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);border-radius:8px;cursor:pointer;transition:background .15s;"
                        onmouseenter="this.style.background='rgba(255,255,255,0.14)'" onmouseleave="this.style.background='rgba(255,255,255,0.08)'">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Close
                    </button>
                </div>
            </div>
            <div id="er-fs-content" style="flex:1;overflow:hidden;background:#F8FAFC;cursor:grab;position:relative;">
                <div id="er-fs-transform" style="display:inline-block;padding:32px;transform-origin:0 0;"></div>
            </div>
        </div>

        @endif
    </div>

</section>

{{-- Routes --}}
<section id="sec-routes" class="p-6" style="display:none">

    @php
    $routeMethodCounts = [];
    foreach ($data['routes'] as $r) {
        foreach (array_filter($r['methods']??[], fn($m)=>$m!=='HEAD') as $m) {
            $routeMethodCounts[strtoupper($m)] = ($routeMethodCounts[strtoupper($m)] ?? 0) + 1;
        }
    }
    $routeMethodStyle = [
        'GET'    => 'bg-emerald-500 text-white',
        'POST'   => 'bg-blue-500 text-white',
        'PUT'    => 'bg-amber-500 text-white',
        'PATCH'  => 'bg-orange-500 text-white',
        'DELETE' => 'bg-red-500 text-white',
    ];
    $routeMethodColors = [
        'GET'    => ['hex'=>'#34D399','bg'=>'rgba(52,211,153,.12)','border'=>'rgba(52,211,153,.3)'],
        'POST'   => ['hex'=>'#60A5FA','bg'=>'rgba(96,165,250,.12)','border'=>'rgba(96,165,250,.3)'],
        'PUT'    => ['hex'=>'#A78BFA','bg'=>'rgba(167,139,250,.12)','border'=>'rgba(167,139,250,.3)'],
        'PATCH'  => ['hex'=>'#FB923C','bg'=>'rgba(251,146,60,.12)','border'=>'rgba(251,146,60,.3)'],
        'DELETE' => ['hex'=>'#F87171','bg'=>'rgba(248,113,113,.12)','border'=>'rgba(248,113,113,.3)'],
    ];
    $routeTotal      = $rs['total'] ?? 0;
    $routeAuthCount  = count(array_filter($data['routes'], fn($r) => in_array('auth', array_map('strtolower', $r['middleware']??[]))));
    $routeApiCount   = count($data['api_docs'] ?? []);
    $routePublic     = $routeTotal - $routeAuthCount;
    $routeDistTotal  = max(array_sum($routeMethodCounts), 1);
    @endphp

    {{-- List view --}}
    <div id="routes-list">

        {{-- Stats (models style) --}}
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--cyan);">{{ $routeTotal }}</span>
                <span class="mds-top-stat-lbl">Total Routes</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--emerald);">{{ $routeAuthCount }}</span>
                <span class="mds-top-stat-lbl">Auth Protected</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--amber);">{{ $routeApiCount }}</span>
                <span class="mds-top-stat-lbl">API Endpoints</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:var(--rose);">{{ $routePublic }}</span>
                <span class="mds-top-stat-lbl">Public</span>
            </div>
        </div>
        <div class="mds-toolbar">
            <div style="position:relative;">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--text-faint);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input id="routes-search" oninput="filterRoutes()" type="search" placeholder="Search URI or handler…"
                    style="border:1px solid var(--border);border-radius:8px;padding:8px 12px 8px 32px;font-size:12px;width:220px;font-family:var(--font-mono);background:var(--bg-elevated);color:var(--text);">
            </div>
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ $routeTotal }} routes</span>
        </div>

        {{-- Table --}}
        <div style="background:var(--bg-elevated);border-radius:12px;border:1px solid var(--border);overflow:hidden;">
            <table class="w-full" style="border-collapse:collapse;">
                <thead>
                    <tr style="background:var(--bg-hover);border-bottom:1px solid var(--border);">
                        <th style="text-align:left;padding:12px 16px;font-family:var(--font-mono);font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.08em;width:100px;">Method</th>
                        <th style="text-align:left;padding:12px 16px;font-family:var(--font-mono);font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.08em;">URI</th>
                        <th style="text-align:left;padding:12px 16px;font-family:var(--font-mono);font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.08em;width:200px;">Handler</th>
                        <th style="text-align:left;padding:12px 16px;font-family:var(--font-mono);font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.08em;width:160px;">Middleware</th>
                        <th style="text-align:left;padding:12px 16px;font-family:var(--font-mono);font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.08em;width:200px;">Route Name</th>
                        <th style="width:32px;"></th>
                    </tr>
                </thead>
                <tbody id="routes-tbody">
                    @foreach($data['routes'] as $i => $route)
                    @php
                        $methods     = array_values(array_filter($route['methods']??[], fn($m)=>$m!=='HEAD'));
                        $ctrl        = class_basename($route['controller']['class']??'');
                        $action      = $route['controller']['method']??'Closure';
                        $isInvokable = $action === '__invoke';
                        $mwsRaw      = implode(',', array_map('strtolower', $route['middleware']??[]));
                        $allMws      = $route['middleware'] ?? [];
                        $mwCount     = count($allMws);
                        $primaryMw   = $mwCount > 0 ? class_basename($allMws[0]) : null;
                        $fullMwTitle = implode(' · ', $allMws);
                        $routeName   = $route['name'] ?? '';
                    @endphp
                    <tr class="route-row route-row-anim" style="--ri:{{$i}};border-bottom:1px solid var(--border);cursor:pointer;transition:background .15s;"
                        onclick="showRouteDetail({{ $i }})"
                        data-uri="{{ strtolower($route['uri']??'') }}"
                        data-methods="{{ implode(',',array_map('strtoupper',$methods)) }}"
                        data-mw="{{ $mwsRaw }}">

                        {{-- Method --}}
                        <td style="padding:10px 16px;">
                            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                                @foreach($methods as $m)
                                <span class="text-xs px-2 py-0.5 rounded-md font-bold method-{{ strtolower($m) }}" style="font-family:var(--font-mono);">{{ $m }}</span>
                                @endforeach
                            </div>
                        </td>

                        {{-- URI --}}
                        <td style="padding:10px 16px;">
                            <code style="font-size:12px;font-family:var(--font-mono);color:var(--text);">{{ $route['uri'] }}</code>
                        </td>

                        {{-- Handler --}}
                        <td style="padding:10px 16px;font-size:12px;">
                            @if($ctrl)
                                <span style="font-weight:700;color:var(--text);font-family:var(--font-mono);">{{ $ctrl }}</span>
                                @if(!$isInvokable)
                                    <span style="color:var(--border-strong);margin:0 2px;">@</span><span style="color:var(--text-faint);font-family:var(--font-mono);">{{ $action }}</span>
                                @endif
                            @else
                                <span style="color:var(--text-faint);font-style:italic;">Closure</span>
                            @endif
                        </td>

                        {{-- Middleware — compact single line --}}
                        <td style="padding:10px 16px;">
                            @if($mwCount === 0)
                                <span style="font-size:12px;color:var(--text-faint);">—</span>
                            @else
                                <div style="display:flex;align-items:center;gap:6px;" title="{{ $fullMwTitle }}">
                                    <span class="ctrl-chip" style="max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $allMws[0] }}">{{ $primaryMw }}</span>
                                    @if($mwCount > 1)
                                    <span class="ctrl-chip" style="white-space:nowrap;flex:none;" title="{{ $fullMwTitle }}">+{{ $mwCount - 1 }}</span>
                                    @endif
                                </div>
                            @endif
                        </td>

                        {{-- Route name --}}
                        <td style="padding:10px 16px;font-family:var(--font-mono);font-size:12px;color:var(--text-faint);">
                            @if($routeName)
                                <span style="display:block;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $routeName }}">{{ $routeName }}</span>
                            @else
                                <span style="color:var(--border-strong);">—</span>
                            @endif
                        </td>

                        {{-- Chevron --}}
                        <td style="padding:10px 16px 10px 0;text-align:right;">
                            <svg style="width:16px;height:16px;color:var(--text-faint);display:inline;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Detail view --}}
    <div id="routes-detail" style="display:none">
        <div id="routes-detail-content"></div>
    </div>

</section>

{{-- ══ API DOCUMENTATION ══ --}}
<section id="sec-apidocs" style="display:none;padding:24px;">

    @php
    $apiDocs   = $data['api_docs'] ?? [];
    $apiGroups = [];
    foreach ($apiDocs as $ep) {
        $apiGroups[$ep['group']][] = $ep;
    }
    ksort($apiGroups);

    $methodStyle = [
        'GET'    => ['color'=>'#34D399','bg'=>'rgba(52,211,153,.15)', 'border'=>'rgba(52,211,153,.3)', 'left'=>'#34D399'],
        'POST'   => ['color'=>'#60A5FA','bg'=>'rgba(96,165,250,.15)', 'border'=>'rgba(96,165,250,.3)', 'left'=>'#60A5FA'],
        'PUT'    => ['color'=>'#FBBF24','bg'=>'rgba(251,191,36,.15)', 'border'=>'rgba(251,191,36,.3)', 'left'=>'#FBBF24'],
        'PATCH'  => ['color'=>'#FB923C','bg'=>'rgba(251,146,60,.15)', 'border'=>'rgba(251,146,60,.3)', 'left'=>'#FB923C'],
        'DELETE' => ['color'=>'#F87171','bg'=>'rgba(248,113,113,.15)','border'=>'rgba(248,113,113,.3)','left'=>'#F87171'],
    ];
    $defaultStyle = ['color'=>'#6B778C','bg'=>'rgba(142,155,184,.1)','border'=>'rgba(142,155,184,.25)','left'=>'#6B778C'];

    $statusStyle = fn(int $code) => match(true) {
        $code < 300 => ['color'=>'#34D399','bg'=>'rgba(52,211,153,.12)','border'=>'rgba(52,211,153,.25)'],
        $code < 500 => ['color'=>'#F87171','bg'=>'rgba(248,113,113,.12)','border'=>'rgba(248,113,113,.25)'],
        default     => ['color'=>'#FBBF24','bg'=>'rgba(251,191,36,.12)','border'=>'rgba(251,191,36,.25)'],
    };

    $typeStyle = [
        'string'  => ['color'=>'#60A5FA','bg'=>'rgba(96,165,250,.12)'],
        'integer' => ['color'=>'#A78BFA','bg'=>'rgba(167,139,250,.12)'],
        'number'  => ['color'=>'#A78BFA','bg'=>'rgba(167,139,250,.12)'],
        'boolean' => ['color'=>'#FBBF24','bg'=>'rgba(251,191,36,.12)'],
        'array'   => ['color'=>'#34D399','bg'=>'rgba(52,211,153,.12)'],
        'file'    => ['color'=>'#F87171','bg'=>'rgba(248,113,113,.12)'],
        'email'   => ['color'=>'#6366F1','bg'=>'rgba(99,102,241,.12)'],
        'url'     => ['color'=>'#60A5FA','bg'=>'rgba(96,165,250,.12)'],
        'date'    => ['color'=>'#FB923C','bg'=>'rgba(251,146,60,.12)'],
        'uuid'    => ['color'=>'#6B778C','bg'=>'rgba(142,155,184,.1)'],
        'enum'    => ['color'=>'#E879F9','bg'=>'rgba(232,121,249,.12)'],
    ];
    $defaultType = ['color'=>'#6B778C','bg'=>'rgba(142,155,184,.1)'];

    $groupLabel = fn(string $g) => ucwords(str_replace(['-', '_'], ' ', $g));

    $methodCounts = array_fill_keys(['GET','POST','PUT','PATCH','DELETE'], 0);
    foreach ($apiDocs as $ep) {
        $m = strtoupper($ep['method'] ?? '');
        if (isset($methodCounts[$m])) $methodCounts[$m]++;
    }
    @endphp

    {{-- Page header --}}
    <div style="margin-bottom:24px;">
        <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:16px;">
            <div class="sec-header" style="margin-bottom:0;">
                <div class="sec-header__icon" style="background:rgba(59,130,246,.10);border:1px solid rgba(59,130,246,.20);color:#3b82f6;">
                    <svg viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h1 class="sec-header__title">API Documentation</h1>
                    <p class="sec-header__sub">
                        {{ count($apiDocs) }} endpoint{{ count($apiDocs) !== 1 ? 's' : '' }} across
                        {{ count($apiGroups) }} resource{{ count($apiGroups) !== 1 ? 's' : '' }} · auto-generated from routes
                    </p>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                {{-- Search input --}}
                <div style="position:relative;">
                    <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--text-faint);pointer-events:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input id="api-search" type="text" placeholder="Search endpoints…" oninput="apiSearch(this.value)"
                        style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:8px;padding:8px 12px 8px 32px;font-size:12px;width:220px;font-family:var(--font-mono);color:var(--text);outline:none;">
                </div>
                {{-- Method filter pill group --}}
                <div id="api-method-filters"
                     style="display:flex;gap:0;background:var(--bg-elevated);border:1px solid var(--border);border-radius:9px;padding:3px;">
                    @foreach(['ALL','GET','POST','PUT','PATCH','DELETE'] as $mf)
                    @php
                        $mfStyle = $methodStyle[$mf] ?? null;
                        $isAll   = $mf === 'ALL';
                    @endphp
                    <button onclick="apiFilter('{{ $mf }}')" data-method="{{ $mf }}"
                        class="api-filter-btn"
                        style="font-size:10px;padding:5px 10px;border-radius:6px;border:none;font-weight:800;font-family:var(--font-mono);cursor:pointer;transition:background .15s,color .15s;{{ $isAll ? 'background:var(--bg-hover);color:var(--text);' : 'background:transparent;color:var(--text-faint);' }}">
                        {{ $mf }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Method stats row --}}
        @if(!empty($apiDocs))
        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:4px;">
            @foreach($methodCounts as $method => $cnt)
            @if($cnt > 0)
            @php $ms = $methodStyle[$method] ?? $defaultStyle; @endphp
            <div style="display:flex;align-items:center;gap:10px;background:var(--bg-elevated);border:1px solid var(--border);border-left:3px solid {{ $ms['left'] }};border-radius:10px;padding:8px 16px;">
                <span style="font-family:var(--font-mono);font-size:10px;font-weight:800;padding:3px 8px;border-radius:5px;background:{{ $ms['bg'] }};color:{{ $ms['color'] }};border:1px solid {{ $ms['border'] }};">{{ $method }}</span>
                <span style="font-size:18px;font-weight:800;font-family:var(--font-mono);color:var(--text);line-height:1;">{{ $cnt }}</span>
            </div>
            @endif
            @endforeach
        </div>
        @endif
    </div>

    @if(empty($apiDocs))
    {{-- Empty state --}}
    <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:16px;padding:64px 24px;text-align:center;">
        <div style="width:56px;height:56px;background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.2);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg style="width:26px;height:26px;color:#6366F1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <p style="font-size:14px;font-weight:700;color:var(--text);margin:0 0 8px;">No API routes found</p>
        <p style="font-size:12px;color:var(--text-faint);margin:0;">
            Define routes under the
            <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);color:var(--cyan);">api/</code>
            prefix or apply the
            <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);color:var(--cyan);">api</code>
            middleware group.
        </p>
    </div>
    @else

    {{-- Resource tab strip --}}
    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;padding:4px 0;margin-bottom:18px;border-bottom:1px solid var(--border);padding-bottom:14px;">
        @foreach($apiGroups as $groupName => $endpoints)
        <button onclick="apiScrollTo('{{ $groupName }}')" class="api-nav-item"
            data-group-tab="{{ $groupName }}"
            style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg-elevated);color:var(--text-dim);font-size:12px;font-weight:600;cursor:pointer;transition:all .18s;white-space:nowrap;">
            <span>{{ $groupLabel($groupName) }}</span>
            <span style="font-family:var(--font-mono);font-size:10px;background:var(--bg-hover);color:var(--text-faint);padding:1px 6px;border-radius:6px;">{{ count($endpoints) }}</span>
        </button>
        @endforeach
    </div>

    {{-- Full-width endpoint groups --}}
    <div id="api-groups-container" style="display:flex;flex-direction:column;gap:20px;">
        @foreach($apiGroups as $groupName => $endpoints)
        <div class="api-group" id="api-group-{{ $groupName }}" data-group="{{ $groupName }}">

            {{-- Group header --}}
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                <div style="width:32px;height:32px;background:rgba(99,102,241,.1);border:1px solid rgba(99,102,241,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg style="width:16px;height:16px;color:#6366F1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div style="min-width:0;">
                    <h3 style="font-size:14px;font-weight:700;color:var(--text);margin:0;line-height:1.3;">{{ $groupLabel($groupName) }}</h3>
                    <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);margin:2px 0 0;">/{{ $groupName }}</p>
                </div>
                <span style="background:var(--bg-elevated);border:1px solid var(--border);color:var(--text-dim);font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;flex-shrink:0;margin-left:auto;">
                    {{ count($endpoints) }} endpoint{{ count($endpoints) !== 1 ? 's' : '' }}
                </span>
            </div>

            {{-- Endpoint cards container --}}
            <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;overflow:hidden;">
            @foreach($endpoints as $epIdx => $ep)
            @php
                $ms      = $methodStyle[$ep['method']] ?? $defaultStyle;
                $hasBody = !empty($ep['body_params']);
                $hasPath = !empty($ep['path_params']);
                $uid     = $groupName . '_' . $epIdx;
                $handler = $ep['controller'] . '@' . $ep['action'];
                $isLast  = $epIdx === array_key_last($endpoints);
            @endphp
            <div class="api-endpoint-wrap"
                 data-method="{{ $ep['method'] }}" data-uri="{{ strtolower($ep['uri']) }}"
                 style="{{ $isLast ? '' : 'border-bottom:1px solid var(--border);' }}">

                {{-- Collapsed row --}}
                <div onclick="apiToggle('{{ $uid }}')"
                     style="display:flex;align-items:center;gap:12px;padding:12px 16px;cursor:pointer;border-left:3px solid {{ $ms['left'] }};transition:background .18s;user-select:none;"
                     onmouseenter="this.style.background='var(--bg-hover)'" onmouseleave="this.style.background='transparent'">

                    {{-- Method badge --}}
                    <span style="font-family:var(--font-mono);font-size:10px;font-weight:800;width:60px;text-align:center;padding:4px 0;border-radius:6px;background:{{ $ms['bg'] }};color:{{ $ms['color'] }};border:1px solid {{ $ms['border'] }};flex-shrink:0;">
                        {{ $ep['method'] }}
                    </span>

                    {{-- URI + route name --}}
                    <div style="flex:1;min-width:0;">
                        <code style="font-family:var(--font-mono);font-size:13px;color:var(--text);font-weight:600;">{{ $ep['uri'] }}</code>
                        @if($ep['name'])
                        <p style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);margin:2px 0 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $ep['name'] }}</p>
                        @endif
                    </div>

                    {{-- Badges + handler + chevron --}}
                    <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                        @if($ep['auth_required'])
                        <span style="display:inline-flex;align-items:center;gap:4px;font-size:10px;font-weight:600;color:#FBBF24;background:rgba(251,191,36,.12);padding:3px 8px;border-radius:20px;border:1px solid rgba(251,191,36,.3);">
                            <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Auth
                        </span>
                        @endif
                        @if($hasBody)
                        <span style="font-size:10px;font-weight:600;color:#60A5FA;background:rgba(96,165,250,.12);padding:3px 8px;border-radius:20px;border:1px solid rgba(96,165,250,.3);">Body</span>
                        @endif
                        @if($hasPath)
                        <span style="font-size:10px;font-weight:600;color:#34D399;background:rgba(52,211,153,.12);padding:3px 8px;border-radius:20px;border:1px solid rgba(52,211,153,.3);">Params</span>
                        @endif
                        <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $handler }}">{{ $handler }}</span>
                        <svg id="chevron-{{ $uid }}" style="width:14px;height:14px;color:var(--text-faint);flex-shrink:0;transition:transform .2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                {{-- Expanded detail panel --}}
                <div id="detail-{{ $uid }}" style="display:none;border-top:1px solid var(--border);">

                    {{-- Detail header bar --}}
                    <div style="display:flex;align-items:center;gap:10px;padding:10px 16px;background:var(--bg-sunken);">
                        <span style="font-family:var(--font-mono);font-size:10px;font-weight:800;padding:3px 8px;border-radius:5px;background:{{ $ms['bg'] }};color:{{ $ms['color'] }};border:1px solid {{ $ms['border'] }};flex-shrink:0;">{{ $ep['method'] }}</span>
                        <code style="font-family:var(--font-mono);font-size:12px;font-weight:600;color:var(--text);">{{ $ep['uri'] }}</code>
                        <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);margin-left:auto;">{{ $handler }}</span>
                    </div>

                    {{-- Detail body --}}
                    <div style="background:var(--bg-hover);padding:18px;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

                            {{-- Left column: parameters --}}
                            <div style="display:flex;flex-direction:column;gap:18px;">

                                {{-- Path parameters --}}
                                @if($hasPath)
                                <div>
                                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                                        <span style="width:3px;height:16px;background:#34D399;border-radius:2px;flex-shrink:0;"></span>
                                        <p style="font-family:var(--font-mono);font-size:9.5px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:.08em;margin:0;">Path Parameters</p>
                                    </div>
                                    <div style="border:1px solid var(--border);border-radius:10px;overflow:hidden;">
                                        <table style="width:100%;border-collapse:collapse;font-size:11px;">
                                            <thead>
                                                <tr style="background:rgba(255,255,255,.03);border-bottom:1px solid var(--border);">
                                                    <th style="padding:8px 12px;text-align:left;font-family:var(--font-mono);font-size:10px;color:var(--text-faint);text-transform:uppercase;font-weight:600;">Name</th>
                                                    <th style="padding:8px 12px;text-align:left;font-family:var(--font-mono);font-size:10px;color:var(--text-faint);text-transform:uppercase;font-weight:600;">Type</th>
                                                    <th style="padding:8px 12px;text-align:left;font-family:var(--font-mono);font-size:10px;color:var(--text-faint);text-transform:uppercase;font-weight:600;">Required</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($ep['path_params'] as $ppIdx => $pp)
                                            @php $ts = $typeStyle[$pp['type']] ?? $defaultType; @endphp
                                            <tr style="{{ $ppIdx > 0 ? 'border-top:1px solid var(--border);' : '' }}">
                                                <td style="padding:9px 12px;font-family:var(--font-mono);font-weight:700;color:var(--text);font-size:12px;">{{ $pp['name'] }}</td>
                                                <td style="padding:9px 12px;">
                                                    <span style="font-family:var(--font-mono);font-size:10px;padding:2px 7px;border-radius:4px;background:{{ $ts['bg'] }};color:{{ $ts['color'] }};">{{ $pp['type'] }}</span>
                                                </td>
                                                <td style="padding:9px 12px;">
                                                    @if($pp['required'])
                                                    <span style="font-family:var(--font-mono);font-size:10px;font-weight:600;padding:2px 8px;border-radius:10px;background:rgba(248,113,113,.12);color:#F87171;border:1px solid rgba(248,113,113,.25);">required</span>
                                                    @else
                                                    <span style="font-family:var(--font-mono);font-size:10px;padding:2px 8px;border-radius:10px;background:var(--bg-hover);color:var(--text-faint);">optional</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endif

                                {{-- Body parameters --}}
                                @if($hasBody)
                                <div>
                                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;flex-wrap:wrap;">
                                        <span style="width:3px;height:16px;background:#60A5FA;border-radius:2px;flex-shrink:0;"></span>
                                        <p style="font-family:var(--font-mono);font-size:9.5px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:.08em;margin:0;">Request Body</p>
                                        @if($ep['request_class'])
                                        <span style="font-family:var(--font-mono);font-size:10px;color:#60A5FA;background:rgba(96,165,250,.12);padding:2px 8px;border-radius:10px;border:1px solid rgba(96,165,250,.25);">{{ $ep['request_class'] }}</span>
                                        @endif
                                    </div>
                                    <div style="border:1px solid var(--border);border-radius:10px;overflow:hidden;">
                                        <table style="width:100%;border-collapse:collapse;font-size:11px;">
                                            <thead>
                                                <tr style="background:rgba(255,255,255,.03);border-bottom:1px solid var(--border);">
                                                    <th style="padding:8px 12px;text-align:left;font-family:var(--font-mono);font-size:10px;color:var(--text-faint);text-transform:uppercase;font-weight:600;">Field</th>
                                                    <th style="padding:8px 12px;text-align:left;font-family:var(--font-mono);font-size:10px;color:var(--text-faint);text-transform:uppercase;font-weight:600;">Type</th>
                                                    <th style="padding:8px 12px;text-align:left;font-family:var(--font-mono);font-size:10px;color:var(--text-faint);text-transform:uppercase;font-weight:600;">Required</th>
                                                    <th style="padding:8px 12px;text-align:left;font-family:var(--font-mono);font-size:10px;color:var(--text-faint);text-transform:uppercase;font-weight:600;">Rules</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($ep['body_params'] as $bpIdx => $bp)
                                            @php $ts = $typeStyle[$bp['type']] ?? $defaultType; @endphp
                                            <tr style="{{ $bpIdx > 0 ? 'border-top:1px solid var(--border);' : '' }}">
                                                <td style="padding:9px 12px;font-family:var(--font-mono);font-weight:700;color:var(--text);font-size:12px;">{{ $bp['field'] }}</td>
                                                <td style="padding:9px 12px;">
                                                    <span style="font-family:var(--font-mono);font-size:10px;padding:2px 7px;border-radius:4px;background:{{ $ts['bg'] }};color:{{ $ts['color'] }};">{{ $bp['type'] }}</span>
                                                </td>
                                                <td style="padding:9px 12px;">
                                                    @if($bp['required'])
                                                    <span style="font-family:var(--font-mono);font-size:10px;font-weight:600;padding:2px 8px;border-radius:10px;background:rgba(248,113,113,.12);color:#F87171;border:1px solid rgba(248,113,113,.25);">required</span>
                                                    @elseif($bp['nullable'])
                                                    <span style="font-family:var(--font-mono);font-size:10px;font-weight:600;padding:2px 8px;border-radius:10px;background:rgba(251,191,36,.12);color:#FBBF24;border:1px solid rgba(251,191,36,.25);">nullable</span>
                                                    @else
                                                    <span style="font-family:var(--font-mono);font-size:10px;padding:2px 8px;border-radius:10px;background:var(--bg-hover);color:var(--text-faint);">optional</span>
                                                    @endif
                                                </td>
                                                <td style="padding:9px 12px;font-family:var(--font-mono);font-size:10px;color:var(--text-faint);max-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $bp['rules'] }}">{{ $bp['rules'] }}</td>
                                            </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endif

                                @if(!$hasPath && !$hasBody)
                                <div style="display:flex;align-items:center;gap:12px;padding:16px;background:var(--bg-elevated);border:1px dashed var(--border);border-radius:10px;">
                                    <svg style="width:18px;height:18px;color:var(--text-faint);flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p style="font-size:11px;color:var(--text-faint);margin:0;">No parameters detected for this endpoint.</p>
                                </div>
                                @endif
                            </div>

                            {{-- Right column: responses + middleware + route name --}}
                            <div style="display:flex;flex-direction:column;gap:18px;">

                                {{-- Responses --}}
                                <div>
                                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                                        <span style="width:3px;height:16px;background:#34D399;border-radius:2px;flex-shrink:0;"></span>
                                        <p style="font-family:var(--font-mono);font-size:9.5px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:.08em;margin:0;">Responses</p>
                                    </div>
                                    <div style="display:flex;flex-direction:column;gap:6px;">
                                        @foreach($ep['responses'] as $code => $label)
                                        @php $sc = $statusStyle((int)$code); @endphp
                                        <div style="display:flex;align-items:center;gap:10px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:8px;padding:8px 12px;">
                                            <span style="font-family:var(--font-mono);font-size:11px;font-weight:700;padding:3px 8px;border-radius:6px;background:{{ $sc['bg'] }};color:{{ $sc['color'] }};border:1px solid {{ $sc['border'] }};flex-shrink:0;">{{ $code }}</span>
                                            <span style="font-size:12px;color:var(--text-dim);">{{ $label }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Middleware --}}
                                @if(!empty($ep['middleware']))
                                <div>
                                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                                        <span style="width:3px;height:16px;background:var(--text-faint);border-radius:2px;flex-shrink:0;"></span>
                                        <p style="font-family:var(--font-mono);font-size:9.5px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:.08em;margin:0;">Middleware</p>
                                    </div>
                                    <div style="display:flex;flex-wrap:wrap;gap:6px;">
                                        @foreach($ep['middleware'] as $mw)
                                        <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-dim);background:var(--bg-elevated);border:1px solid var(--border);padding:4px 10px;border-radius:8px;">{{ $mw }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                {{-- Route name --}}
                                @if($ep['name'])
                                <div>
                                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                                        <span style="width:3px;height:16px;background:#A78BFA;border-radius:2px;flex-shrink:0;"></span>
                                        <p style="font-family:var(--font-mono);font-size:9.5px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:.08em;margin:0;">Route Name</p>
                                    </div>
                                    <code style="font-family:var(--font-mono);font-size:11px;color:#A78BFA;background:rgba(167,139,250,.1);border:1px solid rgba(167,139,250,.25);padding:6px 12px;border-radius:8px;display:inline-block;">{{ $ep['name'] }}</code>
                                </div>
                                @endif

                            </div>{{-- /right column --}}
                        </div>{{-- /grid --}}
                    </div>{{-- /detail body --}}

                    {{-- Request Flow graph --}}
                    <div id="api-flow-{{ $uid }}"
                         style="padding:0 16px 16px;background:var(--bg-hover);"
                         data-controller="{{ $ep['controller'] }}"
                         data-action="{{ $ep['action'] }}"
                         data-method="{{ $ep['method'] }}"
                         data-uri="{{ $ep['uri'] }}"
                         data-rname="{{ $ep['name'] ?? '' }}"
                         data-mws="{{ json_encode($ep['middleware'] ?? []) }}">
                    </div>

                </div>{{-- /expanded panel --}}

            </div>{{-- /api-endpoint-wrap --}}
            @endforeach
            </div>{{-- /endpoint cards container --}}
        </div>{{-- /api-group --}}
        @endforeach
    </div>{{-- /api-groups-container --}}
    @endif

</section>

{{-- Jobs --}}

{{-- Services --}}
<section id="sec-services" class="p-6" style="display:none">
    <div id="services-list">
        @php
            $svcTotal   = count($data['services']);
            $svcMethods = $svcTotal > 0 ? round(array_sum(array_map(fn($s) => count($s['methods']??[]), $data['services'])) / $svcTotal) : 0;
        @endphp
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $svcTotal }}</span>
                <span class="mds-top-stat-lbl">Total Services</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $svcMethods }}</span>
                <span class="mds-top-stat-lbl">Avg Methods</span>
            </div>
        </div>
        <div class="mds-toolbar">
            <input id="services-search" oninput="filterGrid('services')" type="search" placeholder="Search services…" style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;flex:1;max-width:240px;font-family:var(--font-mono);">
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ $svcTotal }} services</span>
        </div>
        @if(empty($data['services']))
        <div class="atlas-card" style="text-align:center;padding:48px;"><p style="color:var(--text-faint);">No services found in <code>app/Services</code></p></div>
        @else
        <div id="services-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;">
            @foreach($data['services'] as $i => $svc)
            <div class="sec2-card" style="--ci:{{$i}};border-left-color:#FF2D20;" onclick="showDetail('services',{{$i}})" data-name="{{ strtolower($svc['name']) }}">
                <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:10px;">
                    <div class="sec2-icon" style="background:rgba(255,45,32,.10);color:#FF2D20;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div style="min-width:0;flex:1;">
                        <p class="sec2-name">{{ $svc['name'] }}</p>
                        <p class="sec2-sub">{{ $svc['namespace'] }}</p>
                    </div>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <span class="sec2-chip" style="background:rgba(255,45,32,.08);color:#FF2D20;border-color:rgba(255,45,32,.20);">{{ count($svc['methods']??[]) }} methods</span>
                    @if(!empty($svc['dependencies']))<span class="sec2-chip" style="background:var(--bg-hover);color:var(--text-dim);border-color:var(--border);">{{ count($svc['dependencies']) }} deps</span>@endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="services-detail" style="display:none"><div id="services-detail-content"></div></div>
</section>

{{-- Repositories --}}
<section id="sec-repositories" class="p-6" style="display:none">
    <div id="repositories-list">
        @php
            $repoTotal    = count($data['repositories']);
            $repoMethods  = $repoTotal > 0 ? round(array_sum(array_map(fn($r) => count($r['methods']??[]), $data['repositories'])) / $repoTotal) : 0;
            $repoDeps     = array_sum(array_map(fn($r) => count($r['dependencies']??[]), $data['repositories']));
        @endphp
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $repoTotal }}</span>
                <span class="mds-top-stat-lbl">Repositories</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $repoMethods }}</span>
                <span class="mds-top-stat-lbl">Avg Methods</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $repoDeps }}</span>
                <span class="mds-top-stat-lbl">Total Deps</span>
            </div>
        </div>
        <div class="mds-toolbar">
            <input id="repositories-search" oninput="filterGrid('repositories')" type="search" placeholder="Search repositories…" style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;flex:1;max-width:240px;font-family:var(--font-mono);">
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ $repoTotal }} repositories</span>
        </div>
        @if(empty($data['repositories']))
        <div class="atlas-card" style="text-align:center;padding:48px;"><p style="color:var(--text-faint);">No repositories found in <code>app/Repositories</code></p></div>
        @else
        <div id="repositories-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;">
            @foreach($data['repositories'] as $i => $repo)
            @php $repoDotCount = min(count($repo['dependencies']??[]), 8); @endphp
            <div class="repo-card" style="--ci:{{$i}};background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;overflow:hidden;cursor:pointer;box-shadow:var(--shadow);transition:transform .2s,box-shadow .2s,border-color .2s;"
                 onclick="showDetail('repositories',{{$i}})" data-name="{{ strtolower($repo['name']) }}"
                 onmouseenter="this.style.borderColor='rgba(99,102,241,0.4)';this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 28px rgba(99,102,241,0.10)'"
                 onmouseleave="this.style.borderColor='var(--border)';this.style.transform='';this.style.boxShadow='var(--shadow)'">
                {{-- Top bar --}}
                <div style="height:5px;background:linear-gradient(90deg,var(--cyan),var(--cyan-bright));"></div>
                <div style="padding:18px;">
                    <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:14px;">
                        <div style="width:38px;height:38px;border-radius:10px;background:rgba(255,45,32,.10);color:#FF2D20;display:flex;align-items:center;justify-content:center;flex:none;border:1px solid rgba(99,102,241,.2);">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </div>
                        <div style="min-width:0;flex:1;">
                            <p style="font-weight:700;font-size:14px;color:var(--text);margin:0 0 3px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $repo['name'] }}</p>
                            <p style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $repo['namespace'] }}</p>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                        <span class="sec2-chip" style="background:rgba(255,45,32,.08);color:#FF2D20;border-color:rgba(255,45,32,.20);">{{ count($repo['methods']??[]) }} methods</span>
                        {{-- Dependency dots --}}
                        @if($repoDotCount > 0)
                        <div style="display:flex;align-items:center;gap:3px;" title="{{ count($repo['dependencies']??[]) }} dependencies">
                            @for($d=0;$d<$repoDotCount;$d++)
                            <div class="repo-dep-dot" style="opacity:{{ round(1 - ($d / max($repoDotCount,1)) * 0.5, 2) }};"></div>
                            @endfor
                            @if(count($repo['dependencies']??[]) > 8)
                            <span style="font-size:9px;color:var(--text-faint);font-family:var(--font-mono);">+{{ count($repo['dependencies'])-8 }}</span>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="repositories-detail" style="display:none">
        <div id="repositories-detail-content"></div>
    </div>
</section>

{{-- Observers --}}
<section id="sec-observers" class="p-6" style="display:none">
    <div id="observers-list">
        @php
            $obsModels = count(array_unique(array_filter(array_column($data['observers'], 'model'))));
        @endphp
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ count($data['observers']) }}</span>
                <span class="mds-top-stat-lbl">Total Observers</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $obsModels }}</span>
                <span class="mds-top-stat-lbl">Models Covered</span>
            </div>
        </div>
        <div class="mds-toolbar">
            <input id="observers-search" oninput="filterGrid('observers')" type="search" placeholder="Search observers…" style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;flex:1;max-width:240px;font-family:var(--font-mono);">
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ count($data['observers']) }} observers</span>
        </div>
        @if(empty($data['observers']))
        <div class="atlas-card" style="text-align:center;padding:48px;"><p style="color:var(--text-faint);">No observers found in <code>app/Observers</code></p></div>
        @else
        <div id="observers-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;">
            @foreach($data['observers'] as $i => $obs)
            <div class="sec2-card" style="--ci:{{$i}};border-left-color:#FF2D20;" onclick="showDetail('observers',{{$i}})" data-name="{{ strtolower($obs['name']) }}">
                <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:10px;">
                    <div class="sec2-icon" style="background:rgba(255,45,32,.10);color:#FF2D20;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <div style="min-width:0;flex:1;">
                        <p class="sec2-name">{{ $obs['name'] }}</p>
                        <p class="sec2-sub">observes: <span style="color:var(--text);">{{ $obs['model']??'Unknown' }}</span></p>
                    </div>
                </div>
                @if(!empty($obs['events']))
                <div style="display:flex;flex-wrap:wrap;gap:5px;">
                    @foreach($obs['events'] as $e)
                    <span class="sec2-chip" style="background:rgba(255,45,32,.08);color:#FF2D20;border-color:rgba(255,45,32,.20);">{{ $e }}</span>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="observers-detail" style="display:none"><div id="observers-detail-content"></div></div>
</section>

{{-- Policies --}}
<section id="sec-policies" class="p-6" style="display:none">
    <div id="policies-list">
        @php
            $polActions = array_sum(array_map(fn($p) => count($p['actions']??[]), $data['policies']));
        @endphp
        <div class="mds-top-stats">
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ count($data['policies']) }}</span>
                <span class="mds-top-stat-lbl">Total Policies</span>
            </div>
            <div class="mds-top-stat">
                <span class="mds-top-stat-num" style="color:#FF2D20;">{{ $polActions }}</span>
                <span class="mds-top-stat-lbl">Total Actions</span>
            </div>
        </div>
        <div class="mds-toolbar">
            <input id="policies-search" oninput="filterGrid('policies')" type="search" placeholder="Search policies…" style="border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:12px;flex:1;max-width:240px;font-family:var(--font-mono);">
            <span style="margin-left:auto;font-size:12px;color:var(--text-faint);font-family:var(--font-mono);">{{ count($data['policies']) }} policies</span>
        </div>
        @if(empty($data['policies']))
        <div class="atlas-card" style="text-align:center;padding:48px;"><p style="color:var(--text-faint);">No policies found in <code>app/Policies</code></p></div>
        @else
        <div id="policies-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;">
            @foreach($data['policies'] as $i => $pol)
            <div class="sec2-card" style="--ci:{{$i}};border-left-color:#FF2D20;" onclick="showDetail('policies',{{$i}})" data-name="{{ strtolower($pol['name']) }}">
                <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:10px;">
                    <div class="sec2-icon" style="background:rgba(255,45,32,.10);color:#FF2D20;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div style="min-width:0;flex:1;">
                        <p class="sec2-name">{{ $pol['name'] }}</p>
                        <p class="sec2-sub">guards: <span style="color:var(--text);">{{ $pol['model']??'Unknown' }}</span></p>
                    </div>
                </div>
                @if(!empty($pol['actions']))
                <div style="display:flex;flex-wrap:wrap;gap:5px;">
                    @foreach($pol['actions'] as $a)
                    <span class="sec2-chip" style="background:rgba(255,45,32,.08);color:#FF2D20;border-color:rgba(255,45,32,.20);">{{ $a }}</span>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div id="policies-detail" style="display:none"><div id="policies-detail-content"></div></div>
</section>

{{-- Dependencies --}}
<section id="sec-dependencies" class="p-6" style="display:none">
    <div class="sec-header" style="margin-bottom:24px;">
        <div class="sec-header__icon" style="background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.18);color:var(--cyan);">
            <svg viewBox="0 0 24 24"><path d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
        </div>
        <div>
            <h1 class="sec-header__title">Dependency Graph</h1>
            <p class="sec-header__sub">{{ count($data['dependencies']['nodes']??[]) }} nodes · {{ count($data['dependencies']['edges']??[]) }} edges — how your classes connect across layers</p>
        </div>
    </div>

    @php
    $depNodes = $data['dependencies']['nodes'] ?? [];
    $depEdges = $data['dependencies']['edges'] ?? [];

    // Layer order (top → bottom in the TD diagram)
    $lOrder  = ['controller','job','event','listener','service','repository','model','database'];
    $lLabels = [
        'controller' => 'Controllers',
        'job'        => 'Jobs',
        'event'      => 'Events',
        'listener'   => 'Listeners',
        'service'    => 'Services',
        'repository' => 'Repositories',
        'model'      => 'Models',
        'database'   => 'Database',
    ];
    $byLayer = array_fill_keys($lOrder, []);
    foreach ($depNodes as $n) {
        $l = $n['layer'] ?? 'model';
        if (isset($byLayer[$l])) $byLayer[$l][] = $n['name'];
    }

    // Edge label mapping
    $edgeLabel = ['injects' => '', 'uses' => 'uses', 'triggers' => 'triggers', 'persists' => 'persists'];

    // Build flowchart TD
    $fLines = ['flowchart TD'];

    foreach ($lOrder as $l) {
        if (empty($byLayer[$l])) continue;
        if ($l === 'database') {
            // Database uses cylinder shape — defined inline in edges, not in subgraph
            continue;
        }
        $fLines[] = '    subgraph ' . $lLabels[$l];
        foreach ($byLayer[$l] as $nm) { $fLines[] = '        ' . $nm; }
        $fLines[] = '    end';
    }

    // Database node (cylinder shape) — add outside subgraphs
    if (!empty($byLayer['database'])) {
        $fLines[] = '    Database[("Database")]';
    }

    // Edges with optional labels
    foreach ($depEdges as $e) {
        $label = $edgeLabel[$e['type'] ?? ''] ?? '';
        $arrow = $label ? "-->|\"{$label}\"|" : '-->';
        $fLines[] = "    {$e['from']} {$arrow} {$e['to']}";
    }

    // Class styles for each layer
    foreach ($depNodes as $n) {
        $fLines[] = "    class {$n['name']} {$n['layer']}";
    }

    $fLines[] = '    classDef controller fill:#EEF2FF,stroke:#6366F1,color:#172B4D';
    $fLines[] = '    classDef service    fill:#E3FCEF,stroke:#00875A,color:#172B4D';
    $fLines[] = '    classDef repository fill:#FFFAE6,stroke:#FF8B00,color:#172B4D';
    $fLines[] = '    classDef model      fill:#F3F0FF,stroke:#6554C0,color:#172B4D';
    $fLines[] = '    classDef job        fill:#FFF4E5,stroke:#FF8B00,color:#172B4D';
    $fLines[] = '    classDef event      fill:#FFF0FB,stroke:#BF40BF,color:#172B4D';
    $fLines[] = '    classDef listener   fill:#FEE4FA,stroke:#DA62AC,color:#172B4D';
    $fLines[] = '    classDef database   fill:#F4F5F7,stroke:#6B778C,color:#172B4D';

    $depCode = implode("\n", $fLines);

    // Layer counts for legend
    $lCounts = [];
    foreach ($depNodes as $n) { $lCounts[$n['layer']] = ($lCounts[$n['layer']] ?? 0) + 1; }

    $legendItems = [
        'controller' => ['Controllers', '#6366F1', '#EEF2FF'],
        'service'    => ['Services',    '#00875A', '#E3FCEF'],
        'repository' => ['Repositories','#FF8B00', '#FFFAE6'],
        'model'      => ['Models',      '#6554C0', '#F3F0FF'],
        'job'        => ['Jobs',        '#FF8B00', '#FFF4E5'],
        'event'      => ['Events',      '#BF40BF', '#FFF0FB'],
        'listener'   => ['Listeners',   '#DA62AC', '#FEE4FA'],
        'database'   => ['Database',    '#6B778C', '#F4F5F7'],
    ];
    @endphp

    @if(empty($depEdges))
    <div class="atlas-card" style="text-align:center;padding:48px;">
        <p style="color:var(--text-dim);font-weight:500;">No dependency edges found yet.</p>
        <p style="color:var(--text-faint);font-size:13px;margin-top:8px;">Add classes like <code>ProductService</code>, <code>ProductRepository</code> with constructor injection to see the graph.</p>
    </div>
    @else
    <div class="atlas-card" style="padding:0;overflow:hidden;">
        {{-- Legend + controls --}}
        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:12px 16px;padding:12px 16px;border-bottom:1px solid var(--border);background:var(--bg-hover);">
            @foreach($legendItems as $layer => [$label, $border, $bg])
            @if(isset($lCounts[$layer]))
            <span style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-dim);font-family:var(--font-mono);">
                <span style="width:10px;height:10px;border-radius:3px;border:1px solid {{ $border }};background:{{ $bg }};display:inline-block;"></span>
                {{ $label }} <span style="font-weight:700;color:var(--text);">{{ $lCounts[$layer] }}</span>
            </span>
            @endif
            @endforeach
            <div style="margin-left:auto;display:flex;align-items:center;gap:4px;">
                <button onclick="depZoom(0.15)" class="atlas-btn" style="width:28px;height:28px;padding:0;justify-content:center;font-size:16px;">+</button>
                <button onclick="depZoom(-0.15)" class="atlas-btn" style="width:28px;height:28px;padding:0;justify-content:center;font-size:16px;">−</button>
                <button onclick="depFit()" class="atlas-btn" style="width:28px;height:28px;padding:0;justify-content:center;" title="Fit all">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                </button>
                <button onclick="depClearHighlight()" class="atlas-btn" style="width:28px;height:28px;padding:0;justify-content:center;" title="Clear selection">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Hint bar --}}
        <div style="padding:6px 16px;background:var(--bg-hover);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
            <p style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);">Scroll to zoom · Drag to pan · Click a node to highlight connections</p>
            <span id="dep-sel-label" style="font-size:11px;color:var(--cyan);font-family:var(--font-mono);font-weight:600;display:none;"></span>
        </div>

        {{-- Custom SVG graph --}}
        <div style="position:relative;height:600px;">
            <svg id="dep-canvas" width="100%" height="100%" style="cursor:grab;background:var(--bg-sunken)">
                <defs>
                    <marker id="dep-arr" markerWidth="7" markerHeight="7" refX="5" refY="3" orient="auto">
                        <path d="M0,0 L0,6 L7,3 z" fill="rgba(148,178,222,0.5)"/>
                    </marker>
                    <marker id="dep-arr-hi" markerWidth="7" markerHeight="7" refX="5" refY="3" orient="auto">
                        <path d="M0,0 L0,6 L7,3 z" fill="#6366F1"/>
                    </marker>
                    <filter id="dep-shadow" x="-20%" y="-20%" width="140%" height="140%">
                        <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="rgba(23,43,77,0.10)" flood-opacity="1"/>
                    </filter>
                </defs>
                <g id="dep-vp">
                    <g id="dep-bands-g"></g>
                    <g id="dep-edges-g"></g>
                    <g id="dep-nodes-g"></g>
                </g>
            </svg>
        </div>
    </div>
    @endif
</section>

{{-- ══ MODULE EXPLORER ══ --}}
<section id="sec-modules" class="p-6" style="display:none">

    @php $modules = $data['modules'] ?? []; @endphp

    <div class="sec-header" style="margin-bottom:24px;">
        <div class="sec-header__icon" style="background:rgba(6,182,212,.10);border:1px solid rgba(6,182,212,.20);color:#06b6d4;">
            <svg viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <div>
            <h1 class="sec-header__title">Module Explorer</h1>
            <p class="sec-header__sub">{{ count($modules) }} module{{ count($modules) !== 1 ? 's' : '' }} detected@if(count($modules) > 0) · {{ array_sum(array_column($modules, 'controllers')) }} controllers · {{ array_sum(array_column($modules, 'models')) }} models · {{ array_sum(array_column($modules, 'routes')) }} routes@endif</p>
        </div>
    </div>

    @if(empty($modules))
    <div class="atlas-card" style="text-align:center;padding:64px;">
        <div style="width:56px;height:56px;background:var(--bg-hover);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg style="width:28px;height:28px;color:var(--text-faint);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <p style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:6px;">No modules detected</p>
        <p style="font-size:13px;color:var(--text-faint);">Create a <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">Modules/</code> directory at your project root with subfolders per module.</p>
        <p style="font-size:12px;color:var(--text-faint);margin-top:8px;">Compatible with <a style="color:var(--cyan);" href="https://nwidart.com/laravel-modules" target="_blank">nwidart/laravel-modules</a> structure.</p>
    </div>
    @else

    {{-- Summary bar --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:14px;margin-bottom:24px;">
        @php
        $totalCtrl  = array_sum(array_column($modules, 'controllers'));
        $totalModel = array_sum(array_column($modules, 'models'));
        $totalRoute = array_sum(array_column($modules, 'routes'));
        $totalSvc   = array_sum(array_column($modules, 'services'));
        @endphp
        <div class="kpi-card">
            <span class="kpi-card__label">Controllers</span>
            <span class="kpi-card__num" style="color:var(--violet);">{{ $totalCtrl }}</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-card__label">Models</span>
            <span class="kpi-card__num" style="color:var(--cyan);">{{ $totalModel }}</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-card__label">Routes</span>
            <span class="kpi-card__num" style="color:var(--emerald);">{{ $totalRoute }}</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-card__label">Services</span>
            <span class="kpi-card__num" style="color:var(--sky);">{{ $totalSvc }}</span>
        </div>
    </div>

    {{-- Module cards --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
        @php
        $modPalette = [
            ['color'=>'#A78BFA','bg'=>'rgba(167,139,250,.15)','border'=>'rgba(167,139,250,.3)'],
            ['color'=>'#6366F1','bg'=>'rgba(99,102,241,.15)','border'=>'rgba(99,102,241,.3)'],
            ['color'=>'#34D399','bg'=>'rgba(52,211,153,.15)','border'=>'rgba(52,211,153,.3)'],
            ['color'=>'#60A5FA','bg'=>'rgba(96,165,250,.15)','border'=>'rgba(96,165,250,.3)'],
            ['color'=>'#F87171','bg'=>'rgba(248,113,113,.15)','border'=>'rgba(248,113,113,.3)'],
            ['color'=>'#FBBF24','bg'=>'rgba(251,191,36,.15)','border'=>'rgba(251,191,36,.3)'],
            ['color'=>'#2DD4BF','bg'=>'rgba(45,212,191,.15)','border'=>'rgba(45,212,191,.3)'],
            ['color'=>'#FB923C','bg'=>'rgba(251,146,60,.15)','border'=>'rgba(251,146,60,.3)'],
            ['color'=>'#E879F9','bg'=>'rgba(232,121,249,.15)','border'=>'rgba(232,121,249,.3)'],
            ['color'=>'#38BDF8','bg'=>'rgba(56,189,248,.15)','border'=>'rgba(56,189,248,.3)'],
        ];
        @endphp
        @foreach($modules as $i => $mod)
        @php
        $mp      = $modPalette[$i % count($modPalette)];
        $initial = strtoupper(substr($mod['name'], 0, 1));
        $hasExtras = $mod['jobs'] > 0 || $mod['events'] > 0 || $mod['services'] > 0;
        @endphp
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:16px;overflow:hidden;display:flex;flex-direction:column;transition:border-color .2s,transform .2s;" onmouseenter="this.style.borderColor='{{ $mp['border'] }}';this.style.transform='translateY(-3px)'" onmouseleave="this.style.borderColor='var(--border)';this.style.transform=''">
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
                    <p style="font-size:20px;font-weight:800;color:var(--violet);font-family:var(--font-sans);">{{ $mod['controllers'] }}</p>
                    <p style="font-size:10px;color:var(--text-faint);font-family:var(--font-mono);margin-top:2px;">Controllers</p>
                </div>
                <div style="text-align:center;padding:12px 8px;border-right:1px solid var(--border);">
                    <p style="font-size:20px;font-weight:800;color:var(--cyan);font-family:var(--font-sans);">{{ $mod['models'] }}</p>
                    <p style="font-size:10px;color:var(--text-faint);font-family:var(--font-mono);margin-top:2px;">Models</p>
                </div>
                <div style="text-align:center;padding:12px 8px;">
                    <p style="font-size:20px;font-weight:800;color:var(--emerald);font-family:var(--font-sans);">{{ $mod['routes'] }}</p>
                    <p style="font-size:10px;color:var(--text-faint);font-family:var(--font-mono);margin-top:2px;">Routes</p>
                </div>
            </div>
            {{-- Extra chips --}}
            <div style="padding:12px 18px;display:flex;flex-wrap:wrap;gap:6px;">
                @if($mod['jobs'] > 0)
                <span style="display:inline-flex;align-items:center;gap:4px;font-size:10px;font-family:var(--font-mono);color:var(--amber);background:rgba(251,191,36,.1);padding:3px 9px;border-radius:20px;border:1px solid rgba(251,191,36,.2);">
                    <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9" stroke-width="2"/></svg>
                    {{ $mod['jobs'] }} Jobs
                </span>
                @endif
                @if($mod['events'] > 0)
                <span style="display:inline-flex;align-items:center;gap:4px;font-size:10px;font-family:var(--font-mono);color:var(--violet);background:rgba(167,139,250,.1);padding:3px 9px;border-radius:20px;border:1px solid rgba(167,139,250,.2);">
                    <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    {{ $mod['events'] }} Events
                </span>
                @endif
                @if($mod['services'] > 0)
                <span style="display:inline-flex;align-items:center;gap:4px;font-size:10px;font-family:var(--font-mono);color:var(--sky);background:rgba(96,165,250,.1);padding:3px 9px;border-radius:20px;border:1px solid rgba(96,165,250,.2);">
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

</section>

{{-- ══ PACKAGE DETECTION ══ --}}
<section id="sec-packages" class="p-6" style="display:none">

    @php
    $packages   = $data['packages'] ?? [];
    $byCategory = [];
    foreach ($packages as $pkg) {
        $byCategory[$pkg['category']][] = $pkg;
    }
    ksort($byCategory);

    $devCount = count(array_filter($packages, fn($p) => $p['dev']));

    $categoryMeta = [
        'Admin Panel'       => ['color'=>'#FBBF24','bg'=>'rgba(251,191,36,.1)','border'=>'#FBBF24','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>'],
        'API Authentication'=> ['color'=>'#60A5FA','bg'=>'rgba(96,165,250,.1)','border'=>'#60A5FA','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>'],
        'Architecture'      => ['color'=>'#A78BFA','bg'=>'rgba(167,139,250,.1)','border'=>'#A78BFA','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>'],
        'Audit'             => ['color'=>'#F87171','bg'=>'rgba(248,113,113,.1)','border'=>'#F87171','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'],
        'Auth Scaffolding'  => ['color'=>'#E879F9','bg'=>'rgba(232,121,249,.1)','border'=>'#E879F9','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
        'Authorization'     => ['color'=>'#34D399','bg'=>'rgba(52,211,153,.1)','border'=>'#34D399','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'],
        'Backup'            => ['color'=>'#34D399','bg'=>'rgba(52,211,153,.1)','border'=>'#34D399','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>'],
        'Debug'             => ['color'=>'#94A3B8','bg'=>'rgba(148,163,184,.1)','border'=>'#94A3B8','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>'],
        'Import / Export'   => ['color'=>'#34D399','bg'=>'rgba(52,211,153,.1)','border'=>'#34D399','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>'],
        'Media'             => ['color'=>'#818CF8','bg'=>'rgba(129,140,248,.1)','border'=>'#818CF8','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>'],
        'Payments'          => ['color'=>'#A78BFA','bg'=>'rgba(167,139,250,.1)','border'=>'#A78BFA','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>'],
        'PDF'               => ['color'=>'#F87171','bg'=>'rgba(248,113,113,.1)','border'=>'#F87171','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>'],
        'Queue Monitoring'  => ['color'=>'#2DD4BF','bg'=>'rgba(45,212,191,.1)','border'=>'#2DD4BF','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'],
        'Search'            => ['color'=>'#60A5FA','bg'=>'rgba(96,165,250,.1)','border'=>'#60A5FA','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>'],
        'UI Framework'      => ['color'=>'#A78BFA','bg'=>'rgba(167,139,250,.1)','border'=>'#A78BFA','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>'],
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

        {{-- Cards Grid --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;">
            @foreach($pkgs as $pkg)
            @php
                $dotHex  = $dotHexColors[$pkg['color']] ?? '#94A3B8';
                $initials = strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $pkg['name']), 0, 2))));
                $hasDoc  = !empty($pkg['docs']);
            @endphp
            <div class="pkg-card" data-name="{{ strtolower($pkg['name'] . ' ' . $pkg['key']) }}" style="--pkg-i:{{ $globalPkgIdx++ }};background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;overflow:hidden;display:flex;flex-direction:column;transition:border-color .2s,transform .2s,box-shadow .2s;" onmouseenter="this.style.borderColor='{{ $dotHex }}88';this.style.boxShadow='0 8px 28px {{ $dotHex }}22';" onmouseleave="this.style.borderColor='var(--border)';this.style.boxShadow='';">
                {{-- Colored top bar --}}
                <div style="height:6px;background:linear-gradient(90deg,{{ $dotHex }},{{ $dotHex }}99);"></div>

                <div style="padding:16px;display:flex;flex-direction:column;gap:12px;flex:1;">
                    {{-- Header row: avatar + name + badges --}}
                    <div style="display:flex;align-items:flex-start;gap:12px;">
                        {{-- Avatar --}}
                        <div style="width:42px;height:42px;border-radius:10px;background:{{ $dotHex }}18;border:1px solid {{ $dotHex }}33;display:flex;align-items:center;justify-content:center;flex:none;font-weight:800;font-size:13px;color:{{ $dotHex }};letter-spacing:.02em;">{{ $initials }}</div>
                        {{-- Name & badges --}}
                        <div style="min-width:0;flex:1;padding-top:2px;">
                            <p style="font-weight:700;font-size:14px;color:var(--text);line-height:1.3;margin-bottom:5px;">{{ $pkg['name'] }}</p>
                            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                                @if($pkg['version'])
                                <span class="pkg-ver-badge" style="font-family:var(--font-mono);font-size:10px;background:{{ $dotHex }}15;color:{{ $dotHex }};padding:2px 8px;border-radius:5px;border:1px solid {{ $dotHex }}33;font-weight:600;">v{{ $pkg['version'] }}</span>
                                @endif
                                @if($pkg['dev'])
                                <span style="font-family:var(--font-mono);font-size:10px;color:var(--amber);background:rgba(251,191,36,.12);padding:2px 8px;border-radius:5px;border:1px solid rgba(251,191,36,.25);font-weight:600;">dev-only</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <p style="font-size:12px;color:var(--text-dim);line-height:1.6;flex:1;">{{ $pkg['description'] }}</p>

                    {{-- Composer key --}}
                    <div style="background:var(--bg-hover);border:1px solid var(--border);border-radius:8px;padding:8px 10px;display:flex;align-items:center;justify-content:space-between;gap:8px;">
                        <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-faint);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $pkg['key'] }}</span>
                        <button onclick="copyPkgKey(this,'{{ $pkg['key'] }}')" title="Copy composer require command" style="flex:none;display:flex;align-items:center;gap:4px;background:transparent;border:none;color:var(--text-faint);cursor:pointer;padding:2px 4px;border-radius:4px;font-size:10px;transition:color .15s;" onmouseenter="this.style.color='var(--text)'" onmouseleave="this.style.color='var(--text-faint)'">
                            <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>

                    {{-- Docs link --}}
                    @if($hasDoc)
                    <a href="{{ $pkg['docs'] }}" target="_blank" rel="noopener" class="pkg-docs-btn" style="color:{{ $dotHex }};border-color:{{ $dotHex }}44;background:{{ $dotHex }}10;align-self:flex-start;" onmouseenter="this.style.background='{{ $dotHex }}20'" onmouseleave="this.style.background='{{ $dotHex }}10'">
                        <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        View Docs
                        <svg style="width:11px;height:11px;opacity:.7;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
    </div>{{-- /packages-categories --}}

    @endif

</section>

{{-- Export --}}
<section id="sec-export" class="p-6" style="display:none">
    <div class="sec-header" style="margin-bottom:30px;">
        <div class="sec-header__icon" style="background:rgba(0,135,90,.10);border:1px solid rgba(0,135,90,.20);color:var(--emerald);">
            <svg viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        </div>
        <div>
            <h1 class="sec-header__title">Export Architecture</h1>
            <p class="sec-header__sub">Download your architecture report in multiple formats for sharing, documentation, or archiving.</p>
        </div>
    </div>

    @php
    $exportPath = rtrim(request()->getSchemeAndHttpHost() . request()->getBasePath(), '/') . '/' . ltrim(config('laradar.dashboard.path', 'architecture'), '/');
    @endphp

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;max-width:880px;">

        {{-- JSON --}}
        <div class="export-card" style="--ei:0;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:10px;background:rgba(251,191,36,.12);border:1px solid rgba(251,191,36,.25);display:flex;align-items:center;justify-content:center;flex:none;">
                    <svg style="width:20px;height:20px;color:var(--amber);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p style="font-weight:700;font-size:14px;color:var(--text);">JSON</p>
                    <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);">architecture.json</p>
                </div>
            </div>
            <p style="font-size:13px;color:var(--text-dim);line-height:1.6;flex:1;">Full raw report data — all components, routes, dependencies, and scores in machine-readable format. Useful for CI pipelines and tooling integrations.</p>
            <div style="display:flex;gap:8px;margin-top:auto;">
                <button onclick="exportJson()" style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;background:rgba(251,191,36,.15);border:1px solid rgba(251,191,36,.3);color:var(--amber);font-size:12px;font-weight:600;padding:8px 16px;border-radius:8px;cursor:pointer;font-family:var(--font-mono);transition:background .15s;" onmouseenter="this.style.background='rgba(251,191,36,.25)'" onmouseleave="this.style.background='rgba(251,191,36,.15)'">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download
                </button>
                <button onclick="copyJson()" id="copy-json-btn" title="Copy to clipboard" style="padding:8px 12px;background:var(--bg-hover);border:1px solid var(--border);border-radius:8px;color:var(--text-faint);cursor:pointer;transition:border-color .15s;" onmouseenter="this.style.borderColor='var(--amber)'" onmouseleave="this.style.borderColor='var(--border)'">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </button>
            </div>
        </div>

        {{-- Markdown --}}
        <div class="export-card" style="--ei:1;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:10px;background:rgba(142,155,184,.1);border:1px solid rgba(142,155,184,.2);display:flex;align-items:center;justify-content:center;flex:none;">
                    <svg style="width:20px;height:20px;color:var(--text-dim);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p style="font-weight:700;font-size:14px;color:var(--text);">Markdown</p>
                    <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);">architecture.md</p>
                </div>
            </div>
            <p style="font-size:13px;color:var(--text-dim);line-height:1.6;flex:1;">Human-readable report with summary tables, model relationships, and a Mermaid dependency graph. Renders beautifully on GitHub and Notion.</p>
            <button onclick="exportMarkdown()" style="display:flex;align-items:center;justify-content:center;gap:6px;background:rgba(142,155,184,.1);border:1px solid rgba(142,155,184,.2);color:var(--text-dim);font-size:12px;font-weight:600;padding:8px 16px;border-radius:8px;cursor:pointer;font-family:var(--font-mono);margin-top:auto;transition:background .15s;" onmouseenter="this.style.background='rgba(142,155,184,.2)'" onmouseleave="this.style.background='rgba(142,155,184,.1)'">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download
            </button>
        </div>

        {{-- HTML --}}
        <div class="export-card" style="--ei:2;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:10px;background:rgba(251,146,60,.12);border:1px solid rgba(251,146,60,.25);display:flex;align-items:center;justify-content:center;flex:none;">
                    <svg style="width:20px;height:20px;color:#FB923C;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                </div>
                <div>
                    <p style="font-weight:700;font-size:14px;color:var(--text);">HTML</p>
                    <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);">architecture.html</p>
                </div>
            </div>
            <p style="font-size:13px;color:var(--text-dim);line-height:1.6;flex:1;">Fully self-contained HTML report. Open in any browser, attach to Jira tickets, or share with stakeholders with no server required.</p>
            <a href="{{ $exportPath }}/export/html" download="architecture.html" style="display:flex;align-items:center;justify-content:center;gap:6px;background:rgba(251,146,60,.15);border:1px solid rgba(251,146,60,.3);color:#FB923C;font-size:12px;font-weight:600;padding:8px 16px;border-radius:8px;cursor:pointer;font-family:var(--font-mono);margin-top:auto;text-decoration:none;transition:background .15s;" onmouseenter="this.style.background='rgba(251,146,60,.25)'" onmouseleave="this.style.background='rgba(251,146,60,.15)'">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download
            </a>
        </div>

        {{-- Graphic Report --}}
        <div class="export-card" style="--ei:3;border-width:2px;border-color:rgba(167,139,250,.35);position:relative;overflow:hidden;">
            <div style="position:absolute;top:0;right:0;background:var(--violet);color:#fff;font-size:10px;font-weight:800;font-family:var(--font-mono);padding:4px 10px;border-radius:0 0 0 10px;letter-spacing:.08em;">NEW</div>
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:10px;background:rgba(167,139,250,.12);border:1px solid rgba(167,139,250,.25);display:flex;align-items:center;justify-content:center;flex:none;">
                    <svg style="width:20px;height:20px;color:var(--violet);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p style="font-weight:700;font-size:14px;color:var(--text);">Graphic Report</p>
                    <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);">architecture-report.html</p>
                </div>
            </div>
            <p style="font-size:13px;color:var(--text-dim);line-height:1.6;flex:1;">Beautiful standalone HTML report with SVG charts, score gauge, route distribution, dependency graph, and full component tables. No server required.</p>
            <button onclick="exportGraphicHTML()" id="graphic-report-btn" style="display:flex;align-items:center;justify-content:center;gap:6px;background:rgba(167,139,250,.15);border:1px solid rgba(167,139,250,.3);color:var(--violet);font-size:12px;font-weight:600;padding:8px 16px;border-radius:8px;cursor:pointer;font-family:var(--font-mono);margin-top:auto;transition:background .15s,opacity .15s;" onmouseenter="if(!this.disabled)this.style.background='rgba(167,139,250,.25)'" onmouseleave="if(!this.disabled)this.style.background='rgba(167,139,250,.15)'">
                <svg id="graphic-report-icon" style="width:14px;height:14px;flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <svg id="graphic-report-spinner" style="width:14px;height:14px;flex:none;display:none;animation:spin .7s linear infinite;" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2.5" d="M12 2a10 10 0 0 1 10 10"/></svg>
                <span id="graphic-report-label">Generate &amp; Download</span>
            </button>
        </div>

    </div>

    {{-- CLI hint --}}
    <div style="margin-top:32px;max-width:660px;background:var(--bg-sunken);border:1px solid var(--border);border-radius:14px;padding:20px;box-shadow:var(--shadow);">
        <p style="font-size:12px;color:var(--text-faint);margin-bottom:12px;font-weight:600;font-family:var(--font-mono);">Export from the command line</p>
        <div style="display:flex;flex-direction:column;gap:8px;font-family:var(--font-mono);font-size:12px;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="color:var(--text-faint);">$</span>
                <span style="color:var(--emerald);">php artisan laradar:scan</span>
                <span style="color:var(--text-faint);font-size:11px;margin-left:4px;">— exports json + html (configured formats)</span>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="color:var(--text-faint);">$</span>
                <span style="color:var(--emerald);">php artisan laradar:scan <span style="color:var(--amber);">--format=svg</span></span>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="color:var(--text-faint);">$</span>
                <span style="color:var(--emerald);">php artisan laradar:scan <span style="color:var(--amber);">--format=markdown --output=docs/architecture.md</span></span>
            </div>
        </div>
    </div>


</section>

{{-- AI Insights --}}
<section id="sec-ai" class="p-6" style="display:none">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;flex-wrap:wrap;gap:12px;">
        <div class="sec-header" style="margin-bottom:0;">
            <div class="sec-header__icon" style="background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.18);color:var(--cyan);">
                <svg viewBox="0 0 24 24"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
            <div>
                <h1 class="sec-header__title">AI Insights</h1>
                <p class="sec-header__sub">AI-powered architecture review — score, SOLID analysis, code smells, and actionable suggestions.</p>
            </div>
        </div>
        @if(config('laradar.ai.enabled', false))
        <span style="display:flex;align-items:center;gap:6px;font-family:var(--font-mono);font-size:11px;color:var(--emerald);background:rgba(52,211,153,0.12);border:1px solid rgba(52,211,153,0.25);padding:5px 12px;border-radius:20px;">
            <span style="width:6px;height:6px;border-radius:50%;background:var(--emerald);"></span>
            AI Ready · {{ config('laradar.ai.model', 'gemini-2.5-flash') }}
        </span>
        @else
        <span style="display:flex;align-items:center;gap:6px;font-family:var(--font-mono);font-size:11px;color:var(--text-faint);background:var(--bg-hover);border:1px solid var(--border);padding:5px 12px;border-radius:20px;">
            <span style="width:6px;height:6px;border-radius:50%;background:var(--text-faint);"></span>
            AI Disabled
        </span>
        @endif
    </div>
    <div style="margin-bottom:24px;"></div>

    @if(!config('laradar.ai.enabled', false))
    {{-- Setup card --}}
    <div style="max-width:560px;background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.25);border-radius:12px;padding:24px;margin-bottom:24px;">
        <div style="display:flex;align-items:flex-start;gap:12px;">
            <svg style="width:20px;height:20px;color:var(--amber);margin-top:2px;flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <p style="font-weight:700;color:var(--amber);margin-bottom:8px;">AI is not enabled</p>
                <p style="font-size:13px;color:var(--text-dim);margin-bottom:12px;">To enable AI insights, add the following to your <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">.env</code> file and publish the config:</p>
                <div style="background:var(--bg-sunken);border-radius:8px;padding:12px;font-family:var(--font-mono);font-size:12px;color:var(--emerald);margin-bottom:10px;">
                    GEMINI_API_KEY=your_api_key_here
                </div>
                <p style="font-size:13px;color:var(--text-dim);margin-bottom:8px;">Then in <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">config/laradar.php</code>:</p>
                <div style="background:var(--bg-sunken);border-radius:8px;padding:12px;font-family:var(--font-mono);font-size:12px;color:var(--text);">
                    'ai' => [<br>
                    &nbsp;&nbsp;'enabled' => <span style="color:var(--emerald);">true</span>,<br>
                    &nbsp;&nbsp;'provider' => 'gemini',<br>
                    &nbsp;&nbsp;'model' => 'gemini-2.5-flash',<br>
                    ]
                </div>
                <p style="font-size:11px;color:var(--text-faint);margin-top:10px;font-family:var(--font-mono);">Get a free API key at aistudio.google.com</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Analyze button --}}
    <div id="ai-trigger" style="margin-bottom:28px;">
        <button onclick="aiAnalyze()" class="ai-analyze-btn" {{ !config('laradar.ai.enabled', false) ? 'disabled' : '' }}>
            <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            Analyze with AI
        </button>
        <p style="font-size:12px;color:var(--text-faint);margin-top:10px;font-family:var(--font-mono);">
            Sends your architecture to <span style="color:var(--cyan);">{{ config('laradar.ai.model', 'gemini-2.5-flash') }}</span> · Takes 10–30 seconds
        </p>
    </div>

    {{-- Loading state --}}
    <div id="ai-loading" style="display:none;margin-bottom:28px;">
        <div style="display:inline-flex;align-items:center;gap:14px;background:rgba(99,102,241,.06);border:1px solid rgba(99,102,241,.18);border-radius:12px;padding:14px 20px;">
            <svg style="width:22px;height:22px;color:var(--cyan);animation:aiSpin 1s linear infinite;flex:none;" fill="none" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="40 20" opacity=".3"></circle>
                <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" opacity=".8"></path>
            </svg>
            <div>
                <p style="font-size:13px;font-weight:700;color:var(--cyan);font-family:var(--font-mono);margin-bottom:2px;">Analyzing architecture…</p>
                <p style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);">Usually takes 10–30 seconds</p>
            </div>
        </div>
    </div>

    {{-- Error state --}}
    <div id="ai-error" style="display:none;margin-bottom:24px;max-width:560px;background:rgba(248,113,113,0.08);border:1px solid rgba(248,113,113,0.3);border-radius:12px;padding:16px;">
        <p style="font-size:13px;font-weight:600;color:var(--rose);margin-bottom:4px;">Analysis failed</p>
        <p id="ai-error-msg" style="font-size:12px;color:var(--rose);font-family:var(--font-mono);"></p>
    </div>

    {{-- Results --}}
    <div id="ai-results" style="display:none;max-width:900px;display:flex;flex-direction:column;gap:16px;">

        {{-- Summary + AI Score --}}
        <div style="display:flex;gap:16px;flex-wrap:wrap;">
            <div style="flex:1;min-width:240px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
                <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:10px;">AI Summary</p>
                <p id="ai-summary" style="font-size:13px;color:var(--text-dim);line-height:1.65;"></p>
            </div>
            <div style="width:160px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;flex:none;">
                <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);">AI Score</p>
                <div style="position:relative;width:90px;height:90px;">
                    <svg width="90" height="90" viewBox="0 0 90 90">
                        <circle cx="45" cy="45" r="36" fill="none" stroke="var(--bg-sunken)" stroke-width="7"/>
                        <circle id="ai-score-ring" class="ai-score-ring" cx="45" cy="45" r="36" fill="none"
                            stroke="var(--cyan)" stroke-width="7" stroke-linecap="round"
                            stroke-dasharray="226" stroke-dashoffset="226"
                            style="transition:stroke-dashoffset .9s cubic-bezier(.4,0,.2,1),stroke .4s;"/>
                    </svg>
                    <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                        <span id="ai-score-num" style="font-size:26px;font-weight:800;color:var(--cyan);font-family:var(--font-sans);line-height:1;"></span>
                        <span style="font-size:10px;color:var(--text-faint);font-family:var(--font-mono);">/100</span>
                    </div>
                </div>
                <div id="ai-score-bar" style="display:none;"></div>
            </div>
        </div>

        {{-- SOLID Review --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
            <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:14px;">SOLID Principles</p>
            <div id="ai-solid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;"></div>
        </div>

        {{-- Problems --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
            <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:14px;">Problems Detected</p>
            <div id="ai-problems" style="display:flex;flex-direction:column;gap:10px;"></div>
        </div>

        {{-- Suggestions --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
            <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:14px;">Suggestions</p>
            <div id="ai-suggestions" style="display:flex;flex-direction:column;gap:10px;"></div>
        </div>

        {{-- Laravel Best Practices --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
            <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:14px;">Laravel Best Practices</p>
            <div id="ai-laravel-practices" style="display:flex;flex-direction:column;gap:8px;"></div>
        </div>

        {{-- Best Practices (followed) --}}
        <div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:14px;padding:20px;">
            <p style="font-family:var(--font-mono);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);margin-bottom:12px;">Practices Already Followed</p>
            <ul id="ai-best-practices" style="display:flex;flex-direction:column;gap:6px;"></ul>
        </div>

        {{-- Re-analyze --}}
        <div style="display:flex;align-items:center;gap:12px;padding-top:4px;">
            <button onclick="aiAnalyze()" class="ai-analyze-btn" style="padding:9px 18px;font-size:12px;">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Re-analyze
            </button>
            <span id="ai-provider-badge" style="font-family:var(--font-mono);font-size:11px;color:var(--text-faint);"></span>
        </div>

    </div>

</section>

{{-- ══ AI CHAT ══ --}}
<section id="sec-chat" style="display:none;flex-direction:column;height:100%;padding:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div class="sec-header" style="margin-bottom:0;">
            <div class="sec-header__icon" style="background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.18);color:var(--cyan);">
                <svg viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <div>
                <h1 class="sec-header__title">AI Chat</h1>
                <p class="sec-header__sub">Ask anything about your architecture — only relevant context is sent to AI.</p>
            </div>
        </div>
        @if(config('laradar.ai.enabled', false))
        <span style="display:flex;align-items:center;gap:6px;font-family:var(--font-mono);font-size:11px;color:var(--emerald);background:rgba(52,211,153,0.12);border:1px solid rgba(52,211,153,0.25);padding:5px 12px;border-radius:20px;">
            <span style="width:6px;height:6px;border-radius:50%;background:var(--emerald);"></span>
            {{ config('laradar.ai.model') }}
        </span>
        @endif
    </div>

    @if(!config('laradar.ai.enabled', false))
    <div style="max-width:560px;background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.25);border-radius:10px;padding:16px;margin-bottom:20px;">
        <p style="font-size:13px;color:var(--amber);">AI is not enabled. Set <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">ai.enabled = true</code> and <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">GEMINI_API_KEY</code> in your .env.</p>
    </div>
    @endif

    {{-- Suggestion chips --}}
    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px;" id="chat-suggestions">
        <button onclick="chatSuggest('Which controller has the most methods?')" class="chat-suggestion-chip">
            <svg style="width:12px;height:12px;color:var(--amber);flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
            Which controller is largest?
        </button>
        <button onclick="chatSuggest('Trace the main request flow from route through controller to model.')" class="chat-suggestion-chip">
            <svg style="width:12px;height:12px;color:var(--emerald);flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
            Trace request flow
        </button>
        <button onclick="chatSuggest('Are there any SOLID principle violations?')" class="chat-suggestion-chip">
            <svg style="width:12px;height:12px;color:var(--rose);flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            SOLID violations?
        </button>
        <button onclick="chatSuggest('Which models have the most relationships?')" class="chat-suggestion-chip">
            <svg style="width:12px;height:12px;color:var(--violet);flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
            Model relationships
        </button>
        <button onclick="chatSuggest('What services should I extract from my controllers?')" class="chat-suggestion-chip">
            <svg style="width:12px;height:12px;color:#2DD4BF;flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Suggest service extractions
        </button>
        <button onclick="chatSuggest('Explain the overall architecture and data flow.')" class="chat-suggestion-chip">
            <svg style="width:12px;height:12px;color:var(--cyan);flex:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Overall architecture
        </button>
    </div>

    {{-- Messages --}}
    <div id="chat-messages" style="flex:1;overflow-y:auto;display:flex;flex-direction:column;gap:16px;margin-bottom:16px;max-height:calc(100vh - 420px);min-height:200px;padding-right:4px;">
        <div id="chat-empty" style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:200px;color:var(--text-faint);">
            <div style="width:56px;height:56px;border-radius:16px;background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.18);display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                <svg style="width:26px;height:26px;color:var(--cyan);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <p style="font-size:13px;font-weight:600;color:var(--text-dim);">Ask anything about your architecture</p>
            <p style="font-size:11px;color:var(--text-faint);margin-top:4px;">Use a suggestion above or type your own question</p>
        </div>
    </div>

    {{-- Input --}}
    <div style="border:1px solid var(--border);border-radius:12px;background:var(--bg-elevated);overflow:hidden;transition:border-color .2s,box-shadow .2s;" onfocusin="this.style.borderColor='rgba(99,102,241,.4)';this.style.boxShadow='0 0 0 3px rgba(99,102,241,.08)'" onfocusout="this.style.borderColor='var(--border)';this.style.boxShadow=''">
        <textarea id="chat-input" rows="2"
            placeholder="e.g. Trace the main request flow  •  Which controller is too large?  •  Where should I add a service?"
            {{ !config('laradar.ai.enabled', false) ? 'disabled' : '' }}
            oninput="chatPreviewContext(this.value)"
            onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();chatSend();}"
            style="width:100%;padding:14px 16px 6px;font-size:13px;color:var(--text);background:transparent;resize:none;outline:none;border:none;font-family:var(--font-sans);box-sizing:border-box;line-height:1.6;"></textarea>
        <div style="display:flex;align-items:center;justify-content:space-between;padding:6px 12px 10px;">
            <span id="chat-context-hint" style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:280px;"></span>
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:10px;color:var(--text-faint);font-family:var(--font-mono);">Enter to send</span>
                <button onclick="chatSend()" id="chat-send-btn" class="chat-send-btn" {{ !config('laradar.ai.enabled', false) ? 'disabled' : '' }}>
                    <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </div>
        </div>
    </div>
</section>

{{-- ══ AI DOCS ══ --}}
<section id="sec-aidocs" class="p-6" style="display:none">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div class="sec-header" style="margin-bottom:0;">
            <div class="sec-header__icon" style="background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.18);color:var(--cyan);">
                <svg viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div>
                <h1 class="sec-header__title">AI Documentation</h1>
                <p class="sec-header__sub">AI writes full markdown docs for each layer of your architecture. One click per file — or generate all at once.</p>
            </div>
        </div>
        @if(config('laradar.ai.enabled', false))
        <span style="display:flex;align-items:center;gap:6px;font-family:var(--font-mono);font-size:11px;color:var(--emerald);background:rgba(52,211,153,0.12);border:1px solid rgba(52,211,153,0.25);padding:5px 12px;border-radius:20px;">
            <span style="width:6px;height:6px;border-radius:50%;background:var(--emerald);"></span>
            {{ config('laradar.ai.model') }}
        </span>
        @endif
    </div>

    @if(!config('laradar.ai.enabled', false))
    <div style="max-width:560px;background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.25);border-radius:10px;padding:16px;margin-bottom:24px;">
        <p style="font-size:13px;color:var(--amber);">AI is not enabled. Set <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">ai.enabled = true</code> and <code style="background:var(--bg-hover);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);">GEMINI_API_KEY</code> in your .env.</p>
    </div>
    @endif

    <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:24px;">
        <button onclick="docsGenerateAll()"
            {{ !config('laradar.ai.enabled', false) ? 'disabled' : '' }}
            class="ai-analyze-btn"
            style="opacity:{{ config('laradar.ai.enabled', false) ? 1 : 0.4 }};cursor:{{ config('laradar.ai.enabled', false) ? 'pointer' : 'not-allowed' }}">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Generate All Docs
        </button>
        <button onclick="docsDownloadAll()" id="docs-download-all-btn" class="atlas-btn" style="display:none;padding:9px 18px;font-size:13px;border-radius:10px;">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Download All
        </button>

        {{-- AI Graphic Report button --}}
        <button onclick="generateAIGraphicReport()" id="ai-report-btn"
            {{ !config('laradar.ai.enabled', false) ? 'disabled' : '' }}
            class="atlas-btn"
            style="padding:9px 18px;font-size:13px;border-radius:10px;border-color:rgba(167,139,250,0.4);color:var(--violet);background:rgba(167,139,250,0.08);opacity:{{ config('laradar.ai.enabled', false) ? 1 : 0.4 }};cursor:{{ config('laradar.ai.enabled', false) ? 'pointer' : 'not-allowed' }}">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span id="ai-report-btn-label">Generate AI Graphic Report</span>
        </button>
    </div>

    {{-- AI Report progress panel --}}
    <div id="ai-report-progress" style="display:none;max-width:480px;margin-bottom:32px;border-radius:16px;overflow:hidden;border:1px solid rgba(167,139,250,0.3);background:var(--bg-elevated);">
        <div style="display:flex;align-items:center;gap:12px;padding:14px 20px;background:rgba(167,139,250,0.15);border-bottom:1px solid rgba(167,139,250,0.2);">
            <svg style="width:16px;height:16px;animation:spin 1s linear infinite;flex-shrink:0;color:var(--violet);" fill="none" viewBox="0 0 24 24" id="ai-report-spinner">
                <circle style="opacity:.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path style="opacity:.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <p style="font-size:13px;font-weight:600;color:var(--violet);font-family:var(--font-mono);" id="ai-report-progress-title">Generating AI Report…</p>
        </div>
        <div style="padding:16px 20px;display:flex;flex-direction:column;gap:10px;" id="ai-report-steps">
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="analyze">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Analyzing architecture with AI</span>
            </div>
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="architecture">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Generating Architecture documentation</span>
            </div>
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="models">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Generating Models documentation</span>
            </div>
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="controllers">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Generating Controllers documentation</span>
            </div>
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="routes">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Generating Routes documentation</span>
            </div>
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="services">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Generating Services documentation</span>
            </div>
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="modules">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Generating Modules documentation</span>
            </div>
            <div class="ai-step" style="display:flex;align-items:center;gap:12px;font-size:13px;" data-step="build">
                <span class="step-icon" style="width:14px;height:14px;border-radius:50%;border:2px solid var(--border-strong);flex-shrink:0;display:inline-block;"></span>
                <span style="color:var(--text-dim);">Building graphic report</span>
            </div>
        </div>
        <div id="ai-report-error" style="display:none;padding:0 20px 16px;font-size:13px;color:var(--rose);font-family:var(--font-mono);"></div>
    </div>

    {{-- Doc cards --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;max-width:880px;" id="docs-grid">

        @php
        $docTypes = [
            ['architecture', 'Architecture.md', 'Overall design, patterns, score, strengths & improvements.', '#4f46e5', 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
            ['models',       'Models.md',       'Each model — purpose, fields, relationships, business role.', '#8b5cf6', 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
            ['controllers',  'Controllers.md',  'Each controller — responsibilities, methods, observations.',  '#3b82f6', 'M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18'],
            ['routes',       'Routes.md',       'All routes grouped by resource — purpose, auth, middleware.',  '#10b981', 'M13 10V3L4 14h7v7l9-11h-7z'],
            ['services',     'Services.md',     'Service layer, repositories, jobs, and events explained.',    '#f59e0b', 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
            ['modules',      'Modules.md',      'Module breakdown — domain responsibilities and coupling.',    '#06b6d4', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ];
        @endphp

        @foreach($docTypes as [$type, $filename, $desc, $color, $icon])
        <div class="card" id="doc-card-{{ $type }}" style="padding:20px;display:flex;flex-direction:column;gap:12px;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:{{ $color }}18;border:1px solid {{ $color }}40">
                        <svg style="width:16px;height:16px;color:{{ $color }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                    </div>
                    <div>
                        <p style="font-weight:600;color:var(--text);font-size:13px;font-family:var(--font-mono);">{{ $filename }}</p>
                    </div>
                </div>
                <span id="doc-status-{{ $type }}" style="font-size:11px;color:var(--text-faint);flex-shrink:0;font-family:var(--font-mono);">Pending</span>
            </div>
            <p style="font-size:12px;color:var(--text-dim);line-height:1.6;">{{ $desc }}</p>

            {{-- Excerpt preview (shown after generation) --}}
            <div id="doc-excerpt-{{ $type }}" onclick="docsPreview('{{ $type }}')"
                style="display:none;padding:10px 13px;background:var(--bg-sunken);border-radius:9px;border:1px solid var(--border);font-size:11.5px;color:var(--text-dim);line-height:1.6;max-height:68px;overflow:hidden;position:relative;cursor:pointer;transition:border-color .2s;">
                <div id="doc-excerpt-text-{{ $type }}"></div>
                <div style="position:absolute;bottom:0;left:0;right:0;height:24px;background:linear-gradient(transparent,var(--bg-sunken));pointer-events:none;border-radius:0 0 9px 9px;"></div>
            </div>

            <div style="display:flex;gap:6px;margin-top:auto;padding-top:4px;flex-wrap:wrap;">
                <button onclick="docsGenerate('{{ $type }}')"
                    {{ !config('laradar.ai.enabled', false) ? 'disabled' : '' }}
                    id="doc-gen-btn-{{ $type }}"
                    class="atlas-btn atlas-btn--cyan"
                    style="flex:1;justify-content:center;font-size:11px;padding:7px 10px;border-radius:8px;opacity:{{ config('laradar.ai.enabled', false) ? 1 : 0.4 }};cursor:{{ config('laradar.ai.enabled', false) ? 'pointer' : 'not-allowed' }}">
                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    Generate
                </button>
                {{-- Preview button --}}
                <button onclick="docsPreview('{{ $type }}')"
                    id="doc-preview-btn-{{ $type }}"
                    class="atlas-btn"
                    style="display:none;align-items:center;justify-content:center;gap:5px;font-size:11px;padding:7px 10px;border-radius:8px;">
                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Preview
                </button>
                {{-- Download .md --}}
                <button onclick="docsDownload('{{ $type }}')"
                    id="doc-dl-btn-{{ $type }}"
                    class="atlas-btn"
                    style="display:none;align-items:center;justify-content:center;gap:5px;font-size:11px;padding:7px 10px;border-radius:8px;">
                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    .md
                </button>
                {{-- Download .html --}}
                <button onclick="docsDownloadHtml('{{ $type }}')"
                    id="doc-dl-html-btn-{{ $type }}"
                    class="atlas-btn atlas-btn--cyan"
                    style="display:none;align-items:center;justify-content:center;gap:5px;font-size:11px;padding:7px 10px;border-radius:8px;">
                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    .html
                </button>
            </div>
        </div>
        @endforeach

    </div>

</section>

{{-- ── Dead Code Section ───────────────────────────────────────────────────── --}}
@php
    $deadData    = $data['dead_code'] ?? ['items' => [], 'summary' => [], 'errors' => []];
    $deadItems   = $deadData['items']   ?? [];
    $deadSummary = $deadData['summary'] ?? [];
    $dTotal  = $deadSummary['total']             ?? 0;
    $dHigh   = $deadSummary['high']              ?? 0;
    $dMedium = $deadSummary['medium']            ?? 0;
    $dLow    = $deadSummary['low']               ?? 0;
    $dDebug  = $deadSummary['debug_statements']  ?? 0;
    $dComm   = $deadSummary['commented_code']    ?? 0;
    $dModels = $deadSummary['unused_models']     ?? 0;
    $dOrphan = $deadSummary['orphan_methods']    ?? 0;
    $dJobs   = $deadSummary['undispatched_jobs'] ?? 0;
    $dEvents = $deadSummary['unfired_events']    ?? 0;
    $dSvc    = $deadSummary['unused_services']   ?? 0;
    $dHighPct = $dTotal > 0 ? round($dHigh   / $dTotal * 100) : 0;
    $dMedPct  = $dTotal > 0 ? round($dMedium / $dTotal * 100) : 0;
    $dLowPct  = $dTotal > 0 ? max(0, 100 - $dHighPct - $dMedPct) : 0;
@endphp
<section id="sec-deadcode" class="p-6" style="display:none">

    {{-- ── Header — matches every other section ── --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div class="sec-header" style="margin-bottom:0;">
            <div class="sec-header__icon" style="background:rgba(239,68,68,.10);border:1px solid rgba(239,68,68,.20);color:#EF4444;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div>
                <h1 class="sec-header__title">Dead Code</h1>
                <p class="sec-header__sub">Debug calls, unused models, orphan methods, jobs, events & services — detected by static analysis</p>
            </div>
        </div>
    </div>

    {{-- ── Stats banner — same pattern as Jobs / Events / Services ── --}}
    <div class="sec-stats-banner" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
        {{-- Total --}}
        <div class="sec-stat-card">
            <div class="sec-stat-icon" style="background:rgba(239,68,68,.10);color:#EF4444;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div>
                <div class="sec-stat-num" style="color:#EF4444;">{{ $dTotal }}</div>
                <div class="sec-stat-lbl">Total Issues</div>
                {{-- Severity bar --}}
                <div class="dc-sev-bar">
                    @if($dHighPct>0)<div class="dc-sev-bar__seg" style="width:{{ $dHighPct }}%;background:#EF4444;"></div>@endif
                    @if($dMedPct>0)<div class="dc-sev-bar__seg" style="width:{{ $dMedPct }}%;background:#F59E0B;"></div>@endif
                    @if($dLowPct>0)<div class="dc-sev-bar__seg" style="width:{{ $dLowPct }}%;background:var(--sky);"></div>@endif
                    @if($dTotal===0)<div class="dc-sev-bar__seg" style="width:100%;background:var(--border);"></div>@endif
                </div>
            </div>
        </div>
        {{-- High --}}
        <div class="sec-stat-card" style="cursor:pointer;" onclick="dcSevFilter('high',document.querySelector('#dc-filter-row [data-sev=high]'))">
            <div class="sec-stat-icon" style="background:rgba(239,68,68,.10);color:#EF4444;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            </div>
            <div>
                <div class="sec-stat-num" style="color:#EF4444;">{{ $dHigh }}</div>
                <div class="sec-stat-lbl">High Severity</div>
            </div>
        </div>
        {{-- Medium --}}
        <div class="sec-stat-card" style="cursor:pointer;" onclick="dcSevFilter('medium',document.querySelector('#dc-filter-row [data-sev=medium]'))">
            <div class="sec-stat-icon" style="background:rgba(245,158,11,.10);color:#F59E0B;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div>
                <div class="sec-stat-num" style="color:#F59E0B;">{{ $dMedium }}</div>
                <div class="sec-stat-lbl">Medium Severity</div>
            </div>
        </div>
        {{-- Low --}}
        <div class="sec-stat-card" style="cursor:pointer;" onclick="dcSevFilter('low',document.querySelector('#dc-filter-row [data-sev=low]'))">
            <div class="sec-stat-icon" style="background:rgba(37,99,235,.10);color:var(--sky);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <div class="sec-stat-num" style="color:var(--sky);">{{ $dLow }}</div>
                <div class="sec-stat-lbl">Low Severity</div>
            </div>
        </div>
    </div>

    {{-- ── Type Grid ── --}}
    @php
        $typeCards = [
            ['emoji'=>'🐛','label'=>'Debug Calls',      'type'=>'debug_statement',  'count'=>$dDebug,  'color'=>'#EF4444'],
            ['emoji'=>'💬','label'=>'Commented Code',   'type'=>'commented_code',   'count'=>$dComm,   'color'=>'#F59E0B'],
            ['emoji'=>'📦','label'=>'Unused Models',    'type'=>'unused_model',     'count'=>$dModels, 'color'=>'#7C3AED'],
            ['emoji'=>'⚡','label'=>'Orphan Methods',   'type'=>'orphan_method',    'count'=>$dOrphan, 'color'=>'#F97316'],
            ['emoji'=>'📮','label'=>'Undispatched Jobs','type'=>'undispatched_job', 'count'=>$dJobs,   'color'=>'#0EA5E9'],
            ['emoji'=>'🔔','label'=>'Unfired Events',   'type'=>'unfired_event',    'count'=>$dEvents, 'color'=>'#10B981'],
            ['emoji'=>'🔧','label'=>'Unused Services',  'type'=>'unused_service',   'count'=>$dSvc,    'color'=>'#EC4899'],
        ];
    @endphp
    <div class="dc-type-grid" id="dc-type-grid">
        <div class="dc-type-card dc-type-active{{ $dTotal===0?' dc-type-zero':'' }}"
             data-type="all" onclick="dcTypeFilter('all',this)"
             style="animation:dcTypeIn .32s var(--ease) both;">
            <span class="tc-emoji">📋</span>
            <span class="tc-label">All Issues</span>
            <span class="tc-count">{{ $dTotal }}</span>
        </div>
        @foreach($typeCards as $tc)
        <div class="dc-type-card{{ $tc['count']===0?' dc-type-zero':'' }}"
             data-type="{{ $tc['type'] }}" onclick="dcTypeFilter('{{ $tc['type'] }}',this)"
             style="animation:dcTypeIn .32s var(--ease) both;animation-delay:{{ ($loop->index+1)*35 }}ms;">
            <span class="tc-emoji">{{ $tc['emoji'] }}</span>
            <span class="tc-label">{{ $tc['label'] }}</span>
            <span class="tc-count" style="color:{{ $tc['color'] }};">{{ $tc['count'] }}</span>
        </div>
        @endforeach
    </div>

    {{-- ── Severity Filter Row ── --}}
    <div class="dc-filter-row" id="dc-filter-row">
        <button class="dc-sev-tab dc-sev-tab--active" data-sev="all" onclick="dcSevFilter('all',this)">
            All <span style="font-family:var(--font-mono);font-size:11px;opacity:.65;">{{ $dTotal }}</span>
        </button>
        <button class="dc-sev-tab dc-sev-tab--high" data-sev="high" onclick="dcSevFilter('high',this)">
            <span style="width:7px;height:7px;border-radius:50%;background:#EF4444;flex:none;{{ $dHigh>0?'animation:severityPulse 1.8s ease infinite;':'' }}"></span>
            High <span style="font-family:var(--font-mono);font-size:11px;opacity:.65;">{{ $dHigh }}</span>
        </button>
        <button class="dc-sev-tab dc-sev-tab--medium" data-sev="medium" onclick="dcSevFilter('medium',this)">
            <span style="width:7px;height:7px;border-radius:50%;background:#F59E0B;flex:none;"></span>
            Medium <span style="font-family:var(--font-mono);font-size:11px;opacity:.65;">{{ $dMedium }}</span>
        </button>
        <button class="dc-sev-tab dc-sev-tab--low" data-sev="low" onclick="dcSevFilter('low',this)">
            <span style="width:7px;height:7px;border-radius:50%;background:var(--sky);flex:none;"></span>
            Low <span style="font-family:var(--font-mono);font-size:11px;opacity:.65;">{{ $dLow }}</span>
        </button>
    </div>

    {{-- ── Item List ── --}}
    @if(empty($deadItems))
    <div style="background:rgba(16,185,129,0.06);border:1px solid rgba(16,185,129,0.25);border-radius:14px;padding:48px;text-align:center;">
        <svg style="width:48px;height:48px;color:#10B981;margin:0 auto 14px;display:block;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <p style="font-size:17px;font-weight:800;color:#10B981;margin:0 0 6px;">Codebase is clean</p>
        <p style="font-size:13px;color:var(--text-faint);margin:0;">No dead code detected. Every class, method, job, and event appears to be in use.</p>
    </div>
    @else
    <div id="dead-list">
        @foreach($deadItems as $di)
        @php
            $sev       = $di['severity'] ?? 'low';
            $type      = $di['type'] ?? '';
            $sevColor  = match($sev) { 'high' => '#EF4444', 'medium' => '#F59E0B', default => '#2563EB' };
            $typeLabel = match($type) {
                'debug_statement'  => 'Debug Call',
                'commented_code'   => 'Commented Code',
                'unused_model'     => 'Unused Model',
                'orphan_method'    => 'Orphan Method',
                'undispatched_job' => 'Undispatched Job',
                'unfired_event'    => 'Unfired Event',
                'unused_service'   => 'Unused Service',
                default            => ucfirst(str_replace('_',' ',$type)),
            };
            $itemName   = $di['name'] ?? '';
            $location   = ($di['path'] ?? '') . (isset($di['line']) && $di['line'] ? ':' . $di['line'] : '');
            $hasSnippet = !empty($di['snippet']);
            $isOrphan   = $type === 'orphan_method';
            $methodOnly = $isOrphan ? ($di['method'] ?? $itemName) : null;
            $ctrlOnly   = $isOrphan ? (str_contains($itemName,'::') ? explode('::',$itemName)[0] : null) : null;
        @endphp
        <div class="dc-item"
             data-type="{{ $type }}"
             data-severity="{{ $sev }}"
             style="--di:{{ $loop->index }}">
            <div class="dc-item__accent" style="background:{{ $sevColor }};{{ $sev==='high' ? 'animation:severityPulse 1.8s ease infinite;' : '' }}"></div>
            <div class="dc-item__body">
                <div class="dc-item__head">
                    <div class="dc-item__badges">
                        <span style="font-size:10px;font-weight:700;background:{{ $sevColor }}18;color:{{ $sevColor }};border:1px solid {{ $sevColor }}30;border-radius:10px;padding:2px 9px;letter-spacing:.05em;text-transform:uppercase;">{{ $sev }}</span>
                        <span style="font-size:10.5px;font-weight:600;background:var(--bg-sunken);color:var(--text-dim);border:1px solid var(--border);border-radius:10px;padding:2px 9px;">{{ $typeLabel }}</span>
                    </div>
                    @if($location)
                    <button class="dc-copy-btn" onclick="dcCopyPath('{{ addslashes($location) }}',this)">
                        <svg style="width:10px;height:10px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                        Copy path
                    </button>
                    @endif
                </div>
                @if($isOrphan && $methodOnly)
                <div class="dc-item__name">
                    @if($ctrlOnly)<span style="color:var(--text-dim);font-weight:500;">{{ $ctrlOnly }}</span><span style="color:var(--border-strong);">::</span>@endif<span style="color:#F97316;">{{ $methodOnly }}</span><span style="color:var(--text-faint);">()</span>
                </div>
                @elseif($itemName)
                <div class="dc-item__name">{{ $itemName }}</div>
                @endif
                @if($location)
                <div class="dc-item__loc">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <span>{{ $location }}</span>
                </div>
                @endif
                @if(!empty($di['detail']))
                <div class="dc-item__detail">{{ $di['detail'] }}</div>
                @endif
                @if($hasSnippet)
                <pre class="dc-item__snippet" style="border-left-color:{{ $sevColor }};">{{ $di['snippet'] }}</pre>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</section>

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
const SECTIONS = ['overview','modules','packages','models','modelmap','controllers','routes','apidocs','services','repositories','observers','policies','dependencies','export','ai','chat','aidocs','deadcode'];

let depRendered     = false;
let mapTreeRendered = false;
let erRendered      = false;
let graphRendered   = false;

// ── ER Diagram — Mermaid erDiagram ──────────────────────────────────────────
const _erFull    = @json($mmErCode);
const _erFocused = @json($mmFocused);
const _erModels  = @json($data['models']);
let _erZoom = 1, _erPan = { x: 0, y: 0 }, _erLayout = 'TB';
let _erFsPan = { x: 0, y: 0 }, _erFsZoom = 1;
const _erPairCount = {{ count($mmErPairs) }}; // unique model pairs in the ER diagram

function erFocus(modelName) {
    const el = document.getElementById('er-mermaid');
    if (!el) return;
    const code = modelName === '__all__' ? _erFull : (_erFocused[modelName] || '');
    if (!code) return;
    const directive = `%%{init:{'er':{'layoutDirection':'${_erLayout}','diagramPadding':20,'minEntityWidth':100,'entityPadding':15}}}%%\n`;
    el.removeAttribute('data-processed');
    el.textContent = directive + code;
    if (window.mermaid) { mermaid.run({ nodes: [el] }); _erSchedulePostProcess(); }
    _erUpdateInfoPanel(modelName);
}

function initER() {
    if (erRendered) return;
    erRendered = true;
    _erInitPan();
    const el = document.getElementById('er-mermaid');
    if (!el) return;
    @if(count($data['models']) > 20 && $mmFirstFocusModel)
    erFocus('{{ $mmFirstFocusModel }}');
    const sel = document.getElementById('er-focus-select');
    if (sel) sel.value = '{{ $mmFirstFocusModel }}';
    @else
    // Inject layout directive into initial render
    const directive = `%%{init:{'er':{'layoutDirection':'TB','diagramPadding':20,'minEntityWidth':100,'entityPadding':15}}}%%\n`;
    el.removeAttribute('data-processed');
    el.textContent = directive + (el.textContent || '');
    if (window.mermaid) { mermaid.run({ nodes: [el] }); _erSchedulePostProcess(); }
    _erUpdateInfoPanel('__all__');
    @endif
}

function _erGetSVG() {
    return document.querySelector('#er-mermaid svg');
}

// ── ER model info panel ────────────────────────────────────────────────────────
function _erUpdateInfoPanel(modelName) {
    const el = document.getElementById('er-info-content');
    if (!el) return;

    if (modelName === '__all__') {
        const totalRels = _erPairCount;
        const rows = _erModels.map(m => {
            const rc = (m.relationships || []).length;
            return `<button onclick="document.getElementById('er-focus-select').value='${m.name}';erFocus('${m.name}')"
                style="display:flex;align-items:center;justify-content:space-between;width:100%;text-align:left;background:transparent;border:none;padding:5px 8px;border-radius:6px;font-size:11px;color:var(--text);cursor:pointer;font-family:var(--font-sans);transition:background .1s;"
                onmouseenter="this.style.background='#EEF2FF'" onmouseleave="this.style.background='transparent'">
                <span style="font-family:var(--font-mono);">${m.name}</span>
                ${rc ? `<span style="font-size:9px;color:#6366F1;background:rgba(99,102,241,.08);padding:1px 5px;border-radius:4px;">${rc}</span>` : ''}
            </button>`;
        }).join('');

        el.innerHTML = `<div style="display:flex;flex-direction:column;gap:14px;">
            <div style="display:flex;gap:8px;">
                <div style="flex:1;background:rgba(99,102,241,.06);border-radius:10px;padding:10px;text-align:center;">
                    <div style="font-size:20px;font-weight:800;color:#6366F1;line-height:1;">${_erModels.length}</div>
                    <div style="font-size:10px;color:#6366F1;margin-top:3px;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Models</div>
                </div>
                <div style="flex:1;background:#F0FDF4;border-radius:10px;padding:10px;text-align:center;">
                    <div style="font-size:20px;font-weight:800;color:#10B981;line-height:1;">${totalRels}</div>
                    <div style="font-size:10px;color:#10B981;margin-top:3px;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Relations</div>
                </div>
            </div>
            <div>
                <p style="font-size:10px;font-weight:700;color:var(--text-faint);letter-spacing:.06em;text-transform:uppercase;margin:0 0 6px;">Jump to Model</p>
                <div style="display:flex;flex-direction:column;gap:1px;">${rows}</div>
            </div>
        </div>`;
        return;
    }

    const model = _erModels.find(m => m.name === modelName);
    if (!model) { el.innerHTML = '<p style="font-size:12px;color:var(--text-faint);text-align:center;margin-top:32px;">No data found.</p>'; return; }

    const rels     = model.relationships || [];
    const fillable = model.fillable     || [];
    const casts    = model.casts        || {};
    const traits   = model.traits       || [];

    const relColors = {
        hasMany:        ['#3B82F6','#EFF6FF'], hasOne:        ['#10B981','#F0FDF4'],
        belongsTo:      ['#6366F1','rgba(99,102,241,.08)'], belongsToMany: ['#8B5CF6','#F5F3FF'],
        hasManyThrough: ['#EC4899','#FDF2F8'], hasOneThrough: ['#F59E0B','#FFFBEB'],
        morphTo:        ['#EF4444','#FEF2F2'], morphMany:     ['#EF4444','#FEF2F2'],
        morphOne:       ['#EF4444','#FEF2F2'], morphToMany:   ['#EF4444','#FEF2F2'],
    };

    const fieldsHtml = fillable.length ? `
        <div>
            <p style="font-size:10px;font-weight:700;color:var(--text-faint);letter-spacing:.06em;text-transform:uppercase;margin:0 0 6px;">Fields (${fillable.length})</p>
            <div style="display:flex;flex-direction:column;gap:2px;">
                ${fillable.map(f => {
                    const t = casts[f];
                    return `<div style="display:flex;align-items:center;justify-content:space-between;padding:4px 8px;background:#F8FAFC;border-radius:6px;border:1px solid #F1F5F9;">
                        <span style="font-size:11px;color:var(--text);font-family:var(--font-mono);">${f}</span>
                        ${t ? `<span style="font-size:9px;font-weight:600;color:#6366F1;background:rgba(99,102,241,.08);padding:1px 5px;border-radius:4px;">${t}</span>` : ''}
                    </div>`;
                }).join('')}
            </div>
        </div>` : '';

    const relsHtml = rels.length ? `
        <div>
            <p style="font-size:10px;font-weight:700;color:var(--text-faint);letter-spacing:.06em;text-transform:uppercase;margin:0 0 6px;">Relationships (${rels.length})</p>
            <div style="display:flex;flex-direction:column;gap:3px;">
                ${rels.map(r => {
                    const [c, bg] = relColors[r.type] || ['#6366F1','rgba(99,102,241,.08)'];
                    return `<div style="display:flex;align-items:center;gap:6px;padding:5px 8px;background:#F8FAFC;border-radius:6px;border:1px solid #F1F5F9;">
                        <span style="font-size:9px;font-weight:700;color:${c};background:${bg};padding:2px 5px;border-radius:4px;flex:none;white-space:nowrap;">${r.type}</span>
                        <span style="font-size:11px;color:var(--text);font-family:var(--font-mono);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${r.related}</span>
                    </div>`;
                }).join('')}
            </div>
        </div>` : '';

    const traitsHtml = traits.length ? `
        <div>
            <p style="font-size:10px;font-weight:700;color:var(--text-faint);letter-spacing:.06em;text-transform:uppercase;margin:0 0 6px;">Traits</p>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                ${traits.map(t => `<span style="font-size:10px;color:#64748B;background:#F1F5F9;padding:2px 7px;border-radius:5px;border:1px solid #E2E8F0;">${t}</span>`).join('')}
            </div>
        </div>` : '';

    el.innerHTML = `<div style="display:flex;flex-direction:column;gap:14px;">
        <div>
            <div style="font-size:14px;font-weight:800;color:var(--text);margin-bottom:6px;">${model.name}</div>
            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                <code style="font-size:10px;color:#6366F1;background:rgba(99,102,241,.08);padding:2px 7px;border-radius:5px;">${model.table}</code>
                ${model.observer ? `<span style="font-size:10px;color:#10B981;background:#F0FDF4;padding:2px 7px;border-radius:5px;">Observer</span>` : ''}
            </div>
        </div>
        ${fieldsHtml}${relsHtml}${traitsHtml}
    </div>`;
}

// ── ER zoom / pan ──────────────────────────────────────────────────────────────

function _erApplyTransform() {
    const w = document.getElementById('er-transform-wrap');
    if (w) w.style.transform = `translate(${_erPan.x}px,${_erPan.y}px) scale(${_erZoom})`;
}

function _erUpdateZoomLabel() {
    const lbl = document.getElementById('er-zoom-lbl');
    if (lbl) lbl.textContent = Math.round(_erZoom * 100) + '%';
}

function erZoom(factor) {
    const wrap = document.getElementById('er-canvas-wrap');
    const newZ = Math.max(0.2, Math.min(6, _erZoom * factor));
    if (wrap) {
        const cx = wrap.clientWidth  / 2;
        const cy = wrap.clientHeight / 2;
        _erPan.x = cx - (cx - _erPan.x) * (newZ / _erZoom);
        _erPan.y = cy - (cy - _erPan.y) * (newZ / _erZoom);
    }
    _erZoom = newZ;
    _erApplyTransform(); _erUpdateZoomLabel();
}

function erZoomFit() {
    _erZoom = 1; _erPan = { x: 0, y: 0 };
    _erApplyTransform(); _erUpdateZoomLabel();
}

function _erInitPan() {
    const wrap = document.getElementById('er-canvas-wrap');
    if (!wrap || wrap._erPanInit) return;
    wrap._erPanInit = true;
    let panning = false, ox = 0, oy = 0;

    wrap.addEventListener('mousedown', e => {
        if (e.button !== 0) return;
        panning = true; wrap.style.cursor = 'grabbing';
        ox = e.clientX - _erPan.x; oy = e.clientY - _erPan.y;
    });
    window.addEventListener('mousemove', e => {
        if (!panning) return;
        _erPan.x = e.clientX - ox; _erPan.y = e.clientY - oy;
        _erApplyTransform();
    });
    window.addEventListener('mouseup', () => {
        if (!panning) return; panning = false;
        if (wrap) wrap.style.cursor = 'grab';
    });
    wrap.addEventListener('wheel', e => {
        e.preventDefault();
        const rect = wrap.getBoundingClientRect();
        const mx = e.clientX - rect.left, my = e.clientY - rect.top;
        const f   = e.deltaY > 0 ? 0.9 : 1.11;
        const newZ = Math.max(0.2, Math.min(6, _erZoom * f));
        _erPan.x = mx - (mx - _erPan.x) * (newZ / _erZoom);
        _erPan.y = my - (my - _erPan.y) * (newZ / _erZoom);
        _erZoom  = newZ;
        _erApplyTransform(); _erUpdateZoomLabel();
    }, { passive: false });
}

// ── ER layout toggle ───────────────────────────────────────────────────────────

function erToggleLayout() {
    _erLayout = _erLayout === 'TB' ? 'LR' : 'TB';
    const btn = document.getElementById('er-layout-btn');
    if (btn) btn.childNodes[btn.childNodes.length - 1].textContent = ' ' + _erLayout;
    const sel = document.getElementById('er-focus-select');
    erFocus(sel ? sel.value : '__all__');
    _erZoom = 1; _erPan = { x: 0, y: 0 };
    _erApplyTransform(); _erUpdateZoomLabel();
}

// ── ER fullscreen ──────────────────────────────────────────────────────────────

function erFullScreen() {
    const svgEl = _erGetSVG();
    if (!svgEl) { alert('ER diagram not ready yet — wait a moment and try again.'); return; }
    const modal   = document.getElementById('er-fs-modal');
    const fsTrans = document.getElementById('er-fs-transform');
    if (!modal || !fsTrans) return;

    // Clone SVG at natural size
    const clone = svgEl.cloneNode(true);
    clone.removeAttribute('style');
    clone.style.display = 'block';
    const vb = svgEl.getAttribute('viewBox');
    if (vb) {
        const p = vb.trim().split(/[\s,]+/);
        if (p.length >= 4) { clone.setAttribute('width', p[2]); clone.setAttribute('height', p[3]); }
    }
    fsTrans.innerHTML = '';
    fsTrans.appendChild(clone);

    _erFsZoom = 1; _erFsPan = { x: 0, y: 0 };
    fsTrans.style.transform = '';
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Wire pan+zoom inside fullscreen
    const fsContent = document.getElementById('er-fs-content');
    if (fsContent && !fsContent._fsPanInit) {
        fsContent._fsPanInit = true;
        let panning = false, ox = 0, oy = 0;
        fsContent.addEventListener('mousedown', e => {
            if (e.button !== 0) return;
            panning = true; fsContent.style.cursor = 'grabbing';
            ox = e.clientX - _erFsPan.x; oy = e.clientY - _erFsPan.y;
        });
        window.addEventListener('mousemove', e => {
            if (!panning) return;
            _erFsPan.x = e.clientX - ox; _erFsPan.y = e.clientY - oy;
            fsTrans.style.transform = `translate(${_erFsPan.x}px,${_erFsPan.y}px) scale(${_erFsZoom})`;
        });
        window.addEventListener('mouseup', () => {
            if (!panning) return; panning = false;
            fsContent.style.cursor = 'grab';
        });
        fsContent.addEventListener('wheel', e => {
            e.preventDefault();
            const rect = fsContent.getBoundingClientRect();
            const mx = e.clientX - rect.left, my = e.clientY - rect.top;
            const f  = e.deltaY > 0 ? 0.9 : 1.11;
            const newZ = Math.max(0.15, Math.min(8, _erFsZoom * f));
            _erFsPan.x = mx - (mx - _erFsPan.x) * (newZ / _erFsZoom);
            _erFsPan.y = my - (my - _erFsPan.y) * (newZ / _erFsZoom);
            _erFsZoom  = newZ;
            fsTrans.style.transform = `translate(${_erFsPan.x}px,${_erFsPan.y}px) scale(${_erFsZoom})`;
        }, { passive: false });
    }
}

function erCloseFullScreen() {
    const modal = document.getElementById('er-fs-modal');
    if (modal) modal.style.display = 'none';
    document.body.style.overflow = '';
}

// Close fullscreen on Escape
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') erCloseFullScreen();
});

// ── ER CSS post-processing (Option B — style injection only, no DOM mutation) ──
let _erPostTimer = null;
function _erSchedulePostProcess() {
    clearTimeout(_erPostTimer);
    _erPostTimer = setTimeout(_erPostProcess, 350);
}

function _erPostProcess() {
    const svg = _erGetSVG();
    if (!svg) return;

    // Ensure defs block exists for the style element
    let defs = svg.querySelector('defs');
    if (!defs) {
        defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
        svg.prepend(defs);
    }

    // Remove any previous injection so re-renders start clean
    const prev = svg.querySelector('#er-pp-style');
    if (prev) prev.remove();

    const styleEl = document.createElementNS('http://www.w3.org/2000/svg', 'style');
    styleEl.id = 'er-pp-style';
    styleEl.textContent = `
        /* Entity header — Laravel Red fill, rounded top corners, drop shadow */
        .er.entityBox {
            fill: #6366F1 !important;
            stroke: #4338CA !important;
            stroke-width: 1.5px !important;
            rx: 10px !important;
            ry: 10px !important;
            filter: drop-shadow(0 4px 12px rgba(99,102,241,0.22)) !important;
        }
        /* Entity name — white bold on red */
        .er.entityLabel {
            fill: #FFFFFF !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            font-family: 'Inter', ui-sans-serif, sans-serif !important;
            letter-spacing: 0.3px !important;
        }
        /* Even attribute rows — very light indigo tint */
        .er.attributeBoxEven {
            fill: #FFFFFF !important;
            stroke: #C7D2FE !important;
            stroke-width: 1px !important;
        }
        /* Odd attribute rows — slightly deeper tint */
        .er.attributeBoxOdd {
            fill: #EEF2FF !important;
            stroke: #C7D2FE !important;
            stroke-width: 1px !important;
        }
        /* Attribute name — dark indigo ink */
        .er.attributeName {
            fill: #1E1B4B !important;
            font-size: 11px !important;
            font-family: 'Inter', ui-sans-serif, sans-serif !important;
            font-weight: 500 !important;
        }
        /* Attribute type — medium indigo, smaller, semibold */
        .er.attributeType {
            fill: #818CF8 !important;
            font-size: 10px !important;
            font-weight: 600 !important;
            font-family: 'Inter', ui-sans-serif, sans-serif !important;
        }
        /* Relationship lines — soft indigo, slightly thicker */
        .er.relationshipLine {
            stroke: #818CF8 !important;
            stroke-width: 1.5px !important;
        }
        /* Cardinality labels */
        .er.relationshipLabel {
            fill: #4338CA !important;
            font-size: 10px !important;
            font-weight: 600 !important;
            font-family: 'Inter', ui-sans-serif, sans-serif !important;
        }
    `;
    defs.appendChild(styleEl);
}

function erDownloadSVG() {
    _erPostProcess(); // ensure styles are applied before cloning
    const svgEl = _erGetSVG();
    if (!svgEl) { alert('ER diagram not ready yet — wait a moment and try again.'); return; }

    const clone = svgEl.cloneNode(true);
    clone.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
    clone.setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');

    // Ensure explicit px dimensions so the file opens at a readable size
    const vb = clone.getAttribute('viewBox');
    if (vb) {
        const p = vb.trim().split(/[\s,]+/);
        if (p.length >= 4) { clone.setAttribute('width', p[2]); clone.setAttribute('height', p[3]); }
    }

    // White background rect so it looks clean in any viewer
    const bg = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
    bg.setAttribute('width', '100%'); bg.setAttribute('height', '100%'); bg.setAttribute('fill', '#FFFFFF');
    clone.insertBefore(bg, clone.firstChild);

    const svgStr = '<?xml version="1.0" encoding="UTF-8"?>\n' + new XMLSerializer().serializeToString(clone);
    const blob = new Blob([svgStr], { type: 'image/svg+xml;charset=utf-8' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href = url; a.download = 'er-diagram.svg'; a.click();
    setTimeout(() => URL.revokeObjectURL(url), 200);
}

function erDownloadPNG() {
    _erPostProcess(); // ensure styles are applied before rasterising
    const svgEl = _erGetSVG();
    if (!svgEl) { alert('ER diagram not ready yet — wait a moment and try again.'); return; }

    const btn = document.getElementById('er-dl-png');
    const _restore = () => {
        if (btn) {
            btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;flex:none;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> PNG';
            btn.disabled = false;
        }
    };
    if (btn) { btn.textContent = 'Generating…'; btn.disabled = true; }

    const clone = svgEl.cloneNode(true);
    clone.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
    clone.setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');

    // Derive natural SVG size from viewBox for best resolution
    let W = svgEl.getBoundingClientRect().width  || 1200;
    let H = svgEl.getBoundingClientRect().height || 800;
    const vb = svgEl.getAttribute('viewBox');
    if (vb) {
        const p = vb.trim().split(/[\s,]+/);
        if (p.length >= 4) { W = parseFloat(p[2]) || W; H = parseFloat(p[3]) || H; }
    }
    clone.setAttribute('width', W); clone.setAttribute('height', H);

    const bg = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
    bg.setAttribute('width', '100%'); bg.setAttribute('height', '100%'); bg.setAttribute('fill', '#FFFFFF');
    clone.insertBefore(bg, clone.firstChild);

    const scale  = 2; // 2× = good balance of resolution vs file size
    const canvas = document.createElement('canvas');
    canvas.width  = Math.round(W * scale);
    canvas.height = Math.round(H * scale);
    const ctx = canvas.getContext('2d');
    ctx.fillStyle = '#FFFFFF'; ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.scale(scale, scale);

    const svgStr = new XMLSerializer().serializeToString(clone);
    const blob   = new Blob([svgStr], { type: 'image/svg+xml;charset=utf-8' });
    const url    = URL.createObjectURL(blob);
    const img    = new Image();
    img.onload = () => {
        ctx.drawImage(img, 0, 0, W, H);
        URL.revokeObjectURL(url);
        const a = document.createElement('a');
        a.download = 'er-diagram.png'; a.href = canvas.toDataURL('image/png'); a.click();
        _restore();
    };
    img.onerror = () => { URL.revokeObjectURL(url); _restore(); alert('PNG export failed — try SVG instead.'); };
    img.src = url;
}

let _navTimer = null;

function _moveNavIndicator(s) {
    const ind  = document.getElementById('nav-indicator');
    const item = document.getElementById('nav-' + s);
    if (!ind || !item) return;
    const r = item.getBoundingClientRect();
    ind.style.top    = r.top + 'px';
    ind.style.height = r.height + 'px';
}

function navigate(s) {
    if (_navTimer) { clearTimeout(_navTimer); _navTimer = null; }

    // Auto-close sidebar on mobile after nav click
    const sb = document.querySelector('.sidebar');
    if (sb && sb.classList.contains('is-open')) toggleSidebar();

    // Update nav highlight + breadcrumb immediately
    SECTIONS.forEach(id => {
        const nav = document.getElementById('nav-' + id);
        if (nav) nav.classList.toggle('nav-active', id === s);
    });
    _moveNavIndicator(s);
    const sectionNames = {
        overview:'Overview', models:'Models', modelmap:'Relation Graph', controllers:'Controllers',
        routes:'Routes', apidocs:'API Docs', services:'Services',
        repositories:'Repositories', observers:'Observers', policies:'Policies',
        dependencies:'Dependencies', export:'Export', ai:'AI Insights', chat:'AI Chat',
        aidocs:'AI Docs', modules:'Modules', packages:'Packages', deadcode:'Dead Code'
    };
    const breadcrumb = document.getElementById('topbar-section');
    if (breadcrumb) breadcrumb.textContent = sectionNames[s] || s;

    // Find the currently visible section to fade out
    let outSec = null;
    SECTIONS.forEach(id => {
        if (id === s) return;
        const sec = document.getElementById('sec-' + id);
        if (sec && sec.style.display !== 'none' && sec.style.display !== '') outSec = sec;
    });

    const _show = () => {
        SECTIONS.forEach(id => {
            const sec = document.getElementById('sec-' + id);
            if (!sec) return;
            if (id === s) {
                sec.style.display = id === 'chat' ? 'flex' : 'block';
                sec.classList.remove('sec-out', 'sec-fade');
                void sec.offsetWidth;
                sec.classList.add('sec-fade');
            } else {
                sec.style.display = 'none';
                sec.classList.remove('sec-out');
            }
        });
        if (s === 'dependencies' && !depRendered) {
            depRendered = true;
            requestAnimationFrame(() => requestAnimationFrame(initDepGraph));
        }
        if (s === 'modelmap' && !graphRendered) {
            setTimeout(initRelGraph, 50);
        }
        if (s === 'deadcode') {
            _dcActiveType = 'all';
            _dcActiveSev  = 'all';
            document.querySelectorAll('#dc-type-grid .dc-type-card').forEach(c => c.classList.remove('dc-type-active'));
            const allTypeCard = document.querySelector('#dc-type-grid .dc-type-card[data-type="all"]');
            if (allTypeCard) allTypeCard.classList.add('dc-type-active');
            document.querySelectorAll('#dc-filter-row .dc-sev-tab').forEach(t => t.classList.remove('dc-sev-tab--active'));
            const allSevTab = document.querySelector('#dc-filter-row .dc-sev-tab[data-sev="all"]');
            if (allSevTab) allSevTab.classList.add('dc-sev-tab--active');
            document.querySelectorAll('#dead-list .dc-item').forEach(item => {
                item.style.display = '';
                item.classList.remove('is-hiding');
            });
        }
        if (s === 'controllers') {
            setTimeout(() => {
                document.querySelectorAll('.ctrl-complexity-fill').forEach(bar => {
                    bar.style.width = (bar.dataset.target || 0) + '%';
                });
            }, 200);
        }

        if (s === 'overview') {
            setTimeout(() => {
                // Ensure ov-reveal cards are visible when returning to overview
                document.querySelectorAll('#sec-overview [data-ov-reveal]').forEach(el => {
                    el.classList.add('ov-in');
                    el.querySelectorAll('.kpi-card__num[data-count]').forEach(n => {
                        _countUp(n, +n.dataset.count, 900);
                    });
                });
                // Re-trigger hc-row stagger animation
                document.querySelectorAll('#sec-overview .hc-row').forEach(el => {
                    el.style.animation = 'none';
                    void el.offsetWidth;
                    el.style.animation = '';
                });
            }, 80);
        }
    };

    if (outSec) {
        outSec.classList.add('sec-out');
        _navTimer = setTimeout(() => { _navTimer = null; _show(); }, 150);
    } else {
        _show();
    }
}

function _atlasTheme(el) {
    // Light theme: Tailwind's native colours are correct — no post-processing needed.
}

const _SECTION_LABELS = {
    models:'Models', controllers:'Controllers', routes:'Route Explorer',
    services:'Services',
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
    const q   = document.getElementById('routes-search').value.toLowerCase();
    const mf  = document.getElementById('routes-method-filter').value;
    const mwf = document.getElementById('routes-mw-filter').value.toLowerCase();
    document.querySelectorAll('.route-row').forEach(row => {
        const handler  = (row.querySelector('td:nth-child(3)')?.textContent || '').toLowerCase();
        const textOk   = !q || row.dataset.uri.includes(q) || handler.includes(q);
        const methodOk = !mf  || row.dataset.methods.includes(mf);
        const mwOk     = !mwf || row.dataset.mw.includes(mwf);
        row.style.display = textOk && methodOk && mwOk ? '' : 'none';
    });
}

// ── Route Explorer ────────────────────────────────────────────────────────────

const MW_DESC = {
    web: 'Web Session / CSRF', api: 'Stateless API', auth: 'Requires Authentication',
    'auth:sanctum': 'Sanctum Token Auth', 'auth:api': 'API Token Auth',
    guest: 'Redirect if Logged In', verified: 'Email Verified Required',
    signed: 'Requires Signed URL', throttle: 'Rate Limiting',
    can: 'Authorization Gate', bindings: 'Route Model Binding',
    cache: 'Response Caching', cors: 'CORS Headers',
};

function mwDesc(mw) {
    return MW_DESC[mw] || MW_DESC[mw.split(':')[0]] || null;
}

function buildDepGraph() {
    const nodeMap = {}, edgeMap = {};
    for (const n of (APP.dependencies?.nodes || [])) nodeMap[n.name] = n;
    for (const e of (APP.dependencies?.edges || [])) {
        if (!edgeMap[e.from]) edgeMap[e.from] = [];
        edgeMap[e.from].push(e);
    }
    return { nodeMap, edgeMap };
}

function traceChain(startName) {
    const { nodeMap, edgeMap } = buildDepGraph();
    const chain = [], visited = new Set();

    function dfs(name, edgeType) {
        if (visited.has(name)) return;
        visited.add(name);
        chain.push({ name, layer: nodeMap[name]?.layer || 'unknown', edgeType });
        for (const e of (edgeMap[name] || [])) dfs(e.to, e.type);
    }
    dfs(startName, null);

    // ── Smart inference fallback ──────────────────────────────────────────────
    // Runs whenever the chain has no service/repo/model.
    // Works for any naming pattern: UserController, PaymentAnalytics, OrderPage, etc.
    if (!chain.some(n => ['service','repository','model'].includes(n.layer))) {
        // Strip common non-domain suffixes to get the entity base words
        const stripped = startName
            .replace(/Controller|Page|Resource|Component|Widget|Action|Form|Table|Livewire|Panel$/g, '');

        // Split PascalCase → individual words: "PaymentAnalytics" → ["Payment","Analytics"]
        const words = stripped.match(/[A-Z][a-z0-9]*/g) || [stripped];
        // Build a ranked candidate list: [full, longest-word, each-word]
        const candidates = [stripped, ...words.sort((a,b) => b.length - a.length)];

        const depNodes = APP.dependencies?.nodes || [];
        const models   = APP.models || [];

        // Score helper: how well does a node name match the candidate keywords?
        const score = (nodeName, layer) => {
            const n = nodeName.toLowerCase().replace(/(service|repository|repo)$/i,'');
            for (let i = 0; i < candidates.length; i++) {
                const c = candidates[i].toLowerCase();
                if (n === c)           return 100 - i;   // exact match
                if (n.startsWith(c))   return  80 - i;   // prefix match
                if (n.includes(c))     return  60 - i;   // contains
                if (c.includes(n) && n.length >= 3) return 40 - i; // reverse contains
            }
            return 0;
        };

        // Find best-scoring service
        if (!chain.some(n => n.layer === 'service')) {
            const best = depNodes
                .filter(n => n.layer === 'service')
                .map(n => ({ n, s: score(n.name, 'service') }))
                .filter(x => x.s > 0)
                .sort((a,b) => b.s - a.s)[0];
            if (best && !visited.has(best.n.name)) {
                chain.push({ name: best.n.name, layer: 'service', edgeType: 'injects', _inferred: true });
                visited.add(best.n.name);
                for (const e of (edgeMap[best.n.name] || [])) dfs(e.to, e.type);
            }
        }

        // Find best-scoring repository
        if (!chain.some(n => n.layer === 'repository')) {
            const best = depNodes
                .filter(n => n.layer === 'repository')
                .map(n => ({ n, s: score(n.name, 'repository') }))
                .filter(x => x.s > 0)
                .sort((a,b) => b.s - a.s)[0];
            if (best && !visited.has(best.n.name)) {
                chain.push({ name: best.n.name, layer: 'repository', edgeType: 'uses', _inferred: true });
                visited.add(best.n.name);
                for (const e of (edgeMap[best.n.name] || [])) dfs(e.to, e.type);
            }
        }

        // Find best-scoring model
        if (!chain.some(n => n.layer === 'model')) {
            const best = models
                .map(m => ({ m, s: score(m.name, 'model') }))
                .filter(x => x.s > 0)
                .sort((a,b) => b.s - a.s)[0];
            if (best && !visited.has(best.m.name)) {
                chain.push({ name: best.m.name, layer: 'model', edgeType: 'uses', _inferred: true });
                visited.add(best.m.name);
            }
        }
    }

    return chain;
}

const LAYER_STYLE = {
    controller: { color:'#60A5FA', bg:'rgba(96,165,250,.12)',  border:'rgba(96,165,250,.3)',  dot:'#60A5FA' },
    service:    { color:'#34D399', bg:'rgba(52,211,153,.12)',  border:'rgba(52,211,153,.3)',  dot:'#34D399' },
    repository: { color:'#FBBF24', bg:'rgba(251,191,36,.12)',  border:'rgba(251,191,36,.3)',  dot:'#FBBF24' },
    model:      { color:'#A78BFA', bg:'rgba(167,139,250,.12)', border:'rgba(167,139,250,.3)', dot:'#A78BFA' },
    job:        { color:'#FB923C', bg:'rgba(251,146,60,.12)',  border:'rgba(251,146,60,.3)',  dot:'#FB923C' },
    event:      { color:'#E879F9', bg:'rgba(232,121,249,.12)', border:'rgba(232,121,249,.3)', dot:'#E879F9' },
    listener:   { color:'#F472B6', bg:'rgba(244,114,182,.12)', border:'rgba(244,114,182,.3)', dot:'#F472B6' },
    database:   { color:'#6B778C', bg:'rgba(142,155,184,.12)', border:'rgba(142,155,184,.3)', dot:'#6B778C' },
    unknown:    { color:'#6B778C', bg:'rgba(91,103,133,.12)',  border:'rgba(91,103,133,.3)',  dot:'#6B778C' },
};

const EDGE_LABEL = { injects: 'injects', uses: 'uses', triggers: 'triggers', persists: 'persists' };

// ── Route Graph Explorer ───────────────────────────────────────────────────────

const RF_COLOR = {
    // Atlassian light theme: white bg, coloured border/stroke, navy text
    request:    { bg:'#EEF2FF', border:'#6366F1', type:'#6366F1', name:'#172B4D', sub:'#6366F1',  dot:'#6366F1' },
    middleware: { bg:'#FFFAE6', border:'#FF8B00', type:'#FF8B00', name:'#172B4D', sub:'#FF8B00',  dot:'#FF8B00' },
    controller: { bg:'#EEF2FF', border:'#6366F1', type:'#6366F1', name:'#172B4D', sub:'#6366F1',  dot:'#6366F1' },
    service:    { bg:'#E3FCEF', border:'#00875A', type:'#00875A', name:'#172B4D', sub:'#00875A',  dot:'#00875A' },
    repository: { bg:'#FFFAE6', border:'#FF8B00', type:'#FF8B00', name:'#172B4D', sub:'#FF8B00',  dot:'#FF8B00' },
    model:      { bg:'#F3F0FF', border:'#6554C0', type:'#6554C0', name:'#172B4D', sub:'#6554C0',  dot:'#6554C0' },
    database:   { bg:'#F4F5F7', border:'#6B778C', type:'#6B778C', name:'#172B4D', sub:'#6B778C',  dot:'#6B778C' },
    job:        { bg:'#FFF4E5', border:'#FF5630', type:'#FF5630', name:'#172B4D', sub:'#FF5630',  dot:'#FF5630' },
    event:      { bg:'#FFF0FB', border:'#BF40BF', type:'#BF40BF', name:'#172B4D', sub:'#BF40BF',  dot:'#BF40BF' },
    listener:   { bg:'#FEE4FA', border:'#DA62AC', type:'#DA62AC', name:'#172B4D', sub:'#DA62AC',  dot:'#DA62AC' },
    unknown:    { bg:'#F4F5F7', border:'#6B778C', type:'#6B778C', name:'#42526E', sub:'#6B778C',  dot:'#6B778C' },
};
const RF_LAYER_ORDER = ['request','middleware','controller','service','repository','model','database','job','event','listener','unknown'];
const RF_TYPE_LABEL  = { request:'HTTP Request', middleware:'Middleware', controller:'Controller', service:'Service', repository:'Repository', model:'Model', database:'Database', job:'Job', event:'Event', listener:'Listener', unknown:'Component' };

let _rfNodes = {}, _rfEdges = [], _rfSelected = null, _rfTab = 'info', _rfRoute = null, _rfMws = [];
let _rfAnimFrame = null;

function startRfFlowAnimation() {
    if (_rfAnimFrame) { cancelAnimationFrame(_rfAnimFrame); _rfAnimFrame = null; }
    const svg   = document.getElementById('rf-svg');
    const dotsG = document.getElementById('rf-dots-g');
    if (!svg || !dotsG) return;
    const paths = svg.querySelectorAll('.rf-edge-path');
    if (!paths.length) return;

    dotsG.innerHTML = '';
    const ns   = 'http://www.w3.org/2000/svg';
    const dots = [];

    paths.forEach((path, i) => {
        const len = path.getTotalLength();
        if (len < 10) return;
        for (let d = 0; d < 2; d++) {
            const glow = document.createElementNS(ns, 'circle');
            glow.setAttribute('r', '9'); glow.setAttribute('fill', '#818CF8'); glow.setAttribute('opacity', '0.18');
            const circle = document.createElementNS(ns, 'circle');
            circle.setAttribute('r', '4'); circle.setAttribute('fill', '#818CF8'); circle.setAttribute('opacity', '0.9');
            dotsG.appendChild(glow);
            dotsG.appendChild(circle);
            dots.push({ dot: circle, glow, path, len, progress: (d * 0.5 + i * 0.17) % 1 });
        }
    });

    const SPEED = 0.35;
    let last = performance.now();
    function tick(now) {
        const dt = (now - last) / 1000; last = now;
        dots.forEach(d => {
            d.progress = (d.progress + SPEED * dt / d.len * 100) % 1;
            const pt = d.path.getPointAtLength(d.progress * d.len);
            d.dot.setAttribute('cx', pt.x); d.dot.setAttribute('cy', pt.y);
            d.glow.setAttribute('cx', pt.x); d.glow.setAttribute('cy', pt.y);
        });
        _rfAnimFrame = requestAnimationFrame(tick);
    }
    _rfAnimFrame = requestAnimationFrame(tick);
}

function showRouteDetail(idx) {
    const route   = APP.routes[idx];
    const methods = (route.methods || []).filter(m => m !== 'HEAD');
    const ctrl    = route.controller?.class ? route.controller.class.split('\\').pop() : null;
    const action  = route.controller?.method || null;
    const mws     = route.middleware || [];
    const chain   = ctrl ? traceChain(ctrl) : [];

    _rfRoute = route; _rfMws = mws; _rfNodes = {}; _rfEdges = []; _rfSelected = null; _rfTab = 'info';

    document.getElementById('routes-list').style.display   = 'none';
    document.getElementById('routes-detail').style.display = 'block';
    _activeDetailType = 'routes';
    _showBackBtn('Back to Route Explorer');

    // ── Build graph nodes + edges ──────────────────────────────────────────────
    let uid = 0;
    const mkNode = (type, name, sub, meta) => {
        const id = 'rg' + (uid++);
        _rfNodes[id] = { id, type, name, sub: sub || '', meta: meta || {} };
        return id;
    };
    const mkEdge = (from, to, label) => _rfEdges.push({ from, to, label: label || '' });

    const routeId = mkNode('request', methods.join('|') + ' /' + (route.uri || ''), route.name || '', { route });
    let prevId = routeId;

    if (mws.length) {
        const mwId = mkNode('middleware', 'Middleware Stack', mws.length + ' layers', { mws });
        mkEdge(prevId, mwId, 'enters');
        prevId = mwId;
    }

    if (chain.length > 0) {
        let lastId = prevId, firstEdge = ctrl ? 'dispatches' : null;
        chain.forEach((node, i) => {
            const isCtrl  = i === 0;
            const sub     = isCtrl ? (action && action !== '__invoke' ? '@' + action : 'Invokable') : (RF_TYPE_LABEL[node.layer] || node.layer);
            const nid     = mkNode(node.layer, node.name, sub, { node, action: isCtrl ? action : null, inferred: !!node._inferred });
            const label   = i === 0 ? firstEdge : (EDGE_LABEL[node.edgeType] || node.edgeType || 'calls');
            mkEdge(lastId, nid, label);
            lastId = nid;
        });
        // DB Table node — find the model node and look up its table
        const modelNode = chain.find(n => n.layer === 'model');
        if (modelNode) {
            const md = (APP.models || []).find(m => m.name === modelNode.name);
            if (md?.table) {
                const dbId     = mkNode('database', md.table, 'DB Table', { table: md.table, model: md });
                const modelNid = Object.values(_rfNodes).find(n => n.name === modelNode.name && n.type === 'model')?.id;
                if (modelNid) mkEdge(modelNid, dbId, 'queries');
            }
        }
    } else if (ctrl) {
        // Controller exists but has no dep-graph edges and inference found nothing
        const cid = mkNode('controller', ctrl, action && action !== '__invoke' ? '@' + action : 'Invokable', {});
        mkEdge(prevId, cid, 'dispatches');
    }

    // ── Layout: group nodes into layers, position each ─────────────────────────
    const NW = 224, NH = 74, GAP_Y = 100, GAP_X = 28, PAD = 40, MAX_PER_ROW = 3;
    const layers = {};
    Object.values(_rfNodes).forEach(n => {
        const li = RF_LAYER_ORDER.indexOf(n.type);
        const k  = li >= 0 ? li : 99;
        (layers[k] = layers[k] || []).push(n);
    });
    const layerKeys   = Object.keys(layers).map(Number).sort((a, b) => a - b);
    const maxRowW     = Math.max(...layerKeys.map(k => Math.min(layers[k].length, MAX_PER_ROW) * NW + (Math.min(layers[k].length, MAX_PER_ROW) - 1) * GAP_X));
    const CANVAS_W    = Math.max(maxRowW + PAD * 2, 480);
    const totalSubRows = layerKeys.reduce((sum, k) => sum + Math.ceil(layers[k].length / MAX_PER_ROW), 0);
    const CANVAS_H    = totalSubRows * (NH + GAP_Y) + PAD * 2 - GAP_Y + PAD;
    const pos = {};
    let currentY = PAD;
    layerKeys.forEach(lk => {
        const row = layers[lk];
        const chunks = [];
        for (let i = 0; i < row.length; i += MAX_PER_ROW) chunks.push(row.slice(i, i + MAX_PER_ROW));
        chunks.forEach(chunk => {
            const totalW = chunk.length * NW + (chunk.length - 1) * GAP_X;
            let x = (CANVAS_W - totalW) / 2;
            chunk.forEach(n => {
                pos[n.id] = { x, y: currentY, cx: x + NW / 2, cy: currentY + NH / 2 };
                x += NW + GAP_X;
            });
            currentY += NH + GAP_Y;
        });
    });

    // ── SVG defs ───────────────────────────────────────────────────────────────
    const defs = `<defs>
        <pattern id="rf-dot" x="0" y="0" width="22" height="22" patternUnits="userSpaceOnUse">
            <circle cx="1" cy="1" r="0.8" fill="rgba(148,178,222,0.15)" opacity="1"/>
        </pattern>
        <marker id="rf-arr" markerWidth="9" markerHeight="7" refX="8" refY="3.5" orient="auto">
            <polygon points="0 0,9 3.5,0 7" fill="rgba(148,178,222,0.5)"/>
        </marker>
        <marker id="rf-arr-hi" markerWidth="9" markerHeight="7" refX="8" refY="3.5" orient="auto">
            <polygon points="0 0,9 3.5,0 7" fill="#6366F1"/>
        </marker>
        <filter id="rf-glow" x="-20%" y="-20%" width="140%" height="140%">
            <feGaussianBlur stdDeviation="3" result="blur"/>
            <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
        </filter>
    </defs>`;

    // ── Edges ──────────────────────────────────────────────────────────────────
    const edgesSvg = _rfEdges.map(e => {
        const f = pos[e.from], t = pos[e.to];
        if (!f || !t) return '';
        const x1 = f.cx, y1 = f.y + NH, x2 = t.cx, y2 = t.y;
        const cp = Math.min(Math.abs(y2 - y1) * 0.45, 100);
        const d  = `M${x1},${y1} C${x1},${y1+cp} ${x2},${y2-cp} ${x2},${y2}`;
        const mx = (x1+x2)/2, my = (y1+y2)/2;
        const lw = (e.label.length * 6) + 14;
        return `<g>
            <path d="${d}" class="rf-edge-path" fill="none" stroke="rgba(148,178,222,0.35)" stroke-width="1.5" marker-end="url(#rf-arr)"/>
            ${e.label ? `<rect x="${mx-lw/2}" y="${my-8}" width="${lw}" height="16" rx="8" fill="#FFFFFF" stroke="#DFE1E6" stroke-width="1"/>
            <text x="${mx}" y="${my+4}" fill="#6B778C" font-size="9" font-family="ui-monospace,monospace" text-anchor="middle" font-weight="600" letter-spacing="0.04em">${_esc(e.label)}</text>` : ''}
        </g>`;
    }).join('');

    // ── Nodes ──────────────────────────────────────────────────────────────────
    const nodesSvg = Object.values(_rfNodes).map(n => {
        const p = pos[n.id]; if (!p) return '';
        const c          = RF_COLOR[n.type] || RF_COLOR.unknown;
        const typeLabel  = RF_TYPE_LABEL[n.type] || n.type;
        const shortName  = n.name.length > 24 ? n.name.slice(0, 23) + '…' : n.name;
        const shortSub   = n.sub.length  > 28 ? n.sub.slice(0, 27)  + '…' : n.sub;
        const isInferred = n.meta?.inferred;
        // Inferred nodes get a dashed border + slightly dimmer opacity to show they are convention-based
        const strokeDash = isInferred ? 'stroke-dasharray="5,3"' : '';
        const opacity    = isInferred ? 'opacity="0.88"' : '';
        return `<g class="rf-node" data-id="${n.id}" onclick="rfClick('${n.id}')" style="cursor:pointer" ${opacity}>
            <rect x="${p.x}" y="${p.y}" width="${NW}" height="${NH}" rx="12" fill="${c.bg}" stroke="${c.border}" stroke-width="1.5" ${strokeDash} id="rf-rect-${n.id}" filter="url(#rf-node-shadow)"/>
            <rect x="${p.x}" y="${p.y}" width="${NW}" height="22" rx="12" fill="${c.border}" fill-opacity="0.2"/>
            <rect x="${p.x}" y="${p.y+10}" width="${NW}" height="12" fill="${c.border}" fill-opacity="0.2"/>
            <circle cx="${p.x+14}" cy="${p.y+11}" r="3.5" fill="${c.dot}"/>
            <text x="${p.x+23}" y="${p.y+15}" fill="${c.type}" font-size="8.5" font-family="ui-monospace,monospace" font-weight="700" letter-spacing="0.1em">${typeLabel.toUpperCase()}${isInferred ? ' ~' : ''}</text>
            <text x="${p.x+12}" y="${p.y+43}" fill="${c.name}" font-size="12.5" font-family="ui-monospace,monospace" font-weight="700">${_esc(shortName)}</text>
            ${shortSub ? `<text x="${p.x+12}" y="${p.y+60}" fill="${c.sub}" font-size="10" font-family="ui-monospace,monospace">${_esc(shortSub)}</text>` : ''}
        </g>`;
    }).join('');

    // ── Method badges (HTML) ───────────────────────────────────────────────────
    const methodBadges = methods.map(m =>
        `<span class="text-xs font-bold px-2.5 py-1 rounded-lg method-${m.toLowerCase()}">${m}</span>`
    ).join('');

    // ── Right panel: default shows route props ─────────────────────────────────
    const firstNode = Object.values(_rfNodes)[0];
    _rfSelected = firstNode;

    document.getElementById('routes-detail-content').innerHTML = `
    <div style="display:flex;height:580px;border-radius:16px;overflow:hidden;border:1px solid var(--border);background:var(--bg-sunken);box-shadow:var(--shadow)">

        <!-- ── Graph canvas ── -->
        <div style="flex:1;display:flex;flex-direction:column;min-width:0;overflow:hidden">

            <!-- Toolbar -->
            <div style="display:flex;align-items:center;gap:8px;padding:10px 16px;background:#F4F5F7;border-bottom:1px solid #DFE1E6;flex-shrink:0">
                <div style="display:flex;gap:4px">
                    <span style="background:#FFFFFF;color:#42526E;border-radius:8px;padding:4px 11px;font-size:10px;font-family:ui-monospace,monospace;font-weight:700;letter-spacing:0.05em;border:1px solid #DFE1E6">TOP-DOWN</span>
                </div>
                <div style="width:1px;height:14px;background:#DFE1E6"></div>
                <span style="font-size:10px;color:#6B778C;font-family:ui-monospace,monospace">${Object.keys(_rfNodes).length} nodes · ${_rfEdges.length} edges</span>
                <div style="margin-left:auto;display:flex;gap:6px;align-items:center">
                    ${methodBadges}
                    <code style="font-size:11px;color:#6B778C;font-family:ui-monospace,monospace">/${_esc(route.uri||'')}</code>
                </div>
            </div>

            <!-- SVG scroll area -->
            <div style="flex:1;overflow:auto;padding:0;background:#F7F8F9" id="rf-canvas-wrap">
                <svg id="rf-svg" width="${CANVAS_W}" height="${CANVAS_H}" style="display:block;min-width:${CANVAS_W}px">
                    ${defs}
                    <rect width="${CANVAS_W}" height="${CANVAS_H}" fill="#F7F8F9"/>
                    <rect width="${CANVAS_W}" height="${CANVAS_H}" fill="url(#rf-dot)"/>
                    ${edgesSvg}
                    ${nodesSvg}
                    <g id="rf-dots-g"></g>
                </svg>
            </div>
        </div>

        <!-- ── Right panel ── -->
        <div style="width:256px;flex-shrink:0;background:#FFFFFF;border-left:1px solid #DFE1E6;display:flex;flex-direction:column;overflow:hidden">

            <!-- Route identity -->
            <div style="padding:14px 16px;border-bottom:1px solid #DFE1E6;background:#F4F5F7">
                <div style="display:flex;gap:5px;flex-wrap:wrap;align-items:center;margin-bottom:4px">
                    ${methodBadges}
                </div>
                <code style="font-size:10px;color:#42526E;font-family:ui-monospace,monospace;word-break:break-all;display:block;margin-top:2px;font-weight:600">${_esc(route.uri||'')}</code>
                ${route.name ? `<p style="font-size:9px;color:#6B778C;font-family:ui-monospace,monospace;margin:2px 0 0">${_esc(route.name)}</p>` : ''}
            </div>

            <!-- Tabs -->
            <div style="display:flex;border-bottom:1px solid #DFE1E6;padding:0 4px;flex-shrink:0;background:#FFFFFF">
                <button id="rftab-info"  onclick="rfTab('info')"  style="flex:1;padding:8px 0;font-size:10px;color:#6366F1;background:none;border:none;border-bottom:2px solid #6366F1;cursor:pointer;font-family:inherit;font-weight:700;letter-spacing:0.04em">INFO</button>
                <button id="rftab-flow"  onclick="rfTab('flow')"  style="flex:1;padding:8px 0;font-size:10px;color:#6B778C;background:none;border:none;border-bottom:2px solid transparent;cursor:pointer;font-family:inherit;font-weight:600;letter-spacing:0.04em">FLOW</button>
                <button id="rftab-edges" onclick="rfTab('edges')" style="flex:1;padding:8px 0;font-size:10px;color:#6B778C;background:none;border:none;border-bottom:2px solid transparent;cursor:pointer;font-family:inherit;font-weight:600;letter-spacing:0.04em">EDGES</button>
            </div>

            <!-- Panel body -->
            <div id="rf-panel" style="flex:1;overflow-y:auto;padding:14px">
                ${rfNodeProps(firstNode)}
            </div>
        </div>

    </div>`;

    setTimeout(() => rfHighlight(firstNode?.id), 10);
    setTimeout(() => startRfFlowAnimation(), 60);
}

function rfProp(label, val) {
    if (!val && val !== 0) return '';
    return `<div style="margin-bottom:10px">
        <p style="font-size:8.5px;font-weight:700;color:#6B778C;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 2px;font-family:ui-monospace,monospace">${label}</p>
        <p style="font-size:11px;color:#172B4D;font-family:ui-monospace,monospace;margin:0;word-break:break-all;line-height:1.4;font-weight:500">${val}</p>
    </div>`;
}

function rfNodeProps(node) {
    if (!node) return '';
    const route = _rfRoute, mws = _rfMws;
    const c = RF_COLOR[node.type] || RF_COLOR.unknown;
    const header = `<div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid rgba(148,178,222,0.08)">
        <div style="width:8px;height:8px;border-radius:50%;background:${c.dot};flex-shrink:0"></div>
        <div>
            <p style="font-size:8px;color:${c.type};text-transform:uppercase;letter-spacing:0.1em;margin:0;font-family:ui-monospace,monospace;font-weight:700">${RF_TYPE_LABEL[node.type]||node.type}</p>
            <p style="font-size:11px;color:${c.name};font-family:ui-monospace,monospace;margin:0;font-weight:700;word-break:break-all">${_esc(node.name)}</p>
        </div>
    </div>`;

    const inferredBadge = node.meta?.inferred
        ? `<div style="margin-bottom:10px;padding:5px 8px;background:rgba(251,191,36,0.12);border:1px solid rgba(251,191,36,0.3);border-radius:6px;font-size:9px;color:#FBBF24;font-family:ui-monospace,monospace">~ inferred by naming convention</div>`
        : '';

    let body = inferredBadge;
    if (node.type === 'request') {
        const methods = (route.methods||[]).filter(m=>m!=='HEAD');
        body += rfProp('Method', methods.join(', ')) + rfProp('URI', '/' + (route.uri||'')) + rfProp('Route Name', route.name||null) + rfProp('Middleware', mws.length ? mws.length + ' layers' : 'None');
    } else if (node.type === 'middleware') {
        body += mws.map((mw, i) => rfProp('#' + (i+1), mw.split('\\').pop())).join('');
    } else if (node.type === 'controller') {
        const cc = route.controller || {};
        body += rfProp('Class', cc.class || node.name) + rfProp('Method', (node.meta?.action && node.meta.action !== '__invoke') ? node.meta.action : null) + rfProp('Type', (!node.meta?.action || node.meta.action === '__invoke') ? 'Invokable Controller' : 'Resource Method');
    } else if (node.type === 'model') {
        const md = (APP.models||[]).find(m => m.name === node.name);
        if (md) body += rfProp('Class', md.name) + rfProp('Table', md.table) + rfProp('Fillable', md.fillable?.length ? md.fillable.length + ' fields' : null) + rfProp('Relationships', md.relationships?.length ? md.relationships.length + ' defined' : null);
    } else if (node.type === 'database') {
        body += rfProp('Table', node.name) + rfProp('Model', node.meta?.model?.name || null);
    } else if (node.type === 'service') {
        const svc = (APP.services||[]).find(s => s.name === node.name);
        body += rfProp('Class', node.name) + rfProp('Methods', svc?.method_count ? svc.method_count + ' methods' : null);
    } else if (node.type === 'repository') {
        body += rfProp('Class', node.name) + rfProp('Layer', 'Repository Pattern');
    } else {
        body += rfProp('Name', node.name) + rfProp('Layer', node.type);
    }
    return header + body;
}

function rfFlowList() {
    return Object.values(_rfNodes).map(n => {
        const c = RF_COLOR[n.type] || RF_COLOR.unknown;
        return `<div onclick="rfClick('${n.id}')" style="display:flex;align-items:center;gap:8px;padding:7px 8px;border-radius:8px;cursor:pointer;margin-bottom:4px;border:1px solid #DFE1E6;background:#FFFFFF;transition:background 0.15s,box-shadow 0.15s" onmouseenter="this.style.boxShadow='0 2px 8px rgba(23,43,77,0.08)'" onmouseleave="this.style.boxShadow='none'">
            <div style="width:7px;height:7px;border-radius:50%;background:${c.dot};flex-shrink:0"></div>
            <div style="flex:1;min-width:0">
                <p style="font-size:10px;color:#172B4D;font-family:ui-monospace,monospace;font-weight:700;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${_esc(n.name)}</p>
                <p style="font-size:8px;color:${c.type};text-transform:uppercase;letter-spacing:0.08em;margin:0;font-family:ui-monospace,monospace;font-weight:600">${RF_TYPE_LABEL[n.type]||n.type}</p>
            </div>
        </div>`;
    }).join('');
}

function rfEdgeList() {
    return _rfEdges.map(e => {
        const from = _rfNodes[e.from], to = _rfNodes[e.to];
        if (!from || !to) return '';
        return `<div style="margin-bottom:8px;padding:8px;border-radius:8px;border:1px solid #DFE1E6;background:#FFFFFF">
            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">
                <span style="font-size:9px;color:#6366F1;font-family:ui-monospace,monospace;font-weight:700">${_esc(from.name)}</span>
                <span style="font-size:8px;color:#6B778C">→</span>
                <span style="font-size:9px;color:#6554C0;font-family:ui-monospace,monospace;font-weight:700">${_esc(to.name)}</span>
            </div>
            ${e.label ? `<span style="font-size:8px;color:#6B778C;font-family:ui-monospace,monospace;font-style:italic">${_esc(e.label)}</span>` : ''}
        </div>`;
    }).join('') || '<p style="font-size:11px;color:#6B778C;text-align:center;margin-top:20px">No edges</p>';
}

function rfClick(nid) {
    _rfSelected = _rfNodes[nid];
    rfHighlight(nid);
    rfRefreshPanel();
}

function rfHighlight(nid) {
    document.querySelectorAll('.rf-node rect:first-child').forEach(r => {
        r.setAttribute('stroke-width', '1.5');
        r.style.filter = '';
    });
    if (!nid) return;
    const rect = document.getElementById('rf-rect-' + nid);
    if (rect) { rect.setAttribute('stroke-width', '3'); rect.style.filter = 'brightness(1.5) drop-shadow(0 0 6px currentColor)'; }
}

function rfTab(tab) {
    _rfTab = tab;
    ['info','flow','edges'].forEach(t => {
        const btn = document.getElementById('rftab-' + t);
        if (!btn) return;
        const active = t === tab;
        btn.style.color        = active ? '#6366F1' : '#6B778C';
        btn.style.borderBottom = active ? '2px solid #6366F1' : '2px solid transparent';
    });
    rfRefreshPanel();
}

function rfRefreshPanel() {
    const el = document.getElementById('rf-panel');
    if (!el) return;
    if (_rfTab === 'info')  el.innerHTML = rfNodeProps(_rfSelected || Object.values(_rfNodes)[0]);
    if (_rfTab === 'flow')  el.innerHTML = rfFlowList();
    if (_rfTab === 'edges') el.innerHTML = rfEdgeList();
}

// ── Detail renderers ──────────────────────────────────────────────────────────

function renderDetail(type, item) {
    const map = {
        models: renderModel, controllers: renderController,
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
    {color:'var(--cyan)',    rgb:'99,102,241',   hex:'#6366F1'},
    {color:'var(--violet)',  rgb:'167,139,250',  hex:'#A78BFA'},
    {color:'var(--emerald)', rgb:'52,211,153',   hex:'#34D399'},
    {color:'var(--amber)',   rgb:'251,191,36',   hex:'#FBBF24'},
    {color:'var(--rose)',    rgb:'248,113,113',  hex:'#F87171'},
    {color:'var(--sky)',     rgb:'96,165,250',   hex:'#60A5FA'},
];
const MDS_REL_CFG = {
    hasMany:       {hex:'#34D399',color:'var(--emerald)',bg:'rgba(52,211,153,.12)', border:'rgba(52,211,153,.3)'},
    hasOne:        {hex:'#6366F1',color:'var(--cyan)',   bg:'rgba(99,102,241,.12)',  border:'rgba(99,102,241,.3)'},
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
          ${hideCnt  ? `<div class="mds-side-stat" onclick="mdsTab('fields')" title="Go to fields"><span class="mds-side-stat-lbl">Hidden</span><span class="mds-side-stat-val" style="color:var(--rose);">${hideCnt}</span></div>` : ''}
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
      ${usedBy.length ? `<button class="mds-tab-btn" id="mds-tab-usedby" onclick="mdsTab('usedby')">Used By <span style="font-size:10px;padding:1px 6px;border-radius:4px;background:rgba(99,102,241,.12);color:var(--cyan);margin-left:5px;font-family:var(--font-mono);">${usedBy.length}</span></button>` : ''}
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

function renderController(c) {
    // Sanitize a string for safe HTML injection (XSS prevention)
    const esc = s => String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    // Sanitize a string for safe use inside Mermaid node labels (strip quotes/brackets/newlines)
    const mEsc = s => String(s ?? '').replace(/["[\]\n\r]/g, '').replace(/&/g, 'and').trim();
    // Safe first-letter avatar — never undefined
    const initial = s => (String(s || '?')[0] || '?').toUpperCase();

    const RESOURCE_VERBS = {
        index:   {verb:'GET',         color:'method-get'},
        create:  {verb:'GET',         color:'method-get'},
        store:   {verb:'POST',        color:'method-post'},
        show:    {verb:'GET',         color:'method-get'},
        edit:    {verb:'GET',         color:'method-get'},
        update:  {verb:'PUT / PATCH', color:'method-patch'},
        destroy: {verb:'DELETE',      color:'method-delete'},
    };

    // ── linked routes for this controller ─────────────────────────
    const ctrlBase = c.name;
    const linkedRoutes = (APP.routes || []).filter(r => {
        const cls = (r.controller?.class || r.action || '');
        return cls.split('\\').pop() === ctrlBase || cls === ctrlBase;
    });

    // ── dependency graph nodes from dep analyser ───────────────────
    const depEdges = (APP.dependencies?.edges || []).filter(e => e.from === ctrlBase);
    const usedModels   = depEdges.filter(e => e.type === 'uses' && (APP.dependencies?.nodes||[]).find(n=>n.name===e.to&&n.layer==='model')).map(e=>e.to);
    const usedServices = depEdges.filter(e => e.type === 'injects' || e.type === 'uses').map(e=>e.to).filter(t=>!usedModels.includes(t));

    // ── Header ─────────────────────────────────────────────────────
    let h = `
    <div style="background:var(--bg-elevated);border-radius:16px;border:1px solid var(--border);padding:24px;margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
            <div style="width:56px;height:56px;border-radius:14px;background:rgba(96,165,250,.15);border:1px solid rgba(96,165,250,.3);display:flex;align-items:center;justify-content:center;color:#60A5FA;font-weight:700;font-size:22px;flex:none;">${esc(initial(c.name))}</div>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px;">
                    <h2 style="font-size:22px;font-weight:700;color:var(--text);margin:0;">${esc(c.name)}</h2>
                    ${c.is_resource ? '<span style="font-size:11px;background:rgba(52,211,153,.12);color:#34D399;border:1px solid rgba(52,211,153,.3);padding:2px 10px;border-radius:20px;font-weight:600;">Resource Controller</span>' : ''}
                </div>
                <p style="font-size:13px;color:var(--text-dim);font-family:var(--font-mono);margin:0 0 2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${esc(c.namespace)}&#92;${esc(c.name)}</p>
                <p style="font-size:11px;color:var(--text-faint);font-family:var(--font-mono);margin:0;">${esc(c.path || '')}</p>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">
            <div style="background:rgba(96,165,250,.08);border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(96,165,250,.18);">
                <p style="font-size:22px;font-weight:700;color:#60A5FA;margin:0 0 2px;">${c.method_count || 0}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Methods</p>
            </div>
            <div style="background:rgba(167,139,250,.08);border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(167,139,250,.18);">
                <p style="font-size:22px;font-weight:700;color:#A78BFA;margin:0 0 2px;">${(c.dependencies||[]).length}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Dependencies</p>
            </div>
            <div style="background:rgba(99,102,241,.07);border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(99,102,241,.15);">
                <p style="font-size:22px;font-weight:700;color:#6366F1;margin:0 0 2px;">${linkedRoutes.length}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Routes</p>
            </div>
            <div style="background:rgba(52,211,153,.08);border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(52,211,153,.18);">
                <p style="font-size:22px;font-weight:700;color:#34D399;margin:0 0 2px;">${usedModels.length}</p>
                <p style="font-size:11px;color:var(--text-faint);margin:0;">Models Used</p>
            </div>
        </div>
    </div>`;

    // ── Request Flow Diagram (card-style) ────────────────────────────────────

    // Collect data
    const _cfAllMw = [...new Set([...(c.middleware||[]), ...linkedRoutes.flatMap(r => r.middleware||[])])].filter(Boolean);
    const _cfDeps  = (c.dependencies||[]).slice(0, 4);
    const _cfMdls  = usedModels.slice(0, 4);
    const _cfRts   = linkedRoutes.slice(0, 5);

    // Method pill (white text on solid bg, shown inside route card header)
    const _cfBadge = ms => (ms||[]).filter(m=>m!=='HEAD').slice(0,1).map(m => {
        const bg = {GET:'rgba(16,185,129,.9)',POST:'rgba(59,130,246,.9)',PUT:'rgba(245,158,11,.9)',PATCH:'rgba(249,115,22,.9)',DELETE:'rgba(239,68,68,.9)'}[m]||'rgba(100,116,139,.9)';
        return `<span style="font-size:8.5px;font-weight:700;font-family:var(--font-mono);padding:2px 7px;border-radius:5px;background:${bg};color:#fff;letter-spacing:.05em;">${m}</span>`;
    }).join('');

    // White SVG icons (for colored card headers)
    const _cfIcoRoute = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.9)" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 16l4.553-2.276A1 1 0 0021 19.382V8.618a1 1 0 00-.553-.894L15 5m0 16V5m0 0L9 7"/></svg>`;
    const _cfIcoLock  = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.9)" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>`;
    const _cfIcoCtrl  = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.95)" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>`;
    const _cfIcoDep   = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.9)" stroke-width="2.5"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>`;
    const _cfIcoDB    = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.9)" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14c0 1.657 4.03 3 9 3s9-1.343 9-3V5"/><path d="M3 12c0 1.657 4.03 3 9 3s9-1.343 9-3"/></svg>`;
    const _cfIcoMdl   = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.9)" stroke-width="2"><path d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>`;

    // Proper card: gradient colored header + white content body
    const _cfCard = (headerGrad, headerIcon, headerLabel, body, delay) =>
        `<div class="cf-node" style="border-radius:14px;overflow:hidden;box-shadow:0 3px 14px rgba(0,0,0,.09),0 0 0 1px rgba(0,0,0,.04);margin-bottom:8px;animation-delay:${delay}ms;">
            <div style="background:${headerGrad};padding:8px 12px;display:flex;align-items:center;gap:7px;">
                ${headerIcon}
                <span style="font-size:9px;font-weight:700;color:rgba(255,255,255,.9);letter-spacing:.08em;text-transform:uppercase;flex:1;">${headerLabel}</span>
            </div>
            <div style="background:#fff;padding:10px 12px;">${body}</div>
        </div>`;

    // Animated arrow connector between columns
    const _cfArrow = (c1, c2) =>
        `<div style="display:flex;align-items:center;align-self:center;padding:0 5px;margin-top:18px;">
            <div style="width:28px;height:2px;background:linear-gradient(90deg,${c1},${c2});border-radius:1px;opacity:.55;"></div>
            <div style="width:0;height:0;border-top:5px solid transparent;border-bottom:5px solid transparent;border-left:8px solid ${c2};opacity:.65;"></div>
        </div>`;

    // Column wrapper with label
    const _cfColWrap = (label, color, inner, width) =>
        `<div style="display:flex;flex-direction:column;align-items:center;">
            <div style="font-size:9px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:${color};margin-bottom:10px;font-family:var(--font-mono);white-space:nowrap;opacity:.75;">${label}</div>
            <div style="width:${width||185}px;">${inner}</div>
        </div>`;

    // ── Build each column ──────────────────────────────────────────

    // Col 0 — Routes
    const _cfRtInner = (_cfRts.length > 0 ? _cfRts : [null]).map((r, i) =>
        _cfCard('linear-gradient(135deg,#6366F1,#818CF8)', _cfIcoRoute,
            r ? `${_cfBadge(r.methods)}&nbsp;&nbsp;Route` : 'No Routes',
            r ? `<div style="font-size:10px;font-family:var(--font-mono);color:#4338CA;font-weight:500;word-break:break-all;line-height:1.5;">${esc(r.uri||'/')}</div>`
              : `<div style="font-size:11px;color:#9CA3AF;font-style:italic;">No routes linked</div>`,
            i * 45)
    ).join('');

    const _cfCols = [
        { label: 'Route' + (_cfRts.length !== 1 ? 's' : ''), color:'#6366F1', inner:_cfRtInner, width:182 },
    ];

    // Col 1 — Middleware (optional)
    if (_cfAllMw.length > 0) {
        const mwInner = _cfAllMw.slice(0,5).map((mw, i) =>
            _cfCard('linear-gradient(135deg,#D97706,#F59E0B)', _cfIcoLock, 'Guard',
                `<span style="font-size:10px;font-family:var(--font-mono);color:#78350F;font-weight:600;word-break:break-all;line-height:1.45;">${esc(mw)}</span>`,
                120 + i * 40)
        ).join('');
        _cfCols.push({ label:'Middleware', color:'#D97706', inner:mwInner, width:165 });
    }

    // Col 2 — Controller (hero card — gradient header + white body, larger shadow)
    const _cfCtrlDelay = _cfAllMw.length > 0 ? 240 : 160;
    const _cfCx        = c.complexity || 0;
    const _cfCxClr     = _cfCx > 10 ? '#EF4444' : _cfCx > 5 ? '#F59E0B' : '#10B981';
    const _cfCtrlCard  = `<div class="cf-node" style="border-radius:14px;overflow:hidden;box-shadow:0 8px 32px rgba(99,102,241,.18),0 0 0 1.5px rgba(99,102,241,.22);animation-delay:${_cfCtrlDelay}ms;">
        <div style="background:linear-gradient(135deg,#4F46E5 0%,#7C3AED 100%);padding:14px 16px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                <div style="width:34px;height:34px;border-radius:10px;background:rgba(255,255,255,.15);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;flex:none;">${_cfIcoCtrl}</div>
                <div style="font-size:12px;font-weight:700;color:#fff;line-height:1.3;word-break:break-word;flex:1;">${esc(c.name)}</div>
            </div>
            <div style="display:flex;gap:5px;flex-wrap:wrap;">
                <span style="font-size:9px;background:rgba(255,255,255,.18);color:#fff;padding:2px 9px;border-radius:20px;font-weight:600;">${c.method_count||0} methods</span>
                ${c.is_resource ? '<span style="font-size:9px;background:rgba(52,211,153,.3);color:#A7F3D0;padding:2px 9px;border-radius:20px;font-weight:600;">Resource</span>' : ''}
            </div>
        </div>
        <div style="background:#FAFBFF;padding:14px 16px;">
            ${_cfCx > 0
                ? `<div>
                    <div style="display:flex;justify-content:space-between;font-size:9px;color:#9CA3AF;margin-bottom:4px;">
                        <span>Complexity score</span>
                        <span style="color:${_cfCxClr};font-weight:700;">${_cfCx}</span>
                    </div>
                    <div style="height:4px;background:#E5E7EB;border-radius:3px;overflow:hidden;">
                        <div style="height:100%;width:${Math.min(100,_cfCx*6)}%;background:linear-gradient(90deg,${_cfCxClr},${_cfCxClr}bb);border-radius:3px;"></div>
                    </div>
                   </div>`
                : '<div style="font-size:11px;color:#10B981;font-weight:500;">Low complexity</div>'}
        </div>
    </div>`;
    _cfCols.push({ label:'Controller', color:'#6366F1', inner:_cfCtrlCard, width:220 });

    // Col 3 — Dependencies + Models (optional)
    const _cfDepDelay = _cfCtrlDelay + 120;
    const _cfRightNodes = [
        ..._cfDeps.map((dep, i) => _cfCard('linear-gradient(135deg,#7C3AED,#A78BFA)', _cfIcoDep, 'Service',
            `<span style="font-size:10px;font-family:var(--font-mono);color:#5B21B6;font-weight:600;word-break:break-all;">${esc(dep.type||dep)}</span>`,
            _cfDepDelay + i * 40)),
        ..._cfMdls.map((mdl, i) => _cfCard('linear-gradient(135deg,#059669,#34D399)', _cfIcoMdl, 'Model',
            `<span style="font-size:10px;font-family:var(--font-mono);color:#065F46;font-weight:600;word-break:break-all;">${esc(mdl)}</span>`,
            _cfDepDelay + (_cfDeps.length + i) * 40)),
    ];
    if (_cfRightNodes.length > 0) {
        _cfCols.push({ label:'Dependencies', color:'#7C3AED', inner:_cfRightNodes.join(''), width:178 });
    }

    // Col 4 — Database (card with header + centered icon body)
    const _cfDbDelay = _cfDepDelay + (_cfRightNodes.length > 0 ? 160 : 0);
    const _cfDbInner = `<div class="cf-node" style="border-radius:14px;overflow:hidden;box-shadow:0 3px 14px rgba(0,0,0,.07),0 0 0 1px rgba(100,116,139,.12);animation-delay:${_cfDbDelay}ms;">
        <div style="background:linear-gradient(135deg,#334155,#64748B);padding:9px 13px;display:flex;align-items:center;gap:7px;">
            ${_cfIcoDB}
            <span style="font-size:9px;font-weight:700;color:rgba(255,255,255,.9);letter-spacing:.08em;text-transform:uppercase;">Database</span>
        </div>
        <div style="background:#fff;padding:18px 14px;text-align:center;">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.5" style="margin:0 auto 6px;display:block;">
                <ellipse cx="12" cy="5" rx="9" ry="3"/>
                <path d="M3 5v14c0 1.657 4.03 3 9 3s9-1.343 9-3V5"/>
                <path d="M3 12c0 1.657 4.03 3 9 3s9-1.343 9-3"/>
            </svg>
            <div style="font-size:10px;font-weight:600;color:#64748B;">Eloquent ORM</div>
            <div style="font-size:9px;color:#94A3B8;margin-top:2px;">${usedModels.length} model${usedModels.length!==1?'s':''}</div>
        </div>
    </div>`;
    _cfCols.push({ label:'Database', color:'#64748B', inner:_cfDbInner, width:142 });

    // ── Assemble ───────────────────────────────────────────────────
    let _cfHtml = '';
    _cfCols.forEach((col, ci) => {
        _cfHtml += _cfColWrap(col.label, col.color, col.inner, col.width);
        if (ci < _cfCols.length - 1) {
            _cfHtml += _cfArrow(col.color, _cfCols[ci+1].color);
        }
    });

    h += `<div style="background:var(--bg-elevated);border-radius:16px;border:1px solid var(--border);padding:20px;margin-bottom:24px;">
        <h3 style="font-size:13px;font-weight:700;color:var(--text);margin:0 0 16px;display:flex;align-items:center;gap:8px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#6366F1" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Request Flow
        </h3>
        <div style="background:#FAFAFA;background-image:radial-gradient(rgba(99,102,241,.06) 1px,transparent 1px);background-size:20px 20px;border-radius:12px;border:1px solid var(--border);padding:22px 24px;overflow-x:auto;">
            <div style="display:flex;align-items:center;gap:0;min-width:max-content;">
                ${_cfHtml}
            </div>
        </div>
    </div>`;

    // ── Methods Table ──────────────────────────────────────────────
    if ((c.method_details || c.methods || []).length) {
        const methods = c.method_details || (c.methods||[]).map(m=>({method:m, params:[]}));
        const RESOURCE_SET = new Set(['index','create','store','show','edit','update','destroy']);

        let rows = methods.map(md => {
            const mName  = md.method || md;
            const params = md.params || [];
            const rv     = RESOURCE_VERBS[mName];
            const isRes  = RESOURCE_SET.has(mName);

            // Find matching route(s)
            const matchedRoutes = linkedRoutes.filter(r => {
                const act = r.controller?.method || r.action || '';
                return act === mName || act.split('@')[1] === mName;
            });

            const httpBadge = rv
                ? `<span class="text-xs font-bold px-1.5 py-0.5 rounded ${rv.color}">${esc(rv.verb)}</span>`
                : (matchedRoutes.length > 0
                    ? matchedRoutes.slice(0,2).map(r => {
                        const ms = (r.methods||[]).filter(m=>m!=='HEAD');
                        return `<span class="text-xs font-bold px-1.5 py-0.5 rounded method-${esc((ms[0]||'get').toLowerCase())}">${esc(ms[0]||'?')}</span>`;
                      }).join(' ')
                    : `<span class="text-xs text-slate-300 italic">—</span>`);

            const routeUri = matchedRoutes.length > 0
                ? `<span class="text-xs text-slate-400 font-mono">${esc(matchedRoutes[0].uri||'')}</span>`
                : '';

            const paramBadges = params.length
                ? params.map(p => `<span class="text-xs bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded font-mono">${p.type ? esc(p.type) + ' ' : ''}<span class="text-indigo-500">$${esc(p.var)}</span></span>`).join(' ')
                : '<span class="text-xs text-slate-300 italic">no params</span>';

            const mwOnMethod = matchedRoutes.length > 0
                ? [...new Set(matchedRoutes.flatMap(r => r.middleware||[]))].slice(0,2)
                    .map(m => `<span class="text-xs bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded">${esc(m)}</span>`).join(' ')
                : '';

            return `<tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                <td class="py-3 px-4">
                    <div class="flex items-center gap-2">
                        <span class="font-mono font-semibold text-slate-700 text-sm">${esc(mName)}</span>
                        ${isRes ? '<span class="text-xs bg-blue-50 text-blue-500 px-1.5 py-0.5 rounded">resource</span>' : ''}
                    </div>
                </td>
                <td class="py-3 px-4">${httpBadge}</td>
                <td class="py-3 px-4">${routeUri}</td>
                <td class="py-3 px-4"><div class="flex flex-wrap gap-1">${paramBadges}</div></td>
                <td class="py-3 px-4"><div class="flex flex-wrap gap-1">${mwOnMethod}</div></td>
            </tr>`;
        }).join('');

        h += `<div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                <h3 class="text-sm font-semibold text-slate-700">Methods (${methods.length})</h3>
            </div>
            <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-xs text-slate-400 uppercase tracking-wide border-b border-slate-100 bg-slate-50">
                    <th class="text-left py-2.5 px-4 font-medium">Method</th>
                    <th class="text-left py-2.5 px-4 font-medium">HTTP</th>
                    <th class="text-left py-2.5 px-4 font-medium">Route URI</th>
                    <th class="text-left py-2.5 px-4 font-medium">Parameters</th>
                    <th class="text-left py-2.5 px-4 font-medium">Middleware</th>
                </tr></thead>
                <tbody>${rows}</tbody>
            </table>
            </div>
        </div>`;
    }

    // ── Dependencies Section ───────────────────────────────────────
    if ((c.dependencies||[]).length) {
        const depCards = c.dependencies.map(d => `
            <div class="flex items-center gap-3 p-3 bg-purple-50 border border-purple-100 rounded-xl">
                <div class="w-8 h-8 rounded-lg bg-purple-200 flex items-center justify-center text-purple-700 font-bold text-sm shrink-0">${esc(initial(d.type))}</div>
                <div>
                    <p class="font-semibold text-slate-700 text-sm">${esc(d.type)}</p>
                    <p class="text-xs text-slate-400 font-mono">$${esc(d.var)}</p>
                </div>
            </div>`).join('');

        h += `<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                <h3 class="text-sm font-semibold text-slate-700">Constructor Dependencies (${c.dependencies.length})</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">${depCards}</div>
        </div>`;
    }

    // ── Linked Routes ──────────────────────────────────────────────
    if (linkedRoutes.length) {
        const routeRows = linkedRoutes.map(r => {
            const ms     = (r.methods||[]).filter(m=>m!=='HEAD');
            const method = ms[0] || '?';
            const mwBadges = (r.middleware||[]).slice(0,3).map(m =>
                `<span class="text-xs bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded">${esc(m)}</span>`).join(' ');
            const name = r.name ? `<span class="text-xs text-slate-400 font-mono">${esc(r.name)}</span>` : '';
            return `<tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                <td class="py-3 px-4"><span class="text-xs font-bold px-1.5 py-0.5 rounded method-${esc(method.toLowerCase())}">${esc(method)}</span></td>
                <td class="py-3 px-4 font-mono text-sm text-slate-700">${esc(r.uri||'')}</td>
                <td class="py-3 px-4 font-mono text-xs text-slate-400">${esc(r.controller?.method || '')}</td>
                <td class="py-3 px-4">${name}</td>
                <td class="py-3 px-4"><div class="flex gap-1 flex-wrap">${mwBadges}</div></td>
            </tr>`;
        }).join('');

        h += `<div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                <h3 class="text-sm font-semibold text-slate-700">Linked Routes (${linkedRoutes.length})</h3>
            </div>
            <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-xs text-slate-400 uppercase tracking-wide border-b border-slate-100 bg-slate-50">
                    <th class="text-left py-2.5 px-4 font-medium">Method</th>
                    <th class="text-left py-2.5 px-4 font-medium">URI</th>
                    <th class="text-left py-2.5 px-4 font-medium">Action</th>
                    <th class="text-left py-2.5 px-4 font-medium">Name</th>
                    <th class="text-left py-2.5 px-4 font-medium">Middleware</th>
                </tr></thead>
                <tbody>${routeRows}</tbody>
            </table>
            </div>
        </div>`;
    }

    // ── Used Models & Services ─────────────────────────────────────
    if (usedModels.length || usedServices.length) {
        let grid = '';
        usedModels.forEach(m => {
            grid += `<div class="flex items-center gap-2 p-3 bg-emerald-50 border border-emerald-100 rounded-xl">
                <div class="w-7 h-7 rounded-lg bg-emerald-200 flex items-center justify-center text-emerald-700 font-bold text-xs">${esc(initial(m))}</div>
                <div><p class="text-sm font-semibold text-slate-700">${esc(m)}</p><p class="text-xs text-emerald-500">Model</p></div>
            </div>`;
        });
        usedServices.forEach(s => {
            grid += `<div class="flex items-center gap-2 p-3 bg-violet-50 border border-violet-100 rounded-xl">
                <div class="w-7 h-7 rounded-lg bg-violet-200 flex items-center justify-center text-violet-700 font-bold text-xs">${esc(initial(s))}</div>
                <div><p class="text-sm font-semibold text-slate-700">${esc(s)}</p><p class="text-xs text-violet-500">Service / Repo</p></div>
            </div>`;
        });

        h += `<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <h3 class="text-sm font-semibold text-slate-700">Used Models & Services</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">${grid}</div>
        </div>`;
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
        restored: {bg:'rgba(99,102,241,.08)',  color:'#6366F1'},
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
            <div style="background:rgba(99,102,241,.07);border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(99,102,241,.15);">
                <p style="font-size:22px;font-weight:700;color:#6366F1;margin:0 0 2px;">${(o.events||[]).length}</p>
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
    const clr = '#6366F1', rgb = '99,102,241';
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
    hasOne:         { color:'#6366F1', bg:'rgba(99,102,241,.12)',   border:'rgba(99,102,241,.3)'   },
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

    const PALETTE = ['#6366F1','#A78BFA','#34D399','#FBBF24','#F87171','#60A5FA'];

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

function setMapTab(tab) {
    ['graph','tree','er'].forEach(t => {
        document.getElementById('map-' + t).style.display = t === tab ? 'block' : 'none';
        const btn = document.getElementById('map-tab-' + t);
        if (btn) {
            btn.style.background = t === tab ? '#6366F1' : 'transparent';
            btn.style.color      = t === tab ? '#FFFFFF'  : '#6B7280';
            btn.style.border     = 'none';
        }
    });

    if (tab === 'graph' && !graphRendered) {
        setTimeout(initRelGraph, 50);
    }
    if (tab === 'tree' && !mapTreeRendered) {
        mapTreeRendered = true;
        renderModelTree();
    }
    if (tab === 'er' && !erRendered) {
        setTimeout(initER, 100);
    }
}


// ── Relation Graph (force-directed SVG) ───────────────────────────────────────

const RG_NW = 150, RG_NH = 60;
let _rgNodes = [], _rgSel = null, _rgAdj = {};
let _rgVp = { x: 0, y: 0, z: 1 };
let _rgW = 0, _rgH = 0;
let _rgMmParams = null;

function rgEdgeTheme(type) {
    if (type.includes('BelongsToMany') || type.includes('MorphToMany'))
        return { stroke:'#c084fc', marker:'url(#rg-arr-mm)',      markerA:'url(#rg-arr-mm-a)',      dash:'7,3' };
    if (type.includes('BelongsTo') || type.includes('MorphTo'))
        return { stroke:'#34d399', marker:'url(#rg-arr-belongs)', markerA:'url(#rg-arr-belongs-a)', dash:'none' };
    if (type.includes('Many'))
        return { stroke:'#818cf8', marker:'url(#rg-arr-many)',    markerA:'url(#rg-arr-many-a)',    dash:'none' };
    return     { stroke:'#2dd4bf', marker:'url(#rg-arr-one)',     markerA:'url(#rg-arr-one-a)',     dash:'5,3' };
}

function initRelGraph() {
    if (graphRendered) return;
    graphRendered = true;

    const models = APP.models || [];
    const svg    = document.getElementById('rg-canvas');
    if (!svg) return;
    _rgW = svg.clientWidth  || 900;
    _rgH = svg.clientHeight || 600;
    const W = _rgW, H = _rgH;

    const edgesG = document.getElementById('rg-edges-g');
    const nodesG = document.getElementById('rg-nodes-g');

    if (!models.length) {
        const t = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        t.setAttribute('x', W/2); t.setAttribute('y', H/2);
        t.setAttribute('text-anchor', 'middle'); t.setAttribute('fill', '#9CA3AF');
        t.setAttribute('font-size', '14'); t.setAttribute('font-family', 'system-ui,sans-serif');
        t.textContent = 'No models found';
        nodesG.appendChild(t);
        return;
    }

    // Deduplicate by name — avoids duplicate node IDs when two namespaces share a class basename
    const nById = {};
    const seenName = new Set();
    const nodes = [];
    models.forEach(m => {
        if (!m.name || seenName.has(m.name)) return;
        seenName.add(m.name);
        const node = { id: m.name, table: m.table || m.name.toLowerCase() + 's', rels: (m.relationships || []).length, x: 0, y: 0, vx: 0, vy: 0 };
        nById[m.name] = node;
        nodes.push(node);
    });

    // Build deduplicated edge list
    const edgeSet = new Map();
    models.forEach(m => {
        (m.relationships || []).forEach(rel => {
            const toName = rel.related ? rel.related.split('\\').pop() : null;
            if (!toName || !nById[toName] || toName === m.name) return;
            const k = m.name + '→' + toName + ':' + rel.type;
            if (!edgeSet.has(k)) edgeSet.set(k, { from: m.name, to: toName, type: rel.type });
        });
    });
    const edges = [...edgeSet.values()];

    // Separate connected nodes (have edges) from isolated ones (no edges)
    const connIds  = new Set();
    edges.forEach(e => { connIds.add(e.from); connIds.add(e.to); });
    const simNodes = nodes.filter(n =>  connIds.has(n.id));
    const isoNodes = nodes.filter(n => !connIds.has(n.id));
    const NC = simNodes.length;

    // Virtual canvas — generous but bounded so nodes stay navigable
    const VW = Math.max(W * 2.2, 1600), VH = Math.max(H * 2.2, 1200);
    const CX = VW / 2, CY = VH / 2;

    // Start connected nodes in a non-overlapping grid (circle start causes hairball collapse)
    // Spacing > node diagonal (sqrt(150²+60²) ≈ 161px) so nodes never overlap at t=0
    const SP    = 192;
    const gcols = Math.max(2, Math.ceil(Math.sqrt(NC * 1.4)));
    const grows  = Math.ceil(NC / gcols);
    simNodes.forEach((n, i) => {
        const col = i % gcols, row = Math.floor(i / gcols);
        n.x = CX - (gcols * SP) / 2 + col * SP + SP / 2 + Math.sin(i * 2.4) * 18;
        n.y = CY - (grows * SP) / 2 + row * SP + SP / 2 + Math.cos(i * 2.4) * 18;
    });

    // Force simulation — strong repel to maintain non-overlap, spring for edges
    const REPEL  = Math.max(18000, NC * 400);
    const IDEAL  = Math.max(180, Math.min(240, SP + NC * 2));
    const SPRING = 0.05, GRAV = 0.004, DAMP = 0.82;
    for (let it = 0; it < 320; it++) {
        for (let a = 0; a < NC; a++) {
            for (let b = a + 1; b < NC; b++) {
                const na = simNodes[a], nb = simNodes[b];
                const dx = na.x - nb.x, dy = na.y - nb.y;
                const d2 = Math.max(dx*dx + dy*dy, 1600), d = Math.sqrt(d2), f = REPEL / d2;
                na.vx += dx/d*f; na.vy += dy/d*f;
                nb.vx -= dx/d*f; nb.vy -= dy/d*f;
            }
        }
        edges.forEach(e => {
            const na = nById[e.from], nb = nById[e.to];
            if (!na || !nb) return;
            const dx = nb.x - na.x, dy = nb.y - na.y;
            const d = Math.sqrt(dx*dx + dy*dy) || 1, f = (d - IDEAL) * SPRING;
            na.vx += dx/d*f; na.vy += dy/d*f;
            nb.vx -= dx/d*f; nb.vy -= dy/d*f;
        });
        simNodes.forEach(n => {
            n.vx += (CX - n.x) * GRAV; n.vy += (CY - n.y) * GRAV;
            n.vx *= DAMP; n.vy *= DAMP;
            n.x = Math.max(RG_NW/2 + 20, Math.min(VW - RG_NW/2 - 20, n.x + n.vx));
            n.y = Math.max(RG_NH/2 + 20, Math.min(VH - RG_NH/2 - 20, n.y + n.vy));
        });
    }

    // Place isolated nodes in a tidy labelled section below the connected cluster
    if (isoNodes.length > 0) {
        const cxArr  = simNodes.length ? simNodes.map(n => n.x) : [CX];
        const cyArr  = simNodes.length ? simNodes.map(n => n.y) : [CY];
        const clMinX = Math.min(...cxArr) - RG_NW / 2;
        const clMaxX = Math.max(...cxArr) + RG_NW / 2;
        const clMaxY = Math.max(...cyArr) + RG_NH / 2;
        const gridW  = Math.max(clMaxX - clMinX, (RG_NW + 16) * 5);
        const isoCols = Math.max(5, Math.floor(gridW / (RG_NW + 14)));
        // Draw a section label
        const lbl = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        lbl.setAttribute('x',           clMinX);
        lbl.setAttribute('y',           clMaxY + 40);
        lbl.setAttribute('font-size',   '11');
        lbl.setAttribute('font-weight', '600');
        lbl.setAttribute('font-family', 'ui-monospace,monospace');
        lbl.setAttribute('fill',        '#9CA3AF');
        lbl.setAttribute('letter-spacing', '0.08em');
        lbl.textContent = 'STANDALONE MODELS (' + isoNodes.length + ')';
        nodesG.appendChild(lbl);
        isoNodes.forEach((n, i) => {
            n.x = clMinX + RG_NW / 2 + (i % isoCols) * (RG_NW + 14);
            n.y = clMaxY + 60 + RG_NH / 2 + Math.floor(i / isoCols) * (RG_NH + 10);
        });
    }

    _rgNodes = nodes;

    // Draw edges via createElementNS
    edges.forEach(e => {
        const na = nById[e.from], nb = nById[e.to];
        if (!na || !nb) return;
        const th   = rgEdgeTheme(e.type);
        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        path.setAttribute('class', 'rg-edge-path g-edge');
        path.setAttribute('data-from', e.from);
        path.setAttribute('data-to',   e.to);
        path.setAttribute('data-type', e.type);
        path.setAttribute('fill',           'none');
        path.setAttribute('stroke',         th.stroke);
        path.setAttribute('stroke-width',   '1.5');
        path.setAttribute('stroke-opacity', '0.4');
        path.setAttribute('marker-end',     th.marker);
        if (th.dash !== 'none') path.setAttribute('stroke-dasharray', th.dash);
        _rgSetEdgePath(path, na, nb);
        edgesG.appendChild(path);
    });

    // Draw nodes via createElementNS
    const _rgScaleInners = [];
    nodes.forEach(n => {
        const isIso = !connIds.has(n.id);
        const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        g.setAttribute('class',     'rg-node-g g-node');
        g.setAttribute('data-id',   n.id);
        g.style.cursor = 'pointer';
        g.setAttribute('transform', 'translate(' + (n.x - RG_NW/2) + ',' + (n.y - RG_NH/2) + ')');

        const inner = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        inner.style.transformOrigin = (RG_NW/2) + 'px ' + (RG_NH/2) + 'px';
        inner.style.opacity   = '0';
        inner.style.transform = 'scale(0)';

        const bg = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        bg.setAttribute('class',        'rg-node-bg g-node-bg');
        bg.setAttribute('width',        RG_NW);
        bg.setAttribute('height',       RG_NH);
        bg.setAttribute('rx',           '10');
        bg.setAttribute('fill',         isIso ? '#F8FAFC' : '#FFFFFF');
        bg.setAttribute('stroke',       isIso ? '#CBD5E1' : '#E5E7EB');
        bg.setAttribute('stroke-width', '1.5');
        bg.setAttribute('filter',       'url(#rg-f-node)');

        const bar = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        bar.setAttribute('class',  'rg-node-bar g-node-bar');
        bar.setAttribute('width',  RG_NW);
        bar.setAttribute('height', '5');
        bar.setAttribute('rx',     '5');
        bar.setAttribute('fill',   isIso ? '#94A3B8' : '#6366F1');

        const nm = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        nm.setAttribute('x',           RG_NW/2);
        nm.setAttribute('y',           '26');
        nm.setAttribute('text-anchor', 'middle');
        nm.setAttribute('font-family', 'ui-monospace,monospace');
        nm.setAttribute('font-size',   '13');
        nm.setAttribute('font-weight', '700');
        nm.setAttribute('fill',        isIso ? '#334155' : '#111827');
        nm.textContent = n.id.length > 17 ? n.id.slice(0, 16) + '…' : n.id;

        const tb = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        tb.setAttribute('x',           RG_NW/2);
        tb.setAttribute('y',           '40');
        tb.setAttribute('text-anchor', 'middle');
        tb.setAttribute('font-family', 'ui-monospace,monospace');
        tb.setAttribute('font-size',   '10');
        tb.setAttribute('fill',        '#6B7280');
        tb.textContent = n.table.length > 20 ? n.table.slice(0, 19) + '…' : n.table;

        inner.appendChild(bg); inner.appendChild(bar); inner.appendChild(nm); inner.appendChild(tb);

        if (n.rels > 0) {
            const rb = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            rb.setAttribute('x',           RG_NW - 8);
            rb.setAttribute('y',           '56');
            rb.setAttribute('text-anchor', 'end');
            rb.setAttribute('font-size',   '9');
            rb.setAttribute('font-weight', '700');
            rb.setAttribute('fill',        '#818CF8');
            rb.textContent = n.rels + 'r';
            inner.appendChild(rb);
        }
        g.appendChild(inner);
        g.addEventListener('click', ev => { ev.stopPropagation(); rgSelect(n.id); });
        nodesG.appendChild(g);
        _rgScaleInners.push(inner);
    });

    // Node scale-in: stagger each node popping from scale(0) → scale(1)
    _rgScaleInners.forEach((inn, i) => {
        setTimeout(() => {
            inn.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
            inn.style.opacity    = '1';
            inn.style.transform  = 'scale(1)';
        }, i * 20);
    });

    // Build adjacency map
    _rgAdj = {};
    nodes.forEach(n => { _rgAdj[n.id] = new Set(); });
    edges.forEach(e => {
        if (nById[e.from] && nById[e.to]) {
            _rgAdj[e.from].add(e.to);
            _rgAdj[e.to].add(e.from);
        }
    });

    // Pan & zoom interaction
    let isPan = false, panOrigin = { x: 0, y: 0 };
    const vpEl = document.getElementById('rg-vp');

    function applyVp() {
        vpEl.setAttribute('transform',
            'translate(' + (-_rgVp.x * _rgVp.z) + ',' + (-_rgVp.y * _rgVp.z) + ') scale(' + _rgVp.z + ')');
        _rgUpdateMinimap();
    }

    svg.addEventListener('mousedown', e => {
        if (!e.target.closest('.g-node')) {
            isPan = true; panOrigin = { x: e.clientX, y: e.clientY }; svg.style.cursor = 'grabbing';
        }
    });
    window.addEventListener('mousemove', e => {
        if (!isPan) return;
        _rgVp.x -= (e.clientX - panOrigin.x) / _rgVp.z;
        _rgVp.y -= (e.clientY - panOrigin.y) / _rgVp.z;
        panOrigin = { x: e.clientX, y: e.clientY };
        applyVp();
    });
    window.addEventListener('mouseup', () => { isPan = false; svg.style.cursor = 'grab'; });
    svg.addEventListener('wheel', e => {
        e.preventDefault();
        const rect   = svg.getBoundingClientRect();
        const mouseX = e.clientX - rect.left, mouseY = e.clientY - rect.top;
        const dataX  = _rgVp.x + mouseX / _rgVp.z, dataY = _rgVp.y + mouseY / _rgVp.z;
        _rgVp.z = Math.max(0.25, Math.min(4, _rgVp.z * (e.deltaY > 0 ? 0.88 : 1.14)));
        _rgVp.x = dataX - mouseX / _rgVp.z;
        _rgVp.y = dataY - mouseY / _rgVp.z;
        applyVp();
    }, { passive: false });

    svg.addEventListener('click', e => { if (e.target === svg) rgDiagClear(); });

    _rgInitMinimap(nodes, VW, VH);

    // Initial view: fit to connected cluster for readable zoom; press ⊡ to see all nodes
    const _initNodes = simNodes.length ? simNodes : nodes;
    const _ixs = _initNodes.map(n => n.x), _iys = _initNodes.map(n => n.y);
    const _iMinX = Math.min(..._ixs) - RG_NW/2 - 40;
    const _iMaxX = Math.max(..._ixs) + RG_NW/2 + 40;
    const _iMinY = Math.min(..._iys) - RG_NH/2 - 40;
    const _iMaxY = Math.max(..._iys) + RG_NH/2 + 40;
    _rgVp.z = Math.max(0.45, Math.min(1.5, Math.min(W / (_iMaxX - _iMinX), H / (_iMaxY - _iMinY)) * 0.92));
    _rgVp.x = _iMinX - (W / _rgVp.z - (_iMaxX - _iMinX)) / 2;
    _rgVp.y = _iMinY - (H / _rgVp.z - (_iMaxY - _iMinY)) / 2;
    applyVp();
}

function _rgSetEdgePath(path, na, nb) {
    const dx = nb.x - na.x, dy = nb.y - na.y;
    const d  = Math.sqrt(dx*dx + dy*dy) || 1, nx = dx/d, ny = dy/d;
    const x1 = na.x + nx*(RG_NW/2), y1 = na.y + ny*(RG_NH/2);
    const x2 = nb.x - nx*(RG_NW/2 + 6), y2 = nb.y - ny*(RG_NH/2 + 6);
    const mx = (x1+x2)/2 - ny*28, my = (y1+y2)/2 + nx*28;
    path.setAttribute('d',
        'M' + x1.toFixed(1) + ',' + y1.toFixed(1) +
        ' Q' + mx.toFixed(1) + ',' + my.toFixed(1) +
        ' ' + x2.toFixed(1) + ',' + y2.toFixed(1));
}

function _rgInitMinimap(nodes, vW, vH) {
    const mm = document.getElementById('rg-minimap');
    if (!mm) return;
    const W = vW || _rgW, H = vH || _rgH, mmW = 160, mmH = 100;
    const scale = Math.min(mmW / W, mmH / H) * 0.88;
    const offX  = (mmW - W * scale) / 2, offY = (mmH - H * scale) / 2;

    const bg = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
    bg.setAttribute('width', mmW); bg.setAttribute('height', mmH); bg.setAttribute('fill', '#F9FAFB');
    mm.appendChild(bg);

    nodes.forEach(n => {
        const dot = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        dot.setAttribute('x',       offX + (n.x - RG_NW/2) * scale);
        dot.setAttribute('y',       offY + (n.y - RG_NH/2) * scale);
        dot.setAttribute('width',   Math.max(4, RG_NW * scale));
        dot.setAttribute('height',  Math.max(3, RG_NH * scale));
        dot.setAttribute('rx',      '2');
        dot.setAttribute('fill',    '#6366F1');
        dot.setAttribute('opacity', '0.45');
        mm.appendChild(dot);
    });

    const vr = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
    vr.setAttribute('id',           'rg-mm-vp');
    vr.setAttribute('fill',         'rgba(99,102,241,0.06)');
    vr.setAttribute('stroke',       '#6366F1');
    vr.setAttribute('stroke-width', '1.5');
    vr.setAttribute('rx',           '2');
    mm.appendChild(vr);

    _rgMmParams = { scale, offX, offY, mmW, mmH };
}

function _rgUpdateMinimap() {
    const vr = document.getElementById('rg-mm-vp');
    if (!vr || !_rgMmParams) return;
    const { scale, offX, offY, mmW, mmH } = _rgMmParams;
    const W = _rgW, H = _rgH;
    const vpW = W / _rgVp.z, vpH = H / _rgVp.z;
    vr.setAttribute('x',      Math.max(0, offX + _rgVp.x * scale));
    vr.setAttribute('y',      Math.max(0, offY + _rgVp.y * scale));
    vr.setAttribute('width',  Math.min(mmW, vpW * scale));
    vr.setAttribute('height', Math.min(mmH, vpH * scale));
}

function rgSelect(id) {
    if (_rgSel === id) { rgDiagClear(); return; }
    _rgSel = id;
    const conn = _rgAdj[id] || new Set();

    document.querySelectorAll('.g-node').forEach(g => {
        const nid = g.getAttribute('data-id');
        const bg  = g.querySelector('.g-node-bg'), bar = g.querySelector('.g-node-bar');
        if (!bg || !bar) return;
        if (nid === id) {
            bg.setAttribute('stroke',       '#6366F1');
            bg.setAttribute('stroke-width', '2.5');
            bg.setAttribute('filter',       'url(#rg-f-node-sel)');
            bar.setAttribute('fill', '#6366F1');
            g.setAttribute('opacity', '1');
        } else if (conn.has(nid)) {
            bg.setAttribute('stroke',       '#34D399');
            bg.setAttribute('stroke-width', '2');
            bg.setAttribute('filter',       'url(#rg-f-node-rel)');
            bar.setAttribute('fill', '#34D399');
            g.setAttribute('opacity', '1');
        } else {
            bg.setAttribute('stroke',       'rgba(229,231,235,0.5)');
            bg.setAttribute('stroke-width', '1.5');
            bg.setAttribute('filter',       'url(#rg-f-node)');
            bar.setAttribute('fill', '#6366F1');
            g.setAttribute('opacity', '0.2');
        }
    });

    document.querySelectorAll('.g-edge').forEach(p => {
        const from = p.getAttribute('data-from'), to = p.getAttribute('data-to');
        const type = p.getAttribute('data-type');
        if (from === id || to === id) {
            const th = rgEdgeTheme(type);
            p.setAttribute('stroke-width',   '2.5');
            p.setAttribute('stroke-opacity', '0.95');
            p.setAttribute('marker-end',     th.markerA);
        } else {
            p.setAttribute('stroke-width',   '1');
            p.setAttribute('stroke-opacity', '0.07');
        }
    });

    // Update info strip
    const n     = _rgNodes.find(n => n.id === id);
    const model = (APP.models || []).find(m => m.name === id);
    const rels  = model ? (model.relationships || []) : [];
    document.getElementById('rg-info-name').textContent  = id;
    document.getElementById('rg-info-table').textContent = n ? n.table : '';
    document.getElementById('rg-info-count').textContent =
        rels.length + ' relationship' + (rels.length !== 1 ? 's' : '');

    // Populate relationship cards
    const cardsEl = document.getElementById('rg-rels-cards');
    cardsEl.innerHTML = '';
    document.getElementById('rg-rels-title').textContent = id + ' relationships';
    rels.forEach(e => {
        const other = e.related ? e.related.split('\\').pop() : '?';
        const th    = rgEdgeTheme(e.type);
        const card  = document.createElement('div');
        card.style.cssText = 'display:flex;flex-direction:column;gap:4px;padding:10px 12px;border-radius:10px;border:1px solid #E5E7EB;border-left:3px solid ' + th.stroke + ';background:#FFFFFF;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);transition:box-shadow 0.2s;';
        card.innerHTML =
            '<div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">' +
                '<span style="font-size:11px;font-weight:700;color:#111827;font-family:ui-monospace,monospace;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + other + '</span>' +
                '<span style="font-size:10px;font-family:ui-monospace,monospace;padding:2px 6px;border-radius:4px;background:' + th.stroke + '22;color:' + th.stroke + '">→</span>' +
            '</div>' +
            '<span style="font-size:10px;font-weight:600;color:' + th.stroke + ';font-family:ui-monospace,monospace;">' + e.type + '</span>' +
            '<span style="font-size:10px;color:#6B7280;font-family:ui-monospace,monospace;">' + (e.method || '') + '()</span>';
        cardsEl.appendChild(card);
    });

    document.getElementById('rg-rels-panel').style.display = 'none';
    document.getElementById('rg-rels-chevron').style.transform = '';
    document.getElementById('rg-info-row').style.display = 'flex';
    document.getElementById('rg-legend').style.display = 'none';
    document.getElementById('rg-clear-btn').style.display = '';
}

function rgDiagClear() {
    _rgSel = null;
    const si = document.getElementById('rg-search-input');
    if (si) si.value = '';
    document.querySelectorAll('.g-node').forEach(g => {
        g.setAttribute('opacity', '1');
        const bg  = g.querySelector('.g-node-bg'), bar = g.querySelector('.g-node-bar');
        if (bg)  { bg.setAttribute('stroke', '#E5E7EB'); bg.setAttribute('stroke-width', '1.5'); bg.setAttribute('filter', 'url(#rg-f-node)'); }
        if (bar) bar.setAttribute('fill', '#6366F1');
    });
    document.querySelectorAll('.g-edge').forEach(p => {
        const th = rgEdgeTheme(p.getAttribute('data-type'));
        p.setAttribute('stroke',         th.stroke);
        p.setAttribute('stroke-width',   '1.5');
        p.setAttribute('stroke-opacity', '0.4');
        p.setAttribute('marker-end',     th.marker);
    });
    document.getElementById('rg-info-row').style.display = 'none';
    document.getElementById('rg-rels-panel').style.display = 'none';
    document.getElementById('rg-rels-chevron').style.transform = '';
    document.getElementById('rg-legend').style.display = 'flex';
    document.getElementById('rg-clear-btn').style.display = 'none';
}

function rgToggleRels() {
    const panel   = document.getElementById('rg-rels-panel');
    const chevron = document.getElementById('rg-rels-chevron');
    const isHidden = panel.style.display === 'none';
    panel.style.display = isHidden ? 'block' : 'none';
    chevron.style.transform = isHidden ? 'rotate(180deg)' : '';
}

function graphZoom(factor) {
    if (!_rgW) return;
    const W = _rgW, H = _rgH;
    const cx = _rgVp.x + W / (2 * _rgVp.z);
    const cy = _rgVp.y + H / (2 * _rgVp.z);
    _rgVp.z  = Math.max(0.25, Math.min(4, _rgVp.z * factor));
    _rgVp.x  = cx - W / (2 * _rgVp.z);
    _rgVp.y  = cy - H / (2 * _rgVp.z);
    const vpEl = document.getElementById('rg-vp');
    if (vpEl) vpEl.setAttribute('transform',
        'translate(' + (-_rgVp.x * _rgVp.z) + ',' + (-_rgVp.y * _rgVp.z) + ') scale(' + _rgVp.z + ')');
    _rgUpdateMinimap();
}

function graphFit() {
    if (!_rgNodes.length || !_rgW) return;
    const W = _rgW, H = _rgH;
    const xs = _rgNodes.map(n => n.x), ys = _rgNodes.map(n => n.y);
    const minX = Math.min(...xs) - RG_NW/2 - 20, maxX = Math.max(...xs) + RG_NW/2 + 20;
    const minY = Math.min(...ys) - RG_NH/2 - 20, maxY = Math.max(...ys) + RG_NH/2 + 20;
    _rgVp.z = Math.max(0.25, Math.min(4, Math.min(W / (maxX - minX), H / (maxY - minY))));
    _rgVp.x = minX - (W/_rgVp.z - (maxX - minX)) / 2;
    _rgVp.y = minY - (H/_rgVp.z - (maxY - minY)) / 2;
    const vpEl = document.getElementById('rg-vp');
    if (vpEl) vpEl.setAttribute('transform',
        'translate(' + (-_rgVp.x * _rgVp.z) + ',' + (-_rgVp.y * _rgVp.z) + ') scale(' + _rgVp.z + ')');
    _rgUpdateMinimap();
}

function graphCenterView() {
    if (!_rgNodes.length || !_rgW) return;
    const W = _rgW, H = _rgH;
    const xs = _rgNodes.map(n => n.x), ys = _rgNodes.map(n => n.y);
    const minX = Math.min(...xs), maxX = Math.max(...xs);
    const minY = Math.min(...ys), maxY = Math.max(...ys);
    const bx = maxX - minX + RG_NW + 40, by = maxY - minY + RG_NH + 40;
    const cx = (minX + maxX) / 2, cy = (minY + maxY) / 2;
    const fitZ = Math.min(W / bx, H / by);
    // Start at min 0.75× so node text (~10px) stays legible; user can zoom/fit for overview
    _rgVp.z = Math.max(0.75, Math.min(4, fitZ));
    _rgVp.x = cx - W / (2 * _rgVp.z);
    _rgVp.y = cy - H / (2 * _rgVp.z);
    const vpEl = document.getElementById('rg-vp');
    if (vpEl) vpEl.setAttribute('transform',
        'translate(' + (-_rgVp.x * _rgVp.z) + ',' + (-_rgVp.y * _rgVp.z) + ') scale(' + _rgVp.z + ')');
    _rgUpdateMinimap();
}

function graphReset() {
    _rgVp = { x: 0, y: 0, z: 1 };
    const vpEl = document.getElementById('rg-vp');
    if (vpEl) vpEl.setAttribute('transform', 'translate(0,0) scale(1)');
    _rgUpdateMinimap();
    rgDiagClear();
}

function graphSearch(query) {
    query = (query || '').toLowerCase().trim();
    if (!query) { rgDiagClear(); return; }
    document.querySelectorAll('.g-node').forEach(g => {
        const nid   = g.getAttribute('data-id');
        const match = nid.toLowerCase().includes(query);
        g.setAttribute('opacity', match ? '1' : '0.12');
        const bg = g.querySelector('.g-node-bg'), bar = g.querySelector('.g-node-bar');
        if (bg) {
            bg.setAttribute('stroke',       match ? '#6366F1' : 'rgba(229,231,235,0.5)');
            bg.setAttribute('stroke-width', match ? '2.5'     : '1.5');
            bg.setAttribute('filter',       match ? 'url(#rg-f-node-sel)' : 'url(#rg-f-node)');
        }
        if (bar) bar.setAttribute('fill', match ? '#6366F1' : 'rgba(99,102,241,0.15)');
    });
    document.querySelectorAll('.g-edge').forEach(p => {
        const from  = p.getAttribute('data-from'), to = p.getAttribute('data-to');
        const match = from.toLowerCase().includes(query) || to.toLowerCase().includes(query);
        p.setAttribute('stroke-opacity', match ? '0.7'  : '0.04');
        p.setAttribute('stroke-width',   match ? '2'    : '1');
    });
    document.getElementById('rg-clear-btn').style.display = '';
}


// ── API Docs ──────────────────────────────────────────────────────────────────

let _apiActiveMethod = 'ALL';

function apiToggle(uid) {
    const detail  = document.getElementById('detail-'  + uid);
    const chevron = document.getElementById('chevron-' + uid);
    if (!detail) return;
    const wasHidden = detail.style.display === 'none' || !detail.style.display;
    detail.style.display = wasHidden ? 'block' : 'none';
    chevron.style.transform = wasHidden ? 'rotate(180deg)' : '';
    if (wasHidden) apiRenderFlow(uid);
}

function apiRenderFlow(uid) {
    const el = document.getElementById('api-flow-' + uid);
    if (!el || el.dataset.rendered) return;
    el.dataset.rendered = '1';

    const ctrl   = el.dataset.controller ? el.dataset.controller.split('\\').pop() : null;
    const action = el.dataset.action || null;
    const method = el.dataset.method || '';
    const uri    = el.dataset.uri || '';
    const rname  = el.dataset.rname || '';
    let mws = [];
    try { mws = JSON.parse(el.dataset.mws || '[]'); } catch(e) { mws = []; }
    const chain = ctrl ? traceChain(ctrl) : [];

    // No chain and no ctrl: nothing to show
    if (!ctrl && !chain.length) return;

    // Build local graph (use unique prefix to avoid DOM id collisions)
    let cnt = 0;
    const pfx    = 'af' + uid.replace(/[^a-z0-9]/gi, '');
    const lNodes = {}, lEdges = [];
    const mkN = (type, name, sub) => {
        const id = pfx + (cnt++);
        lNodes[id] = { id, type, name: name || '', sub: sub || '' };
        return id;
    };
    const mkE = (f, t, label) => lEdges.push({ from: f, to: t, label: label || '' });

    const rId = mkN('request', (method ? method + ' ' : '') + '/' + uri, rname);
    let prev = rId;

    if (mws.length) {
        const mId = mkN('middleware', 'Middleware Stack', mws.length + ' layer' + (mws.length > 1 ? 's' : ''));
        mkE(prev, mId, 'enters');
        prev = mId;
    }

    if (chain.length) {
        let last = prev;
        chain.forEach((node, i) => {
            const sub = i === 0
                ? (action && action !== '__invoke' ? '@' + action : 'Invokable')
                : (RF_TYPE_LABEL[node.layer] || node.layer);
            const nid = mkN(node.layer, node.name, sub);
            mkE(last, nid, i === 0 ? 'dispatches' : (EDGE_LABEL[node.edgeType] || node.edgeType || 'calls'));
            last = nid;
        });
        const mn = chain.find(n => n.layer === 'model');
        if (mn) {
            const md = (APP.models || []).find(m => m.name === mn.name);
            if (md?.table) {
                const dbId  = mkN('database', md.table, 'DB Table');
                const mnId  = Object.values(lNodes).find(n => n.name === mn.name && n.type === 'model')?.id;
                if (mnId) mkE(mnId, dbId, 'queries');
            }
        }
    } else if (ctrl) {
        const cid = mkN('controller', ctrl, action && action !== '__invoke' ? '@' + action : 'Invokable');
        mkE(prev, cid, 'dispatches');
    }

    // Horizontal layout (left → right, single row)
    const NW = 196, NH = 66, GAP_X = 72, PAD = 20;
    const nodeList = Object.values(lNodes);
    const CW = nodeList.length * NW + (nodeList.length - 1) * GAP_X + PAD * 2;
    const CH = NH + PAD * 2;
    const pos = {};
    nodeList.forEach((n, i) => {
        const x = PAD + i * (NW + GAP_X);
        pos[n.id] = { x, y: PAD, cx: x + NW / 2, cy: PAD + NH / 2 };
    });

    // Unique SVG ids
    const dotId = pfx + 'dot', arrId = pfx + 'arr';

    const defs = `<defs>
        <pattern id="${dotId}" x="0" y="0" width="22" height="22" patternUnits="userSpaceOnUse">
            <circle cx="1" cy="1" r="0.8" fill="rgba(148,178,222,0.15)" opacity="1"/>
        </pattern>
        <marker id="${arrId}" markerWidth="9" markerHeight="7" refX="8" refY="3.5" orient="auto">
            <polygon points="0 0,9 3.5,0 7" fill="rgba(148,178,222,0.5)"/>
        </marker>
    </defs>`;

    const edgesSvg = lEdges.map(e => {
        const f = pos[e.from], t = pos[e.to];
        if (!f || !t) return '';
        const x1 = f.x + NW, y1 = f.cy, x2 = t.x, y2 = t.cy;
        const cp = (x2 - x1) * 0.45;
        const d  = `M${x1},${y1} C${x1+cp},${y1} ${x2-cp},${y2} ${x2},${y2}`;
        const mx = (x1 + x2) / 2, my = (y1 + y2) / 2;
        const lw = (e.label.length * 6) + 14;
        return `<g>
            <path d="${d}" fill="none" stroke="rgba(148,178,222,0.35)" stroke-width="1.5" marker-end="url(#${arrId})"/>
            ${e.label ? `<rect x="${mx-lw/2}" y="${my-8}" width="${lw}" height="16" rx="8" fill="#FFFFFF" stroke="#DFE1E6" stroke-width="1"/>
            <text x="${mx}" y="${my+4}" fill="#6B778C" font-size="9" font-family="ui-monospace,monospace" text-anchor="middle" font-weight="600">${_esc(e.label)}</text>` : ''}
        </g>`;
    }).join('');

    const nodesSvg = nodeList.map(n => {
        const p = pos[n.id]; if (!p) return '';
        const c  = RF_COLOR[n.type] || RF_COLOR.unknown;
        const tl = RF_TYPE_LABEL[n.type] || n.type;
        const sn = n.name.length > 21 ? n.name.slice(0, 20) + '…' : n.name;
        const ss = n.sub.length  > 24 ? n.sub.slice(0, 23)  + '…' : n.sub;
        return `<g>
            <rect x="${p.x}" y="${p.y}" width="${NW}" height="${NH}" rx="12" fill="${c.bg}" stroke="${c.border}" stroke-width="1.5"/>
            <rect x="${p.x+1.5}" y="${p.y+1.5}" width="${NW-3}" height="18" rx="10" fill="${c.border}" fill-opacity="0.15"/>
            <circle cx="${p.x+12}" cy="${p.y+10}" r="3" fill="${c.dot}"/>
            <text x="${p.x+21}" y="${p.y+14}" fill="${c.type}" font-size="7.5" font-family="ui-monospace,monospace" font-weight="700" letter-spacing="0.1em">${tl.toUpperCase()}</text>
            <text x="${p.x+10}" y="${p.y+40}" fill="${c.name}" font-size="11.5" font-family="ui-monospace,monospace" font-weight="700">${_esc(sn)}</text>
            ${ss ? `<text x="${p.x+10}" y="${p.y+56}" fill="${c.sub}" font-size="9.5" font-family="ui-monospace,monospace">${_esc(ss)}</text>` : ''}
        </g>`;
    }).join('');

    el.innerHTML = `
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:10px;padding-top:4px">
            <div style="width:3px;height:14px;border-radius:2px;background:#6366F1;flex-shrink:0"></div>
            <p style="font-size:10px;font-weight:700;color:#6B778C;text-transform:uppercase;letter-spacing:0.1em;margin:0;font-family:ui-monospace,monospace">Request Flow</p>
            <span style="font-size:9px;color:#6B778C;font-family:ui-monospace,monospace">${nodeList.length} nodes · ${lEdges.length} edges</span>
        </div>
        <div style="overflow-x:auto;border-radius:12px;border:1px solid #DFE1E6;background:#F7F8F9">
            <svg width="${CW}" height="${CH}" style="display:block;min-width:${CW}px">
                ${defs}
                <rect width="${CW}" height="${CH}" fill="#F7F8F9"/>
                <rect width="${CW}" height="${CH}" fill="url(#${dotId})"/>
                ${edgesSvg}
                ${nodesSvg}
            </svg>
        </div>`;
}

function apiScrollTo(groupName) {
    const el = document.getElementById('api-group-' + groupName);
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        document.querySelectorAll('.api-nav-item').forEach(a => {
            a.style.background = 'var(--bg-elevated)';
            a.style.color      = 'var(--text-dim)';
            a.style.borderColor= 'var(--border)';
        });
        const tab = document.querySelector(`.api-nav-item[data-group-tab="${groupName}"]`);
        if (tab) {
            tab.style.background  = 'rgba(99,102,241,0.08)';
            tab.style.color       = 'var(--cyan)';
            tab.style.borderColor = 'rgba(99,102,241,0.25)';
        }
    }
}

function apiFilter(method) {
    _apiActiveMethod = method;
    document.querySelectorAll('.api-filter-btn').forEach(btn => {
        const active = btn.dataset.method === method;
        btn.style.background    = active ? 'var(--bg-hover)' : 'transparent';
        btn.style.color         = active ? 'var(--cyan)'     : 'var(--text-dim)';
        btn.style.borderColor   = active ? 'var(--cyan)'     : 'var(--border)';
        btn.style.fontWeight    = active ? '700'             : '500';
    });
    _apiApplyFilters();
}

function apiSearch(q) {
    _apiApplyFilters(q.toLowerCase().trim());
}

function _apiApplyFilters(q) {
    const query  = q ?? (document.getElementById('api-search')?.value || '').toLowerCase().trim();
    const method = _apiActiveMethod;

    document.querySelectorAll('.api-endpoint-wrap').forEach(ep => {
        const matchMethod = method === 'ALL' || ep.dataset.method === method;
        const matchQuery  = !query || ep.dataset.uri.includes(query);
        ep.style.display  = matchMethod && matchQuery ? '' : 'none';
    });

    // Hide group headers if all their endpoints are hidden
    document.querySelectorAll('.api-group').forEach(grp => {
        const visible = [...grp.querySelectorAll('.api-endpoint-wrap')].some(ep => ep.style.display !== 'none');
        grp.style.display = visible ? '' : 'none';
    });
}

// ── Boot ──────────────────────────────────────────────────────────────────────
mermaid.initialize({
    startOnLoad: false,
    theme: 'base',
    themeVariables: {
        background:                      '#FFFFFF',
        primaryColor:                    '#EEF2FF',      // entity box fill — soft red tint
        primaryBorderColor:              '#6366F1',      // entity border
        primaryTextColor:                '#1D1D1F',      // entity text — charcoal
        lineColor:                       '#818CF8',      // relationship lines
        secondaryColor:                  '#F0FDF4',
        tertiaryColor:                   '#FFF7ED',
        edgeLabelBackground:             '#FFFFFF',
        attributeBackgroundColorEven:    '#FAFAFA',
        attributeBackgroundColorOdd:     '#FFFFFF',
        fontFamily:                      "'Inter', ui-sans-serif, sans-serif",
        fontSize:                        '13px',
    },
    er: {
        diagramPadding:  20,
        layoutDirection: 'TB',
        minEntityWidth:  100,
        minEntityHeight: 75,
        entityPadding:   15,
        useMaxWidth:     true,
    },
    flowchart: { rankSpacing:80, nodeSpacing:40, curve:'basis', padding:20 }
});

navigate('overview');

// ── Architecture Explorer ────────────────────────────────────────────────────
const OV_VIOLET = [101,84,192], OV_CYAN_RGB = [0,82,204];
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
        const g = _svgEl('g', { class:'ov-arch-node', tabindex:'0', role:'button', 'aria-label':pos.label });
        g.dataset.id = id;
        const x = pos.x - BOX_W/2, y = pos.y - BOX_H/2;

        // "more" nodes get a dashed, dimmer style
        const rectFill   = pos.isMore ? 'rgba(99,102,241,0.04)' : '#FFFFFF';
        const rectStroke = pos.isMore ? `rgba(99,102,241,0.25)` : color;
        const rectDash   = pos.isMore ? '4,3' : 'none';
        const rect = _svgEl('rect', { x, y, width:BOX_W, height:BOX_H, rx:'10', fill:rectFill, stroke:rectStroke, 'stroke-width':'1.5', 'stroke-dasharray':rectDash, filter:'url(#ov-shadow)' });

        const dotR = pos.isMore ? '2.5' : '4';
        const dotEl = _svgEl('circle', { cx:x+16, cy:y+BOX_H/2, r:dotR, fill: pos.isMore ? 'rgba(99,102,241,0.35)' : color });

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

    // Hover highlight / detail
    const detail = document.getElementById('ovArchDetail');
    const defaultDetail = detail ? detail.innerHTML : '';
    const neighbors = {};
    EDGES.forEach(([f, t]) => { (neighbors[f]=neighbors[f]||new Set()).add(t); (neighbors[t]=neighbors[t]||new Set()).add(f); });

    function ovHighlight(id) {
        const rel = neighbors[id] || new Set();
        edgeEls.forEach(p => { const c = p.dataset.from===id||p.dataset.to===id; p.style.opacity=c?'0.9':'0.05'; p.style.strokeWidth=c?'2.2':'1.5'; });
        nodeEls.forEach(nd => { nd.style.opacity=(nd.dataset.id===id||rel.has(nd.dataset.id))?'1':'0.25'; });
        if (detail) { const pos=positions[id]; detail.textContent = `${pos.label} · ${pos.sub||pos.layerName} — ${rel.size} connection${rel.size===1?'':'s'}`; }
    }
    function ovReset() {
        edgeEls.forEach(p => { p.style.opacity='0.3'; p.style.strokeWidth='1.5'; });
        nodeEls.forEach(nd => { nd.style.opacity='1'; });
        if (detail) detail.innerHTML = defaultDetail;
    }
    nodeEls.forEach(nd => {
        nd.addEventListener('mouseenter', () => ovHighlight(nd.dataset.id));
        nd.addEventListener('focus',      () => ovHighlight(nd.dataset.id));
        nd.addEventListener('mouseleave', ovReset);
        nd.addEventListener('blur',       ovReset);
    });

    // Zoom controls
    const zoomIn  = document.getElementById('ovZoomIn');
    const zoomOut = document.getElementById('ovZoomOut');
    function _applyOvZoom() { svg.setAttribute('width', VB_W*_ovArchScale); svg.setAttribute('height', VB_H*_ovArchScale); }
    if (zoomIn)  zoomIn.addEventListener('click',  () => { _ovArchScale = Math.min(1.7, _ovArchScale+0.15); _applyOvZoom(); });
    if (zoomOut) zoomOut.addEventListener('click', () => { _ovArchScale = Math.max(0.6, _ovArchScale-0.15); _applyOvZoom(); });
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
            document.querySelectorAll('#sec-overview [data-ov-reveal]').forEach(el => {
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
    controller: { label:'Controllers', color:'#6366F1', bg:'#EEF2FF', order:0 },
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
        p.setAttribute('stroke',       on ? '#6366F1' : 'rgba(148,178,222,0.15)');
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
    const dlBtn  = document.getElementById('doc-dl-btn-' + type);

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
        dlBtn.style.display = 'inline-flex';

        // Show preview + html download buttons
        const previewBtn = document.getElementById('doc-preview-btn-' + type);
        if (previewBtn) previewBtn.style.display = 'inline-flex';
        const dlHtmlBtn = document.getElementById('doc-dl-html-btn-' + type);
        if (dlHtmlBtn) dlHtmlBtn.style.display = 'inline-flex';

        // Populate excerpt snippet
        const excerptEl   = document.getElementById('doc-excerpt-' + type);
        const excerptText = document.getElementById('doc-excerpt-text-' + type);
        if (excerptEl && excerptText) {
            excerptText.textContent = _mdExcerpt(json.content, 260);
            excerptEl.style.display = 'block';
        }

        // Show "Download All" if at least one doc is ready
        document.getElementById('docs-download-all-btn').style.display = 'inline-flex';

    } catch (err) {
        status.textContent = '✘ Failed';
        status.style.color = 'var(--rose)';
        btn.innerHTML = `<svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Retry`;
        btn.disabled = false;
    }
}

async function docsGenerateAll() {
    for (const type of DOC_TYPES) {
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

        // ── Steps 2–7: AI docs per section ─────────────────────────────────
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
                // Non-fatal: skip section, mark with warning
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
        ['Services', s.services],
        ['Repositories', s.repositories], ['Observers', s.observers],
        ['Policies', s.policies], ['Modules', s.modules], ['Packages', s.packages],
        ['Dep. Edges', (d.dependencies?.edges || []).length],
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
        controller: { fill:'#EEF2FF', stroke:'#6366F1', text:'#172B4D' },
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
        return `<path d="M${x1},${y1} C${x1},${y1+cp} ${x2},${y2-cp} ${x2},${y2}" fill="none" stroke="rgba(99,102,241,0.15)" stroke-width="1.4" marker-end="url(#dep-arr)"/>`;
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
    document.getElementById('ai-results').style.display = 'none';
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
    document.getElementById('ai-score-num').textContent = score;
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

    document.getElementById('ai-results').style.display = 'block';
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
window.addEventListener('load',   () => _moveNavIndicator('overview'));
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


</body>
</html>
