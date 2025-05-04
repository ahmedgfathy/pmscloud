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

            $request->validate([
                'files.*' => 'required|file|max:' . (500 * 1024),
            ]);

            // Ensure storage directory exists
            Storage::disk('public')->makeDirectory('uploads', 0755, true);

            $files = is_array($request->file('files')) ? $request->file('files') : [$request->file('files')];
            $uploadedFiles = [];
            $userId = auth()->id();

            foreach ($files as $file) {
                try {
                    if (!$file->isValid()) {
                        continue;
                    }

                    $folderPath = $request->input('folder_path', '');
                    $basePath = "uploads/users/{$userId}/" . now()->format('Y/m/d');
                    
                    if ($folderPath) {
                        $basePath .= '/' . trim($folderPath, '/');
                    }

                    if (!Storage::disk('public')->exists($basePath)) {
                        Storage::disk('public')->makeDirectory($basePath, 0755, true);
                    }

                    $filename = $file->getClientOriginalName();
                    $storedPath = $file->storeAs($basePath, $filename, 'public');

                    if (!$storedPath) {
                        continue;
                    }

                    $fileModel = File::create([
                        'user_id' => $userId,
                        'name' => $filename,
                        'path' => $storedPath,
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'folder_path' => $basePath,
                    ]);

                    // Add preview info to response
                    $fileModel->preview = $fileModel->preview;
                    $uploadedFiles[] = $fileModel;

                } catch (\Exception $e) {
                    \Log::error("Error uploading file {$file->getClientOriginalName()}: " . $e->getMessage());
                    continue;
                }
            }

            if (empty($uploadedFiles)) {
                throw new \Exception('No files were uploaded successfully');
            }

            return response()->json([
                'success' => true,
                'files' => $uploadedFiles,
                'message' => 'Files uploaded successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'debug' => config('app.debug') ? $e->getTraceAsString() : null
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
