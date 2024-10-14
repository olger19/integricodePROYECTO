<?php
require_once('../inc/conexion.php');
session_start();

set_time_limit(300); // 5 minutos
ini_set('memory_limit', '256M'); // Aumentar límite de memoria

$conexion = conectar();

$idActividad = $_POST['id'];

// Consultar todas las rutas de archivos de texto asociadas a la actividad
$sql = "SELECT da.id, da.rutatxt FROM detalleact da JOIN usuarios u ON da.alumno = u.id WHERE da.actividad = '$idActividad'";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    $archivos = [];
    while ($row = $resultado->fetch_assoc()) {
        $archivos[] = [
            'id' => $row['id'],
            'ruta' => $row['rutatxt']
        ];
    }

    class AhoCorasick {
        private $trie;
        private $output;
        private $fail;

        public function __construct() {
            $this->trie = [];
            $this->output = [];
            $this->fail = [];
        }

        public function insert($pattern) {
            $node = &$this->trie;
            foreach (str_split($pattern) as $char) {
                if (!isset($node[$char])) {
                    $node[$char] = [];
                }
                $node = &$node[$char];
            }
            $this->output[] = $pattern;
            $node['output'] = isset($node['output']) ? array_merge($node['output'], [$pattern]) : [$pattern];
        }

        public function build() {
            $queue = [];
            foreach ($this->trie as $char => $node) {
                $this->fail[$char] = 0;
                $queue[] = $char;
            }

            while ($queue) {
                $currentChar = array_shift($queue);
                $currentNode = &$this->trie[$currentChar];

                foreach ($currentNode as $char => $childNode) {
                    $queue[] = $char;
                    $fail = $this->fail[$currentChar];

                    while ($fail !== 0 && !isset($this->trie[$fail][$char])) {
                        $fail = $this->fail[$fail];
                    }

                    $this->fail[$char] = isset($this->trie[$fail][$char]) ? $fail : 0;

                    if (isset($this->trie[$this->fail[$char]]['output'])) {
                        $currentNode['output'] = array_merge(
                            isset($currentNode['output']) ? $currentNode['output'] : [],
                            $this->trie[$this->fail[$char]]['output']
                        );
                    }
                }
            }
        }

        public function search($text) {
            $node = &$this->trie;
            $matches = [];

            foreach (str_split($text) as $char) {
                while ($node !== null && !isset($node[$char])) {
                    $node = $node['fail'] ?? null;
                }
                if ($node === null) {
                    $node = &$this->trie;
                    continue;
                }
                $node = &$node[$char];
                if (isset($node['output'])) {
                    foreach ($node['output'] as $pattern) {
                        $matches[] = $pattern;
                    }
                }
            }

            return array_unique($matches);
        }
    }

    function calcularPlagioAhoCorasick($archivo1, $archivo2) {
        $ahoCorasick = new AhoCorasick();
        $patrones = explode("\n", $archivo1);
        
        foreach ($patrones as $patron) {
            if (!empty(trim($patron))) {
                $ahoCorasick->insert(trim($patron));
            }
        }

        $ahoCorasick->build();
        return $ahoCorasick->search($archivo2);
    }

    function actualizarSimilitud($id, $similitud, $conexion) {
        $sql = "UPDATE detalleact SET similitud = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('si', $similitud, $id);
        $stmt->execute();
        $stmt->close();
    }

    $totalArchivos = count($archivos);
    $respuesta = [];

    foreach ($archivos as $i => $archivoActual) {
        try {
            $codigoBase = @file_get_contents($archivoActual['ruta']);
            if ($codigoBase === false) {
                $respuesta[] = ["status" => "error", "id" => $archivoActual['id'], "message" => "Error al leer el archivo: " . $archivoActual['ruta']];
                continue; // Saltar a la siguiente iteración
            }

            $similitudTotal = 0;
            $comparaciones = 0;

            foreach ($archivos as $j => $archivoComparar) {
                if ($i !== $j) {
                    $codigoComparar = @file_get_contents($archivoComparar['ruta']);
                    if ($codigoComparar === false) {
                        $respuesta[] = ["status" => "error", "id" => $archivoComparar['id'], "message" => "Error al leer el archivo: " . $archivoComparar['ruta']];
                        continue;
                    }

                    // Calcular la similitud
                    $matches = calcularPlagioAhoCorasick($codigoBase, $codigoComparar);
                    $similitud = count($matches);
                    $similitudTotal += $similitud;
                    $comparaciones++;
                }
            }

            $promedioSimilitud = $comparaciones > 0 ? $similitudTotal / $comparaciones : 0;
            actualizarSimilitud($archivoActual['id'], $promedioSimilitud, $conexion);
        } catch (Exception $e) {
            error_log("Error procesando archivo: " . $e->getMessage());
        }
    }

    $respuesta = ["status" => "success", "message" => "Todos los archivos han sido procesados correctamente"];
} else {
    $respuesta = ["status" => "error", "message" => "No se encontraron registros para la actividad con id: $idActividad"];
}

// Devolver la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($respuesta);
$conexion->close();
?>
