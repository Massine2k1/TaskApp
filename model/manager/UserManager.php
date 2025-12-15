<?php
namespace model\manager;

use PDO;
use Exception;
use model\mapping\UserMapping;
use model\UserInterface;
use model\ManagerInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP; // Si vous utilisez SMTP
use PHPMailer\PHPMailer\Exception as MailerException; // Renommer pour éviter le conflit

class UserManager implements ManagerInterface, UserInterface

{
    protected PDO $connect;

    public function __construct(PDO $connect)
    {
        $this->connect = $connect;
    }

    public function connect(array $data): bool
    {
        $datas = new UserMapping($data);
        $sql = "SELECT id, user_name, user_email,user_pwd,is_verified FROM users WHERE user_name = ?";
        $stmt = $this->connect->prepare($sql);
        $stmt->execute([$datas->getUserName()]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        // 2. Vérifier l'existence et le mot de passe
        if (!$user || !password_verify($datas->getUserPwd(), $user['user_pwd'])) {
            throw new Exception("Login et/ou mot de passe non valide !");
        }

        // 3. Vérifier si le compte est vérifié (EST_VERIFIE = 1)
        if ($user['is_verified'] != 1) {
            throw new Exception("Compte non vérifié. Veuillez cliquer sur le lien dans l'e-mail de confirmation.");
        }

        // 4. Connexion réussie : Initialisation de la session
        $_SESSION['id'] = $user['id'];
        $_SESSION['user_name'] = $user['user_name'];
        
        return true;
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
public function register(array $data): bool
{
    if (!isset($data['user_name'], $data['user_pwd'], $data['user_email'])) {
        return false;
    }

    $user = new UserMapping($data);
    $hashedPassword = password_hash($data['user_pwd'], PASSWORD_DEFAULT);
    $token = mt_rand(10000, 99999);
    $userId = null;

    $sql = 'INSERT INTO users (user_name, user_email, user_pwd, user_token) VALUES (?,?,?,?)';

    try {
        $stmt = $this->connect->prepare($sql);
        $stmt->execute([
            $user->getUserName(),
            $user->getUserEmail(),
            $hashedPassword,
            $token
        ]);
        $userId = (int) $this->connect->lastInsertId();
        $stmt->closeCursor();
    } catch (Exception $e) {
        throw $e;
    }

    if ($userId === null) {
        return false;
    }

    return $this->sendVerificationEmail($userId, $user->getUserEmail(), $token);
}

private function sendVerificationEmail(int $userId, string $email, string $token): bool
    {
        // --- Configuration Mailer (Utilisez des constantes ou variables d'environnement !) ---
        $verification_link = "https://task-app.infinityfreeapp.com/public/?pg=verification&id={$userId}&token={$token}";
        $mail = new PHPMailer(true);

        try {
            // Configuration SMTP (À configurer avec votre serveur réel)
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'msnabgar2001@gmail.com';
            $mail->Password   = PWD_MAIL;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            
            // Options pour gérer les certificats SSL (nécessaire pour Gmail)
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
            // Contenu
            $mail->setFrom('msnabgar2001@gmail.com', 'Task Manager');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Veuillez vérifier votre compte';
            $mail->Body    = "Cliquez sur le lien pour vérifier votre compte : <a href='{$verification_link}'>Vérifier</a>";
            $mail->AltBody = "Veuillez utiliser le lien pour vérifier votre compte : {$verification_link}";
            
            $mail->send();
            return true;
        } catch (MailerException $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
            // On lance une exception pour que le Controller puisse la capturer
            throw new Exception("Échec de l'envoi de l'e-mail de vérification.");
        }
    }

    public function verifyToken(int $userId, string $token): bool
    {
        // 1. Vérifier l'ID et le token ET s'assurer qu'il n'est pas déjà vérifié (est_verifie = 0)
        $sql_check = "SELECT id, user_name FROM users WHERE id = ? AND user_token = ? AND is_verified = 0";
        $stmt_check = $this->connect->prepare($sql_check);
        $stmt_check->execute([$userId, $token]);
        $user = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        if ($stmt_check->rowCount() === 0) {
            throw new Exception("Lien de vérification invalide ou déjà utilisé.");
        }
        $stmt_check->closeCursor();

        // 2. Mettre à jour : est_verifie = 1 et supprimer le token (NULL)
        $sql_update = "UPDATE users SET is_verified = 1, user_token = NULL WHERE id = ?";
        $stmt_update = $this->connect->prepare($sql_update);
        
        if ($stmt_update->execute([$userId])) {
            $stmt_update->closeCursor();
            $_SESSION['id'] = $user['id'];
            $_SESSION['user_name'] = $user['user_name'];
            return true;
        } else {
            throw new Exception("Erreur DB lors de la validation du compte.");
        }
    }
}