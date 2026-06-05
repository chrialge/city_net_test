<?php
require_once   './assets/controller/pokemon_tipologie.php';
require_once './assets/controller/pokemon.php';
require_once './assets/helper/function.php';

$pokemonData = getPokemonFromPokeAPI(50, 0);



$arrayPokemon = [];

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
        'colorePrincipale' => $shortInfo['colorePrincipale']
    ];

    $arrayPokemon[] = $newPokemon;
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

    <link rel="stylesheet" href="./assets/css/pokedex.css">
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
                    <button type="button" class="filter-chip filter-chip--gen" data-filter-group="generation" data-filter="6">Gen VI</button>
                    <button type="button" class="filter-chip filter-chip--gen" data-filter-group="generation" data-filter="7">Gen VII</button>
                    <button type="button" class="filter-chip filter-chip--gen" data-filter-group="generation" data-filter="8">Gen VIII</button>
                    <button type="button" class="filter-chip filter-chip--gen" data-filter-group="generation" data-filter="9">Gen IX</button>
                </div>
            </div>
        </section>

        <div class="main-scroll">
            <section class="pokemon-list" id="pokemonList">


                <?php foreach ($arrayPokemon as $pokemon) : ?>
                    <?php
                    $tipologie = array_map('trim', explode(',', $pokemon['tipologiaNome']));
                    $colori = array_map('trim', explode(',', $pokemon['colorePrincipale']));
                    ?>
                    <a href="#" class="pokemon-card" data-name="<?= $pokemon['nome'] ?>" data-types="<?= $pokemon['tipologiaNome'] ?>" data-number="<?= $pokemon['numeroPokedex'] ?>" data-generation="1">
                        <div class="card-image-wrap">
                            <img src="<?= $pokemon['img'] ?>" alt="<?= $pokemon['nome'] ?>" loading="lazy">
                        </div>
                        <div class="card-body">
                            <p class="pokemon-title">
                                <span class="pokemon-number"><?= $pokemon['numeroPokedex'] ?></span>
                                <span class="pokemon-name"><?= $pokemon['nome'] ?></span>
                            </p>
                            <div class="type-list">
                                <?php foreach ($tipologie as $idx => $tipologia) : ?>
                                    <span class="type" style="background-color: <?= $colori[$idx] ?? '#6b7280' ?>;"><?= trim($tipologia) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>




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
                    var value = chip.getAttribute('data-filter').toLowerCase();
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
                var cardTypes = typesAttr.split(',').map(function(t) {
                    return t.trim().toLowerCase();
                });
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