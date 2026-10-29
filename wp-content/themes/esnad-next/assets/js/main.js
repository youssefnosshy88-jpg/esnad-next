/* ESNAD NEXT — vanilla JS, no dependencies. */
(function () {
	'use strict';
	var d = document;
	var root = d.documentElement;
	root.classList.add('js');
	var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* Header: solid after scrolling past the hero top. */
	var header = d.getElementById('site-header');
	if (header) {
		var onScroll = function () { header.classList.toggle('is-solid', window.scrollY > 40); };
		onScroll();
		window.addEventListener('scroll', onScroll, { passive: true });
	}

	/* Mobile navigation drawer. */
	var toggle = d.querySelector('.nav-toggle');
	var nav = d.getElementById('primary-nav');
	if (toggle && nav) {
		toggle.addEventListener('click', function () {
			var open = toggle.getAttribute('aria-expanded') !== 'true';
			toggle.setAttribute('aria-expanded', String(open));
			nav.classList.toggle('is-open', open);
			d.body.style.overflow = open ? 'hidden' : '';
		});
		d.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				toggle.setAttribute('aria-expanded', 'false');
				nav.classList.remove('is-open');
				d.body.style.overflow = '';
				d.querySelectorAll('.has-sub.is-open').forEach(function (li) { li.classList.remove('is-open'); });
			}
		});
	}
	/* Sub-menus: buttons toggle aria-expanded (works for touch and keyboard). */
	d.querySelectorAll('.sub-toggle').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var li = btn.closest('.has-sub');
			var open = !li.classList.contains('is-open');
			li.classList.toggle('is-open', open);
			btn.setAttribute('aria-expanded', String(open));
		});
	});

	/* Reveal on scroll. */
	var reveals = d.querySelectorAll('.reveal');
	if ('IntersectionObserver' in window && !reduce) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
			});
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
		reveals.forEach(function (el) { io.observe(el); });
	} else {
		reveals.forEach(function (el) { el.classList.add('is-in'); });
	}

	/* Hijri + Gregorian date in the top strip (Umm al-Qura calendar). */
	var lang = root.lang === 'en' ? 'en' : 'ar';
	try {
		var hd = d.querySelector('[data-hijri]');
		var gd = d.querySelector('[data-greg]');
		var now = new Date();
		if (hd) { hd.textContent = new Intl.DateTimeFormat(lang === 'ar' ? 'ar-SA-u-ca-islamic-umalqura-nu-latn' : 'en-US-u-ca-islamic-umalqura', { day: 'numeric', month: 'long', year: 'numeric' }).format(now); }
		if (gd) { gd.textContent = new Intl.DateTimeFormat(lang === 'ar' ? 'ar-SA-u-ca-gregory-nu-latn' : 'en-GB', { day: 'numeric', month: 'long', year: 'numeric' }).format(now); }
	} catch (e) { /* date strip is decorative */ }

	/* Hero video: load only after the page has loaded, on capable connections, never with reduced motion. */
	var vid = d.querySelector('.hero__video');
	if (vid && !reduce) {
		var conn = navigator.connection || {};
		var light = conn.saveData || /(^|-)2g$/.test(conn.effectiveType || '');
		if (!light) {
			var startVideo = function () {
				vid.src = window.innerWidth > 760 ? vid.getAttribute('data-src-lg') : vid.getAttribute('data-src-sm');
				vid.addEventListener('canplay', function () { vid.classList.add('is-ready'); vid.play().catch(function () {}); }, { once: true });
				vid.load();
			};
			if (d.readyState === 'complete') { setTimeout(startVideo, 300); } else { window.addEventListener('load', function () { setTimeout(startVideo, 300); }); }
			d.addEventListener('visibilitychange', function () { if (d.hidden) { vid.pause(); } else if (vid.classList.contains('is-ready')) { vid.play().catch(function () {}); } });
		}
	}

	/* Stage-curtain reveal: panels part when a block scrolls into view. */
	if ('IntersectionObserver' in window && !reduce) {
		var targets = d.querySelectorAll('.explorer .cards, .pillars, .needs, .docs, .articles, .faq, .about__grid, .atlas__grid, .journey__steps, .marquee, .contact-grid, .info-cards, .chooser, .people, .year-grid, .vm, .values');
		var cio = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				if (en.isIntersecting) { en.target.classList.add('is-open'); cio.unobserve(en.target); }
			});
		}, { rootMargin: '0px 0px -12% 0px', threshold: 0 });
		targets.forEach(function (el, i) {
			el.classList.add('curtain');
			if (el.closest('.atlas, .clients--dark, .cta')) { el.classList.add('curtain--light'); }
			el.style.setProperty('--cd', '0s');
			cio.observe(el);
		});
	}

	/* Numbered service directory: hover or focus selects a service; on touch the first tap selects, the panel link opens it. */
	d.querySelectorAll('[data-svlist]').forEach(function (box) {
		var items = box.querySelectorAll('.svlist__item');
		var slides = box.querySelectorAll('.svlist__slide');
		var coarse = window.matchMedia && window.matchMedia('(hover: none)').matches;
		var select = function (n) {
			items.forEach(function (it, i) { it.classList.toggle('is-active', i === n); });
			slides.forEach(function (s, i) {
				var on = i === n;
				s.classList.toggle('is-active', on);
				if (on) { s.removeAttribute('aria-hidden'); } else { s.setAttribute('aria-hidden', 'true'); }
				var a = s.querySelector('a'); if (a) { if (on) { a.removeAttribute('tabindex'); } else { a.setAttribute('tabindex', '-1'); } }
			});
		};
		items.forEach(function (it, i) {
			it.addEventListener('mouseenter', function () { select(i); });
			it.addEventListener('focus', function () { select(i); });
			it.addEventListener('click', function (e) {
				if (coarse && !it.classList.contains('is-active')) { e.preventDefault(); select(i); }
			});
		});
	});

	/* Analytics consent: nothing loads until the visitor accepts; the choice lives in localStorage. */
	var consent = d.querySelector('[data-consent]');
	if (consent) {
		var gaId = consent.getAttribute('data-ga');
		var KEY = 'esnad-consent';
		var read = function () { try { return window.localStorage.getItem(KEY); } catch (e) { return null; } };
		var write = function (v) { try { window.localStorage.setItem(KEY, v); } catch (e) { /* private mode: the choice just is not remembered */ } };
		var loaded = false;
		var loadGa = function () {
			if (loaded || !gaId) { return; }
			loaded = true;
			var s = d.createElement('script');
			s.async = true;
			s.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(gaId);
			d.head.appendChild(s);
			window.dataLayer = window.dataLayer || [];
			window.gtag = function () { window.dataLayer.push(arguments); };
			window.gtag('js', new Date());
			window.gtag('config', gaId, { anonymize_ip: true });
		};
		var state = read();
		if (state === 'yes') { loadGa(); } else if (state !== 'no') { consent.hidden = false; }
		consent.querySelector('[data-consent-yes]').addEventListener('click', function () { write('yes'); consent.hidden = true; loadGa(); });
		consent.querySelector('[data-consent-no]').addEventListener('click', function () { write('no'); consent.hidden = true; });
		d.querySelectorAll('[data-consent-open]').forEach(function (b) { b.addEventListener('click', function () { consent.hidden = false; }); });
	}

	/* Article category filter (progressive: without JS every article stays visible). */
	d.querySelectorAll('.blog-filter').forEach(function (bar) {
		var grid = d.querySelector('[data-blog]');
		if (!grid) { return; }
		bar.addEventListener('click', function (e) {
			var b = e.target.closest('button[data-cat]');
			if (!b) { return; }
			var cat = b.getAttribute('data-cat');
			bar.querySelectorAll('button').forEach(function (x) { x.classList.toggle('is-active', x === b); });
			grid.querySelectorAll('.article').forEach(function (a) {
				var has = cat === 'all' || (' ' + a.getAttribute('data-cats') + ' ').indexOf(' ' + cat + ' ') > -1;
				a.hidden = !has;
			});
		});
	});

	/* Back to top with scroll-progress ring. */
	var topBtn = d.querySelector('.to-top');
	if (topBtn) {
		var tick = function () {
			var max = Math.max(1, d.documentElement.scrollHeight - window.innerHeight);
			var p = Math.min(1, window.scrollY / max);
			topBtn.style.setProperty('--sp', p.toFixed(3));
			topBtn.classList.toggle('is-on', window.scrollY > 500);
		};
		window.addEventListener('scroll', tick, { passive: true });
		tick();
		topBtn.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' }); });
	}

	/* Reading progress bar. */
	var bar = d.querySelector('.read-bar span');
	if (bar) {
		var upd = function () { var m = Math.max(1, d.documentElement.scrollHeight - window.innerHeight); bar.parentNode.style.setProperty('--rp', Math.min(1, window.scrollY / m).toFixed(3)); };
		window.addEventListener('scroll', upd, { passive: true }); upd();
	}

	/* Stagger index for children of curtain blocks. */
	d.querySelectorAll('.cards, .pillars, .needs, .docs, .articles, .faq, .info-cards, .year-grid, .people, .values, .vm').forEach(function (g) {
		Array.prototype.slice.call(g.children, 0, 14).forEach(function (ch, i) { ch.style.setProperty('--i', i); });
	});

	if (!reduce) {
		/* Hero parallax: video and lattice drift slower than the page. */
		var heroMedia = d.querySelector('.hero__media');
		var heroArt = d.querySelector('.hero__art');
		if (heroMedia) {
			var par = function () {
				var y = Math.min(window.scrollY, 900);
				heroMedia.style.transform = 'translate3d(0,' + (y * 0.25).toFixed(1) + 'px,0) scale(1.08)';
				if (heroArt) { heroArt.style.transform = 'translate3d(0,' + (y * 0.12).toFixed(1) + 'px,0)'; }
			};
			window.addEventListener('scroll', par, { passive: true }); par();
		}
		/* Soft 3D tilt on service cards (pointer devices only). */
		if (false) { /* removed on purpose: an institutional look keeps cards still */
			d.querySelectorAll('.card').forEach(function (card) {
				card.addEventListener('pointermove', function (e) {
					var r = card.getBoundingClientRect();
					var px = (e.clientX - r.left) / r.width - 0.5;
					var py = (e.clientY - r.top) / r.height - 0.5;
					card.style.transform = 'perspective(900px) rotateX(' + (-py * 5).toFixed(2) + 'deg) rotateY(' + (px * 6).toFixed(2) + 'deg) translateY(-6px)';
				});
				card.addEventListener('pointerleave', function () { card.style.transform = ''; });
			});
		}
	}

	/* Service explorer: tabs + need/challenge choosers. */
	d.querySelectorAll('[data-explorer]').forEach(function (ex) {
		var tabs = Array.prototype.slice.call(ex.querySelectorAll('[role="tab"]'));
		function select(tab) {
			tabs.forEach(function (t) {
				var on = t === tab;
				t.setAttribute('aria-selected', String(on));
				t.tabIndex = on ? 0 : -1;
				var p = d.getElementById(t.getAttribute('aria-controls'));
				if (p) { p.hidden = !on; }
			});
		}
		tabs.forEach(function (t, i) {
			t.addEventListener('click', function () { select(t); });
			t.addEventListener('keydown', function (e) {
				var n = null;
				if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
					var rtl = root.dir === 'rtl';
					var step = (e.key === 'ArrowRight') !== rtl ? 1 : -1;
					n = tabs[(i + step + tabs.length) % tabs.length];
				}
				if (n) { e.preventDefault(); select(n); n.focus(); }
			});
		});
		ex.querySelectorAll('.chooser').forEach(function (ch) {
			var btns = ch.querySelectorAll('.chooser__list button');
			var cards = ch.querySelectorAll('.chip-card');
			function show(btn) {
				var targets = (btn.getAttribute('data-targets') || '').split(',');
				btns.forEach(function (b) { b.setAttribute('aria-pressed', String(b === btn)); });
				cards.forEach(function (c) { c.hidden = targets.indexOf(c.getAttribute('data-service')) === -1; });
			}
			btns.forEach(function (b) { b.addEventListener('click', function () { show(b); }); });
			if (btns[0]) { show(btns[0]); }
		});
	});

	/* Financial Atlas: choose a service, trace its route from Riyadh. */
	d.querySelectorAll('[data-atlas]').forEach(function (at) {
		var btns = at.querySelectorAll('.atlas__list button, .node');
		var items = at.querySelectorAll('.atlas__item');
		var routes = at.querySelectorAll('.route');
		var rails = at.querySelectorAll('.rail');
		function show(key) {
			btns.forEach(function (b) { b.setAttribute('aria-pressed', String(b.getAttribute('data-service') === key)); });
			items.forEach(function (i) { i.hidden = i.getAttribute('data-service') !== key; });
			routes.forEach(function (r) { r.classList.toggle('is-on', r.getAttribute('data-route') === key); });
			rails.forEach(function (r) { r.classList.toggle('is-on', r.getAttribute('data-rail') === key); });
		}
		btns.forEach(function (b) { b.addEventListener('click', function () { show(b.getAttribute('data-service')); }); });
		rails.forEach(function (r) { r.addEventListener('click', function () { show(r.getAttribute('data-rail')); }); });
		if (btns[0]) { show(btns[0].getAttribute('data-service')); }
	});

	/* Advisory journey: desktop progress follows scroll; click/focus also activates a step. */
	d.querySelectorAll('[data-journey]').forEach(function (j) {
		var steps = Array.prototype.slice.call(j.querySelectorAll('.step'));
		var bar = j.querySelector('.journey__line span');
		function set(n) {
			steps.forEach(function (s, i) { s.classList.toggle('is-active', i === n); });
			if (bar) { j.style.setProperty('--p', ((n + 1) / steps.length * 100) + '%'); }
		}
		steps.forEach(function (s, i) {
			s.addEventListener('click', function () { set(i); });
			s.tabIndex = 0;
			s.addEventListener('focus', function () { set(i); });
		});
		if (reduce || !('IntersectionObserver' in window)) { set(0); return; }
		var wide = window.matchMedia('(min-width:1025px)');
		function sync() {
			if (!wide.matches) { return; }
			var r = j.getBoundingClientRect();
			var vh = window.innerHeight;
			var p = (vh * 0.65 - r.top) / (r.height * 0.8);
			set(Math.max(0, Math.min(steps.length - 1, Math.floor(p * steps.length))));
		}
		window.addEventListener('scroll', sync, { passive: true });
		sync();
	});
})();
