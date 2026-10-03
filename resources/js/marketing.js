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

    const openVideo = () => {
        video?.classList.remove('hidden');
        unlockScroll();
    };

    const closeVideo = () => {
        video?.classList.add('hidden');
        unlockScroll();
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
