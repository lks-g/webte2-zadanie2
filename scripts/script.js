$(document).ready(function () {
    $("#download").click(function () {
        $.ajax({
            url: "./rest-be/download_menu.php",
            method: "POST",
            success: function (response) {
                alert('Data downloaded successfully!');
            },
            error: function (xhr, status, error) {
                console.log(error);
            }
        });
    });

    $('#parse').click(function () {
        getDishes();
    });

    $('#delete').click(function () {
        $.ajax({
            type: 'POST',
            url: './rest-be/delete_tables.php',
            success: function (response) {
                alert('Tables deleted successfully!');
            },
            error: function () {
                alert('Error deleting tables');
            }
        });
    });

    function getDishes() {
        $.getJSON('./php/eatnmeet.php', function (data) {
            var tbody = $('#menu-rows');
            tbody.empty();

            var rows = '';
            for (var i = 0; i < data.length; i++) {
                var date = data[i].date;
                var day = data[i].day;
                var menu = data[i].menu;

                for (var j = 0; j < menu.length; j++) {
                    var meal = menu[j];

                    var parts = meal.split(':');
                    var name = parts[0];
                    var prices = parts[1];

                    rows += '<tr><td>' + date + '</td><td>' + day + '</td><td>' + name + '</td><td>' + prices + '</td></tr>';
                }
            }

            tbody.html(rows);
        });
    }

});