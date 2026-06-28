<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdController extends Controller
{
    public function index()
    {
        $ads = Ad::latest()->paginate(10);
        return view('admin.ads.index', compact('ads'));
    }

    public function create()
    {
        return view('admin.ads.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'image_file' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB
            'ad_type'    => 'required|in:banner,popup',
            'position'   => 'required|string|max:100',
            'target_url' => 'nullable|url',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'status'     => 'sometimes|boolean',
        ]);

        $data = $validated;

        // Set default status if not provided
        if (!isset($data['status'])) {
            $data['status'] = true;
        }

        // Save image to storage/app/public/ads/images/...
        $path = $request->file('image_file')->store('ads/images', 'public');
        $data['image'] = $path;

        unset($data['image_file']);

        try {
            $ad = Ad::create($data);
            \Log::info('Ad created successfully', ['ad_id' => $ad->id]);
        } catch (\Exception $e) {
            \Log::error('Failed to create ad', ['error' => $e->getMessage(), 'data' => $data]);
            throw $e;
        }

        return redirect()
            ->route('admin.ads.index')
            ->with('success', 'Ad created successfully.');
    }

    public function edit(Ad $ad)
    {
        return view('admin.ads.edit', compact('ad'));
    }

    public function update(Request $request, Ad $ad)
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'image_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'ad_type'    => 'required|in:banner,popup',
            'position'   => 'required|string|max:100',
            'target_url' => 'nullable|url',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'status'     => 'sometimes|boolean',
        ]);

        $data = $validated;

        // If new image uploaded: delete old + replace
        if ($request->hasFile('image_file')) {
            if ($ad->image && Storage::disk('public')->exists($ad->image)) {
                Storage::disk('public')->delete($ad->image);
            }

            $path = $request->file('image_file')->store('ads/images', 'public');
            $data['image'] = $path;
        }

        unset($data['image_file']);

        $ad->update($data);

        return redirect()
            ->route('admin.ads.index')
            ->with('success', 'Ad updated successfully.');
    }

    public function destroy(Ad $ad)
    {
        if ($ad->image && Storage::disk('public')->exists($ad->image)) {
            Storage::disk('public')->delete($ad->image);
        }

        $ad->delete();

        return redirect()
            ->route('admin.ads.index')
            ->with('success', 'Ad deleted successfully.');
    }
}
