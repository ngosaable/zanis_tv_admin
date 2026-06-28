@extends('layouts.admin')

@section('page_title', 'Edit Ad')
@section('page_subtitle', 'Update advertisement')

@section('page_actions')
    <a href="{{ route('admin.ads.index') }}" class="dstv-btn dstv-btn-outline">
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

<form action="{{ route('admin.ads.update', $ad) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="dstv-card">
        <div class="dstv-card-header">
            <h3 class="dstv-card-title"><i class="fas fa-ad"></i> Ad Details</h3>
        </div>
        <div class="dstv-card-body" style="padding: 24px;">
            <div class="dstv-form-group">
                <label class="dstv-form-label">Current Image</label>
                @if($ad->image)
                    <img src="{{ asset('storage/'.$ad->image) }}" style="height: 150px; border-radius: 10px; margin-bottom: 12px; display: block; border: 2px solid var(--dstv-gold);">
                @else
                    <p style="color: var(--text-secondary); margin-bottom: 12px;">No image uploaded.</p>
                @endif
            </div>

            <div class="dstv-form-group">
                <label class="dstv-form-label">Replace Image</label>
                <div style="position: relative;">
                    <input type="file" name="image_file" id="adImageEdit" accept="image/*" onchange="previewAdImageEdit()" style="position: absolute; inset: 0; opacity: 0; cursor: pointer;">
                    <div style="border: 3px dashed var(--dstv-gold); border-radius: 12px; padding: 30px 20px; text-align: center; background: rgba(255, 184, 28, 0.05);">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 28px; color: var(--dstv-gold-dark); margin-bottom: 8px; display: block;"></i>
                        <span style="color: var(--dstv-gold-dark); font-weight: 500;">Click to upload new image</span>
                    </div>
                </div>
                <div id="adPreviewEdit" class="hidden" style="margin-top: 16px; position: relative;">
                    <img id="adPreviewImgEdit" src="" alt="New ad preview" style="width: 100%; height: 200px; object-fit: contain; border-radius: 12px; border: 2px solid var(--dstv-gold); background: #f5f5f5;">
                    <button type="button" onclick="removeAdPreviewEdit()" style="position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; background: rgba(239, 68, 68, 0.9); border: none; border-radius: 50%; color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px;"><i class="fas fa-times"></i></button>
                </div>
            </div>

            <div class="dstv-form-group">
                <label class="dstv-form-label">Title <span style="color: var(--danger);">*</span></label>
                <input type="text" name="title" class="dstv-form-input" value="{{ old('title', $ad->title) }}" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="dstv-form-group">
                    <label class="dstv-form-label">Ad Type</label>
                    <select name="ad_type" id="ad_type" class="dstv-form-input" onchange="togglePositions()">
                        <option value="banner" {{ old('ad_type', $ad->ad_type) == 'banner' ? 'selected' : '' }}>Banner</option>
                        <option value="popup" {{ old('ad_type', $ad->ad_type) == 'popup' ? 'selected' : '' }}>Popup</option>
                    </select>
                </div>

                <div class="dstv-form-group">
                    <label class="dstv-form-label">Position</label>
                    <select name="position" id="position" class="dstv-form-input">
                        <option value="home_bottom" {{ old('position', $ad->position) == 'home_bottom' ? 'selected' : '' }}>Home - Bottom</option>
                        <option value="player_bottom" {{ old('position', $ad->position) == 'player_bottom' ? 'selected' : '' }}>Player - Bottom</option>
                        <option value="documentary_bottom" {{ old('position', $ad->position) == 'documentary_bottom' ? 'selected' : '' }}>Documentary - Bottom</option>
                        <option value="home_popup" {{ old('position', $ad->position) == 'home_popup' ? 'selected' : '' }}>Home - Popup</option>
                        <option value="player_popup" {{ old('position', $ad->position) == 'player_popup' ? 'selected' : '' }}>Player - Popup</option>
                        <option value="splash_popup" {{ old('position', $ad->position) == 'splash_popup' ? 'selected' : '' }}>Splash Screen</option>
                    </select>
                </div>
            </div>

            <div class="dstv-form-group">
                <label class="dstv-form-label">Target URL</label>
                <input type="text" name="target_url" class="dstv-form-input" value="{{ old('target_url', $ad->target_url) }}" placeholder="https://...">
            </div>

            <div class="dstv-form-group">
                <label class="dstv-form-label">Status</label>
                <div style="margin-top: 8px;">
                    <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                        <input type="hidden" name="status" value="0">
                        <input type="checkbox" name="status" id="adStatus" value="1" {{ old('status', $ad->status) ? 'checked' : '' }} style="display: none;">
                        <div class="dstv-toggle-slider">
                            <div class="dstv-toggle-knob"></div>
                        </div>
                        <span style="font-weight: 500; color: var(--text-primary);">Active</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 20px;">
        <a href="{{ route('admin.ads.index') }}" class="dstv-btn dstv-btn-outline">Cancel</a>
        <button type="submit" class="dstv-btn dstv-btn-gold">
            <i class="fas fa-save"></i> Update Ad
        </button>
    </div>
</form>

@push('scripts')
<script>
function togglePositions() {
    const adType = document.getElementById('ad_type').value;
    const positionSelect = document.getElementById('position');
    const options = positionSelect.options;
    
    for (let i = 0; i < options.length; i++) {
        if (adType === 'popup') {
            options[i].style.display = options[i].value.includes('popup') ? 'block' : 'none';
        } else {
            options[i].style.display = !options[i].value.includes('popup') ? 'block' : 'none';
        }
    }
}

function previewAdImageEdit() {
    const input = document.getElementById('adImageEdit');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('adPreviewImgEdit').src = e.target.result;
            document.getElementById('adPreviewEdit').classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeAdPreviewEdit() {
    document.getElementById('adPreviewEdit').classList.add('hidden');
    document.getElementById('adPreviewImgEdit').src = '';
    document.getElementById('adImageEdit').value = '';
}

// Run on page load
document.addEventListener('DOMContentLoaded', togglePositions);
</script>
@endpush

@endsection

@section('content')

@if($errors->any())
    <div class="dt-alert dt-alert-error">
        <i class="fas fa-exclamation-circle"></i>
        Please fix the errors below
    </div>
@endif

<form action="{{ route('admin.ads.update', $ad) }}" method="POST" enctype="multipart/form-data" class="dt-form">
    @csrf
    @method('PUT')

    <div class="dt-card">
        <div class="dt-card-header">
            <h3 class="dt-card-title"><i class="fas fa-ad"></i> Ad Details</h3>
        </div>
        <div class="dt-card-body">
            <div class="dt-form-group">
                <label class="dt-form-label">Current Image</label>
                @if($ad->image)
                    <img src="{{ asset('storage/'.$ad->image) }}" style="height: 120px; border-radius: 10px;">
                @else
                    <p style="color: var(--dt-text-secondary);">No image uploaded.</p>
                @endif
            </div>

            <div class="dt-form-group">
                <label class="dt-form-label">Replace Image</label>
                <input type="file" name="image_file" class="dt-form-input" accept="image/*">
            </div>

            <div class="dt-form-group">
                <label class="dt-form-label">Title <span class="dt-required">*</span></label>
                <input type="text" name="title" class="dt-form-input" value="{{ old('title', $ad->title) }}" required>
            </div>

            <div class="dt-form-grid">
                <div class="dt-form-group">
                    <label class="dt-form-label">Ad Type</label>
                    <select name="ad_type" class="dt-form-input">
                        <option value="banner" {{ old('ad_type', $ad->ad_type) == 'banner' ? 'selected' : '' }}>Banner</option>
                        <option value="popup" {{ old('ad_type', $ad->ad_type) == 'popup' ? 'selected' : '' }}>Popup</option>
                    </select>
                </div>

                <div class="dt-form-group">
                    <label class="dt-form-label">Position</label>
                    <select name="position" class="dt-form-input">
                        <optgroup label="Home">
                            <option value="home_top" {{ old('position', $ad->position) == 'home_top' ? 'selected' : '' }}>Home - Top</option>
                            <option value="home_middle" {{ old('position', $ad->position) == 'home_middle' ? 'selected' : '' }}>Home - Middle</option>
                            <option value="home_bottom" {{ old('position', $ad->position) == 'home_bottom' ? 'selected' : '' }}>Home - Bottom</option>
                            <option value="home_popup" {{ old('position', $ad->position) == 'home_popup' ? 'selected' : '' }}>Home - Popup</option>
                        </optgroup>
                        <optgroup label="Player">
                            <option value="player_bottom" {{ old('position', $ad->position) == 'player_bottom' ? 'selected' : '' }}>Player - Bottom</option>
                            <option value="player_popup" {{ old('position', $ad->position) == 'player_popup' ? 'selected' : '' }}>Player - Popup</option>
                        </optgroup>
                    </select>
                </div>
            </div>

            <div class="dt-form-group">
                <label class="dt-form-label">Target URL</label>
                <input type="text" name="target_url" class="dt-form-input" value="{{ old('target_url', $ad->target_url) }}" placeholder="https://...">
            </div>

            <div class="dt-form-group">
                <label class="dt-form-label">Status</label>
                <select name="status" class="dt-form-input">
                    <option value="1" {{ old('status', $ad->status ? '1' : '0') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', $ad->status ? '1' : '0') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <div class="dt-form-actions">
        <a href="{{ route('admin.ads.index') }}" class="dt-btn dt-btn-outline">Cancel</a>
        <button type="submit" class="dt-btn dt-btn-primary">
            <i class="fas fa-save"></i> Update Ad
        </button>
    </div>
</form>

@endsection