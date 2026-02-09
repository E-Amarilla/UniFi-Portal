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

// Manejo de filtro y exportación
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
$export_excel = isset($_GET['excel']);

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
        .filter-form { margin-bottom: 20px; text-align: center; }
        .filter-form input { padding: 6px 10px; border-radius: 6px; border: 1px solid #ccc; margin-right: 10px; }
        .filter-form button { padding: 6px 16px; border-radius: 6px; border: none; background: #223f7f; color: #fff; font-weight: bold; }
        .filter-form button:hover { background: #ed292e; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reporte de conexiones WiFi</h1>
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