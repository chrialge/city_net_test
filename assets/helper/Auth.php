<?php

// recupero il file del database
require_once __DIR__ . '/db.php';

// la classe Auth serve per la gestione dell'autenticazione degli utenti
class Auth
{
    /**
     * funzione che controlla se l'utente è autenticato
     * @param $connection mysqli_connection
     * @param $username string nome dell'azienda
     * @param $password string password dell'azienda
     * @return bool
     */
    public static function check($connection, $username, $password)
    {
        // check if the session is already active
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // password viene trasformata con la crittografia sha con il metodo
        $hashedPassword = md5($password);

        // restituisce i risultati della query
        $result = $connection->query("SELECT `id`, `email`, 'password' FROM `utenti` WHERE `email` = '$username' AND `password` = '$hashedPassword'");

        // dd($result, $username, $password);
        if ($result->num_rows > 0) {

            // estrago i dati dalla query sql
            $userData = $result->fetch_assoc();

            // setto le variabili di sessione con i dati dell'utente
            $_SESSION['userId'] = $userData['id'];
            $_SESSION['userName'] = $userData['email'];

            // ritorno true se l'utente è autenticato
            return true;
        } else {
            // se non è autenticato setto le variabili di sessione a 0 e vuote
            $_SESSION['userId'] = 0;
            $_SESSION['userName'] = '';

            // ritorno false se l'utente non è autenticato
            return false;
        }
    }

    public static function checkAllenatore($connection, $nomeAllenatore, $codiceProfilo)
    {

        // check if the session is already active
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // restituisce i risultati della query
        $result = $connection->query("SELECT `id`, `nomeAllenatore`, 'codiceProfilo', 'master'  FROM `allenatori` WHERE `nomeAllenatore` = '$nomeAllenatore' AND `codiceProfilo` = '$codiceProfilo'");

        // dd($result, $username, $password);
        if ($result->num_rows > 0) {

            // estrago i dati dalla query sql
            $userData = $result->fetch_assoc();

            // setto le variabili di sessione con i dati dell'utente
            $_SESSION['allenatoreId'] = $userData['id'];
            $_SESSION['nomeAllenatore'] = $userData['nomeAllenatore'];
            $_SESSION['codiceProfilo'] = $userData['codiceProfilo'];
            $_SESSION['master'] = $userData['master'];

            // ritorno true se l'utente è autenticato
            return true;
        } else {
            // se non è autenticato setto le variabili di sessione a 0 e vuote
            $_SESSION['allenatoreId'] = 0;


            // ritorno false se l'utente non è autenticato
            return false;
        }
    }
}
