<?php

require_once 'controller/controllerbis.php';
require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'useful/ToolsBis.php';

class ControllerMain extends ControllerBis {

    //gestion de la connexion d'un utilisateur
    public function login() {
        $username = '';
        $password = '';
        $errors = [];
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $errors = User::validate_login($username, $password);
            if (empty($errors)) {
                $this->log_user(User::get_user_by_username($username));
            }
        }
        (new View("login"))->show(array("username" => $username, "password" => $password, "errors" => $errors));
    }

    public function signup() {
        $username = '';
        $password = '';
        $confirm_password = '';
        $fullname = '';
        $email = '';
        $birthdate = '';
        $errors = [];
        if (ToolsBis::check_fields(['username', 'password', 'password_confirm', 'fullname', 'email', 'birthdate'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];
            $fullname = $_POST['fullname'];
            $email = $_POST['email'];
            $birthdate = $_POST['birthdate'];

            $errors = User:: validate_user(null, $username, $password, $password_confirm, $fullname, $email, $birthdate);
        }
        (new View("signup"))->show(array("username" => $username, "password" => $password,
            "password_confirm" => $confirm_password, "fullname" => $fullname, "email" => $email,
            "birthdate" => $birthdate, "errors" => $errors));
    }

}
