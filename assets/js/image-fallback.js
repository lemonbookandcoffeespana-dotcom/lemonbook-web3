(() => {
  'use strict';

  const selector = 'img[data-content-image]';

  const showFallback = (image) => {
    const container = image.closest('[data-image-fallback]');
    if (!container || container.classList.contains('is-broken')) return;

    container.classList.add('is-broken');
    image.setAttribute('aria-hidden', 'true');
  };

  document.addEventListener('error', (event) => {
    const target = event.target;
    if (target instanceof HTMLImageElement && target.matches(selector)) {
      showFallback(target);
    }
  }, true);

  document.querySelectorAll(selector).forEach((image) => {
    if (image.complete && image.naturalWidth === 0) {
      showFallback(image);
    }
  });
})();

