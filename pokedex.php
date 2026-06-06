<?php

require_once   './assets/controller/pokemon_tipologie.php';
require_once './assets/controller/pokemon.php';
require_once './assets/helper/function.php';

$maxPokemon = 649;
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$loaded = isset($_GET['loaded']) ? intval($_GET['loaded']) : 50;
if ($loaded < 50) {
    $loaded = 50;
}
if ($loaded > $maxPokemon) {
    $loaded = $maxPokemon;
}

$arrayPokemon = [];

if ($searchQuery !== '') {
    // search mode: use name substring or id-prefix
    $matches = searchPokemonByQuery($searchQuery, 200);
    foreach ($matches as $entry) {
        $detail = getPokemonDetailFromPokeAPI($entry['id']);
        if ($detail === null) continue;

        $numeroPokemon = '#' . str_pad($detail['id'], 3, '0', STR_PAD_LEFT);
        $shortInfo = getShortInfoPokemon($numeroPokemon);
        if ($shortInfo === null) continue;

        $arrayPokemon[] = [
            'numeroPokedex' => $shortInfo['numeroPokedex'],
            'nome' => $shortInfo['nome'],
            'img' => $detail['img'],
            'tipologiaNome' => $shortInfo['tipologiaNome'],
            'colorePrincipale' => $shortInfo['colorePrincipale'],
            'generazione' => $shortInfo['generazione']
        ];
    }

    $canLoadMore = false;
    $nextLoaded = $loaded;
} else {
    $pokemonData = getPokemonFromPokeAPI($loaded, 0);

    $canLoadMore = $loaded < $maxPokemon;
    $nextLoaded = min($maxPokemon, $loaded + 50);

    foreach ($pokemonData as $pokemon) {
        $numeroPokemon = '#' . str_pad($pokemon['id'], 3, '0', STR_PAD_LEFT);
        $shortInfo = getShortInfoPokemon($numeroPokemon);

        if ($shortInfo === null) {
            continue;
        }

        $newPokemon = [
            'numeroPokedex' => $shortInfo['numeroPokedex'],
            'nome' => $shortInfo['nome'],
            'img' => $pokemon['img'],
            'tipologiaNome' => $shortInfo['tipologiaNome'],
            'colorePrincipale' => $shortInfo['colorePrincipale'],
            'generazione' => $shortInfo['generazione']
        ];

        $arrayPokemon[] = $newPokemon;
    }
}

$tipologie = getAllPokemonTipologie();

/*
$tipologia = getPokemonTipologieById(1);
echo  "<pre>";
print_r($tipologia);
echo "</pre>";
die();*/

?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PokéRole Dex</title>

    <link rel="stylesheet" href="./assets/css/pokedex.css?v=2">
</head>

<body>

    <main class="app">

        <section class="topbar">
            <h1>PokéRole Dex</h1>
            <form method="get" action="./pokedex.php">
                <input class="search-box" type="search" id="searchInput" name="q" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Cerca Pokémon..." autocomplete="off" aria-label="Cerca Pokémon">
            </form>

            <div class="filters-wrap">
                <span class="filters-label">Tipologie</span>
                <span class="filters-hint">Tocca più tipi per combinarli (es. Fuoco + Acqua)</span>
                <div class="type-filters" role="group" aria-label="Filtra per tipologia">
                    <button type="button" class="filter-chip filter-chip--type active" data-filter-group="type" data-filter="all">Tutti</button>
                    <?php foreach ($tipologie as $tipologia) : ?>
                        <button type="button" class="filter-chip filter-chip--type" data-filter-group="type" data-filter="<?= $tipologia['nome'] ?>"><?= $tipologia['nome'] ?></button>
                    <?php endforeach; ?>
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
                </div>
            </div>
        </section>

        <div class="main-scroll">
            <section class="pokemon-list" id="pokemonList">








            </section>

            <div class="load-more-box">
                <div class="load-more-inner">
                    <span class="load-more-info" id="loadMoreInfo">Mostrati <?= count($arrayPokemon) ?> / <?= $maxPokemon ?> Pokémon</span>
                    <button id="loadMoreBtn" class="load-more-button" data-next="<?= $nextLoaded ?>" <?= $canLoadMore ? '' : 'disabled' ?>><?= $canLoadMore ? 'Carica altri 50' : 'Hai caricato tutti i Pokémon disponibili' ?></button>
                </div>
            </div>

            <p class="empty-state" id="emptyState">Nessun Pokémon trovato con i filtri selezionati.</p>
        </div>

        <nav class="bottom-nav">
            <a href="#" class="active">Dex</a>
            <a href="#">Nature</a>
            <a href="#">Team</a>
        </nav>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var searchInput = document.getElementById('searchInput');
            var pokemonList = document.getElementById('pokemonList');
            var loadMoreBtn = document.getElementById('loadMoreBtn');
            var loadMoreInfo = document.getElementById('loadMoreInfo');
            var searchForm = document.querySelector('.topbar form');
            var typeChips = document.querySelectorAll('.filter-chip--type');
            var genChips = document.querySelectorAll('.filter-chip--gen');
            var initialLoaded = <?= $loaded ?>;
            var currentLoaded = initialLoaded;
            var debounceTimer = null;

            function getSelectedFilters(chips) {
                var values = [];
                chips.forEach(function(chip) {
                    var value = chip.getAttribute('data-filter');
                    if (value !== 'all' && chip.classList.contains('active')) {
                        values.push(value);
                    }
                });
                return values;
            }

            function isFilterActive(chips) {
                var active = 0;
                chips.forEach(function(chip) {
                    if (chip.getAttribute('data-filter') !== 'all' && chip.classList.contains('active')) {
                        active++;
                    }
                });
                return active > 0;
            }

            function buildParams(loaded, q, types, gens) {
                var params = new URLSearchParams();
                if (q) params.set('q', q);
                if (types && types.length) params.set('types', types.join(','));
                if (gens && gens.length) params.set('gens', gens.join(','));
                if (loaded) params.set('loaded', loaded);
                return params;
            }

            function updateLoadMoreControls(data) {
                loadMoreInfo.textContent = 'Mostrati ' + data.shown + ' / ' + data.max + ' Pokémon';
                if (data.canLoadMore) {
                    loadMoreBtn.style.display = 'inline-flex';
                    loadMoreBtn.disabled = false;
                    loadMoreBtn.classList.remove('disabled');
                    loadMoreBtn.dataset.next = data.nextLoaded;
                    loadMoreBtn.textContent = 'Carica altri 50';
                } else {
                    loadMoreBtn.style.display = 'none';
                }
            }

            function renderCards(data) {
                pokemonList.innerHTML = data.html;
                updateLoadMoreControls(data);
                currentLoaded = parseInt(data.nextLoaded, 10) - 50;
            }

            function fetchCards(loaded, q, types, gens) {
                var params = buildParams(loaded, q, types, gens);
                fetch('./assets/controller/load_cards.php?' + params.toString())
                    .then(function(r) {
                        return r.json();
                    })
                    .then(function(data) {
                        renderCards(data);
                    })
                    .catch(function() {
                        pokemonList.innerHTML = '<p class="empty-state visible">Errore nel caricamento.</p>';
                        loadMoreBtn.style.display = 'none';
                    });
            }

            function refreshWithFilters() {
                var q = searchInput.value.trim();
                var selectedTypes = getSelectedFilters(typeChips);
                var selectedGens = getSelectedFilters(genChips);
                var filterActive = q !== '' || selectedTypes.length > 0 || selectedGens.length > 0;
                var loaded = filterActive ? 50 : currentLoaded || initialLoaded;
                fetchCards(loaded, q, selectedTypes, selectedGens);
            }

            function toggleChip(chip) {
                var value = chip.getAttribute('data-filter');
                var group = chip.getAttribute('data-filter-group');
                var groupChips = group === 'type' ? typeChips : genChips;
                var allChip = chip.closest('.type-filters') ? chip.closest('.type-filters').querySelector('.filter-chip[data-filter="all"]') : null;

                if (value === 'all') {
                    groupChips.forEach(function(item) {
                        item.classList.toggle('active', item.getAttribute('data-filter') === 'all');
                    });
                } else {
                    if (allChip) allChip.classList.remove('active');
                    chip.classList.toggle('active');
                    var anyActive = false;
                    groupChips.forEach(function(item) {
                        if (item.getAttribute('data-filter') !== 'all' && item.classList.contains('active')) {
                            anyActive = true;
                        }
                    });
                    if (!anyActive && allChip) {
                        allChip.classList.add('active');
                    }
                }
            }

            function attachFilterEvents() {
                typeChips.forEach(function(chip) {
                    chip.addEventListener('click', function() {
                        toggleChip(chip);
                        refreshWithFilters();
                    });
                });
                genChips.forEach(function(chip) {
                    chip.addEventListener('click', function() {
                        toggleChip(chip);
                        refreshWithFilters();
                    });
                });
            }

            attachFilterEvents();

            function debounceRefresh() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    refreshWithFilters();
                }, 300);
            }

            searchInput.addEventListener('input', function() {
                debounceRefresh();
            });

            if (searchForm) {
                searchForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    refreshWithFilters();
                });
            }

            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var q = searchInput.value.trim();
                    var selectedTypes = getSelectedFilters(typeChips);
                    var selectedGens = getSelectedFilters(genChips);
                    if (q !== '' || selectedTypes.length > 0 || selectedGens.length > 0) {
                        return;
                    }
                    if (this.disabled) return;
                    var next = parseInt(this.dataset.next, 10) || (currentLoaded + 50);
                    fetchCards(next, '', [], []);
                });
            }

            // initial load
            var initialQ = '<?= addslashes($searchQuery) ?>';
            var initialTypes = [];
            var initialGens = [];
            fetchCards(initialLoaded, initialQ, initialTypes, initialGens);
        });
    </script>

</body>

</html>