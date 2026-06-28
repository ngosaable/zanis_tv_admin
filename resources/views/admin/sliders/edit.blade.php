@extends('layouts.admin')

@section('page_title', 'Edit Slider')
@section('page_subtitle', 'Update slider banner')

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

<form method="POST" action="{{ route('admin.sliders.update', $slider) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="dstv-card">
        <div class="dstv-card-header" style="background: var(--dstv-gold-dark);">
            <h3 class="dstv-card-title" style="color: white;">
                <i class="fas fa-images"></i> Slider Details
            </h3>
        </div>
        <div class="dstv-card-body" style="padding: 24px;">
            <div class="dstv-form-group">
                <label class="dstv-form-label">Current Image</label>
                @if($slider->image)
                    <img src="{{ asset('storage/'.$slider->image) }}" style="height: 150px; border-radius: 10px; margin-bottom: 12px; display: block; border: 2px solid var(--dstv-gold);">
                @else
                    <p style="color: var(--text-secondary); margin-bottom: 12px;">No image uploaded.</p>
                @endif
            </div>

            <div class="dstv-form-group">
                <label class="dstv-form-label">Replace Image</label>
                <div style="position: relative;">
                    <input type="file" name="image" id="sliderImageEdit" accept="image/*" onchange="previewSliderImageEdit()" style="position: absolute; inset: 0; opacity: 0; cursor: pointer;">
                    <div style="border: 3px dashed var(--dstv-gold); border-radius: 12px; padding: 30px 20px; text-align: center; background: rgba(255, 184, 28, 0.05);">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 28px; color: var(--dstv-gold-dark); margin-bottom: 8px; display: block;"></i>
                        <span style="color: var(--dstv-gold-dark); font-weight: 500;">Click to upload new image</span>
                    </div>
                </div>
                <div id="sliderPreviewEdit" class="hidden" style="margin-top: 16px; position: relative;">
                    <img id="sliderPreviewImgEdit" src="" alt="New image preview" style="width: 100%; height: 200px; object-fit: cover; border-radius: 12px; border: 2px solid var(--dstv-gold);">
                    <button type="button" onclick="removeSliderPreviewEdit()" style="position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; background: rgba(239, 68, 68, 0.9); border: none; border-radius: 50%; color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px;"><i class="fas fa-times"></i></button>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="dstv-form-group">
                    <label class="dstv-form-label">Title <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="title" class="dstv-form-input" value="{{ old('title', $slider->title) }}" required>
                </div>

                <div class="dstv-form-group">
                    <label class="dstv-form-label">Order</label>
                    <input type="number" name="order" class="dstv-form-input" value="{{ old('order', $slider->sort_order ?? 1) }}" min="1">
                </div>
            </div>

            <div class="dstv-form-group">
                <label class="dstv-form-label">Linked Type</label>
                <select name="linked_type" class="dstv-form-input">
                    <option value="movie" {{ $slider->linked_type=='movie'?'selected':'' }}>Movie</option>
                    <option value="live_channel" {{ $slider->linked_type=='live_channel'?'selected':'' }}>Live Channel</option>
                    <option value="series" {{ $slider->linked_type=='series'?'selected':'' }}>Series</option>
                    <option value="none" {{ $slider->linked_type=='none'?'selected':'' }}>None</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="dstv-form-group">
                    <label class="dstv-form-label">Linked ID</label>
                    <input type="number" name="linked_id" class="dstv-form-input" value="{{ old('linked_id', $slider->linked_id) }}">
                </div>

                <div class="dstv-form-group">
                    <label class="dstv-form-label">Status</label>
                    <div style="margin-top: 8px;">
                        <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                            <input type="hidden" name="status" value="0">
                            <input type="checkbox" name="status" id="sliderStatus" value="1" {{ $slider->status ? 'checked' : '' }} style="display: none;">
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
        <a href="{{ route('admin.sliders.index') }}" class="dstv-btn dstv-btn-outline">Cancel</a>
        <button type="submit" class="dstv-btn dstv-btn-gold">
            <i class="fas fa-save"></i> Update Slider
        </button>
    </div>
</form>

@push('scripts')
<script>
function previewSliderImageEdit() {
    const input = document.getElementById('sliderImageEdit');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('sliderPreviewImgEdit').src = e.target.result;
            document.getElementById('sliderPreviewEdit').classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeSliderPreviewEdit() {
    document.getElementById('sliderPreviewEdit').classList.add('hidden');
    document.getElementById('sliderPreviewImgEdit').src = '';
    document.getElementById('sliderImageEdit').value = '';
}
</script>
@endpush

@endsection