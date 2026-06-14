<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\FirmwareProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FirmwareProjectController extends Controller
{
    /**
     * Display a listing of firmware projects.
     */
    public function index()
    {
        $projects = FirmwareProject::withCount('files')->get();
        return view('cms.firmware-projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('cms.firmware-projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:firmware_projects,name',
            'device_type' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = FirmwareProject::create($validated);

        return redirect()->route('cms.firmware-projects.index')
            ->with('success', "Projek firmware '{$project->name}' berhasil dibuat.");
    }

    /**
     * Display the specified resource.
     */
    public function show(FirmwareProject $firmwareProject)
    {
        // Load the files ordered by version desc
        $firmwareProject->load(['files' => function($query) {
            $query->orderBy('version', 'desc');
        }]);

        return view('cms.firmware-projects.show', compact('firmwareProject'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FirmwareProject $firmwareProject)
    {
        return view('cms.firmware-projects.edit', compact('firmwareProject'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FirmwareProject $firmwareProject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:firmware_projects,name,' . $firmwareProject->id,
            'device_type' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $firmwareProject->update($validated);

        return redirect()->route('cms.firmware-projects.index')
            ->with('success', "Projek firmware '{$firmwareProject->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FirmwareProject $firmwareProject)
    {
        // Delete all physical files in storage
        foreach ($firmwareProject->files as $file) {
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
        }

        $name = $firmwareProject->name;
        $firmwareProject->delete(); // Cascading delete will remove files in DB

        return redirect()->route('cms.firmware-projects.index')
            ->with('success', "Projek firmware '{$name}' dan semua versinya berhasil dihapus.");
    }
}
