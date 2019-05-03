<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Return book</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
        <script src="lib/jquery-3.3.1.min.js" type="text/javascript"></script>
        <link href='fullcalendar-scheduler-4.1.0/packages/core/main.css' rel='stylesheet' />
        <link href='fullcalendar-scheduler-4.1.0/packages/daygrid/main.css' rel='stylesheet' />
        <link href='fullcalendar-scheduler-4.1.0/packages/timegrid/main.css' rel='stylesheet' />
        <link href='fullcalendar-scheduler-4.1.0/packages/list/main.css' rel='stylesheet' />
        <link href='fullcalendar-scheduler-4.1.0/packages/timeline/main.css' rel='stylesheet' />
        <link href='fullcalendar-scheduler-4.1.0/packages/resource-timeline/main.css' rel='stylesheet' />
        <script src='fullcalendar-scheduler-4.1.0/packages/core/main.js'></script>
        <script src='fullcalendar-scheduler-4.1.0/packages/interaction/main.js'></script>
        <script src='fullcalendar-scheduler-4.1.0/packages/daygrid/main.js'></script>
        <script src='fullcalendar-scheduler-4.1.0/packages/timegrid/main.js'></script>
        <script src='fullcalendar-scheduler-4.1.0/packages/list/main.js'></script>
        <script src='fullcalendar-scheduler-4.1.0/packages/timeline/main.js'></script>
        <script src='fullcalendar-scheduler-4.1.0/packages/resource-common/main.js'></script>
        <script src='fullcalendar-scheduler-4.1.0/packages/resource-timeline/main.js'></script>
        <script>


            var rental = <?= $rentals_json ?>;
            $(function () {


                //cache le bouton apply filter
                $("#submit").hide();

                table = $("#rentals_list");

                //cache la liste des rentals php
                $("#rentals_list").hide();


                //filtre dynamique
                $("input").change(function () {
//                    console.log("filter action");
//                    $.post("rental/filterService", {member: $("#member").val(), book: $("#book").val(), date: $("#date").val(), state: $("#state").val()}, function (data) {
//                        rental = data;
//                        console.log("refresh calendar");
//                        //displayTable();
//                    }, "json");

                    calendar.refetchEvents();
                    calendar.refetchResources();
                });




                //fullcalendar timeline
                var calendarEl = document.getElementById('table');

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    plugins: ['interaction', 'resourceTimeline'],
                    timeZone: 'UTC',
                    defaultView: 'resourceTimelineDay',
                    aspectRatio: 1.5,
                    header: {
                        left: 'today,prev,next',
                        center: 'title',
                        right: 'resourceTimelineWeek,resourceTimelineMonth,resourceTimelineYear'
                    },

                    resourceColumns: [
                        {
                            labelText: 'User',
                            field: 'user'
                        },
                        {
                            labelText: 'Book',
                            field: 'book'
                        }
                    ],

                    editable: false,

                    resources: {
                        url: 'rental/filterServiceResources',
                        method: 'POST',
                        extraParams: function () { // a function that returns an object
                            return {
                                member: $("#member").val(),
                                book: $("#book").val(),
                                date: $("#date").val(),
                                state: $("#state").val()
                            };
                        }
                    },

                    events: {
                        url: 'rental/filterServiceEvents',
                        method: 'POST',
                        extraParams: function () { // a function that returns an object
                            return {
                                member: $("#member").val(),
                                book: $("#book").val(),
                                date: $("#date").val(),
                                state: $("#state").val()
                            };
                        }
                    }




                });
                $("#table").html(calendar.render());


            });



        </script>
    </head>
    <body>
        <div class="title">Return book</div>
        <?php include("menu.html"); ?>
        <div class="main">
            <div class="book_list">
                <form id="filter" method="POST" action="rental/returnBook" class="filter">              
                    <fieldset>
                        <legend>Filter</legend>
                        <table>
                            <tr>
                                <td>Member:</td>
                                <td><input type="text" name="member" id="member" value="<?= $filterUser ?>"/></td>
                            </tr>
                            <tr>
                                <td>Book:</td>
                                <td><input type="text" name="book" id="book" value="<?= $filterBook ?>" /></td>
                            </tr>
                            <tr>
                                <td>Rental date:</td>
                                <td><input type="date" name="date" id="date" value="<?= $filterRentalDate ?>"/></td>
                            </tr>
                            <tr>
                                <td>State:</td>
                                <td>
                                    <input type="radio" name="state" value="open" id="open" <?= $filterState == "open" ? "checked = 'checked'" : "" ?>/><label for="Open">Open</label>
                                    <input type="radio" name="state" value="returned" id="returned"  <?= $filterState == "returned" ? "checked = 'checked'" : "" ?>/><label for="Returned">Returned</label>
                                    <input type="radio" name="state" value="all" id="all" <?= $filterState == "all" ? "checked = 'checked'" : "" ?>/><label for="all">All</label>
                                </td>
                            </tr>
                        </table>
                        <input id="submit" type="submit" value="Apply filter">
                    </fieldset>
                </form>

                <div id="table">
                    <table id="rentals_list" class="message_list">
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
                                            <form class="button" action="rental/returnBook" method="POST">
                                                <input type="hidden" name="delete" value="<?php echo $rent->id; ?>">
                                                <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                                                <input type="image" src='logo/garbage.png'>
                                            </form>
                                        <?php endif; ?>
                                        <?php if (!$rent->returndate) : ?>
                                            <form class="button" action="rental/returnBook" method="POST">
                                                <input type="hidden" name="return" value="<?php echo $rent->id; ?>">
                                                <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                                                <input type="image"  src='logo/editRent.png'>                                     
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?> 

                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </body>
</html>