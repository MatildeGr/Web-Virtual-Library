<!DOCTYPE html>
<html>
    <head>
        <title>Users</title>
        <base href="<?= $web_root ?>"/>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="title">Users</div>
        <?php include('menu.html'); ?>
        <div class="main">
            <table class="message_list">
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Birth Date</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $usr) : ?>
                        <tr>
                            <td><?= $usr->username ?></td>
                            <td><?= $usr->fullname ?></td>
                            <td><?= $usr->email ?></td>
                            <td><?= ToolsBis::format_date($usr->birthdate) ?></td>
                            <td><?= $usr->role ?></td>
                            <td>
                                <form class="button" action="user/add_edit_user" method="GET">
                                    <input type="hidden" name="id" value="<?= $usr->id ?>">
                                    <input type="submit" value="Edit">
                                </form>
                                <?php if ($user->is_admin() && $user->id !== $usr->id): ?>
                                    <form class="button" action="delete_user.php" method="GET">
                                        <input type="hidden" name="id" value="<?= $usr->id ?>">
                                        <input type="submit" value="Delete">
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <br>
            <form class="button" action="user/add_edit_user" method="GET">
                <input type="submit" value="New User">
            </form>
        </div>
    </body>
</html>