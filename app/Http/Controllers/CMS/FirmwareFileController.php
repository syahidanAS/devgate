<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\FirmwareProject;
use App\Models\FirmwareFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FirmwareFileController extends Controller
{
    /**
     * Store a newly uploaded firmware file.
     */
    public function store(Request $request)
    {
        $request->validate([
            'firmware_project_id' => 'required|exists:firmware_projects,id',
            'version' => 'required|string|max:50',
            'file' => 'required|file|max:16384', // Max 16MB
            'flash_offset' => 'required|string|max:50',
            'changelog' => 'nullable|string',
        ]);

        $project = FirmwareProject::findOrFail($request->firmware_project_id);

        // Check file extension
        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        if (!in_array(strtolower($extension), ['bin', 'hex'])) {
            return back()->withErrors(['file' => 'Format file harus berupa .bin atau .hex'])->withInput();
        }

        // Check if version already exists for this project
        $exists = FirmwareFile::where('firmware_project_id', $project->id)
            ->where('version', $request->version)
            ->exists();
        if ($exists) {
            return back()->withErrors(['version' => 'Versi ini sudah ada untuk projek ini.'])->withInput();
        }

        // Store file in public storage disk
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
        $filePath = $file->storeAs('firmwares/' . $project->slug, $filename, 'public');

        // Create database record
        FirmwareFile::create([
            'firmware_project_id' => $project->id,
            'version' => $request->version,
            'file_path' => $filePath,
            'flash_offset' => $request->flash_offset,
            'changelog' => $request->changelog,
            'is_active' => $request->has('is_active') ? $request->is_active : true,
        ]);

        return redirect()->route('cms.firmware-projects.show', $project->id)
            ->with('success', "Firmware versi {$request->version} berhasil diunggah.");
    }

    /**
     * Show the form for editing the specified firmware file.
     */
    public function edit(FirmwareFile $firmwareFile)
    {
        $project = $firmwareFile->project;
        return view('cms.firmware-files.edit', compact('firmwareFile', 'project'));
    }

    /**
     * Update the specified firmware file in storage.
     */
    public function update(Request $request, FirmwareFile $firmwareFile)
    {
        $request->validate([
            'version' => 'required|string|max:50',
            'file' => 'nullable|file|max:16384', // Max 16MB (optional replacement)
            'flash_offset' => 'required|string|max:50',
            'changelog' => 'nullable|string',
        ]);

        $project = $firmwareFile->project;

        // Check if version already exists for this project (excluding current record)
        $exists = FirmwareFile::where('firmware_project_id', $project->id)
            ->where('version', $request->version)
            ->where('id', '!=', $firmwareFile->id)
            ->exists();
        if ($exists) {
            return back()->withErrors(['version' => 'Versi ini sudah ada untuk projek ini.'])->withInput();
        }

        // Handle file replacement if a new file is uploaded
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            if (!in_array(strtolower($extension), ['bin', 'hex'])) {
                return back()->withErrors(['file' => 'Format file harus berupa .bin atau .hex'])->withInput();
            }

            // Delete old file
            if (Storage::disk('public')->exists($firmwareFile->file_path)) {
                Storage::disk('public')->delete($firmwareFile->file_path);
            }

            // Store new file
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $filePath = $file->storeAs('firmwares/' . $project->slug, $filename, 'public');
            
            $firmwareFile->file_path = $filePath;
        }

        // Update database record fields
        $firmwareFile->version = $request->version;
        $firmwareFile->flash_offset = $request->flash_offset;
        $firmwareFile->changelog = $request->changelog;
        if ($request->has('is_active')) {
            $firmwareFile->is_active = $request->is_active;
        }
        $firmwareFile->save();

        return redirect()->route('cms.firmware-projects.show', $project->id)
            ->with('success', "Firmware versi {$request->version} berhasil diperbarui.");
    }

    /**
     * Toggle the active status of a firmware file.
     */
    public function toggleActive(FirmwareFile $firmwareFile)
    {
        $firmwareFile->is_active = !$firmwareFile->is_active;
        $firmwareFile->save();

        $status = $firmwareFile->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Firmware versi {$firmwareFile->version} berhasil {$status}.");
    }

    /**
     * Remove the specified firmware file from storage.
     */
    public function destroy(FirmwareFile $firmwareFile)
    {
        $projectId = $firmwareFile->firmware_project_id;
        $version = $firmwareFile->version;

        // Delete physical file
        if (Storage::disk('public')->exists($firmwareFile->file_path)) {
            Storage::disk('public')->delete($firmwareFile->file_path);
        }

        // Delete DB record
        $firmwareFile->delete();

        return redirect()->route('cms.firmware-projects.show', $projectId)
            ->with('success', "Firmware versi {$version} berhasil dihapus.");
    }
}
