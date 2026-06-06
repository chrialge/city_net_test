<?php

require_once __DIR__ . '/../helper/function.php';
require_once __DIR__ . '/../helper/db.php';
require_once __DIR__ . '/../models/conoscenze.php';

function getConoscenzeById($id)
{
    $connection = DB::connect();
    $conoscenza = Conoscenze::getConoscenzeById($connection, $id);
    DB::Disconnect($connection);
    return $conoscenza;
}

function getAllConoscenze()
{
    $connection = DB::connect();
    $conoscenze = Conoscenze::all($connection);
    DB::Disconnect($connection);
    return $conoscenze;
}

function getConoscenzeByArrayId($ids)
{
    $connection = DB::connect();
    $conoscenze = Conoscenze::getConoscenzeByArrayId($connection, $ids);
    DB::Disconnect($connection);
    return $conoscenze;
}
