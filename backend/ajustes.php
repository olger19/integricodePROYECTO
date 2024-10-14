<?php
require_once('../inc/conexion.php');
require_once('../vendor/autoload.php');

use \Firebase\JWT\JWT;
use \Firebase\JWT\key;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '../inc/.env');
$dotenv->safeLoad();
session_start();


function listarAjustes()
{
    if ($_POST['tk'] == $_SESSION['token']) {
?>
        <div class="py-4">
            <div class="height-100 scroll bg-white pt-2">
                <div class="container">
                    <div class="archivo" id="archivo">
                        <img src="../../assets/images/sf.jpg" id="perfil-edit" alt="">
                        <div class="file-select" id="src-file1">
                            <input id="upload" type="file" id="file" name="src-file1" accept="image/*" aria-label="Archivo">
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

function actualizarFoto()
{
    if ($_POST['tk'] == $_SESSION['token']) {

        $key = $_ENV['KEY'];
        if (isset($_COOKIE['sesion'])) {
            $decode = JWT::decode($_COOKIE['sesion'], new key($key, 'HS256'));
        } else {
            //redireccionamos al login
            header("Location: ../");
        }

        $id = $decode->data->id;
        $nombreEx = explode(" ", $decode->data->nombre);
        $nombreEx = $nombreEx[0];
        extract($_POST);
        $dir = "../server/usuarios/";
        if (!is_dir($dir))
            mkdir($dir);
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $img = base64_decode($img);
        $save = file_put_contents($dir . $id . $nombreEx . ".png", $img);
        if ($save) {
            $resp['status'] = 'success';
        } else {
            $resp['status'] = 'failed';
        }
        echo json_encode($resp);

    } else {
        echo "no autorizado";
    }
}

function actualizarFotoCurso()
{
    if ($_POST['tk'] == $_SESSION['token']) {

        $key = $_ENV['KEY'];
        if (isset($_COOKIE['sesion'])) {
            $decode = JWT::decode($_COOKIE['sesion'], new key($key, 'HS256'));
        } else {
            //redireccionamos al login
            header("Location: ../");
        }

        $id = $decode->data->id;
        $codigo = $_POST['cod'];
        extract($_POST);
        $dir = "../server/cursos/";
        if (!is_dir($dir))
            mkdir($dir);
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $img = base64_decode($img);
        $save = file_put_contents($dir . $id . $codigo . ".png", $img);
        if ($save) {
            $resp['status'] = 'success';
        } else {
            $resp['status'] = 'failed';
        }
        echo json_encode($resp);

    } else {
        echo "no autorizado";
    }
}


if (function_exists($_GET['f'])) {
    $_GET['f'](); //llama la función si es que existe
}
