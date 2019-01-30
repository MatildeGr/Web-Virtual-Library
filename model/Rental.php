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
        $query = self::execute("select * from rental  where user = :user", array("user" => $userid));
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

}
