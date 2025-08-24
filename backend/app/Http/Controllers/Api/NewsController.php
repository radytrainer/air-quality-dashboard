<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class NewsController extends Controller
{
    // GET /api/news (public)
    public function index()
    {
        return News::latest()->get();
    }

    // POST /api/news (admin)
    public function store(Request $request)
    {
        $data = $request->validate([
            'caption' => ['required', 'string', 'max:255'],
            'media.*' => ['file', 'mimes:jpg,jpeg,png,gif,mp4,webm', 'max:20480'], // 20MB each
        ]);

        $paths = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $paths[] = $file->store('news', 'public');
            }
        }

        $news = News::create([
            'caption' => $data['caption'],
            'media'   => $paths,
        ]);

        return response()->json($news, 201);
    }

    // PATCH /api/news/{id} (admin)
    // Send: caption? (string), keep[] (array of file paths to KEEP), media[] (new files to add)
    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $validated = $request->validate([
            'caption' => ['sometimes', 'string', 'max:255'],
            'keep'    => ['sometimes', 'array'],
            'keep.*'  => ['string'],
            'media.*' => ['file', 'mimes:jpg,jpeg,png,gif,mp4,webm', 'max:20480'],
        ]);

        $existing = $news->media ?? [];
        $keep = $validated['keep'] ?? [];

        // Compute removed files
        $removed = array_values(array_diff($existing, $keep));
        foreach ($removed as $path) {
            Storage::disk('public')->delete($path);
        }

        // Add new files
        $final = array_values($keep);
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $final[] = $file->store('news', 'public');
            }
        }

        if (isset($validated['caption'])) {
            $news->caption = $validated['caption'];
        }
        $news->media = $final;
        $news->save();

        return response()->json($news);
    }
}
