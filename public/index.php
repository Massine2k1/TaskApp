<?php

declare(strict_types=1);

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

session_start();

// Chargement de l'autoload Composer
require_once '../vendor/autoload.php';

// Chargement de la configuration
require_once '../config/configdev.php';

$loader = new FilesystemLoader(RACINE_PATH . '/../view');

$twig = new Environment($loader, [
    'debug' => true
]);

$twig->addExtension(new DebugExtension());

// Chargement du router (chemin corrigé)
require_once RACINE_PATH . '/../controller/routerController.php';