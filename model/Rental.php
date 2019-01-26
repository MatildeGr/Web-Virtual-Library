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

}
