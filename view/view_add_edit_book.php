<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $titlePage ?> book</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="title"><?php echo $titlePage ?> book</div>
        <?php include("menu.html"); ?>
        <div class="main">
            <form action="" method="post" enctype='multipart/form-data'>
                <table>
                    <tr>
                        <td>ISBN(*):</td>
                        <td><input id="isbn" name="isbn" type="text" value="<?php echo $isbn ?>"  <?= $is_admin ? '' : 'disabled' ?>></td>
                    </tr>
                    <tr>
                        <td>Title(*):</td>
                        <td><input id="title" name="title" type="text" value="<?php echo $title ?>"<?= $is_admin ? '' : 'disabled' ?>></td>
                    </tr>
                    <tr>
                        <td>Author(*)</td>
                        <td><input id="author" name="author" type="text" value="<?php echo $author ?>"<?= $is_admin ? '' : 'disabled' ?>></td>
                    </tr>
                    <tr>
                        <td>Editor(*)</td>
                        <td><input id="editor" name="editor" type="text" value="<?php echo $editor ?>"<?= $is_admin ? '' : 'disabled' ?>></td>
                    </tr>
                    <tr>
                        <td>Picture:</td>
                        <?php if (!$view): ?>
                        <td><input id="picture" name="picture" type="file" accept="image/x-png, image/gif, image/jpeg"></td>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <td></td>
                        <?php if ($picture): ?>
                        <td><img src='<?php echo $picture ?> ?>' width="150" alt="Book image"></td>
                    <?php endif; ?>
                    </tr>
                </table>
                <?php if (!$view): ?>
                <input type="submit" name="save" value="Save">
                <?php endif; ?>
                <input type="submit" name="cancel" value="Cancel">
            </form>
            <?php include('insert_errors.php'); ?>


        </div>
    </body>
</html>