<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use App\Jobs\ProcessCsvImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    public function index()
    {
        $uploads = Upload::withCount(['records as successful_count' => function ($query) {
            $query->where('status', 'successful');
        }])->latest()->get();

        return view('dashboard', compact('uploads'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB file validation
        ]);

        $file = $request->file('csv_file');
        $path = $file->store('csv_uploads');

        $upload = Upload::create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'status' => 'pending',
        ]);

        // Dispatch background job for async handling
        ProcessCsvImport::dispatch($upload);

        return back()->with('success', 'File uploaded successfully! Processing started in the background.');
    }
}
