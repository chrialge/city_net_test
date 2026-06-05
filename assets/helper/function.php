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
