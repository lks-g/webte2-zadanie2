<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Restaurants - API</title>
</head>

<body>
    <nav class="navbar navbar-expand-md bg-dark navbar-dark">
        <a class="navbar-brand" href="../index.php">Lunch Menus</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="#">API</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="validate.php">Validate API</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../html/index.html">Test API</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-sm-12">
                <h2>Popis API</h2>
                <p>
                    Toto API poskytuje prístup k databáze, ktorá obsahuje informácie o ponuke jedálneho lístka 
                    od poskytovateľov FreeFood, Venza, Eat&Meet. Jedná sa o RESTful API, ktoré 
                    používa štandardné HTTP metódy (GET, POST, PUT, DELETE) na interakciu s dátami. 
                    API vracia odpovede vo formáte JSON.
                </p>
                <h3>Endpointy</h3>
                <p>
                    K dispozícii sú nasledujúce endpointy:
                </p>
                <ul>
                    <li><code>GET /api/menus</code>: Vráti zoznam všetkých reštaurácií v databáze.</li>
                    <li><code>GET /api/menus/{menu_id}</code>: Vráti konkrétne menu podľa ID.</li>
                    <li><code>POST /api/menus</code>: Vytvorí nové menu.</li>
                    <li><code>PUT /api/menus/{menu_id}</code>: Aktualizuje menu podľa ID.</li>
                    <li><code>DELETE /api/menus/{menu_id}</code>: Zmaže menu podľa ID.</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="../scripts/script.js"></script>
</body>

</html>