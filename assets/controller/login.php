<?php

require_once __DIR__ . '/../helper/db.php';
require_once __DIR__ . '/../helper/Auth.php';



if (isset($_POST['name_company']) && isset($_POST['password'])) {


    $username = $_POST['name_company'];
    $password = $_POST['password'];

    $connection = DB::connect();

    if (Auth::check($connection, $username, $password) === true) {

        $_SESSION['message'] = 'Login effettuato con successo!';

        DB::disconnect($connection);
        header('Location: ../../view/dashboard.php');
        exit;
    } else {
        $_SESSION['message'] = 'Username o password errati!';

        var_dump($_SESSION);

        DB::disconnect($connection);

        header('Location: ../../view/login.php');
    }
} else {
    header('Location: ../../view/login.php');
    exit;
}
