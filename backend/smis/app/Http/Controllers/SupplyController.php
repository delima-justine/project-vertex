<?php

namespace App\Http\Controllers;

use App\Models\Supply;
use Illuminate\Http\Request;

class SupplyController extends Controller
{
    // List all supplies
    public function index() 
    {
        return response()->json(Supply::all());
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
}
