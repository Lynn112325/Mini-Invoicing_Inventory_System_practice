<?php
require_once '../app/Models/User.php';
class AuthController
{
    private $userModel;

    public function __construct($pdo)
    {
        $this->userModel = new User($pdo);
    }

    public function showLogin()
    {
        if (isset($_SESSION['user_id'])) {
            header("Location: ?route=dashboard");
            exit;
        }
        $error = '';
        require '../app/Views/auth/login.php';
    }

    public function login()
    {
        $error = '';
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!empty($username) && !empty($password)) {
            $user = $this->userModel->findByName($username);

            if ($user && password_verify($password, $user['password'])) {
                session_start();
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['name'];
                header("Location: ?route=dashboard");
                exit;
            } else {
                $error = "account or password is incorrect.";
            }
        } else {
            $error = "Please fill in all fields.";
        }

        require '../app/Views/auth/login.php';
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        header("Location: ?route=login");
        exit;
    }
}
