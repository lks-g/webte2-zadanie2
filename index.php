<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title>Restaurants</title>
</head>

<body>
    <nav class="top-nav">
        <ul>
            <li><a href="#">Restaurants</a></li>
            <li><a href="php/api_doc.php">API Doc</a></li>
        </ul>
    </nav>

    <div id="buttons">
        <h2>Validate API</h2>
        <div id="validate">
            <button id="download" type="button"">Download</button>
            <button id="parse" type="button">Parse</button>
            <button id="delete" type="button">Delete</button>
        </div>
    </div>

    <div id="menu">
        <table id="menu-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Day</th>
                    <th>Menu</th>
                </tr>
            </thead>
            <tbody id="menu-rows">

            </tbody>
        </table>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="scripts/script.js"></script>
</body>

</html>