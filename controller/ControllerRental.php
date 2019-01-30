<?php

require_once 'controller/controllerbis.php';
require_once 'model/Rental.php';
require_once 'model/Book.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'useful/ToolsBis.php';

class ControllerRental extends ControllerBis {

    public function returnBook() {
        $user = $this->get_user_or_redirect();
        $this->check_manager_or_admin();
        $isAdmin = $this->isAdmin();
        $rentals = Rental::getAllRent();

        (new View("return"))->show(array("rentals" => $rentals, "isAdmin" => $isAdmin));
    }

    public function deleteRental() {
        $user = $this->get_user_or_redirect();
        $this->check_admin();
        if (isset($_GET['param1'])) {
            $idRent = trim($_GET['param1']);
            $rent = Rental::getRent($idRent);
            if (!$rent) {
                ToolsBis::abort("Unknown rent");
            }
            $id = $rent->id;
        } else {
            $this->redirect("rental", "returnBook");
        }
        if (isset($_POST['confirm'])) {
            Rental::delRentalById($id);
            $this->redirect("rental", "returnBook");
        } elseif (isset($_POST['cancel'])) {
            $this->redirect("rental", "returnBook");
        }
        (new View("delete_rental"))->show(array("rent" => $rent));
    }

    public function confirmReturn() {
        $user = $this->get_user_or_redirect();
        $this->check_manager_or_admin();
        if (isset($_GET['param1'])) {
            $idRent = trim($_GET['param1']);
            $rent = Rental::getRent($idRent);
            if (!$rent) {
                ToolsBis::abort("Unknown rent");
            }else if($rent->returndate !== null){
                ToolsBis::abort("you can not return an rental already returned");
            }
            $id = $rent->id;
        } else {
            $this->redirect("rental", "returnBook");
        }
        if (isset($_POST['confirm'])) {
            Rental::returnRental($id);
            $this->redirect("rental", "returnBook");
        } elseif (isset($_POST['cancel'])) {
            $this->redirect("rental", "returnBook");
        }
        (new View("confirmReturn"))->show(array("rent" => $rent));
    }

}
