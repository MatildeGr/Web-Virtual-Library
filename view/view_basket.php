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
        <?php include("menu.html"); ?>
        <div class="main">
            <div class="book_list">
                <form methode="post" action="" class="filter">              
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
                                <td><?= $book->isbn ?></td>
                                <td><?= $book->title ?></td>
                                <td><?= $book->author ?></td>
                                <td><?= $book->editor ?></td>
                                <td>  <?php if ($user->is_admin()) : ?>
                                        <form class="button" action="book/add_edit_book/<?php echo $book->id; ?>" method="GET">
                                            <input type="image" value="Edit" src='logo/pen.png'>
                                        </form>
                                        <form class="button" action="book/delete_book/<?php echo $book->id; ?>" method="GET">
                                            <input type="image" value="Edit" src='logo/garbage.png'>
                                        </form>
                                    <?php endif; ?>
                                    <?php if (!$user->is_admin()) : ?>
                                        <form class="button" action="book/add_edit_book/<?php echo $book->id; ?>" method="GET">
                                            <input type="image"  src='logo/eyes.png'>
                                        </form>
                                    <?php endif; ?>
                                    <form class="button" action="book/add_basket/" method="POST">
                                        <input type=hidden name="bookid" value="<?= $book->id ?>">
                                        <input id ="rent" type="image" value="" src='logo/arrow_bottom.png'>
                                    </form></td>
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
                                <td><?= $b->isbn ?></td>
                                <td><?= $b->title ?></td>
                                <td><?= $b->author ?></td>
                                <td><?= $b->editor ?></td>
                                <td> 
                                    <?php if ($user->is_admin()) : ?>
                                        <form class="button" action="book/add_edit_book/<?php echo $book->id; ?>" method="GET">
                                            <input type="image" value="Edit" src='logo/pen.png'>
                                        </form>
                                        <form class="button" action="book/delete_book/<?php echo $book->id; ?>" method="GET">
                                            <input type="image" value="Edit" src='logo/garbage.png'>
                                        </form>
                                    <?php endif; ?>
                                    <?php if (!$user->is_admin()) : ?>
                                        <form class="button" action="book/add_edit_book/<?php echo $book->id; ?>" method="GET">
                                            <input type="image"  src='logo/eyes.png'>
                                        </form>
                                    <?php endif; ?>
                                    <form class="button" action="book/delete_basket/" method="POST">
                                        <input type="hidden" name='bookid' value="<?= $b->id ?>" >
                                        <input id="backrent" type="image" value="" src='logo/arrow_top.png'>
                                    </form></td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
                <form methode="post" action="">              
                    <label for="user">This basket is for </label>
                    <select name="user" id="user">

                        <!--                    <?php foreach ($users as $user) : ?>
                                                                                                                        <option value="<?= $username->id ?>"><?= $username->username ?></option>
                        <?php endforeach; ?>
                        -->                   
                    </select>
                    <input type="submit" value="Confirm basket">
                    <input type="submit" value="Clear Basket">
                </form>
            </div>

        </div>
    </body>
</html>