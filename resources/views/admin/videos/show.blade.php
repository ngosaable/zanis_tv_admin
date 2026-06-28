@extends('layouts.admin')

@section('page_title', 'Video Details')
@section('page_subtitle', $video->title)

@section('page_actions')
    <a href="{{ route('admin.videos.edit', $video) }}" class="dt-btn dt-btn-outline">
        <i class="fas fa-edit"></i> Edit
    </a>
    <a href="{{ route('admin.videos.index') }}" class="dt-btn dt-btn-outline">
        <i class="fas fa-arrow-left"></i> Back
    </a>
@endsection

@section('content')

<div id="processingModal" class="dt-modal-overlay hidden">
    <div class="dt-modal-content">
        <div class="dt-modal-header">
            <h3><i class="fas fa-cog fa-spin"></i> HLS Conversion</h3>
            <button class="dt-modal-close" onclick="closeProcessingModal()">&times;</button>
        </div>
        <div class="dt-modal-body">
            <div class="dt-progress-info">
                <span id="processingProgress" class="dt-progress-percent">0%</span>
                <span id="processingStatus" class="dt-progress-status">Starting...</span>
            </div>
            <div class="dt-progress-bar">
                <div id="processingBar" class="dt-progress-fill" style="width: 0%"></div>
            </div>
            <div id="processingDetails" class="dt-progress-details">
                Please wait while we convert your video...
            </div>
        </div>
    </div>
</div>

<div class="dt-video-grid">
    <div class="dt-video-main">
        <div class="dt-card">
            <div class="dt-card-header">
                <h3 class="dt-card-title"><i class="fas fa-play"></i> Video Preview</h3>
            </div>
            <div class="dt-card-body">
                @if($video->hls_conversion_status == 'completed' && $video->hls_playlist_path)
                    <div class="dt-video-player">
                        <video id="videoPlayer" controls playsinline>
                            <source src="{{ $video->hls_playlist_full_url }}" type="application/x-mpegURL">
                            Your browser does not support HLS playback.
                        </video>
                    </div>
                    <div class="dt-stream-url">
                        <label>HLS Stream URL:</label>
                        <input type="text" value="{{ $video->hls_playlist_full_url }}" readonly onclick="this.select()">
                        <button onclick="copyStreamUrl()"><i class="fas fa-copy"></i></button>
                    </div>
                @elseif($video->video_path)
                    <div class="dt-video-player">
                        <video controls playsinline>
                            <source src="{{ $video->video_full_url }}">
                            Your browser does not support video playback.
                        </video>
                    </div>
                    @if($video->is_external_url)
                        <div class="dt-stream-url">
                            <label>Source URL:</label>
                            <input type="text" value="{{ $video->video_full_url }}" readonly onclick="this.select()">
                            <button type="button" onclick="copyInputValue(this)"><i class="fas fa-copy"></i></button>
                        </div>
                    @endif
                @else
                    <div class="dt-no-video">
                        <i class="fas fa-video-slash"></i>
                        <p>No video file available</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="dt-card">
            <div class="dt-card-header">
                <h3 class="dt-card-title"><i class="fas fa-tasks"></i> Processing Status</h3>
            </div>
            <div class="dt-card-body">
                <div class="dt-status-list">
                    <div class="dt-status-item">
                        <div class="dt-status-info">
                            <i class="fas fa-{{ $video->processing_status == 'completed' ? 'check-circle' : ($video->processing_status == 'processing' ? 'spinner fa-spin' : 'clock') }}"></i>
                            <span>Processing</span>
                        </div>
                        <span class="dt-badge dt-badge-{{ $video->processing_status == 'completed' ? 'success' : ($video->processing_status == 'processing' ? 'warning' : 'secondary') }}">
                            {{ ucfirst($video->processing_status) }}
                        </span>
                    </div>
                    
                    <div class="dt-status-item">
                        <div class="dt-status-info">
                            <i class="fas fa-{{ $video->is_external_url ? 'link' : ($video->hls_conversion_status == 'completed' ? 'check-circle' : ($video->hls_conversion_status == 'processing' ? 'spinner fa-spin' : 'clock')) }}"></i>
                            <span>HLS Conversion</span>
                        </div>
                        <span class="dt-badge dt-badge-{{ $video->is_external_url ? 'secondary' : ($video->hls_conversion_status == 'completed' ? 'success' : ($video->hls_conversion_status == 'processing' ? 'warning' : ($video->hls_conversion_status == 'failed' ? 'error' : 'secondary'))) }}">
                            {{ $video->is_external_url ? 'Not required' : ucfirst($video->hls_conversion_status) }}
                        </span>
                    </div>

                    @if($video->processing_progress > 0 && $video->processing_status == 'processing')
                    <div class="dt-progress-item">
                        <label>Progress: {{ $video->processing_progress }}%</label>
                        <div class="dt-progress-bar">
                            <div class="dt-progress-fill" style="width: {{ $video->processing_progress }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>

                @if($video->is_external_url)
                <div class="dt-ready-badge" style="background: rgba(59, 130, 246, 0.08); border-color: rgba(59, 130, 246, 0.2); color: #60a5fa;">
                    <i class="fas fa-link"></i>
                    <span>External URL videos stream directly and do not need HLS conversion.</span>
                </div>
                @elseif($video->hls_conversion_status != 'completed' && $video->video_path)
                <form action="{{ route('admin.videos.convert-to-hls', $video) }}" method="POST" class="dt-convert-form">
                    @csrf
                    <div class="dt-form-group">
                        <label class="dt-form-label">Select Quality</label>
                        <div class="dt-quality-buttons">
                            <button type="button" class="dt-quality-btn" data-quality="360p" onclick="selectHlsQuality(this, '360p')">
                                <span>360p</span>
                                <small>Low</small>
                            </button>
                            <button type="button" class="dt-quality-btn dt-quality-btn-selected" data-quality="480p" onclick="selectHlsQuality(this, '480p')">
                                <span>480p</span>
                                <small>Standard</small>
                            </button>
                            <button type="button" class="dt-quality-btn" data-quality="720p" onclick="selectHlsQuality(this, '720p')">
                                <span>720p</span>
                                <small>HD</small>
                            </button>
                            <button type="button" class="dt-quality-btn" data-quality="1080p" onclick="selectHlsQuality(this, '1080p')">
                                <span>1080p</span>
                                <small>Full HD</small>
                            </button>
                            <button type="button" class="dt-quality-btn" data-quality="adaptive" onclick="selectHlsQuality(this, 'adaptive')">
                                <span>Adaptive</span>
                                <small>All qualities</small>
                            </button>
                        </div>
                        <input type="hidden" name="quality" id="quality_input" value="480p">
                    </div>
                    <button type="submit" class="dt-btn dt-btn-primary" onclick="showProcessingModal()">
                        <i class="fas fa-film"></i> Convert to HLS
                    </button>
                </form>
                @endif

                @if($video->hls_conversion_status == 'completed' && !$video->is_external_url)
                <div class="dt-ready-badge">
                    <i class="fas fa-check-circle"></i>
                    <span>Ready for streaming</span>
                </div>
                @if($video->hls_qualities)
                <div class="dt-qualities-list">
                    <label>Available Qualities:</label>
                    <div class="dt-quality-tags">
                        @foreach($video->hls_qualities as $q)
                        <span class="dt-quality-tag">{{ $q['quality'] ?? $q }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                @endif
            </div>
        </div>
    </div>

    <div class="dt-video-sidebar">
        <div class="dt-card">
            <div class="dt-card-header">
                <h3 class="dt-card-title"><i class="fas fa-info-circle"></i> Details</h3>
            </div>
            <div class="dt-card-body">
                <div class="dt-detail-list">
                    <div class="dt-detail-item">
                        <label>ID</label>
                        <span>#{{ $video->id }}</span>
                    </div>
                    <div class="dt-detail-item">
                        <label>Title</label>
                        <span>{{ $video->title }}</span>
                    </div>
                    <div class="dt-detail-item">
                        <label>Category</label>
                        <span>{{ $video->category->name ?? 'Uncategorized' }}</span>
                    </div>
                    <div class="dt-detail-item">
                        <label>Status</label>
                        <span class="dt-badge dt-badge-{{ $video->status ? 'success' : 'error' }}">
                            {{ $video->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="dt-detail-item">
                        <label>Source</label>
                        <span>{{ $video->is_external_url ? 'External URL' : 'Uploaded file' }}</span>
                    </div>
                    <div class="dt-detail-item">
                        <label>Created</label>
                        <span>{{ $video->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="dt-card">
            <div class="dt-card-header">
                <h3 class="dt-card-title"><i class="fas fa-cog"></i> Technical</h3>
            </div>
            <div class="dt-card-body">
                <div class="dt-detail-list">
                    <div class="dt-detail-item">
                        <label>Duration</label>
                        <span>{{ $video->formatted_duration ?? 'N/A' }}</span>
                    </div>
                    <div class="dt-detail-item">
                        <label>Resolution</label>
                        <span>{{ $video->resolution ?? 'N/A' }}</span>
                    </div>
                    <div class="dt-detail-item">
                        <label>File Size</label>
                        <span>{{ $video->formatted_file_size ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="dt-card">
            <div class="dt-card-header">
                <h3 class="dt-card-title"><i class="fas fa-tools"></i> Actions</h3>
            </div>
            <div class="dt-card-body">
                <form action="{{ route('admin.videos.update', $video) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ $video->status ? 0 : 1 }}">
                    <button type="submit" class="dt-btn dt-btn-outline w-full">
                        <i class="fas fa-{{ $video->status ? 'eye-slash' : 'eye' }}"></i>
                        {{ $video->status ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
                <form action="{{ route('admin.videos.destroy', $video) }}" method="POST" onsubmit="return confirm('Delete this video?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dt-btn dt-btn-danger w-full">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.dt-video-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 24px;
}

@media (max-width: 1024px) {
    .dt-video-grid {
        grid-template-columns: 1fr;
    }
}

.dt-video-player {
    background: #000;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 16px;
    max-width: 480px;
}

.dt-video-player video {
    width: 100%;
    max-height: 270px;
}

.dt-stream-url {
    display: flex;
    align-items: center;
    gap: 10px;
}

.dt-stream-url label {
    font-size: 13px;
    color: #a0a0b9;
    white-space: nowrap;
}

.dt-stream-url input {
    flex: 1;
    padding: 10px 14px;
    background: #0f0f23;
    border: 1px solid #2d2d5a;
    border-radius: 8px;
    color: #a0a0b9;
    font-size: 13px;
}

.dt-stream-url button {
    padding: 10px 14px;
    background: #6366f1;
    border: none;
    border-radius: 8px;
    color: #fff;
    cursor: pointer;
}

.dt-no-video {
    text-align: center;
    padding: 60px 20px;
    color: #6b7280;
}

.dt-no-video i {
    font-size: 48px;
    margin-bottom: 12px;
    display: block;
}

.dt-status-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 20px;
}

.dt-status-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    background: #0f0f23;
    border-radius: 10px;
}

.dt-status-info {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #fff;
}

.dt-status-info i {
    color: #6366f1;
}

.dt-badge-success { background: rgba(16, 185, 129, 0.15); color: #10b981; }
.dt-badge-warning { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
.dt-badge-error { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
.dt-badge-secondary { background: rgba(107, 114, 128, 0.15); color: #6b7280; }

.dt-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.dt-progress-item {
    margin-top: 12px;
}

.dt-progress-item label {
    color: #a0a0b9;
    font-size: 13px;
    margin-bottom: 8px;
    display: block;
}

.dt-convert-form {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.dt-quality-buttons {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
    gap: 10px;
}

.dt-quality-btn {
    border: 1px solid #2d2d5a;
    background: #0f0f23;
    color: #fff;
    border-radius: 12px;
    padding: 14px 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.dt-quality-btn small {
    color: #a0a0b9;
}

.dt-quality-btn:hover,
.dt-quality-btn-selected {
    border-color: #6366f1;
    background: rgba(99, 102, 241, 0.12);
    box-shadow: 0 0 0 1px rgba(99, 102, 241, 0.18);
}

.dt-ready-badge {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 18px;
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.3);
    border-radius: 10px;
    color: #10b981;
    font-weight: 600;
}

.dt-qualities-list {
    margin-top: 16px;
}

.dt-qualities-list label {
    color: #a0a0b9;
    font-size: 13px;
    display: block;
    margin-bottom: 10px;
}

.dt-quality-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.dt-quality-tag {
    padding: 6px 12px;
    background: rgba(99, 102, 241, 0.15);
    border-radius: 6px;
    color: #818cf8;
    font-size: 13px;
    font-weight: 500;
}

.dt-detail-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.dt-detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dt-detail-item label {
    color: #a0a0b9;
    font-size: 13px;
}

.dt-detail-item span {
    color: #fff;
    font-weight: 500;
    font-size: 14px;
}

.dt-video-sidebar .dt-card {
    margin-bottom: 20px;
}

.dt-btn-danger {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid #ef4444;
    color: #ef4444;
    margin-top: 12px;
}

.dt-btn-danger:hover {
    background: #ef4444;
    color: #fff;
}
</style>
@endpush

@push('scripts')
<script>
function showProcessingModal() {
    const modal = document.getElementById('processingModal');
    const progressBar = document.getElementById('processingBar');
    const percentText = document.getElementById('processingProgress');
    const statusText = document.getElementById('processingStatus');
    const detailsText = document.getElementById('processingDetails');
    
    modal.classList.remove('hidden');
    
    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 20;
        if (progress > 95) progress = 95;
        
        progressBar.style.width = progress + '%';
        percentText.textContent = Math.round(progress) + '%';
        
        if (progress < 25) {
            statusText.textContent = 'Analyzing video...';
            detailsText.textContent = 'Extracting metadata...';
        } else if (progress < 50) {
            statusText.textContent = 'Converting...';
            detailsText.textContent = 'Creating HLS segments...';
        } else if (progress < 75) {
            statusText.textContent = 'Encoding...';
            detailsText.textContent = 'Generating quality variants...';
        } else {
            statusText.textContent = 'Finalizing...';
            detailsText.textContent = 'Creating playlist...';
        }
    }, 1500);
    
    setTimeout(() => {
        clearInterval(interval);
        window.location.reload();
    }, 12000);
}

function closeProcessingModal() {
    document.getElementById('processingModal').classList.add('hidden');
}

function copyStreamUrl() {
    const input = document.querySelector('.dt-stream-url input');
    if (!input) {
        return;
    }

    input.select();
    document.execCommand('copy');
    alert('Stream URL copied!');
}

function copyInputValue(button) {
    const input = button.closest('.dt-stream-url').querySelector('input');
    if (!input) {
        return;
    }

    input.select();
    document.execCommand('copy');
    alert('URL copied!');
}

function selectHlsQuality(button, quality) {
    document.querySelectorAll('.dt-quality-btn').forEach((item) => {
        item.classList.remove('dt-quality-btn-selected');
    });

    button.classList.add('dt-quality-btn-selected');
    document.getElementById('quality_input').value = quality;
}

@if($video->processing_status == 'processing' || $video->hls_conversion_status == 'processing')
setInterval(() => {
    fetch('{{ route("admin.videos.processing-status", $video) }}')
        .then(res => res.json())
        .then(data => {
            if (data.processing_status === 'completed') {
                window.location.reload();
            }
        });
}, 15000);
@endif
</script>
@endpush
