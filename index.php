<?php
session_start();

//Get the MAC addresses of AP and user
$_SESSION["id"] = $_GET["id"];
$_SESSION["ap"] = $_GET["ap"];
// MySQL connection
$db_host = 'localhost';
$db_user = 'unifi'; // Cambia por tu usuario
$db_pass = 'unifi123';
$db_name = 'usuarios-unifi';
$table_name = 'usuarios';
$mac = $_SESSION["id"];

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die('Error de conexión MySQL: ' . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT mac FROM `$table_name` WHERE mac = ?");
$stmt->bind_param('s', $mac);
$stmt->execute();
$stmt->store_result();
$mac_registered = $stmt->num_rows > 0;
$stmt->close();

if ($mac_registered) {
    // Usuario ya registrado, mostrar mensaje de acceso
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Acceso WiFi | Cremona Inoxidable</title><meta name="viewport" content="width=device-width, initial-scale=1.0"><style>*{box-sizing:border-box;margin:0;padding:0;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;}body{background:#f0f2f5;display:flex;justify-content:center;align-items:center;height:100vh;}.card{background:#fff;padding:40px 30px;border-radius:15px;box-shadow:0 10px 25px rgba(0,0,0,0.1);width:100%;max-width:400px;text-align:center;}.logo{width:200px;margin-bottom:10px;}p.back-text{margin-bottom:5px;color:#333;font-size:18px;font-weight:bold;}p.back2-text{margin-bottom:0px;color:#333;font-size:14px;}</style></head><body><div class="card"><img src="Creminox.png" alt="Creminox Logo" class="logo"><p class="back-text">¡Su conexión fue establecida!</p><p class="back2-text">Ya puede navegar por la web. <br>Si no es redirigido automaticamente, puede retirarse de esta página sin problemas.</p></div></body></html>';
    exit;
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
        <!-- Logo SVG centrado -->
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
