<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected $appends = ['full_path', 'relative_path'];

    public function getFullPathAttribute()
    {
        return Storage::url($this->path);
    }

    public function getRelativePathAttribute()
    {
        return str_replace("uploads/users/{$this->user_id}/", '', $this->folder_path);
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
