<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ontology;
use App\Services\OntologyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OntologyController extends Controller
{
    protected $ontologyService;

    public function __construct(OntologyService $ontologyService)
    {
        $this->ontologyService = $ontologyService;
    }

    /**
     * Get current active ontology
     */
    public function current()
    {
        $ontology = Ontology::active()->with('uploader')->latest()->first();

        if (!$ontology) {
            return response()->json([
                'message' => 'No active ontology found',
                'ontology' => null,
            ]);
        }

        return response()->json([
            'ontology' => $ontology,
        ]);
    }

    /**
     * Get all ontologies (Admin only)
     */
    public function index()
    {
        $ontologies = Ontology::with('uploader')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'ontologies' => $ontologies,
        ]);
    }

    /**
     * Upload new ontology (Admin only)
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:owl,rdf,xml|max:5120', // Max 5MB
            'version' => 'nullable|string',
            'description' => 'nullable|string',
            'set_active' => 'nullable|boolean',
        ]);

        try {
            $file = $request->file('file');
            
            // Validate ontology file
            $tempPath = $file->getRealPath();
            if (!$this->ontologyService->validateOntologyFile($tempPath)) {
                return response()->json([
                    'message' => 'Invalid ontology file format',
                ], 422);
            }

            // Save file
            $fileName = 'ontology_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $this->ontologyService->saveOntologyFile($file, $fileName);

            if (!$filePath) {
                return response()->json([
                    'message' => 'Error saving ontology file',
                ], 500);
            }

            // Deactivate other ontologies if set_active is true
            if ($request->boolean('set_active')) {
                Ontology::query()->update(['is_active' => false]);
            }

            // Create ontology record
            $ontology = Ontology::create([
                'filename' => $fileName,
                'file_path' => $filePath,
                'version' => $request->version ?? '1.0',
                'is_active' => $request->boolean('set_active', true),
                'description' => $request->description,
                'uploaded_by' => $request->user()->id,
            ]);

            return response()->json([
                'message' => 'Ontology uploaded successfully',
                'ontology' => $ontology->load('uploader'),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error uploading ontology: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error uploading ontology',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Set ontology as active (Admin only)
     */
    public function setActive($id)
    {
        try {
            $ontology = Ontology::findOrFail($id);

            // Deactivate all ontologies
            Ontology::query()->update(['is_active' => false]);

            // Activate selected ontology
            $ontology->is_active = true;
            $ontology->save();

            return response()->json([
                'message' => 'Ontology set as active',
                'ontology' => $ontology,
            ]);
        } catch (\Exception $e) {
            Log::error('Error setting active ontology: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error setting active ontology',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete ontology (Admin only)
     */
    public function destroy($id)
    {
        try {
            $ontology = Ontology::findOrFail($id);

            // Don't allow deleting active ontology
            if ($ontology->is_active) {
                return response()->json([
                    'message' => 'Cannot delete active ontology. Please set another ontology as active first.',
                ], 422);
            }

            // Delete file
            if (file_exists($ontology->file_path)) {
                unlink($ontology->file_path);
            }

            $ontology->delete();

            return response()->json([
                'message' => 'Ontology deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting ontology: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error deleting ontology',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
