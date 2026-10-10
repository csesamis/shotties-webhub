// Keeps the Spotify player playing while visitors move around the site.
// Link clicks and form submits are fetched in the background and only the header and
// <main> are swapped, so the player (which sits outside both) is never reloaded.
// If anything goes wrong, the browser falls back to normal page loads.
(function () {
    'use strict';

    const player = document.getElementById('music-player');
    if (!player) { return; }

    // ---- Collapse / expand ------------------------------------------------
    const toggle = player.querySelector('.music-toggle');
    const KEY = 'shotties-player-collapsed';
    const store = {
        get() { try { return sessionStorage.getItem(KEY); } catch (e) { return null; } },
        set(value) { try { sessionStorage.setItem(KEY, value); } catch (e) { /* storage unavailable */ } }
    };
    function setCollapsed(collapsed) {
        player.classList.toggle('is-collapsed', collapsed);
        toggle.setAttribute('aria-expanded', String(!collapsed));
    }
    setCollapsed(store.get() === '1');
    toggle.addEventListener('click', function () {
        const collapsed = !player.classList.contains('is-collapsed');
        setCollapsed(collapsed);
        store.set(collapsed ? '1' : '0');
    });

    // ---- Background navigation -------------------------------------------
    let currentPath = location.pathname + location.search;

    function swapPage(doc) {
        const newHeader = doc.querySelector('.site-header');
        const newMain = doc.querySelector('main');
        if (!newHeader || !newMain) { throw new Error('Unexpected page'); }

        document.title = doc.title;
        document.querySelector('.site-header').innerHTML = newHeader.innerHTML;
        const main = document.querySelector('main');
        main.innerHTML = newMain.innerHTML;

        // Scripts added through innerHTML do not run, so recreate them.
        main.querySelectorAll('script').forEach(function (old) {
            const fresh = document.createElement('script');
            fresh.textContent = old.textContent;
            old.replaceWith(fresh);
        });
        return main;
    }

    async function load(url, init, push, hash, fallback) {
        document.body.classList.add('is-loading');
        try {
            const response = await fetch(url, init);
            if (!(response.headers.get('content-type') || '').includes('text/html')) {
                throw new Error('Not an HTML page');
            }
            const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
            const main = swapPage(doc);

            const finalUrl = new URL(response.url);
            if (push) { history.pushState(null, '', finalUrl.pathname + finalUrl.search + (hash || '')); }
            currentPath = finalUrl.pathname + finalUrl.search;

            const target = hash && document.getElementById(decodeURIComponent(hash.slice(1)));
            if (target) { target.scrollIntoView(); } else { window.scrollTo(0, 0); }
            main.setAttribute('tabindex', '-1');
            main.focus({ preventScroll: true });
        } catch (error) {
            fallback();
        } finally {
            document.body.classList.remove('is-loading');
        }
    }

    function isPhpPage(url) {
        return url.origin === location.origin && /(\.php|\/)$/i.test(url.pathname);
    }

    document.addEventListener('click', function (event) {
        if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) { return; }
        const link = event.target.closest('a[href]');
        if (!link || (link.target && link.target !== '_self') || link.hasAttribute('download') || link.hasAttribute('data-no-ajax')) { return; }

        const url = new URL(link.href, location.href);
        if (!isPhpPage(url)) { return; }
        // Jumping to a section of the page we are already on: let the browser do it.
        if (url.hash && url.pathname + url.search === currentPath) { return; }

        event.preventDefault();
        load(url.href, {}, true, url.hash, function () { location.href = url.href; });
    });

    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (event.defaultPrevented || !(form instanceof HTMLFormElement) || form.hasAttribute('data-no-ajax')) { return; }

        const action = new URL(form.getAttribute('action') || location.href, location.href);
        if (!isPhpPage(action)) { return; }

        const data = new FormData(form);
        if (event.submitter && event.submitter.name) { data.append(event.submitter.name, event.submitter.value); }

        event.preventDefault();
        if (form.method.toLowerCase() === 'post') {
            load(action.href, { method: 'POST', body: data }, true, '', function () { form.submit(); });
        } else {
            action.search = new URLSearchParams(data).toString();
            load(action.href, {}, true, '', function () { form.submit(); });
        }
    });

    window.addEventListener('popstate', function () {
        if (location.pathname + location.search === currentPath) { return; }
        load(location.href, {}, false, location.hash, function () { location.reload(); });
    });
})();