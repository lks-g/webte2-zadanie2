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
        if(getAllDishes()) {
            alert('Data parsed successfully!');
        } else {
            alert('Error parsing data!');
        }
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

    $('#btn-pon, #btn-uto, #btn-str, #btn-stv, #btn-pia, #btn-sob, #btn-ned').on('click', function () {
        var day = $(this).text();
        getDishes(day);
    });

    $('#btn-all').on('click', function () {
        getAllDishes();
    });
});

function getDishes(day) {
    $.getJSON('./php/eatnmeet.php', function (data) {

        var tbody = $('#eat-rows');
        tbody.empty();

        var rows = '';
        for (var i = 0; i < data.length; i++) {
            if (data[i].day === day) {
                var date = data[i].date;
                var menu = data[i].menu;

                for (var j = 0; j < menu.length; j++) {
                    var meal = menu[j];
                    var parts = meal.split(':');
                    var name = parts[0];
                    var price = parts[1];

                    rows += '<tr><td>' + date + '</td><td>' + name + '</td><td>' + price + '</td></tr>';
                }
            }
        }
        tbody.html(rows);
    });

    $.getJSON('./php/freefood.php', function (data) {

        var tbody = $('#free-rows');
        tbody.empty();

        var rows = '';
        for (var i = 0; i < data.length; i++) {

            if (data[i].day === "SOBOTA" || data[i].day === "NEDEĽA") {
                continue;
            }

            var date = data[i].date;
            var d = data[i].day;
            var menu = data[i].menu;

            if (d === day) {
                for (var j = 0; j < menu.length; j++) {
                    var meal = menu[j];
                    var meal_components = meal.split(' ');
                    var name = meal_components.slice(0, -1).join(' ');
                    var price = meal_components.slice(-1)[0];

                    if (name === 'Zatvorené:') {
                        price = '---';
                    }
                    rows += '<tr><td>' + date + '</td><td>' + name + '</td><td>' + price + '</td></tr>';
                }
            }
        }
        tbody.html(rows);
    });

    $.getJSON('./php/delikanti.php', function (data) {
        var tbody = $('#deli-rows');
        tbody.empty();

        var rows = '';
        for (var i = 0; i < data.length; i++) {

            if (data[i].day === "SOBOTA" || data[i].day === "NEDEĽA") {
                continue;
            }

            var date = data[i].date;
            var menu = data[i].menu;
            var d = data[i].day;

            if (d === day) {
                if (menu[i] === "ŠTÁTNY SVIATOK") {
                    rows = '<tr><td>' + date + '</td><td>' + menu[i] + '</td><td>---</td></tr>'
                    break;
                }
                for (var j = 0; j < menu.length; j++) {
                    var meal = menu[j];
                    var parts = meal.split(' ');
                    var name = parts.slice(0, -1).join(' ');
                    var price = '---';

                    rows += '<tr><td>' + date + '</td><td>' + name + '</td><td>' + price + '</td></tr>';
                }
            }
        }
        tbody.html(rows);
    });
}

function getAllDishes() {
    $.getJSON('./php/eatnmeet.php', function (data) {
        var tbody = $('#eat-rows');
        tbody.empty();

        var rows = '';
        for (var i = 0; i < data.length; i++) {

            var date = data[i].date;
            var menu = data[i].menu;

            for (var j = 0; j < menu.length; j++) {
                var meal = menu[j];
                var parts = meal.split(':');
                var name = parts[0];
                var price = parts[1];

                rows += '<tr><td>' + date + '</td><td>' + name + '</td><td>' + price + '</td></tr>';
            }
        }
        tbody.html(rows);
    });

    $.getJSON('./php/freefood.php', function (data) {

        var tbody = $('#free-rows');
        tbody.empty();

        var rows = '';
        for (var i = 0; i < data.length; i++) {

            if (data[i].day === "SOBOTA" || data[i].day === "NEDEĽA") {
                continue;
            }

            var date = data[i].date;
            var menu = data[i].menu;

            for (var j = 0; j < menu.length; j++) {
                var meal = menu[j];
                var meal_components = meal.split(' ');
                var name = meal_components.slice(0, -1).join(' ');
                var price = meal_components.slice(-1)[0];

                if (name === 'Zatvorené:') {
                    price = '---';
                }
                rows += '<tr><td>' + date + '</td><td>' + name + '</td><td>' + price + '</td></tr>';
            }
        }
        tbody.html(rows);
    });

    $.getJSON('./php/delikanti.php', function (data) {

        var tbody = $('#deli-rows');
        tbody.empty();

        var rows = '';
        for (var i = 0; i < data.length; i++) {

            if (data[i].day === "SOBOTA" || data[i].day === "NEDEĽA") {
                continue;
            }

            var date = data[i].date;
            var menu = data[i].menu;

            for (var j = 0; j < menu.length; j++) {
                if (menu[i] === "ŠTÁTNY SVIATOK") {
                    rows = '<tr><td>' + date + '</td><td>' + menu[i] + '</td><td>---</td></tr>'
                    break;
                }
                var meal = menu[j];
                var parts = meal.split(' ');
                var name = parts.slice(0, -1).join(' ');
                var price = '---';

                rows += '<tr><td>' + date + '</td><td>' + name + '</td><td>' + price + '</td></tr>';
            }
        }
        tbody.html(rows);
    });

    return true;
}