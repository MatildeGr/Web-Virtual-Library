<?php

class ControllerBis extends Controller {

    public function index() {
        if ($this->user_logged()) {
            $this->redirect("User", "profile");
        } else {
            (new View("index"))->show();
        }
    }

}
