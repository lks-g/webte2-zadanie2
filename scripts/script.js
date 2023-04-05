$(document).ready(function () {
    $("#download").click(function () {
        $.ajax({
            url: "../rest-be/download_menu.php",
            method: "POST",
            error: function (xhr, status, error) {
                console.log(error);
            }
        });
    });

    $('#parse').click(function () {
        $.ajax({
            type: 'POST',
            url: '../rest-be/parse_data.php',
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
            url: '../rest-be/delete_tables.php',
            success: function (response) {
                alert('Tables deleted successfully');
            },
            error: function () {
                alert('Error deleting tables');
            }
        });
    });
});