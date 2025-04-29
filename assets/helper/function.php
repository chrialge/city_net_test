<?php

// funzione che fa cicclare per tutti i parametri con l'operatore spreds `...` -->(che non gli interessa il numro di parametri) e poi blocca il programma
/**
 * funzione che fa scorere i dati e poi blocca il programma
 * @param $params con lo spread puo passare una moltitudine di parametri
 */
function dd(...$params)
{
    // fa scorere tutti i parametri
    foreach ($params as $param) {
        // li dampa per ogni parametro
        var_dump($param);
    }
    // blocca il programma
    die;
}
