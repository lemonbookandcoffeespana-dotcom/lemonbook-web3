(() => {
  'use strict';

  const track = document.querySelector('[data-events-carousel]');
  if (!track) return;

  // Solo se actúa cuando el propio gesto ya trae componente horizontal (shift+rueda o
  // trackpad); una rueda vertical normal debe seguir haciendo scroll de la página.
  track.addEventListener('wheel', (event) => {
    if (event.deltaX === 0) return;
    if (track.scrollWidth <= track.clientWidth) return;
    event.preventDefault();
    track.scrollLeft += event.deltaX;
  }, { passive: false });

  // Arrastre con ratón (el touch/trackpad ya funciona de forma nativa vía overflow-x).
  let dragging = false;
  let moved = false;
  let justDragged = false;
  let startX = 0;
  let startScroll = 0;

  track.addEventListener('pointerdown', (event) => {
    if (event.pointerType === 'touch') return;
    dragging = true;
    moved = false;
    startX = event.clientX;
    startScroll = track.scrollLeft;
    track.classList.add('is-dragging');
  });

  track.addEventListener('pointermove', (event) => {
    if (!dragging) return;
    const delta = event.clientX - startX;
    if (Math.abs(delta) > 4) moved = true;
    track.scrollLeft = startScroll - delta;
  });

  const endDrag = () => {
    if (!dragging) return;
    dragging = false;
    track.classList.remove('is-dragging');
    if (moved) {
      // Evita que el arrastre se interprete como un click sobre la tarjeta;
      // se consume con el siguiente click o caduca sola si no llega ninguno.
      justDragged = true;
      setTimeout(() => { justDragged = false; }, 300);
    }
  };

  track.addEventListener('pointerup', endDrag);
  track.addEventListener('pointerleave', endDrag);
  track.addEventListener('pointercancel', endDrag);

  track.addEventListener('click', (event) => {
    if (!justDragged) return;
    justDragged = false;
    event.preventDefault();
    event.stopPropagation();
  }, true);
})();
