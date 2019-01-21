<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?= $username->username?>'s Profile!</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="title"><?= $username->username ?>'s Profile!</div>
        <?php include('menu.html');?>
        <div class="main">
            <?php if (strlen($username->username) == 0): ?>
                No profile string entered yet!
            <?php else: ?>
                <?= $username->username; ?>
            <?php endif; ?>
            <br><br>
        </div>
    </body>
</html>
