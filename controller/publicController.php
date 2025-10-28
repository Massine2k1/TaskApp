<?php

if (empty($_GET['pg'])) {

    echo $twig->render('homepage.html.twig');
}else{

    $page = trim($_GET['pg']);

    switch ($page) {
        case 'connexion':

            if(isset($_SESSION['user_id'])){

                header("Location: ".RACINE_URL);
                exit();
            }

            echo $twig->render('connexion.html.twig');
            break;
        case 'inscription':

            break;
        default:
            break;
    }
}