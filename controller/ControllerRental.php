<?php

require_once 'controller/controllerbis.php';
require_once 'model/Rental.php';
require_once 'model/Book.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'useful/ToolsBis.php';

class ControllerRental extends ControllerBis {

    public function basket() {
        $user = $this->get_user_or_redirect();
        $userselected = $user->id;
        $filter = ' ';
        if (!($user->is_admin()) && !($user->is_manager())) {
            $menu = "view/menu_member.html";
        } else {
            $menu = "view/menu.html";
        }
        if (isset($_GET["param1"])) {
            if ($user->is_admin() || $user->is_manager() || $user->id == $_GET['param1']) {
                if (User::get_user_by_id($_GET['param1'])) {
                    $userselected = trim($_GET['param1']);
                } else {
                    ToolsBis::abort("Unknown User");
                }
            } else {
                ToolsBis::abort("You may not order a basket for someone since you're not an admin or manager.");
            }
        } elseif (isset($_POST["userselected"])) {
            $userselected = trim($_POST["userselected"]);
            $this->redirect("rental", "basket", $userselected);
        } else {
            $this->redirect("rental", "basket", $userselected);
        }
        if (isset($_GET['param2'])) {
            $filter = ToolsBis::url_safe_decode($_GET['param2']);
            if (!$filter) {
                ToolsBis::abort("Bad url parameter");
            }
        }
        if (isset($_POST['filter']) && !empty($_POST['filter'])) {
            $filterBook = $_POST['filter'];
            $filter = $filterBook;
            $this->redirect("rental", "basket", $userselected, ToolsBis::url_safe_encode($filter));
        } else {
            $filterBook = ' ';
        }
        $checkRent = Rental::checkhowmanyrent($userselected);
        $all_books = Rental::getBookByFilter($userselected, $filter); //Book possible a ajouter
        $books_to_rent = Rental::getBookBasket($userselected); //Tableau de BOOK dans le panier virtuel
        $users = User::get_users();
        (new View("basket"))->show(array("user" => $user,
            "books" => $all_books,
            "books_to_rent" => $books_to_rent,
            "users" => $users,
            "userselected" => $userselected,
            "filter" => $filter,
            "menu" => $menu,
            "checkRent" => $checkRent));
    }

    //permet de clear le filtre de la liste des livres. 
    public function clearfilter() {
        $this->redirect("rental", "basket", $this->checkUserSelected(), " ");
    }

    //vérifie si un user est selectionner ou prend l'id de l'user connecter. 
    private function checkUserSelected() {
        $user = $this->get_user_or_redirect();
        return isset($_POST["userselected"]) ? $_POST['userselected'] : $user->id;
    }

    //Ajoute un livre au panier virtuel et met à jour la view basket.
    public function add_basket() {
        $user = $this->get_user_or_redirect();
        if (ToolsBis::check_fields(['filter'])) {
            $filter = $_POST['filter'];
        }
        if (ToolsBis::check_fields(['bookid']) && ToolsBis::check_fields(['userselected'])) {
            $idbook = trim($_POST['bookid']);
            if (Book::getCopy($idbook) < 1) {
                ToolsBis::abort("This book is not avalaible");
            }
            Rental::add_rental($this->checkUserSelected(), $idbook, null, null);
            $this->redirect("rental", "basket", $this->checkUserSelected(), ToolsBis::url_safe_encode($filter));
        }
    }

    //Supprime un livre du panier virtuel et met à jour la view basket.
    public function delete_basket() {
        $user = $this->get_user_or_redirect();
        if (ToolsBis::check_fields(['filter'])) {
            $filter = $_POST['filter'];
        }
        if (ToolsBis::check_fields(['bookid']) && ToolsBis::check_fields(['userselected'])) {
            $idbook = trim($_POST['bookid']);
            Rental::delete_basket($this->checkUserSelected(), $idbook);
            $this->redirect("rental", "basket", $this->checkUserSelected(), ToolsBis::url_safe_encode($filter));
        }
    }

    public function returnBook() {
        $user = $this->get_user_or_redirect();
        $this->check_manager_or_admin();
        $isAdmin = $this->isAdmin();
        $filter = [];
        $filterUser = "";
        $filterBook = "";
        $filterRentalDate = null;
        $filterState = "all";

        if (isset($_POST['delete']) && isset($_POST['filter'])) {

            $this->redirect("rental", "deleteRental", $_POST['delete'], $_POST['filter']);
        }

        if (isset($_POST['return']) && isset($_POST['filter'])) {

            $this->redirect("rental", "deleteRental", $_POST['return'], $_POST['filter']);
        }


        if (isset($_GET["param1"])) {

            $filter = ToolsBis::url_safe_decode($_GET["param1"]);
            if (!empty($filter['username']))
                $filterUser = $filter['username'];
            if (!empty($filter['title']))
                $filterBook = $filter['title'];
            if (!empty($filter['rentaldate']))
                $filterRentalDate = $filter['rentaldate'];
            if (!empty($filter['state']))
                $filterState = $filter['state'];
        } else if (isset($_POST['member']) || isset($_POST['book']) || isset($_POST['date']) || isset($_POST['sate'])) {

            if (isset($_POST['member']) && !empty($_POST['member'])) {
                $filter['username'] = $_POST['member'];
            }

            if (isset($_POST['book']) && !empty($_POST['book'])) {
                $filter['title'] = $_POST['book'];
            }

            if (isset($_POST['date']) && !empty($_POST['date'])) {
                $filter['rentaldate'] = ToolsBis::get_date($_POST['date']);
            }

            if (isset($_POST['state']) && !empty($_POST['state'])) {
                $state = $_POST['state'];
                $filter['state'] = $state;
            }
            $this->redirect("rental", "returnBook", ToolsBis::url_safe_encode($filter));
        } else {
            $this->redirect("rental", "returnBook", ToolsBis::url_safe_encode($filter));
        }

        $rentals = Rental::getRentalsByFilter($filter);

        (new View("return"))->show(array("rentals" => $rentals, "isAdmin" => $isAdmin, "filterUser" => $filterUser, "filterBook" => $filterBook,
            "filterRentalDate" => $filterRentalDate, "filterState" => $filterState, "filter" => ToolsBis::url_safe_encode($filter)));
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
            $this->redirect("rental", "returnBook", $_GET["param2"]);
        }
        if (isset($_POST['confirm'])) {
            Rental::delRentalById($id);
            $this->redirect("rental", "returnBook", $_GET["param2"]);
        } elseif (isset($_POST['cancel'])) {
            $this->redirect("rental", "returnBook", $_GET["param2"]);
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
            $this->redirect("rental", "returnBook",$_GET["param2"]);
        }
        if (isset($_POST['confirm'])) {
            Rental::returnRental($id);
            $this->redirect("rental", "returnBook",$_GET["param2"]);
        } elseif (isset($_POST['cancel'])) {
            $this->redirect("rental", "returnBook",$_GET["param2"]);
        }
        (new View("confirmReturn"))->show(array("rent" => $rent));
    }

    public function confirm_basket() {
        $user = $this->get_user_or_redirect();
        $books_to_rent = Rental::getBookBasket($this->checkUserSelected());
        $datetoday = ToolsBis::getTodayDateTimeBdd();
        foreach ($books_to_rent as $book) {
            Rental::add_rental($this->checkUserSelected(), $book->id, $datetoday, null);
            Rental::delete_basket($this->checkUserSelected(), $book->id);
        }
        $this->redirect("rental", "basket", $iduser);
    }

    public function clear_basket() {
        $user = $this->get_user_or_redirect();
        $books_to_rent = Rental::getBookBasket($this->checkUserSelected());
        foreach ($books_to_rent as $book) {
            Rental::delete_basket($this->checkUserSelected(), $book->id);
        }
        $this->redirect("rental", "basket", $this->checkUserSelected());
    }

}
