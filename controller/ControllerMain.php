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
        if (ToolsBis::check_fields(['username', 'password'])) {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
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
            $username = trim($_POST['username']);
            $fullname = trim($_POST['fullname']);
            $password = trim($_POST['password']);
            $password_confirm = trim($_POST['password_confirm']);
            $email = trim($_POST['email']);
            $birthdate = trim($_POST['birthdate']);
            $role = 'member';

            if ($birthdate === '') {
                $birthdate = null;
            }

            $user = new User($fullname, $username, Tools::my_hash($password), $email, $role, $birthdate);
            $errors = User::validate_unicity($username);
            $errors = array_merge($errors, $user->validate());
            $errors = array_merge($errors, $user->fullname_validate());
            $errors = array_merge($errors, User::validate_passwords($password, $password_confirm));
            $errors = array_merge($errors, User::validate_email($email));


            if (count($errors) == 0) {
                $user->update();
                Controller::log_user($user);
            }
        }
        (new View("signup"))->show(array("username" => $username, "password" => $password,
            "password_confirm" => $confirm_password, "fullname" => $fullname, "email" => $email,
            "birthdate" => $birthdate, "errors" => $errors));
    }

}
