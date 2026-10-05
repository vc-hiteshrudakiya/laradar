@extends('laradar::layouts.laradar')
@section('content')
@php
$typeLabel = match($type ?? '') {
    'controllers'  => 'Controller',
    'models'       => 'Model',
    'routes'       => 'Route',
    'migrations'   => 'Migration',
    'jobs'         => 'Job',
    'events'       => 'Event',
    'services'     => 'Service',
    'repositories' => 'Repository',
    'observers'    => 'Observer',
    'policies'     => 'Policy',
    'modules'      => 'Module',
    'middleware'   => 'Middleware',
    'packages'     => 'Package',
    default        => 'Item',
};
$listRoute = match($type ?? '') {
    'controllers'  => route('laradar.controllers'),
    'models'       => route('laradar.models'),
    'routes'       => route('laradar.routes'),
    'migrations'   => route('laradar.migrations'),
    'jobs'         => route('laradar.jobs'),
    'events'       => route('laradar.events'),
    'services'     => route('laradar.services'),
    'repositories' => route('laradar.repositories'),
    'observers'    => route('laradar.observers'),
    'policies'     => route('laradar.policies'),
    'modules'      => route('laradar.modules'),
    'middleware'   => route('laradar.middleware'),
    'packages'     => route('laradar.packages'),
    default        => route('laradar.dashboard'),
};
$listLabel = match($type ?? '') {
    'controllers'  => 'All Controllers',
    'models'       => 'All Models',
    'routes'       => 'All Routes',
    'migrations'   => 'All Migrations',
    'jobs'         => 'All Jobs',
    'events'       => 'All Events',
    'services'     => 'All Services',
    'repositories' => 'All Repositories',
    'observers'    => 'All Observers',
    'policies'     => 'All Policies',
    'modules'      => 'All Modules',
    'middleware'   => 'All Middleware',
    'packages'     => 'All Packages',
    default        => 'Dashboard',
};
@endphp

<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px 24px;text-align:center;">

    {{-- Icon --}}
    <div style="width:64px;height:64px;border-radius:16px;background:rgba(255,45,32,.08);border:1.5px solid rgba(255,45,32,.2);display:flex;align-items:center;justify-content:center;margin-bottom:24px;">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#FF2D20" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/><path d="M11 8v4M11 16h.01"/>
        </svg>
    </div>

    {{-- Title --}}
    <h2 style="font-size:20px;font-weight:800;color:var(--text);margin:0 0 8px;letter-spacing:-0.3px;">
        {{ $typeLabel }} not found in scan
    </h2>

    {{-- Name --}}
    <code style="font-family:var(--font-mono);font-size:13px;color:#FF2D20;background:rgba(255,45,32,.08);border:1px solid rgba(255,45,32,.2);padding:4px 12px;border-radius:6px;display:inline-block;margin-bottom:14px;">
        {{ $name ?? 'Unknown' }}
    </code>

    {{-- Reason --}}
    <p style="font-size:13px;color:var(--text-faint);max-width:420px;line-height:1.7;margin:0 0 32px;">
        This {{ strtolower($typeLabel) }} is referenced in your project but no matching file was found during the architecture scan.
        The file may have been deleted, moved, or not yet created.
    </p>

    {{-- Back button --}}
    <a href="{{ $listRoute }}"
       onclick="event.preventDefault(); showList('{{ $type ?? '' }}', false)"
       style="display:inline-flex;align-items:center;gap:8px;font-family:var(--font-mono);font-size:12px;font-weight:700;padding:10px 20px;border-radius:8px;background:#FF2D20;color:#fff;text-decoration:none;border:none;cursor:pointer;transition:opacity .15s;"
       onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
        {{ $listLabel }}
    </a>

</div>
@endsection
