<?php
include("../conexion.php");




//-----------------------------------------------------------------PAGINADO
$limite = 8;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $limite;

//----------------------------------------------------------------ORDEN TABLA
$orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'nombre';
$status = isset($_GET['status']) ? $_GET['status'] : '0';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$validColumns = ['nombre', 'apellido', 'fecha_inscripcion'];
if (!in_array($orderBy, $validColumns)) {
    $orderBy = 'nombre';
}

$validStatus = ['0', '1']; // Puedes adaptar según tus necesidades
if (!in_array($status, $validStatus)) {
    $status = '0';
}

//-----------------------------------------------------------------CONSULTA PRINCIPAL
$query = "SELECT cliente.dni, cliente.nombre, cliente.apellido, cliente.fecha_inscripcion, cliente.correo, cliente.telefono, cliente.estado
          FROM cliente
          WHERE cliente.estado = $status";

// Agregar búsqueda si se introduce una búsqueda
if (!empty($search)) {
    $query .= " AND (cliente.nombre LIKE '%$search%' OR cliente.apellido LIKE '%$search%')";
}

$query .= " ORDER BY $orderBy LIMIT $limite OFFSET $offset";
$result = mysqli_query($conex, $query) or die("ERROR AL OBTENER CLIENTES");

// Obtener el número total de registros
$queryTotal = "SELECT COUNT(*) AS total FROM cliente WHERE estado = $status";
if (!empty($search)) {
    $queryTotal .= " AND (nombre LIKE '%$search%' OR apellido LIKE '%$search%')";
}
$resultTotal = mysqli_query($conex, $queryTotal) or die("ERROR DE CONTEO");
$rowTotal = mysqli_fetch_assoc($resultTotal);
$total_records = $rowTotal['total'];
$total_paginas = ceil($total_records / $limite);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header>
        <h1 class='text-center-titulo'>Lista de Clientes</h1><br>
        <a class="btn-accion" href="http://localhost/Sportclub/Clientes/Form-Alta-Clientes.php">
            <img src="../SVG/Agregar.svg" alt="Agregar" class="icono" width="24px">
        </a>
    </header>
    <!-- Filtros y búsqueda -->
    <div class='container'>
        <div class='row mb-3'>
            <div class='col-md-3'>
                <label for='orderSelect'>Ordenar por:</label>
                <select class='form-select' id='orderSelect' onchange='changeFilter()'>
                    <option value='nombre' <?php echo $orderBy == 'nombre' ? 'selected' : ''; ?>>Nombre</option>
                    <option value='apellido' <?php echo $orderBy == 'apellido' ? 'selected' : ''; ?>>Apellido</option>
                    <option value='fecha_inscripcion' <?php echo $orderBy == 'fecha_inscripcion' ? 'selected' : ''; ?>>Fecha de Inscripción</option>
                </select>
            </div>
            <div class='col-md-3'>
                <label for='statusSelect'>Estado:</label>
                <select class='form-select' id='statusSelect' onchange='changeFilter()'>
                    <option value='0' <?php echo $status == '0' ? 'selected' : ''; ?>>ACTIVOS</option>
                    <option value='1' <?php echo $status == '1' ? 'selected' : ''; ?>>INACTIVOS</option>
                </select>
            </div>
            <div class='col-md-3'>
                <label for="searchInput"></label>
                <div class="input-group">
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar cliente..." value="<?php echo $search; ?>" onkeypress="handleSearchKeypress(event)">
                    <button class="input-group-text" onclick="changeFilter()">Buscar</button>
                </div>
            </div>
        </div>

        <!-- Tabla de clientes -->
        <table class='table table-striped'>
    <thead>
        <tr>
            <th>DNI</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Fecha de Inscripción</th>
            <th>Estado</th>
            <th>Acciones</th> <!-- Nueva columna -->
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_array($result)) { ?>
            <tr>
                <td><?php echo $row['dni']; ?></td>
                <td><?php echo $row['nombre']; ?></td>
                <td><?php echo $row['apellido']; ?></td>
                <td><?php echo $row['correo']; ?></td>
                <td><?php echo $row['telefono']; ?></td>
                <td><?php echo date('d-m-Y', strtotime($row['fecha_inscripcion'])); ?></td>
                <td><?php echo $row['estado'] == 0 ? 'Activo' : 'Inactivo'; ?></td>
                <td class="acciones"> <!-- Botones de acciones -->
                    <a class="btn-accion" href="Form-Modi-Clientes.php?cliente=<?php echo $row['dni']; ?>">
                        <img src="../SVG/Perfil.svg" alt="Modificar" class="icono" width="24px">
                    </a>
                    <form method="POST" action="Eliminar-Clientes.php" style="display:inline;">
                        <input type="hidden" name="DNI" value="<?php echo $row['dni']; ?>">
                        <button type="submit" class="btn-accion">
                            <img src="../SVG/Eliminar.svg" alt="Eliminar" class="icono">
                        </button>
                    </form>
                    <a class="btn-accion" href="pago_cliente.php?cliente=<?php echo $row['dni']; ?>">
                        <img src="../SVG/cuota.svg" alt="Boletín" class="icono" width="24px">
                    </a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>


        <!-- Paginación -->
        <nav>
            <ul class='pagination'>
                <?php for ($i = 1; $i <= $total_paginas; $i++) { ?>
                    <li class='page-item <?php if ($i == $pagina_actual) echo 'active'; ?>'>
                        <a class='page-link' href='?pagina=<?php echo $i; ?>&orderBy=<?php echo $orderBy; ?>&status=<?php echo $status; ?>&search=<?php echo $search; ?>'>
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </nav>
    </div>

    <script>
        function changeFilter() {
            var orderBy = document.getElementById('orderSelect').value;
            var status = document.getElementById('statusSelect').value;
            var search = document.getElementById('searchInput').value;
            window.location.href = '?orderBy=' + orderBy + '&status=' + status + '&search=' + search;
        }
        
        function handleSearchKeypress(event) {
            if (event.key === 'Enter') {
                changeFilter();
            }
        }
    </script>
</body>
</html>
