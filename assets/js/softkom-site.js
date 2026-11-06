(function () {
  'use strict';

  var root = document.querySelector('.sk-site');
  if (!root) return;

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var mobileQuery = window.matchMedia('(max-width: 900px)');
  var doc = document.documentElement;

  if (!reduceMotion) {
    doc.classList.add('sk-motion');
  }

  function isMobile() {
    return mobileQuery.matches;
  }

  /* —— Sticky header scroll state —— */
  var header = root.querySelector('.sk-header');
  if (header) {
    var onScroll = function () {
      if (window.scrollY > 16) {
        header.classList.add('is-scrolled');
      } else {
        header.classList.remove('is-scrolled');
      }
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  /* —— Premium slide-over mobile nav —— */
  var drawer = root.querySelector('[data-sk-nav-drawer]');
  var overlay = root.querySelector('[data-sk-nav-overlay]');
  var openBtns = root.querySelectorAll('[data-sk-nav-open]');
  var closeBtns = root.querySelectorAll('[data-sk-nav-close]');
  var lastFocus = null;

  function getFocusable(container) {
    if (!container) return [];
    return Array.prototype.slice.call(
      container.querySelectorAll('a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])')
    );
  }

  function setDrawerInert(open) {
    if (!drawer) return;
    var targets = drawer.querySelectorAll('a[href], button, [tabindex]');
    if (open) {
      drawer.removeAttribute('inert');
      Array.prototype.forEach.call(targets, function (el) {
        if (el.hasAttribute('data-sk-tabindex')) {
          var prev = el.getAttribute('data-sk-tabindex');
          el.removeAttribute('data-sk-tabindex');
          if (prev === '' || prev === null) {
            el.removeAttribute('tabindex');
          } else {
            el.setAttribute('tabindex', prev);
          }
        } else if (el.getAttribute('tabindex') === '-1') {
          el.removeAttribute('tabindex');
        }
      });
    } else {
      drawer.setAttribute('inert', '');
      Array.prototype.forEach.call(targets, function (el) {
        if (!el.hasAttribute('data-sk-tabindex')) {
          el.setAttribute('data-sk-tabindex', el.hasAttribute('tabindex') ? el.getAttribute('tabindex') : '');
        }
        el.setAttribute('tabindex', '-1');
      });
    }
  }

  function setNavOpen(open) {
    if (!drawer) return;
    if (open) {
      lastFocus = document.activeElement;
      drawer.classList.add('is-open');
      drawer.setAttribute('aria-hidden', 'false');
      setDrawerInert(true);
      if (overlay) {
        overlay.hidden = false;
        overlay.classList.add('is-open');
      }
      openBtns.forEach(function (btn) {
        btn.setAttribute('aria-expanded', 'true');
      });
      doc.classList.add('sk-nav-locked');
      var focusables = getFocusable(drawer);
      if (focusables.length) focusables[0].focus();
    } else {
      drawer.classList.remove('is-open');
      drawer.setAttribute('aria-hidden', 'true');
      setDrawerInert(false);
      if (overlay) {
        overlay.classList.remove('is-open');
        overlay.hidden = true;
      }
      openBtns.forEach(function (btn) {
        btn.setAttribute('aria-expanded', 'false');
      });
      doc.classList.remove('sk-nav-locked');
      if (lastFocus && typeof lastFocus.focus === 'function') {
        lastFocus.focus();
      }
    }
  }

  /* Closed drawer must not expose focusable descendants. */
  setDrawerInert(false);

  openBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      setNavOpen(true);
    });
  });

  closeBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      setNavOpen(false);
    });
  });

  if (overlay) {
    overlay.addEventListener('click', function () {
      setNavOpen(false);
    });
  }

  if (drawer) {
    drawer.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        setNavOpen(false);
      });
    });

    document.addEventListener('keydown', function (event) {
      if (!drawer.classList.contains('is-open')) return;
      if (event.key === 'Escape') {
        setNavOpen(false);
        return;
      }
      if (event.key !== 'Tab') return;
      var focusables = getFocusable(drawer);
      if (!focusables.length) return;
      var first = focusables[0];
      var last = focusables[focusables.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    });
  }

  mobileQuery.addEventListener('change', function () {
    if (!isMobile()) setNavOpen(false);
  });

  /* —— Journey accordion (legacy sections) —— */
  var stages = root.querySelectorAll('.sk-journey-stage, .journey-stage');
  stages.forEach(function (stage) {
    stage.addEventListener('click', function () {
      if (!isMobile()) return;
      var open = stage.classList.contains('is-open');
      stages.forEach(function (s) { s.classList.remove('is-open'); });
      if (!open) stage.classList.add('is-open');
    });

    stage.addEventListener('keydown', function (event) {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        if (!isMobile()) {
          stage.classList.toggle('is-open');
          return;
        }
        stage.click();
      }
    });
  });

  mobileQuery.addEventListener('change', function () {
    if (!isMobile()) {
      stages.forEach(function (s) { s.classList.remove('is-open'); });
    }
  });

  /* —— Reveal on scroll —— */
  if (reduceMotion || !('IntersectionObserver' in window)) {
    root.querySelectorAll('.sk-reveal').forEach(function (el) {
      el.classList.add('is-visible');
    });
    root.querySelectorAll('.sk-hero-enter').forEach(function (el) {
      el.classList.add('is-visible');
    });
    return;
  }

  requestAnimationFrame(function () {
    root.querySelectorAll('.sk-hero-enter').forEach(function (el) {
      el.classList.add('is-visible');
    });
  });

  var observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.12, rootMargin: '0px 0px -8% 0px' }
  );

  root.querySelectorAll('.sk-reveal').forEach(function (el) {
    observer.observe(el);
  });
})();
