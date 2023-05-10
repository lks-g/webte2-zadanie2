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

    <div id="menu-buttons">
        <button class="day-btn" type="button" id="btn-pon">Pondelok</button>
        <button class="day-btn" type="button" id="btn-uto">Utorok</button>
        <button class="day-btn" type="button" id="btn-str">Streda</button>
        <button class="day-btn" type="button" id="btn-stv">Štvrtok</button>
        <button class="day-btn" type="button" id="btn-pia">Piatok</button>
        <button class="day-btn" type="button" id="btn-sob">Sobota</button>
        <button class="day-btn" type="button" id="btn-ned">Nedeľa</button>
        <button class="day-btn" type="button" id="btn-all">Všetky</button>
    </div>

    <div id="menu">
        <table id="eat-table">
            <h1>Eat&Meet</h1>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Menu</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody id="eat-rows"></tbody>
        </table>

        <table id="free-table">
            <h1>FIIT Food</h1>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Menu</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody id="free-rows"></tbody>
        </table>

        <table id="deli-table">
            <h1>Delikanti</h1>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Menu</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody id="deli-rows"></tbody>
        </table>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="scripts/script.js"></script>
</body>

</html>