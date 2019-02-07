<?php

require_once 'controller/controllerbis.php';
require_once 'model/Book.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'useful/ToolsBis.php';
require_once 'model/Rental.php';

class ControllerBook extends ControllerBis {

    public function basket() {
        $user = $this->get_user_or_redirect();
        $all_books = Rental::getBookYouCanRent($user->id); //Book qu'on peut ajouter au panier virtuel. 
        $books_to_rent = Rental::getBookBasket($user->id); //Tableau de BOOK dans le panier virtuel
        (new View("basket"))->show(array("user" => $user, "books" => $all_books, "books_to_rent" => $books_to_rent));
    }

    //Ajoute un livre au panier virtuel et met à jour la view basket.
    public function add_basket() {
        $user = $this->get_user_or_redirect();
        if (ToolsBis::check_fields(['bookid'])) {
            $idbook = trim($_POST['bookid']);
            Rental::add_rental($user->id, $idbook, null, null);
            $this->redirect("book", "basket");
        }
    }

    //Supprime un livre du panier virtuel et met à jour la view basket.
    public function delete_basket() {
        $user = $this->get_user_or_redirect();
        if (ToolsBis::check_fields(['bookid'])) {
            $idbook = trim($_POST['bookid']);
            Rental::delete_basket($user->id, $idbook);
            $this->redirect("book", "basket");
        }
    }

    const UPLOAD_ERR_OK = 0;

    public function add_edit_book() {
        $errors = [];
        $user = $this->get_user_or_redirect();
        $is_admin = $user->is_admin();
        if (isset($_GET['param1'])) {
            $id = trim($_GET['param1']);
            $book = Book::get_by_id($id);
            if (!$book) {
                ToolsBis::abort('Unknown book');
            }
            $isbn = $book->isbn;
            $title = $book->title;
            $author = $book->author;
            $editor = $book->editor;
            $picture_path = $book->picture;
            $titlePage = "Edit";
        } else {
            $id = null;
            $isbn = '';
            $title = '';
            $author = '';
            $editor = '';
            $picture_path = '';
            $titlePage = "Add new";
        }
        if ($this->isMember() || $this->isManager()) {
            $view = true;
            $titlePage = "View";
        } else {
            $view = false;
        }

        if (ToolsBis::check_fields(['cancel'])) {
            $this->redirect("book", "basket");
        }
        if (ToolsBis::check_fields(['save', 'isbn', 'title', 'author', 'editor']) && !$view) {
            //&& ($user->is_admin() || ToolsBis::check_fields(['role'])) à quoi sert elle ???
            $isbn = trim($_POST['isbn']);
            $title = trim($_POST['title']);
            $author = trim($_POST['author']);
            $editor = trim($_POST['editor']);
            $errors = Book::validateBook($id, $isbn, $title, $author, $editor);
            if (isset($_FILES['picture']) && $_FILES['picture']['error'] === self::UPLOAD_ERR_OK) {
                $errors = ToolsBis::validate_photo($_FILES['picture']);
                if (empty($errors)) {
                    $saveTo = ToolsBis::generate_photo_name($_FILES['picture']);
                    $oldFileName = $picture_path;
                    if ($oldFileName && file_exists("picture/" . $oldFileName)) {
                        unlink("picture/" . $oldFileName);
                    }
                    move_uploaded_file($_FILES['picture']['tmp_name'], "picture/$saveTo");
                    $picture_path = "picture/$saveTo";
                }
            }


            if (count($errors) === 0) {
                if ($is_new) {
                    Book::add_book($isbn, $title, $author, $editor, $picture_path);
                } else {
                    Book::edit_book($id, $isbn, $title, $author, $editor, $picture_path);
                }
                $this->redirect("book", "basket");
            }
        }
        (new View("add_edit_book"))->show(array("isbn" => $isbn, "title" => $title,
            "author" => $author, "editor" => $editor, "picture" => $picture_path,
            "errors" => $errors, "view" => $view, "titlePage" => $titlePage, "is_admin" => $is_admin));
    }

    public function delete_book() {
        $user = $this->get_user_or_redirect();
        $this->check_admin();
        $errors = [];
        $book_del = '';

        if (isset($_GET['param1'])) {
            $id = trim($_GET['param1']);
            $book_del = book::get_by_id($id);

            if (!$book_del) {
                ToolsBis::abort('Unknown book');
            }
            $book_del = $book_del->title;
        } else {
            $this->redirect("book", "basket");
        }

        if (isset($_POST['confirm'])) {
            Book::del_book_by_id($id);
            $this->redirect("book", "basket");
        } elseif (isset($_POST['cancel'])) {
            $this->redirect("book", "basket");
        }
        (new View("delete_book"))->show(array("user" => $user, "book_del" => $book_del, "errors" => $errors));
    }

    public function add_book_to_rent() {
        if (ToolsBis::check_fields(['rent'])) {
            $user = $this->get_user_or_redirect();
            if (isset($_GET['param1'])) {
                $id = trim($_GET['param1']);
                $book_to_rent = book::get_by_id($id);
                if (!$book_to_rent) {
                    ToolsBis::abort('Unknown book');
                }
            } else {
                $this->redirect("book", "basket");
            }
            $books_to_rent[] = new Book($book_to_rent->title, $book_to_rent->id, $book_to_rent->author, $book_to_rent->editor, $book_to_rent->picture);
        }
        (new View("basket"))->show(array("user" => $user, "books" => $all_books, "books_to_rent" => $books_to_rent));
    }

}
