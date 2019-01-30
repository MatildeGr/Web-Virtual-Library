<?php

require_once 'controller/controllerbis.php';
require_once 'model/Rental.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'useful/ToolsBis.php';

class ControllerRental extends ControllerBis {

    
    public function returnBook(){
        (new View("return"))->show(array());
    }

    
}
