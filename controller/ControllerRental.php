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
        $conditions = '';
        $filter = [];
        
        if (isset($_GET["param1"])) {
            $filter = ToolsBis::url_safe_decode($_GET["param1"]);
            if (!$filter)
                Tools::abort("Bad url parameter");
        }else if (isset($_POST['member']) || isset($_POST['book']) || isset($_POST['date']) || isset($_POST['sate'])) {
            if (isset($_POST['member']) && !empty($_POST['member'])) {
                $filterUser = $_POST['member'];
                $filter[] = "AND username LIKE '$filterUser%' ";
            }
            if (isset($_POST['book']) && !empty($_POST['book'])) {
                $filterBook = $_POST['book'];
                $filter[] = " AND title LIKE '$filterBook%' ";
            }
            if (isset($_POST['date']) && !empty($_POST['date'])) {
                $filterRentalDate = ToolsBis::get_date($_POST['date']);
                $filter[] = " AND (rentaldate = '$filterRentalDate' or returndate = '$filterRentalDate') ";
            }
            if (isset($_POST['state']) && !empty($_POST['state'])) {
                $filterAll = false;$filterReturn = false;$filterOpen = false;
                if ($_POST['state'] == 'all') {
                    $filterAll = true;
                    $filter[] = " AND (returndate is not null or returndate is null) ";                
                } elseif ($_POST['state'] == 'returned') {
                    $filterReturn = true;
                    $filter[] = " AND returndate is not null ";
                } elseif ($_POST['state'] == 'open') {
                    $filterOpen = true;
                    $filter[] = " AND returndate is null ";
                }
            }
            $this->redirect("rental", "returnBook", ToolsBis::url_safe_encode($filter));
        }else{
            $filterUser ='';
            $filterBook = '';
            $filterRentalDate =null;
            $filterAll = false;
            $filterReturn = false;
            $filterOpen = false;
        }


        if ($filter) {
            foreach ($filter as $f) {
                $conditions .= " $f ";
            }
        }
        $rentals = Rental::getRentalsByFilter($conditions);

        (new View("return"))->show(array("rentals" => $rentals, "isAdmin" => $isAdmin));
    }//,"filterUser"=>$filterUser,"filterBook"=>$filterBook,
         // "filterRentalDate"=>$filterRentalDate,"filterAll"=>$filterAll,"filterReturn"=>$filterReturn,"filterOpen"=>$filterOpen

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
            } else if ($rent->returndate !== null) {
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

    public function confirm_basket() {
        $user = $this->get_user_or_redirect();
        $books_to_rent = Rental::getBookBasket($user->id);
        $datetoday = ToolsBis::getTodayDate();
        $returndate = $datetoday . Rental::getMaxDuration();
        foreach ($books_to_rent as $book) {
            Rental::add_rental($user->id, $book->id, $datetoday, $returndate);
        }
        $this->redirect("book", "basket");
    }

}
