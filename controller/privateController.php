<?php

use model\manager\UserManager;
use model\manager\TaskManager;
use model\mapping\TaskMapping;

$userManager = new UserManager($connectPDO);
$taskManager = new TaskManager($connectPDO);

$statusCounts = $taskManager->getTaskCountsByStatus();
$twig->addGlobal('statusCounts', $statusCounts);
$twig->addGlobal('session', $_SESSION ?? []);
$error = [];

if (empty($_GET)) {

    $tasks = $taskManager->getAllTasks();
    $statusTask = $taskManager->getTaskCountsByStatus();
    
    echo $twig->render('tasklist.html.twig', ['tasks' => $tasks,'statusTask'=>$statusTask]);    
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
                if (!empty($_POST['task_title'])&&!empty($_POST['task_desc'])&&!empty($_POST['task_due_date'])&&!empty($_POST['user_id'])) {
                    
                    $_POST['user_id'] = (int) $_POST['user_id'];
                    $task = new TaskMapping($_POST);
                    if ($task->isValid()) {
                        $result = $taskManager->addTask($task);
                        if ($result===true) {
                            header('Location:./');
                        }
                    }else {
                       $error = $task->getErrors(); 
                    }
                }else {
                    $error[] = 'Veuillez remplir tous les champs';
                }
                
            }
            echo $twig->render('addtask.html.twig', ['error' => $error]);
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
        case 'api_update_status':
            header('Content-Type: application/json');
            
            if ($_SERVER['REQUEST_METHOD']!=='POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            }
            $data = json_decode(file_get_contents('php://input'), true);
            $taskId = $data['task_id'] ?? null;
            $statusId = $data['status_id'] ?? null;

            if (!$taskId || !$statusId) {
                echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
                return;
            }

            try {
                $success = $taskManager->updateTaskStatus($taskId, $statusId);
        
                if ($success) {
                    echo json_encode(['success' => true, 'message' => 'Statut mis à jour']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erreur de mise à jour']);
                }
            }catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }     

            break;
        case 'delete':
            $taskManager->deleteTask($_GET['id']);
            if ($taskManager) {
                header('Location: ./');
                exit();
            }
            break;
        case 'calendrier':
            $tasks = $taskManager->getTodayTask();
            $error = null;
            if (!is_array($tasks)) {
                $error = $tasks;
            }
            echo $twig->render('calendrier.html.twig',['tasks' => $tasks,'error'=> $error]);
            break;
        case 'api_calendar':
            // Endpoint JSON pour récupérer les tâches d'un mois (AJAX)
            header('Content-Type: application/json; charset=utf-8');
            $month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
            $year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
            if ($month < 1 || $month > 12) $month = date('n');
            if ($year < 2000 || $year > 2100) $year = date('Y');
                $tasks = $taskManager->getTasksForMonth($month, $year);

                // Convertir les objets TaskMapping en tableaux associatifs
                $tasksArray = array_map(function($t){
                    // Si l'objet expose jsonSerialize(), l'utiliser
                    if (is_object($t) && method_exists($t, 'jsonSerialize')) {
                        return $t->jsonSerialize();
                    }

                    // Sinon, tenter d'utiliser les getters connus
                    return [
                        'id' => method_exists($t, 'getId') ? $t->getId() : null,
                        'user_id' => method_exists($t, 'getUserId') ? $t->getUserId() : null,
                        'task_title' => method_exists($t, 'getTaskTitle') ? $t->getTaskTitle() : null,
                        'task_desc' => method_exists($t, 'getTaskDesc') ? $t->getTaskDesc() : null,
                        'task_status_id' => method_exists($t, 'getTaskStatusId') ? $t->getTaskStatusId() : null,
                        'task_due_date' => method_exists($t, 'getTaskDueDate') ? $t->getTaskDueDate() : null,
                    ];
                }, $tasks ?: []);

                // Répondre avec JSON (tableaux associatifs garantis)
                echo json_encode([
                    'success' => true,
                    'month' => $month,
                    'year' => $year,
                    'tasks' => $tasksArray
                ], JSON_UNESCAPED_UNICODE);
            exit();
        case 'task_date':
            $tasks = []; 

            if (!empty($_GET['date'])){
                $result = $taskManager->getTaskByDate($_GET['date']);
                
                if ($result !== false && is_array($result)) {
                    $tasks = $result;
                }
            }
            
            echo $twig->render('tasksByDate.html.twig', ['tasks' => $tasks]);
            break;
        case 'api_dashboard':
            header('Content-Type: application/json; charset=utf-8');
            if (isset($_GET['year'])) {
                $tasks = $taskManager->CountTaskByMonth($_GET['year']);
            }
            
            echo json_encode($tasks,JSON_UNESCAPED_UNICODE);
            exit();
        case 'dashboard':

            echo $twig->render('dashboard.html.twig');
            break;
        default:
            break;
        }
}else {
    $tasks = $taskManager->getAllTasksByStatus((int)$_GET['status_id']);
    $statusTask = $taskManager->getTaskCountsByStatus();
    $statusId = (int)$_GET['status_id'];    
    
    echo $twig->render('tasklist.html.twig', ['tasks' => $tasks, 'statusTask'=>$statusTask, 'status_id'=>$statusId]);      
}