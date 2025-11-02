<?php


use model\manager\UserManager;

$userManager = new UserManager($connectPDO); 


if (empty($_GET['pg'])) {

    echo $twig->render('homepage.html.twig');
}else{

    $page = trim($_GET['pg']);

    switch ($page) {
        case 'connexion':

            if(isset($_SESSION['user_name'])){

                header("Location: ".RACINE_URL);
                exit();
            }

            if (isset($_POST['user_name'],$_POST['user_pwd'])) {
                try {
                    $connect = $userManager->connect($_POST);

                    if ($connect===true) {
                        header('Location: ./');
                        exit();
                    }else {
                        $error = "Login et ou mot de passe non valide !";
                    }
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
            }else{}

            echo $twig->render('connexion.html.twig');
            break;
        case 'inscription':

            break;
        default:
            break;
    }
}