<?php
// SIMPLE LOGIN PARA REPORTE - UNA SOLA CUENTA
session_start();

// Credenciales de la única cuenta de admin
$admin_user = 'admin';
$admin_pass = 'Rfc@32415';

// Verificar si el usuario está intentando hacer logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: reporte.php');
    exit;
}

// Verificar si el usuario está intentando hacer login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    if ($_POST['username'] === $admin_user && $_POST['password'] === $admin_pass) {
        $_SESSION['admin_logged'] = true;
        $_SESSION['login_time'] = time();
    } else {
        $login_error = 'Usuario o contraseña incorrectos';
    }
}

// Si no está logueado, mostrar formulario de login
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="utf-8">
        <title>Login | Reporte de Conexiones</title>
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
            .login-container {
                background: #fff;
                padding: 40px 30px;
                border-radius: 15px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.1);
                width: 100%;
                max-width: 350px;
                text-align: center;
            }
            .login-container h1 {
                margin-bottom: 10px;
                color: #223f7f;
                font-size: 24px;
            }
            .login-container p {
                margin-bottom: 30px;
                color: #666;
                font-size: 14px;
            }
            .form-group {
                margin-bottom: 15px;
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
                font-size: 14px;
                transition: border 0.3s;
            }
            .form-group input:focus {
                border-color: #223f7f;
                box-shadow: 0 0 5px rgba(34, 63, 127, 0.3);
                outline: none;
            }
            .login-btn {
                width: 100%;
                padding: 12px;
                border: none;
                border-radius: 10px;
                background-color: #ed292e;
                color: #fff;
                font-size: 16px;
                font-weight: bold;
                cursor: pointer;
                transition: background-color 0.3s;
            }
            .login-btn:hover {
                background-color: #a31b1e;
            }
            .error {
                background-color: #ffe0e0;
                color: #a31b1e;
                padding: 10px;
                border-radius: 8px;
                margin-bottom: 20px;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h1>Reporte WiFi</h1>
            <p>Ingrese sus credenciales para acceder</p>
            
            <?php if (isset($login_error)): ?>
                <div class="error"><?= htmlspecialchars($login_error) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" id="username" name="username" placeholder="admin" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="login-btn">Ingresar</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Si llegó aquí, está logueado - MOSTRAR EL REPORTE

$db_host = 'localhost';
$db_user = 'unifi';
$db_pass = 'unifi123';
$db_name = 'usuarios_unifi';
$table_name = 'usuarios';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die('Error de conexión MySQL: ' . $conn->connect_error);
}

$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
$export_excel = isset($_GET['excel']);

if ($fecha_inicio && strlen($fecha_inicio) == 10) {
    $fecha_inicio .= ' 00:00:00';
}
if ($fecha_fin && strlen($fecha_fin) == 10) {
    $fecha_fin .= ' 23:59:59';
}

$where = '';
$params = [];
if ($fecha_inicio && $fecha_fin) {
    $where = " WHERE fecha BETWEEN ? AND ? ";
    $params[] = $fecha_inicio;
    $params[] = $fecha_fin;
}

$sql = "SELECT nombre, red, mac, fecha, correo FROM `$table_name`" . $where . " ORDER BY fecha DESC";
$stmt = $conn->prepare($sql);
if ($where) {
    $stmt->bind_param('ss', $params[0], $params[1]);
}
$stmt->execute();
$result = $stmt->get_result();

if ($export_excel) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="reporte_wifi.xls"');
    echo "<table border='1'>";
    echo "<tr><th>Nombre</th><th>Red</th><th>MAC</th><th>Fecha</th><th>Correo</th></tr>";
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($row['red']) . "</td>";
            echo "<td>" . htmlspecialchars($row['mac']) . "</td>";
            echo "<td>" . htmlspecialchars($row['fecha']) . "</td>";
            echo "<td>" . htmlspecialchars($row['correo']) . "</td>";
            echo "</tr>";
        }
    }
    echo "</table>";
    $conn->close();
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Red invitados | Reporte de conexiones</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            max-width: 900px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            padding: 30px;
            width: 100%;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        h1 { text-align: center; color: #223f7f; flex: 1; }
        .logout-btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            background: #ed292e;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.3s;
        }
        .logout-btn:hover {
            background: #a31b1e;
        }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 12px 10px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #ed292e; color: #fff; }
        tr:hover { background: #f9f9f9; }
        .filter-form { margin-bottom: 20px; text-align: center; }
        .filter-form input { padding: 6px 10px; border-radius: 6px; border: 1px solid #ccc; margin-right: 10px; }
        .filter-form button { padding: 6px 16px; border-radius: 6px; border: none; background: #223f7f; color: #fff; font-weight: bold; }
        .filter-form button:hover { background: #ed292e; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Red invitados | Reporte de conexiones</h1>
            <a href="reporte.php?logout=true" class="logout-btn">Cerrar Sesión</a>
        </div>
        <form class="filter-form" method="get">
            <label>Desde: <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>"></label>
            <label>Hasta: <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>"></label>
            <button type="submit">Filtrar</button>
            <button type="submit" name="excel" value="1">Descargar Excel</button>
        </form>
        <table>
            <tr>
                <th>Nombre</th>
                <th>Red</th>
                <th>MAC</th>
                <th>Fecha</th>
                <th>Correo</th>
            </tr>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                        <td><?= htmlspecialchars($row['red']) ?></td>
                        <td><?= htmlspecialchars($row['mac']) ?></td>
                        <td><?= htmlspecialchars($row['fecha']) ?></td>
                        <td><?= htmlspecialchars($row['correo']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">No hay registros.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
<?php $conn->close(); ?>