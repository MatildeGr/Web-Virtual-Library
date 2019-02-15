<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Basket</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="title">Basket</div>
        <?php include($menu); ?>
        <div class="main">
            <div class="book_list">
                <form method="post" action="" class="filter">              
                    <fieldset>
                        <legend>Filter</legend>
                        <label for="filter">Text filter</label>
                        <input type="search" name="filter" id="filter"/>
                        <input type="submit" value="Apply filter">
                    </fieldset>
                </form>
                <table class="message_list">
                    <thead>
                        <tr>
                            <th>ISBN</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Editor</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book) : ?>
                            <tr>
                                <td><?= ToolsBis::formatISBN($book->isbn) ?></td>
                                <td><?= $book->title ?></td>
                                <td><?= $book->author ?></td>
                                <td><?= $book->editor ?></td>
                                <td>  <?php if ($user->is_admin()) : ?>
                                        <form class="button" action="book/add_edit_book/<?= $book->id ?>" method="get">                                            
                                            <input type="image" value="Edit" src='logo/pen.png'>
                                        </form>
                                        <form class="button" action="book/delete_book/<?= $book->id ?>" method="get">                                           
                                            <input type="image" value="" src='logo/garbage.png'>
                                        </form>
                                    <?php endif; ?>
                                    <?php if (!$user->is_admin()) : ?>
                                        <form class="button" action="book/add_edit_book/<?php echo $book->id; ?>" method="GET">                                           
                                            <input type="image"  src='logo/eyes.png'>
                                        </form>
                                    <?php endif; ?>
                                    <?php if($checkRent) : ?>
                                    <form class="button" action="rental/add_basket/" method="POST">
                                        <input type=hidden name="bookid" value="<?= $book->id ?>">
                                        <input type=hidden name="userselected" value="<?= $userselected ?>">
                                        <input id ="rent" type="image" value="" src='logo/arrow_bottom.png'>
                                    </form></td>
                                    <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
            <div class='main'>
                <?php if ($user->is_admin()) : ?>
                    <form class="button" action="book/add_edit_book/" method="GET">
                        <input type="submit" value="Add book">
                    </form>
                <?php endif; ?>
            </div>
            <div class="book_rent">
                <p>Basket of books to rent</p>
                <table class="message_list">
                    <thead>
                        <tr>
                            <th>ISBN</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Editor</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books_to_rent as $b) : ?>
                            <tr>
                                <td><?= ToolsBis::formatISBN($b->isbn)  ?></td>
                                <td><?= $b->title ?></td>
                                <td><?= $b->author ?></td>
                                <td><?= $b->editor ?></td>
                                <td> 
                                    <?php if ($user->is_admin()) : ?>
                                        <form class="button" action="book/add_edit_book/<?= $b->id ?>" method="get">
                                            <input type=hidden name="userselected" value="<?= $userselected ?>">
                                            <input type="image" value="Edit" src='logo/pen.png'>
                                        </form>
                                        <form class="button" action="book/delete_book/<?php echo $b->id; ?>" method="get">
                                            <input type="image" value="" src='logo/garbage.png'>
                                        </form>
                                    <?php endif; ?>
                                    <?php if (!$user->is_admin()) : ?>
                                        <form class="button" action="book/add_edit_book/<?php echo $b->id; ?>" method="GET">
                                            <input type=hidden name="userselected" value="<?= $userselected ?>">
                                            <input type="image"  src='logo/eyes.png'>
                                        </form>
                                    <?php endif; ?>
                                    <form class="button" action="rental/delete_basket/" method="POST">
                                        <input type=hidden name="userselected" value="<?= $userselected ?>">
                                        <input type="hidden" name='bookid' value="<?= $b->id ?>" >
                                        <input id="backrent" type="image" value="" src='logo/arrow_top.png'>
                                    </form></td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
                <?php if (!$user->is_member()) : ?>
                    <form method="POST" action="rental/basket/">              
                        <label for="user">This basket is for </label>
                        <select name="userselected" id="user">
                            <?php foreach ($users as $u) : ?>
                                <option value="<?= $u->id ?>"<?php if ($userselected == $u->id): ?> selected<?php endif ?>> <?= $u->username ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="submit" value="Save user selected">
                    </form>
                <?php endif; ?>
                <form class="button" action="Rental/confirm_basket" method="POST">
                    <input type="hidden" name="userselected" value="<?= $userselected ?>">
                    <input type="submit" value="Confirm basket">
                </form>
                <form class='button' action='Rental/clear_basket' method='POST'>
                    <input type="hidden" name="userselected" value="<?= $userselected ?>">
                    <input type="submit" value="Clear Basket">
                </form>
            </div>
        </div>
    </body>
</html>