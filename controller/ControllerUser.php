<?php

class ControllerUser extends ControllerBis {

    public function index() {
        $this->profile();
    }

    public function profile() {
        $user = $this->get_user_or_redirect();
        if (isset($_GET["param1"]) && $_GET["param1"] !== "") {
            $user = User::get_user_by_username($_GET["param1"]);
        }
        (new View("profile"))->show(array("user" => $user));
    }
    

}
