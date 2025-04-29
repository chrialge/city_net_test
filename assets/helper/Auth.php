<?php

require_once __DIR__ . '/db.php';

class Auth
{

    public static function check($connection, $username, $password)
    {
        // check if the session is already active
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // password viene trasformata con la crittografia sha con il metodo
        $hashedPassword = md5($password);

        $result = $connection->query("SELECT `id`, `name_company`, 'password' FROM `companies` WHERE `username` = '$username' AND `password` = '$hashedPassword'");
        var_dump($result);

        // dd($result, $username, $password);
        if ($result->num_rows > 0) {
            var_dump('here in result');
            // dd($result->fetch_assoc());
            $userData = $result->fetch_assoc();

            $_SESSION['userId'] = $userData['id'];
            $_SESSION['userName'] = $userData['username'];
        } else {
            $_SESSION['userId'] = 0;
            $_SESSION['userName'] = '';
        }
        // var_dump($result);
    }
}
