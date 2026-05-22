<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class VideoController extends Controller
{
    /**
     * List all videos grouped by platform.
     */
    public function index(): View
    {
        $youtubeVideos = Video::youtube()->ordered()->get();
        $tiktokVideos  = Video::tiktok()->ordered()->get();

        return view('cms.videos.index', compact('youtubeVideos', 'tiktokVideos'));
    }

    /**
     * Show the create form.
     */
    public function create(): View
    {
        return view('cms.videos.form', ['video' => null]);
    }

    /**
     * Store a new video.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'platform'    => ['required', 'in:youtube,tiktok'],
            'video_id'    => ['required', 'string', 'max:500'],
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:500'],
            'duration'    => ['nullable', 'string', 'max:20'],
            'views'       => ['nullable', 'string', 'max:20'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['boolean'],
        ]);

        $validated['created_by'] = Auth::id();
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active']  = $request->boolean('is_active', true);

        // Auto-fetch TikTok thumbnail via oEmbed
        if ($validated['platform'] === 'tiktok') {
            $validated['thumbnail_url'] = $this->fetchTikTokThumbnail($validated['video_id']);
        }

        Video::create($validated);

        return redirect()->route('cms.videos.index')
            ->with('success', 'Video berhasil ditambahkan.');
    }

    /**
     * Show the edit form.
     */
    public function edit(Video $video): View
    {
        return view('cms.videos.form', compact('video'));
    }

    /**
     * Update video data.
     */
    public function update(Request $request, Video $video): RedirectResponse
    {
        $validated = $request->validate([
            'platform'    => ['required', 'in:youtube,tiktok'],
            'video_id'    => ['required', 'string', 'max:500'],
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:500'],
            'duration'    => ['nullable', 'string', 'max:20'],
            'views'       => ['nullable', 'string', 'max:20'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['boolean'],
        ]);

        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Re-fetch thumbnail if video_id changed or thumbnail not yet stored
        if ($validated['platform'] === 'tiktok') {
            if (empty($video->thumbnail_url) || $video->video_id !== $validated['video_id']) {
                $fetched = $this->fetchTikTokThumbnail($validated['video_id']);
                if ($fetched) {
                    $validated['thumbnail_url'] = $fetched;
                }
            }
        }

        $video->update($validated);

        return redirect()->route('cms.videos.index')
            ->with('success', 'Video berhasil diperbarui.');
    }

    /**
     * Delete a video.
     */
    public function destroy(Video $video): RedirectResponse
    {
        $video->delete();

        return redirect()->route('cms.videos.index')
            ->with('success', 'Video berhasil dihapus.');
    }

    /**
     * Toggle active status via quick action.
     */
    public function toggleActive(Video $video): RedirectResponse
    {
        $video->update(['is_active' => !$video->is_active]);

        $status = $video->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Video berhasil {$status}.");
    }

    /**
     * Re-fetch thumbnail from TikTok oEmbed and save it.
     */
    public function refreshThumbnail(Video $video): RedirectResponse
    {
        if ($video->platform !== 'tiktok') {
            return back()->with('error', 'Hanya video TikTok yang memerlukan refresh thumbnail.');
        }

        $url = $this->fetchTikTokThumbnail($video->video_id);

        if ($url) {
            $video->update(['thumbnail_url' => $url]);
            return back()->with('success', 'Thumbnail TikTok berhasil diperbarui.');
        }

        return back()->with('error', 'Gagal mengambil thumbnail. Pastikan URL TikTok valid dan dapat diakses.');
    }

    /**
     * Move sort order up.
     */
    public function moveUp(Video $video): RedirectResponse
    {
        $prev = Video::where('platform', $video->platform)
            ->where('sort_order', '<', $video->sort_order)
            ->orderByDesc('sort_order')
            ->first();

        if ($prev) {
            [$video->sort_order, $prev->sort_order] = [$prev->sort_order, $video->sort_order];
            $video->save();
            $prev->save();
        }

        return back()->with('success', 'Urutan diperbarui.');
    }

    /**
     * Move sort order down.
     */
    public function moveDown(Video $video): RedirectResponse
    {
        $next = Video::where('platform', $video->platform)
            ->where('sort_order', '>', $video->sort_order)
            ->orderBy('sort_order')
            ->first();

        if ($next) {
            [$video->sort_order, $next->sort_order] = [$next->sort_order, $video->sort_order];
            $video->save();
            $next->save();
        }

        return back()->with('success', 'Urutan diperbarui.');
    }

    /**
     * Fetch thumbnail URL from TikTok oEmbed API.
     * Public API — no authentication required.
     * @see https://developers.tiktok.com/doc/embed-videos
     */
    private function fetchTikTokThumbnail(string $videoUrl): string
    {
        if (!str_starts_with($videoUrl, 'http')) {
            return '';
        }

        try {
            $response = Http::timeout(6)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; DevGateBot/1.0)'])
                ->get('https://www.tiktok.com/oembed', ['url' => $videoUrl]);

            if ($response->successful()) {
                return $response->json('thumbnail_url', '');
            }

            Log::warning("TikTok oEmbed failed [{$response->status()}] for: {$videoUrl}");
        } catch (\Exception $e) {
            Log::warning("TikTok oEmbed exception: {$e->getMessage()}");
        }

        return '';
    }
}
