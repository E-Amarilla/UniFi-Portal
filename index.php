<?php
session_start();

$_SESSION["id"] = $_GET["id"];
$_SESSION["ap"] = $_GET["ap"];

$db_host = 'localhost';
$db_user = 'unifi';
$db_pass = 'unifi123';
$db_name = 'usuarios_unifi';
$table_name = 'usuarios';
$mac = $_SESSION["id"];
$ap = $_SESSION["ap"];

$mac_registered = false;
$nombre = '';
$correo = '';

$conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);

if (!$conn->connect_error) {
    $stmt = $conn->prepare("SELECT nombre, correo FROM `$table_name` WHERE mac = ? ORDER BY fecha DESC LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('s', $mac);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $mac_registered = true;
            $stmt->bind_result($nombre, $correo);
            $stmt->fetch();
        }
        $stmt->close();
    }
}

if ($mac_registered) {
    require __DIR__ . '/vendor/autoload.php';

    $duration = 0;
    $site_id = 'default';

    $controlleruser     = 'admin';
    $controllerpassword = 'miloj@324156';
    $controllerurl      = 'https://192.168.20.16:8443';
    $controllerversion  = '10.0.162';
    $debug = false;

    $unifi_connection = new UniFi_API\Client($controlleruser, $controllerpassword, $controllerurl, $site_id, $controllerversion);
    $unifi_connection->set_debug($debug);
    $unifi_connection->login();

    $unifi_connection->unauthorize_guest($mac);

    $unifi_connection->authorize_guest($mac, $duration, null, null, null, $ap);

    $red = 'WiFi Invitados';
    $fecha = date('Y-m-d H:i:s');

    $stmt_insert = $conn->prepare("INSERT INTO `$table_name` (nombre, red, mac, fecha, correo) VALUES (?, ?, ?, ?, ?)");
    if ($stmt_insert) {
        $stmt_insert->bind_param('sssss', $nombre, $red, $mac, $fecha, $correo);
        $stmt_insert->execute();
        $stmt_insert->close();
    }

    $conn->close();

    echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Acceso WiFi | Cremona Inoxidable</title><meta name="viewport" content="width=device-width, initial-scale=1.0"><style>*{box-sizing:border-box;margin:0;padding:0;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;}body{background:#f0f2f5;display:flex;justify-content:center;align-items:center;height:100vh;}.card{background:#fff;padding:40px 30px;border-radius:15px;box-shadow:0 10px 25px rgba(0,0,0,0.1);width:100%;max-width:400px;text-align:center;}.logo{width:200px;margin-bottom:10px;}p.back-text{margin-bottom:5px;color:#333;font-size:18px;font-weight:bold;}p.back2-text{margin-bottom:0px;color:#333;font-size:14px;}</style></head><body><div class="card"><img src="Creminox.png" alt="Creminox Logo" class="logo"><p class="back-text">¡Su conexión fue establecida!</p><p class="back2-text">Ya puede navegar por la web. <br>Si no es redirigido automaticamente, puede retirarse de esta página sin problemas.</p></div></body></html>';
    exit;
} else {
    if ($conn && !$conn->connect_error) {
        $conn->close();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Acceso WiFi | Cremona Inoxidable</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
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

        .card svg {
            width: 150px;
            height: auto;
            margin-bottom: 30px;
        }

        .card h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 16px;
            transition: border 0.3s, box-shadow 0.3s;
        }

        .form-group input:focus {
            border-color: #223f7f;
            box-shadow: 0 0 5px rgba(34, 63, 127, 0.3);
            outline: none;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            background-color: #ed292e;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #a31b1e;
        }

        p.welcome-text {
            margin-bottom: 5px;
            color: #333;
            font-size: 18px;
            font-weight: bold;
        }
        
        p.welcome2-text {
            margin-bottom: 20px;
            color: #333;
            font-size: 15px;
        }

        p.exit-text {
            margin-bottom: 10px;
            color: #ccc;
            font-size: 9px;
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

        <p class="welcome-text">¡Bienvenidos!</p>
        <p class="welcome2-text">Por favor, complete los campos para obtener conexión a internet.</p>

        <form method="post" action="connecting.php">
            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" id="name" name="name" placeholder="Ingrese su nombre">
            </div>
            <div class="form-group">
                <label for="email">Correo electronico</label>
                <input type="email" id="email" name="email" placeholder="Ingrese su correo electronico">
            </div>
            <p class="exit-text">Su información será almacenada con fines estadísticos y de mejora del servicio.</p>
            <input type="submit" value="Ingresar">
        </form>
    </div>
</body>
</html>
