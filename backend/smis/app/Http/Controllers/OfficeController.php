<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    // List all offices
    public function index() {
        return response()->json(Office::all());
    }

    // Save a new Office
    public function store(Request $request)
    {
        $validated = $request->validate([
            'office_name' => 'required|string|max:100|unique:tbl_office,office_name',
        ]);

        $office = Office::create($validated);
        return response()->json($office, 201);
    }

    // Show a specific Office
    public function show(Office $office)
    {
        return response()->json($office);
    }

    // Update an Office
    public function update(Request $request, Office $office)
    {
        $validated = $request->validate([
            'office_name' => 'required|string|max:100|unique:tbl_office,office_name,' . $office->id,
        ]);

        $office->update($validated);
        return response()->json($office);
    }

    // Delete an Office
    public function destroy(Office $office)
    {
        $office->delete();
        return response()->json(['message' => 'Office deleted successfully']);
    }
}   