<?php

session_start();

$mac = $_SESSION["id"];
$ap = $_SESSION["ap"];
$name = $_POST['name'];
$email = $_POST['email'];

require __DIR__ . '/vendor/autoload.php';

$duration = 30; //Duration of authorization in minutes
$site_id = 'default'; //Site ID found in URL (https://1.1.1.1:8443/manage/site/<site_ID>/devices/1/50)

$controlleruser     = 'admin'; // the user name for access to the UniFi Controller
$controllerpassword = 'miloj@324156'; // the password for access to the UniFi Controller
$controllerurl      = 'https://192.168.20.16:8443'; // full url to the UniFi Controller, eg. 'https://22.22.11.11:8443'
$controllerversion  = '10.0.162'; // the version of the Controller software, eg. '4.6.6' (must be at least 4.0.0)
$debug = false;

$unifi_connection = new UniFi_API\Client($controlleruser, $controllerpassword, $controllerurl, $site_id, $controllerversion);
$set_debug_mode   = $unifi_connection->set_debug($debug);
$loginresults     = $unifi_connection->login();

$auth_result = $unifi_connection->authorize_guest($mac, $duration, $up = null, $down = null, $MBytes = null, $ap);

//User will be authorized at this point; their name and email address can be saved to some database now
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Acceso WiFi | Cremona Inoxidable</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Estilos embebidos -->
    <style>
        /* Reset básico */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: #fff;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        p.welcome-text {
            margin-bottom: 5px;
            color: #333;
            font-size: 18px;
            font-weight: bold;
        }
        
        p.welcome2-text {
            margin-bottom: 0px;
            color: #333;
            font-size: 15px;
        }

        .logo {
            width: 200px;
            margin-bottom: 10px;
        }

    </style>
</head>
<body>
    <div class="card">
        <img src="Creminox.png" alt="Creminox Logo" class="logo">

        <p class="welcome-text">¡Su conexión fue establecida!</p>
        <p class="welcome2-text">Será redirigido a "google.com" automáticamente. <br>Si no es así, puede retirarse de esta página sin problemas.</p>
    </div>
</body>
</html>
