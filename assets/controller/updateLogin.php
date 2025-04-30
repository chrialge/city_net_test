<?php

require_once __DIR__ . '/../helper/db.php';

// if the form is submitted
if (!empty($_POST['name_company']) && !empty($_POST['password'])) {

    // save the username and password in variables
    $username = $_POST['name_company'];
    $password = $_POST['password'];

    // start the session
    session_start();

    // get the user id from the session
    $id = $_SESSION['userId'];

    // get the user name from the session
    $_SESSION['userName'] = $username;

    // connect to the database
    $mysqli = DB::connect();

    // hashed the password using md5
    $hashedPassword = md5($password);

    // update the user data in the database
    $query = "UPDATE `companies` SET `password` = '$hashedPassword', `name_company` = '$username' WHERE `id` = '$id'";

    // execute the query
    $result = $mysqli->query($query);

    // set message session variable to display success message
    $_SESSION['message'] = 'Modifica dei dati di accesso avvenuta con successo!';

    // redirect to the Setting page
    header('Location: ../../view/setting.php');
    exit;
} else {

    // session start
    session_start();

    // set message session variable to display error message
    $_SESSION['error'] = 'Errore durante la modifica dei dati!';

    // redirect to the Setting page
    header('Location: ../../view/setting.php');
    exit;
}
