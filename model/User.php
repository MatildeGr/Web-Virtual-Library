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

    public function update() {
        if (self::get_user_by_username($this->username))
            self::execute("UPDATE user set fullname = :fullname,username = :username,birthdate = :birthdate, email = :email ,role =:role where id =:id ", array("id" => $this->id, "username" => $this->username, "password" => $this->hashed_password, "fullname" => $this->fullname, "email" => $this->email,
                "birthdate" => $this->birthdate, "role" => $this->role));
        else
            self::execute("INSERT INTO user(username,password,fullname,email,birthdate,role) VALUES(:username,:password,:fullname,:email,:birthdate,:role)", array("fullname" => $this->fullname, "username" => $this->username, "password" => $this->hashed_password, "email" => $this->email,
                "birthdate" => $this->birthdate, "role" => $this->role));
        return $this;
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

    public static function get_user() {
        $query = self::execute("SELECT * FROM user", array());
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = new User($row["fullname"], $row["username"], $row["password"], $row["email"], $row["role"], $row["birthdate"], $row["id"]);
        }
        return $results;
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

    //revoie true si une émail existe
    public static function exist_email($email) {
        $query = self::execute("SELECT * FROM user where email = :email", array("email" => $email));
        $result = $query->fetchAll();
        return count($result) !== 0;
    }

    //envoie un tableau d'erreur si un email existe
    public static function validate_email($email) {
        $errors = [];
        if (User::exist_email($email)) {
            $errors[] = "the email that you have introduced already exists";
        }
        return $errors;
    }

    //renvoie un tableau d'erreur(s) 
    //le tableau est vide s'il n'y a pas d'erreur.
    //ne s'occupe que de la validation "métier" des champs obligatoires (le pseudo)
    //les autres champs (mot de passe, description et image) sont gérés par d'autres
    //méthodes.
    public function validate() {
        $errors = array();
        if (!(isset($this->username) && is_string($this->username) && strlen($this->username) > 0)) {
            $errors[] = "Pseudo is required.";
        } if (!(isset($this->username) && is_string($this->username) && strlen($this->username) >= 3 && strlen($this->username) <= 16)) {
            $errors[] = "Pseudo length must be between 3 and 16.";
        } if (!(isset($this->username) && is_string($this->username) && preg_match("/^[a-zA-Z][a-zA-Z0-9]*$/", $this->username))) {
            $errors[] = "Pseudo must start by a letter and must contain only letters and numbers.";
        }
        return $errors;
    }

    public function fullname_validate() {
        $errors = array();
        if (!(isset($this->fullname) && is_string($this->fullname) && strlen($this->fullname) > 0)) {
            $errors[] = "Fullname is required.";
        } if (!(isset($this->fullname) && is_string($this->fullname) && strlen($this->fullname) >= 3 && strlen($this->fullname) <= 16)) {
            $errors[] = "Fullname length must be between 3 and 16.";
        } if (!(isset($this->fullname) && is_string($this->fullname) && preg_match("/^[a-zA-Z][a-zA-Z]*$/", $this->fullname))) {
            $errors[] = "Fullname must contain only letters .";
        }
        return $errors;
    }

    private static function validate_password($password) {
        $errors = [];
        if (strlen($password) < 4 || strlen($password) > 16) {
            $errors[] = "Password length must be between 4 and 16.";
        }
//         if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/['\";:,.\/?\\-]/", $password))) {
//            $errors[] = "Password must contain one uppercase letter, one number and one punctuation mark.";
//        }
        return $errors;
    }

    public static function validate_passwords($password, $password_confirm) {
        $errors = User::validate_password($password);
        if ($password != $password_confirm) {
            $errors[] = "You have to enter twice the same password.";
        }
        return $errors;
    }

    public static function validate_unicity($username) {
        $errors = [];
        $member = self::get_user_by_username($username);
        if ($member) {
            $errors[] = "This user already exists.";
        }
        return $errors;
    }

    //indique si un mot de passe correspond à son hash
    private static function check_password($clear_password, $hash) {
        return $hash === Tools::my_hash($clear_password);
    }

    //renvoie un tableau d'erreur(s) 
    //le tableau est vide s'il n'y a pas d'erreur.
    public static function validate_login($username, $password) {
        $errors = [];
        $member = User::get_user_by_username($username);
        if ($member) {
            if (!self::check_password($password, $member->hashed_password)) {
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

    public static function del_user_by_name($username) {
        self::execute("delete FROM user where username = :username", array("username" => $username));
    }

    public static function del_user_by_id($id) {
        self::execute("delete FROM user where id = :id", array("id" => $id));
    }

    public static function count_admins() {
        $row = sql_fetch("SELECT count(*) from user where role='admin'");
        return (int) $row[0];
    }

    public static function get_date($str) {
        $ts = strtotime($str);
        $d = new DateTime();
        $d->setTimestamp($ts);
        return $d->format('Y-m-d');
    }

    public static function validate_user($id, $username, $password, $password_confirm, $fullname, $email, $birthdate) {
        $errors = [];
        $member = User::get_user_by_username($username);
        if ($member && $member->id !== $id)
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
            $member = User::get_user_by_email($email);
            if ($member && ($id === null || $member['id'] !== $id))
                $errors[] = "This email address is already used";
        }
        if ($password != $password_confirm) {
            $errors[] = "You have to enter twice the same password.";
        }
        if (!empty($birthdate)) {
            if (!ToolsBis::is_valid_date($birthdate)) {
                $errors[] = "Birth Date is not valid";
            }
            if ($birthdate > User::get_date('-18 years')) {
                $errors[] = "User must be at least 18 years old";
            }
        }
        return $errors;
    }

    public static function add_user_from_manager($username, $password, $fullname, $email, $birthdate, $role = 'member') {
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
