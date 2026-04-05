<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    // List all units
    public function index()
    {
        return response()->json(Unit::all());
    }

    // Save a new Unit
    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_name' => 'required|string|max:20|unique:tbl_unit,unit_name',
        ]);

        $unit = Unit::create($validated);
        return response()->json($unit, 201);
    }

    // Show a specific Unit
    public function show(Unit $unit)
    {
        return response()->json($unit);
    }

    // Update a Unit
    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'unit_name' => 'required|string|max:20|unique:tbl_unit,unit_name,' . $unit->id,
        ]);

        $unit->update($validated);
        return response()->json($unit);
    }

    // Delete a Unit
    public function destroy(Unit $unit)
    {
        $unit->delete();
        return response()->json(['message' => 'Unit deleted successfully']);
    }
}
