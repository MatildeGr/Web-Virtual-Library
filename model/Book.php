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
//
//    public static function validate_isbn($isbn) {
//        
//    }

    public static function add_book($isbn, $title, $author, $editor, $picture) {
        if (empty($picture))
            $picture = null;
        $id = self::execute("INSERT INTO book (isbn, title, author, editor, picture) 
                VALUES (:isbn,:title,:author,:editor,:picture)", array(
                    "isbn" => $isbn,
                    "title" => $title,
                    "author" => $author,
                    "editor" => $editor,
                    "picture" => $picture,
                        ), true// genere l'id du book ajouté
        );
        return $id;
    }

    //renvoie la liste de book
    public static function get_books() {
        $query = self::execute("SELECT * FROM book", array());
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = new Book($row["isbn"], $row["title"], $row["author"], $row["editor"], $row["picture"], $row["id"]);
        }
        return $results;
    }

    public static function edit_book($id, $isbn, $title, $author, $editor, $picture) {
       // if (validate_isbn($isbn)) {
            $id = self::execute("UPDATE book SET isbn=:isbn, title=:title, author=:author, editor=:editor, picture=:picture
                WHERE id=:id", array(
                        "isbn" => $isbn,
                        "title" => $title,
                        "author" => $author,
                        "editor" => $editor,
                        "picture" => $picture,
                        "id" => $id
                            )
            );
        //}
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
    
     public static function del_book_by_id($id) {
        self::execute("delete FROM book where id = :id", array("id" => $id));
    }

}
