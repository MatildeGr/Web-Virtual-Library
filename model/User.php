<?php

require_once "framework/Model.php";

class User extends Model {

    public $id;
    public $fullname;
    public $username;
    public $hashed_password;
    public $email;
    public $birthdate;
    public $role;

    public function __construct($fullname, $username, $hashed_password, $email, $role, $birthdate, $id = null) {
        $this->id = $id;
        $this->fullname = $fullname;
        $this->username = $username;
        $this->hashed_password = $hashed_password;
        $this->email = $email;
        $this->birthdate = $birthdate;
        $this->role = $role;
    }

    public function is_member() {
        return $this->role === "member";
    }

    public function is_manager() {
        return $this->role === "manager";
    }

    public function is_admin() {
        return $this->role === "admin";
    }

    public static function get_user_by_id($id) {
        $query = self::execute("select * FROM user where id = :id", array("id" => $id));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data['fullname'], $data["username"], $data["password"], $data["email"], $data["role"], $data["birthdate"], $data["id"]);
        }
    }

    public static function get_user_by_username($username) {
        $query = self::execute("SELECT * FROM user where username = :username", array("username" => $username));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data['fullname'], $data["username"], $data["password"], $data["email"], $data["role"], $data["birthdate"], $data["id"]);
        }
    }

    public static function get_user_by_email($email) {
        $query = self::execute("SELECT * FROM user where email = :email", array("email" => $email));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data['fullname'], $data["username"], $data["password"], $data["email"], $data["role"], $data["birthdate"], $data["id"]);
        }
    }

    public static function get_users() {
        $query = self::execute("SELECT * FROM user", array());
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = new User($row["fullname"], $row["username"], $row["password"], $row["email"], $row["role"], $row["birthdate"], $row["id"]);
        }
        return $results;
    }


    //renvoie un tableau d'erreur(s) 
    //le tableau est vide s'il n'y a pas d'erreur.
    public static function validate_login($username, $password) {
        $errors = [];
        $member = User::get_user_by_username($username);
        if ($member) {
            if (!ToolsBis::check_password($password, $member->hashed_password)) {
                $errors[] = "Wrong password. Please try again.";
            }
        } else {
            $errors[] = "Can't find a member with the pseudo '$username'. Please sign up.";
        }
        return $errors;
    }

    public static function how_many_admin() {
        $query = self::execute("SELECT * FROM User WHERE role = :role", array("role" => "admin"));
        $result = $query->fetch(); // un seul résultat au maximum
        return count($result);
    }


    public static function del_user_by_id($id) {
        self::execute("delete FROM user where id = :id", array("id" => $id));
    }

    public static function count_admins() {
        $query = self::execute("SELECT count(*) from user where role='admin'");
        $result = $query->fetch();
        return count($result);
    }

// CETTE FONCTION MERDE COMPLET...

    public static function validate_user($id, $username, $password, $password_confirm, $fullname, $email, $birthdate) {
        $errors = [];
        $user = User::get_user_by_username($username);
        if ($user && $user->id !== $id)
            $errors[] = "This user name is already used.";
        if (empty($username)) {
            $errors[] = "User Name is required.";
        } elseif (!ToolsBis::check_string_length($username, 3, 32)) {
            $errors[] = "User Name length must be between 3 and 32 characters.";
        }
        if (empty($password)) {
            $errors[] = "Password is required.";
        }
        if (empty($fullname)) {
            $errors[] = "Full Name is required.";
        } elseif (!ToolsBis::check_string_length($fullname, 3, 255)) {
            $errors[] = "Full Name length must be between 3 and 255 characters.";
        }
        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!ToolsBis::check_string_length($email, 5, 64)) {
            $errors[] = "Email length must be between 5 and 64 characters.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email address is not valid";
        } else {
            $user = User::get_user_by_email($email);
            if ($user && ($id === null || $user->id !== $id))
                $errors[] = "This email address is already used";
        }
        if ($password != $password_confirm) {
            $errors[] = "You have to enter twice the same password.";
        }
        if (!empty($birthdate)) {
            if (!ToolsBis::is_valid_date($birthdate)) {
                $errors[] = "Birth Date is not valid";
            }
            if ($birthdate > ToolsBis::get_date('-18 years')) {
                $errors[] = "User must be at least 18 years old";
            }
        }
        return $errors;
    }

    public static function add_user($username, $password, $fullname, $email, $birthdate, $role = 'member') {
        if (empty($birthdate))
            $birthdate = null;
        $id = self::execute("INSERT INTO user(username,password, fullname, email, birthdate, role)
                 VALUES(:username,:password, :fullname, :email, :birthdate, :role)", array(
                    "username" => $username,
                    "password" => ToolsBis::my_hash($password),
                    "fullname" => $fullname,
                    "email" => $email,
                    "birthdate" => $birthdate,
                    "role" => $role
                        ), true  // pour récupérer l'id généré par la BD
        );
        return $id;
    }

    public static function add_user_from_admin($username, $password, $fullname, $email, $birthdate, $role) {
        if (empty($birthdate))
            $birthdate = null;
        $id = self::execute("INSERT INTO user(username,password, fullname, email, birthdate, role)
                 VALUES(:username,:password, :fullname, :email, :birthdate, :role)", array(
                    "username" => $username,
                    "password" => ToolsBis::my_hash($password),
                    "fullname" => $fullname,
                    "email" => $email,
                    "birthdate" => $birthdate,
                    "role" => $role
                        ), true  // pour récupérer l'id généré par la BD
        );
        return $id;
    }

    public static function update_user($id, $username, $fullname, $email, $birthdate, $role) {
        if (empty($birthdate)) {
            $birthdate = null;
        }
        self::execute("UPDATE user SET username=:username, fullname=:fullname, email=:email, 
                 birthdate=:birthdate, role=:role WHERE id=:id", array(
            "username" => $username,
            "fullname" => $fullname,
            "email" => $email,
            "birthdate" => $birthdate,
            "role" => $role,
            "id" => $id
                )
        );
    }

}
