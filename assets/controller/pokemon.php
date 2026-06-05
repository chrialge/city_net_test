<?php

require_once  __DIR__ . '/../helper/function.php';
require_once __DIR__ . '/../helper/db.php';
require_once __DIR__ . '/../models/pokemon.php';




function getPokemonById($id)
{
    $connection = DB::connect();
    $pokemon = Pokemon::getPokemonById($connection, $id);
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
