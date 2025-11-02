<?php

namespace model\mapping;

use Exception;
use model\AbstractMapping;
use model\StringTrait;
use model\mapping\UserMapping;

class TaskMapping extends AbstractMapping
{
    // champs du mapping selon votre DB
    protected ?int $id = null;
    protected ?int $user_id = null;
    protected ?string $task_title = null;
    protected ?string $task_desc = null;
    protected ?int $task_status_id = null;
    protected ?string $task_due_date = null;
    protected ?string $task_created_at = null;
    
    // champ de jointure pour le nom du statut
    protected ?string $status_name = null;
    
    // champs de jointures (optionnel - supprimable si pas utilisé)
    // protected ?UserMapping $user = null;

    // utilisation du trait
    use StringTrait;

    // getters and setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        if($id <= 0) throw new Exception("id doit être un entier positif");
        $this->id = $id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(?int $user_id): void
    {
        if($user_id <= 0) throw new Exception("user_id doit être un entier positif");
        $this->user_id = $user_id;
    }

    public function getTaskTitle(): ?string
    {
        return $this->task_title;
    }

    public function setTaskTitle(?string $task_title): void
    {
        $task_title = htmlspecialchars(strip_tags(trim($task_title)));
        if(strlen($task_title) < 3 || strlen($task_title) > 100){
            throw new Exception("Le titre de la tâche doit faire entre 3 et 100 caractères");
        }
        $this->task_title = $task_title;
    }

    public function getTaskDesc(): ?string
    {
        return html_entity_decode($this->task_desc);
    }

    public function setTaskDesc(?string $task_desc): void
    {
        if(is_null($task_desc)) {
            $this->task_desc = null;
            return;
        }
        $task_desc = htmlspecialchars(strip_tags(trim($task_desc)));
        if(strlen($task_desc) > 500){
            throw new Exception("La description de la tâche ne peut pas dépasser 500 caractères");
        }
        $this->task_desc = $task_desc;
    }

    public function getTaskStatusId(): ?int
    {
        return $this->task_status_id;
    }

    public function setTaskStatusId(?int $task_status_id): void
    {
        // Supposons que vous avez des statuts : 1=En cours, 2=Terminé, 3=En attente, etc.
        if(!is_null($task_status_id) && ($task_status_id < 1 || $task_status_id > 5)){
            throw new Exception("Le statut de la tâche doit être entre 1 et 5");
        }
        $this->task_status_id = $task_status_id;
    }

    public function getStatusName(): ?string
    {
        return $this->status_name;
    }

    public function setStatusName(?string $status_name): void
    {
        $this->status_name = $status_name;
    }

    public function getTaskDueDate(): ?string
    {
        return $this->task_due_date;
    }

    public function setTaskDueDate(?string $task_due_date): void
    {
        if(is_null($task_due_date)) {
            $this->task_due_date = null;
            return;
        }
        $date = date('Y-m-d', strtotime($task_due_date));
        if(!$date) throw new Exception("La date d'échéance n'est pas au bon format");
        $this->task_due_date = $date;
    }

    public function getTaskCreatedAt(): ?string
    {
        return $this->task_created_at;
    }

    public function setTaskCreatedAt(?string $task_created_at): void
    {
        if(is_null($task_created_at)) {
            $this->task_created_at = date('Y-m-d H:i:s');
            return;
        }
        $date = date('Y-m-d H:i:s', strtotime($task_created_at));
        if(!$date) throw new Exception("La date de création n'est pas au bon format");
        $this->task_created_at = $date;
    }

    /*
    public function getUser(): ?UserMapping
    {
        return $this->user;
    }

    public function setUser(?UserMapping $user): void
    {
        $this->user = $user;
    }
    */
}