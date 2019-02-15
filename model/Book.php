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

    public static function getDefaultPicture() {
        return Configuration::get("default_picture");
    }

    public static function getDefaultLocation() {
        return Configuration::get("default_location");
    }

    public static function add_book($isbn, $title, $author, $editor, $picture) {
        if (empty($picture)) {
            $picture = Book::getDefaultPicture();
        }
        $isbn = str_replace("-", "", $isbn);
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

    public static function edit_book($id, $isbn, $title, $author, $editor, $picture) {
        $isbn = str_replace("-", "", $isbn);
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

    public static function getBookByIsbn($isbn) {
        $query = self::execute("SELECT * FROM book where isbn = :isbn", array("isbn" => $isbn));
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

    //retourne tous les id livres comprenant le mot de recherche
    public static function getIdBookByWord($word) {
        $query = self::execute("SELECT id FROM book where title like :word", array("word" => "%$word%"));
        $data = $query->fetchAll();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return $data;
        }
    }

    public static function validateBook($id, $isbn, $title, $author, $editor) {
        $errors = [];
        $book = Book::getBookByIsbn($isbn);
        if ($book && ($book->id !== $id)) {
            $errors[] = "This ISBN is already used.";
        } elseif (!preg_match("#^[0-9-]+$#", $isbn)) {
            $errors[] = "ISBN must contains only numbers.";
        } else if (!ToolsBis::check_string_length(str_replace("-", "", $isbn), 13, 13)) {
            $errors[] = "ISBN length must be 13 characters.";
        }
        var_dump($isbn);
        if (empty($title)) {
            $errors[] = "Title is required.";
        }
        if (empty($author)) {
            $errors[] = "Author is required.";
        }
        if (empty($editor)) {
            $errors[] = "Editor is required.";
        }
        return $errors;
    }

}
