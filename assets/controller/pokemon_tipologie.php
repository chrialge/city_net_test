<?php

require_once __DIR__ . '/../helper/function.php';
require_once __DIR__ . '/../helper/db.php';
require_once __DIR__ . '/../models/pokemon_tipologie.php';

function getPokemonTipologieById($id)
{
    $connection = DB::connect();
    $tipologia = PokemonTipologie::getPokemonTipologieById($connection, $id);
    DB::Disconnect($connection);
    return $tipologia;
}

function getAllPokemonTipologie()
{
    $connection = DB::connect();
    $tipologie = PokemonTipologie::all($connection);
    DB::Disconnect($connection);
    return $tipologie;
}

function getPokemonTipologieByArrayId($ids)
{
    $connection = DB::connect();
    $tipologie = PokemonTipologie::getPokemonTipologieByArrayId($connection, $ids);
    DB::Disconnect($connection);
    return $tipologie;
}
