<?php
session_start();

$mac = $_SESSION["id"];
$ap = $_SESSION["ap"];
$name = $_POST['name'];
$email = $_POST['email'];

$db_host = 'localhost';
$db_user = 'unifi';
$db_pass = 'unifi123';
$db_name = 'usuarios_unifi';
$table_name = 'usuarios';

$conn = new mysqli($db_host, $db_user, $db_pass);
if ($conn->connect_error) {
    die('Error de conexión MySQL: ' . $conn->connect_error);
}

$conn->query("CREATE DATABASE IF NOT EXISTS `$db_name`");
$conn->select_db($db_name);

$create_table_sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    red VARCHAR(100),
    mac VARCHAR(20),
    fecha DATETIME,
    correo VARCHAR(100),
    INDEX idx_mac (mac)
);";
$conn->query($create_table_sql);

$col_check = $conn->query("SHOW COLUMNS FROM `$table_name` LIKE 'id'");
if ($col_check && $col_check->num_rows == 0) {
    $conn->query("ALTER TABLE `$table_name` DROP PRIMARY KEY");
    $conn->query("ALTER TABLE `$table_name` ADD COLUMN id INT AUTO_INCREMENT PRIMARY KEY FIRST");
    $conn->query("ALTER TABLE `$table_name` ADD INDEX idx_mac (mac)");
}

require __DIR__ . '/vendor/autoload.php';

$duration = 0;
$site_id = 'default';

$controlleruser     = 'admin';
$controllerpassword = 'miloj@324156';
$controllerurl      = 'https://192.168.20.16:8443';
$controllerversion  = '10.0.162'; //Version del UniFi sv
$debug = false;

$unifi_connection = new UniFi_API\Client($controlleruser, $controllerpassword, $controllerurl, $site_id, $controllerversion);
$unifi_connection->set_debug($debug);
$unifi_connection->login();

$unifi_connection->unauthorize_guest($mac);

$unifi_connection->authorize_guest($mac, $duration, null, null, null, $ap);

$nombre = $name;
$red = 'WiFi Invitados';
$mac_address = $mac;
$fecha = date('Y-m-d H:i:s');
$correo = $email;

$stmt = $conn->prepare("INSERT INTO `$table_name` (nombre, red, mac, fecha, correo) VALUES (?, ?, ?, ?, ?)");
if ($stmt) {
    $stmt->bind_param('sssss', $nombre, $red, $mac_address, $fecha, $correo);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
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

        p.back-text {
            margin-bottom: 5px;
            color: #333;
            font-size: 18px;
            font-weight: bold;
        }
        
        p.back2-text {
            margin-bottom: 0px;
            color: #333;
            font-size: 14px;
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

        <p class="back-text">¡Su conexión fue establecida!</p>
        <p class="back2-text">Ya puede navegar por la web. <br>Si no es redirigido automaticamente, puede retirarse de esta página sin problemas.</p>
    </div>
</body>
</html>
