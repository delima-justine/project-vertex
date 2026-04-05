<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    // List all archived records
    public function index() 
    {
        return response()->json(Archive::with('archiver')->get());
    }

    // View a specific archived record
    public function show(Archive $archive) 
    {
        return response()->json($archive->load('archiver'));
    }

    // Permanently delete an archived record
    public function destroy(Archive $archive)
    {
        $archive->delete();
        return response()->json(['message' => 'Archived record deleted successfully']);
    }
}
