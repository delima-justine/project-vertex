<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HRController extends Controller
{
    public function dashboard()
    {
        return view('hr.dashboard');
    }

    public function interns()
    {
        // Temporary sample data. Replace with real models later.
        $interns = [
            [
                'name' => 'Emma Davis',
                'role' => 'Marketing Intern',
                'week' => 8,
                'of' => 8,
                'progress' => 75,
                'status' => 'on-track',
            ],
            [
                'name' => 'Frank Miller',
                'role' => 'Software Developer Intern',
                'week' => 4,
                'of' => 8,
                'progress' => 50,
                'status' => 'on-track',
            ],
            [
                'name' => 'Grace Lee',
                'role' => 'Design Intern',
                'week' => 2,
                'of' => 8,
                'progress' => 30,
                'status' => 'needs-attention',
            ],
        ];

        return view('hr.interns', compact('interns'));
    }
}