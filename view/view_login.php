<!DOCTYPE html>
<html>
    <head>
        <title>Log In</title>
        <meta charset="UTF-8">
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="title">Log In</div>
        <div class="menu">
            <a href="main/index">Home</a>
            <a href="main/signup">Sign Up</a>
        </div>
        <div class="main">
            <form action="main/login" method="post">
                <table>
                    <tr>
                        <td>User Name:</td>
                        <td><input id="username" name="username" type="text" value="<?php echo $username; ?>"></td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td><input id="password" name="password" type="password" value="<?php echo $password; ?>"></td>
                    </tr>
                </table>
                <input type="submit" value="Log In">
            </form>
            <?php
            if (isset($error))
                echo "<div class='errors'><br><br>$error</div>";
            ?>
        </div>
    </body>
</html>
