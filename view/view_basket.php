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
                        <!--                     <?php foreach ($books as $book) : ?>
                                                                    <tr>
                                                                        <td><?= $book->isnb ?></td>
                                                                        <td><?= $book->title ?></td>
                                                                        <td><?= $book->author ?></td>
                                                                        <td><?= $book->editor ?></td>
                                                                    </tr>
                        <?php endforeach; ?>
                        -->
                    </tbody>
                </table>
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
                        <!--                     <?php foreach ($books as $book) : ?>
                                                                        <tr>
                                                                            <td><?= $book->isnb ?></td>
                                                                            <td><?= $book->title ?></td>
                                                                            <td><?= $book->author ?></td>
                                                                            <td><?= $book->editor ?></td>
                                                                            <td></td>
                                                                        </tr>
                        <?php endforeach; ?>
                        -->
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