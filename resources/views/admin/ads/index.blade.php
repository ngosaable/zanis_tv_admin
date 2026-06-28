@extends('layouts.admin')

@section('page_title', 'Advertisements')
@section('page_subtitle', 'Manage ads and banners')

@section('page_actions')
    <a href="{{ route('admin.ads.create') }}" class="dstv-btn" style="background: var(--ad-color); color: white; box-shadow: 0 4px 14px rgba(239, 68, 68, 0.4);">
        <i class="fas fa-plus"></i> Add Ad
    </a>
@endsection>

@section('content')

@if(session('success'))
    <div class="dstv-alert dstv-alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

<div class="dstv-card" style="border-top: 3px solid var(--ad-color);">
    <div class="dstv-card-header" style="background: var(--ad-light);">
        <h3 class="dstv-card-title" style="color: var(--ad-color);"><i class="fas fa-ad"></i> All Ads</h3>
        <span style="color: var(--ad-color); font-size: 14px; font-weight: 600;">{{ $ads->count() }} total</span>
    </div>
    <div class="dstv-card-body" style="padding: 0;">
        @forelse($ads as $ad)
            <div style="display: flex; align-items: center; padding: 16px 24px; border-bottom: 1px solid var(--border-light); gap: 16px;">
                <div style="width: 100px; height: 60px; background: var(--ad-light); border-radius: 10px; overflow: hidden; flex-shrink: 0;">
                    @if($ad->image)
                        <img src="{{ asset('storage/' . $ad->image) }}" alt="{{ $ad->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-image" style="color: var(--ad-color); font-size: 24px;"></i>
                        </div>
                    @endif
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div style="font-weight: 600; color: var(--text-primary); font-size: 15px;">{{ $ad->title }}</div>
                    <div style="font-size: 12px; color: var(--text-secondary); margin-top: 4px;">
                        {{ $ad->ad_type ?? 'banner' }} | {{ str_replace('_', ' ', ucfirst($ad->position ?? 'home')) }}
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <!-- Toggle Status -->
                    <form action="{{ route('admin.ads.update', $ad) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $ad->status ? 0 : 1 }}">
                        <button type="submit" class="dstv-action-btn" style="color: var(--ad-color); border-color: var(--ad-color);" title="{{ $ad->status ? 'Deactivate' : 'Activate' }}">
                            <i class="fas fa-{{ $ad->status ? 'eye' : 'eye-slash' }}"></i>
                        </button>
                    </form>
                    <!-- Edit -->
                    <a href="{{ route('admin.ads.edit', $ad) }}" class="dstv-action-btn" style="color: var(--ad-color); border-color: var(--ad-color);" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <!-- Delete -->
                    <form action="{{ route('admin.ads.destroy', $ad) }}" method="POST" onsubmit="return confirm('Delete?');">
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
                <i class="fas fa-ad" style="color: var(--ad-color);"></i>
                <h3>No ads yet!</h3>
                <p>Start by creating your first ad</p>
                <a href="{{ route('admin.ads.create') }}" class="dstv-btn" style="background: var(--ad-color); color: white; margin-top: 12px;">
                    <i class="fas fa-plus"></i> Create Ad
                </a>
            </div>
        @endforelse
    </div>
</div>

@if($ads->hasPages())
    <div style="margin-top: 24px; display: flex; justify-content: center;">
        {{ $ads->links() }}
    </div>
@endif

@endsection
