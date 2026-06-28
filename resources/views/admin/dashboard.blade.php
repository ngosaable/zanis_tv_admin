@extends('layouts.admin')

@section('page_title', 'Dashboard')
@section('page_subtitle', 'Welcome to Zanis TV Admin Panel')

@section('page_actions')
    <a href="{{ route('admin.videos.create') }}" class="dstv-btn dstv-btn-gold">
        <i class="fas fa-plus"></i> Upload Video
    </a>
@endsection

@section('content')

<!-- Stats Cards Row -->
<div class="dstv-stats-grid">
    <div class="dstv-stat-card" style="border-left: 4px solid var(--video-color);">
        <div class="dstv-stat-icon" style="background: var(--video-light); color: var(--video-color);">
            <i class="fas fa-film"></i>
        </div>
        <div>
            <div class="dstv-stat-value">{{ $totalVideos }}</div>
            <div class="dstv-stat-label">Total Videos</div>
        </div>
    </div>

    <div class="dstv-stat-card" style="border-left: 4px solid var(--live-color);">
        <div class="dstv-stat-icon" style="background: var(--live-light); color: var(--live-color);">
            <i class="fas fa-satellite-dish"></i>
        </div>
        <div>
            <div class="dstv-stat-value">{{ $totalLiveChannels }}</div>
            <div class="dstv-stat-label">Live TV Channels</div>
        </div>
    </div>

    <div class="dstv-stat-card" style="border-left: 4px solid var(--category-color);">
        <div class="dstv-stat-icon" style="background: var(--category-light); color: var(--category-color);">
            <i class="fas fa-folder"></i>
        </div>
        <div>
            <div class="dstv-stat-value">{{ $totalCategories }}</div>
            <div class="dstv-stat-label">Categories</div>
        </div>
    </div>

    <div class="dstv-stat-card" style="border-left: 4px solid var(--ad-color);">
        <div class="dstv-stat-icon" style="background: var(--ad-light); color: var(--ad-color);">
            <i class="fas fa-ad"></i>
        </div>
        <div>
            <div class="dstv-stat-value">{{ $activeAds }}</div>
            <div class="dstv-stat-label">Active Ads</div>
        </div>
    </div>

    <div class="dstv-stat-card" style="border-left: 4px solid var(--slider-color);">
        <div class="dstv-stat-icon" style="background: var(--slider-light); color: var(--slider-color);">
            <i class="fas fa-images"></i>
        </div>
        <div>
            <div class="dstv-stat-value">{{ $totalSliders }}</div>
            <div class="dstv-stat-label">Sliders</div>
        </div>
    </div>

    <div class="dstv-stat-card" style="border-left: 4px solid var(--ad-color);">
        <div class="dstv-stat-icon" style="background: var(--ad-light); color: var(--ad-color);">
            <i class="fas fa-bullhorn"></i>
        </div>
        <div>
            <div class="dstv-stat-value">{{ $totalAds }}</div>
            <div class="dstv-stat-label">Total Ads</div>
        </div>
    </div>

    <div class="dstv-stat-card" style="border-left: 4px solid var(--info);">
        <div class="dstv-stat-icon cyan">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <div class="dstv-stat-value">--</div>
            <div class="dstv-stat-label">Total Users</div>
        </div>
    </div>

    <div class="dstv-stat-card" style="border-left: 4px solid var(--purple, #8B5CF6);">
        <div class="dstv-stat-icon purple">
            <i class="fas fa-eye"></i>
        </div>
        <div>
            <div class="dstv-stat-value">--</div>
            <div class="dstv-stat-label">Total Views</div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="dstv-dashboard-main">
    <!-- Left Column -->
    <div class="dstv-dashboard-left">
        <!-- Recently Added Videos -->
        <div class="dstv-card">
            <div class="dstv-card-header">
                <h3 class="dstv-card-title"><i class="fas fa-clock"></i> Recently Added</h3>
                <a href="{{ route('admin.videos.index') }}" class="dstv-view-all">View All <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="dstv-card-body">
                @forelse($recentVideos->take(5) as $video)
                    <a href="{{ route('admin.videos.show', $video) }}" class="dstv-activity-item">
                        @if($video->poster && $video->poster_full_url)
                            <img src="{{ $video->poster_full_url }}" alt="{{ $video->title }}" class="dstv-activity-thumb">
                        @else
                            <div class="dstv-activity-thumb-placeholder"><i class="fas fa-film"></i></div>
                        @endif
                        <div class="dstv-activity-info">
                            <div class="dstv-activity-title">{{ $video->title }}</div>
                            <div class="dstv-activity-meta">{{ $video->category->name ?? 'Uncategorized' }} • {{ $video->created_at->diffForHumans() }}</div>
                        </div>
                        <span class="dstv-badge dstv-badge-{{ $video->status ? 'success' : 'secondary' }}">
                            {{ $video->status ? 'Active' : 'Off' }}
                        </span>
                    </a>
                @empty
                    <div class="dstv-empty">
                        <i class="fas fa-inbox"></i>
                        <p>No recent videos added</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Live TV Channels -->
        <div class="dstv-card">
            <div class="dstv-card-header">
                <h3 class="dstv-card-title"><i class="fas fa-broadcast-tower"></i> Live TV Channels</h3>
                <a href="{{ route('admin.live-channels.index') }}" class="dstv-view-all">View All <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="dstv-card-body">
                @forelse($recentChannels as $channel)
                    <div class="dstv-channel-item">
                        <div class="dstv-channel-icon"><i class="fas fa-satellite-dish"></i></div>
                        <div class="dstv-activity-info">
                            <div class="dstv-channel-name">{{ $channel->name }}</div>
                            <div class="dstv-activity-meta">
                                <span class="dstv-live-badge"><i class="fas fa-circle"></i> {{ $channel->is_live ? 'Live Now' : 'Offline' }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="dstv-empty">
                        <i class="fas fa-satellite-dish"></i>
                        <p>No live channels configured</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Ads Overview -->
        <div class="dstv-card" style="border-top: 3px solid var(--ad-color);">
            <div class="dstv-card-header" style="background: var(--ad-light);">
                <h3 class="dstv-card-title" style="color: var(--ad-color);"><i class="fas fa-ad"></i> Ads Overview</h3>
                <a href="{{ route('admin.ads.index') }}" class="dstv-view-all" style="color: var(--ad-color);">View All <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="dstv-card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div style="text-align: center; padding: 16px; background: var(--ad-light); border-radius: 10px;">
                        <div style="font-size: 28px; font-weight: 700; color: var(--ad-color);">{{ $activeAds }}</div>
                        <div style="font-size: 12px; color: var(--text-secondary);">Active Ads</div>
                    </div>
                    <div style="text-align: center; padding: 16px; background: var(--bg-primary); border-radius: 10px;">
                        <div style="font-size: 28px; font-weight: 700; color: var(--text-secondary);">{{ $totalAds - $activeAds }}</div>
                        <div style="font-size: 12px; color: var(--text-secondary);">Inactive</div>
                    </div>
                </div>
                
                @forelse($recentAds as $ad)
                    <a href="{{ route('admin.ads.show', $ad) }}" class="dstv-activity-item">
                        @if($ad->image)
                            <img src="{{ asset('storage/'.$ad->image) }}" alt="{{ $ad->title }}" class="dstv-activity-thumb">
                        @else
                            <div class="dstv-activity-thumb-placeholder"><i class="fas fa-ad"></i></div>
                        @endif
                        <div class="dstv-activity-info">
                            <div class="dstv-activity-title">{{ $ad->title }}</div>
                            <div class="dstv-activity-meta">{{ $ad->position ?? 'General' }} • {{ $ad->created_at->diffForHumans() }}</div>
                        </div>
                        <span class="dstv-badge dstv-badge-{{ $ad->status ? 'success' : 'secondary' }}">
                            {{ $ad->status ? 'Active' : 'Off' }}
                        </span>
                    </a>
                @empty
                    <div class="dstv-empty">
                        <i class="fas fa-ad"></i>
                        <p>No ads created yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="dstv-dashboard-right">
        <!-- Platform Statistics -->
        <div class="dstv-card">
            <div class="dstv-card-header">
                <h3 class="dstv-card-title"><i class="fas fa-chart-bar"></i> Platform Stats</h3>
            </div>
            <div class="dstv-card-body">
                <div class="dstv-platform-item">
                    <span class="dstv-platform-label"><i class="fas fa-database"></i> Storage Used</span>
                    <span class="dstv-platform-value">--</span>
                </div>
                <div class="dstv-platform-item">
                    <span class="dstv-platform-label"><i class="fas fa-eye"></i> Total Views</span>
                    <span class="dstv-platform-value">--</span>
                </div>
                <div class="dstv-platform-item">
                    <span class="dstv-platform-label"><i class="fas fa-video"></i> Videos Ready</span>
                    <span class="dstv-platform-value">{{ $totalVideos }}</span>
                </div>
                <div class="dstv-platform-item">
                    <span class="dstv-platform-label"><i class="fas fa-signal"></i> Live Channels</span>
                    <span class="dstv-platform-value">{{ $totalLiveChannels }}</span>
                </div>
            </div>
        </div>

        <!-- Content by Category -->
        <div class="dstv-card">
            <div class="dstv-card-header">
                <h3 class="dstv-card-title"><i class="fas fa-chart-pie"></i> Content by Category</h3>
            </div>
            <div class="dstv-card-body">
                <div class="dstv-category-list">
                    @forelse($chartVideosLabels ?? [] as $index => $label)
                        <div class="dstv-category-item">
                            <div class="dstv-category-name">{{ $label }}</div>
                            <div class="dstv-category-bar">
                                <div class="dstv-category-fill" style="width: {{ $chartVideosData[$index] ? ($chartVideosData[$index] / max($totalVideos, 1)) * 100 : 0 }}%"></div>
                            </div>
                            <div class="dstv-category-count">{{ $chartVideosData[$index] ?? 0 }}</div>
                        </div>
                    @empty
                        <div class="dstv-empty" style="padding: 20px;">
                            <i class="fas fa-chart-pie"></i>
                            <p>No category data available</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="dstv-card">
            <div class="dstv-card-header">
                <h3 class="dstv-card-title"><i class="fas fa-bolt"></i> Quick Actions</h3>
            </div>
            <div class="dstv-card-body">
                <div class="dstv-quick-grid">
                    <a href="{{ route('admin.videos.create') }}" class="dstv-quick-btn">
                        <i class="fas fa-upload"></i>
                        <span>Upload Video</span>
                    </a>
                    <a href="{{ route('admin.live-channels.create') }}" class="dstv-quick-btn">
                        <i class="fas fa-satellite-dish"></i>
                        <span>Add Live TV</span>
                    </a>
                    <a href="{{ route('admin.categories.create') }}" class="dstv-quick-btn">
                        <i class="fas fa-folder-plus"></i>
                        <span>New Category</span>
                    </a>
                    <a href="{{ route('admin.sliders.create') }}" class="dstv-quick-btn">
                        <i class="fas fa-image"></i>
                        <span>Add Slider</span>
                    </a>
                    <a href="{{ route('admin.ads.create') }}" class="dstv-quick-btn">
                        <i class="fas fa-ad"></i>
                        <span>Create Ad</span>
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="dstv-quick-btn" onclick="location.reload()">
                        <i class="fas fa-sync-alt"></i>
                        <span>Refresh</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection