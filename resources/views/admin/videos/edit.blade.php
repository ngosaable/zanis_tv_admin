@extends('layouts.admin')

@php
    $currentSource = old('video_source', $video->is_external_url ? 'link' : 'upload');
    $currentQuality = old('hls_quality', '480p');
@endphp

@section('page_title', 'Edit Video')
@section('page_subtitle', 'Update video details')

@section('page_actions')
    <a href="{{ route('admin.videos.index') }}" class="dstv-btn dstv-btn-outline">
        <i class="fas fa-arrow-left"></i> Back
    </a>
@endsection

@section('content')

<form action="{{ route('admin.videos.update', $video) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PATCH')

    <div class="dstv-card" style="border-top: 3px solid var(--video-color);">
        <div class="dstv-card-header" style="background: var(--video-light);">
            <h3 class="dstv-card-title" style="color: var(--video-color);"><i class="fas fa-film"></i> Video Details</h3>
        </div>
        <div class="dstv-card-body" style="padding: 24px;">
            <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px;">
                <div class="dstv-form-group">
                    <label class="dstv-form-label">Category <span style="color: var(--danger);">*</span></label>
                    <select name="category_id" class="dstv-form-input" required>
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $video->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="dstv-form-group">
                    <label class="dstv-form-label">Title <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="title" class="dstv-form-input" value="{{ old('title', $video->title) }}" required>
                </div>
            </div>

            <div class="dstv-form-group" style="margin-top: 20px;">
                <label class="dstv-form-label">Description</label>
                <textarea name="description" class="dstv-form-input" rows="4">{{ old('description', $video->description) }}</textarea>
            </div>

            <div class="dstv-form-group" style="margin-top: 24px;">
                <label class="dstv-form-label">Video Source</label>
                <div style="display: flex; gap: 12px; margin-top: 8px;">
                    <button type="button" class="video-source-btn {{ $currentSource === 'upload' ? 'source-selected' : '' }}" id="editSourceUploadBtn" onclick="selectEditVideoSource('upload')">
                        <i class="fas fa-upload"></i> Upload file
                    </button>
                    <button type="button" class="video-source-btn {{ $currentSource === 'link' ? 'source-selected' : '' }}" id="editSourceLinkBtn" onclick="selectEditVideoSource('link')">
                        <i class="fas fa-link"></i> Use URL
                    </button>
                </div>
                <p style="color: var(--text-secondary); font-size: 13px; margin-top: 10px;">Switch the source if you want this video to come from a hosted URL instead of a local upload.</p>
                <input type="hidden" name="video_source" id="edit_video_source" value="{{ $currentSource }}">
            </div>

            <div id="editUploadSection" class="{{ $currentSource === 'link' ? 'hidden' : '' }}" style="margin-top: 20px;">
                <div class="dstv-form-group">
                    <label class="dstv-form-label">Replace Video File</label>
                    @if(!$video->is_external_url && $video->video_path)
                        <p style="color: var(--text-secondary); font-size: 13px; margin-bottom: 10px;">Current file: {{ $video->video_path }}</p>
                    @else
                        <p style="color: var(--text-secondary); font-size: 13px; margin-bottom: 10px;">Upload a file to switch this video back to a local upload.</p>
                    @endif
                    <input type="file" name="video" id="edit_video" class="dstv-form-input" accept="video/*" style="padding: 8px;" {{ $video->is_external_url && $currentSource === 'upload' ? 'required' : '' }}>
                    <p style="color: var(--text-light); font-size: 12px; margin-top: 8px;">Leave blank to keep the current file when this video already uses an uploaded source.</p>
                    @error('video')
                        <div style="color: var(--danger); font-size: 12px; margin-top: 6px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div id="editLinkSection" class="{{ $currentSource === 'link' ? '' : 'hidden' }}" style="margin-top: 20px;">
                <div class="dstv-form-group">
                    <label class="dstv-form-label">Video URL <span style="color: var(--danger);">*</span></label>
                    <input type="url" name="video_url" id="edit_video_url" class="dstv-form-input" value="{{ old('video_url', $video->is_external_url ? $video->video_path : '') }}" placeholder="https://example.com/video.m3u8" {{ $currentSource === 'link' ? 'required' : '' }}>
                    @error('video_url')
                        <div style="color: var(--danger); font-size: 12px; margin-top: 6px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div id="editHlsSection" class="{{ $currentSource === 'link' ? 'hidden' : '' }}" style="margin-top: 24px; padding: 20px; border: 1px solid var(--border-light); border-radius: 14px; background: rgba(59, 130, 246, 0.05);">
                <div style="margin-bottom: 16px;">
                    <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                        <input type="checkbox" name="convert_to_hls" id="edit_convert_to_hls" value="1" {{ old('convert_to_hls', '1') ? 'checked' : '' }} style="display: none;">
                        <div class="dstv-toggle-slider">
                            <div class="dstv-toggle-knob"></div>
                        </div>
                        <span style="font-weight: 500; color: var(--text-primary);">Convert replacement upload to HLS</span>
                    </label>
                    <p style="color: var(--text-secondary); font-size: 13px; margin-top: 6px; margin-left: 60px;">This only runs when you upload a new local file.</p>
                </div>

                <div id="editHlsOptions">
                    <label class="dstv-form-label" style="color: var(--video-color);">HLS Quality</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 12px;">
                        <button type="button" class="quality-btn {{ $currentQuality === '360p' ? 'quality-selected' : '' }}" onclick="selectEditQuality(this, '360p')">
                            <i class="fas fa-mobile-alt"></i>
                            <span>360p</span>
                            <small>Low</small>
                        </button>
                        <button type="button" class="quality-btn {{ $currentQuality === '480p' ? 'quality-selected' : '' }}" onclick="selectEditQuality(this, '480p')">
                            <i class="fas fa-laptop"></i>
                            <span>480p</span>
                            <small>Standard</small>
                        </button>
                        <button type="button" class="quality-btn {{ $currentQuality === '720p' ? 'quality-selected' : '' }}" onclick="selectEditQuality(this, '720p')">
                            <i class="fas fa-desktop"></i>
                            <span>720p</span>
                            <small>HD</small>
                        </button>
                        <button type="button" class="quality-btn {{ $currentQuality === '1080p' ? 'quality-selected' : '' }}" onclick="selectEditQuality(this, '1080p')">
                            <i class="fas fa-tv"></i>
                            <span>1080p</span>
                            <small>Full HD</small>
                        </button>
                        <button type="button" class="quality-btn quality-adaptive {{ $currentQuality === 'adaptive' ? 'quality-selected' : '' }}" onclick="selectEditQuality(this, 'adaptive')">
                            <i class="fas fa-layer-group"></i>
                            <span>Adaptive</span>
                            <small>All qualities</small>
                        </button>
                    </div>
                    <input type="hidden" name="hls_quality" id="edit_hls_quality" value="{{ $currentQuality }}">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; margin-top: 24px;">
                <div class="dstv-form-group">
                    <label class="dstv-form-label">Current Poster</label>
                    @if($video->poster)
                        <img src="{{ $video->poster_full_url }}" style="height: 150px; border-radius: 10px; margin-bottom: 12px; display: block;">
                    @else
                        <p style="color: var(--text-secondary); margin-bottom: 12px;">No poster</p>
                    @endif
                    <input type="file" name="poster" class="dstv-form-input" accept="image/*">
                </div>

                <div class="dstv-form-group">
                    <label class="dstv-form-label">Current Thumbnail</label>
                    @if($video->thumbnail)
                        <img src="{{ $video->thumbnail_full_url }}" style="height: 100px; border-radius: 10px; margin-bottom: 12px; display: block;">
                    @else
                        <p style="color: var(--text-secondary); margin-bottom: 12px;">No thumbnail</p>
                    @endif
                    <input type="file" name="thumbnail" class="dstv-form-input" accept="image/*">
                </div>
            </div>

            <div class="dstv-form-group" style="margin-top: 24px;">
                <label class="dstv-form-label">Status</label>
                <select name="status" class="dstv-form-input">
                    <option value="1" {{ old('status', $video->status) ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', $video->status) ? '' : 'selected' }}>Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--border-light);">
        <a href="{{ route('admin.videos.index') }}" class="dstv-btn dstv-btn-outline">
            <i class="fas fa-arrow-left"></i> Cancel
        </a>
        <button type="submit" class="dstv-btn dstv-btn-primary">
            <i class="fas fa-save"></i> Update Video
        </button>
    </div>
</form>

@endsection

@push('styles')
<style>
.video-source-btn {
    flex: 1;
    border: 1px solid var(--border-light);
    background: var(--bg-primary);
    color: var(--text-secondary);
    border-radius: 12px;
    padding: 14px 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.video-source-btn:hover {
    border-color: var(--video-color);
    color: var(--video-color);
}

.video-source-btn.source-selected {
    background: rgba(59, 130, 246, 0.1);
    border-color: var(--video-color);
    color: var(--video-color);
    box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.15);
}

.quality-btn {
    min-width: 110px;
    border: 1px solid var(--border-light);
    background: var(--bg-primary);
    border-radius: 12px;
    padding: 14px 12px;
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.2s ease;
}

.quality-btn i {
    font-size: 18px;
}

.quality-btn span {
    font-weight: 600;
    color: var(--text-primary);
}

.quality-btn small {
    color: var(--text-secondary);
}

.quality-btn:hover,
.quality-btn.quality-selected {
    border-color: var(--video-color);
    background: rgba(59, 130, 246, 0.1);
    box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.15);
}

.quality-btn.quality-selected i,
.quality-btn.quality-selected span {
    color: var(--video-color);
}
</style>
@endpush

@push('scripts')
<script>
function toggleEditHlsOptions() {
    const source = document.getElementById('edit_video_source').value;
    const convertToggle = document.getElementById('edit_convert_to_hls');
    const hlsOptions = document.getElementById('editHlsOptions');
    const hlsSection = document.getElementById('editHlsSection');

    if (source === 'link') {
        convertToggle.checked = false;
        convertToggle.disabled = true;
        hlsSection.classList.add('hidden');
        return;
    }

    convertToggle.disabled = false;
    hlsSection.classList.remove('hidden');
    hlsOptions.classList.toggle('hidden', !convertToggle.checked);
}

function selectEditVideoSource(source) {
    const uploadBtn = document.getElementById('editSourceUploadBtn');
    const linkBtn = document.getElementById('editSourceLinkBtn');
    const uploadSection = document.getElementById('editUploadSection');
    const linkSection = document.getElementById('editLinkSection');
    const fileInput = document.getElementById('edit_video');
    const urlInput = document.getElementById('edit_video_url');

    document.getElementById('edit_video_source').value = source;

    if (source === 'upload') {
        uploadBtn.classList.add('source-selected');
        linkBtn.classList.remove('source-selected');
        uploadSection.classList.remove('hidden');
        linkSection.classList.add('hidden');
        urlInput.removeAttribute('required');
        if (@json($video->is_external_url)) {
            fileInput.setAttribute('required', 'required');
        }
    } else {
        uploadBtn.classList.remove('source-selected');
        linkBtn.classList.add('source-selected');
        uploadSection.classList.add('hidden');
        linkSection.classList.remove('hidden');
        fileInput.removeAttribute('required');
        urlInput.setAttribute('required', 'required');
    }

    toggleEditHlsOptions();
}

function selectEditQuality(button, quality) {
    document.querySelectorAll('#editHlsSection .quality-btn').forEach((item) => {
        item.classList.remove('quality-selected');
    });

    button.classList.add('quality-selected');
    document.getElementById('edit_hls_quality').value = quality;
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('edit_convert_to_hls').addEventListener('change', toggleEditHlsOptions);
    selectEditVideoSource(document.getElementById('edit_video_source').value || 'upload');
});
</script>
@endpush
