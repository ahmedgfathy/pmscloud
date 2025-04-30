<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        try {
            if (!$request->hasFile('files')) {
                throw new \Exception('No file was uploaded');
            }

            $file = $request->file('files');
            
            if (!$file->isValid()) {
                throw new \Exception('Invalid file upload');
            }

            $userId = auth()->id();
            $basePath = "uploads/users/{$userId}/" . now()->format('Y/m/d');
            
            // Create directory if it doesn't exist
            if (!Storage::disk('public')->exists($basePath)) {
                Storage::disk('public')->makeDirectory($basePath);
            }
            
            // Store file with original name
            $filename = $file->getClientOriginalName();
            $storedPath = $file->storeAs($basePath, $filename, 'public');
            
            if (!$storedPath) {
                throw new \Exception('Failed to store file');
            }

            $fileModel = File::create([
                'user_id' => $userId,
                'name' => $filename,
                'path' => $storedPath,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'folder_path' => $basePath,
            ]);

            return response()->json([
                'success' => true,
                'file' => $fileModel,
                'message' => 'File uploaded successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    private function convertToBytes($from)
    {
        $units = ['B', 'K', 'M', 'G', 'T', 'P'];
        $number = substr($from, 0, -1);
        $suffix = strtoupper(substr($from, -1));

        if (is_numeric(substr($from, -1))) {
            return $from;
        }

        $exponent = array_flip($units)[$suffix] ?? null;
        if ($exponent === null) {
            return null;
        }

        return $number * (1024 ** $exponent);
    }

    public function searchUsers(Request $request)
    {
        $search = $request->input('search', '');
        
        $users = User::where('id', '!=', auth()->id())
                    ->where(function($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%")
                              ->orWhere('email', 'LIKE', "%{$search}%");
                    })
                    ->take(10)
                    ->get(['id', 'name', 'email']);

        return response()->json($users);
    }

    public function share(Request $request, File $file)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permissions' => 'required|array',
            'permissions.can_read' => 'boolean',
            'permissions.can_write' => 'boolean',
            'permissions.can_delete' => 'boolean',
        ]);

        // Check if user owns the file
        if ($file->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Update or create permissions
        $file->permissions()->updateOrCreate(
            ['user_id' => $request->user_id],
            $request->permissions
        );

        return response()->json(['message' => 'File shared successfully']);
    }

    public function getPermissions(File $file)
    {
        $permissions = $file->permissions()
            ->with('user:id,name,email')
            ->get();

        return response()->json($permissions);
    }
}
