<!DOCTYPE html>
<html>
    <head>
        <title>Sign Up</title>
        <meta charset="UTF-8">
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="title">Sign Up</div>
        <div class="menu">
            <a href="main/index">Home</a>
        </div>
        <div class="main">
            Please enter your details to sign up :
            <br><br>
            <form action="main/signup" method="post">
                <table>
                    <tr>
                        <td>User Name:</td>
                        <td><input id="username" name="username" type="text" value="<?php echo $username; ?>"></td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td><input id="password" name="password" type="password" value="<?php echo $password; ?>"></td>
                    </tr>
                    <tr>
                        <td>Confirm Password:</td>
                        <td><input id="password_confirm" name="password_confirm" type="password" value="<?php echo $password_confirm; ?>"></td>
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
                </table>
                <input type="submit" value="Sign Up">
            </form>
                <?php include('insert_errors.php'); ?>
        </div>
    </body>
</html>