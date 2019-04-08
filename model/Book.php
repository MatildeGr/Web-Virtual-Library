<?php

require_once "framework/Model.php";

class Book extends Model {

    public $id;
    public $isbn;
    public $title;
    public $author;
    public $editor;
    public $picture;
    public $nbCopies;

    public function __construct($isbn, $title, $author, $editor, $picture, $id = null, $nbCopies = 1) {
        $this->id = $id;
        $this->isbn = $isbn;
        $this->title = $title;
        $this->author = $author;
        $this->editor = $editor;
        $this->picture = $picture;
        $this->nbCopies = $nbCopies;
    }

    private static function getDefaultPicture() {
        return Configuration::get("default_picture");
    }

    public static function getDefaultLocation() {
        return Configuration::get("default_location");
    }

    public static function add_book($isbn, $title, $author, $editor, $picture, $copy) {
        if (empty($picture)) {
            $picture = Book::getDefaultPicture();
        }
        $isbn = str_replace("-", "", $isbn);
        $id = self::execute("INSERT INTO book (isbn, title, author, editor, picture,nbCopies) 
                VALUES (:isbn,:title,:author,:editor,:picture,:nbCopies)", array(
                    "isbn" => $isbn,
                    "title" => $title,
                    "author" => $author,
                    "editor" => $editor,
                    "picture" => $picture,
                    "nbCopies" => $copy,
                        ), true// genere l'id du book ajouté
        );
        return $id;
    }

    public static function edit_book($id, $isbn, $title, $author, $editor, $picture, $nbCopies) {
        $isbn = str_replace("-", "", $isbn);
        $id = self::execute("UPDATE book SET isbn=:isbn, title=:title, author=:author, editor=:editor, picture=:picture,nbCopies=:nbCopies
                WHERE id=:id", array(
                    "isbn" => $isbn,
                    "title" => $title,
                    "author" => $author,
                    "editor" => $editor,
                    "picture" => $picture,
                    "nbCopies" => $nbCopies,
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
            return new Book($data["isbn"], $data["title"], $data["author"], $data["editor"], $data["picture"], $data["id"], $data["nbCopies"]);
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

    public function validateBook() {
        $errors = [];
        $isbn = str_replace("-", "", $this->isbn);
        $isbn.= ToolsBis::makeCheckDigit($isbn);
        $book = Book::getBookByIsbn($isbn);
        $numberBooked = Rental::numberBookedOrRent($this->id);
        if ($book) {
            if (($this->id == null && $book->id !== $this->id) || ($book->isbn == $isbn)) {
                $errors[] = "This ISBN is already used.";
            }
        } elseif (empty($isbn)) {
            $errors[] = "ISBN is required.";
        } elseif (!preg_match("#^[0-9-]+$#", $isbn)) {
            $errors[] = "ISBN must contains only numbers.";
        } else if (!ToolsBis::check_string_length($isbn, 12, 16)) {
            $errors[] = "ISBN length must be 12 characters.";
        }
        if ($this->nbCopies < $numberBooked) {
            $errors[] = "Copies must remain greater than or equal to the number of copies currently reserved or rented which is $numberBooked ";
        }
        if ($this->nbCopies < 1) {
            $errors[] = "Copies cannot be a negative number or 0.";
        }
        if (empty($this->title)) {
            $errors[] = "Title is required.";
        }
        if (empty($this->author)) {
            $errors[] = "Author is required.";
        }
        if (empty($this->editor)) {
            $errors[] = "Editor is required.";
        }
        return $errors;
    }

    //Renvoie le nombre de copie d'un book 
    public static function getCopy($idBook) {
        $query = self::execute("SELECT nbCopies from book where id=:id", array("id" => $idBook));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return (int) $data[0];
        }
    }

    //Ajoute une copie d'un book
    public static function updateCopyPlus($idBook) {
        $nbCopy = Book::getCopy($idBook);
        $nbCopies = $nbCopy - 1;
        self::execute("UPDATE book SET nbCopies=:nbCopies where id=:id", array("id" => $idBook, "nbCopies" => $nbCopies));
    }

    //Enlève une copie d'un book
    public static function updateCopyMinus($idBook) {
        $nbCopy = Book::getCopy($idBook);
        $nbCopies = $nbCopy + 1;
        self::execute("UPDATE book SET nbCopies=:nbCopies where id=:id", array("id" => $idBook, "nbCopies" => $nbCopies));
    }

}
