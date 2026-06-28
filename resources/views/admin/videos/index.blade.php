@extends('layouts.admin')

@section('page_title', 'Videos')
@section('page_subtitle', 'Manage your video content')

@section('page_actions')
    <a href="{{ route('admin.videos.create') }}" class="dstv-btn" style="background: var(--video-color); color: white; box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4);">
        <i class="fas fa-plus"></i> Add Video
    </a>
@endsection

@section('content')

<div class="dstv-card" style="border-top: 3px solid var(--video-color);">
    <div class="dstv-card-header" style="background: var(--video-light);">
        <h3 class="dstv-card-title" style="color: var(--video-color);"><i class="fas fa-film"></i> All Videos</h3>
        <span style="color: var(--video-color); font-size: 14px; font-weight: 600;">{{ $videos->total() }} total</span>
    </div>
    <div class="dstv-card-body">
        @forelse($videos as $video)
            <div style="display: flex; align-items: center; padding: 16px 24px; border-bottom: 1px solid var(--border-light); gap: 16px;">
                <div style="width: 80px; height: 50px; background: var(--bg-primary); border-radius: 10px; overflow: hidden; flex-shrink: 0;">
                    @if($video->poster)
                        <img src="{{ $video->poster_full_url }}" alt="{{ $video->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-film" style="color: var(--text-light);"></i>
                        </div>
                    @endif
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div style="font-weight: 600; color: var(--text-primary); font-size: 15px;">{{ $video->title }}</div>
                    <div style="font-size: 12px; color: var(--text-secondary); margin-top: 4px;">
                        {{ $video->category->name ?? 'Uncategorized' }} • {{ $video->formatted_duration ?? '00:00' }}
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <!-- Toggle Active -->
                    <form action="{{ route('admin.videos.update', $video) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $video->status ? 0 : 1 }}">
                        <button type="submit" class="dstv-action-btn" title="{{ $video->status ? 'Deactivate' : 'Activate' }}">
                            <i class="fas fa-{{ $video->status ? 'eye' : 'eye-slash' }}"></i>
                        </button>
                    </form>
                    <!-- Edit -->
                    <a href="{{ route('admin.videos.edit', $video) }}" class="dstv-action-btn" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <!-- Delete -->
                    <form action="{{ route('admin.videos.destroy', $video) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this video?');">
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
                <i class="fas fa-film"></i>
                <h3>No videos yet!</h3>
                <p>Start by adding your first video</p>
                <a href="{{ route('admin.videos.create') }}" class="dstv-btn dstv-btn-gold" style="margin-top: 12px;">
                    <i class="fas fa-plus"></i> Add Video
                </a>
            </div>
        @endforelse
    </div>
</div>

@if($videos->hasPages())
    <div style="margin-top: 24px; display: flex; justify-content: center;">
        {{ $videos->links() }}
    </div>
@endif

@endSection