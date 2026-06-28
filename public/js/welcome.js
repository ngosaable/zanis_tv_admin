(function () {
    const pageUrl = new URL(window.location.href);
    const API_BASE = new URL('api/', pageUrl).href.replace(/\/$/, '');
    const STORAGE_BASE = new URL('storage/', pageUrl).href;

    const kidsChannels = [
        { name: 'Zanis Kids', emoji: '🦁', tagline: 'Fun & Learning', color: '#7c3aed' },
        { name: 'Cartoon World', emoji: '🎨', tagline: 'Animated Adventures', color: '#ec4899' },
        { name: 'Junior Sports', emoji: '⚽', tagline: 'Kids Athletics', color: '#10b981' },
        { name: 'Story Time', emoji: '📚', tagline: 'Bedtime Stories', color: '#f59e0b' },
        { name: 'Music Kids', emoji: '🎵', tagline: 'Sing & Dance', color: '#3b82f6' },
        { name: 'Nature Kids', emoji: '🌿', tagline: 'Explore Wildlife', color: '#22c55e' },
    ];

    let playerModal = null;
    let playerVideo = null;
    let hlsInstance = null;

    function storageUrl(path) {
        if (!path) return null;
        if (path.startsWith('http')) return path;
        return STORAGE_BASE + path.replace(/^\//, '');
    }

    async function fetchJson(url) {
        const res = await fetch(url);
        if (!res.ok) throw new Error('Failed to load ' + url);
        return res.json();
    }

    function showSkeleton(containerId, count) {
        const el = document.getElementById(containerId);
        el.innerHTML = Array(count).fill('<div class="skeleton"></div>').join('');
    }

    function renderChannelCard(channel) {
        const logo = storageUrl(channel.logo);
        const logoHtml = logo
            ? `<img src="${logo}" alt="${channel.name}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">`
            : '';
        const fallback = `<div class="channel-logo-fallback" style="${logo ? 'display:none' : ''}"><i class="fas fa-tv"></i></div>`;

        return `
            <article class="channel-card" data-stream="${channel.stream_url || ''}" data-title="${channel.name}">
                <div class="channel-logo-wrap">
                    ${logoHtml}
                    ${fallback}
                    <span class="live-dot"><i class="fas fa-circle" style="font-size:6px"></i> LIVE</span>
                </div>
                <div class="channel-info">
                    <h3>${channel.name}</h3>
                    <p>24/7 Live Broadcast</p>
                    <button class="watch-btn" type="button"><i class="fas fa-play"></i> Watch Now</button>
                </div>
            </article>
        `;
    }

    function renderProgrammeCard(video) {
        const poster = storageUrl(video.poster || video.thumbnail);
        const streamUrl = video.video_url || video.video_path || '';
        const duration = video.formatted_duration || '';
        const posterHtml = poster
            ? `<img src="${poster}" alt="${video.title}" onerror="this.parentElement.innerHTML='<div class=\\'programme-poster-fallback\\'><i class=\\'fas fa-film\\'></i></div>'">`
            : `<div class="programme-poster-fallback"><i class="fas fa-film"></i></div>`;

        return `
            <article class="programme-card" data-stream="${streamUrl}" data-title="${video.title}">
                <div class="programme-poster">
                    ${posterHtml}
                    <div class="programme-play"><i class="fas fa-play"></i></div>
                </div>
                <div class="programme-details">
                    <h3>${video.title}</h3>
                    <div class="programme-meta">
                        ${duration ? `<span><i class="far fa-clock"></i> ${duration}</span>` : ''}
                        <span><i class="fas fa-play-circle"></i> On Demand</span>
                    </div>
                </div>
            </article>
        `;
    }

    function renderKidsCard(kid) {
        return `
            <article class="kids-card" style="background: linear-gradient(145deg, ${kid.color}33, ${kid.color}11)">
                <div class="kids-card-visual" style="background: linear-gradient(180deg, ${kid.color}44, transparent)">
                    <span class="kids-badge">Kids</span>
                    <span class="emoji">${kid.emoji}</span>
                </div>
                <div class="kids-card-info">
                    <h3>${kid.name}</h3>
                    <p>${kid.tagline}</p>
                </div>
            </article>
        `;
    }

    function bindPlayButtons(container) {
        container.querySelectorAll('[data-stream]').forEach(card => {
            const stream = card.dataset.stream;
            const title = card.dataset.title;
            if (!stream) return;

            const trigger = card.querySelector('.watch-btn') || card;
            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                openPlayer(title, stream);
            });

            if (!card.querySelector('.watch-btn')) {
                card.addEventListener('click', () => openPlayer(title, stream));
            }
        });
    }

    function openPlayer(title, streamUrl) {
        if (!playerModal) {
            playerModal = document.getElementById('playerModal');
            playerVideo = document.getElementById('playerVideo');
            document.getElementById('modalClose').addEventListener('click', closePlayer);
            playerModal.addEventListener('click', (e) => {
                if (e.target === playerModal) closePlayer();
            });
        }

        document.getElementById('modalTitle').textContent = title;
        playerModal.classList.add('active');
        document.body.style.overflow = 'hidden';

        if (hlsInstance) {
            hlsInstance.destroy();
            hlsInstance = null;
        }

        playerVideo.pause();
        playerVideo.removeAttribute('src');
        playerVideo.load();

        const isHls = streamUrl.includes('.m3u8');

        if (isHls && window.Hls && Hls.isSupported()) {
            hlsInstance = new Hls();
            hlsInstance.loadSource(streamUrl);
            hlsInstance.attachMedia(playerVideo);
            hlsInstance.on(Hls.Events.MANIFEST_PARSED, () => playerVideo.play().catch(() => {}));
        } else if (isHls && playerVideo.canPlayType('application/vnd.apple.mpegurl')) {
            playerVideo.src = streamUrl;
            playerVideo.play().catch(() => {});
        } else {
            playerVideo.src = streamUrl;
            playerVideo.play().catch(() => {});
        }
    }

    function closePlayer() {
        if (hlsInstance) {
            hlsInstance.destroy();
            hlsInstance = null;
        }
        playerVideo.pause();
        playerVideo.removeAttribute('src');
        playerModal.classList.remove('active');
        document.body.style.overflow = '';
    }

    async function loadChannels() {
        showSkeleton('channelsGrid', 4);
        try {
            const channels = await fetchJson(API_BASE + '/live-channels');
            const grid = document.getElementById('channelsGrid');

            if (!channels.length) {
                grid.innerHTML = '<div class="empty-state"><i class="fas fa-satellite-dish"></i><p>No live channels available yet.</p></div>';
                return;
            }

            grid.innerHTML = channels.map(renderChannelCard).join('');
            bindPlayButtons(grid);
            document.getElementById('statChannels').textContent = channels.length;

            const heroImg = document.getElementById('heroFeatured');
            const firstLogo = storageUrl(channels[0].logo);
            if (firstLogo && heroImg) {
                heroImg.innerHTML = `<img src="${firstLogo}" alt="${channels[0].name}">`;
            }
        } catch (err) {
            document.getElementById('channelsGrid').innerHTML =
                '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Could not load channels.</p></div>';
        }
    }

    async function loadProgrammes() {
        showSkeleton('programmesGrid', 4);
        try {
            let videos = await fetchJson(API_BASE + '/movies');
            if (!videos.length) {
                videos = await fetchJson(API_BASE + '/videos/latest?limit=8');
            }

            const grid = document.getElementById('programmesGrid');

            if (!videos.length) {
                grid.innerHTML = '<div class="empty-state"><i class="fas fa-film"></i><p>No programmes available yet.</p></div>';
                return;
            }

            grid.innerHTML = videos.map(renderProgrammeCard).join('');
            bindPlayButtons(grid);
            document.getElementById('statProgrammes').textContent = videos.length;
        } catch (err) {
            document.getElementById('programmesGrid').innerHTML =
                '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Could not load programmes.</p></div>';
        }
    }

    async function loadKidsSection() {
        const grid = document.getElementById('kidsGrid');
        let kidsVideos = [];

        try {
            const categories = await fetchJson(API_BASE + '/categories');
            const kidsCats = categories.filter(c =>
                /kid|child|junior|cartoon/i.test(c.name) || /kid|child/i.test(c.type || '')
            );

            for (const cat of kidsCats) {
                const data = await fetchJson(API_BASE + '/movies/category/' + cat.id);
                if (data.videos) kidsVideos = kidsVideos.concat(data.videos);
            }
        } catch (err) {
            /* fall through to featured kids channels */
        }

        if (kidsVideos.length) {
            grid.innerHTML = kidsVideos.map(v => renderProgrammeCard(v)).join('');
            bindPlayButtons(grid);
            document.getElementById('statKids').textContent = kidsVideos.length;
        } else {
            grid.innerHTML = kidsChannels.map(renderKidsCard).join('');
            document.getElementById('statKids').textContent = kidsChannels.length;
        }
    }

    function initNav() {
        const btn = document.getElementById('mobileMenuBtn');
        const nav = document.getElementById('navLinks');
        btn.addEventListener('click', () => nav.classList.toggle('open'));
    }

    async function loadCategoriesCount() {
        try {
            const categories = await fetchJson(API_BASE + '/categories');
            document.getElementById('statCategories').textContent = categories.length;
        } catch (err) {
            document.getElementById('statCategories').textContent = '—';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        initNav();
        loadChannels();
        loadProgrammes();
        loadKidsSection();
        loadCategoriesCount();
    });
})();
