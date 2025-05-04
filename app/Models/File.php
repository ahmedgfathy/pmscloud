<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class File extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'path',
        'mime_type',
        'size',
        'is_public',
        'folder_path',
    ];

    protected $appends = ['full_path', 'relative_path', 'preview', 'icon'];

    public function getFullPathAttribute()
    {
        return asset('storage/' . $this->path);
    }

    public function getRelativePathAttribute()
    {
        return str_replace("uploads/users/{$this->user_id}/", '', $this->folder_path);
    }

    public function getPreviewAttribute()
    {
        try {
            if (str_starts_with($this->mime_type, 'image/')) {
                $thumbPath = $this->getThumbPath();
                
                if (!Storage::disk('public')->exists($thumbPath)) {
                    $this->generateThumbnail();
                }
                
                if (Storage::disk('public')->exists($thumbPath)) {
                    return asset('storage/' . $thumbPath);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Preview generation failed: ' . $e->getMessage());
        }
        
        return $this->getFileIcon();
    }

    public function getIconAttribute()
    {
        return $this->getFileIcon();
    }

    protected function getThumbPath()
    {
        $pathInfo = pathinfo($this->path);
        return $pathInfo['dirname'] . '/thumbs/' . $pathInfo['filename'] . '_thumb.' . ($pathInfo['extension'] ?? '');
    }

    protected function generateThumbnail()
    {
        try {
            $originalPath = Storage::disk('public')->path($this->path);
            $thumbPath = $this->getThumbPath();
            
            if (!file_exists($originalPath)) {
                throw new \Exception("Original file not found: {$originalPath}");
            }
            
            $thumbDir = dirname(Storage::disk('public')->path($thumbPath));
            if (!file_exists($thumbDir)) {
                mkdir($thumbDir, 0755, true);
            }
            
            $image = Image::make($originalPath);
            $image->fit(200, 200, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            
            $image->save(Storage::disk('public')->path($thumbPath));
            
        } catch (\Exception $e) {
            \Log::error('Thumbnail generation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function getFileIcon()
    {
        $extension = strtolower(pathinfo($this->name, PATHINFO_EXTENSION));
        
        // Direct extension-to-icon mapping
        return asset("images/icons/{$extension}.svg");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(FilePermission::class);
    }
}
