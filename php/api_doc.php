<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <title>Restaurants - API</title>
</head>

<body>
    <nav class="top-nav">
        <ul>
            <li><a href="../index.php">Restaurants</a></li>
            <li><a href="#">API Doc</a></li>
        </ul>
    </nav>

    <div id="docs">
        <h2>Popis API</h2>
        <p>
            Toto API poskytuje prístup k databáze, ktorá obsahuje informácie o ponuke jedálneho lístka
            od poskytovateľov FreeFood, Venza, Eat&Meet. Jedná sa o RESTful API, ktorá
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

        <br>
        <h2><code>get_menus</code></h2><br>
        <p>
            <strong>API endpoint:</strong> <code>/menus</code><br>
            <strong>HTTP Metóda:</strong> <code>GET</code><br>
            <strong>Popis:</strong> Tento API endpoint vracia informácie o menu, ktoré sú k dispozícii v databáze. Informácie je možné získať buď podľa ID menu alebo všetkých menu.
        </p>
        <h3>Parametre požiadavky:</h3>
        <table>
            <thead>
                <tr>
                    <th>Parameter</th>
                    <th>Typ</th>
                    <th>Popis</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>id</code></td>
                    <td>Celé číslo</td>
                    <td>ID menu, ktoré chcete získať.</td>
                </tr>
                <tr>
                    <td><code>method</code></td>
                    <td>Reťazec</td>
                    <td>HTTP metóda použitá na získanie dát. Tento parameter sa v tomto endpointe nepoužíva.</td>
                </tr>
            </tbody>
        </table>
        <h3>Odpoveď:</h3>
        <p>Ak je požiadavka úspešná, telo odpovede bude obsahovať informácie o menu vo formáte JSON, ktoré zodpovedajú parametrom požiadavky. Ak parameter <code>id</code> existuje, odpoveď bude JSON objekt obsahujúci informácie o jednom menu. Ak parameter <code>id</code> neexistuje, odpoveď bude JSON pole obsahujúce informácie o všetkých menu.</p>
        <h4>Príklad požiadavky:</h4>
        <pre>
            GET /menus?id=1 HTTP/1.1
            Host: example.com
            Content-Type: application/json
        </pre>
        <h4>Príklad odpovede:</h4>
        <pre>
            HTTP/1.1 200 OK
            Content-Type: application/json
            {
                "menu_id": 1,
                "name": "Hlavné menu",
                "price": 6.99
            }
    </pre>

        <h4>Možné chybové odpovede:</h4>
        <ul>
            <li>Ak zadané ID menu v požiadavke neexistuje v databáze, server odpovie HTTP kódom 404 a chybovou správou:</li>
        </ul>
        <pre>
        HTTP/1.1 404 Not Found
        Content-Type: application/json

        {
            "error": "Menu not found"
        }
    </pre>

        <h2><code>create_menu</code></h2>
        <p>
            <b>Endpoint:</b> POST /menus<br><br>
            <b>Popis:</b> Vytvorí nové menu.<br><br>

            php

            <b>Request Body:</b><br>
            <code>
                {
                "provider_id": integer,
                "menu_date": string,
                "source_code": string,
                "download_date": string
                }
            </code><br><br>

            <b>Odpoveď:</b><br>
            Stav: 201 Created<br>
            <code>
                {
                "menu_id": integer,
                "provider_id": integer,
                "menu_date": string,
                "source_code": string,
                "download_date": string
                }
            </code><br><br>

            <b>Chybové odpovede:</b><br>
            400 Bad Request: Neplatný formát požiadavky alebo chýbajúce požadované polia.<br>
            500 Internal Server Error: Nepodarilo sa vytvoriť menu v databáze.
        </p>

        <h2><code>update_menu</code></h2>
        <p>
            Aktualizovať menu
            Tento API koncový bod aktualizuje existujúci menu zdroj identifikovaný jeho menu_id.

        </p>
        <h3>Žiadosť</h3>
        <ul>
            <li>HTTP metóda: PUT</li>
            <li>URL cesta: /menus/{menu_id}</li>
            <li>Hlavičky:
                <ul>
                    <li>Content-Type: application/json</li>
                </ul>
            </li>
            <li>Žiadosť Body: JSON objekt obsahujúci aktualizované hodnoty vlastností zdroja menu. Musí obsahovať tieto vlastnosti:
                <ul>
                    <li>provider_id (povinné): ID poskytovateľa, ktorý poskytuje menu. Musí byť celé číslo.</li>
                    <li>menu_date (povinné): dátum menu v formáte YYYY-MM-DD. Musí byť reťazec.</li>
                    <li>source_code (povinné): zdrojový kód menu. Musí byť reťazec.</li>
                    <li>download_date (povinné): dátum, kedy bolo menu stiahnuté v formáte YYYY-MM-DD. Musí byť reťazec.</li>
                </ul>
            </li>
        </ul>
        <h3>Odpoveď</h3>
        <p>
            Odpoveď na úspešnú požiadavku:
        </p>
        <ul>
            <li>Status kód: 200 OK</li>
            <li>Odpoveď Body: JSON objekt obsahujúci aktualizované hodnoty vlastností zdroja menu. Musí obsahovať tieto vlastnosti:
                <ul>
                    <li>menu_id (celé číslo): ID aktualizovaného menu.</li>
                    <li>provider_id (celé číslo): ID poskytovateľa, ktorý poskytuje menu.</li>
                    <li>menu_date (reťazec): dátum menu v formáte YYYY-MM-DD.</li>
                    <li>source_code (reťazec): zdrojový kód menu.</li>
                    <li>download_date (reťazec): dátum, kedy bolo menu stiahnuté v formáte YYYY-MM-DD.</li>
                </ul>
            </li>
        </ul>
        <p>
            Odpoveď na chybnú požiadavku:
        </p>
        <ul>
            <li>Status kód: 400 Bad Request</li>
            <li>Odpoveď Body: JSON objekt obsahujúci chybovú správu.</li>
            <li>Status kód: 500 Internal Server Error</li>
            <li>Odpoveď Body: JSON objekt obsahujúci chybovú správu.</li>
        </ul>

        <h2><code>delete_menu</code></h2>
        <br>
        <p>
            Endpoint: <code>DELETE /menus/{menu_id}</code>
        </p>
        <p>
            Popis: Vymaže menu s daným identifikátorom.
        </p>
        <p>
            URL parametre:
        </p>
        <ul>
            <li><code>menu_id</code> (povinné): Identifikátor menu, ktoré sa má vymazať.</li>
        </ul>
        <p>
            Odpoveď:
        </p>
        <ul>
            <li><code>200 OK</code>: Ak sa menu úspešne vymaže.</li>
            <li><code>400 Bad Request</code>: Ak chýba identifikátor menu.</li>
            <li><code>500 Internal Server Error</code>: Ak sa vyskytne chyba na strane servera.</li>
        </ul>
        <p>
            Príklad:
        </p>
        <pre>
            DELETE /menus/123
        </pre>
        <p>
            Odpoveď:
        </p>
        <pre>
            {
                "success": true
            }
        </pre>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="../scripts/script.js"></script>
</body>

</html>