<?php
require_once('../inc/conexion.php');
require_once('../vendor/autoload.php');

use \Firebase\JWT\JWT;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '../inc/.env');
$dotenv->safeLoad();
session_start();

function listarActividadesDB($conexion, $actividad)
{
    $sql = "SELECT da.id, u.nombre, u.apellidos, da.estado, da.similitud, da.url FROM detalleact da join usuarios u on da.alumno=u.id WHERE da.actividad = '$actividad'";
    $resultado = $conexion->query($sql);
    return $resultado;
}

function instruccionesDB($conexion, $actividad)
{
    $alumno = $_SESSION['idAlumno'];
    $sql = "SELECT da.id, a.titulo, a.fechai, a.fechaf, a.descripcion, da.estado FROM detalleact da join actividades a on da.actividad=a.id WHERE da.alumno = '$alumno' and a.id = '$actividad' AND a.estado=1 ORDER BY da.id ASC";
    $resultado = $conexion->query($sql);
    return $resultado;
}

function listarDetalleActDB($conexion, $actividad)
{
    $sql = "SELECT * from actividades WHERE id='$actividad' AND estado=1";
    $resultado = $conexion->query($sql);
    return $resultado;
}

function detalleActDB($conexion, $actividad){
    $sql = "SELECT * from detalleact WHERE actividad='$actividad' and alumno='".$_SESSION['idAlumno']."'";
    $resultado = $conexion->query($sql);
    return $resultado;
}

function listarActividades()
{
    if ($_POST['tk'] == $_SESSION['token']) {
?>
        <div class="py-4">
            <div class="height-100 pb-3 scroll accordion accordion-flush bg-white" id="accordionFlushExample">
                <div class="container pb-3">
                    <?php
                    for ($i = 0; $i < 20; $i++) {
                    ?>
                        <div class="accordion-item">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="<?php echo "#aact" . $i; ?>" aria-expanded="false" aria-controls="flush-collapseOne">
                                    Accordion Item #<?php echo $i; ?>
                                </button>
                            </div>
                            <div id="<?php echo "aact" . $i; ?>" class="accordion-collapse collapse w-100" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">

                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="text-muted">Publicado: 20/10/2024</span>
                                        <span class="text-success fw-bold">Asignado</span>
                                    </div>
                                    <div class="mt-4">
                                        <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Fuga at facilis minima nihil unde fugit laudantium quasi? Recusandae neque atque delectus vitae. Possimus alias aliquid aliquam tempore voluptas rerum facilis.</p>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="?f=instrucciones" type="button" class="btn btn-primary">Subir Actividad</a>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php
    } else {
        echo "no autorizado";
    }
}

function listarInstrucciones()
{
    if ($_POST['tk'] == $_SESSION['token']) {
        $conexion = conectar();
        $actividad = $_POST['id'];
        $dataActividades = instruccionesDB($conexion, $actividad);
        $dataActividades = $dataActividades->fetch_assoc();
        if (!$dataActividades) {
            $dataActividades = listarDetalleActDB($conexion, $actividad);
            $dataActividades = $dataActividades->fetch_assoc();
            if (!$dataActividades) {
                die("actividad no encontrada");
            }
            $template = '
            <div class="mt-2">
                <input type="text" class="form-control" id="link" placeholder="Ingrese el link del código">
            </div>
            <div class="mt-3">
                <button class="btn bg-azul btn-primary w-100 fw-bold" id="btn_entregar">Entregar actividad</button>
            </div>
        ';
        $estado = 'Asignado';
} else {
    $template = '
    <div class="mt-2">
        <span>Actividad entregada</span>
    </div>
    <div class="mt-3">
        <button class="btn btn-secondary w-100 fw-bold" id="btn_cancelar_entrega" disabled>Actividad entregada</button>
    </div>
';


        $revision = detalleActDB($conexion, $actividad);
        $revision = $revision->fetch_assoc();
        if($revision['similitud'] != null){
            $plagio = $revision['similitud'];
            $similitud = '
            <p>Simitud: '.$plagio.'%</p>
            ';
            $estado = 'Analizado';
        }else{
            $similitud = '';
            $estado = 'Entregado';
        }
        
        }
    ?>
        <div class="py-4">
            <div class="height-100 scroll px-2 bg-blanco pt-2">
                <div class="container">
                    <div class="row">
                        <div class="col-md-7 col-sm-12 col-lg-8">
                            <div class="d-flex align-items-center fw-bold fs-3">
                                <i class='bx bx-book-alt bg-azul p-2 circulo text-light me-2'></i>
                                <h3><?php echo $dataActividades['titulo']; ?></h3>
                            </div>
                            <div class="mt-2">
                                <span>Tiempo: <?php echo $dataActividades['fechai'] . " hasta " . $dataActividades['fechaf']; ?></span>
                                <hr>
                                <p><?php echo $dataActividades['descripcion']; ?></p>
                                <hr>
                                <?php echo $similitud; ?>
                            </div>
                        </div>

                        <div class="col-md-5 col-sm-12 col-lg-4">
                            <div class="tarjeta px-3 py-3 bg-blanco redondear">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h3>Tu trabajo</h3>
                                    <span class="text-success fw-bold"><?php echo $estado; ?></span>
                                </div>
                                <?php echo $template; ?>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    <?php
    } else {
        echo "no autorizado";
    }
}

function revisarActividades()
{
    if ($_POST['tk'] == $_SESSION['token']) {
        $conexion = conectar();
        $actividad = $_POST['id'];

        $dataActividades = listarActividadesDB($conexion, $actividad);
    ?>
        <div class="py-3">
            <div class="height-100 pb-3 bg-blanco pt-2">
                <div class="container h-100">
                    <div class="table-responsive h-85 scroll">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">Alumnos</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Similitud</th>
                                    <th scope="col">Link</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php while ($actividades = $dataActividades->fetch_assoc()) {
                                    //extraer del nombre solo el primer nombre
                                    $nombre = explode(" ", $actividades['nombre']);
                                    $nombre = $nombre[0];
                                    //extraer del apellido solo el primer apellido
                                    $apellido = explode(" ", $actividades['apellidos']);
                                    $apellido = $apellido[0];
                                    $nombreCompleto = $nombre . " " . $apellido;
                                    //poner mayuscula el primer caracter del nombre
                                    $nombreCompleto = ucfirst($nombreCompleto);
                                    if ($actividades['estado'] == 1) {
                                        $estado = "Entregado";
                                    } else {
                                        $estado = "No entregado";
                                    }
                                    if ($actividades['similitud'] == null) {
                                        $similitud = "NI";
                                    } else {
                                        $similitud = $actividades['similitud'];
                                    }
                                ?>
                                    <tr>
                                        <th scope="row"><?php echo $nombreCompleto; ?></th>
                                        <td><?php echo $estado; ?></td>
                                        <td><?php echo $similitud; ?>%</td>
                                        <td><a href="<?php echo $actividades['url']; ?>" type="button" target="_blank" class="text-azul fw-bold">Ver codigo</a></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex gap-3">
                        <div class="">
                        <button id="btn_verificar" class="btn btn-success fw-bold">Analizar</button>
                        </div>
                        <div class="">
                        <select class="form-select" name="comboAlgoritmos" id="comboAlgoritmos">
                            <option value="0">Seleccionar Algoritmo</option>
                            <option value="1">Variables</option>
                            <option value="2">Levenshtein</option>
                            <option value="3">Suffix</option>
                        </select>
                        </div>
                    </div>
                </div>

            </div>
        </div>
<?php
    } else {
        echo "no autorizado";
    }
}

if (function_exists($_GET['f'])) {
    $_GET['f'](); //llama la función si es que existe
}
?>