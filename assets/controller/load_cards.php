<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../controller/pokemon.php';
require_once __DIR__ . '/../controller/pokemon_tipologie.php';
require_once __DIR__ . '/../helper/function.php';

$maxPokemon = 649;
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$loaded = isset($_GET['loaded']) ? intval($_GET['loaded']) : 50;
$activeTypes = isset($_GET['types']) ? array_filter(array_map('trim', explode(',', $_GET['types']))) : [];
$activeGens = isset($_GET['gens']) ? array_filter(array_map('trim', explode(',', $_GET['gens']))) : [];

if ($loaded < 50) $loaded = 50;
if ($loaded > $maxPokemon) $loaded = $maxPokemon;

$selectedTypes = array_map('strtolower', $activeTypes);
$selectedGens = array_map('strtolower', $activeGens);

$filterMode = $searchQuery !== '' || !empty($selectedTypes) || !empty($selectedGens);
$arrayPokemon = [];

if ($filterMode) {
    if ($searchQuery !== '') {
        $matches = searchPokemonByQuery($searchQuery, 649);
    } else {
        $matches = getPokemonNamesListFromPokeAPI(649, 0);
    }

    $numeroPokedexList = [];
    foreach ($matches as $entry) {
        $numeroPokedexList[] = '#' . str_pad($entry['id'], 3, '0', STR_PAD_LEFT);
    }

    $shortInfos = getShortInfoPokemonList($numeroPokedexList);

    foreach ($matches as $entry) {
        $numeroPokemon = '#' . str_pad($entry['id'], 3, '0', STR_PAD_LEFT);
        if (!isset($shortInfos[$numeroPokemon])) {
            continue;
        }

        $shortInfo = $shortInfos[$numeroPokemon];
        $tipologie = array_map('trim', explode(',', strtolower($shortInfo['tipologiaNome'])));
        $generazione = strtolower($shortInfo['generazione']);

        if (!empty($selectedTypes)) {
            $matchType = false;
            foreach ($selectedTypes as $type) {
                if (in_array(strtolower($type), $tipologie, true)) {
                    $matchType = true;
                    break;
                }
            }
            if (!$matchType) {
                continue;
            }
        }

        if (!empty($selectedGens) && !in_array($generazione, $selectedGens, true)) {
            continue;
        }

        $arrayPokemon[] = [
            'numeroPokedex' => $shortInfo['numeroPokedex'],
            'nome' => $shortInfo['nome'],
            'img' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/' . intval($entry['id']) . '.png',
            'tipologiaNome' => $shortInfo['tipologiaNome'],
            'colorePrincipale' => $shortInfo['colorePrincipale'],
            'generazione' => $shortInfo['generazione']
        ];
    }

    $canLoadMore = false;
    $shown = count($arrayPokemon);
} else {
    $pokemonData = getPokemonFromPokeAPI($loaded, 0);
    foreach ($pokemonData as $pokemon) {
        $numeroPokemon = '#' . str_pad($pokemon['id'], 3, '0', STR_PAD_LEFT);
        $shortInfo = getShortInfoPokemon($numeroPokemon);
        if ($shortInfo === null) continue;

        $arrayPokemon[] = [
            'numeroPokedex' => $shortInfo['numeroPokedex'],
            'nome' => $shortInfo['nome'],
            'img' => $pokemon['img'],
            'tipologiaNome' => $shortInfo['tipologiaNome'],
            'colorePrincipale' => $shortInfo['colorePrincipale'],
            'generazione' => $shortInfo['generazione']
        ];
    }

    $canLoadMore = $loaded < $maxPokemon;
    $shown = count($arrayPokemon);
}

// render partial to buffer
ob_start();
// $arrayPokemon is expected by the partial
include __DIR__ . '/../partials/pokemon_card.php';
$cardsHtml = ob_get_clean();

echo json_encode([
    'html' => $cardsHtml,
    'canLoadMore' => $canLoadMore,
    'shown' => $shown,
    'max' => $maxPokemon,
    'nextLoaded' => min($maxPokemon, $loaded + 50)
]);

exit;
