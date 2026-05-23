<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

class ImageProcessingService
{
    /**
     * Upload, compress, convert to webp, and store an image.
     * Useful for standalone uploads (e.g., within rich text editor content).
     *
     * @param UploadedFile $file
     * @param string $folder Directory under storage/app/public/
     * @param int|null $width Target width for resizing (aspect ratio maintained)
     * @param int $quality Compression quality (1-100)
     * @return string Public URL of the stored WebP image
     */
    public function uploadAndProcess(UploadedFile $file, string $folder = 'uploads', ?int $width = 1200, int $quality = 80): string
    {
        // 1. Generate clean and unique filename
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $cleanName = Str::slug($originalName) . '-' . time() . '.webp';
        
        // 2. Read file with Intervention Image (using v3 facade syntax if available)
        $img = Image::read($file);

        // 3. Optional resize preserving aspect ratio
        if ($width && $img->width() > $width) {
            $img->resize(width: $width);
        }

        // 4. Encode as WebP with target quality
        $webpEncoded = $img->toWebp($quality);

        // 5. Store on s3 disk
        $path = $folder . '/' . $cleanName;
        Storage::disk('s3')->put($path, (string) $webpEncoded);

        // 6. Return s3 asset URL
        return Storage::disk('s3')->url($path);
    }

    /**
     * Delete an image from storage using its public URL.
     */
    public function deleteByUrl(string $url): bool
    {
        $storagePrefix = Storage::disk('s3')->url('');
        if (str_starts_with($url, $storagePrefix)) {
            $path = str_replace($storagePrefix, '', $url);
            return Storage::disk('s3')->delete($path);
        }
        return false;
    }
}
