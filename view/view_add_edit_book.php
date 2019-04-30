<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $titlePage ?> book</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
        <script src="lib/jquery-3.3.1.min.js" type="text/javascript"></script>
        <script src="lib/jquery-validation-1.19.0/jquery.validate.min.js" type="text/javascript"></script>
        <script>


            $(function () {

                var isbn = $("#isbn");

                isbn.on("keyup", function (event) {
                    var selection = window.getSelection().toString();
                    if (selection !== '') {
                        return;
                    }
                    if ($.inArray(event.keyCode, [38, 40, 37, 39]) !== -1) {
                        return;
                    }

                    var $this = $(this);
                    var input = $this.val();
                    input = input.replace(/[\W\s\._\-]+/g, '');

                    var split = 3;
                    var chunk = [];

                    for (var i = 0, len = input.length; i < len; i += split) {
                        split = (i >= 4 && i <= 11) ? 4 : (i === 3) ? 1 : 3;
                        chunk.push(input.substr(i, split));
                    }

                    $this.val(function () {
                        return chunk.join("-").toUpperCase();
                    });

                    $("#checkdigit").val(checkDigit(input));

                });


                $('#form').validate({
                    rules: {
                        isbn: {
                             remote: {
                                url: 'book/isbn_available_service',
                                type: 'post',
                                data: {
                                    username: function () {
                                        return $("#isbn").val();
                                             
                                    },
                                    id: function(){
                                         return $("#id").val();
                                     }   

                                }
                            },
                            required: true,
                            minlength: 15,
                            maxlength: 15
                        },
                        title: {
                            required: true
                        },
                        author: {
                            required: true
                        },
                        editor: {
                            required: true
                        },
                        copies: {
                            required: true,
                            min: 1
                        }
                    },
                    messages: {
                        isbn: {
                            required: 'required',
                            minlength: 'minimum 12 characters',
                            maxlength: 'maximum 12 characters'
                        },
                        title: {
                            required: 'required'
                        },
                        author: {
                            required: 'required'
                        },
                        editor: {
                            required: 'required'
                        },
                        copies: {
                            required: 'required',
                            min: 'the number of copies must be greater than or equal to 1'
                        }

                    }
                });
                $("input:text:first").focus();


            });

            function checkDigit(data) {

                var isbn = data.replace(/[($)\s\._\-]+/g, '');
                if (isbn.length === 12) {
                    var check = 0;
                    for (var i = 0; i < 12; i += 2) {
                        check += isbn.substr(i, 1);
                    }

                    for (var i = 1; i < 12; i += 2) {
                        check += 3 * isbn.substr(i, 1);
                    }

                    check = 10 - check % 10;
                    if (check === 10) {
                        check = 0;
                    }

                    return check;
                }

            }

        </script>



    </head>
    <body>
        <div class="title"><?php echo $titlePage ?> book</div>
        <?php include("menu.html"); ?>
        <div class="main">
            <form id="form" action="" method="post" enctype='multipart/form-data'>
                <table>
                    <tr>
                        <td><input id="id" name="title" type="text" value="<?php echo $title ?>" hidden></td>
                    </tr>
                    <tr>
                        <td>ISBN(*):</td>
                        <td><input id="isbn" name="isbn" type="text"  value="<?php echo ToolsBis::formatISBN12($isbn) ?>"  <?= $is_admin ? '' : 'disabled' ?> maxlength="15"> 
                            - <input id="checkdigit" name="checkdigit" type="text"  value="<?php echo ToolsBis::makeCheckDigit($isbn) ?>"size="1" disabled>
                            (first 12 characters)
                        </td>
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
                    <tr>
                        <td>Copies: </td>
                        <td><input id="copies" name="copies" type="number" value="<?php echo $nbCopies ?>"<?= $is_admin ? '' : 'disabled' ?> > </td>
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