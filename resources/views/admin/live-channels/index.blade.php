@extends('layouts.admin')

@section('page_title', 'Live TV Channels')
@section('page_subtitle', 'Manage live streaming channels')

@section('page_actions')
    <a href="{{ route('admin.live-channels.create') }}" class="dstv-btn" style="background: var(--live-color); color: white; box-shadow: 0 4px 14px rgba(249, 115, 22, 0.4);">
        <i class="fas fa-plus"></i> Add Channel
    </a>
@endsection

@section('content')

<div class="dstv-card" style="border-top: 3px solid var(--live-color);">
    <div class="dstv-card-header" style="background: var(--live-light);">
        <h3 class="dstv-card-title" style="color: var(--live-color);"><i class="fas fa-satellite-dish"></i> All Channels</h3>
        <span style="color: var(--live-color); font-size: 14px; font-weight: 600;">{{ $channels->count() }} total</span>
    </div>
    <div class="dstv-card-body">
        @forelse($channels as $channel)
            <div style="display: flex; align-items: center; padding: 16px 24px; border-bottom: 1px solid var(--border-light); gap: 16px;">
                <div style="width: 50px; height: 50px; background: var(--dstv-gold); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-tv" style="color: var(--dstv-blue-dark); font-size: 20px;"></i>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div style="font-weight: 600; color: var(--text-primary); font-size: 15px;">{{ $channel->name }}</div>
                    <div style="font-size: 12px; color: var(--text-secondary); margin-top: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        {{ $channel->stream_url }}
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <!-- Toggle Status -->
                    <form action="{{ route('admin.live-channels.update', $channel) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $channel->status ? 0 : 1 }}">
                        <button type="submit" class="dstv-action-btn" title="{{ $channel->status ? 'Go Offline' : 'Go Live' }}">
                            <i class="fas fa-{{ $channel->status ? 'broadcast-tower' : 'eye-slash' }}"></i>
                        </button>
                    </form>
                    <!-- Edit -->
                    <a href="{{ route('admin.live-channels.edit', $channel) }}" class="dstv-action-btn" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <!-- Delete -->
                    <form action="{{ route('admin.live-channels.destroy', $channel) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this channel?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dstv-action-btn delete" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="dstv-empty">
                <i class="fas fa-tv"></i>
                <h3>No live channels yet!</h3>
                <p>Start by adding your first channel</p>
                <a href="{{ route('admin.live-channels.create') }}" class="dstv-btn dstv-btn-gold" style="margin-top: 12px;">
                    <i class="fas fa-plus"></i> Add Channel
                </a>
            </div>
        @endforelse
    </div>
</div>

@endsection