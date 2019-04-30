<!DOCTYPE html>
<html>
    <head>
        <title><?= $is_new ? "Add" : "Edit" ?> User</title>
        <base href="<?= $web_root ?>"/>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
        <script src="lib/jquery-3.3.1.min.js" type="text/javascript"></script>
        <script src="lib/jquery-validation-1.19.0/jquery.validate.min.js" type="text/javascript"></script>
        <script>

            $(function () {
                $.validator.addMethod("validateDate", function (value, element) {

                    return isNaN(Date.parse(value)); //true si fausse date et false si vraie date

                }, "Please enter a valid date.");


                $.validator.addMethod("validateAge", function (value, element) {

                    var optimizedBirthday = value.replace(/-/g, "/");
                    var myBirthday = new Date(optimizedBirthday);
                    var currentDate = new Date().toJSON().slice(0, 10) + '01:00:00';
                    var age = ~~((Date.now(currentDate) - myBirthday) / (31557600000));
                    return age > 18;
                }, "Please be older.");


                $('#signupForm').validate({
                    rules: {
                        username: {
                             remote: {
                                url: 'user/user_available_service',
                                type: 'post',
                                data: {
                                    username: function () {
                                        return $("#username").val();
                                             
                                    },
                                    id: function(){
                                         return $("#id").val();
                                     }   

                                }
                            },
                            required: true,
                            minlength: 3,
                            maxlength: 32
                        },
                        fullname: {
                            required: true,
                            minlength: 3,
                            maxlength: 255
                        },
                        email: {
                            remote: {
                                url: 'user/email_available_service',
                                type: 'post',
                                data: {
                                    email: function () {
                                        return $("#email").val();
                                    },
                                    id: function(){
                                         return $("#id").val();
                                     } 
                                }
                            },
                            required: true,
                            minlength: 5,
                            maxlength: 64
                        },
                        birthdate: {
                            validateDate: false,
                            validateAge: true
                        }
                    },
                    messages: {
                        username: {
                            remote: 'this pseudo is already taken',
                            required: 'required',
                            minlength: 'minimum 3 characters',
                            maxlength: 'maximum 32 characters'
                        },
                        fullname: {
                            required: 'required',
                            minlength: 'minimum 3 characters',
                            maxlength: 'maximum 255 characters'
                        },
                        email: {
                            remote: 'this email is already taken',
                            minlength: 'minimum 5 characters',
                            maxlength: 'maximum 64 characters'
                        }

                    }
                });
                $("input:text:first").focus();
            });
        </script>
    </head>
    <body>
        <div class="title"><?= $is_new ? "Add" : "Edit" ?> User</div>
        <?php include("menu.html"); ?>
        <div class="main">
            Please enter the user details :
            <br><br>
            <form id="signupForm" action="" method="post">
                <table>
                    <tr>
                        <td><input id="id" name="id" type="text" value="<?php echo $id; ?>" hidden></td>
                    </tr>
                    <tr>
                        <td>User Name:</td>
                        <td><input id="username" name="username" type="text" value="<?php echo $username; ?>"></td>
                    </tr>
                    <tr>
                        <td>Full Name:</td>
                        <td><input id="fullname" name="fullname" type="text" value="<?php echo $fullname; ?>"></td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td><input id="email" name="email" type="email" value="<?php echo $email; ?>"></td>
                    </tr>
                    <tr>
                        <td>Birth Date:</td>
                        <td><input id="birthdate" name="birthdate" type="date" value="<?php echo $birthdate; ?>"></td>
                    </tr>
                    <tr>
                        <td>Role:</td>
                        <td>
                            <select id="role" name="role" <?= $is_admin ? '' : 'disabled' ?>>
                                <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>admin</option>
                                <option value="manager" <?= $role === 'manager' ? 'selected' : '' ?>>manager</option>
                                <option value="member" <?= $role === 'member' ? 'selected' : '' ?>>member</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <input type="submit" name="save" value="Save">
                <input type="submit" name="cancel" value="Cancel">
            </form>
            <?php include('insert_errors.php'); ?>
        </div>
    </body>
</html>