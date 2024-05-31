<?php
session_start();
require_once "../modelos/Remitos.php";

$remitos = new Remitos();

header('Content-Type: application/json');

try {
    switch ($_GET["op"]) {
        case 'listarRemitos':
            $fechai = $_POST["fechai"];
            $fechaf = $_POST["fechaf"];
            
            if (empty($fechai) || empty($fechaf)) {
                throw new Exception('Fechas no proporcionadas');
            }

            $rspta = $remitos->listarRemitos($fechai, $fechaf);

            if (!$rspta) {
                throw new Exception('Error al obtener los remitos');
            }

            $data = array();

            while ($reg = $rspta->fetch_object()) {
                $data[] = array(
                    "id" => $reg->idremito,
                    "fecha" => $reg->fecha,
                    "cliente" => $reg->cliente_nombre,
                    "pventa" => $reg->pventa
                );
            }

            echo json_encode($data);
            break;

            case 'verDetalles':
                $id = $_POST["id"];
                
                if (empty($id)) {
                    throw new Exception('ID no proporcionado');
                }
            
                $rspta = $remitos->verDetalles($id);
            
                if (!$rspta) {
                    throw new Exception('Error al obtener los detalles');
                }
            
                $data = array();
            
                while ($reg = $rspta->fetch_object()) {
                    $data[] = array(
                        "idtipo_producto" => $reg->idtipo_producto,
                        "nombre_tipo_producto" => $reg->tipo_producto_nombre,
                        "capacidadSuma" => $reg->capacidadSuma,
                        "propiedad" => $reg->propiedad,
                        "tipoenvase" => $reg->tipoenvase,
                        "nserie" => $reg->nserie,
                        "accion" => $reg->accion,
                        "cantidad" => $reg->cantidad,
                        "totalNP" => $reg->totalNP,
                        "totalSP" => $reg->totalSP
                    );
                }
            
                echo json_encode($data);
                break;

                case 'verMasDetalles':
                    $id = $_POST["id"];
                    $idtipo_producto = $_POST["idtipo_producto"];
                    $propiedad = $_POST["propiedad"];
                    $accion = $_POST["accion"];
                    $tipoenvase = $_POST["tipoenvase"];
                    
                    if (empty($id)) {
                        throw new Exception('ID no proporcionado');
                    }
                
                    $rspta = $remitos->verMasDetalles($id,$idtipo_producto,$propiedad,$accion,$tipoenvase);
                
                    if (!$rspta) {
                        throw new Exception('Error al obtener los detalles');
                    }
                
                    $data = array();
                
                    while ($reg = $rspta->fetch_object()) {
                        $data[] = array(
                            "nserie" => $reg->nserie,
                            "capacidad" => $reg->capacidad,
                            "nombre_tipo_producto" => $reg->nombre_tipo_producto
                        );
                    }
                
                    echo json_encode($data);
                    break;
            
        
        default:
            throw new Exception('Operación no válida');
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
