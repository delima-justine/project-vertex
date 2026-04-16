<?php

namespace App\Http\Controllers;

use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplyController extends Controller
{
    // List all supplies
    public function index() 
    {
        return response()->json(Supply::with(['category', 'unit'])->get());
    }

    // Save a new supply
    public function store(Request $request)
    {
        $validated = $request->validate([
            'stock_num' => 'required|string|unique:tbl_supply,stock_num',
            'item_desc' => 'required|string',
            'quantity' => 'required|integer',
            'category_id' => 'required|integer',
            'unit_id' => 'required|integer',
            'status' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $supply = Supply::create($validated);
        
        return response()->json($supply, 201);
    }

    // Show a specific supply
    public function show(Supply $supply)
    {
        return response()->json($supply);
    }

    // Update a supply
    public function update(Request $request, Supply $supply)
    {
        $validated = $request->validate([
            'stock_num' => [
                'required',
                'string',
                Rule::unique('tbl_supply', 'stock_num')->ignore($supply->stock_num, 'stock_num'),
            ],
            'item_desc' => 'required|string',
            'quantity' => 'required|integer',
            'category_id' => 'required|integer',
            'unit_id' => 'required|integer',
            'status' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $supply->update($validated);
        return response()->json($supply);
    }

    // Delete a supply
    public function destroy(Supply $supply)
    {
        $supply->delete();
        return response()->json(['message' => 'Supply deleted successfully']);
    }
}
