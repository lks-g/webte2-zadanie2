$(document).ready(function () {
    fetchMenuData();
});

function fetchMenuData() {
    $.ajax({
        url: "../php/menu.php",
        type: "GET",
        dataType: "json",
        success: function (data) {
            populateMenuTable(data);
        },
        error: function () {
            alert("[Error] - fetching data from menu.");
        }
    });
}

function populateMenuTable(data) {
    var table = '<table class="table"><thead><tr><th>Provider</th><th>Dish</th><th>Price</th><th>Location</th><th>Image</th></tr></thead><tbody>';

    $.each(data, function (providerName, dishes) {
        table += '<tr><td rowspan="' + dishes.length + '">' + providerName + '</td>';

        $.each(dishes, function (index, dish) {
            table += '<td>' + dish.name + '</td><td>' + dish.price + '</td><td>' + dish.location + '</td><td><img src="' + dish.image_url + '" alt="' + dish.name + '"></td></tr>';
        });
    });

    table += '</tbody></table>';
    $('#menus').html(table);
}

$(document).ready(function () {
    $("#download").click(function () {
        $.ajax({
            url: "../php/download_menu.php",
            method: "POST",
            success: function (data) {
                $("#downloaded-data").html(data);
            }
        });
    });

    $('#parse').click(function () {
        $.ajax({
            type: 'POST',
            url: '../php/parse_data.php',
            success: function(response) {
                alert('Data parsed successfully');
            },
            error: function() {
                alert('Error parsing data');
            }
        });
    });

    $('#delete').click(function () {
        $.ajax({
            type: 'POST',
            url: '../php/delete_tables.php',
            success: function(response) {
                alert('Tables deleted successfully');
            },
            error: function() {
                alert('Error deleting tables');
            }
        });
    });
});