<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\LiveChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LiveChannelController extends Controller
{
    public function index()
    {
        $channels = LiveChannel::with('category')->latest()->paginate(10);
        return view('admin.live-channels.index', compact('channels'));
    }

    public function create()
    {
        $categories = Category::where('status', 1)->get();
        return view('admin.live-channels.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'stream_url' => 'required|url|max:2048',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
            'description' => 'nullable|string',
            'is_live' => 'required|boolean',
            'status' => 'required|boolean',
        ]);

        $data = $request->only([
            'name',
            'category_id',
            'stream_url',
            'description',
            'is_live',
            'status',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('live_channels', 'public');
        }

        LiveChannel::create($data);

        return redirect()->route('admin.live-channels.index')
            ->with('success', 'Live channel created successfully');
    }

    public function edit(LiveChannel $live_channel)
    {
        $categories = Category::where('status', 1)->get();
        return view('admin.live-channels.edit', [
            'channel' => $live_channel,
            'categories' => $categories
        ]);
    }

    public function update(Request $request, LiveChannel $live_channel)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'stream_url' => 'required|url|max:2048',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
            'description' => 'nullable|string',
            'is_live' => 'required|boolean',
            'status' => 'required|boolean',
        ]);

        $data = $request->only([
            'name',
            'category_id',
            'stream_url',
            'description',
            'is_live',
            'status',
        ]);

        if ($request->hasFile('logo')) {
            if ($live_channel->logo) {
                Storage::disk('public')->delete($live_channel->logo);
            }
            $data['logo'] = $request->file('logo')->store('live_channels', 'public');
        }

        $live_channel->update($data);

        return redirect()->route('admin.live-channels.index')
            ->with('success', 'Live channel updated successfully');
    }

    public function destroy(LiveChannel $live_channel)
    {
        if ($live_channel->logo) {
            Storage::disk('public')->delete($live_channel->logo);
        }

        $live_channel->delete();

        return redirect()->route('admin.live-channels.index')
            ->with('success', 'Live channel deleted successfully');
    }
}
