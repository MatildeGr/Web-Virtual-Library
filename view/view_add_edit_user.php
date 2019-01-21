<!DOCTYPE html>
<html>
    <head>
        <title><?= $is_new ? "Add" : "Edit" ?> User</title>
        <base href="<?= $web_root ?>"/>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="title"><?= $is_new ? "Add" : "Edit" ?> User</div>
        <?php include("menu.html"); ?>
        <div class="main">
            Please enter the user details :
            <br><br>
            <form action="" method="post">
                <table>
                    <tr>
                        <td>User Name:</td>
                        <td><input id="username" name="username" type="text" value="<?php echo $username; ?>"></td>
                    </tr>
                    <?php if ($is_new): ?>
                        <tr>
                            <td>Password:</td>
                            <td><input id="password" name="password" type="password" value="<?php echo $password; ?>"></td>
                        </tr>
                        <tr>
                            <td>Confirm Password:</td>
                            <td><input id="password_confirm" name="password_confirm" type="password" value=""></td>
                        </tr>
                    <?php endif ?>
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
<!--                    <tr>
                        <td>Role:</td>
                        <td>
                            <select id="role" name="role" <?= isAdmin() ? '' : 'disabled' ?>>
                                <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>admin</option>
                                <option value="manager" <?= $role === 'manager' ? 'selected' : '' ?>>manager</option>
                                <option value="member" <?= $role === 'member' ? 'selected' : '' ?>>member</option>
                            </select>
                        </td>
                    </tr>-->
                </table>
                <input type="submit" name="save" value="Save">
                <input type="submit" name="cancel" value="Cancel">
            </form>
            <?php
            if (isset($errors) && count($errors) > 0) {
                echo "<div class='errors'>
                          <p>Please correct the following error(s) :</p>
                          <ul>";
                foreach ($errors as $error) {
                    echo "<li>" . $error . "</li>";
                }
                echo '</ul></div>';
            }
            ?>
        </div>
    </body>
</html>