<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class BackupController extends Controller
{
    public function backup()
    {
        $sourcePath = database_path('database.sqlite');

        if (!File::exists($sourcePath)) {
            return back()->with('error', 'Database file not found.');
        }

        // nama file
        $fileName = 'backup-' . now()->format('Y-m-d-His') . '.sqlite';

        // Return the file as a download response
        return Response::download($sourcePath, $fileName, [
            'Content-Type' => 'application/x-sqlite3',
        ]);
    }

    public function restore(Request $request)
    {
        // dd($request->all());
        if (!$request->hasFile('backup_file')) {
            return back()->with('error', 'No file uploaded.');
        }

        $file = $request->file('backup_file');

        // Safety check: Is it actually an sqlite file?
        if ($file->getClientOriginalExtension() !== 'sqlite') {
            return back()->with('error', 'Invalid file type.');
        }

        $path = database_path('database.sqlite');

        // Move the uploaded file to overwrite the current DB
        File::put($path, file_get_contents($file->getRealPath()));

        return back()->with('success', 'Database restored successfully!');
    }
}
