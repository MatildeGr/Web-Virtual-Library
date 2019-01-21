<?php

require_once 'controller/controllerbis.php';
require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'useful/ToolsBis.php';

class ControllerUser extends ControllerBis {

    public function index() {
        $this->profile();
    }

    public function profile() {
        $user = $this->get_user_or_redirect();

        if (!($user->is_admin()) && !($user->is_manager())) {
            $menu = "view/menu_member.html";
        } else {
            $menu = "view/menu.html";
        }

        if (isset($_GET["param1"]) && $_GET["param1"] !== "") {
            $user = User::get_user_by_username($_GET["param1"]);
        }

        (new View("profile"))->show(array("username" => $user, "menu" => $menu));
    }

    public function user_lst() {
        $user = $this->get_user_or_redirect();
        if (!($user->is_admin()) && !($user->is_manager())) {
            ToolsBis::abort("Vous ne disposez pas des droits d'aministrateur.");
        }
        $all_users = User::get_user();

        (new View("users"))->show(array("user" => $user, "users" => $all_users));
    }

    public function delete() {
        $user = $this->get_user_or_redirect();
        if (!$user->is_admin()) {
            ToolsBis::abort("Vous ne disposez pas des droits d'aministrateur.");
        }
        $errors = [];
        $user_del = '';
        if (isset($_GET['param1'])) {
            $userdel = trim($_GET['param1']);
            $user_del = user::get_user_by_username($userdel);
            $user_del = $user_del->username;
        }
        if (isset($_POST["conf"]) && isset($_POST["username"])) {
            $username = trim($_POST["username"]);
            if ($_POST["conf"] === "true") {
                if ($username === $user->username) {
                    array_push($errors, "Vous ne pouvez pas vous supprimez.");
                }
                if (User::how_many_admin() === 1) {
                    array_push($errors, "Vous etes le dernier admin, vous ne pouvez pas vous supprimez.");
                }
                if (count($errors) == 0) {
                    User::del_user_by_name($username);
                    $this->redirect("member", "user_lst");
                }
            }
        }
        (new View("delete"))->show(array("user" => $user, "user_del" => $user_del, "errors" => $errors));
    }

    
    public function add_edit_user() {
        if (ToolsBis::check_fields(['id'], $_GET)) {
            $is_new = false;
            $id = sanitize($_GET['id']);
            $usr = get_user($id);
            if (!$usr) {
                abort('Unknown user');
            }
            $username = $usr['username'];
            $fullname = $usr['fullname'];
            $email = $usr['email'];
            $birthdate = $usr['birthdate'];
            $role = $usr['role'];
            $password = $usr['password'];
        } else {
            $is_new = true;
            $id = null;
            $username = '';
            $fullname = '';
            $email = '';
            $birthdate = null;
            $role = 'member';
            $password='';
        }

        if (ToolsBis::check_fields(['cancel'])) {
            redirect('users.php');
        }
        $errors=[];
        (new View("add_edit_user"))->show(array("username" => $username, "fullname" => $fullname, "password" => $password,
            "email" => $email, "birthdate" => $birthdate, "role" => $role,"is_new"=>$is_new, "errors" => $errors));
    }

    public function user_add() {
        $user = $this->get_user_or_redirect();
        if (!($user->is_admin()) && !($user->is_manager())) {
            ToolsBis::abort("Vous ne disposez pas des droits d'aministrateur.");
        }
        $fullname = '';
        $username = '';
        $birthdate = '';
        $email = '';
        $password = '';
        $password_confirm = '';
        $role = '';
        $errors = [];
        if (isset($_POST['username']) && isset($_POST['fullname']) && isset($_POST['birthdate']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['password_confirm']) && isset($_POST['role'])) {
            $username = trim($_POST['username']);
            $fullname = trim($_POST['fullname']);
            $password = trim($_POST['password']);
            $password_confirm = trim($_POST['password_confirm']);
            $email = trim($_POST['email']);
            $birthdate = trim($_POST['birthdate']);
            $role = trim($_POST['role']);

            if (!$user->is_admin()) {
                $role = 'member';
            }
            if ($birthdate === '') {
                $birthdate = null;
            }
            $userAdd = new User($fullname, $username, Tools::my_hash($password), $email, $role, $birthdate);
            $errors = User::validate_unicity($username);
            $errors = array_merge($errors, $user->validate());
            $errors = array_merge($errors, $user->fullname_validate());
            $errors = array_merge($errors, User::validate_passwords($password, $password_confirm));
            $errors = array_merge($errors, User::validate_email($email));


            if (count($errors) == 0) {
                $userAdd->update();
                Controller::log_user($user);
            }
        }

        (new View("add_edit_user"))->show(array("member" => $user, "username" => $username, "password" => $password, "password_confirm" => $password_confirm,
            "fullname" => $fullname, "email" => $email, "birthdate" => $birthdate, "role" => $role, "errors" => $errors));
    }

    public function user_upd() {
        $user = $this->get_user_or_redirect();
        $profile = '';
        $errors = [];
        if (!($user->is_admin()) && !($user->is_manager())) {
            ToolsBis::abort("Vous ne disposez pas des droits d'aministrateur.");
        }
        if (isset($_GET['param1'])) {
            $profile = trim($_GET['param1']);
        }

        if (isset($_POST['fullname']) && isset($_POST['username']) && isset($_POST['birthdate']) && isset($_POST['email']) && isset($_POST['role'])) {
            $fullname = trim($_POST['fullname']);
            $username = trim($_POST['username']);
            $birthdate = trim($_POST['birthdate']);
            $email = trim($_POST['email']);
            $role = trim($_POST['role']);
        }



        (new View("user_upd"))->show(array("user" => $user, "profile" => $profile, "errors" => $errors));
    }

}
