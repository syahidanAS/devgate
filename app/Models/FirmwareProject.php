<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FirmwareProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'device_type',
    ];

    protected static function booted()
    {
        static::creating(function ($project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->name);
            }
        });
        
        static::updating(function ($project) {
            if ($project->isDirty('name') && !$project->isDirty('slug')) {
                $project->slug = Str::slug($project->name);
            }
        });
    }

    /**
     * Get the firmware files/versions for the project.
     */
    public function files()
    {
        return $this->hasMany(FirmwareFile::class)->orderBy('version', 'desc');
    }
}
