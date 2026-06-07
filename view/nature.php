<?php

require_once __DIR__ . "/../assets/controller/nature.php";


$nature = getAllNature();





?>
<!DOCTYPE html>
<html lang="it">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PokéRole Dex - Nature</title>

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f4f6fa;
      color: #1f2937;
    }

    .app {
      width: 100%;
      max-width: 100%;
      margin: 0 auto;
      min-height: 100vh;
      min-height: 100dvh;
      background: #ffffff;
      display: flex;
      flex-direction: column;
    }

    .topbar {
      position: sticky;
      top: 0;
      z-index: 10;
      background: #ef4444;
      color: white;
      padding: 14px 16px 16px;
      border-bottom-left-radius: 22px;
      border-bottom-right-radius: 22px;
      box-shadow: 0 4px 20px rgba(239, 68, 68, 0.25);
    }

    .topbar h1 {
      margin: 0 0 6px;
      font-size: clamp(1.25rem, 4vw, 1.75rem);
    }

    .topbar-subtitle {
      margin: 0 0 12px;
      font-size: 13px;
      line-height: 1.35;
      opacity: 0.9;
    }

    .search-box {
      width: 100%;
      border: none;
      border-radius: 14px;
      padding: 12px 14px;
      font-size: 16px;
      outline: none;
      -webkit-appearance: none;
    }

    .filters-wrap {
      margin-top: 12px;
    }

    .filters-label {
      display: block;
      font-size: 12px;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      opacity: 0.9;
      margin-bottom: 8px;
    }

    .type-filters {
      display: flex;
      gap: 8px;
      overflow-x: auto;
      padding-bottom: 4px;
      -webkit-overflow-scrolling: touch;
      scrollbar-width: none;
    }

    .type-filters::-webkit-scrollbar {
      display: none;
    }

    .filter-chip {
      flex: 0 0 auto;
      border: 2px solid rgba(255, 255, 255, 0.5);
      background: rgba(255, 255, 255, 0.15);
      color: white;
      font-size: 13px;
      font-weight: 600;
      padding: 8px 14px;
      border-radius: 999px;
      cursor: pointer;
      white-space: nowrap;
      transition: background 0.15s, border-color 0.15s, transform 0.1s;
    }

    .filter-chip:hover {
      background: rgba(255, 255, 255, 0.28);
    }

    .filter-chip.active {
      background: white;
      color: #ef4444;
      border-color: white;
    }

    .main-scroll {
      flex: 1;
      padding-bottom: env(safe-area-inset-bottom, 0);
    }

    .nature-list {
      padding: 12px;
      display: grid;
      gap: 10px;
      grid-template-columns: 1fr;
    }

    .nature-card {
      display: flex;
      flex-direction: column;
      gap: 10px;
      padding: 14px;
      border-radius: 18px;
      background: #f9fafb;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.07);
      text-decoration: none;
      color: inherit;
      min-width: 0;
      transition: transform 0.15s, box-shadow 0.15s;
    }

    .nature-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .nature-card.hidden {
      display: none;
    }

    .nature-header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 12px;
    }

    .nature-title {
      margin: 0;
      line-height: 1.2;
      min-width: 0;
    }

    .nature-name-it {
      display: block;
      font-size: 17px;
      font-weight: bold;
      color: #111827;
    }

    .nature-name-original {
      display: block;
      margin-top: 3px;
      font-size: 12px;
      font-weight: 600;
      color: #9ca3af;
    }

    .nature-badge {
      flex: 0 0 auto;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 38px;
      height: 38px;
      border-radius: 999px;
      background: #ef4444;
      color: white;
      font-size: 16px;
      font-weight: bold;
      box-shadow: 0 4px 10px rgba(239, 68, 68, 0.24);
    }

    .nature-description {
      margin: 0;
      font-size: 13px;
      line-height: 1.45;
      color: #4b5563;
    }

    .effect-box {
      border-radius: 14px;
      padding: 10px 12px;
      background: #ffffff;
      border: 1px solid #e5e7eb;
    }

    .effect-label {
      display: block;
      margin-bottom: 4px;
      font-size: 10px;
      font-weight: bold;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      color: #ef4444;
    }

    .effect-text {
      margin: 0;
      font-size: 13px;
      line-height: 1.4;
      color: #1f2937;
    }

    .empty-state {
      display: none;
      text-align: center;
      padding: 32px 16px;
      color: #6b7280;
      font-size: 15px;
    }

    .empty-state.visible {
      display: block;
    }

    .bottom-nav {
      position: sticky;
      bottom: 0;
      z-index: 10;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      background: white;
      border-top: 1px solid #e5e7eb;
      padding-bottom: env(safe-area-inset-bottom, 0);
    }

    .bottom-nav a {
      padding: 14px 6px;
      text-align: center;
      text-decoration: none;
      color: #6b7280;
      font-size: 13px;
    }

    .bottom-nav a.active {
      color: #ef4444;
      font-weight: bold;
    }

    @media (min-width: 380px) {
      .nature-list {
        gap: 12px;
        padding: 14px;
      }

      .nature-name-it {
        font-size: 18px;
      }
    }

    /* Tablet */
    @media (min-width: 600px) {
      .app {
        max-width: 720px;
        box-shadow: 0 0 40px rgba(0, 0, 0, 0.06);
      }

      .topbar {
        padding: 18px 24px 20px;
        border-bottom-left-radius: 28px;
        border-bottom-right-radius: 28px;
      }

      .type-filters {
        flex-wrap: wrap;
        overflow-x: visible;
      }

      .nature-list {
        padding: 20px 24px;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
      }

      .nature-card {
        padding: 16px;
        border-radius: 18px;
      }
    }

    /* Desktop */
    @media (min-width: 1024px) {
      body {
        padding: 24px 16px;
      }

      .app {
        max-width: 1100px;
        border-radius: 24px;
        overflow: hidden;
        min-height: calc(100vh - 48px);
      }

      .nature-list {
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        padding: 24px 28px 28px;
      }

      .nature-name-it {
        font-size: 19px;
      }

      .bottom-nav {
        max-width: 1100px;
        margin: 0 auto;
        border-radius: 0 0 24px 24px;
      }
    }
  </style>
</head>

<body>

  <main class="app">

    <section class="topbar">
      <h1>PokéRole - Natura</h1>
      <p class="topbar-subtitle">Consulta le nature e l'effetto speciale collegato all'abilità.</p>

      <input class="search-box" type="search" id="searchInput" placeholder="Cerca natura..." autocomplete="off" aria-label="Cerca natura">

    </section>

    <div class="main-scroll">
      <section class="nature-list" id="natureList">

        <?php foreach ($nature as $natura) : ?>
          <article class="nature-card" data-name="<?= str_replace("/", " ", $natura['nomi']) ?>" data-category="offensiva">
            <div class="nature-header">
              <h2 class="nature-title">
                <span class="nature-name-it"><?= $natura['nome'] ?></span>
                <span class="nature-name-original"><?= $natura['nomi'] ?></span>
              </h2>
              <span class="nature-badge"><?= $natura['confidenza'] ?></span>
            </div>
            <div class="effect-box">
              <span class="effect-label">Descrizione</span>
              <p class="effect-text"><?= $natura['descrizione'] ?></p>
            </div>
          </article>
        <?php endforeach; ?>
      </section>

      <p class="empty-state" id="emptyState">Nessuna natura trovata con i filtri selezionati.</p>
    </div>

    <nav class="bottom-nav">
      <a href="../pokedex.php">Dex</a>
      <a href="nature.php" class="active">Nature</a>
      <a href="team.php">Team</a>
    </nav>

  </main>

  <script>
    (function() {
      var searchInput = document.getElementById('searchInput');
      var filterChips = document.querySelectorAll('.filter-chip');
      var allChip = document.querySelector('.filter-chip[data-filter="all"]');
      var cards = document.querySelectorAll('.nature-card');
      var emptyState = document.getElementById('emptyState');
      var selectedFilter = 'all';

      function cardMatches(card) {
        var query = searchInput.value.trim().toLowerCase();
        var name = (card.getAttribute('data-name') || '').toLowerCase();
        var category = card.getAttribute('data-category') || '';
        var text = card.textContent.toLowerCase();

        var filterOk = selectedFilter === 'all' || category === selectedFilter;
        var searchOk = !query || name.indexOf(query) !== -1 || text.indexOf(query) !== -1;

        return filterOk && searchOk;
      }

      function applyFilters() {
        var visible = 0;

        cards.forEach(function(card) {
          var show = cardMatches(card);
          card.classList.toggle('hidden', !show);
          if (show) visible++;
        });

        emptyState.classList.toggle('visible', visible === 0);
      }

      searchInput.addEventListener('input', applyFilters);

      filterChips.forEach(function(chip) {
        chip.addEventListener('click', function() {
          selectedFilter = chip.getAttribute('data-filter') || 'all';

          filterChips.forEach(function(item) {
            item.classList.toggle('active', item === chip);
          });

          if (!selectedFilter) {
            selectedFilter = 'all';
            allChip.classList.add('active');
          }

          applyFilters();
        });
      });
    })();
  </script>

</body>

</html>