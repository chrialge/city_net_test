<?php

require_once __DIR__ . '/../helper/function.php';
require_once __DIR__ . '/../helper/db.php';
require_once __DIR__ . '/../models/nature.php';

function getNatureById($id)
{
    $connection = DB::connect();
    $natura = Nature::getNatureById($connection, $id);
    DB::Disconnect($connection);
    return $$natura;
}

function getAllNature()
{
    $connection = DB::connect();
    $nature = Nature::all($connection);
    DB::Disconnect($connection);
    return $nature;
}
