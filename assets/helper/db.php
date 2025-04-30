<?php

// recupero il file dove ci sono le constanti che definiscono il mio database
require_once __DIR__ . '../../../env.php';

// classe DB dove ci sono due funzione per la connesione e la disconessione del databse
class DB
{

    /**
     * funzione che crea una istanza per la connesione al dtabase
     * @return mysqli_connection
     */
    public static function connect()
    {
        // creo una istanza dove inserisco le mie costanti del database
        $connection = new mysqli(DB_SERVERNAME, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

        // se contiene error
        if ($connection && $connection->connect_error) {
            // crea una nuova eccezione che mi fa vedere l'erore
            throw new Exception("DB Connection failed", 1);
        };

        // return la connesione
        return $connection;
    }

    // funzionde di disconnessione che passa la mia connesione
    /**
     * funzione che chiude la connesione al database
     * @param mysqli mi passa l'istanza della connessione
     */
    public static function disconnect($connection)
    {
        // prendo il metodo close che chiude la connesione
        $connection->close();
    }
}
