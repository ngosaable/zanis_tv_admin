<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryApiController extends Controller
{
    /**
     * GET /api/categories
     */
    public function index()
    {
        $categories = Category::query()
            ->select('id', 'name', 'type', 'status')
            ->where('status', 1)
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'type' => $category->type,
                    'status' => $category->status ? 1 : 0,
                ];
            });

        return response()->json($categories);
    }
}
