@extends('layouts.admin')

@section('page_title', 'Sliders')
@section('page_subtitle', 'Manage homepage sliders')

@section('page_actions')
    <a href="{{ route('admin.sliders.create') }}" class="dstv-btn" style="background: var(--slider-color); color: white; box-shadow: 0 4px 14px rgba(139, 92, 246, 0.4);">
        <i class="fas fa-plus"></i> Add Slider
    </a>
@endsection

@section('content')

@if(session('success'))
    <div class="dstv-alert dstv-alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

<div class="dstv-card" style="border-top: 3px solid var(--slider-color);">
    <div class="dstv-card-header" style="background: var(--slider-light);">
        <h3 class="dstv-card-title" style="color: var(--slider-color);"><i class="fas fa-images"></i> All Sliders</h3>
        <span style="color: var(--slider-color); font-size: 14px; font-weight: 600;">{{ $sliders->count() }} total</span>
    </div>
    <div class="dstv-card-body" style="padding: 0;">
        @forelse($sliders as $slider)
            <div style="display: flex; align-items: center; padding: 16px 24px; border-bottom: 1px solid var(--border-light); gap: 16px;">
                <div style="width: 100px; height: 60px; background: var(--slider-light); border-radius: 10px; overflow: hidden; flex-shrink: 0;">
                    @if($slider->image)
                        <img src="{{ asset('storage/' . $slider->image) }}" alt="{{ $slider->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-image" style="color: var(--slider-color); font-size: 24px;"></i>
                        </div>
                    @endif
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div style="font-weight: 600; color: var(--text-primary); font-size: 15px;">{{ $slider->title }}</div>
                    <div style="font-size: 12px; color: var(--text-secondary); margin-top: 4px;">
                        {{ ucfirst($slider->linked_type ?? 'None') }}
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <!-- Toggle Status -->
                    <form action="{{ route('admin.sliders.update', $slider) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $slider->status ? 0 : 1 }}">
                        <button type="submit" class="dstv-action-btn" style="color: var(--slider-color); border-color: var(--slider-color);" title="{{ $slider->status ? 'Deactivate' : 'Activate' }}">
                            <i class="fas fa-{{ $slider->status ? 'eye' : 'eye-slash' }}"></i>
                        </button>
                    </form>
                    <!-- Edit -->
                    <a href="{{ route('admin.sliders.edit', $slider) }}" class="dstv-action-btn" style="color: var(--slider-color); border-color: var(--slider-color);" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <!-- Delete -->
                    <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST" onsubmit="return confirm('Delete?');">
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
                <i class="fas fa-images" style="color: var(--slider-color);"></i>
                <h3>No sliders yet!</h3>
                <p>Start by creating your first slider</p>
                <a href="{{ route('admin.sliders.create') }}" class="dstv-btn" style="background: var(--slider-color); color: white; margin-top: 12px;">
                    <i class="fas fa-plus"></i> Add Slider
                </a>
            </div>
        @endforelse
    </div>
</div>

@endsection
