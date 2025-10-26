<?php

declare(strict_types=1);

session_start();

// Chargement de l'autoload Composer
// require_once '../vendor/autoload.php';

// Chargement de la configuration
require_once '../config/configdev.php';

// Chargement du router (chemin corrigé)
require_once RACINE_PATH . '/../controller/routerController.php';