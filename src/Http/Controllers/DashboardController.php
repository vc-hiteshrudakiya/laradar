<?php

namespace Hitesh\LaravelArchitectureDiscovery\Http\Controllers;

use Illuminate\Routing\Controller;
use Hitesh\LaravelArchitectureDiscovery\ArchitectureDiscovery;

class DashboardController extends Controller
{
    public function __invoke(ArchitectureDiscovery $discovery)
    {
        $report = $discovery->discover();
        $data   = $report->getReport();

        return view('architecture-discovery::dashboard', compact('data'));
    }
}
