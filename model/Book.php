<?php

require_once "framework/Model.php";

class Book extends Model {

    public $id;
    public $isbn;
    public $title;
    public $author;
    public $editor;
    public $picture;

    public function __construct($isbn, $title, $author, $editor, $picture, $id = null) {
        $this->id = $id;
        $this->isbn = $isbn;
        $this->title = $title;
        $this->author = $author;
        $this->editor = $editor;
        $this->picture = $picture;
    }

    public static function get_by_id($id) {
        $query = self::execute("SELECT * FROM book where id = :id", array("id" => $id));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Book($data["isbn"], $data["title"], $data["author"], $data["editor"], $data["picture"], $data["id"]);
        }
    }

}
