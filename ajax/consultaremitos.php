<?php
session_start();
require_once "../modelos/Remitos.php";

$remitos = new Remitos();

header('Content-Type: application/json');

try {
    switch ($_GET["op"]) {
        case 'listarRemitos':
            $fechai = isset($_POST["fechai"]) ? $_POST["fechai"] : null;
            $fechaf = isset($_POST["fechaf"]) ? $_POST["fechaf"] : null;
            $remito = isset($_POST["remito"]) ? $_POST["remito"] : null;

            try {
                if (empty($remito) && (empty($fechai) || empty($fechaf))) {
                    throw new Exception("Debe indicar un rango de fechas o Nº de remito para filtrar.");
                } else if (empty($remito)) {
                    $rspta = $remitos->listarRemitos($fechai, $fechaf);
                } else {
                    $rspta = $remitos->listarRemitos2($remito);
                }

                if (!$rspta) {
                    throw new Exception('Error al obtener los remitos');
                }

                $data = array();
                while ($reg = $rspta->fetch_object()) {
                    $data[] = array(
                        "id" => $reg->idremito,
                        "fecha" => $reg->fecha,
                        "cliente" => $reg->cliente_nombre,
                        "pventa" => $reg->pventa,
                        "numero" => $reg->numero,
                        "clasificacion" => $reg->clasificacion
                    );
                }
                echo json_encode($data);
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
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
                        "totalE" => $reg->totalE,
                        "totalD" => $reg->totalD,
                        "totalNPE" => $reg->totalNPE,
                        "totalSPE" => $reg->totalSPE,
                        "totalNPD" => $reg->totalNPD,
                        "totalSPD" => $reg->totalSPD,
                        "metros" => $reg->metros
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
            case 'importarDocumento':
                $remitos->importarDocumento();
                break;
            case 'eliminarRemito':
                $idRemito = $_POST['id'];
                $remitos->eliminarRemito($idRemito);
                break;
        default:
            throw new Exception('Operación no válida');
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
