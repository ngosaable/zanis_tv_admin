<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LiveChannel;

class LiveChannelApiController extends Controller
{
    /**
     * GET /api/live-channels
     */
    public function index()
    {
        $channels = LiveChannel::query()
            ->select('id', 'name', 'category_id', 'stream_url', 'logo', 'is_live', 'status')
            ->where('status', 1)
            ->orderBy('name')
            ->get()
            ->map(function ($c) {
                return [
                    "id" => $c->id,
                    "name" => $c->name,
                    "logo" => $c->logo,
                    "stream_type" => "url",
                    "stream_url" => $c->stream_url,
                    "stream_path" => null,
                    "status" => $c->status ? 1 : 0,
                ];
            });

        return response()->json($channels);
    }
}
