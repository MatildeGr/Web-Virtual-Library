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