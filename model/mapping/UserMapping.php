<?php

namespace model\mapping;

use model\AbstractMapping;
use Exception;

class UserMapping extends AbstractMapping
{
    protected ?int $id = null;
    protected ?string $user_name = null;
    protected ?string $user_email = null;
    protected ?string $user_pwd = null;
    protected ?string $created_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        if($id <= 0) throw new Exception("id doit être un entier positif");
        $this->id = $id;
    }

    public function getUserName(): ?string
    {
        return $this->user_name;
    }

    public function setUserName(?string $user_name): void
    {
        $user_name = strip_tags(trim($user_name));
        if(empty($user_name))
            throw new Exception("Username non valide");
        if(strlen($user_name) < 3 || strlen($user_name) > 45)
            throw new Exception("Le username doit faire entre 3 et 45 caractères");
        // Ne peut pas commencer par un chiffre, ni contenir des espaces ou des caractères spéciaux
        if(preg_match('/^[a-zA-Z][a-zA-Z0-9]{2,29}$/', $user_name)){
            $this->user_name = $user_name;
        }else{
            throw new Exception("Votre username doit faire de 3 à 30 caractères, commencer par une lettre et ne contenir que des lettres et des chiffres non accentués");
        }
    }

    public function getUserEmail(): ?string
    {
        return $this->user_email;
    }

    public function setUserEmail(?string $user_email): void
    {
        if(filter_var($user_email, FILTER_VALIDATE_EMAIL)){
            $this->user_email = $user_email;
        }else{
            throw new Exception("Votre email n'est pas valide");
        }
    }

    public function getUserPwd(): ?string
    {
        return $this->user_pwd;
    }

    public function setUserPwd(string $user_pwd): void
    {
        $user_pwd = trim($user_pwd);
        $this->user_pwd = $user_pwd;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setCreatedAt(?string $created_at): void
    {
        if(is_null($created_at)) return;
        $date = date('Y-m-d H:i:s', strtotime($created_at));
        if(!$date) throw new Exception("La date de création n'est pas au bon format");
        $this->created_at = $date;
    }
}