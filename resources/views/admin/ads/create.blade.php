@extends('layouts.admin')

@section('page_title', 'Add Advertisement')
@section('page_subtitle', 'Create new banner or popup ad')

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

<form action="{{ route('admin.ads.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="dstv-card">
        <div class="dstv-card-header">
            <h3 class="dstv-card-title"><i class="fas fa-ad"></i> Ad Details</h3>
        </div>
        <div class="dstv-card-body" style="padding: 24px;">
            <div class="dstv-form-group">
                <label class="dstv-form-label">Ad Title <span style="color: var(--danger);">*</span></label>
                <input type="text" name="title" class="dstv-form-input" value="{{ old('title') }}" required placeholder="e.g., Summer Sale Banner">
            </div>

            <div class="dstv-form-group">
                <label class="dstv-form-label">Ad Image <span style="color: var(--danger);">*</span></label>
                <div style="position: relative;">
                    <input type="file" name="image_file" id="adImage" accept="image/*" onchange="previewAdImage()" style="position: absolute; inset: 0; opacity: 0; cursor: pointer;" required>
                    <div style="border: 3px dashed var(--dstv-gold); border-radius: 12px; padding: 40px 20px; text-align: center; background: rgba(255, 184, 28, 0.05); transition: all 0.25s ease;">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 32px; color: var(--dstv-gold-dark); margin-bottom: 8px; display: block;"></i>
                        <span style="color: var(--dstv-gold-dark); font-weight: 500;">Click to upload ad image</span>
                    </div>
                </div>
                <p style="color: var(--text-light); font-size: 12px; margin-top: 8px;">Recommended: 1920x540px for banner, 400x400px for popup</p>
                <div id="adPreview" class="hidden" style="margin-top: 16px; position: relative;">
                    <img id="adPreviewImg" src="" alt="Ad preview" style="width: 100%; height: 200px; object-fit: contain; border-radius: 12px; border: 2px solid var(--dstv-gold); background: #f5f5f5;">
                    <button type="button" onclick="removeAdPreview()" style="position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; background: rgba(239, 68, 68, 0.9); border: none; border-radius: 50%; color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px;"><i class="fas fa-times"></i></button>
                </div>
            </div>

            <div class="dstv-form-group">
                <label class="dstv-form-label">Target URL (optional)</label>
                <input type="url" name="target_url" class="dstv-form-input" value="{{ old('target_url') }}" placeholder="https://example.com">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="dstv-form-group">
                    <label class="dstv-form-label">Ad Type</label>
                    <select name="ad_type" id="ad_type" class="dstv-form-input" onchange="togglePositions()">
                        <option value="banner" {{ old('ad_type', 'banner') == 'banner' ? 'selected' : '' }}>Banner</option>
                        <option value="popup" {{ old('ad_type') == 'popup' ? 'selected' : '' }}>Popup</option>
                    </select>
                </div>

                <div class="dstv-form-group">
                    <label class="dstv-form-label">Position</label>
                    <select name="position" id="position" class="dstv-form-input">
                        <option value="home_bottom" {{ old('position', 'home_bottom') == 'home_bottom' ? 'selected' : '' }}>Home - Bottom</option>
                        <option value="player_bottom" {{ old('position') == 'player_bottom' ? 'selected' : '' }}>Player - Bottom</option>
                        <option value="documentary_bottom" {{ old('position') == 'documentary_bottom' ? 'selected' : '' }}>Documentary - Bottom</option>
                        <option value="home_popup" {{ old('position') == 'home_popup' ? 'selected' : '' }}>Home - Popup</option>
                        <option value="player_popup" {{ old('position') == 'player_popup' ? 'selected' : '' }}>Player - Popup</option>
                        <option value="splash_popup" {{ old('position') == 'splash_popup' ? 'selected' : '' }}>Splash Screen</option>
                    </select>
                </div>
            </div>

            <div class="dstv-form-group">
                <label class="dstv-form-label">Status</label>
                <div style="margin-top: 8px;">
                    <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                        <input type="hidden" name="status" value="0">
                        <input type="checkbox" name="status" id="adStatus" value="1" {{ old('status', '1') == '1' ? 'checked' : '' }} style="display: none;">
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
            <i class="fas fa-save"></i> Save Ad
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

function previewAdImage() {
    const input = document.getElementById('adImage');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('adPreviewImg').src = e.target.result;
            document.getElementById('adPreview').classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeAdPreview() {
    document.getElementById('adPreview').classList.add('hidden');
    document.getElementById('adPreviewImg').src = '';
    document.getElementById('adImage').value = '';
}

// Run on page load
document.addEventListener('DOMContentLoaded', togglePositions);
</script>
@endpush

@endsection