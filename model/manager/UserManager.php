<?php
namespace model\manager;

use PDO;
use Exception;
use model\mapping\UserMapping;
use model\UserInterface;

class UserManager implements ManagerInterface, UserInterface

{
    protected PDO $connect;

    public function __construct(PDO $connect)
    {
        $this->connect = $connect;
    }

    public function connect(array $tab):bool
    {
        if (!isset($tab['user_name'],$tab['user_pwd'])) {
            return false;
        }

        $user = new UserMapping($tab);

        $sql = "SELECT * FROM 'users' WERE 'user_name'=?";

        $stmt = $this->connect->prepare($sql);

        try {
            $stmt->execute([
                $user->getUserName()
            ]);

            if ($stmt->rowCount()!=1) {
                return false;
            }

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
        } catch (Exeption $e) {
                
            echo $e->getMessage();
            return false;
        }
    }

}