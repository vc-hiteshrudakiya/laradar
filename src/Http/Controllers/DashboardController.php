<?php

namespace Vcian\Laradar\Http\Controllers;

use Illuminate\Routing\Controller;
use Vcian\Laradar\ArchitectureDiscovery;

class DashboardController extends Controller
{
    public function __invoke(ArchitectureDiscovery $discovery)
    {
        $report = $discovery->discover();
        $data   = $report->getReport();

        return view('laradar::dashboard', compact('data'));
    }
}
