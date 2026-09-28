(() => {
  'use strict';

  const dialog = document.querySelector('[data-fair-popup]');
  if (!dialog || typeof dialog.showModal !== 'function') return;

  const key = 'lemon_fair_popup_' + (dialog.getAttribute('data-fair') || '');
  const quietMs = 3 * 24 * 60 * 60 * 1000;

  const lastSeen = () => {
    try { return Number(window.localStorage.getItem(key)) || 0; } catch (error) { return 0; }
  };
  const remember = () => {
    try { window.localStorage.setItem(key, String(Date.now())); } catch (error) { /* sin almacenamiento: se volverá a mostrar */ }
  };

  dialog.querySelectorAll('[data-close]').forEach((button) => button.addEventListener('click', () => dialog.close()));
  dialog.querySelectorAll('a').forEach((link) => link.addEventListener('click', remember));
  dialog.addEventListener('close', remember);
  dialog.addEventListener('click', (event) => {
    if (event.target === dialog) dialog.close();
  });

  if (Date.now() - lastSeen() < quietMs) return;

  window.setTimeout(() => {
    const active = document.activeElement;
    const typing = active && /^(INPUT|TEXTAREA|SELECT)$/.test(active.tagName);
    if (!dialog.open && !typing) dialog.showModal();
  }, 2500);
})();
