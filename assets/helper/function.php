<?php

// funzione che fa cicclare per tutti i parametri con l'operatore spreds `...` -->(che non gli interessa il numro di parametri) e poi blocca il programma

/**
 * funzione che fa scorere i dati e poi blocca il programma
 * @param $params con lo spread puo passare una moltitudine di parametri
 */
function dd(...$params)
{
    // fa scorere tutti i parametri
    foreach ($params as $param) {
        // li dampa per ogni parametro
        var_dump($param);
    }
    // blocca il programma
    die;
}

/**
 * Recupera la lista dei Pokémon da PokeAPI
 * @param int $limit Numero di Pokémon da recuperare
 * @param int $offset Offset per la paginazione
 * @return array Array di Pokémon con id, url, name e img
 */
function getPokemonFromPokeAPI($limit = 50, $offset = 0)
{
    // Fetch the list of Pokémon
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://pokeapi.co/api/v2/pokemon?limit={$limit}&offset={$offset}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $decodedResponse = json_decode($response, true);
    $pokemonList = $decodedResponse['results'];
    $pokemonData = [];

    // Fetch detailed info for each Pokémon
    foreach ($pokemonList as $pokemon) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $pokemon['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $decodedPokemon = json_decode($response, true);
        curl_close($ch);

        $data = [
            'id' => $decodedPokemon['id'],
            'url' => $pokemon['url'],
            'name' => $pokemon['name'],
            'img' => $decodedPokemon['sprites']['front_default']
        ];

        $pokemonData[] = $data;
    }

    return $pokemonData;
}

/**
 * Recupera la lista completa dei Pokémon con nome e id.
 * @param int $limit Numero di Pokémon da recuperare
 * @param int $offset Offset per la paginazione
 * @return array Array di Pokémon con id, name e url
 */
function getPokemonNamesListFromPokeAPI($limit = 649, $offset = 0)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://pokeapi.co/api/v2/pokemon?limit={$limit}&offset={$offset}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $decodedResponse = json_decode($response, true);
    $pokemonList = $decodedResponse['results'] ?? [];
    $pokemonData = [];

    foreach ($pokemonList as $pokemon) {
        $urlParts = explode('/', rtrim($pokemon['url'], '/'));
        $id = intval(end($urlParts));
        $pokemonData[] = [
            'id' => $id,
            'name' => $pokemon['name'],
            'url' => $pokemon['url']
        ];
    }

    return $pokemonData;
}

/**
 * Cerca Pokémon per query: se la query è numerica cerca per prefisso dell'id,
 * altrimenti cerca per substring nel nome.
 * Restituisce array di entry ['id','name','url']
 */
function searchPokemonByQuery($query, $limit = 649)
{
    $q = trim((string) $query);
    if ($q === '') {
        return [];
    }

    $all = getPokemonNamesListFromPokeAPI(649, 0);
    $results = [];

    $numeric = preg_replace('/[^0-9]/', '', $q);
    $isNumeric = $numeric !== '' && ctype_digit($numeric);

    foreach ($all as $entry) {
        if ($isNumeric) {
            if (strpos((string) $entry['id'], $numeric) === 0) {
                $results[] = $entry;
            }
        } else {
            if (stripos($entry['name'], $q) !== false) {
                $results[] = $entry;
            }
        }

        if (count($results) >= $limit) {
            break;
        }
    }

    return $results;
}

/**
 * Recupera i dettagli di un Pokémon tramite nome o id.
 * @param string|int $nameOrId Nome o id del Pokémon
 * @return array|null Dettagli del Pokémon o null se non trovato
 */
function getPokemonDetailFromPokeAPI($nameOrId)
{
    $q = trim((string) $nameOrId);
    if ($q === '') {
        return null;
    }

    // If numeric-like, fetch directly by id
    $numeric = preg_replace('/[^0-9]/', '', $q);
    if ($numeric !== '' && ctype_digit($numeric)) {
        $idOrName = $numeric;
    } else {
        // normalize name to lower-hyphen form
        $idOrName = strtolower(str_replace(' ', '-', $q));
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://pokeapi.co/api/v2/pokemon/{$idOrName}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $decoded = json_decode($response, true);
    if (!$decoded) {
        return null;
    }

    return [
        'id' => $decoded['id'],
        'name' => $decoded['name'],
        'img' => $decoded['sprites']['front_default'] ?? null
    ];
}
