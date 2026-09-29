(() => {
  'use strict';

  const grid = document.querySelector('[data-book-grid]');
  const bar = document.querySelector('[data-book-filters]');
  if (!grid || !bar) return;

  const search = bar.querySelector('[data-book-search]');
  const murcianoOnly = bar.querySelector('[data-book-murciano-filter]');
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
    let visible = 0;

    cards.forEach((card) => {
      const matchesQuery = !query
        || (card.dataset.bookName || '').includes(query)
        || (card.dataset.bookAuthor || '').includes(query);
      const matchesMurciano = !onlyMurciano || card.dataset.bookMurciano === '1';
      const show = matchesQuery && matchesMurciano;
      card.hidden = !show;
      if (show) visible += 1;
    });

    if (empty) empty.hidden = visible > 0;
  };

  if (search) search.addEventListener('input', apply);
  if (murcianoOnly) murcianoOnly.addEventListener('change', apply);
})();
