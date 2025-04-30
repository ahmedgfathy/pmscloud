<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FilePermission extends Model
{
    protected $fillable = [
        'file_id',
        'user_id',
        'can_read',
        'can_write',
        'can_delete',
    ];

    protected $casts = [
        'can_read' => 'boolean',
        'can_write' => 'boolean',
        'can_delete' => 'boolean',
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
