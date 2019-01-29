<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>edit book</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="title">Edit book</div>
        <?php include("menu.html"); ?>
        <div class="main">
            <form action="" method="post">
                <!- besoin du enctype='multipart/form-data' dans la signature du formulaire mais rend l'utilisation impossible -->
                <table>
                    <tr>
                        <td>ISBN(*):</td>
                        <td><input id="isbn" name="isbn" type="text" value="<?php echo $isbn ?>"></td>
                    </tr>
                    <tr>
                        <td>Title(*):</td>
                        <td><input id="title" name="title" type="text" value="<?php echo $title ?>"></td>
                    </tr>
                    <tr>
                        <td>Author(*)</td>
                        <td><input id="author" name="author" type="text" value="<?php echo $author ?>"></td>
                    </tr>
                    <tr>
                        <td>Editor(*)</td>
                        <td><input id="editor" name="editor" type="text" value="<?php echo $editor ?>"></td>
                    </tr>
                    <tr>
                        <td>Picture:</td>
                        <td><input id="picture" name="picture" type="file" accept="image/x-png, image/gif, image/jpeg"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <?php if ($picture): ?>
                        <td><img src='<?php echo $picture ?> ?>' width="150" alt="Book image"></td>
                    <?php endif; ?>
                    </tr>
                </table>
                <input type="submit" name="save" value="Save">
                <input type="submit" name="cancel" value="Cancel">
            </form>
            <?php include('insert_errors.php'); ?>


        </div>
    </body>
</html>