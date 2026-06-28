@extends('layouts.admin')

@section('page_title', 'Upload Video')
@section('page_subtitle', 'Add new video content to your platform')

@section('page_actions')
    <a href="{{ route('admin.videos.index') }}" class="dstv-btn dstv-btn-outline">
        <i class="fas fa-arrow-left"></i> Back
    </a>
@endsection

@section('content')

<div id="uploadProgressModal" class="dstv-modal-overlay hidden">
    <div class="dstv-modal-content">
        <div class="dstv-modal-header" style="background: linear-gradient(135deg, var(--video-color), var(--video-light));">
            <h3 style="color: white;"><i class="fas fa-cog fa-spin"></i> Processing Video</h3>
            <button class="dstv-modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="dstv-modal-body" style="padding: 30px;">
            <div class="dstv-progress-info" style="margin-bottom: 20px;">
                <span id="uploadProgressText" class="dstv-progress-percent" style="font-size: 42px;">0%</span>
                <span id="uploadStatusText" class="dstv-progress-status" style="font-size: 16px; font-weight: 600;">Preparing...</span>
            </div>
            <div class="dstv-progress-bar" style="height: 16px; background: rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; margin-bottom: 20px;">
                <div id="uploadProgressBar" class="dstv-progress-fill" style="width: 0%; background: linear-gradient(90deg, var(--video-color), #60a5fa); height: 100%; border-radius: 8px; transition: width 0.5s ease;"></div>
            </div>
            <div id="uploadDetails" class="dstv-progress-details" style="text-align: center; color: var(--text-secondary); font-size: 14px; margin-bottom: 20px;">
                Please wait while we process your video...
            </div>
            <div id="processingInfo" class="dstv-processing-info hidden" style="background: var(--video-light); padding: 16px; border-radius: 12px; text-align: center;">
                <div class="dstv-quality-badge" style="background: var(--video-color); color: white; padding: 8px 16px; border-radius: 20px; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-film"></i> 
                    <span id="selectedQuality">480p Standard</span>
                    <span style="margin-left: 8px;">&middot; HLS Conversion</span>
                </div>
                <div style="margin-top: 12px; font-size: 12px; color: var(--text-secondary);">
                    <i class="fas fa-circle-notch fa-spin" style="margin-right: 6px;"></i>
                    Converting video for streaming...
                </div>
            </div>
        </div>
    </div>
</div>

<form id="videoUploadForm" action="{{ route('admin.videos.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return false;">
    @csrf
    
    <div class="dstv-card" style="border-top: 3px solid var(--video-color);">
        <div class="dstv-card-header" style="background: var(--video-light);">
            <h3 class="dstv-card-title" style="color: var(--video-color);"><i class="fas fa-info-circle"></i> Basic Information</h3>
        </div>
        <div class="dstv-card-body" style="padding: 24px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="dstv-form-group">
                    <label class="dstv-form-label">Category <span style="color: var(--danger);">*</span></label>
                    <select name="category_id" id="category_id" class="dstv-form-input" required>
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div style="color: var(--danger); font-size: 12px; margin-top: 6px;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="dstv-form-group">
                    <label class="dstv-form-label">Title <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="title" id="title" class="dstv-form-input" value="{{ old('title') }}" required maxlength="255" placeholder="Enter video title">
                    @error('title')
                        <div style="color: var(--danger); font-size: 12px; margin-top: 6px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="dstv-form-group">
                <label class="dstv-form-label">Description</label>
                <textarea name="description" id="description" class="dstv-form-input" rows="4" placeholder="Enter video description">{{ old('description') }}</textarea>
            </div>
        </div>
    </div>

    <div class="dstv-card" style="border-top: 3px solid var(--video-color); margin-top: 24px;">
        <div class="dstv-card-header" style="background: var(--video-light);">
            <h3 class="dstv-card-title" style="color: var(--video-color);"><i class="fas fa-images"></i> Media Upload</h3>
        </div>
        <div class="dstv-card-body" style="padding: 24px;">
            <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 20px;">
                <!-- Poster -->
                <div class="dstv-form-group">
                    <label class="dstv-form-label">Poster Image</label>
                    <input type="file" name="poster" id="poster" accept="image/*" onchange="previewImage(this, 'posterPreview'); showFileName(this, 'posterFileName')" class="dstv-form-input" style="padding: 8px;">
                    <p style="color: var(--text-light); font-size: 12px; margin-top: 8px;">JPEG, PNG (Max: 2MB)</p>
                    <div id="posterFileName" style="color: var(--video-color); font-size: 13px; font-weight: 600; margin-top: 8px;">No file selected</div>
                    <div id="posterPreview" class="hidden" style="margin-top: 12px; position: relative;">
                        <img id="posterPreviewImg" src="" alt="Poster preview" style="width: 100%; height: 120px; object-fit: cover; border-radius: 10px; border: 2px solid var(--video-color);">
                        <button type="button" onclick="clearPreview('poster', 'posterPreview', 'posterFileName')" style="position: absolute; top: 8px; right: 8px; width: 28px; height: 28px; background: rgba(239, 68, 68, 0.9); border: none; border-radius: 50%; color: #fff; cursor: pointer;"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                
                <!-- Thumbnail -->
                <div class="dstv-form-group">
                    <label class="dstv-form-label">Thumbnail Image</label>
                    <input type="file" name="thumbnail" id="thumbnail" accept="image/*" onchange="previewImage(this, 'thumbnailPreview'); showFileName(this, 'thumbnailFileName')" class="dstv-form-input" style="padding: 8px;">
                    <p style="color: var(--text-light); font-size: 12px; margin-top: 8px;">JPEG, PNG (Max: 2MB)</p>
                    <div id="thumbnailFileName" style="color: var(--video-color); font-size: 13px; font-weight: 600; margin-top: 8px;">No file selected</div>
                    <div id="thumbnailPreview" class="hidden" style="margin-top: 12px; position: relative;">
                        <img id="thumbnailPreviewImg" src="" alt="Thumbnail preview" style="width: 100%; height: 120px; object-fit: cover; border-radius: 10px; border: 2px solid var(--video-color);">
                        <button type="button" onclick="clearPreview('thumbnail', 'thumbnailPreview', 'thumbnailFileName')" style="position: absolute; top: 8px; right: 8px; width: 28px; height: 28px; background: rgba(239, 68, 68, 0.9); border: none; border-radius: 50%; color: #fff; cursor: pointer;"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                
                <!-- Video Source Toggle -->
                <div class="dstv-form-group" style="grid-column: 1 / -1;">
                    <label class="dstv-form-label">Video Source</label>
                    <div style="display: flex; gap: 8px; margin-top: 8px;">
                        <button type="button" class="src-btn src-active" id="srcUpload" onclick="switchSource('upload')">
                            <i class="fas fa-upload"></i> Upload
                        </button>
                        <button type="button" class="src-btn" id="srcLink" onclick="switchSource('link')">
                            <i class="fas fa-link"></i> URL Link
                        </button>
                    </div>
                    <input type="hidden" name="video_source" id="video_source" value="upload">
                </div>

                <!-- Video File -->
                <div id="uploadSection" style="grid-column: 1 / -1;">
                    <div class="dstv-form-group">
                        <label class="dstv-form-label">Video File</label>
                        <input type="file" name="video" id="video" accept="video/*" onchange="handleVideoSelect(this)" class="dstv-form-input" style="padding: 8px;">
                        <p style="color: var(--text-light); font-size: 12px; margin-top: 8px;">MP4, AVI, MOV, MKV, WebM (Max: 500MB)</p>
                        <div id="videoFileName" style="color: var(--video-color); font-size: 13px; font-weight: 600; margin-top: 8px;">No file selected</div>
                        <div id="videoPreview" class="hidden" style="margin-top: 12px; max-width: 320px;">
                            <div style="position: relative;">
                                <video id="videoPreviewPlayer" controls style="width: 100%; height: 180px; object-fit: contain; border-radius: 8px; background: #000;"></video>
                                <button type="button" onclick="clearVideoPreview()" style="position: absolute; top: 8px; right: 8px; width: 28px; height: 28px; background: rgba(239, 68, 68, 0.9); border: none; border-radius: 50%; color: #fff; cursor: pointer;"><i class="fas fa-times"></i></button>
                            </div>
                            <div id="videoFileInfo" style="color: var(--text-secondary); font-size: 12px; margin-top: 8px;"></div>
                        </div>
                        @error('video')
                            <div style="color: var(--danger); font-size: 12px; margin-top: 6px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Video URL -->
                <div id="linkSection" class="hidden" style="grid-column: 1 / -1;">
                    <div class="dstv-form-group">
                        <label class="dstv-form-label">Video URL</label>
                        <div style="position: relative;">
                            <input type="url" name="video_url" id="video_url" placeholder="https://example.com/video.mp4" value="{{ old('video_url') }}" oninput="previewVideoLink(this.value)" class="dstv-form-input" style="padding: 8px; padding-left: 40px;">
                            <i class="fas fa-link" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--video-color); font-size: 16px;"></i>
                        </div>
                        <p style="color: var(--text-light); font-size: 12px; margin-top: 8px;">MP4, M3U8, MKV, WebM (Direct video links)</p>
                        <div id="videoLinkPreview" class="hidden" style="margin-top: 12px; position: relative;">
                            <video id="videoLinkPlayer" controls style="width: 100%; height: 120px; object-fit: contain; border-radius: 10px; background: #000; border: 2px solid var(--video-color);"></video>
                            <button type="button" onclick="clearVideoUrl()" style="position: absolute; top: 8px; right: 8px; width: 28px; height: 28px; background: rgba(239, 68, 68, 0.9); border: none; border-radius: 50%; color: #fff; cursor: pointer;"><i class="fas fa-times"></i></button>
                        </div>
                        @error('video_url')
                            <div style="color: var(--danger); font-size: 12px; margin-top: 6px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div id="inlineUploadProgressCard" class="hidden" style="grid-column: 1 / -1; background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.18); border-radius: 14px; padding: 18px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 10px;">
                        <div>
                            <div id="inlineUploadStatus" style="font-size: 15px; font-weight: 700; color: var(--video-color);">Ready to upload</div>
                            <div id="inlineUploadDetails" style="font-size: 13px; color: var(--text-secondary);">Your upload progress will appear here.</div>
                        </div>
                        <div id="inlineUploadPercent" style="font-size: 24px; font-weight: 700; color: var(--video-color);">0%</div>
                    </div>
                    <div style="height: 12px; background: rgba(15, 23, 42, 0.08); border-radius: 999px; overflow: hidden;">
                        <div id="inlineUploadBar" style="width: 0%; height: 100%; border-radius: 999px; background: linear-gradient(90deg, var(--video-color), #60a5fa); transition: width 0.3s ease;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dstv-card" style="border-top: 3px solid var(--video-color); margin-top: 24px;">
        <div class="dstv-card-header" style="background: var(--video-light);">
            <h3 class="dstv-card-title" style="color: var(--video-color);"><i class="fas fa-cog"></i> Processing Options</h3>
        </div>
        <div class="dstv-card-body" style="padding: 24px;">
            <div id="convertToHlsRow" style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                    <input type="hidden" name="convert_to_hls" value="0">
                    <input type="checkbox" name="convert_to_hls" id="convert_to_hls" value="1" {{ old('convert_to_hls', '1') ? 'checked' : '' }} style="display: none;">
                    <div class="dstv-toggle-slider">
                        <div class="dstv-toggle-knob"></div>
                    </div>
                    <span style="font-weight: 500; color: var(--text-primary);">Convert to HLS for streaming</span>
                </label>
                <p style="color: var(--text-secondary); font-size: 13px; margin-top: 4px; margin-left: 60px;">HLS enables smooth streaming with quality adaptation</p>
            </div>
            <div id="hlsOptions" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-light);">
                <label class="dstv-form-label" style="color: var(--video-color);">Select Quality for HLS Conversion</label>
                <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 12px;">
                    <button type="button" class="quality-btn {{ old('hls_quality', '480p') === '360p' ? 'quality-selected' : '' }}" data-quality="360p" onclick="selectQuality(this, '360p')">
                        <i class="fas fa-mobile-alt"></i>
                        <span>360p</span>
                        <small>Low (~600kbps)</small>
                    </button>
                    <button type="button" class="quality-btn {{ old('hls_quality', '480p') === '480p' ? 'quality-selected' : '' }}" data-quality="480p" onclick="selectQuality(this, '480p')">
                        <i class="fas fa-laptop"></i>
                        <span>480p</span>
                        <small>Standard (~1Mbps)</small>
                    </button>
                    <button type="button" class="quality-btn {{ old('hls_quality', '480p') === '720p' ? 'quality-selected' : '' }}" data-quality="720p" onclick="selectQuality(this, '720p')">
                        <i class="fas fa-desktop"></i>
                        <span>720p</span>
                        <small>HD (~2.5Mbps)</small>
                    </button>
                    <button type="button" class="quality-btn {{ old('hls_quality', '480p') === '1080p' ? 'quality-selected' : '' }}" data-quality="1080p" onclick="selectQuality(this, '1080p')">
                        <i class="fas fa-tv"></i>
                        <span>1080p</span>
                        <small>Full HD (~5Mbps)</small>
                    </button>
                    <button type="button" class="quality-btn quality-adaptive {{ old('hls_quality', '480p') === 'adaptive' ? 'quality-selected' : '' }}" data-quality="adaptive" onclick="selectQuality(this, 'adaptive')">
                        <i class="fas fa-layer-group"></i>
                        <span>Adaptive</span>
                        <small>All Qualities</small>
                    </button>
                </div>
                <input type="hidden" name="hls_quality" id="hls_quality_input" value="{{ old('hls_quality', '480p') }}">
                <div id="qualitySelectionSummary" style="margin-top: 14px; padding: 14px 16px; border-radius: 12px; background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.16);">
                    <div style="font-size: 13px; font-weight: 700; color: var(--video-color); margin-bottom: 4px;">Selected quality</div>
                    <div id="qualitySelectionLabel" style="font-size: 15px; font-weight: 700; color: var(--text-primary);">480p Standard</div>
                    <div id="qualitySelectionHelp" style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Balanced file size and playback quality for most uploads.</div>
                </div>
                <p style="color: var(--text-secondary); font-size: 13px; margin-top: 12px; display: flex; align-items: center; gap: 8px;"><i class="fas fa-info-circle"></i> Adaptive creates multiple quality versions for best streaming experience</p>
            </div>

            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-light);">
                <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox" name="status" id="status" value="1" {{ old('status', '1') ? 'checked' : '' }} style="display: none;">
                    <div class="dstv-toggle-slider">
                        <div class="dstv-toggle-knob"></div>
                    </div>
                    <span style="font-weight: 500; color: var(--text-primary);">Publish immediately</span>
                </label>
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--border-light);">
        <a href="{{ route('admin.videos.index') }}" class="dstv-btn dstv-btn-outline">
            <i class="fas fa-arrow-left"></i> Cancel
        </a>
        <button type="submit" id="uploadBtn" class="dstv-btn" style="background: var(--video-color); color: white; box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4);">
            <i class="fas fa-upload"></i> {{ old('video_source', 'upload') === 'link' ? 'Save Video Link' : 'Upload Video' }}
        </button>
    </div>

</form>

@endsection

@push('styles')
<style>
.hidden { display: none !important; }

/* Modal Styles */
.dstv-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    backdrop-filter: blur(5px);
}

.dstv-modal-content {
    background: var(--bg-white);
    border: 1px solid var(--border-light);
    border-radius: 18px;
    width: 90%;
    max-width: 480px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.dstv-modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-light);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.dstv-modal-header h3 {
    margin: 0;
    font-size: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.dstv-modal-close {
    background: none;
    border: none;
    color: var(--text-secondary);
    font-size: 24px;
    cursor: pointer;
    padding: 0;
    line-height: 1;
}

.dstv-modal-body {
    padding: 24px;
}

.dstv-progress-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
}

.dstv-progress-percent {
    font-size: 32px;
    font-weight: 700;
    background: linear-gradient(135deg, var(--video-color), var(--video-light));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.dstv-progress-status {
    color: var(--text-secondary);
    font-size: 14px;
    align-self: flex-end;
}

.dstv-progress-bar {
    height: 12px;
    background: var(--bg-primary);
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 16px;
}

.dstv-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--video-color), var(--video-light));
    border-radius: 6px;
    transition: width 0.3s ease;
}

.dstv-progress-details {
    color: var(--text-secondary);
    font-size: 13px;
    text-align: center;
}

.dstv-processing-info {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--border-light);
}

.dstv-quality-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: rgba(59, 130, 246, 0.1);
    border-radius: 20px;
    color: var(--video-color);
    font-size: 14px;
    font-weight: 600;
}

/* Toggle Switch */
.dstv-toggle-slider {
    width: 48px;
    height: 26px;
    background: var(--border-light);
    border-radius: 13px;
    position: relative;
    transition: background 0.25s ease;
}

.dstv-toggle-knob {
    width: 20px;
    height: 20px;
    background: white;
    border-radius: 50%;
    position: absolute;
    top: 3px;
    left: 3px;
    transition: transform 0.25s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

input:checked + .dstv-toggle-slider {
    background: var(--video-color);
}

input:checked + .dstv-toggle-slider .dstv-toggle-knob {
    transform: translateX(22px);
}

/* Source Switch Buttons */
.src-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 24px;
    background: var(--bg-primary);
    border: 2px solid var(--border-light);
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    color: var(--text-secondary);
    transition: all 0.3s ease;
}
.src-btn:hover {
    transform: translateY(-1px);
}
#srcUpload:hover {
    border-color: #3b82f6;
    color: #3b82f6;
}
#srcLink:hover {
    border-color: #f59e0b;
    color: #f59e0b;
}
#srcUpload.src-active {
    border-color: #3b82f6;
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
}
#srcLink.src-active {
    border-color: #f59e0b;
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);
}

/* Quality Buttons */
.quality-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 14px 20px;
    background: var(--bg-primary);
    border: 2px solid var(--border-light);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.25s ease;
    min-width: 120px;
}

.quality-btn i {
    font-size: 24px;
    color: var(--text-secondary);
    margin-bottom: 6px;
}

.quality-btn span {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 15px;
}

.quality-btn small {
    color: var(--text-secondary);
    font-size: 11px;
    margin-top: 4px;
}

.quality-btn:hover {
    border-color: var(--video-color);
    transform: translateY(-2px);
}

.quality-btn.quality-selected {
    border-color: var(--video-color);
    background: rgba(59, 130, 246, 0.1);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}

.quality-btn.quality-selected i,
.quality-btn.quality-selected span {
    color: var(--video-color);
}

.quality-btn.quality-adaptive {
    border-color: var(--dstv-gold);
}

.quality-btn.quality-adaptive i {
    color: var(--dstv-gold-dark);
}

.quality-btn.quality-adaptive.quality-selected {
    background: rgba(255, 184, 28, 0.1);
    box-shadow: 0 4px 12px rgba(255, 184, 28, 0.2);
}

input:checked + .dstv-quality-adaptive {
    background: rgba(255, 184, 28, 0.05);
}

@media (max-width: 768px) {
    div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
    
    div[style*="grid-template-columns: repeat(3, minmax(0, 1fr))"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
let currentChunk = 0;
let totalChunks = 0;
let fileToUpload = null;
let chunkSize = 5 * 1024 * 1024; // 5MB chunks
let activeVideoPreviewUrl = null;

const QUALITY_OPTIONS = {
    '360p': {
        badge: '360p Low',
        label: '360p Low',
        help: 'Best for smaller screens and slower connections.'
    },
    '480p': {
        badge: '480p Standard',
        label: '480p Standard',
        help: 'Balanced file size and playback quality for most uploads.'
    },
    '720p': {
        badge: '720p HD',
        label: '720p HD',
        help: 'Sharper playback for tablets, laptops, and TV apps.'
    },
    '1080p': {
        badge: '1080p Full HD',
        label: '1080p Full HD',
        help: 'Highest single-quality output with a larger final size.'
    },
    adaptive: {
        badge: 'Adaptive HLS',
        label: 'Adaptive Streaming',
        help: 'Creates multiple HLS qualities so playback can switch automatically.'
    }
};

function getQualityMeta(quality) {
    return QUALITY_OPTIONS[quality] || QUALITY_OPTIONS['480p'];
}

function updateQualitySummary(quality) {
    const meta = getQualityMeta(quality);
    document.getElementById('selectedQuality').textContent = meta.badge;
    document.getElementById('qualitySelectionLabel').textContent = meta.label;
    document.getElementById('qualitySelectionHelp').textContent = meta.help;
}

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const previewImage = document.getElementById(previewId + 'Img');

    if (!input.files || !input.files[0]) {
        preview.classList.add('hidden');
        previewImage.src = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        previewImage.src = e.target.result;
        preview.classList.remove('hidden');
    };
    reader.onerror = function() {
        preview.classList.add('hidden');
        previewImage.src = '';
    };
    reader.readAsDataURL(input.files[0]);
}

function showFileName(input, fileNameId) {
    const fileNameSpan = document.getElementById(fileNameId);
    if (input.files && input.files[0]) {
        fileNameSpan.textContent = input.files[0].name;
        fileNameSpan.style.color = 'var(--video-color)';
        fileNameSpan.style.fontWeight = '600';
    } else {
        fileNameSpan.textContent = 'No file selected';
        fileNameSpan.style.color = 'var(--text-secondary)';
        fileNameSpan.style.fontWeight = 'normal';
    }
}

function clearPreview(inputId, previewId, fileNameId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const previewImage = document.getElementById(previewId + 'Img');

    input.value = '';
    preview.classList.add('hidden');
    previewImage.src = '';
    showFileName(input, fileNameId);
}

function clearVideoPreview() {
    const videoInput = document.getElementById('video');
    const videoPreview = document.getElementById('videoPreview');
    const videoPlayer = document.getElementById('videoPreviewPlayer');
    const fileInfo = document.getElementById('videoFileInfo');

    if (activeVideoPreviewUrl) {
        URL.revokeObjectURL(activeVideoPreviewUrl);
        activeVideoPreviewUrl = null;
    }

    videoInput.value = '';
    videoPlayer.pause();
    videoPlayer.removeAttribute('src');
    videoPlayer.load();
    fileInfo.textContent = '';
    videoPreview.classList.add('hidden');
    showFileName(videoInput, 'videoFileName');
    updateInlineUploadProgress(0, 'Ready to upload', 'Your upload progress will appear here.');
}

function defaultSubmitLabel() {
    return '<i class="fas fa-upload"></i> Upload Video';
}

function showInlineUploadProgress() {
    document.getElementById('inlineUploadProgressCard').classList.remove('hidden');
}

function updateInlineUploadProgress(percent, status, details) {
    showInlineUploadProgress();
    document.getElementById('inlineUploadBar').style.width = percent + '%';
    document.getElementById('inlineUploadPercent').textContent = percent + '%';
    document.getElementById('inlineUploadStatus').textContent = status;
    document.getElementById('inlineUploadDetails').textContent = details;
}

function resetProgressDisplay() {
    document.getElementById('uploadProgressBar').style.width = '0%';
    document.getElementById('uploadProgressBar').style.background = 'linear-gradient(90deg, var(--video-color), #60a5fa)';
    document.getElementById('uploadProgressText').textContent = '0%';
    document.getElementById('uploadStatusText').textContent = 'Preparing...';
    document.getElementById('uploadDetails').textContent = 'Please wait while we process your video...';
    document.getElementById('inlineUploadBar').style.width = '0%';
    document.getElementById('inlineUploadBar').style.background = 'linear-gradient(90deg, var(--video-color), #60a5fa)';
    document.getElementById('inlineUploadPercent').textContent = '0%';
    document.getElementById('inlineUploadStatus').textContent = 'Ready to upload';
    document.getElementById('inlineUploadDetails').textContent = 'Your upload progress will appear here.';
    document.getElementById('processingInfo').classList.add('hidden');
}

function setErrorProgressState(message) {
    document.getElementById('uploadStatusText').textContent = 'Error!';
    document.getElementById('uploadDetails').textContent = message;
    document.getElementById('uploadProgressBar').style.background = 'linear-gradient(90deg, var(--danger), #fca5a5)';
    document.getElementById('inlineUploadBar').style.background = 'linear-gradient(90deg, var(--danger), #fca5a5)';
}

function prepareAjaxRequest(xhr) {
    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Accept', 'application/json');
}

function parseJsonResponse(xhr) {
    try {
        return JSON.parse(xhr.responseText);
    } catch (error) {
        return null;
    }
}

function extractErrorMessage(xhr, fallbackMessage) {
    const response = parseJsonResponse(xhr);

    if (response?.message) {
        return response.message;
    }

    const firstError = response?.errors ? Object.values(response.errors)[0] : null;
    if (Array.isArray(firstError) && firstError[0]) {
        return firstError[0];
    }

    return fallbackMessage;
}



function previewVideoLink(url) {
    const linkPreview = document.getElementById('videoLinkPreview');
    const videoPlayer = document.getElementById('videoLinkPlayer');
    const cleanUrl = (url || '').trim();
    
    if (cleanUrl) {
        linkPreview.classList.remove('hidden');
        videoPlayer.src = cleanUrl;
    } else {
        linkPreview.classList.add('hidden');
        videoPlayer.src = '';
    }
}

function clearVideoUrl() {
    document.getElementById('video_url').value = '';
    previewVideoLink('');
    document.getElementById('video_url').focus();
}

function handleVideoSelect(input) {
    showFileName(input, 'videoFileName');
    previewVideo(input);
    updateInlineUploadProgress(0, 'Ready to upload', input.files && input.files[0] ? input.files[0].name : 'Your upload progress will appear here.');
    
    if (input.files && input.files[0]) {
        const fileSizeMB = (input.files[0].size / 1024 / 1024).toFixed(2);
        if (fileSizeMB > 40) {
            console.log('Large file detected (' + fileSizeMB + 'MB). Chunked upload will be used.');
        }
    }
}

function previewVideo(input) {
    const videoPreview = document.getElementById('videoPreview');
    const videoPlayer = document.getElementById('videoPreviewPlayer');
    const fileInfo = document.getElementById('videoFileInfo');

    if (activeVideoPreviewUrl) {
        URL.revokeObjectURL(activeVideoPreviewUrl);
        activeVideoPreviewUrl = null;
    }

    if (!input.files || !input.files[0]) {
        videoPlayer.pause();
        videoPlayer.removeAttribute('src');
        videoPlayer.load();
        fileInfo.textContent = '';
        videoPreview.classList.add('hidden');
        return;
    }

    const file = input.files[0];
    activeVideoPreviewUrl = URL.createObjectURL(file);
    videoPlayer.src = activeVideoPreviewUrl;

    const fileSize = (file.size / 1024 / 1024).toFixed(2);
    fileInfo.textContent = 'Size: ' + fileSize + ' MB | Type: ' + (file.type || 'Unknown');
    videoPreview.classList.remove('hidden');

    videoPlayer.onloadedmetadata = function() {
        const duration = formatDuration(videoPlayer.duration);
        fileInfo.textContent = 'Size: ' + fileSize + ' MB | Type: ' + (file.type || 'Unknown') + ' | Duration: ' + duration;
    };

    videoPlayer.onerror = function() {
        fileInfo.textContent = 'Size: ' + fileSize + ' MB | Preview unavailable in this browser, but the file is selected.';
    };
}

function formatDuration(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = Math.floor(seconds % 60);
    
    if (hours > 0) {
        return `${hours}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    } else {
        return `${minutes}:${secs.toString().padStart(2, '0')}`;
    }
}

function closeModal() {
    document.getElementById('uploadProgressModal').classList.add('hidden');
}

function selectQuality(btn, quality) {
    document.querySelectorAll('.quality-btn').forEach((button) => {
        button.classList.remove('quality-selected');
        button.setAttribute('aria-pressed', 'false');
    });
    btn.classList.add('quality-selected');
    btn.setAttribute('aria-pressed', 'true');
    document.getElementById('hls_quality_input').value = quality;
    updateQualitySummary(quality);
}

function finishSuccessfulUpload(message, redirectUrl) {
    document.getElementById('uploadProgressBar').style.width = '100%';
    document.getElementById('uploadProgressText').textContent = '100%';
    document.getElementById('uploadStatusText').textContent = 'Complete!';
    document.getElementById('uploadDetails').textContent = message;
    updateInlineUploadProgress(100, 'Complete!', message);

    setTimeout(() => {
        window.location.href = redirectUrl || '{{ route("admin.videos.index") }}';
    }, 700);
}

function handleUploadSuccess(response) {
    if (response.success && response.processing_started && response.video_id) {
        document.getElementById('uploadProgressBar').style.width = '100%';
        document.getElementById('uploadProgressText').textContent = '100%';
        document.getElementById('uploadStatusText').textContent = 'Processing...';
        document.getElementById('uploadDetails').textContent = response.message || 'Video uploaded! Starting HLS conversion.';
        updateInlineUploadProgress(100, 'Processing...', response.message || 'Upload finished. Starting HLS conversion.');
        document.getElementById('processingInfo').classList.remove('hidden');
        pollProcessingStatus(response.video_id);
        return;
    }

    if (response.success) {
        finishSuccessfulUpload(response.message || 'Video uploaded successfully.', response.redirect);
    }
}

function enableUploadButton() {
    const uploadBtn = document.getElementById('uploadBtn');
    uploadBtn.disabled = false;
    uploadBtn.innerHTML = defaultSubmitLabel();
}

document.addEventListener('DOMContentLoaded', function() {
    const convertToHls = document.getElementById('convert_to_hls');
    const uploadForm = document.getElementById('videoUploadForm');
    const uploadBtn = document.getElementById('uploadBtn');
    const initialQuality = document.getElementById('hls_quality_input').value || '480p';

    switchSource(document.getElementById('video_source').value || 'upload');
    previewVideoLink(document.getElementById('video_url').value);
    updateQualitySummary(initialQuality);
    document.querySelectorAll('.quality-btn').forEach((button) => {
        button.setAttribute('aria-pressed', button.dataset.quality === initialQuality ? 'true' : 'false');
    });
    
    if (convertToHls) {
        convertToHls.addEventListener('change', function() {
            document.getElementById('hlsOptions').classList.toggle('hidden', !this.checked);
        });
    }
    
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleUpload();
        });
    }
    
    if (uploadBtn) {
        uploadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            handleUpload();
        });
    }
});

function switchSource(source) {
    document.getElementById('video_source').value = source;
    document.getElementById('srcUpload').classList.toggle('src-active', source === 'upload');
    document.getElementById('srcLink').classList.toggle('src-active', source === 'link');
    document.getElementById('uploadSection').classList.toggle('hidden', source !== 'upload');
    document.getElementById('linkSection').classList.toggle('hidden', source !== 'link');
    
    const hlsRow = document.getElementById('convertToHlsRow');
    const hlsOptions = document.getElementById('hlsOptions');
    if (source === 'link') {
        hlsRow.classList.add('hidden');
        hlsOptions.classList.add('hidden');
    } else {
        hlsRow.classList.remove('hidden');
        hlsOptions.classList.toggle('hidden', !document.getElementById('convert_to_hls').checked);
    }
}

function handleUpload() {
    const form = document.getElementById('videoUploadForm');
    const source = document.getElementById('video_source').value;
    
    if (source === 'link') {
        handleLinkUpload(form);
        return;
    }
    
    const videoInput = document.getElementById('video');
    
    if (!videoInput.files[0]) {
        alert('Please select a video file');
        return;
    }
    
    const file = videoInput.files[0];
    fileToUpload = file;
    
    if (file.size <= 20 * 1024 * 1024) {
        uploadNormal(form);
        return;
    }
    
    openModal();
    totalChunks = Math.ceil(file.size / chunkSize);
    currentChunk = 0;
    uploadNextChunk(form);
}

function handleLinkUpload(form) {
    const videoUrl = document.getElementById('video_url').value;
    
    if (!videoUrl) {
        alert('Please enter a video URL');
        return;
    }
    
    openModal();
    updateInlineUploadProgress(10, 'Saving video link...', 'Creating the video entry from your URL.');
    
    const formData = new FormData(form);
    formData.append('video_url', videoUrl);
    formData.append('video_source', 'link');
    
    const statusText = document.getElementById('uploadStatusText');
    const detailsText = document.getElementById('uploadDetails');
    const progressBar = document.getElementById('uploadProgressBar');
    const progressText = document.getElementById('uploadProgressText');
    const uploadBtn = document.getElementById('uploadBtn');
    
    statusText.textContent = 'Saving video link...';
    detailsText.textContent = 'Creating database entry...';
    progressBar.style.width = '50%';
    progressText.textContent = '50%';
    updateInlineUploadProgress(50, 'Saving video link...', 'Creating the video entry from your URL.');
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '{{ route("admin.videos.store") }}');
    prepareAjaxRequest(xhr);
    
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const percent = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = percent + '%';
            progressText.textContent = percent + '%';
            detailsText.textContent = 'Uploading cover images and saving the link...';
            statusText.textContent = 'Saving video link...';
            updateInlineUploadProgress(percent, 'Saving video link...', 'Uploading cover images and saving the link.');
        }
    });
    
    xhr.onload = function() {
        enableUploadButton();
        
        if (xhr.status === 200) {
            const response = parseJsonResponse(xhr);
            if (response && response.success) {
                finishSuccessfulUpload(response.message || 'Video link saved successfully.', response.redirect);
            } else {
                const message = extractErrorMessage(xhr, 'Failed to save video link.');
                setErrorProgressState(message);
                updateInlineUploadProgress(100, 'Error!', message);
            }
        } else {
            const message = extractErrorMessage(xhr, 'Server error occurred while saving the link.');
            setErrorProgressState(message);
            updateInlineUploadProgress(100, 'Error!', message);
        }
    };
    
    xhr.onerror = function() {
        enableUploadButton();
        setErrorProgressState('Network error while saving the link.');
        updateInlineUploadProgress(100, 'Error!', 'Network error while saving the link.');
    };
    
    xhr.send(formData);
}

function uploadNormal(form) {
    openModal();
    updateInlineUploadProgress(0, 'Starting upload...', 'Preparing your video file for upload.');
    
    const formData = new FormData(form);
    const progressBar = document.getElementById('uploadProgressBar');
    const progressText = document.getElementById('uploadProgressText');
    const statusText = document.getElementById('uploadStatusText');
    const detailsText = document.getElementById('uploadDetails');
    const uploadBtn = document.getElementById('uploadBtn');
    const quality = formData.get('hls_quality') || '720p';
    
    updateQualitySummary(quality);
    
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
    
    const xhr = new XMLHttpRequest();
    
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const percentComplete = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = percentComplete + '%';
            progressText.textContent = percentComplete + '%';
            statusText.textContent = 'Uploading...';
            detailsText.textContent = 'Uploading your selected video file...';
            updateInlineUploadProgress(percentComplete, 'Uploading...', 'Uploading your selected video file.');
        }
    });
    
    xhr.addEventListener('load', function() {
        if (xhr.status === 200) {
            const response = parseJsonResponse(xhr);
            if (response) {
                handleUploadSuccess(response);
            } else {
                finishSuccessfulUpload('Video uploaded successfully.', '{{ route("admin.videos.index") }}');
            }
        } else {
            const message = extractErrorMessage(xhr, 'Upload failed. Please try again.');
            setErrorProgressState(message);
            updateInlineUploadProgress(100, 'Error!', message);
            enableUploadButton();
        }
    });
    
    xhr.addEventListener('error', function() {
        setErrorProgressState('Network error. Please check your connection.');
        updateInlineUploadProgress(100, 'Error!', 'Network error. Please check your connection.');
        enableUploadButton();
    });
    
    xhr.open('POST', form.action);
    prepareAjaxRequest(xhr);
    xhr.send(formData);
}

function uploadNextChunk(form) {
    if (currentChunk >= totalChunks) {
        // All chunks uploaded, now process the video
        mergeChunks(form);
        return;
    }
    
    const start = currentChunk * chunkSize;
    const end = Math.min(start + chunkSize, fileToUpload.size);
    const chunk = fileToUpload.slice(start, end);
    
    const formData = new FormData();
    formData.append('video_chunk', chunk);
    formData.append('chunk_index', currentChunk);
    formData.append('total_chunks', totalChunks);
    formData.append('original_name', fileToUpload.name);
    formData.append('category_id', form.querySelector('[name="category_id"]').value);
    formData.append('title', form.querySelector('[name="title"]').value);
    formData.append('description', form.querySelector('[name="description"]').value);
    formData.append('convert_to_hls', document.getElementById('convert_to_hls').checked ? '1' : '0');
    formData.append('hls_quality', document.getElementById('hls_quality_input').value || '480p');
    formData.append('status', document.getElementById('status').checked ? '1' : '0');
    
    const progressBar = document.getElementById('uploadProgressBar');
    const progressText = document.getElementById('uploadProgressText');
    const statusText = document.getElementById('uploadStatusText');
    const detailsText = document.getElementById('uploadDetails');
    const percentComplete = Math.round((start / fileToUpload.size) * 100);
    progressBar.style.width = percentComplete + '%';
    progressText.textContent = percentComplete + '%';
    statusText.textContent = `Uploading chunk ${currentChunk + 1}/${totalChunks}...`;
    detailsText.textContent = `Uploading ${formatFileSize(chunk.size)} chunk...`;
    updateInlineUploadProgress(percentComplete, `Uploading chunk ${currentChunk + 1}/${totalChunks}...`, `Uploading ${formatFileSize(chunk.size)} chunk.`);
    
    const xhr = new XMLHttpRequest();

    xhr.upload.addEventListener('progress', function(e) {
        if (!e.lengthComputable) {
            return;
        }

        const uploadedBytes = start + e.loaded;
        const overallPercent = Math.min(99, Math.round((uploadedBytes / fileToUpload.size) * 100));
        progressBar.style.width = overallPercent + '%';
        progressText.textContent = overallPercent + '%';
        statusText.textContent = `Uploading chunk ${currentChunk + 1}/${totalChunks}...`;
        detailsText.textContent = `Uploaded ${formatFileSize(uploadedBytes)} of ${formatFileSize(fileToUpload.size)}.`;
        updateInlineUploadProgress(overallPercent, `Uploading chunk ${currentChunk + 1}/${totalChunks}...`, `Uploaded ${formatFileSize(uploadedBytes)} of ${formatFileSize(fileToUpload.size)}.`);
    });
    
    xhr.addEventListener('load', function() {
        if (xhr.status === 200) {
            currentChunk++;
            uploadNextChunk(form);
        } else {
            const message = extractErrorMessage(xhr, 'Chunk upload failed. Please try again.');
            setErrorProgressState(message);
            updateInlineUploadProgress(100, 'Error!', message);
            enableUploadButton();
        }
    });
    
    xhr.addEventListener('error', function() {
        setErrorProgressState('Network error during chunk upload.');
        updateInlineUploadProgress(100, 'Error!', 'Network error during chunk upload.');
        enableUploadButton();
    });
    
    xhr.open('POST', '{{ route("admin.videos.chunk-upload") }}');
    prepareAjaxRequest(xhr);
    xhr.send(formData);
}

function mergeChunks(form) {
    const statusText = document.getElementById('uploadStatusText');
    const detailsText = document.getElementById('uploadDetails');
    const progressBar = document.getElementById('uploadProgressBar');
    const progressText = document.getElementById('uploadProgressText');
    updateQualitySummary(document.getElementById('hls_quality_input').value || '480p');
    statusText.textContent = 'Merging chunks...';
    detailsText.textContent = 'Finalizing video file...';
    progressBar.style.width = '100%';
    progressText.textContent = '100%';
    updateInlineUploadProgress(100, 'Merging chunks...', 'Finalizing the uploaded video file.');
    
    const formData = new FormData();
    formData.append('original_name', fileToUpload.name);
    formData.append('total_chunks', totalChunks);
    formData.append('category_id', form.querySelector('[name="category_id"]').value);
    formData.append('title', form.querySelector('[name="title"]').value);
    formData.append('description', form.querySelector('[name="description"]').value);
    formData.append('convert_to_hls', document.getElementById('convert_to_hls').checked ? '1' : '0');
    formData.append('hls_quality', document.getElementById('hls_quality_input').value || '480p');
    formData.append('status', document.getElementById('status').checked ? '1' : '0');
    
    // Handle poster and thumbnail
    const posterInput = form.querySelector('[name="poster"]');
    const thumbnailInput = form.querySelector('[name="thumbnail"]');
    if (posterInput && posterInput.files[0]) {
        formData.append('poster', posterInput.files[0]);
    }
    if (thumbnailInput && thumbnailInput.files[0]) {
        formData.append('thumbnail', thumbnailInput.files[0]);
    }
    
    const xhr = new XMLHttpRequest();
    
    xhr.addEventListener('load', function() {
        if (xhr.status === 200) {
            const response = parseJsonResponse(xhr);
            if (response) {
                handleUploadSuccess(response);
            } else {
                finishSuccessfulUpload('Video uploaded successfully.', '{{ route("admin.videos.index") }}');
            }
        } else {
            const message = extractErrorMessage(xhr, 'Failed to merge uploaded chunks.');
            setErrorProgressState(message);
            updateInlineUploadProgress(100, 'Error!', message);
            enableUploadButton();
        }
    });
    
    xhr.addEventListener('error', function() {
        setErrorProgressState('Network error during chunk upload.');
        updateInlineUploadProgress(100, 'Error!', 'Network error during chunk upload.');
        enableUploadButton();
    });
    
    xhr.open('POST', '{{ route("admin.videos.merge-chunks") }}');
    prepareAjaxRequest(xhr);
    xhr.send(formData);
}

function pollProcessingStatus(videoId) {
    const progressBar = document.getElementById('uploadProgressBar');
    const statusText = document.getElementById('uploadStatusText');
    const progressText = document.getElementById('uploadProgressText');
    const detailsText = document.getElementById('uploadDetails');
    const processingInfo = document.getElementById('processingInfo');
    
    // Show processing info section
    processingInfo.classList.remove('hidden');
    
    const poll = setInterval(() => {
        const xhr = new XMLHttpRequest();
        xhr.addEventListener('load', function() {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    const progress = response.processing_progress || 0;
                    const status = response.processing_status;
                    const conversionStatus = response.hls_conversion_status;
                    
                    // Update progress bar directly from backend progress (0-100)
                    progressBar.style.width = progress + '%';
                    progressText.textContent = progress + '%';
                    
                    // Show detailed status based on conversion state
                    if (conversionStatus === 'pending' || status === 'pending') {
                        statusText.textContent = 'Preparing...';
                        detailsText.textContent = 'Setting up video conversion...';
                        updateInlineUploadProgress(progress, 'Preparing...', 'Setting up video conversion.');
                    } else if (conversionStatus === 'processing' || status === 'processing') {
                        statusText.textContent = 'Converting to HLS...';
                        detailsText.textContent = `Processing: ${progress}% complete`;
                        updateInlineUploadProgress(progress, 'Converting to HLS...', `Processing: ${progress}% complete.`);
                    } else if (status === 'completed') {
                        clearInterval(poll);
                        finishSuccessfulUpload('Video ready for streaming!', '{{ route("admin.videos.index") }}');
                    } else if (status === 'failed' || conversionStatus === 'failed') {
                        clearInterval(poll);
                        setErrorProgressState('Conversion failed. Please try again.');
                        updateInlineUploadProgress(progress, 'Error!', 'Conversion failed. Please try again.');
                        enableUploadButton();
                    } else {
                        statusText.textContent = 'Converting...';
                        detailsText.textContent = `Progress: ${progress}%`;
                        updateInlineUploadProgress(progress, 'Converting...', `Progress: ${progress}%`);
                    }
                } catch (e) {
                    console.error('Error parsing status:', e);
                    detailsText.textContent = 'Checking status...';
                    updateInlineUploadProgress(100, 'Checking status...', 'Waiting for the latest conversion update.');
                }
            }
        });
        
        xhr.addEventListener('error', function() {
            console.error('Failed to get processing status');
            detailsText.textContent = 'Checking conversion status...';
        });
        
        xhr.open('GET', '{{ route("admin.videos.processing-status", ":id") }}'.replace(':id', videoId));
        prepareAjaxRequest(xhr);
        xhr.send();
    }, 2000); // Poll every 2 seconds
}

function openModal() {
    const modal = document.getElementById('uploadProgressModal');
    const uploadBtn = document.getElementById('uploadBtn');
    resetProgressDisplay();
    modal.classList.remove('hidden');
    uploadBtn.disabled = true;
}

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
    if (bytes < 1024 * 1024 * 1024) return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    return (bytes / (1024 * 1024 * 1024)).toFixed(2) + ' GB';
}
</script>
@endpush
