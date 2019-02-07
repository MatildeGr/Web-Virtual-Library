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
        //$rentals = Rental::getAllRent();
        $conditions = '';
        $filter = [];

        if (isset($_GET["param1"])) {
            $filter = ToolsBis::url_safe_decode($_GET["param1"]);
            if (!$filter)
                Tools::abort("Bad url parameter");
        }

        //gestion du filtre
        if (isset($_POST['member']) || isset($_POST['book']) || isset($_POST['date']) || isset($_POST['sate'])) {
            if (isset($_POST['member']) && !empty($_POST['member'])) {
                $filter[] = "user LIKE '%$_POST[member]%'";
            }
            if (isset($_POST['book']) && !empty($_POST['book'])) {
                $idBook = Book::getIdBookByWord($_POST['book']);
                $selectId = 'book in (';
                foreach ($idBook as $id) {
                    $selectId .= "$id";
                }
                $selectId .= ")";
            }
            if (isset($_POST['date']) && !empty($_POST['date'])) {
                $rentalDate = ToolsBis::get_date($_POST['date']);
                $filter[] = "rentaldate = '$rentalDate'";
            }
            if (isset($_POST['state']) && !empty($_POST['state'])) {
                if ($_POST['state'] == 'all') {
                    $filter[] = "returndate is not null or returndate is null";
                } elseif ($_POST['state'] == 'returned') {
                    $filter[] = "returndate is not null";
                } elseif ($_POST['state'] == 'open') {
                    $filter[] = " returndate is null";
                }
            }
            $this->redirect("rental", "returnBook", ToolsBis::url_safe_encode($filter));
        }

        if ($filter) {
            $first = true;

            foreach ($filter as $f) {
                // si c'est la premiere condition, on met where, sinon on met and
                if ($first) {
                    $first = false;
                    $conditions .= " WHERE $f ";
                } else {
                    $conditions .= " AND $f ";
                }
            }
        }
        var_dump($conditions);
        $rentals = Rental::getRentalsByFilter($conditions);

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
        $user=$this->get_user_or_redirect();
        $books_to_rent = Rental::getBookBasket($user->id);
        $datetoday = ToolsBis::getTodayDateTimeBdd();
        $returndate = ToolsBis::format_datetimBD($datetoday.Rental::getMaxDuration());
        foreach ($books_to_rent as $book) {
            Rental::add_rental($user->id, $book->id, $datetoday, $returndate);
             Rental::delete_basket($user->id, $book->id);
        }
        $this->redirect("book", "basket");
    }

}
