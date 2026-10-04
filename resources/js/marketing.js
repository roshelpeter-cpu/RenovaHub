// Landing-page behaviour: navigation state, search, and the introduction video.
// The hero photograph is a single static image, so nothing here rotates slides.
document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('[data-page="marketing"]');
    if (!page) {
        return;
    }

    const nav = document.querySelector('[data-marketing-nav]');
    const links = [...document.querySelectorAll('[data-nav]')];
    const sections = links
        .map((link) => document.querySelector(link.getAttribute('href')))
        .filter(Boolean);

    const setActive = (id) => {
        links.forEach((link) => {
            link.classList.toggle('is-active', link.dataset.nav === id);
        });
    };

    const onScroll = () => {
        nav?.classList.toggle('is-scrolled', window.scrollY > 8);

        const marker = window.scrollY + 150;
        let current = sections[0]?.id ?? 'top';

        sections.forEach((section) => {
            const top = section.getBoundingClientRect().top + window.scrollY;
            if (top <= marker) {
                current = section.id;
            }
        });

        setActive(current);
    };

    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });

    const menu = document.querySelector('[data-mobile-menu]');
    const menuToggle = document.querySelector('[data-menu-toggle]');
    const search = document.querySelector('[data-search-modal]');
    const video = document.querySelector('[data-video-modal]');

    const unlockScroll = () => {
        const searchOpen = search && !search.classList.contains('hidden');
        const videoOpen = video && !video.classList.contains('hidden');
        document.body.classList.toggle('overflow-hidden', Boolean(searchOpen || videoOpen));
    };

    const closeMenu = () => {
        menu?.classList.add('hidden');
        menuToggle?.setAttribute('aria-expanded', 'false');
    };

    menuToggle?.addEventListener('click', () => {
        const open = menu?.classList.toggle('hidden') === false;
        menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    menu?.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', closeMenu);
    });

    const searchInput = search?.querySelector('input');
    const searchItems = [...(search?.querySelectorAll('[data-search-item]') ?? [])];

    const openSearch = () => {
        search?.classList.remove('hidden');
        unlockScroll();
        searchInput?.focus();
    };

    const closeSearch = () => {
        search?.classList.add('hidden');
        if (searchInput) {
            searchInput.value = '';
        }
        searchItems.forEach((item) => item.classList.remove('hidden'));
        unlockScroll();
    };

    document.querySelectorAll('[data-open-search]').forEach((button) => {
        button.addEventListener('click', openSearch);
    });

    search?.querySelectorAll('[data-close-search]').forEach((button) => {
        button.addEventListener('click', closeSearch);
    });

    searchInput?.addEventListener('input', () => {
        const query = searchInput.value.trim().toLowerCase();
        searchItems.forEach((item) => {
            const haystack = item.dataset.searchItem.toLowerCase();
            item.classList.toggle('hidden', query !== '' && !haystack.includes(query));
        });
    });

    const player = video?.querySelector('[data-video-player]');
    const videoFallback = video?.querySelector('[data-video-fallback]');
    const videoFrame = video?.querySelector('[data-video-frame]');
    const playButton = video?.querySelector('[data-video-play]');
    const muteButton = video?.querySelector('[data-video-mute]');
    const seek = video?.querySelector('[data-video-seek]');
    const volume = video?.querySelector('[data-video-volume]');
    const playIcon = video?.querySelector('[data-icon-play]');
    const pauseIcon = video?.querySelector('[data-icon-pause]');
    const volumeIcon = video?.querySelector('[data-icon-volume]');
    const mutedIcon = video?.querySelector('[data-icon-muted]');
    const videoReady = Boolean(video?.hasAttribute('data-video-available') && player);

    const setPlaying = (playing) => {
        playIcon?.classList.toggle('hidden', playing);
        pauseIcon?.classList.toggle('hidden', !playing);
        playButton?.setAttribute('aria-label', playing ? 'Pause' : 'Play');
    };

    const setMuted = (muted) => {
        volumeIcon?.classList.toggle('hidden', muted);
        mutedIcon?.classList.toggle('hidden', !muted);
        muteButton?.setAttribute('aria-label', muted ? 'Unmute' : 'Mute');
    };

    const showVideoFallback = () => {
        player?.classList.add('hidden');
        videoFallback?.classList.remove('hidden');
        videoFallback?.classList.add('flex');
        playButton?.setAttribute('disabled', 'true');
        muteButton?.setAttribute('disabled', 'true');
        seek?.setAttribute('disabled', 'true');
        volume?.setAttribute('disabled', 'true');
        video?.querySelector('[data-video-full]')?.setAttribute('disabled', 'true');
    };

    if (videoReady) {
        player.addEventListener('loadedmetadata', () => {
            videoFallback?.classList.add('hidden');
            videoFallback?.classList.remove('flex');
            player.classList.remove('hidden');
        });
        player.addEventListener('error', showVideoFallback);
        player.addEventListener('play', () => setPlaying(true));
        player.addEventListener('pause', () => setPlaying(false));
        player.addEventListener('timeupdate', () => {
            if (!seek || !player.duration) {
                return;
            }
            seek.value = String((player.currentTime / player.duration) * 100);
        });
        player.addEventListener('volumechange', () => {
            setMuted(player.muted || player.volume === 0);
            if (volume && !player.muted) {
                volume.value = String(player.volume);
            }
        });
        if (player.error) {
            showVideoFallback();
        }
    }

    playButton?.addEventListener('click', () => {
        if (!videoReady) {
            return;
        }
        if (player.paused) {
            player.play();
        } else {
            player.pause();
        }
    });

    muteButton?.addEventListener('click', () => {
        if (!videoReady) {
            return;
        }
        player.muted = !player.muted;
    });

    volume?.addEventListener('input', () => {
        if (!videoReady) {
            return;
        }
        player.volume = Number(volume.value);
        player.muted = player.volume === 0;
    });

    seek?.addEventListener('input', () => {
        if (!videoReady || !player.duration) {
            return;
        }
        player.currentTime = (Number(seek.value) / 100) * player.duration;
    });

    video?.querySelector('[data-video-full]')?.addEventListener('click', () => {
        const frame = videoFrame;
        if (!frame) {
            return;
        }
        if (document.fullscreenElement) {
            document.exitFullscreen();
        } else {
            frame.requestFullscreen?.();
        }
    });

    const openVideo = () => {
        video?.classList.remove('hidden');
        requestAnimationFrame(() => video?.classList.add('is-open'));
        unlockScroll();
        if (videoReady && player.readyState === 0) {
            player.load();
        }
    };

    const closeVideo = () => {
        video?.classList.remove('is-open');
        player?.pause();
        window.setTimeout(() => {
            if (!video?.classList.contains('is-open')) {
                video?.classList.add('hidden');
                unlockScroll();
            }
        }, 320);
    };

    document.querySelectorAll('[data-open-video]').forEach((button) => {
        button.addEventListener('click', openVideo);
    });

    video?.querySelectorAll('[data-close-video]').forEach((button) => {
        button.addEventListener('click', closeVideo);
    });

    const story = document.querySelector('[data-story]');
    document.querySelector('[data-story-toggle]')?.addEventListener('click', () => {
        story?.classList.toggle('hidden');
    });

    const contactForm = document.querySelector('[data-contact-form]');
    contactForm?.addEventListener('submit', (event) => {
        event.preventDefault();
        contactForm.querySelector('[data-contact-fields]')?.classList.add('hidden');
        contactForm.querySelector('[data-contact-success]')?.classList.remove('hidden');
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }
        closeMenu();
        closeSearch();
        closeVideo();
    });
});
