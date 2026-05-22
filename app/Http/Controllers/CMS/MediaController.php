<?php

namespace App\Http\Controllers\CMS;

// AJAX Image uploads for Rich Text Editor (TipTap)
use App\Http\Controllers\Controller;
use App\Services\Media\ImageProcessingService;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    protected ImageProcessingService $imageService;

    public function __construct(ImageProcessingService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Upload an image from TipTap inline attachments, convert to modern webp, and return JSON URL.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120', // max 5MB
        ]);

        try {
            $url = $this->imageService->uploadAndProcess(
                $request->file('image'),
                'editor-media', // Storage directory name
                800,           // Standard body width max 800px
                80
            );

            return response()->json([
                'success' => true,
                'url'     => $url,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah gambar: ' . $e->getMessage(),
            ], 500);
        }
    }
}
