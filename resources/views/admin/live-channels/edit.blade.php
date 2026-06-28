@extends('layouts.admin')

@section('page_title', 'Edit Live Channel')
@section('page_subtitle', 'Update channel')

@section('page_actions')
    <a href="{{ route('admin.live-channels.index') }}" class="dstv-btn dstv-btn-outline">
        <i class="fas fa-arrow-left"></i> Back
    </a>
@endsection

@section('content')

@if($errors->any())
    <div class="dstv-alert dstv-alert-error">
        <i class="fas fa-exclamation-circle"></i>
        Please fix the errors below
    </div>
@endif

<form method="POST" action="{{ route('admin.live-channels.update', $channel) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="dstv-card" style="border-top: 3px solid var(--live-color);">
        <div class="dstv-card-header" style="background: var(--live-light);">
            <h3 class="dstv-card-title" style="color: var(--live-color);"><i class="fas fa-satellite-dish"></i> Channel Details</h3>
        </div>
        <div class="dstv-card-body" style="padding: 24px;">
            <div class="dstv-form-group">
                <label class="dstv-form-label">Channel Name <span style="color: var(--danger);">*</span></label>
                <input type="text" name="name" class="dstv-form-input" value="{{ old('name', $channel->name) }}" required placeholder="Enter channel name">
            </div>

            <div class="dstv-form-group">
                <label class="dstv-form-label">Category</label>
                <select name="category_id" class="dstv-form-input">
                    <option value="">Select category (optional)</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $channel->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="dstv-form-group">
                <label class="dstv-form-label">Stream URL <span style="color: var(--danger);">*</span></label>
                <input type="text" name="stream_url" class="dstv-form-input" value="{{ old('stream_url', $channel->stream_url) }}" required placeholder="https://example.com/stream.m3u8 or .mp4">
                <p style="color: var(--text-light); font-size: 12px; margin-top: 6px;">Accepted: m3u8, m3u, mp4, mkv, webm</p>
            </div>

            <div class="dstv-form-group">
                <label class="dstv-form-label">Channel Logo</label>
                
                <!-- Current Logo -->
                @if($channel->logo)
                <div style="margin-bottom: 12px;">
                    <span style="color: var(--text-secondary); font-size: 12px; display: block; margin-bottom: 6px;">Current Logo:</span>
                    <img src="{{ asset('storage/'.$channel->logo) }}" style="height: 80px; border-radius: 10px; border: 2px solid var(--dstv-gold);">
                </div>
                @endif
                
                <!-- Upload New Logo -->
                <div style="position: relative;">
                    <input type="file" name="logo" id="channelLogoEdit" accept="image/*" onchange="previewChannelLogoEdit()">
                    <div id="uploadAreaLogoEdit" style="border: 3px dashed var(--dstv-gold); border-radius: 12px; padding: 30px 20px; text-align: center; background: rgba(255, 184, 28, 0.05); transition: all 0.25s ease;">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 28px; color: var(--dstv-gold-dark); margin-bottom: 8px; display: block;"></i>
                        <span style="color: var(--dstv-gold-dark); font-weight: 500;">Click to upload new logo (optional)</span>
                    </div>
                </div>
                <div id="logoFileNameEdit" style="color: var(--text-secondary); font-size: 13px; font-weight: 500; margin-top: 8px;">No new file selected</div>
                <p style="color: var(--text-light); font-size: 12px; margin-top: 6px;">Accepted: jpg, jpeg, png, gif, svg (Max: 2MB)</p>
                
                <!-- Logo Preview Box -->
                <div id="logoPreviewEdit" class="hidden" style="margin-top: 16px; border-radius: 12px; overflow: hidden; border: 3px solid var(--dstv-gold); background: linear-gradient(135deg, rgba(255, 184, 28, 0.1) 0%, rgba(255, 184, 28, 0.05) 100%); padding: 16px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: var(--dstv-gold-dark); font-weight: 600; font-size: 14px;">New Logo Preview</span>
                        <button type="button" onclick="removeLogoPreviewEdit()" style="width: 28px; height: 28px; background: rgba(239, 68, 68, 0.9); border: none; border-radius: 50%; color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px;"><i class="fas fa-times"></i></button>
                    </div>
                    <img id="logoPreviewImgEdit" src="" alt="New logo preview" style="width: 120px; height: 120px; object-fit: contain; border-radius: 10px; background: white; border: 2px solid var(--dstv-gold);">
                    <div id="logoPreviewInfoEdit" style="margin-top: 8px; color: var(--text-secondary); font-size: 12px; text-align: center;"></div>
                </div>
            </div>

            <div class="dstv-form-group">
                <label class="dstv-form-label">Description</label>
                <textarea name="description" class="dstv-form-input" rows="4" placeholder="Enter description">{{ old('description', $channel->description) }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="dstv-form-group">
                    <label class="dstv-form-label">Is Live?</label>
                    <div style="margin-top: 8px;">
                        <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                            <input type="hidden" name="is_live" value="0">
                            <input type="checkbox" name="is_live" id="isLive" value="1" {{ old('is_live', $channel->is_live) ? 'checked' : '' }} style="display: none;">
                            <div class="dstv-toggle-slider">
                                <div class="dstv-toggle-knob"></div>
                            </div>
                            <span style="font-weight: 500; color: var(--text-primary);">Live</span>
                        </label>
                    </div>
                </div>

                <div class="dstv-form-group">
                    <label class="dstv-form-label">Status</label>
                    <div style="margin-top: 8px;">
                        <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                            <input type="hidden" name="status" value="0">
                            <input type="checkbox" name="status" id="channelStatus" value="1" {{ old('status', $channel->status) ? 'checked' : '' }} style="display: none;">
                            <div class="dstv-toggle-slider">
                                <div class="dstv-toggle-knob"></div>
                            </div>
                            <span style="font-weight: 500; color: var(--text-primary);">Active</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 20px;">
        <a href="{{ route('admin.live-channels.index') }}" class="dstv-btn dstv-btn-outline">Cancel</a>
        <button type="submit" class="dstv-btn dstv-btn-gold">
            <i class="fas fa-save"></i> Update Channel
        </button>
    </div>
</form>

@push('scripts')
<script>
function previewChannelLogoEdit() {
    const input = document.getElementById('channelLogoEdit');
    const fileNameDiv = document.getElementById('logoFileNameEdit');
    const uploadArea = document.getElementById('uploadAreaLogoEdit');
    const previewBox = document.getElementById('logoPreviewEdit');
    const previewImg = document.getElementById('logoPreviewImgEdit');
    const previewInfo = document.getElementById('logoPreviewInfoEdit');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        fileNameDiv.textContent = file.name;
        fileNameDiv.style.color = 'var(--dstv-gold-dark)';
        fileNameDiv.style.fontWeight = '600';
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewBox.classList.remove('hidden');
            
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            previewInfo.textContent = `📁 ${file.name} | Size: ${fileSize} MB | ${file.type}`;
        };
        reader.readAsDataURL(file);
        
        uploadArea.style.borderColor = 'var(--success)';
        uploadArea.style.background = 'rgba(16, 185, 129, 0.1)';
    } else {
        fileNameDiv.textContent = 'No new file selected';
        fileNameDiv.style.color = 'var(--text-secondary)';
        fileNameDiv.style.fontWeight = 'normal';
    }
}

function removeLogoPreviewEdit() {
    const input = document.getElementById('channelLogoEdit');
    const fileNameDiv = document.getElementById('logoFileNameEdit');
    const uploadArea = document.getElementById('uploadAreaLogoEdit');
    const previewBox = document.getElementById('logoPreviewEdit');
    const previewImg = document.getElementById('logoPreviewImgEdit');
    const previewInfo = document.getElementById('logoPreviewInfoEdit');
    
    input.value = '';
    previewBox.classList.add('hidden');
    previewImg.src = '';
    previewInfo.textContent = '';
    
    fileNameDiv.textContent = 'No new file selected';
    fileNameDiv.style.color = 'var(--text-secondary)';
    fileNameDiv.style.fontWeight = 'normal';
    
    uploadArea.style.borderColor = 'var(--dstv-gold)';
    uploadArea.style.background = 'rgba(255, 184, 28, 0.05)';
}
</script>
@endpush

@endsection