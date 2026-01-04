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
}
