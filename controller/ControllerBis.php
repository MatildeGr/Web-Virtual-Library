<?php
require_once 'model/User.php';
require_once 'framework/Controller.php';

class ControllerBis extends Controller {

    public function index() {
        if ($this->user_logged()) {
            $this->redirect("User", "profile");
        } else {
            (new View("index"))->show();
        }
    }

    /* ============================================================ */
    /* ===  Fonctions de vérifications de connection et de role === */
    /* ============================================================ */

    function isLoggedIn() {
        return ToolsBis::check_fields(['user'], $_SESSION);
    }

    function check_login() {
        global $logged_user;
        if (!ControllerBis::isLoggedIn())
            redirect('index.php');
        else {
            $logged_user = $_SESSION['user'];
        }
    }

    function get_logged_role() {
        return $_SESSION['user']->role;
    }

    function get_logged_userid() {
        return $_SESSION['user']->id;
    }

    function get_logged_username() {
        return $_SESSION['user']->username;
    }

    function isAdmin() {
        return $this->get_logged_role() === 'admin';
    }

    function isManager() {
        return $this->get_logged_role() === 'manager';
    }

    function isMember() {
        return $this->get_logged_role() === 'member';
    }

    function check_admin() {
        ControllerBis::check_login();
        if (!$this->isAdmin())
            ToolsBis::abort("You must have the 'admin' role");
    }

    function check_manager_or_admin() {
        ControllerBis::check_login();
        if (!$this->isAdmin() && !$this->isManager())
            ToolsBis::abort("You must have the 'manager' or the 'admin' role");
    }

}
