<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Entrenador</title>
    <link rel="stylesheet" href="../CSS/indexmodi.css">
    <link rel="stylesheet" href="../CSS/header.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header>
    <div class="prese">
        <h1>Modificar Entrenador</h1>
        <div class="logo">
            <img src="../Imagenes/sanmiguel.png" alt="Logo San Miguel">
        </div>
    </div>
    <nav class="nav-list">
        <ul>
            <h2><li><a href="http://localhost:8080/Sportclub/">Principal</a></li></h2>
            <h2><li><a href="http://localhost:8080/Sportclub/entrenadores/listado_entrenadores.php">Entrenadores</a></li></h2>
        </ul>
    </nav>
</header>

<?php
include("../conexion.php");
$con = mysqli_connect($host, $user, $pwd, $BD) or die("FALLO DE CONEXION");

$dni_entrenador = isset($_GET['entrenador']) ? mysqli_real_escape_string($con, $_GET['entrenador']) : null;
$mostrar_alerta = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['modiDNI'])) {

    $query_update = "UPDATE entrenador SET
        nombre = '$_POST[modiNombre]',
        apellido = '$_POST[modiApellido]',
        telefono = '$_POST[modiTelefono]',
        
        id_actividad = '$_POST[modiActividad]'
    WHERE dni = '$_POST[modiDNI]'";

    $resultado_update = mysqli_query($con, $query_update) or die("FALLO DE CONSULTA DE ACTUALIZACIÓN");

    if ($resultado_update) {
        $mostrar_alerta = true;
    }
}

if ($dni_entrenador) {
    $query_select = "SELECT * FROM entrenador WHERE dni = '$dni_entrenador'";
    $result_select = mysqli_query($con, $query_select) or die("ERROR DE CONSULTA");

    if (mysqli_num_rows($result_select) > 0) {
        while ($row = mysqli_fetch_array($result_select)) {
?>
<!-- Aquí comienza el formulario -->
<form method="POST" action="">
    DNI: <input type="text" name="modiDNI" value="<?php echo htmlspecialchars($row['dni']); ?>" readonly> <br>
    Nombre: <input type="text" name="modiNombre" value="<?php echo htmlspecialchars($row['nombre']); ?>"> <br>
    Apellido: <input type="text" name="modiApellido" value="<?php echo htmlspecialchars($row['apellido']); ?>"> <br>
    Teléfono: <input type="text" name="modiTelefono" value="<?php echo htmlspecialchars($row['telefono']); ?>"> <br>
    Actividad:
    <select name="modiActividad">
        <?php
        $query_actividades = "SELECT id_actividad, nombre FROM actividad";
        $result_actividades = mysqli_query($con, $query_actividades);

        while ($actividad = mysqli_fetch_array($result_actividades)) {
            $selected = ($row['id_actividad'] == $actividad['id_actividad']) ? 'selected' : '';
            echo "<option value='{$actividad['id_actividad']}' $selected>{$actividad['nombre']}</option>";
        }
        ?>
    </select> <br>
    <input type="submit" value="Actualizar">
</form>

<?php
        }
    } else {
        echo "<div class='alert alert-danger'>No se encontraron resultados para el DNI: " . htmlspecialchars($dni_entrenador) . "</div>";
    }
}

mysqli_close($con);
?>

<script>
function updateLabel(checkbox) {
    var label = document.getElementById('estadoLabel');
    label.textContent = checkbox.checked ? 'Activo' : 'Inactivo';
}
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
    <?php if ($mostrar_alerta): ?>
        Swal.fire({
            position: 'mid',
            icon: 'success',
            title: 'Datos actualizados correctamente',
            showConfirmButton: false,
            timer: 1500
        });
    <?php endif; ?>
</script>
</body>
</html>
