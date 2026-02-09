<?php
// reporte.php
$db_host = 'localhost';
$db_user = 'unifi';
$db_pass = 'unifi123';
$db_name = 'usuarios_unifi';
$table_name = 'usuarios';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die('Error de conexión MySQL: ' . $conn->connect_error);
}

$sql = "SELECT nombre, red, mac, fecha, correo FROM `$table_name` ORDER BY fecha DESC";
$result = $conn->query($sql);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de conexiones WiFi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .container { max-width: 900px; margin: 40px auto; background: #fff; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); padding: 30px; }
        h1 { text-align: center; color: #223f7f; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 12px 10px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #ed292e; color: #fff; }
        tr:hover { background: #f9f9f9; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reporte de conexiones WiFi</h1>
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