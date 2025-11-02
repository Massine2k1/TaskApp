<?php

try {
    $connectPDO = new PDO(
        DB_TYPE . ':host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
        DB_LOGIN,
        DB_PWD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (Exception $e) {
    die($e->getMessage());
}



if (isset($_SESSION['user_name'])) {
    require_once RACINE_PATH . "/../controller/privateController.php";
}else{
    require_once RACINE_PATH . "/../controller/publicController.php";
}
// Bonne pratique
$connectPDO = null;