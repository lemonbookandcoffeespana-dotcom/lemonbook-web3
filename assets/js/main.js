(() => {
  'use strict';

  const toggle = document.querySelector('[data-menu-toggle]');
  const navigation = document.querySelector('[data-navigation]');

  if (toggle && navigation) {
    toggle.addEventListener('click', () => {
      const open = toggle.getAttribute('aria-expanded') === 'true';
      toggle.setAttribute('aria-expanded', String(!open));
      navigation.classList.toggle('is-open', !open);
    });

    navigation.addEventListener('click', (event) => {
      if (event.target.closest('a')) {
        toggle.setAttribute('aria-expanded', 'false');
        navigation.classList.remove('is-open');
      }
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
        toggle.setAttribute('aria-expanded', 'false');
        navigation.classList.remove('is-open');
        toggle.focus();
      }
    });
  }

  document.querySelectorAll('[data-api-form]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      if (!form.checkValidity()) {
        event.preventDefault();
        form.reportValidity();
        return;
      }

      const endpoint = form.getAttribute('data-endpoint');
      if (!endpoint) {
        event.preventDefault();
        const status = form.querySelector('[data-form-status]');
        if (status) {
          status.textContent = form.getAttribute('data-unavailable-message') || '';
          status.focus();
        }
        return;
      }

      event.preventDefault();
      if (form.classList.contains('is-sending')) return;

      const status = form.querySelector('[data-form-status]');
      const submit = form.querySelector('[type="submit"]');
      const say = (text, ok) => {
        if (!status) return;
        status.textContent = text;
        status.classList.toggle('is-success', ok === true);
        status.classList.toggle('is-error', ok === false);
        status.focus();
      };
      const clearErrors = () => form.querySelectorAll('[aria-invalid]').forEach((el) => el.removeAttribute('aria-invalid'));

      clearErrors();
      form.classList.add('is-sending');
      if (submit) submit.setAttribute('aria-disabled', 'true');
      if (status) status.textContent = form.getAttribute('data-sending-message') || 'Enviando…';

      const data = {};
      new FormData(form).forEach((value, key) => { data[key] = value; });
      data.consent = form.querySelector('[name="consent"]') ? form.querySelector('[name="consent"]').checked : false;

      fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify(data),
      })
        .then((response) => response.json().catch(() => ({})).then((body) => ({ status: response.status, body })))
        .then(({ status: code, body }) => {
          if (body.ok) {
            form.reset();
            say(body.message || form.getAttribute('data-success-message') || 'Enviado.', true);
            return;
          }
          // La API devuelve errores por campo con los nombres de su contrato; se asocian a los campos del formulario.
          const alias = { party_size: 'guests', message: form.querySelector('[name="message"]') ? 'message' : 'notes' };
          Object.keys(body.errors || {}).forEach((field) => {
            const input = form.querySelector('[name="' + (alias[field] || field) + '"]');
            if (input) input.setAttribute('aria-invalid', 'true');
          });
          const details = Object.values(body.errors || {}).join(' ');
          say([body.message, details].filter(Boolean).join(' ') || form.getAttribute('data-unavailable-message') || '', false);
          const firstInvalid = form.querySelector('[aria-invalid="true"]');
          if (firstInvalid && code === 422) firstInvalid.focus();
        })
        .catch(() => say(form.getAttribute('data-unavailable-message') || '', false))
        .finally(() => {
          form.classList.remove('is-sending');
          if (submit) submit.removeAttribute('aria-disabled');
        });
    });
  });
})();
