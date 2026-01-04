<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CoordinatorController extends Controller
{
    /**
     * Display the coordinator dashboard.
     */
    public function dashboard()
    {
        return view('coordinator.dashboard');
    }

    /**
     * Display the monitor interns page.
     */
    public function monitorInterns()
    {
        return view('coordinator.monitor-interns');
    }

    /**
     * Display the support documents page.
     */
    public function supportDocs()
    {
        return view('coordinator.support-docs');
    }

    /**
     * Display the settings page.
     */
    public function settings()
    {
        return view('coordinator.settings');
    }
}
