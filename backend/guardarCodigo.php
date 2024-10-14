<?php
session_start();
// URL del servidor Node.js
$apiUrl = 'http://localhost:3000/extract-code';

// URL que deseas extraer
//$url = 'https://www.online-java.com/Sn6VaZ80Yv';
//$url = 'https://www.online-java.com/eX7GMClJRs';

function validarURL($url)
{
    $patron = "/^https:\/\/www\.online-java\.com\/[a-zA-Z0-9]+$/";

    if (preg_match($patron, $url)) {
        return true; // La URL es válida
    } else {
        return false; // La URL no es válida
    }
}

$url = $_POST['url'];

if (validarURL($url)) {

    // Configura la solicitud POST
    $data = json_encode(['url' => $url]);
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => $data,
        ],
    ];
    $context  = stream_context_create($options);

    // Envía la solicitud a la API
    $response = file_get_contents($apiUrl, false, $context);

    // Maneja la respuesta
    if ($response === FALSE) {
        die('Error en la solicitud.');
    }

    $responseData = json_decode($response, true);

    // Muestra el código extraído o un mensaje de error
    if (isset($responseData['content'])) {
        //echo "<h2>Código extraído:</h2>";
        //$codeContent = "<pre>" . htmlspecialchars($responseData['code']) . "</pre>";
        $codeContent = htmlspecialchars($responseData['content']);

        // Ruta de la carpeta y nombre del archivo
        $folderPath = '../server/codigos';
        $idAlumno = $_SESSION['idAlumno'];
        //generar codigo de 6 digitos unico
        $codigo = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
        $idActividad = $_POST['id'];
        //traer la fecha y hora junto sin espacios
        date_default_timezone_set('America/Lima');
        $fecha = date("YmdHis");
        $fileName = $idAlumno . $codigo . $idActividad . $fecha . ".txt";

        /* Verificar si la carpeta existe, si no, crearla
    if (!is_dir($folderPath)) {
        //mkdir($folderPath, 0777, true); // Crear la carpeta con permisos
        die("Error no se encontro la carpeta");
    }
    */

        // Ruta completa del archivo
        $file = $folderPath . '/' . $fileName;

        // Guardar el contenido en el archivo
        file_put_contents($file, $codeContent);

        // Mensaje de confirmación
        $respuesta['status'] = 'success';
        $respuesta['message'] = 'Código guardado correctamente';
        $respuesta['ruta'] = $file;
    } else {
        $respuesta['status'] = 'error';
        $respuesta['message'] = htmlspecialchars($responseData['error'] ?? 'Desconocido');
        $respuesta['ruta'] = '';
    }
} else {
    $respuesta['status'] = 'error'; //si la url no es válida, devolver error osea que el dom
    $respuesta['message'] = 'URL no válida';
    $respuesta['ruta'] = '';
}
// Devolver la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($respuesta);
