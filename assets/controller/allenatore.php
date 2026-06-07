<?php

require_once __DIR__ . '/../helper/Auth.php';
require_once __DIR__ . '/../helper/db.php';




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



function getTeamPokemon($idAllenatore) {}
