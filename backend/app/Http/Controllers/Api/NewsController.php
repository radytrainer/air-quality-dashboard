<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    // GET /api/news (public)
    public function index()
    {
        // Load category relationship for frontend
        return News::with('category')->latest()->get();

    }

    // POST /api/news (admin)
    public function store(Request $request)
    {
        $data = $request->validate([
            'caption'     => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'media.*'     => ['file', 'mimes:jpg,jpeg,png,gif,mp4,webm', 'max:20480'], // 20MB
        ]);

        $paths = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $paths[] = $file->store('news', 'public');
            }
        }

        $news = News::create([
            'caption'     => $data['caption'],
            'category_id' => $data['category_id'], // Save category
            'media'       => $paths,
        ]);

        // Return news with category loaded
        return response()->json($news->load('category'), 201);
    }

    // PATCH /api/news/{id} (admin)
    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $validated = $request->validate([
            'caption'     => ['sometimes', 'string', 'max:255'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'keep'        => ['sometimes', 'array'],
            'keep.*'      => ['string'],
            'media.*'     => ['file', 'mimes:jpg,jpeg,png,gif,mp4,webm', 'max:20480'],
        ]);

        // Handle media files
        $existing = $news->media ?? [];
        $keep = $validated['keep'] ?? [];

        // Remove files not kept
        $removed = array_values(array_diff($existing, $keep));
        foreach ($removed as $path) {
            Storage::disk('public')->delete($path);
        }

        $final = array_values($keep);
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $final[] = $file->store('news', 'public');
            }
        }

        // Update fields
        if (isset($validated['caption'])) $news->caption = $validated['caption'];
        if (isset($validated['category_id'])) $news->category_id = $validated['category_id'];

        $news->media = $final;
        $news->save();

        return response()->json($news->load('category'));
    }

    // DELETE /api/news/{id} (admin)
    public function destroy($id)
    {
        $news = News::findOrFail($id);

        // Delete all media files from storage
        if ($news->media) {
            foreach ($news->media as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $news->delete();

        return response()->json(['message' => 'News deleted successfully']);
    }
}
