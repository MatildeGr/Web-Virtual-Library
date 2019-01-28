<?php

require_once 'controller/controllerbis.php';
require_once 'model/Book.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'useful/ToolsBis.php';

class ControllerBook extends ControllerBis {
    
    public function basket() {
        $user = $this->get_user_or_redirect();
        $this->check_manager_or_admin();
        $all_books = Book::get_books();
        (new View("basket"))->show(array("user" => $user, "books" => $all_books));
    }

}
