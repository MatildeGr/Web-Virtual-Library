<?php

require_once "framework/Model.php";

class User extends Model {

    public $username;
    public $hashed_password;
    public $fullname;
    public $email;
    public $birthdate;
    public $role;

    public function __construct($username, $hashed_password, $fullname, $email, $role = 'member', $birthdate = null) {
        $this->username = $username;
        $this->hashed_password = $hashed_password;
        $this->fullname = $fullname;
        $this->email = $email;
        $this->birthdate = $birthdate;
        $this->role = $role;
    }

    public function validate_login($username, $password) {
        $errors = [];
        $username = User::get_user_by_username($username);
        if ($username) {
            if (!self::check_password($password, $username->hashed_password)) {
                $errors[] = "Wrong password. Please try again.";
            }
        } else {
            $errors[] = "Can't find a member with the pseudo '$username'. Please sign up.";
        }
        return $errors;
    }

    public function get_user_by_username($username) {
        $query = self::execute("SELECT * FROM User where username = :username", array("username" => $username));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Member($data["username"], $data["password"], $data["fullname"], $data["email"], $data["role"], $data["birthdate"]);
        }
    }

    public function update() {
        if (self::get_user_by_username($this->username))
            self::execute("UPDATE User SET username=:username,password=:password,fullname=:fullname,"
                    . "email=:email,birthdate=:birthdate,role=:role"
                    . " WHERE username=:username ", array("username" => $this->username, "fullname" => $this->fullname,
                "email" => $this->email, "birthdate" => $this->birthdate, "role" => $this->role,
                "password" => $this->hashed_password));
        else
            self::execute("INSERT INTO Members(username,password,fullname,email,birthdate,role) VALUES(:username,:password,:fullname,:email,:birthdate,:role)", array("username" => $this->username, "fullname" => $this->fullname,
                "email" => $this->email, "birthdate" => $this->birthdate, "role" => $this->role,
                "password" => $this->hashed_password));
        return $this;
    }

    function validate_user($id, $username, $password, $password_confirm, $fullname, $email, $birthdate) {
        $errors = [];
        $member = get_user_by_name($username);
        if ($member && $member['id'] !== $id)
            $errors[] = "This user name is already used.";
        if (empty($username)) {
            $errors[] = "User Name is required.";
        } elseif (!check_string_length($username, 3, 32)) {
            $errors[] = "User Name length must be between 3 and 32 characters.";
        }
        if (empty($password)) {
            $errors[] = "Password is required.";
        }
        if (empty($fullname)) {
            $errors[] = "Full Name is required.";
        } elseif (!check_string_length($fullname, 3, 255)) {
            $errors[] = "Full Name length must be between 3 and 255 characters.";
        }
        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!check_string_length($email, 5, 64)) {
            $errors[] = "Email length must be between 5 and 64 characters.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email address is not valid";
        } else {
            $member = get_user_by_email($email);
            if ($member && ($id === null || $member['id'] !== $id))
                $errors[] = "This email address is already used";
        }
        if ($password != $password_confirm) {
            $errors[] = "You have to enter twice the same password.";
        }
        if (!empty($birthdate)) {
            if (!is_valid_date($birthdate)) {
                $errors[] = "Birth Date is not valid";
            }
            if ($birthdate > get_date('-18 years')) {
                $errors[] = "User must be at least 18 years old";
            }
        }
        return $errors;
    }

}
