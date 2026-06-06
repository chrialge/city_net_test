<?php

require_once __DIR__ . '/../helper/function.php';
require_once __DIR__ . '/../helper/db.php';
require_once __DIR__ . '/../models/ranghi.php';

function getRanghiById($id)
{
    $connection = DB::connect();
    $ranghi = Ranghi::getRanghiId($connection, $id);
    DB::Disconnect($connection);
    return $ranghi;
}

function getAllRanghi()
{
    $connection = DB::connect();
    $ranghi = Ranghi::all($connection);
    DB::Disconnect($connection);
    return $ranghi;
}

function getRanghiByArrayId($ids)
{
    $connection = DB::connect();
    $ranghi = Ranghi::getRanghiByArrayId($connection, $ids);
    DB::Disconnect($connection);
    return $ranghi;
}
