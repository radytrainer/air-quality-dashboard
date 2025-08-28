<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class NewsController extends Controller
{
    // GET /api/news
    public function index()
    {
        return News::with('category')->latest()->get();
    }

    // POST /api/news
    public function store(Request $request)
    {
        $data = $request->validate([
            'caption' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'media.*' => ['file', 'mimes:jpg,jpeg,png,gif,mp4,webm', 'max:20480'],
            'video_link' => ['nullable', 'string'], // ✅ add video link
        ]);

        $paths = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $paths[] = $file->store('news', 'public');
            }
        }

        $news = News::create([
            'caption' => $data['caption'],
            'category_id' => $data['category_id'],
            'media' => count($paths) ? $paths : null,
            'video_link' => $data['video_link'] ?? null, // ✅ store video link
        ]);

        return response()->json($news->load('category'), 201);
    }

    // PUT /api/news/{news}
    public function update(Request $request, News $news)
    {
        $input = $request->all();
        $validated = Validator::make($input, [
            'caption' => ['required', 'string', 'max:255'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'keep' => ['sometimes', 'array'],
            'keep.*' => ['string'],
            'media.*' => ['file', 'mimes:jpg,jpeg,png,gif,mp4,webm', 'max:20480'],
            'video_link' => ['nullable', 'string'], // ✅ allow video link
        ])->validate();

        $existing = $news->media ?? [];
        $keep = $validated['keep'] ?? $existing;

        // Delete removed files
        $removed = array_diff($existing, $keep);
        foreach ($removed as $path) {
            Storage::disk('public')->delete($path);
        }

        $final = array_values($keep);

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $final[] = $file->store('news', 'public');
            }
        }

        $news->caption = $validated['caption'];
        if (isset($validated['category_id'])) {
            $news->category_id = $validated['category_id'];
        }
        $news->media = $final;
        $news->video_link = $validated['video_link'] ?? $news->video_link; // ✅ save video link
        $news->save();

        return response()->json($news->load('category'));
    }

    // DELETE /api/news/{news}
    public function destroy(News $news)
    {
        if ($news->media) {
            foreach ($news->media as $path) {
                Storage::disk('public')->delete($path);
            }
        }
        $news->delete();
        return response()->json(['message' => 'News deleted successfully']);
    }
}
