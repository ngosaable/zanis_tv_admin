<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slider;

class SliderApiController extends Controller
{
    /**
     * GET /api/sliders
     */
    public function index()
    {
        $sliders = Slider::query()
            ->select('id', 'title', 'image', 'linked_type', 'linked_id', 'sort_order', 'status')
            ->where('status', 1)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($slider) {
                $imagePath = $slider->image ? ltrim($slider->image, '/') : null;
                $imagePath = $imagePath ? ltrim($imagePath, 'storage/') : null;
                
                return [
                    'id' => $slider->id,
                    'title' => $slider->title,
                    'image' => $slider->image,
                    'linked_type' => $slider->linked_type ?: 'none',
                    'linked_id' => $slider->linked_id,
                    'sort_order' => $slider->sort_order,
                    'status' => $slider->status ? 1 : 0,
                ];
            });

        return response()->json($sliders);
    }
}
