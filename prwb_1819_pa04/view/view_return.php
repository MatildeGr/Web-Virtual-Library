<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Return book</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="title">Return book</div>
        <?php include("menu.html"); ?>
        <div class="main">
            <div class="book_list">
                <form method="POST" action="rental/returnBook" class="filter">              
                    <fieldset>
                        <legend>Filter</legend>
                        <table>
                            <tr>
                                <td>Member:</td>
                                <td><input type="text" name="member" id="member"/></td>
                            </tr>
                            <tr>
                                <td>Book:</td>
                                <td><input type="text" name="book" id="book"/></td>
                            </tr>
                            <tr>
                                <td>Rental date:</td>
                                <td><input type="date" name="date" id="date"/></td>
                            </tr>
                            <tr>
                                <td>State:</td>
                                <td>
                                    <input type="radio" name="state" value="open" id="open" /><label for="Open">Open</label>
                                    <input type="radio" name="state" value="returned" id="returned" /><label for="Returned">Returned</label>
                                    <input type="radio" name="state" value="all" id="all" /><label for="all">All</label>
                                </td>
                            </tr>
                        </table>
                        <input type="submit" value="Apply filter">
                    </fieldset>
                </form>


                <table class="message_list">
                    <thead>
                        <tr>
                            <th>Rental Date/Time</th>
                            <th>Member</th>
                            <th>Book</th>
                            <th>Return Date/Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rentals as $rent) : ?>
                            <tr>
                                <td><?= ToolsBis::format_datetime($rent->rentaldate) ?></td>
                                <td><?= $rent->user ?></td>
                                <td><?= $rent->book ?></td>
                                <td><?= ToolsBis::format_datetime($rent->returndate) ?></td>
                                <td>  <?php if ($isAdmin) : ?>
                                        <form class="button" action="rental/deleteRental/<?php echo $rent->id; ?>" method="GET">
                                            <input type="image" value="delete" src='logo/garbage.png'>
                                        </form>
                                    <?php endif; ?>
                                    <?php if (!$rent->returndate) : ?>
                                    <form class="button" action="rental/confirmReturn/<?php echo $rent->id; ?>" method="GET">
                                        <input type="image" value="return" src='logo/editRent.png'>                                     
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?> 

                    </tbody>
                </table>
            </div>


        </div>
    </body>
</html>