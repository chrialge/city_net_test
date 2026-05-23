<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PokéRole Dex</title>

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
            margin: 0 0 12px;
            font-size: clamp(1.25rem, 4vw, 1.75rem);
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

        .filters-wrap+.filters-wrap {
            margin-top: 10px;
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

        .filters-hint {
            display: block;
            font-size: 11px;
            font-weight: normal;
            text-transform: none;
            letter-spacing: 0;
            opacity: 0.85;
            margin: -4px 0 8px;
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

        .filter-chip[data-filter="electric"].active {
            color: #ca8a04;
        }

        .filter-chip[data-filter="fire"].active {
            color: #ea580c;
        }

        .filter-chip[data-filter="water"].active {
            color: #0284c7;
        }

        .filter-chip[data-filter="grass"].active {
            color: #16a34a;
        }

        .main-scroll {
            flex: 1;
            padding-bottom: env(safe-area-inset-bottom, 0);
        }

        .pokemon-list {
            padding: 12px;
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(3, 1fr);
        }

        .empty-state {
            display: none;
            grid-column: 1 / -1;
            text-align: center;
            padding: 32px 16px;
            color: #6b7280;
            font-size: 15px;
        }

        .empty-state.visible {
            display: block;
        }

        .pokemon-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            aspect-ratio: 1;
            padding: 10px 8px 12px;
            border-radius: 16px;
            background: #f9fafb;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.07);
            text-decoration: none;
            color: inherit;
            text-align: center;
            min-width: 0;
            transition: transform 0.15s, box-shadow 0.15s;
        }

        .pokemon-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .pokemon-card.hidden {
            display: none;
        }

        .card-image-wrap {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 0;
        }

        .pokemon-card img {
            width: 100%;
            max-width: 76px;
            height: auto;
            max-height: 76px;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.08));
        }

        .card-body {
            width: 100%;
            flex-shrink: 0;
        }

        .pokemon-title {
            margin: 0 0 6px;
            line-height: 1.2;
        }

        .pokemon-number {
            display: block;
            font-size: 10px;
            font-weight: 600;
            color: #9ca3af;
            margin-bottom: 2px;
        }

        .pokemon-name {
            display: block;
            font-size: 13px;
            font-weight: bold;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .type-list {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .type {
            font-size: 10px;
            padding: 3px 7px;
            border-radius: 999px;
            color: white;
            background: #6b7280;
            line-height: 1.2;
        }

        .type-electric {
            background: #facc15;
            color: #1f2937;
        }

        .type-fire {
            background: #f97316;
        }

        .type-water {
            background: #38bdf8;
        }

        .type-grass {
            background: #22c55e;
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
            padding: 14px 8px;
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
            .pokemon-list {
                gap: 12px;
                padding: 14px;
            }

            .pokemon-card img {
                max-width: 88px;
                max-height: 88px;
            }

            .pokemon-name {
                font-size: 14px;
            }

            .pokemon-number {
                font-size: 11px;
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

            .pokemon-list {
                padding: 20px 24px;
                grid-template-columns: repeat(4, 1fr);
                gap: 14px;
            }

            .pokemon-card {
                padding: 12px 10px 14px;
                border-radius: 18px;
            }

            .pokemon-card img {
                max-width: 96px;
                max-height: 96px;
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

            .pokemon-list {
                grid-template-columns: repeat(6, 1fr);
                gap: 16px;
                padding: 24px 28px 28px;
            }

            .pokemon-name {
                font-size: 15px;
            }

            .type {
                font-size: 11px;
                padding: 4px 8px;
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
            <h1>PokéRole Dex</h1>
            <input class="search-box" type="search" id="searchInput" placeholder="Cerca Pokémon..." autocomplete="off" aria-label="Cerca Pokémon">

            <div class="filters-wrap">
                <span class="filters-label">Tipologie</span>
                <span class="filters-hint">Tocca più tipi per combinarli (es. Fuoco + Acqua)</span>
                <div class="type-filters" role="group" aria-label="Filtra per tipologia">
                    <button type="button" class="filter-chip filter-chip--type active" data-filter-group="type" data-filter="all">Tutti</button>
                    <button type="button" class="filter-chip filter-chip--type" data-filter-group="type" data-filter="electric">Elettro</button>
                    <button type="button" class="filter-chip filter-chip--type" data-filter-group="type" data-filter="fire">Fuoco</button>
                    <button type="button" class="filter-chip filter-chip--type" data-filter-group="type" data-filter="water">Acqua</button>
                    <button type="button" class="filter-chip filter-chip--type" data-filter-group="type" data-filter="grass">Erba</button>
                </div>
            </div>

            <div class="filters-wrap">
                <span class="filters-label">Generazioni</span>
                <span class="filters-hint">Tocca più generazioni per combinarle (es. Gen I + Gen II)</span>
                <div class="type-filters" role="group" aria-label="Filtra per generazione">
                    <button type="button" class="filter-chip filter-chip--gen active" data-filter-group="generation" data-filter="all">Tutte</button>
                    <button type="button" class="filter-chip filter-chip--gen" data-filter-group="generation" data-filter="1">Gen I</button>
                    <button type="button" class="filter-chip filter-chip--gen" data-filter-group="generation" data-filter="2">Gen II</button>
                    <button type="button" class="filter-chip filter-chip--gen" data-filter-group="generation" data-filter="3">Gen III</button>
                    <button type="button" class="filter-chip filter-chip--gen" data-filter-group="generation" data-filter="4">Gen IV</button>
                    <button type="button" class="filter-chip filter-chip--gen" data-filter-group="generation" data-filter="5">Gen V</button>
                    <button type="button" class="filter-chip filter-chip--gen" data-filter-group="generation" data-filter="6">Gen VI</button>
                    <button type="button" class="filter-chip filter-chip--gen" data-filter-group="generation" data-filter="7">Gen VII</button>
                    <button type="button" class="filter-chip filter-chip--gen" data-filter-group="generation" data-filter="8">Gen VIII</button>
                    <button type="button" class="filter-chip filter-chip--gen" data-filter-group="generation" data-filter="9">Gen IX</button>
                </div>
            </div>
        </section>

        <div class="main-scroll">
            <section class="pokemon-list" id="pokemonList">

                <a href="#" class="pokemon-card" data-name="pikachu" data-types="electric" data-number="025" data-generation="1">
                    <div class="card-image-wrap">
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/25.png" alt="Pikachu" loading="lazy">
                    </div>
                    <div class="card-body">
                        <p class="pokemon-title">
                            <span class="pokemon-number">#025</span>
                            <span class="pokemon-name">Pikachu</span>
                        </p>
                        <div class="type-list">
                            <span class="type type-electric">Elettro</span>
                        </div>
                    </div>
                </a>

                <a href="#" class="pokemon-card" data-name="charmander" data-types="fire" data-number="004" data-generation="1">
                    <div class="card-image-wrap">
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/4.png" alt="Charmander" loading="lazy">
                    </div>
                    <div class="card-body">
                        <p class="pokemon-title">
                            <span class="pokemon-number">#004</span>
                            <span class="pokemon-name">Charmander</span>
                        </p>
                        <div class="type-list">
                            <span class="type type-fire">Fuoco</span>
                        </div>
                    </div>
                </a>

                <a href="#" class="pokemon-card" data-name="squirtle" data-types="water" data-number="007" data-generation="1">
                    <div class="card-image-wrap">
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/7.png" alt="Squirtle" loading="lazy">
                    </div>
                    <div class="card-body">
                        <p class="pokemon-title">
                            <span class="pokemon-number">#007</span>
                            <span class="pokemon-name">Squirtle</span>
                        </p>
                        <div class="type-list">
                            <span class="type type-water">Acqua</span>
                        </div>
                    </div>
                </a>

                <a href="#" class="pokemon-card" data-name="bulbasaur" data-types="grass" data-number="001" data-generation="1">
                    <div class="card-image-wrap">
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/1.png" alt="Bulbasaur" loading="lazy">
                    </div>
                    <div class="card-body">
                        <p class="pokemon-title">
                            <span class="pokemon-number">#001</span>
                            <span class="pokemon-name">Bulbasaur</span>
                        </p>
                        <div class="type-list">
                            <span class="type type-grass">Erba</span>
                        </div>
                    </div>
                </a>

                <a href="#" class="pokemon-card" data-name="charizard" data-types="fire" data-number="006" data-generation="1">
                    <div class="card-image-wrap">
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/6.png" alt="Charizard" loading="lazy">
                    </div>
                    <div class="card-body">
                        <p class="pokemon-title">
                            <span class="pokemon-number">#006</span>
                            <span class="pokemon-name">Charizard</span>
                        </p>
                        <div class="type-list">
                            <span class="type type-fire">Fuoco</span>
                        </div>
                    </div>
                </a>

                <a href="#" class="pokemon-card" data-name="wartortle" data-types="water" data-number="008" data-generation="1">
                    <div class="card-image-wrap">
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/8.png" alt="Wartortle" loading="lazy">
                    </div>
                    <div class="card-body">
                        <p class="pokemon-title">
                            <span class="pokemon-number">#008</span>
                            <span class="pokemon-name">Wartortle</span>
                        </p>
                        <div class="type-list">
                            <span class="type type-water">Acqua</span>
                        </div>
                    </div>
                </a>

                <a href="#" class="pokemon-card" data-name="chikorita" data-types="grass" data-number="152" data-generation="2">
                    <div class="card-image-wrap">
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/152.png" alt="Chikorita" loading="lazy">
                    </div>
                    <div class="card-body">
                        <p class="pokemon-title">
                            <span class="pokemon-number">#152</span>
                            <span class="pokemon-name">Chikorita</span>
                        </p>
                        <div class="type-list">
                            <span class="type type-grass">Erba</span>
                        </div>
                    </div>
                </a>

                <a href="#" class="pokemon-card" data-name="cyndaquil" data-types="fire" data-number="155" data-generation="2">
                    <div class="card-image-wrap">
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/155.png" alt="Cyndaquil" loading="lazy">
                    </div>
                    <div class="card-body">
                        <p class="pokemon-title">
                            <span class="pokemon-number">#155</span>
                            <span class="pokemon-name">Cyndaquil</span>
                        </p>
                        <div class="type-list">
                            <span class="type type-fire">Fuoco</span>
                        </div>
                    </div>
                </a>

                <a href="#" class="pokemon-card" data-name="totodile" data-types="water" data-number="158" data-generation="2">
                    <div class="card-image-wrap">
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/158.png" alt="Totodile" loading="lazy">
                    </div>
                    <div class="card-body">
                        <p class="pokemon-title">
                            <span class="pokemon-number">#158</span>
                            <span class="pokemon-name">Totodile</span>
                        </p>
                        <div class="type-list">
                            <span class="type type-water">Acqua</span>
                        </div>
                    </div>
                </a>

                <a href="#" class="pokemon-card" data-name="treecko" data-types="grass" data-number="252" data-generation="3">
                    <div class="card-image-wrap">
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/252.png" alt="Treecko" loading="lazy">
                    </div>
                    <div class="card-body">
                        <p class="pokemon-title">
                            <span class="pokemon-number">#252</span>
                            <span class="pokemon-name">Treecko</span>
                        </p>
                        <div class="type-list">
                            <span class="type type-grass">Erba</span>
                        </div>
                    </div>
                </a>

                <a href="#" class="pokemon-card" data-name="torchic" data-types="fire" data-number="255" data-generation="3">
                    <div class="card-image-wrap">
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/255.png" alt="Torchic" loading="lazy">
                    </div>
                    <div class="card-body">
                        <p class="pokemon-title">
                            <span class="pokemon-number">#255</span>
                            <span class="pokemon-name">Torchic</span>
                        </p>
                        <div class="type-list">
                            <span class="type type-fire">Fuoco</span>
                        </div>
                    </div>
                </a>

                <a href="#" class="pokemon-card" data-name="mudkip" data-types="water" data-number="258" data-generation="3">
                    <div class="card-image-wrap">
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/258.png" alt="Mudkip" loading="lazy">
                    </div>
                    <div class="card-body">
                        <p class="pokemon-title">
                            <span class="pokemon-number">#258</span>
                            <span class="pokemon-name">Mudkip</span>
                        </p>
                        <div class="type-list">
                            <span class="type type-water">Acqua</span>
                        </div>
                    </div>
                </a>

            </section>

            <p class="empty-state" id="emptyState">Nessun Pokémon trovato con i filtri selezionati.</p>
        </div>

        <nav class="bottom-nav">
            <a href="#" class="active">Dex</a>
            <a href="#">Tipi</a>
            <a href="#">Team</a>
        </nav>

    </main>

    <script>
        (function() {
            var searchInput = document.getElementById('searchInput');
            var filterChips = document.querySelectorAll('.filter-chip');
            var typeChips = document.querySelectorAll('.filter-chip--type');
            var typeAllChip = document.querySelector('.filter-chip--type[data-filter="all"]');
            var genChips = document.querySelectorAll('.filter-chip--gen');
            var genAllChip = document.querySelector('.filter-chip--gen[data-filter="all"]');
            var cards = document.querySelectorAll('.pokemon-card');
            var emptyState = document.getElementById('emptyState');
            var selectedTypes = [];
            var selectedGenerations = [];

            function isTypeFilterAll() {
                return typeAllChip.classList.contains('active');
            }

            function syncSelectedTypes() {
                selectedTypes = [];
                typeChips.forEach(function(chip) {
                    var value = chip.getAttribute('data-filter');
                    if (value !== 'all' && chip.classList.contains('active')) {
                        selectedTypes.push(value);
                    }
                });
            }

            function activateTypeFilterAll() {
                typeChips.forEach(function(chip) {
                    chip.classList.toggle('active', chip.getAttribute('data-filter') === 'all');
                });
                selectedTypes = [];
            }

            function isGenFilterAll() {
                return genAllChip.classList.contains('active');
            }

            function syncSelectedGenerations() {
                selectedGenerations = [];
                genChips.forEach(function(chip) {
                    var value = chip.getAttribute('data-filter');
                    if (value !== 'all' && chip.classList.contains('active')) {
                        selectedGenerations.push(value);
                    }
                });
            }

            function activateGenFilterAll() {
                genChips.forEach(function(chip) {
                    chip.classList.toggle('active', chip.getAttribute('data-filter') === 'all');
                });
                selectedGenerations = [];
            }

            function cardMatchesType(typesAttr) {
                if (isTypeFilterAll() || selectedTypes.length === 0) {
                    return true;
                }
                var cardTypes = typesAttr.split(',');
                for (var i = 0; i < selectedTypes.length; i++) {
                    if (cardTypes.indexOf(selectedTypes[i]) !== -1) {
                        return true;
                    }
                }
                return false;
            }

            function cardMatchesGeneration(generation) {
                if (isGenFilterAll() || selectedGenerations.length === 0) {
                    return true;
                }
                for (var i = 0; i < selectedGenerations.length; i++) {
                    if (generation === selectedGenerations[i]) {
                        return true;
                    }
                }
                return false;
            }

            function cardMatches(card) {
                var name = (card.getAttribute('data-name') || '').toLowerCase();
                var types = (card.getAttribute('data-types') || '').toLowerCase();
                var generation = card.getAttribute('data-generation') || '';
                var query = searchInput.value.trim().toLowerCase();

                var typeOk = cardMatchesType(types);
                var genOk = cardMatchesGeneration(generation);
                var number = (card.getAttribute('data-number') || '').toLowerCase();
                var displayName = card.querySelector('.pokemon-name').textContent.toLowerCase();
                var searchOk = !query || name.indexOf(query) !== -1 ||
                    displayName.indexOf(query) !== -1 ||
                    number.indexOf(query.replace('#', '')) !== -1;

                return typeOk && genOk && searchOk;
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

            function handleTypeChipClick(chip) {
                var value = chip.getAttribute('data-filter');

                if (value === 'all') {
                    activateTypeFilterAll();
                } else {
                    typeAllChip.classList.remove('active');
                    chip.classList.toggle('active');
                    syncSelectedTypes();
                    if (selectedTypes.length === 0) {
                        activateTypeFilterAll();
                    }
                }

                applyFilters();
            }

            function handleGenerationChipClick(chip) {
                var value = chip.getAttribute('data-filter');

                if (value === 'all') {
                    activateGenFilterAll();
                } else {
                    genAllChip.classList.remove('active');
                    chip.classList.toggle('active');
                    syncSelectedGenerations();
                    if (selectedGenerations.length === 0) {
                        activateGenFilterAll();
                    }
                }

                applyFilters();
            }

            searchInput.addEventListener('input', applyFilters);

            filterChips.forEach(function(chip) {
                chip.addEventListener('click', function() {
                    var group = chip.getAttribute('data-filter-group');

                    if (group === 'type') {
                        handleTypeChipClick(chip);
                    } else if (group === 'generation') {
                        handleGenerationChipClick(chip);
                    }
                });
            });
        })();
    </script>

</body>

</html>