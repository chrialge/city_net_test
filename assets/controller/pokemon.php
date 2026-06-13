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

function getDebolezzePokemon($pokemon)
{
    // Se il Pokémon non esiste o non ha tipologie, restituiamo un array vuoto con la stessa struttura
    if (!$pokemon || empty($pokemon['tipologie'])) {
        return [
            'resistenze' => [],
            'resistenze2' => [],
            'debolezze'  => [],
            'debolezze2'  => [],
            'immunita'   => []
        ];
    }

    $allRows = [];

    $connection = DB::connect();

    // 1. Chiediamo alla Model i dati per ogni tipologia del Pokémon
    foreach ($pokemon['tipologie'] as $tipologia) {
        // CHIAMATA ALLA MODEL STATICA
        $rowsDb = Pokemon::getDebolezzeByTipologiaId($connection, $tipologia['id']);
        $allRows = array_merge($allRows, $rowsDb);
    }

    DB::Disconnect($connection);

    // 2. Inizializziamo i contatori interni
    $conteggioTipi = [];

    foreach ($allRows as $riga) {
        $idTipoDefensivo = $riga['id'];
        $stato = (int)$riga['statoDebolezza'];

        if (!isset($conteggioTipi[$idTipoDefensivo])) {
            $conteggioTipi[$idTipoDefensivo] = [
                'dati' => $riga,
                'resistenze' => 0,
                'debolezze' => 0,
                'immunita' => 0
            ];
        }

        if ($stato === 1) {
            $conteggioTipi[$idTipoDefensivo]['resistenze']++;
        } else if ($stato === 2) {
            $conteggioTipi[$idTipoDefensivo]['debolezze']++;
        } else if ($stato === 3) {
            $conteggioTipi[$idTipoDefensivo]['immunita']++;
        }
    }

    // 3. Prepariamo la struttura di output per la View
    $output = [
        'resistenze'  => [],
        'resistenze2' => [],
        'debolezze'   => [],
        'debolezze2'  => [],
        'immunita'    => []
    ];

    // 4. Logica di annullamento e smistamento
    foreach ($conteggioTipi as $idTipo => $info) {
        $r = $info['resistenze'];
        $d = $info['debolezze'];
        $i = $info['immunita'];
        $dati = $info['dati'];

        if ($i > 0) {
            $output['immunita'][] = $dati;
            continue;
        }

        $bilancio = $d - $r;

        if ($bilancio === 0) {
            continue; // Si annullano
        }

        if ($bilancio > 0) {
            if ($bilancio === 1) {
                $output['debolezze'][] = $dati;
            } else if ($bilancio >= 2) {
                $output['debolezze2'][] = $dati;
            }
        } else if ($bilancio < 0) {
            $valoreAssoluto = abs($bilancio);
            if ($valoreAssoluto === 1) {
                $output['resistenze'][] = $dati;
            } else if ($valoreAssoluto >= 2) {
                $output['resistenze2'][] = $dati;
            }
        }
    }

    return $output;
}
