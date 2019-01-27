<?php

require_once 'controller/controllerbis.php';
require_once 'model/Book.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'useful/ToolsBis.php';

class ControllerBook extends ControllerBis {
    public function edit_book(){
        (new View("book"))->show(array());
    }
    
}
