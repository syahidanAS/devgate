<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FirmwareFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'firmware_project_id',
        'version',
        'file_path',
        'flash_offset',
        'changelog',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['file_url'];

    /**
     * Get the project that owns the firmware file.
     */
    public function project()
    {
        return $this->belongsTo(FirmwareProject::class, 'firmware_project_id');
    }

    /**
     * Get the download URL of the binary file.
     */
    public function getFileUrlAttribute()
    {
        return Storage::disk('public')->url($this->file_path);
    }
}
