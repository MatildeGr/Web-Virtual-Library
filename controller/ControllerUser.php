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
        
        (new View("profile"))->show(array("username" => $user, "menu" => $menu));
    }

    public function user_lst() {
        $user = $this->get_user_or_redirect();
        $this->check_manager_or_admin();
        $all_users = User::get_users();
        (new View("users"))->show(array("user" => $user, "users" => $all_users));
    }

    public function delete() {
        $user = $this->get_user_or_redirect();
        $this->check_admin();
        $errors = [];
        $user_del = '';

        if (isset($_GET['param1'])) {
            $id = trim($_GET['param1']);
            $user_del = user::get_user_by_id($id);

            if (!$user_del) {
                ToolsBis::abort('Unknown user');
            }
            if ($user->id == $id) {
                ToolsBis::abort("You may not delete yourself!");
            }
            $user_del = $user_del->username;
        } else {
            $this->redirect("user", "profile");
        }

        if (isset($_POST['confirm'])) {
            User::del_user_by_id($id);
            $this->redirect("user", "user_lst");
        } elseif (isset($_POST['cancel'])) {
            $this->redirect("user", "user_lst");
        }
        (new View("delete_user"))->show(array("user" => $user, "user_del" => $user_del, "errors" => $errors));
    }

    public function add_edit_user() {

        $errors = [];
        $user = $this->get_user_or_redirect();
        $this->check_manager_or_admin();

        $is_admin = $user->is_admin();
        if (isset($_GET['param1'])) {
            $is_new = false;
            $id = trim($_GET['param1']);
            $usr = User::get_user_by_id($id);
            if (!$usr) {
                abort('Unknown user');
            }
            $username = $usr->username;
            $fullname = $usr->fullname;
            $email = $usr->email;
            $birthdate = $usr->birthdate;
            $role = $usr->role;
            $password = $usr->hashed_password;
        } else {
            $is_new = true;
            $id = null;
            $username = '';
            $fullname = '';
            $email = '';
            $birthdate = null;
            $role = 'member';
            $password = '';
        }

        if (ToolsBis::check_fields(['cancel'])) {
            $this->redirect("user", "user_lst");
        }

        if (!$is_admin && ToolsBis::check_fields(['role'])) {
            ToolsBis::abort("You may not change the role since you're not an admin.");
        }
        if (ToolsBis::check_fields(['save', 'username', 'fullname', 'email', 'birthdate']) && ($user->is_manager() || ToolsBis::check_fields(['role']))) {

            $username = trim($_POST['username']);
            $fullname = trim($_POST['fullname']);
            $email = trim($_POST['email']);
            $birthdate = trim($_POST['birthdate']);
            // si c'est un nouveau user, on initialise son mot de passe avec son pseudo (convention)
            if ($is_new) {
                $password = $username;
            }
            $errors = User::validate_user($id, $username, $password, $password, $fullname, $email, $birthdate);

            if ($is_admin) {
                $role = trim($_POST['role']);
                // si j'édite un user existant et que je mets un rôle différent d'admin alors que le rôle courant de ce user
                // est admin, et si c'est le seul admin en base de données, alors je dois déclencher une erreur
                if (!$is_new && $role !== 'admin' && $role === 'admin' && User::count_admins() === 1) {
                    $errors[] = "You're the last admin in the system: you must keep your role";
                }
            }
            if (count($errors) === 0) {
                // Si le user dont on a reçu l'id dans l'url est le user connecté et si son rôle ou son username ont changé,
                // mettre à jour la session en reloguant l'utilisateur, sans faire de redirection (4ème paramètre de log_user).
                if (!$is_new && $id === $user->id && ($username != $user->username || $role != $user->role)) {
                     //$this->redirect("user", "profile");/// GROS SOUCIS ICI J'ARRIVE PAS :)))
                     //Controller::log_user($logged_userid, $username, $role, false);
                }
                if ($is_new) {
                    if ($user->is_admin()) {
                        User::add_user_from_admin($username, $password, $fullname, $email, $birthdate, $role);
                    }
                    if (!$user->is_admin()) {
                        User::add_user_from_manager($username, $password, $fullname, $email, $birthdate, $role);
                    }
                } else {
                    User::update_user($id, $username, $fullname, $email, $birthdate, $role);
                    // si à cause d'un update du rôle on est devenu un membre, rediriger vers le profile
                    if ($user->is_member()) {
                        $this->redirect("user", "profile");
                    }
                }
                $this->redirect("user", "user_lst");
            }
        }
        (new View("add_edit_user"))->show(array("username" => $username, "fullname" => $fullname,
            "email" => $email, "birthdate" => $birthdate, "role" => $role, "is_new" => $is_new, "errors" => $errors, "is_admin" => $is_admin));
    }

}
