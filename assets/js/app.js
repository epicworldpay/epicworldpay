(() => {
  const doc = document.documentElement;
  doc.classList.add('reveal-ready');
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const hasIO = 'IntersectionObserver' in window;

  const header = document.querySelector('[data-header]');
  if (header) {
    const update = () => header.classList.toggle('is-scrolled', window.scrollY > 8);
    update();
    window.addEventListener('scroll', update, { passive: true });
  }

  const burger = document.querySelector('[data-burger]');
  const menu = document.querySelector('[data-mobile-menu]');
  if (burger && menu) {
    const isOpen = () => burger.getAttribute('aria-expanded') === 'true';
    const setOpen = (open) => {
      burger.setAttribute('aria-expanded', open ? 'true' : 'false');
      burger.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
      doc.classList.toggle('menu-open', open);
      if (open) {
        menu.hidden = false;
        void menu.offsetWidth;
        menu.classList.add('is-open');
      } else {
        menu.classList.remove('is-open');
        window.setTimeout(() => {
          if (!isOpen()) menu.hidden = true;
        }, 260);
      }
    };
    burger.addEventListener('click', () => setOpen(!isOpen()));
    menu.addEventListener('click', (event) => {
      if (event.target.closest('a')) setOpen(false);
    });
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && isOpen()) setOpen(false);
    });
    window.matchMedia('(min-width: 1024px)').addEventListener('change', (event) => {
      if (event.matches && isOpen()) setOpen(false);
    });
  }

  const revealItems = document.querySelectorAll('.reveal');
  if (hasIO && !reduceMotion) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-in');
          observer.unobserve(entry.target);
        }
      });
    }, { rootMargin: '0px 0px -6% 0px', threshold: 0.06 });
    const fold = window.innerHeight * 0.94;
    revealItems.forEach((item) => {
      const rect = item.getBoundingClientRect();
      if (rect.top < fold && rect.bottom > 0) item.classList.add('is-in');
      else observer.observe(item);
    });
  } else {
    revealItems.forEach((item) => item.classList.add('is-in'));
  }

  const lottieNodes = Array.from(document.querySelectorAll('[data-lottie]'));
  if (lottieNodes.length) {
    let library = null;
    const animations = new Map();
    const loadLibrary = () => {
      library = library || new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = doc.dataset.lottieLib;
        script.async = true;
        script.onload = () => (window.lottie ? resolve(window.lottie) : reject(new Error('lottie')));
        script.onerror = reject;
        document.head.appendChild(script);
      });
      return library;
    };
    const boot = (node) => {
      if (animations.has(node)) return;
      animations.set(node, null);
      loadLibrary().then((lottie) => {
        const animation = lottie.loadAnimation({
          container: node,
          renderer: 'svg',
          loop: !node.closest('.success'),
          autoplay: false,
          path: node.dataset.lottie,
          rendererSettings: { preserveAspectRatio: 'xMidYMid meet', progressiveLoad: true },
        });
        animations.set(node, animation);
        animation.addEventListener('DOMLoaded', () => {
          node.classList.add('is-ready');
          if (reduceMotion) animation.goToAndStop(Math.round(animation.totalFrames * 0.8), true);
          else if (node.dataset.visible !== '0') animation.play();
        });
      }).catch(() => node.classList.add('is-ready'));
    };
    if (hasIO) {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          const node = entry.target;
          node.dataset.visible = entry.isIntersecting ? '1' : '0';
          const animation = animations.get(node);
          if (entry.isIntersecting) {
            boot(node);
            if (animation && !reduceMotion) animation.play();
          } else if (animation) {
            animation.pause();
          }
        });
      }, { rootMargin: '160px 0px' });
      lottieNodes.forEach((node) => observer.observe(node));
    } else {
      lottieNodes.forEach(boot);
    }
    lottieNodes.filter((node) => node.dataset.eager === 'true').forEach(boot);
  }

  const toc = document.querySelector('[data-toc]');
  if (toc) {
    const desktop = window.matchMedia('(min-width: 1024px)');
    const sync = () => {
      if (desktop.matches) toc.open = true;
    };
    sync();
    desktop.addEventListener('change', sync);
    const links = Array.from(toc.querySelectorAll('a[href^="#"]'));
    const targets = links.map((link) => document.getElementById(link.hash.slice(1))).filter(Boolean);
    if (hasIO && targets.length) {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            links.forEach((link) => link.classList.toggle('is-active', link.hash === `#${entry.target.id}`));
          }
        });
      }, { rootMargin: '-20% 0px -70% 0px' });
      targets.forEach((target) => observer.observe(target));
    }
    toc.addEventListener('click', (event) => {
      if (event.target.closest('a') && !desktop.matches) toc.open = false;
    });
  }

  document.querySelectorAll('[data-form]').forEach((form) => {
    const success = form.parentElement.querySelector('[data-form-success]');
    const button = form.querySelector('[type="submit"]');
    const fields = Array.from(form.querySelectorAll('[required]'));
    const check = (field) => field.value.trim() !== '' && field.checkValidity();
    const mark = (field, valid) => {
      const id = `err-${field.id}`;
      let error = document.getElementById(id);
      field.setAttribute('aria-invalid', valid ? 'false' : 'true');
      if (valid) {
        if (error) error.remove();
        field.removeAttribute('aria-describedby');
        return;
      }
      if (!error) {
        error = document.createElement('p');
        error.className = 'field__error';
        error.id = id;
        error.textContent = field.dataset.error || 'Please fill in this field.';
        field.after(error);
      }
      field.setAttribute('aria-describedby', id);
    };
    form.addEventListener('submit', (event) => {
      event.preventDefault();
      const invalid = fields.filter((field) => {
        const valid = check(field);
        mark(field, valid);
        return !valid;
      });
      if (invalid.length) {
        invalid[0].focus();
        return;
      }
      if (button) {
        button.disabled = true;
        button.textContent = 'Sending...';
      }
      window.setTimeout(() => {
        form.reset();
        form.hidden = true;
        if (success) {
          success.hidden = false;
          success.focus();
        }
      }, 900);
    });
    form.addEventListener('input', (event) => {
      if (event.target.getAttribute('aria-invalid') === 'true' && check(event.target)) mark(event.target, true);
    });
  });
})();
