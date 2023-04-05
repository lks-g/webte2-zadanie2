<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <title>Restaurants</title>
</head>

<body>
    <nav class="navbar navbar-expand-md bg-dark navbar-dark">
        <a class="navbar-brand" href="#">Validate API</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="api_doc.php">API</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../index.php">Restaurant Menus</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../html/index.html">Test API</a>
                </li>
            </ul>
        </div>
    </nav>

    <div id="buttons">
        <h2>Validate API</h2>
        <div id="validate">
            <button id="download" type="button" class="btn btn-primary">Download</button>
            <button id="parse" type="button" class="btn btn-success">Parse</button>
            <button id="delete" type="button" class="btn btn-danger">Delete</button>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="../scripts/script.js"></script>
</body>

</html>