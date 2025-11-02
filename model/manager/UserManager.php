<?php
namespace model\manager;

use PDO;
use Exception;
use model\mapping\UserMapping;
use model\UserInterface;
use model\ManagerInterface;

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

        $sql = "SELECT * FROM users WHERE user_name=?";

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

            if ($tab['user_pwd']===$result['user_pwd']) {
                $_SESSION = $result;
                unset($_SESSION['user_pwd']);
                return true;
            }else{
                return false;
            }
        } catch (Exception $e) {
                
            echo $e->getMessage();
            return false;
        }
    }

 public function disconnect(): bool
    {
        session_unset();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        if(session_destroy()){
            return true;
        }else{
            return false;
                }
        
            }


}