<?php

use model\manager\UserManager;
use model\manager\TaskManager;

$userManager = new UserManager($connectPDO);
$taskManager = new TaskManager($connectPDO);

if (!isset($_GET['pg'])) {

    $tasks = $taskManager->getAllTasks();
    echo $twig->render('tasklist.html.twig',
    ['tasks'=>$tasks,
    'session' => $_SESSION ?? []]);
}else {
    switch ($_GET['pg']) {
        case 'deconnexion':
            
            $disconnect = $userManager->disconnect();

            if ($disconnect===true) {
                header('Location:./');
            }

            break;
        
        default:
            break;
    }
}