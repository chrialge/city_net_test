<?php

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/assets/helper/function.php';

$mysqli = new mysqli(DB_SERVERNAME, DB_USERNAME, DB_PASSWORD, DB_NAME);

$mysqli->query("CREATE TABLE IF NOT EXISTS `companies` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `name_company` VARCHAR(100) NOT NULL,
    `password` VARCHAR(100) NOT NULL,
    `vat_number` VARCHAR(11) NOT NULL,
    `telephone` VARCHAR(13) DEFAULT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `birth_of_day` DATE DEFAULT NULL,
    `address` TEXT(1000) NOT NULL,
    `create_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `update_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);");
dd($mysqli);
