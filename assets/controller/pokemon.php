<?php

require_once  __DIR__ . '/../helper/function.php';
require_once __DIR__ . '/../helper/db.php';
require_once __DIR__ . '/../models/pokemon.php';




function getPokemonById($id)
{
    $connection = DB::connect();
    $pokemon = Pokemon::getPokemonById($connection, $id);
    if ($pokemon) {
        $pokemon['tipologie'] = Pokemon::getPokemonTypesById($connection, $id);
        $pokemon['abilita'] = Pokemon::getPokemonAbilitiesById($connection, $id);
    }
    DB::Disconnect($connection);
    return $pokemon;
}

function getAllPokemon()
{
    $connection = DB::connect();
    $pokemon = Pokemon::all($connection);
    DB::Disconnect($connection);
    return $pokemon;
}

function getShortInfoPokemon($numeroPokedex)
{
    $connection = DB::connect();
    $pokemon = Pokemon::getShortInfoPokemon($connection, $numeroPokedex);
    DB::Disconnect($connection);
    return $pokemon;
}

function getShortInfoPokemonList(array $numeroPokedexList)
{
    if (empty($numeroPokedexList)) {
        return [];
    }

    $connection = DB::connect();
    $escapedValues = array_map(function ($numero) use ($connection) {
        return "'" . $connection->real_escape_string($numero) . "'";
    }, $numeroPokedexList);

    $inClause = implode(',', $escapedValues);
    $sql = "SELECT pokemon.numeroPokedex, pokemon.nome, pokemon.generazione, GROUP_CONCAT(pokemon_tipologie.nome SEPARATOR ', ') as tipologiaNome, GROUP_CONCAT(pokemon_tipologie.colorePrincipale SEPARATOR ', ') as colorePrincipale FROM pokemon INNER JOIN pokemon_tipologie_pivot ON pokemon.id = pokemon_tipologie_pivot.idPokemon INNER JOIN pokemon_tipologie ON pokemon_tipologie_pivot.idTipologiaPokemon = pokemon_tipologie.id WHERE pokemon.numeroPokedex IN ({$inClause}) GROUP BY pokemon.id";

    $result = $connection->query($sql);
    $pokemonList = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $pokemonList[$row['numeroPokedex']] = $row;
        }
        $result->free();
    }

    DB::Disconnect($connection);
    return $pokemonList;
}
