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

                header("Location:./");
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
            $error='';
            if (isset( $_POST['user_name'],$_POST['user_pwd'],$_POST['user_email'])&&$_POST['user_pwd']===$_POST['user_pwd_clone']) {
                try {
                    $register = $userManager->register($_POST);
                    if ($register) {
                      header('Location: ./?pg=inscription_success&email=' . urlencode($_POST['user_email']));
                      exit();
                    } else {
                        $error = "echec de l'inscription";
                    }
                } catch (Exception $th) {
                    $error = $th->getMessage();
                }
            }else {
                $error = "Champs incorrects";
            }

            echo $twig->render('register.html.twig', ['error' => $error]);
            break;
        case 'verification':
            if (isset($_GET['id'], $_GET['token'])) {
                try {
                    $userId = (int)$_GET['id'];
                    $token = $_GET['token'];

                    if ($userManager->verifyToken($userId, $token)) {
                        header('Location: ./');
                        exit();
                    }
                    
                } catch (Exception $e) {
                    $error = $e->getMessage();
                    echo $twig->render('verification_error.html.twig', ['error' => $error]);
                }
            }
        case 'inscription_success':
            if (isset($_GET['email'])){
                $email = $_GET['email'];
                echo $twig->render('check_email.html.twig', ['email'=> $email]);
            }
            break;
        default:
            echo $twig->render('404.html.twig');
            break;
    }
}