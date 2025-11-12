<?php

use model\manager\UserManager;
use model\manager\TaskManager;
use model\mapping\TaskMapping;

$userManager = new UserManager($connectPDO);
$taskManager = new TaskManager($connectPDO);

$statusCounts = $taskManager->getTaskCountsByStatus();
$twig->addGlobal('statusCounts', $statusCounts);
$twig->addGlobal('session', $_SESSION ?? []);

if (empty($_GET)) {

    $tasks = $taskManager->getAllTasks();
    
    echo $twig->render('tasklist.html.twig', ['tasks' => $tasks]);    
}elseif (isset($_GET['pg'])) {
   
    switch ($_GET['pg']) {
        case 'deconnexion':
            
            $disconnect = $userManager->disconnect();

            if ($disconnect===true) {
                header('Location:./');
            }

            break;
        case 'addtask':

            if (isset($_POST['task_title'],$_POST['task_desc'],$_POST['task_due_date'])) {
                
                $_POST['user_id'] = (int) $_POST['user_id'];
                $task = new TaskMapping($_POST);
                $result = $taskManager->addTask($task);
                if ($result===true) {
                    header('Location:./');
                }else {
                    $error = 'Veuillez remplir tous les champs';
                }
            }

            echo $twig->render('addtask.html.twig');
            break;
        case 'update':
        
            $task = $taskManager->getTaskById($_GET['id']);
            $error = null;

            if (isset($_POST)&&!empty($_POST)) {
                $newTask = new TaskMapping($_POST);
                $ok = $taskManager->updateTask($newTask);

                if ($ok) {
                    header('Location: ./');
                    exit();
                }else {
                    $error = "Erreur lors de la mise à jour de la tâche";
                }
            }
            echo $twig->render('updateTask.html.twig', ['item' => $task, 'error' => $error]);
            break;
        case 'delete':
            $taskManager->deleteTask($_GET['id']);
            if ($taskManager) {
                header('Location: ./');
                exit();
            }
            break;
        default:
            break;
    }
}else {
    $tasks = $taskManager->getAllTasksByStatus((int)$_GET['status_id']);
    
    echo $twig->render('tasklist.html.twig', ['tasks' => $tasks]);      
}