<?php

require_once __DIR__ . '/../helper/function.php';
require_once __DIR__ . '/../helper/db.php';
require_once __DIR__ . '/../models/categorie_conoscenze.php';

function getCategorieConoscenzeById($id)
{
    $connection = DB::connect();
    $categoria = CategorieConoscenze::getCategorieConoscenzeById($connection, $id);
    DB::Disconnect($connection);
    return $categoria;
}

function getAllCategorieConoscenze()
{
    $connection = DB::connect();
    $categorie = CategorieConoscenze::all($connection);
    DB::Disconnect($connection);
    return $categorie;
}

function getCategorieConoscenzeByArrayId($ids)
{
    $connection = DB::connect();
    $categorie = CategorieConoscenze::getCategorieConoscenzeByArrayId($connection, $ids);
    DB::Disconnect($connection);
    return $categorie;
}
