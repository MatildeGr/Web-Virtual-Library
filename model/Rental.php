<?php

require_once "framework/Model.php";

class Rental extends Model {

    public $id;
    public $user; //ID de l'user.
    public $book; //ID du book.
    public $rentaldate;
    public $returndate;

    public function __construct($user, $book, $rentaldate, $returndate, $id = null) {
        $this->id = $id;
        $this->user = $user;
        $this->book = $book;
        $this->rentaldate = $rentaldate;
        $this->returndate = $returndate;
    }

    public static function currently_rent($userid) {
        $query = self::execute("select * from rental  where user = :user and rentaldate is not null", array("user" => $userid));
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            if ($row['returndate'] === null) {
                $id = $row['book'];
                $book = Book::get_by_id($id);
                $results[] = new Rental($row["user"], $book, $row["rentaldate"], $row["returndate"], $row["id"]);
            }
        }
        return $results;
    }

    public static function getAllRent() {
        $query = self::execute("select * from rental ", array());
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $id = $row['book'];
            $idUser = $row["user"];
            $book = Book::get_by_id($id);
            $user = User::get_user_by_id($idUser);
            $results[] = new Rental($user, $book, $row["rentaldate"], $row["returndate"], $row["id"]);
        }
        return $results;
    }

    public static function getRent($idRent) {
        $query = self::execute("select * FROM rental where id = :id", array("id" => $idRent));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            $id = $data['book'];
            $idUser = $data["user"];
            $book = Book::get_by_id($id);
            $user = User::get_user_by_id($idUser);
            return new Rental($user, $book, $data["rentaldate"], $data["returndate"], $data["id"]);
        }
    }

    public static function getMaxDuration() {
        return Configuration::get("max_time");
    }

    public static function add_rental($user, $book, $rentaldate, $returndate) {
        $id = self::execute("INSERT INTO rental(user,book,rentaldate,returndate)
                 VALUES(:user,:book,:rentaldate,:returndate)", array(
                    "user" => $user,
                    "book" => $book,
                    "rentaldate" => $rentaldate,
                    "returndate" => $returndate,
                        ), true  // pour récupérer l'id généré par la BD
        );
        return $id;
    }

    public static function delRentalById($idRental) {
        self::execute("delete FROM rental where id = :id", array("id" => $idRental));
    }

    public static function delRentalByUserId($idUser) {
        self::execute("delete FROM rental where user = :user", array("user" => $idUser));
    }

    public static function returnRental($idRental) {
        self::execute("UPDATE rental SET returndate=:returndate WHERE id=:id", array("id" => $idRental, "returndate" => ToolsBis::getTodayDateTimeBdd()));
    }

    //Renvoie les book qui sont dans le panier virtuel 
    public static function getBookBasket($iduser) {
        $query = self::execute("select rental.book from rental where user=:user and rentaldate is null", array("user" => $iduser));
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = Book::get_by_id($row['book']);
        }
        return $results;
    }

//Renvoie les book possible a ajouter au panier virtuel CONDITIONS NBCOPIES
    public static function getBookByFilter($userselected, $filter) {
        if (empty($filter)) {
            $query = self::execute("SELECT id from book where id not in(select book from rental"
                            . " where user=:user and rentaldate is null)"
                            . " and nbCopies>0 ", array("user" => $userselected));
        } else {
            $query = self::execute("SELECT id from book where id not in(select book from rental"
                            . " where user=:user and rentaldate is null)"
                            . " and nbCopies>0 "
                            . "AND (title LIKE '%$filter%' or author LIKE '%$filter%' or editor LIKE '%$filter%'"
                            . " or isbn LIKE '%$filter%') ", array("user" => $userselected));
        }
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = Book::get_by_id($row['id']);
        }
        return $results;
    }

    public static function bookToJson($books) {

        $str = "";

        foreach ($books as $book) {
            $isbn = json_encode($book->isbn);
            $title = json_encode($book->title);
            $author = json_encode($book->author);
            $editor = json_encode($book->editor);
            $copies = json_encode($book->nbCopies);

            $str .= "{\"isbn\":$isbn,\"title\":$title,\"author\":$author,\"editor\":$editor,\"copies\":$copies},";
        }
        if ($str !== "") {

            $str = substr($str, 0, strlen($str) - 1);
        }

        return "[$str]";
    }

    public static function getRentalsByFilter($filter) {
        $filter = Rental::filterToSqlQuery($filter);
        $query = self::execute("SELECT rental.id, username,title,rentaldate,returndate "
                        . "FROM rental,book,user "
                        . "WHERE user.id = user AND book.id = book AND rentaldate IS NOT NULL"
                        . " $filter ", array());
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = new Rental($row["username"], $row["title"], $row["rentaldate"], $row["returndate"], $row["id"]);
        }
        return $results;
    }

    private static function filterToSqlQuery($filter) {
        $sql = " ";
        if (($filter)) {
            if (!empty($filter['username'])) {
                $filterUser = $filter['username'];
                $sql .= "AND username LIKE '%$filterUser%' ";
            }
            if (!empty($filter['title'])) {
                $filterTitle = $filter['title'];
                $sql .= " AND title LIKE '%$filterTitle%' ";
            }
            if (!empty($filter['rentaldate'])) {
                $filterReturndate = $filter['rentaldate'];
                $sql .= " AND (rentaldate = '$filterReturndate') ";
            }
            if (!empty($filter['state'])) {
                $state = $filter['state'];

                if ($state == "all") {

                    $sql .= " AND (returndate is not null or returndate is null) ";
                } elseif ($state == "returned") {

                    $sql .= " AND returndate is not null ";
                } elseif ($state == "open") {

                    $sql .= " AND returndate is null ";
                }
            }
        }
        return $sql;
    }

    public static function delete_basket($iduser, $idbook) {
        self::execute("delete from rental where user=:user and book=:book and rentaldate is null", array("user" => $iduser, "book" => $idbook));
    }

    public static function delete_bookrental($idbook) {
        self::execute("delete from rental where book=:book", array("book" => $idbook));
    }

    public static function getMaxLocation() {
        return Configuration::get('max_location');
    }

    //fonction qui renvoie true s'il est possible d'ajouter des livres au panier. 
    public static function checkhowmanyrent($iduser) {
        $query = self::execute("SELECT count(*) from rental where user=:user and returndate is null", array("user" => $iduser));
        $data = $query->fetch();
        return (int) $data[0] < Rental::getMaxLocation();
    }

    //Renvoie le nombre de copies actuellement réservées ou louées d'un livre.
    public static function numberBookedOrRent($idbook) {
        $query = self::execute("SELECT count(*) from rental where book=:book and returndate is null", array("book" => $idbook));
        $data = $query->fetch();
        return (int) $data[0];
    }

    //Renvoie le nombre de copies d'un book
    public static function getNbCopies($idbook) {
        $query = self::execute("SELECT nbCopies from book where id=:id", array("id" => $idbook));
        $data = $query->fetch();
        return (int) $data;
    }

    //Renvoie true si le book est disponible. 
    public static function checkBookAvalaible($idbook) {
        $numberBooked = Rental::numberBookedOrRent($idbook);
        $book = Book::get_by_id($idbook);
        return $book->nbCopies - $numberBooked != 0;
    }

    

    public static function rentalsResourcesToJson($rentals) {
        $str = "";
        foreach ($rentals as $rental) {
            $id = json_encode($rental->id);
            $book = json_encode($rental->book);
            $user = json_encode($rental->user);
            $str .= "{\"id\":$id,\"user\":$user,\"book\":$book},";
        }
        if ($str !== "") {
            $str = substr($str, 0, strlen($str) - 1);
        }

        return "[$str]";
    }

    public static function rentalsEventsToJson($rentals) {

        $todayDate = ToolsBis::getTodayDateTimeBdd();
        $events = "";

        foreach ($rentals as $rental) {
            $id = json_encode($rental->id);
            $rentaldate = json_encode($rental->rentaldate);

            if ($rental->returndate === null) {
                $returndate = ToolsBis::get_datetime($rental->rentaldate . Rental::getMaxDuration());
                $color = json_encode(($returndate < $todayDate) ? '#FE0103' : '#0DA601');
                $returndate = json_encode(ToolsBis::get_datetime($todayDate));
            } else {
                $returndate = json_encode($rental->returndate);
                $color = json_encode(($rental->returndate > ToolsBis::get_datetime($rental->rentaldate . Rental::getMaxDuration())) ? '#FE7677' : '#48F13B');
            }

            $events .= "{\"id\":$id,\"resourceId\":$id,\"start\":$rentaldate,\"end\":$returndate,\"color\":$color},";
        }
        if ($events !== "") {

            $events = substr($events, 0, strlen($events) - 1);
        }

        return "[$events]";
    }

}
