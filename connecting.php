<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Crear tabla con id autoincremental para permitir múltiples registros por MAC (historial)
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

// Migrar esquema viejo si la tabla ya existía con mac como PRIMARY KEY
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

// Desconectar cualquier sesión activa existente para esta MAC
$unifi_connection->unauthorize_guest($mac);

// Autorizar al invitado
$unifi_connection->authorize_guest($mac, $duration, null, null, null, $ap);

// Guardar registro de conexión
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

// Redirigir a la URL de detección de Apple para que iOS cierre el CNA automáticamente
header('Location: http://captive.apple.com/hotspot-detect.html');
exit;
