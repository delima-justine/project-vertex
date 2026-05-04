<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\SupplyRequest;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    public function index()
    {
        $archives = Archive::with('archiver')
            ->where('table_name', 'tbl_request')
            ->orderByDesc('archived_at')
            ->get();

        return response()->json($archives);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_id' => 'required|integer|exists:tbl_request,id',
        ]);

        $requestId = $validated['request_id'];

        $supplyRequest = SupplyRequest::with(['user.office', 'supply', 'approver'])
            ->find($requestId);

        if (! $supplyRequest) {
            return response()->json(['message' => 'Supply request not found.'], 404);
        }

        if ($supplyRequest->status !== 'released') {
            return response()->json(['message' => 'Only released requests may be archived.'], 422);
        }

        $alreadyArchived = Archive::where('table_name', 'tbl_request')
            ->where('original_id', $requestId)
            ->exists();

        if ($alreadyArchived) {
            return response()->json(['message' => 'This request is already archived.'], 409);
        }

        $archive = Archive::create([
            'table_name' => 'tbl_request',
            'original_id' => $requestId,
            'data' => $supplyRequest->toArray(),
            'archived_by' => $request->user()->id,
            'archived_at' => now(),
        ]);

        return response()->json($archive->load('archiver'), 201);
    }

    public function restore(Archive $archive)
    {
        $supplyRequest = SupplyRequest::find($archive->original_id);

        if (! $supplyRequest) {
            return response()->json(['message' => 'Original request not found.'], 404);
        }

        $supplyRequest->status = 'released';
        $supplyRequest->save();

        $archive->delete();

        return response()->json(['message' => 'Archive restored successfully.']);
    }
}
