<?php
require_once('../inc/conexion.php');
require_once('../vendor/autoload.php');

use \Firebase\JWT\JWT;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '../inc/.env');
$dotenv->safeLoad();
session_start();

function createActividad()
{
    if ($_POST['tk'] == $_SESSION['token']) {
        if ($_POST['titulo'] != "" && $_POST['descripcion'] != "" && $_POST['fechaf'] != "" && $_POST['curso'] != "" && isset($_SESSION['idProfesor'])) {

            $titulo = $_POST['titulo'];
            $desc = $_POST['descripcion'];
            $fechaf = $_POST['fechaf'];
            $curso = $_POST['curso'];
            $idProfesor = $_SESSION['idProfesor'];

            //obtener fecha establecer lima
            date_default_timezone_set('America/Lima');
            $fechai = date('Y-m-d'); // Cambiado a formato de fecha de MySQL
            
            $conexion = conectar();

            $sql = "INSERT INTO actividades(titulo, descripcion, fechaf, fechai, curso, estado) VALUES (?,?,?,?,?,1)";
            $stmt = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($stmt, "ssssi", $titulo, $desc, $fechaf, $fechai, $curso);
            $resultado = mysqli_stmt_execute($stmt);

            if ($resultado) {
                $respuesta['status'] = 'success';
                $respuesta['message'] = 'Actividad creada correctamente';
            } else {
                $respuesta['status'] = 'error';
                $respuesta['message'] = 'Error al ejecutar la consulta';
            }
            //cerrar la declaración preparada
            mysqli_stmt_close($stmt);

            // Cerrar la conexión a la base de datos
            mysqli_close($conexion);
        } else {
            $respuesta['status'] = 'error';
            $respuesta['message'] = 'Todos los campos son obligatorios';
        }
    } else {
        $respuesta['status'] = 'error';
        $respuesta['message'] = 'Token no autorizado';
    }
    // Devolver la respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode($respuesta);
}

function entregarActividad(){
    if ($_POST['tk'] == $_SESSION['token']) {
        if ($_POST['id'] != "" && $_POST['link'] != "") {
            $id = $_POST['id'];
            $link = $_POST['link'];
            $alumno = $_SESSION['idAlumno'];

            $rutatxt = $_POST['ruta'];

            $conexion = conectar();

            $sql = "INSERT INTO detalleact(alumno, url, actividad, rutatxt, estado) VALUES ('$alumno', '$link', '$id', '$rutatxt', '1')";

            $conexion->query($sql);
            $respuesta['status'] = 'success';
            $respuesta['message'] = 'Actividad entregada correctamente';
        } else {
            $respuesta['status'] = 'error';
            $respuesta['message'] = 'Todos los campos son obligatorios';
        }
    } else {
        $respuesta['status'] = 'error';
        $respuesta['message'] = 'Token no autorizado';
    }
    // Devolver la respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode($respuesta);
}

function eliminarActividad(){
    if ($_POST['tk'] == $_SESSION['token']){
        if($_POST['id'] != ""){
            $id = $_POST['id'];
            $conexion = conectar();
            //actualizar estado a 0
            $sql = "UPDATE actividades SET estado = 0 WHERE id = ?";
            $stmt = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            // Cerrar la conexión a la base de datos
            mysqli_close($conexion);
            $respuesta['status'] = 'success';
            $respuesta['message'] = 'Actividad eliminada correctamente';
        }else{
            $respuesta['status'] = 'error';
            $respuesta['message'] = 'No se envio el id';
        }
    }else{
        $respuesta['status'] = 'error';
        $respuesta['message'] = 'Token no autorizado';
    }
    // Devolver la respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode($respuesta);
}

function editarActividad(){
    if ($_POST['tk'] == $_SESSION['token']){
        if($_POST['id'] != "" && $_POST['titulo'] != "" && $_POST['descripcion'] != "" && $_POST['fechaf'] != ""){
            $id = $_POST['id'];
            $titulo = $_POST['titulo'];
            $descripcion = $_POST['descripcion'];
            $fechaf = $_POST['fechaf'];
            $conexion = conectar();
            //actualizar estado a 0
            $sql = "UPDATE actividades SET titulo = ?, descripcion = ?, fechaf = ? WHERE id = ?";
            $stmt = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($stmt, "sssi", $titulo, $descripcion, $fechaf, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            // Cerrar la conexión a la base de datos
            mysqli_close($conexion);
            $respuesta['status'] = 'success';
            $respuesta['message'] = 'Actividad actualizada correctamente';
        }else{
            $respuesta['status'] = 'error';
            $respuesta['message'] = 'No se envio el id';
        }
    }else{
        $respuesta['status'] = 'error';
        $respuesta['message'] = 'Token no autorizado';
    }
    // Devolver la respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode($respuesta);
}

if (function_exists($_GET['f'])) {
    $_GET['f'](); //llama la función si es que existe
}
