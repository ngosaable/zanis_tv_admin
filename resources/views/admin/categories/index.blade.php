@extends('layouts.admin')

@section('page_title', 'Categories')
@section('page_subtitle', 'Organize your content')

@section('page_actions')
    <a href="{{ route('admin.categories.create') }}" class="dstv-btn" style="background: var(--category-color); color: white; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4);">
        <i class="fas fa-plus"></i> Add Category
    </a>
@endsection

@section('content')

<div class="dstv-card" style="border-top: 3px solid var(--category-color);">
    <div class="dstv-card-header" style="background: var(--category-light);">
        <h3 class="dstv-card-title" style="color: var(--category-color);"><i class="fas fa-folder"></i> All Categories</h3>
        <span style="color: var(--category-color); font-size: 14px; font-weight: 600;">{{ $categories->count() }} total</span>
    </div>
    <div class="dstv-card-body">
        @forelse($categories as $category)
            <div style="display: flex; align-items: center; padding: 16px 24px; border-bottom: 1px solid var(--border-light); gap: 16px;">
                <div style="width: 50px; height: 50px; background: var(--dstv-blue); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-folder" style="color: white; font-size: 20px;"></i>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div style="font-weight: 600; color: var(--text-primary); font-size: 15px;">{{ $category->name }}</div>
                    <div style="font-size: 12px; color: var(--text-secondary); margin-top: 4px;">
                        {{ ucfirst($category->type ?? 'General') }}
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <!-- Toggle Status -->
                    <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $category->status ? 0 : 1 }}">
                        <button type="submit" class="dstv-action-btn" title="{{ $category->status ? 'Deactivate' : 'Activate' }}">
                            <i class="fas fa-{{ $category->status ? 'eye' : 'eye-slash' }}"></i>
                        </button>
                    </form>
                    <!-- Edit -->
                    <a href="{{ route('admin.categories.edit', $category) }}" class="dstv-action-btn" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <!-- Delete -->
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');">
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
                <i class="fas fa-folder"></i>
                <h3>No categories yet!</h3>
                <p>Start by creating your first category</p>
                <a href="{{ route('admin.categories.create') }}" class="dstv-btn dstv-btn-primary" style="margin-top: 12px;">
                    <i class="fas fa-plus"></i> Add Category
                </a>
            </div>
        @endforelse
    </div>
</div>

@endsection