@extends('layouts.admin')

@section('page_title', 'Add Slider')
@section('page_subtitle', 'Create new slider banner')

@section('page_actions')
    <a href="{{ route('admin.sliders.index') }}" class="dstv-btn dstv-btn-outline">
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

<form method="POST" action="{{ route('admin.sliders.store') }}" enctype="multipart/form-data" onsubmit="return validateSliderForm()">
    @csrf

    <div class="dstv-card">
        <div class="dstv-card-header" style="background: var(--dstv-blue);">
            <h3 class="dstv-card-title" style="color: white;">
                <i class="fas fa-images"></i> Slider Details
            </h3>
        </div>
        <div class="dstv-card-body" style="padding: 24px;">
            <div class="dstv-form-group">
                <label class="dstv-form-label">Title <span style="color: var(--danger);">*</span></label>
                <input type="text" name="title" class="dstv-form-input" value="{{ old('title') }}" required placeholder="Enter slider title">
            </div>

            <div class="dstv-form-group">
                <label class="dstv-form-label">Subtitle</label>
                <input type="text" name="subtitle" class="dstv-form-input" value="{{ old('subtitle') }}" placeholder="Enter subtitle">
            </div>

<div class="dstv-form-group">
                <label class="dstv-form-label">Slider Image <span style="color: var(--danger);">*</span></label>
                <div style="position: relative;">
                    <input type="file" name="image" id="sliderImage" accept="image/*" onchange="previewSliderImage()" style="position: absolute; inset: 0; opacity: 0; cursor: pointer;">
                    <div id="uploadArea" style="border: 3px dashed var(--dstv-blue); border-radius: 12px; padding: 40px 20px; text-align: center; background: rgba(0, 58, 112, 0.05); transition: all 0.25s ease;">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 32px; color: var(--dstv-blue); margin-bottom: 8px; display: block;"></i>
                        <span style="color: var(--dstv-blue); font-weight: 500;">Click to upload slider image</span>
                    </div>
                </div>
                <div id="sliderFileName" style="color: var(--text-secondary); font-size: 13px; font-weight: 500; margin-top: 8px;">No file selected</div>
                <p style="color: var(--text-light); font-size: 12px; margin-top: 8px;">Recommended size: 1920x540px (16:9 ratio)</p>
                
                <!-- Preview Box -->
                <div id="sliderPreview" class="hidden" style="margin-top: 16px; border-radius: 12px; overflow: hidden; border: 3px solid var(--dstv-blue); background: linear-gradient(135deg, var(--dstv-blue) 0%, var(--dstv-blue-light) 100%); padding: 16px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: white; font-weight: 600; font-size: 14px;">Selected Image Preview</span>
                        <button type="button" onclick="removeSliderPreview()" style="width: 28px; height: 28px; background: rgba(255,255,255,0.2); border: none; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px;"><i class="fas fa-times"></i></button>
                    </div>
                    <img id="sliderPreviewImg" src="" alt="Slider preview" style="width: 100%; height: 200px; object-fit: contain; border-radius: 8px; background: white;">
                    <div id="sliderPreviewInfo" style="margin-top: 8px; color: rgba(255,255,255,0.8); font-size: 12px; text-align: center;"></div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="dstv-form-group">
                    <label class="dstv-form-label">Order</label>
                    <input type="number" name="order" class="dstv-form-input" value="{{ old('order', 1) }}" min="1">
                </div>

                <div class="dstv-form-group">
                    <label class="dstv-form-label">Linked Type</label>
                    <select name="linked_type" class="dstv-form-input">
                        <option value="movie">Movie</option>
                        <option value="live_channel">Live Channel</option>
                        <option value="series">Series</option>
                        <option value="none">None</option>
                    </select>
                </div>
            </div>

            <div class="dstv-form-group">
                <label class="dstv-form-label">Linked ID</label>
                <input type="number" name="linked_id" class="dstv-form-input" value="{{ old('linked_id') }}" placeholder="Enter movie/channel ID">
            </div>

            <div class="dstv-form-group">
                <label class="dstv-form-label">Status</label>
                <div style="margin-top: 8px;">
                    <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                        <input type="hidden" name="status" value="0">
                        <input type="checkbox" name="status" id="sliderStatus" value="1" checked style="display: none;">
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
        <a href="{{ route('admin.sliders.index') }}" class="dstv-btn dstv-btn-outline">
            <i class="fas fa-times"></i> Cancel
        </a>
        <button type="submit" class="dstv-btn dstv-btn-primary">
            <i class="fas fa-save"></i> Save Slider
        </button>
    </div>
</form>

@push('scripts')
<script>
function validateSliderForm() {
    const imageInput = document.getElementById('sliderImage');
    const fileNameDiv = document.getElementById('sliderFileName');
    
    if (!imageInput.files || !imageInput.files[0]) {
        alert('Please select a slider image before submitting!');
        fileNameDiv.textContent = '⚠️ Please select an image file!';
        fileNameDiv.style.color = 'var(--danger)';
        fileNameDiv.style.fontWeight = '600';
        return false;
    }
    return true;
}

function previewSliderImage() {
    const input = document.getElementById('sliderImage');
    const fileNameDiv = document.getElementById('sliderFileName');
    const uploadArea = document.getElementById('uploadArea');
    const previewBox = document.getElementById('sliderPreview');
    const previewImg = document.getElementById('sliderPreviewImg');
    const previewInfo = document.getElementById('sliderPreviewInfo');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Update filename display
        fileNameDiv.textContent = file.name;
        fileNameDiv.style.color = 'var(--dstv-blue)';
        fileNameDiv.style.fontWeight = '600';
        
        // Show preview image
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewBox.classList.remove('hidden');
            
            // Display file info
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            previewInfo.textContent = `📁 ${file.name} | Size: ${fileSize} MB | Type: ${file.type}`;
        };
        reader.readAsDataURL(file);
        
        // Update upload area style to show it's been used
        uploadArea.style.borderColor = 'var(--dstv-gold)';
        uploadArea.style.background = 'rgba(255, 184, 28, 0.1)';
    } else {
        fileNameDiv.textContent = 'No file selected';
        fileNameDiv.style.color = 'var(--text-secondary)';
        fileNameDiv.style.fontWeight = 'normal';
    }
}

function removeSliderPreview() {
    const input = document.getElementById('sliderImage');
    const fileNameDiv = document.getElementById('sliderFileName');
    const uploadArea = document.getElementById('uploadArea');
    const previewBox = document.getElementById('sliderPreview');
    const previewImg = document.getElementById('sliderPreviewImg');
    const previewInfo = document.getElementById('sliderPreviewInfo');
    
    input.value = '';
    previewBox.classList.add('hidden');
    previewImg.src = '';
    previewInfo.textContent = '';
    
    // Reset filename and style
    fileNameDiv.textContent = 'No file selected';
    fileNameDiv.style.color = 'var(--text-secondary)';
    fileNameDiv.style.fontWeight = 'normal';
    
    // Reset upload area style
    uploadArea.style.borderColor = 'var(--dstv-blue)';
    uploadArea.style.background = 'rgba(0, 58, 112, 0.05)';
}
</script>
@endpush

@endsection