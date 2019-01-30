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

    public static function returnRental($idRental){
        self::execute("UPDATE rental SET returndate=:returndate WHERE id=:id", 
                array("id"=>$idRental, "returndate"=> ToolsBis::getTodayDateTimeBdd()));
    }
    
    

}
