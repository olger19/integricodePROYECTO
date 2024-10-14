<?php
require_once('../inc/conexion.php');
require_once('../vendor/autoload.php');

use \Firebase\JWT\JWT;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '../inc/.env');
$dotenv->safeLoad();
session_start();

function detalleCurso($conexion, $idcurso)
{
    $sql = "SELECT c.id, c.nombre, c.aula, c.usuario, c.cod, u.nombre AS 'profesor' FROM cursos c join usuarios u ON c.usuario=u.id WHERE c.id = '$idcurso' AND c.estado = 1 ORDER BY c.id ASC";
    $resultado = $conexion->query($sql);
    return $resultado;
}

function actividadesDB($conexion, $idcurso)
{
    $sql = "SELECT * FROM actividades WHERE curso = '$idcurso' AND estado = 1 ORDER BY id ASC";
    $resultado = $conexion->query($sql);
    return $resultado;
}



function listarCursos()
{
    if ($_POST['tk'] == $_SESSION['token']) {
        $conexion = conectar();
        $usuario = $_SESSION['idAlumno'];
        $sql = "SELECT c.id, c.usuario, c.cod, u.nombre, c.nombre as 'curso' FROM detallecurso dc JOIN cursos c on dc.curso=c.id join usuarios u on c.usuario=u.id WHERE dc.alumno='$usuario' AND c.estado=1 order by c.id ASC";
        $resultado = $conexion->query($sql);
?>
        <div class="height-100 scroll py-3">
            <div class="cursos">
                <?php
                while ($fila = $resultado->fetch_assoc()) {
                    $imagenprofesor = "server/usuarios/" . $fila['usuario'] . $fila['nombre'] . ".png";
                    if (!file_exists("../" . $imagenprofesor)) {
                        $imagenprofesor = "assets/images/sf.jpg";
                    }
                    $imagencurso = "server/cursos/" . $fila['usuario'] . $fila['cod'] . ".png";
                    if (!file_exists("../" . $imagencurso)) {
                        $imagencurso = "assets/images/curso.jpg";
                    }
                    $link = "curso=" . $fila['id'];
                ?>
                    <div class="w-100 tarjeta redondear">
                        <div class="altura">
                            <img src="../../<?php echo $imagencurso; ?>" alt="logo">
                        </div>
                        <div class="foto-docente">
                            <img src="../../<?php echo $imagenprofesor; ?>" alt="logo">
                        </div>
                        <div class="linea-lateral"></div>
                        <a href="?f=detalle&<?php echo $link; ?>" type="button" class="p-3 mt-4 d-flex align-items-center text-dark flex-row">
                            <div class="me-3">
                                <img class="carpeta" src="../../assets/images/carpeta2.png" alt="logo">
                            </div>
                            <div class="lh-1">
                                <span class="fw-bold"><?php echo $fila['curso']; ?></span>
                            </div>
                        </a>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    <?php
    } else {
        echo "no autorizado";
    }
}

function listarCursosProfesor()
{
    if ($_POST['tk'] == $_SESSION['token']) {
        $conexion = conectar();
        $usuario = $_SESSION['idProfesor'];
        $sql = "SELECT c.id, c.nombre, c.aula, c.usuario, c.cod, u.nombre AS 'profesor' FROM cursos c join usuarios u ON c.usuario=u.id WHERE c.usuario = '$usuario' AND c.estado = 1 ORDER BY c.id ASC";
        $resultado = $conexion->query($sql);
    ?>
        <div class="height-100 scroll py-3">
            <div class="cursos">
                <?php
                while ($fila = $resultado->fetch_assoc()) {
                    $imagenprofesor = "server/usuarios/" . $fila['usuario'] . $fila['profesor'] . ".png";
                    if (!file_exists("../" . $imagenprofesor)) {
                        $imagenprofesor = "assets/images/sf.jpg";
                    }
                    $imagencurso = "server/cursos/" . $fila['usuario'] . $fila['cod'] . ".png";
                    if (!file_exists("../" . $imagencurso)) {
                        $imagencurso = "assets/images/curso.jpg";
                    }
                    $link = "curso=" . $fila['id'] . "&cod=" . $fila['cod'];
                    $editar = $fila['id']."||".
                    $fila['nombre']."||".
                    $fila['aula'];
                ?>
                    <div class="w-100 tarjeta redondear">
                        <div class="altura">
                            <img src="../../<?php echo $imagencurso; ?>" alt="curso">
                        </div>
                        <div class="foto-docente">
                            <img src="../../<?php echo $imagenprofesor; ?>" alt="img_profesor">
                        </div>
                        <div class="linea-lateral"></div>
                        <div class="p-3 mt-4 d-flex align-items-center text-dark flex-row">
                            <a href="?f=detalle&<?php echo $link; ?>" type="button" class="d-flex align-items-center text-dark flex-row">
                                <div class="me-3">
                                    <img class="carpeta" src="../../assets/images/carpeta2.png" alt="logo">
                                </div>
                                <div class="lh-1">
                                    <span class="fw-bold"><?php echo $fila['nombre']; ?></span>
                                </div>
                            </a>
                            <div class="ms-auto">
                                <div class="dropdown">
                                    <div role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class='bx bx-dots-vertical-rounded fs-4'></i>
                                    </div>

                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                        <li><span onclick="editarCurso('<?php echo $editar; ?>')" class="dropdown-item" role="button">Editar</span></li>
                                        <li><span onclick="eliminarCurso('<?php echo $fila['id']; ?>')" class="dropdown-item" role="button">Eliminar</span></li>
                                    </ul>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    <?php
    } else {
        echo "no autorizado";
    }
}

function listarDetalleCursos()
{
    if ($_POST['tk'] == $_SESSION['token']) {
        $conexion = conectar();
        $curso = $_POST['curso'];

        $dataActividades = actividadesDB($conexion, $curso);

        $dataCurso = detalleCurso($conexion, $curso);
        $fila = $dataCurso->fetch_assoc();
        //validar que exista el curso
        if (!$fila) {
            die("curso no encontrado");
        }
        $imagenprofesor = "server/usuarios/" . $fila['usuario'] . $fila['profesor'] . ".png";
        if (!file_exists("../" . $imagenprofesor)) {
            $imagenprofesor = "assets/images/sf.jpg";
        }
        $imagencurso = "server/cursos/" . $fila['usuario'] . $fila['cod'] . ".png";
        if (!file_exists("../" . $imagencurso)) {
            $imagencurso = "assets/images/curso.jpg";
        }
    ?>
        <div class="py-4">
            <div class="tabs">

                <input type="radio" id="tab1" name="tab-control" checked>
                <input type="radio" id="tab2" name="tab-control">
                <input type="radio" id="tab3" name="tab-control">
                <ul>
                    <li title="Curso">
                        <label for="tab1" role="button">
                            <i class='bx bxs-graduation'></i><br>
                            <span>Curso</span>
                        </label>
                    </li>
                    <li title="Actividades">
                        <label for="tab2" role="button">
                            <i class='bx bx-book-alt'></i><br>
                            <span>Actividades</span>
                        </label>
                    </li>
                </ul>

                <div class="slider">
                    <div class="indicator"></div>
                </div>
                <div class="content">
                    <section>
                        <h2>Curso</h2>
                        <div class="container">
                            <div class="row">
                                <div class="col-md-3 col-sm-12 col-lg-2 mb-3">
                                </div>

                                <div class="col-md-9 col-sm-12 col-lg-10">
                                    <div class="card redondear text-white mb-3">
                                        <img src="../../<?php echo $imagencurso; ?>" class="card-img-top banner-curso redondear" alt="curso">
                                        <div class="card-img-overlay bg-fondo redondear">
                                            <div class="text-bottom d-flex align-items-end h-100">
                                                <div>
                                                    <h4 class="card-title fw-bold"><?php echo $fila['nombre']; ?></h4>
                                                    <p class="card-text"><?php echo $fila['aula']; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </section>
                    <section>
                        <h2>Actividades</h2>
                        <div class="accordion accordion-flush" id="accordionFlushExample">
                            <?php while ($actividades = $dataActividades->fetch_assoc()) { ?>
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="<?php echo "#act" . $actividades['id']; ?>" aria-expanded="false" aria-controls="flush-collapseOne">
                                            <?php echo $actividades['titulo']; ?>
                                        </button>
                                    </div>
                                    <div id="<?php echo "act" . $actividades['id']; ?>" class="accordion-collapse collapse w-100" data-bs-parent="#accordionFlushExample">
                                        <div class="accordion-body">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="text-muted">Publicado: <?php echo $actividades['fechai']; ?></span>
                                                <span class="text-success fw-bold">Asignado</span>
                                            </div>
                                            <div class="mt-4">
                                                <p><?php echo $actividades['descripcion']; ?></p>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <a href="?f=instrucciones&id=<?php echo $actividades['id']; ?>" type="button" class="btn btn-primary">Subir Actividad</a>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </section>
                    <section>
                            </div>

                        </div>
                    </section>
                </div>
            </div>
        </div>
    <?php
    } else {
        echo "no autorizado";
    }
}

function listarDetalleCursoProfesor()
{
    if ($_POST['tk'] == $_SESSION['token']) {
        $conexion = conectar();
        $curso = $_POST['curso'];

        $dataActividades = actividadesDB($conexion, $curso);

        $dataCurso = detalleCurso($conexion, $curso);
        $fila = $dataCurso->fetch_assoc();
        //validar que exista el curso
        if (!$fila) {
            die("curso no encontrado");
        }
        $imagenprofesor = "server/usuarios/" . $fila['usuario'] . $fila['profesor'] . ".png";
        if (!file_exists("../" . $imagenprofesor)) {
            $imagenprofesor = "assets/images/sf.jpg";
        }
        $imagencurso = "server/cursos/" . $fila['usuario'] . $fila['cod'] . ".png";
        if (!file_exists("../" . $imagencurso)) {
            $imagencurso = "assets/images/curso.jpg";
        }
    ?>
        <div class="py-4">
            <div class="tabs">

                <input type="radio" id="tab1" name="tab-control" checked>
                <input type="radio" id="tab2" name="tab-control">
                <input type="radio" id="tab3" name="tab-control">
                <ul>
                    <li title="Curso">
                        <label for="tab1" role="button">
                            <i class='bx bxs-graduation'></i><br>
                            <span>Curso</span>
                        </label>
                    </li>
                    <li title="Actividades">
                        <label for="tab2" role="button">
                            <i class='bx bx-book-alt'></i><br>
                            <span>Actividades</span>
                        </label>
                    </li>
                </ul>

                <div class="slider">
                    <div class="indicator"></div>
                </div>
                <div class="content">
                    <section>
                        <h2>Curso</h2>
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-2 col-md-3 col-sm-12 mb-3">
                                    <div class="card">
                                        <div class="card-body fw-bold">
                                            <span>Código de curso</span>
                                            <div class="mt-3">
                                                <span class="text-azul arial"><?php echo $fila['cod']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-10 col-md-9 col-sm-12">
                                    <div class="card redondear text-white mb-3">
                                        <img id="cursoImagen" src="../../<?php echo $imagencurso; ?>" class="card-img-top banner-curso redondear" alt="curso">
                                        <div class="card-img-overlay bg-fondo redondear">
                                            <div class="">
                                                <div class="text-end">
                                                    <input type="file" id="fotoCurso" name="fotoCurso" accept="image/*" class="d-none">
                                                    <label for="fotoCurso" class="btn btn-light fw-bold">Imagen</label>
                                                </div>
                                            </div>
                                            <div class="text-bottom d-flex align-items-end h-75">
                                                <div>
                                                    <h4 class="card-title fw-bold"><?php echo $fila['nombre']; ?></h4>
                                                    <p class="card-text"><?php echo $fila['aula']; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </section>
                    <section>
                        <h2>Actividades</h2>
                        <div class="pt-2 pb-2">
                            <button class="btn bg-azul btn-primary fw-bold redondear" data-bs-toggle="modal" data-bs-target="#addActividad">+ Crear</button>
                            <hr>
                        </div>
                        <div class="accordion accordion-flush" id="accordionFlushExample">
                            <?php while ($actividades = $dataActividades->fetch_assoc()) { 
                                $editar = $actividades['id']."||".
                                $actividades['titulo']."||".
                                $actividades['descripcion']."||".
                                $actividades['fechaf'];
                                ?>
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="<?php echo "#act" . $actividades['id']; ?>" aria-expanded="false" aria-controls="flush-collapseOne">
                                            <?php echo $actividades['titulo']; ?>
                                        </button>
                                    </div>
                                    <div id="<?php echo "act" . $actividades['id']; ?>" class="accordion-collapse collapse w-100" data-bs-parent="#accordionFlushExample">
                                        <div class="accordion-body">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="text-muted">Publicado: <?php echo $actividades['fechai']; ?></span>
                                                <span class="text-success fw-bold">Asignado</span>
                                            </div>
                                            <div class="mt-4">
                                                <p><?php echo $actividades['descripcion']; ?></p>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="d-flex align-items-center text-dark flex-row gap-2">
                                                <div>
                                                <a href="?f=revisar&id=<?php echo $actividades['id']; ?>" type="button" class="btn btn-primary">Revisar actividad</a>
                                                </div>
                                                <div>
                                                    <button onclick="eliminarActividad('<?php echo $actividades['id']; ?>')" class="btn btn-danger"><i class='bx bx-trash-alt' ></i></button>
                                                </div>
                                                <div>
                                                    <button onclick="editarActividad('<?php echo $editar; ?>')" class="btn btn-warning"><i class='bx bx-edit-alt' ></i></button>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </section>
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