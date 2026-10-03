(() => {
  'use strict';

  const grid = document.querySelector('[data-book-grid]');
  const bar = document.querySelector('[data-book-filters]');
  if (!grid || !bar) return;

  const search = bar.querySelector('[data-book-search]');
  const murcianoOnly = bar.querySelector('[data-book-murciano-filter]');
  const category = bar.querySelector('[data-book-category]');
  const empty = document.querySelector('[data-book-empty]');
  const cards = Array.from(grid.querySelectorAll('[data-book]'));

  const normalize = (text) => text
    .normalize('NFD')
    .replace(/[̀-ͯ]/g, '')
    .toLowerCase()
    .trim();

  const apply = () => {
    const query = search ? normalize(search.value) : '';
    const onlyMurciano = murcianoOnly ? murcianoOnly.checked : false;
    const wantedCategory = category ? category.value : '';
    let visible = 0;

    cards.forEach((card) => {
      const matchesQuery = !query
        || (card.dataset.bookName || '').includes(query)
        || (card.dataset.bookAuthor || '').includes(query);
      const matchesMurciano = !onlyMurciano || card.dataset.bookMurciano === '1';
      const matchesCategory = !wantedCategory || card.dataset.bookCategory === wantedCategory;
      const show = matchesQuery && matchesMurciano && matchesCategory;
      card.hidden = !show;
      if (show) visible += 1;
    });

    if (empty) empty.hidden = visible > 0;
  };

  if (search) search.addEventListener('input', apply);
  if (murcianoOnly) murcianoOnly.addEventListener('change', apply);
  if (category) category.addEventListener('change', apply);

  // Aplica ya al cargar: la categoría puede venir preseleccionada desde una pestaña de la portada.
  apply();
})();
