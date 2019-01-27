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
        <?php include($menu);?>
        <div class="main">
            <p>These are your currently rented books. Don't forget to return them in time!</p>
             <table class="message_list">
                <thead>
                    <tr>
                        <th>Rental Date/Time</th>
                        <th>Book</th>
                        <th>To be returned on</th>
                    </tr>
                </thead>
                <tbody>
<!--                     <?php foreach ($books as $book) : ?>
                        <tr>
                            <td><?= $book->retaldate ?></td>
                            <td><?= $book->book ?></td>
                            <td><?= $book->returndate ?></td>
                        </tr>
                    <?php endforeach; ?>
-->
                </tbody>
             </table>
            <br><br>
        </div>
    </body>
</html>
