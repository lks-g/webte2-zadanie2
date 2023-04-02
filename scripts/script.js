$(document).ready(function () {
    $("#download").click(function () {
        $.ajax({
            url: "../php/download_menu.php",
            method: "POST",
            error: function (xhr, status, error) {
                console.log(error);
            }
        });
    });

    $('#parse').click(function () {
        $.ajax({
            type: 'POST',
            url: '../php/parse_data.php',
            success: function (response) {
                alert('Data parsed successfully');
            },
            error: function () {
                alert('Error parsing data');
            }
        });
    });

    $('#delete').click(function () {
        $.ajax({
            type: 'POST',
            url: '../php/delete_tables.php',
            success: function (response) {
                alert('Tables deleted successfully');
            },
            error: function () {
                alert('Error deleting tables');
            }
        });
    });
});