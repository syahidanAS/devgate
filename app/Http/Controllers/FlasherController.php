<?php

namespace App\Http\Controllers;

use App\Models\FirmwareProject;
use Illuminate\Http\Request;

class FlasherController extends Controller
{
    /**
     * Display the public Web Flasher tool page.
     */
    public function index()
    {
        // Get all projects that have at least one active firmware file
        $projects = FirmwareProject::with(['files' => function ($query) {
            $query->where('is_active', true)->orderBy('version', 'desc');
        }])->whereHas('files', function ($query) {
            $query->where('is_active', true);
        })->get();

        return view('flasher.index', compact('projects'));
    }
}
