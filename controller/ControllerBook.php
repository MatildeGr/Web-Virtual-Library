<?php

require_once 'controller/controllerbis.php';
require_once 'model/Book.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'useful/ToolsBis.php';
require_once 'model/Rental.php';

class ControllerBook extends ControllerBis {

    const UPLOAD_ERR_OK = 0;

    public function add_edit_book() {
        $errors = [];
        $userselected = $this->get_user_or_redirect();
        $is_admin = $userselected->is_admin();
        if (isset($_GET['userselected'])) {
            $userselected = trim($_POST["userselected"]);
        }

        if (isset($_GET['param1'])) {
            $id = trim($_GET['param1']);
            $book = Book::get_by_id($id);
            if (!$book) {
                ToolsBis::abort('Unknown book');
            }
            $is_new = false;
            $isbn = $book->isbn;
            $title = $book->title;
            $author = $book->author;
            $editor = $book->editor;
            $picture_path = $book->picture;
            $nbCopies = $book->nbCopies;
            $titlePage = "Edit";
        } else {
            $is_new = true;
            $id = null;
            $isbn = '';
            $title = '';
            $author = '';
            $editor = '';
            $picture_path = '';
            $titlePage = "Add new";
            $nbCopies = 1;
        }
        if ($this->isMember() || $this->isManager()) {
            $view = true;
            $titlePage = "View";
        } else {
            $view = false;
        }

        if (ToolsBis::check_fields(['cancel'])) {
            $this->redirect("rental", "basket", $userselected->id);
        }
        if (ToolsBis::check_fields(['save', 'isbn', 'title', 'author', 'editor', 'nbCopies']) && !$view) {
            $isbn = trim($_POST['isbn']);
            $title = trim($_POST['title']);
            $author = trim($_POST['author']);
            $editor = trim($_POST['editor']);
            $nbCopies = trim($_POST['nbCopies']);
            $errors = (new Book($isbn, $title, $author, $editor, null, $id, $nbCopies))->validateBook();
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
                $isbn .= ToolsBis::makeCheckDigit($isbn);
                if ($is_new) {
                    Book::add_book($isbn, $title, $author, $editor, $picture_path, $nbCopies);
                } else {
                    Book::edit_book($id, $isbn, $title, $author, $editor, $picture_path, $nbCopies);
                }
                $this->redirect("rental", "basket", $userselected->id);
            }
        }
        (new View("add_edit_book"))->show(array("isbn" => $isbn, "title" => $title,
            "author" => $author, "editor" => $editor, "picture" => $picture_path, "nbCopies" => $nbCopies,
            "errors" => $errors, "view" => $view, "titlePage" => $titlePage, "is_admin" => $is_admin));
    }

    public function delete_book() {
        $user = $this->get_user_or_redirect();
        $this->check_admin();
        $errors = [];
        $book_del = '';
        if (isset($_GET['userselected'])) {
            $userselected = trim($_GET["userselected"]);
        }
        if (isset($_GET['param1'])) {
            $id = trim($_GET['param1']);
            $book = book::get_by_id($id);
            if (!$book) {
                ToolsBis::abort('Unknown book');
            }
            $book_del = $book->title;
        } else {
            ToolsBis::abort("pas de bookid");
            $this->redirect("rental", "basket", $userselected);
        }
        if (isset($_POST['confirm'])) {
            Rental::delete_bookrental($id);
            Book::del_book_by_id($id);
            $this->redirect("rental", "basket");
        } elseif (isset($_POST['cancel'])) {
            $this->redirect("rental", "basket", $userselected);
        }
        (new View("delete_book"))->show(array("user" => $user, "book_del" => $book_del, "errors" => $errors));
    }

}
