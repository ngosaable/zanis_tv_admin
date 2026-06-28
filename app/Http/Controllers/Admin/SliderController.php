<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::orderBy('sort_order')->latest()->paginate(10);
        return view('admin.sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.sliders.create');
    }

public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'linked_type' => 'required|in:movie,live_channel,series,none',
            'linked_id' => 'nullable|integer',
            'order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        $data = $request->only(['title','linked_type','linked_id','order','status']);
        $data['sort_order'] = $data['order'];
        unset($data['order']);

        $data['image'] = $request->file('image')->store('sliders', 'public');

        Slider::create($data);

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Slider created successfully');
    }

    public function edit(Slider $slider)
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, Slider $slider)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'linked_type' => 'required|in:movie,live_channel,series,none',
            'linked_id' => 'nullable|integer',
            'order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        $data = $request->only(['title','linked_type','linked_id','order','status']);
        $data['sort_order'] = $data['order'];
        unset($data['order']);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($slider->image);
            $data['image'] = $request->file('image')->store('sliders', 'public');
        }

        $slider->update($data);

        return redirect()->route('admin.sliders.index')->with('success', 'Slider updated successfully');
    }

    public function destroy(Slider $slider)
    {
        Storage::disk('public')->delete($slider->image);
        $slider->delete();

        return redirect()->route('admin.sliders.index')->with('success', 'Slider deleted successfully');
    }
}
