@extends('layouts.admin')

@section('page_title', 'Edit Category')
@section('page_subtitle', 'Update category')

@section('page_actions')
    <a href="{{ route('admin.categories.index') }}" class="dstv-btn dstv-btn-outline">
        <i class="fas fa-arrow-left"></i> Back
    </a>
@endsection>

@section('content')

<form method="POST" action="{{ route('admin.categories.update', $category->id) }}">
    @csrf
    @method('PATCH')

    <div class="dstv-card" style="border-top: 3px solid var(--category-color);">
        <div class="dstv-card-header" style="background: var(--category-light);">
            <h3 class="dstv-card-title" style="color: var(--category-color);"><i class="fas fa-folder"></i> Category Details</h3>
        </div>
        <div class="dstv-card-body" style="padding: 24px;">
            <div class="dstv-form-group">
                <label class="dstv-form-label">Category Name <span style="color: var(--danger);">*</span></label>
                <input type="text" name="name" class="dstv-form-input" value="{{ $category->name }}" required>
            </div>

            <div class="dstv-form-group">
                <label class="dstv-form-label">Type</label>
                <select name="type" class="dstv-form-input">
                    <option value="Movie" @selected($category->type=='Movie')>Movie</option>
                    <option value="Live" @selected($category->type=='Live')>Live TV</option>
                    <option value="Series" @selected($category->type=='Series')>TV Series</option>
                    <option value="Documentary" @selected($category->type=='Documentary')>Documentary</option>
                </select>
            </div>

            <div class="dstv-form-group">
                <label class="dstv-form-label">Status</label>
                <select name="status" class="dstv-form-input">
                    <option value="1" @selected($category->status)>Active</option>
                    <option value="0" @selected(!$category->status)>Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 20px;">
        <a href="{{ route('admin.categories.index') }}" class="dstv-btn dstv-btn-outline">Cancel</a>
        <button type="submit" class="dstv-btn" style="background: var(--category-color); color: white; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4);">
            <i class="fas fa-save"></i> Update Category
        </button>
    </div>
</form>

@endsection
