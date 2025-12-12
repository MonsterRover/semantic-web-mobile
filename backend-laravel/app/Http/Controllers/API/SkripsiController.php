<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Skripsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SkripsiController extends Controller
{
    /**
     * Get all skripsi (Admin only)
     */
    public function index(Request $request)
    {
        $query = Skripsi::with('creator');

        // Apply filters if provided
        if ($request->has('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        if ($request->has('topik')) {
            $query->where('topik', 'like', '%' . $request->topik . '%');
        }

        $skripsi = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($skripsi);
    }

    /**
     * Get single skripsi detail
     */
    public function show($id)
    {
        $skripsi = Skripsi::with('creator')->findOrFail($id);

        return response()->json($skripsi);
    }

    /**
     * Upload new skripsi (Kaprodi & Admin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'abstrak' => 'nullable|string',
            'kata_kunci' => 'nullable|string',
            'topik' => 'nullable|string',
            'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'penulis' => 'required|string|max:255',
            'pembimbing' => 'nullable|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240', // Max 10MB
        ]);

        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('skripsi', $fileName);

            $skripsi = Skripsi::create([
                'judul' => $request->judul,
                'abstrak' => $request->abstrak,
                'kata_kunci' => $request->kata_kunci,
                'topik' => $request->topik,
                'tahun' => $request->tahun,
                'penulis' => $request->penulis,
                'pembimbing' => $request->pembimbing,
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_type' => $file->getClientOriginalExtension(),
                'file_size' => $file->getSize(),
                'created_by' => $request->user()->id,
            ]);

            return response()->json([
                'message' => 'Skripsi uploaded successfully',
                'skripsi' => $skripsi->load('creator'),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error uploading skripsi: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error uploading skripsi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update skripsi (Owner, Admin)
     */
    public function update(Request $request, $id)
    {
        $skripsi = Skripsi::findOrFail($id);

        // Check permission
        if ($request->user()->role !== 'admin' && $skripsi->created_by !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $request->validate([
            'judul' => 'sometimes|required|string|max:255',
            'abstrak' => 'nullable|string',
            'kata_kunci' => 'nullable|string',
            'topik' => 'nullable|string',
            'tahun' => 'sometimes|required|integer|min:1900|max:' . (date('Y') + 1),
            'penulis' => 'sometimes|required|string|max:255',
            'pembimbing' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        try {
            // Update file if provided
            if ($request->hasFile('file')) {
                // Delete old file
                if ($skripsi->file_path) {
                    Storage::delete($skripsi->file_path);
                }

                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('skripsi', $fileName);

                $skripsi->file_path = $filePath;
                $skripsi->file_name = $fileName;
                $skripsi->file_type = $file->getClientOriginalExtension();
                $skripsi->file_size = $file->getSize();
            }

            // Update other fields
            $skripsi->fill($request->except('file'));
            $skripsi->save();

            return response()->json([
                'message' => 'Skripsi updated successfully',
                'skripsi' => $skripsi->load('creator'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating skripsi: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error updating skripsi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete skripsi (Owner, Admin)
     */
    public function destroy(Request $request, $id)
    {
        $skripsi = Skripsi::findOrFail($id);

        // Check permission
        if ($request->user()->role !== 'admin' && $skripsi->created_by !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        try {
            // Delete file
            if ($skripsi->file_path) {
                Storage::delete($skripsi->file_path);
            }

            $skripsi->delete();

            return response()->json([
                'message' => 'Skripsi deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting skripsi: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error deleting skripsi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get my uploaded skripsi (Kaprodi)
     */
    public function myUploads(Request $request)
    {
        $skripsi = Skripsi::where('created_by', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($skripsi);
    }

    /**
     * Download skripsi file
     */
    public function download($id)
    {
        $skripsi = Skripsi::findOrFail($id);

        if (!$skripsi->file_path || !Storage::exists($skripsi->file_path)) {
            return response()->json([
                'message' => 'File not found',
            ], 404);
        }

        return Storage::download($skripsi->file_path, $skripsi->file_name);
    }
}
