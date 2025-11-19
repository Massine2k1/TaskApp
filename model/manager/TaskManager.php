<?php

namespace model\manager;

// use model\AbstractMapping;
use PDO;
use Exception;
use model\ManagerInterface;
use model\StringTrait;
use model\mapping\TaskMapping;

class TaskManager implements ManagerInterface
{
    private PDO $db;

    public function __construct(PDO $connect){
        $this->db=$connect;
    }

    use StringTrait;

    public function getAllTasks():array | bool
    {
        $userId = $_SESSION['id'];
        $sql = "SELECT t.id,
                       t.task_title,
                       t.task_desc,
                       t.task_due_date,
                       t.task_created_at,
                       s.name as status_name
                FROM tasks t 
                INNER JOIN task_statuses s ON t.task_status_id = s.id
                WHERE t.user_id = ?
                ORDER BY t.task_created_at DESC";
        try {
            
            $stmt=$this->db->prepare($sql);
            $stmt->execute([$userId]);

            $result = $stmt->fetchAll();
            $stmt->closeCursor();
            $tasks = [];

            foreach ($result as $task) {
                $tasks[]= new TaskMapping($task);
            }
            return $tasks;

        } catch (Exception $e) {
            $e->getMessage();
            return false;
        }
        
        
    }

    public function getAllTasksByStatus($id): bool | array
    {
        $userId = $_SESSION['id'];
        $sql = "SELECT t.id,
                       t.task_title,
                       t.task_desc,
                       t.task_due_date,
                       t.task_created_at,
                       s.name as status_name
                FROM tasks t 
                INNER JOIN task_statuses s ON t.task_status_id = s.id
                WHERE t.user_id = ? 
                  AND t.task_status_id = ?
                ORDER BY t.task_created_at DESC";
        
        try {
            
            $stmt=$this->db->prepare($sql);
            $stmt->execute([$userId, $id]);

            $result = $stmt->fetchAll();
            $stmt->closeCursor();
            $tasks = [];

            foreach ($result as $task) {
                $tasks[]= new TaskMapping($task);
            }
            return $tasks;

        } catch (Exception $e) {
            $e->getMessage();
            return false;
        }
    }

    public function addTask(TaskMapping $data): bool
    {
        $sql = 'INSERT INTO tasks(user_id, task_title, task_desc, task_due_date)
                VALUES (?, ?, ?, ?)';

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data->getUserId(),
                $data->getTaskTitle(),
                $data->getTaskDesc(),
                $data->getTaskDueDate()
            ]);
            $stmt->closeCursor();
            return true;
        } catch (Exception $e) {

            error_log("Erreur addTask: " . $e->getMessage());
            return false;
        }
    }
    public function getTaskById($id): bool | TaskMapping
    {
        $sql = "SELECT t.id,
                        t.task_title,
                        t.task_desc,
                        t.task_due_date,
                        t.task_created_at,
                        s.name as status_name
                FROM tasks t 
                INNER JOIN task_statuses s ON t.task_status_id = s.id
                WHERE t.id = ?
                ORDER BY t.task_created_at DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            $stmt->closeCursor();
            $task = new TaskMapping($result);
            return $task;
        } catch (Exception $e) {
                $e->getMessage();
                return false;
        }
    }

    public function updateTask(TaskMapping $data): bool
    {
        $sql = "UPDATE tasks 
                SET task_title = ?, 
                    task_desc = ?, 
                    task_due_date = ?
                WHERE id = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data->getTaskTitle(),
                $data->getTaskDesc(),
                $data->getTaskDueDate(),
                $data->getId()
            ]);
            $stmt->closeCursor();
            return true;
        } catch (Exception $e) {
            $e->getMessage();
            return false;
        }

    }

    public function deleteTask($id): bool

    {
        $sql = "DELETE FROM tasks WHERE id = ?";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $stmt->closeCursor();
            return true;
        } catch (Exception $e) {
            $e->getMessage();
            return false;
        }
    }

    public function getTaskCountsByStatus(): array
    {
        $userId = $_SESSION['id'];
        $sql = "SELECT s.id,
                       s.name,
                       COUNT(t.id) as task_count
                FROM task_statuses s
                LEFT JOIN tasks t ON s.id = t.task_status_id
                                  AND t.user_id = ?
                GROUP BY s.id
                ORDER BY s.id";
    try {
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Erreur getTaskCountsByStatus: " . $e->getMessage());
        return [];
    }

    }

    /**
     * Récupère les tâches d'un mois spécifique
     * @param int $month Numéro du mois (1-12)
     * @param int $year Année (ex: 2025)
     * @return array Liste des tâches du mois
     */
    public function getTasksForMonth(int $month, int $year): array
    {
        $userId = $_SESSION['id'];

        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $endDate = sprintf('%04d-%02d-%02d', $year, $month, $daysInMonth);

        $sql = "SELECT t.id,
                       t.task_title,
                       t.task_desc,
                       t.task_due_date,
                       t.task_created_at,
                       s.name as status_name
                FROM tasks t
                INNER JOIN task_statuses s ON t.task_status_id = s.id
                WHERE t.user_id = ?
                  AND t.task_due_date BETWEEN ? AND ?
                ORDER BY t.task_due_date ASC";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $startDate, $endDate]);
            $result = $stmt->fetchAll();
            $stmt->closeCursor();

            $tasks = [];
            foreach ($result as $row) {
                $tasks[] = new TaskMapping($row);
            }

            return $tasks;
        } catch (Exception $e) {
            error_log("Erreur getTasksForMonth: " . $e->getMessage());
            return [];
        }
    }

        public function getTaskByDate($date): bool | array
    {
        $sql = "SELECT t.id,
                        t.task_title,
                        t.task_desc,
                        t.task_due_date,
                        t.task_created_at,
                        s.name as status_name
                FROM tasks t 
                INNER JOIN task_statuses s ON t.task_status_id = s.id
                WHERE t.task_due_date = ? AND
                WHERE s.name IN ('À faire','En cours','En retard')
                ORDER BY t.task_created_at DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$date]);
            $result = $stmt->fetchAll();
            $stmt->closeCursor();
            $tasks = [];
            foreach ($result as $item) {
                $tasks[]=new TaskMapping($item);
            }
            return $tasks;
        } catch (Exception $e) {
                $e->getMessage();
                return false;
        }
    }

}
