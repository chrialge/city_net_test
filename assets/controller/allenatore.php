<?php

require_once __DIR__ . '/../helper/Auth.php';
require_once __DIR__ . '/../helper/db.php';
require_once __DIR__ . '/../models/allenatore.php';
require_once __DIR__ . "/pokemon.php";
require_once __DIR__ . "/../helper/function.php";



// if the form is submitted
if (!empty($_POST['nomeAllenatore']) && !empty($_POST['codiceProfilo'])) {

    // save the username and password in variables
    $nomeAllenatore = $_POST['nomeAllenatore'];
    $codiceProfilo = $_POST['codiceProfilo'];

    // connect to the database
    $connection = DB::connect();

    // check if authentication is successful
    if (Auth::checkAllenatore($connection, $nomeAllenatore, $codiceProfilo) === true) {

        // Set message to be displayed on the dashboard
        $_SESSION['message'] = 'Login effettuato con successo!';
        $_SESSION['loginResult'] = true;
        $_SESSION['pokemonId'] = $_POST['pokemonId'] ?? 0;


        // disconnect from the database
        DB::disconnect($connection);

        // redirect to the dashboard page
        header('Location: ../../view/team.php');
        exit;
    } else {

        // if authentication fails, set an error message
        $_SESSION['message'] = 'Username o password errati!';
        $_SESSION['loginResult'] = false;

        // disconnect from the database
        DB::disconnect($connection);

        // redirect to the login page
        header('Location: ../../view/team.php');
    }
}

function getAllenatoreId($id)
{
    $connection = DB::connect();
    $allenatore = Allenatore::getAllenatoreById($connection, $id);
    DB::Disconnect($connection);
    return $allenatore;
}


function getTeamPokemon($idAllenatore)
{

    $connection = DB::connect();
    $teamPokemon = Allenatore::getTeamPokemon($connection, $idAllenatore);
    DB::Disconnect($connection);

    if (count($teamPokemon) > 0) {
        $array = [];
        foreach ($teamPokemon as $pokemon) {
            $newDataPokemon = Pokemon::getPokemonById($pokemon['idPokemon']);
            $pokemonNew = getPokemonDetailFromPokeAPI(intval($newDataPokemon['numeroPokedex']));
            array_push($array, $pokemonNew);
        }

        return $array;
    } else {
        return $teamPokemon;
    }
}



function catchPokemon($allenatoreId, $pokemonId)
{
    $connection = DB::connect();
    $cattura = Allenatore::getTeamPokemon($connection, $allenatoreId, $pokemonId);
    DB::Disconnect($connection);
    return $cattura;
}
