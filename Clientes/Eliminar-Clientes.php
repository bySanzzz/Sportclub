<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Cliente</title>
    <link rel="stylesheet" href="../CSS/indexmodi.css">
    <link rel="stylesheet" href="../CSS/header.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<header>
    <div class="prese">
        <h1>Eliminar Cliente</h1>
        <div class="logo">
            <img src="../Imagenes/sanmiguel.png" alt="Logo San Miguel">
        </div>
    </div>
</header>

<?php
    include("../conexion.php");

    // Variable para almacenar mensajes
    $mensaje = '';

    // Conectar a la base de datos
    $con = mysqli_connect($host, $user, $pwd, $BD) or die("Fallo de conexión");

    // Verificar si se ha recibido el DNI por POST
    if (isset($_POST['DNI'])) {
        // Obtener el DNI del cliente desde el formulario
        $dni = $_POST['DNI'];

        // Consulta para verificar si el cliente existe
        $check_query = "SELECT * FROM cliente WHERE dni = '$dni'";
        $check_result = mysqli_query($con, $check_query) or die("Fallo en la consulta");

        // Si hay resultados, significa que el cliente existe
        if (mysqli_num_rows($check_result) > 0) {
            // Mostrar los datos del cliente
            while ($row = mysqli_fetch_array($check_result)) {
?>
                <form method="POST" action="">
                    DNI: <input type="text" name="modiDNI" value="<?php echo($row['dni']); ?>" readonly> <br>
                    Nombre: <?php echo($row['nombre']); ?> <br>
                    Apellido: <?php echo($row['apellido']); ?> <br>
                    Correo: <?php echo($row['correo']); ?> <br>
                    Estado: <?php echo($row['estado']); ?> <br>
                    <br>
                    <input type="button" name="eliminar" value="Eliminar Cliente" class="btn btn-danger" style="background-color: red;" onclick="confirmDelete('<?php echo $row['dni']; ?>');">
                </form>
                <div class="volvido">
                    <a href="../cliente/listarClientes.php">VOLVER</a>
                </div>
<?php
            }
        } else {
            // Si el DNI no existe en la base de datos
            $mensaje = "<div class='alert alert-danger'>El cliente con DNI '$dni' no existe.</div>";
        }
    }

    // Código para eliminar al cliente
    if (isset($_POST['eliminar']) && isset($_POST['modiDNI'])) {
        $dni_eliminar = $_POST['modiDNI'];
        $query_delete = "DELETE FROM cliente WHERE dni ='$dni_eliminar'";
        $resultado = mysqli_query($con, $query_delete) or die("Fallo de consulta");

        if ($resultado) {
            echo "<script>
                Swal.fire({
                    position: 'mid',
                    icon: 'success',
                    title: 'El cliente ha sido eliminado correctamente.',
                    html: '<a href=\"../cliente/listarClientes.php\" class=\"btn btn-success\">VOLVER A LISTA DE CLIENTES</a>',
                    showConfirmButton: false,
                });
              </script>";
        }
    }
?>

<!-- Mostrar mensaje si existe -->
<?php if ($mensaje != ''): ?>
    <?php echo $mensaje; ?>
<?php endif; ?>

<!-- Confirmación antes de eliminar -->
<script>
function confirmDelete(dni) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "¡No podrás revertir esto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "No, cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';
            form.innerHTML = `<input type="hidden" name="modiDNI" value="${dni}">
                              <input type="hidden" name="eliminar" value="true">`;
            document.body.appendChild(form);
            form.submit();
        } else {
            Swal.fire("Operación cancelada", "", "error");
        }
    });
}
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>