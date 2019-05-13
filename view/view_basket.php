<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Basket</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
        <script src="lib/jquery-3.3.1.min.js" type="text/javascript"></script>
        <script src="lib/url-tools-bundle.min.js"></script>
        <script>


            var isAdmin = <?php echo json_encode($user->is_admin()); ?>;
            var books = <?= $bookToJson ?>;
            var table;
            var userSelected;




            $(function () {

                userSelected = $("#user").val();

                $('#formFilter').submit(function () {
                    return false;
                });

                table = $("#message_list");
                $("#applyFilter").hide();


                $("#clear").click(function () {
                    $("#filter").val("");
                    applyChange();
                });


                $("#filter").change(function () {
                    setChangeFilter();
                    applyChange();

                });


                displayTable();

            });

            function applyChange() {
                $.post("rental/basketFilterService", {userSelected: $("#user").val(), filter: $("#filter").val()}, function (data) {
                    books = data;
                    displayTable();
                }, "json");

            }

            function checkBookAvalaible(id, fct) {
                $.post("rental/checkBookAvalaibleService", {id: id}, function (data) {
                    if (data === "true")
                        fct();
                });

                //return JSON.parse(data);
            }



            function displayTable() {
                var html = html += "<thead>";
                html += "<tr>";
                html += "<th>ISBN</th>";
                html += "<th>Title</th>";
                html += "<th>Author</th>";
                html += "<th>Editor</th>";
                html += "<th>Nb Copies</th>";
                html += "<th>Action</th>";
                html += "</tr>";
                html += "</thead>";
                for (var b of books) {
                    html += "<tr>";
                    html += "<td>" + b.isbn + "</td>";
                    html += "<td>" + b.title + "</td>";
                    html += "<td>" + b.author + "</td>";
                    html += "<td>" + b.editor + "</td>";
                    html += "<td>" + b.copies + "</td>";


                    //edit + delete reste à ajouter le controle de visibilité si role == admin
                    html += "<td>"

                    if (isAdmin) {
                        html += "<form class='button' action='book/add_edit_book/" + b.id + "' method='get'>";
                        html += "<input type='image' value='Edit' src='logo/pen.png'>";
                        html += "</form>";

                        html += "<form class='button' action='book/delete_book/" + b.id + "' method='get'>";
                        html += "<input type='image'  src='logo/garbage.png'>";
                        html += "</form>";
                    }

                    if (!isAdmin) {
                        html += "<form class='button' action='book/add_edit_book/" + b.id + "' method='GET'>";
                        html += "<input type='image'  src='logo/eyes.png'>";
                        html += "</form>";
                    }


                    var checkRent = <?php echo json_encode($checkRent); ?>;
                    //filter = url_safe_encode({ filter: $("#filter").val() });

                    if (checkRent) {
                        html += "<form id='frm_" + b.id  + "' class='button' action='rental/add_basket/' method='POST'>";
                        html += "<input type=hidden name='bookid' value=" + b.id + ">";
                        html += "<input type=hidden name='userselected' value=" + userSelected + ">";
                        html += "<input type='hidden' name='filter' value='" + $("#filter").val() + "' >";
                        console.log($("#filter").val());
                        html += "<input id ='rent' type='image' src='logo/arrow_bottom.png'>";
                        html += "</form>";
                    }

                    html += "</td>";
                    html += "</tr>";
                }
                table.html(html);
            }

            function setChangeFilter() {
                $("#filterDelete").val($("#filter").val());
                $("#filterConfirm").val($("#filter").val());
                $("#filterClear").val($("#filter").val());

            }


        </script>
    </head>
    <body>
        <div class="title">Basket</div>
        <?php include($menu); ?>
        <div class="main">
            <div class="book_list">
                <form id="formFilter" method="post" action="" class="filter">              
                    <fieldset>
                        <legend>Filter</legend>
                        <label for="filter">Text filter</label>
                        <input type="text" name="filter" id="filter" value="<?= $filter ?>"/>
                        <input type="submit" id="applyFilter" value="Apply filter">
                        <input type="submit" id="clear" value="Clear filter" formaction="rental/clearfilter"> 
                        <?php if (!empty($filter)) { ?>
                            books that contain " <?= $filter ?> "
                        <?php } else { ?>
                            list of all books
                        <?php } ?>
                    </fieldset>
                </form>

                <table id="message_list" class="message_list">
                    <thead>
                        <tr>
                            <th>ISBN</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Editor</th>
                            <th>Avalaible Copies</th>
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
                                <td><?= $book->nbCopies - Rental::numberBookedOrRent($book->id) ?></td>
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
                                    <?php if ($checkRent && Rental::checkBookAvalaible($book->id)) : ?>
                                        <form class="button" action="rental/add_basket/" method="POST">
                                            <input type=hidden name="bookid" value="<?= $book->id ?>">
                                            <input type=hidden name="userselected" value="<?= $userselected ?>">
                                            <input type="hidden" name="filter" value="<?= $filter ?>">
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
                            <th>Avalaible Copies</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books_to_rent as $b) : ?>
                            <tr>
                                <td><?= ToolsBis::formatISBN($b->isbn) ?></td>
                                <td><?= $b->title ?></td>
                                <td><?= $b->author ?></td>
                                <td><?= $b->editor ?></td>
                                <td><?= $b->nbCopies - Rental::numberBookedOrRent($b->id) ?></td>
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
                                        <input type="hidden" id="filterDelete" name="filter" value="<?= $filter ?>">
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
                    <input type="hidden" id="filterConfirm" name="userselected" value="<?= $userselected ?>">
                    <input type="hidden" name="filter" value="<?= $filter ?>">
                    <input type="submit" value="Confirm basket">
                </form>
                <form class='button' action='Rental/clear_basket' method='POST'>
                    <input type="hidden" id="filterClear" name="filter" value="<?= $filter ?>">
                    <input type="hidden" name="userselected" value="<?= $userselected ?>">
                    <input type="submit" value="Clear Basket">
                </form>
            </div>
        </div>
    </body>
</html>