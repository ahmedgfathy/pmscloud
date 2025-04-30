<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FilePermission;

class DashboardController extends Controller
{
    public function index()
    {
        $files = File::where('user_id', auth()->id())
                    ->orderBy('created_at', 'desc')
                    ->paginate(12);
        return view('dashboard', compact('files'));
    }

    public function shared()
    {
        $files = File::whereHas('permissions', function($query) {
            $query->where('user_id', auth()->id());
        })->with(['permissions' => function($query) {
            $query->where('user_id', auth()->id());
        }])->orderBy('created_at', 'desc')->paginate(12);

        return view('dashboard', [
            'files' => $files,
            'isSharedView' => true
        ]);
    }
}
