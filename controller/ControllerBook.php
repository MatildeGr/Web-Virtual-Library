<?php

require_once 'controller/controllerbis.php';
require_once 'model/Book.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'useful/ToolsBis.php';

class ControllerBook extends ControllerBis {

    public function basket() {
        $user = $this->get_user_or_redirect();
        $this->check_manager_or_admin();
        $all_books = Book::get_books();
        (new View("basket"))->show(array("user" => $user, "books" => $all_books));
    }

    public function add_edit_book() {
        $errors = [];
        $user = $this->get_user_or_redirect();
        $this->check_manager_or_admin();
        $is_admin = $user->is_admin();
        if (isset($_GET['param1'])) {
            $is_new = false;
            $id = trim($_GET['param1']);
            $book = Book::get_by_id($id);
            if (!$user) {
                abort('Unknown user');
            }
            $isbn = $book->isbn;
            $title = $book->title;
            $author = $book->author;
            $editor = $book->editor;
            $picture_path = $book->picture;
        } else {
            $is_new = true;
            $id = null;
            $isbn = '';
            $title = '';
            $author = '';
            $editor = '';
            $picture_path = '';
        }

        if (ToolsBis::check_fields(['cancel'])) {
            $this->redirect("book", "basket");
        }
        if (ToolsBis::check_fields(['save', 'isbn', 'title', 'author', 'editor']) && ($user->is_admin() || ToolsBis::check_fields(['role']))) {

            $isbn = trim($_POST['isbn']);
            $title = trim($_POST['title']);
            $author = trim($_POST['author']);
            $editor = trim($_POST['editor']);
            $picture_path = trim($_POST[picture_path]);
            //$errors = Book::validate_book($isbn, $title, $author, $editor, $picture_path);
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
            "author" => $author, "editor" => $editor, "picture" => $picture_path, "is_new" => $is_new, "errors" => $errors, "is_admin" => $is_admin));
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

}
