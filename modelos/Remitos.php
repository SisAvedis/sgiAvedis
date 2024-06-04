<?php
require_once "../config/conexion.php";

class Remitos {
    public function __construct() {}

    public function listarRemitos($fechai, $fechaf) {
        $sql = "SELECT r.idremito, r.pventa, c.nombre AS cliente_nombre, DATE_FORMAT(r.fecha_remito, '%d/%m/%Y') AS fecha 
                FROM remito r
                INNER JOIN cliente c ON r.cliente = c.idcliente
                WHERE r.fecha_remito >= '$fechai' AND r.fecha_remito <= '$fechaf'";
        return ejecutarConsulta($sql);
    }
    

    public function verDetalles($id) {
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM detalle_remito WHERE propiedad = 'NP' AND idremito = '$id' AND accion = 'E') AS totalNPE,
                    (SELECT COUNT(*) FROM detalle_remito WHERE propiedad = 'NP' AND idremito = '$id' AND accion = 'D') AS totalNPD,
                    (SELECT COUNT(*) FROM detalle_remito WHERE propiedad = 'SP' AND idremito = '$id' AND accion = 'E') AS totalSPE,
                    (SELECT COUNT(*) FROM detalle_remito WHERE propiedad = 'SP' AND idremito = '$id' AND accion = 'D') AS totalSPD,
                    (SELECT COUNT(*) FROM detalle_remito WHERE idremito = '$id' AND accion = 'E') AS totalE,
                    (SELECT COUNT(*) FROM detalle_remito WHERE idremito = '$id' AND accion = 'D') AS totalD,
                    COUNT(*) AS cantidad,
                    d.idtipo_producto, 
                    tp.nombre AS tipo_producto_nombre, 
                    SUM(CASE WHEN d.accion = 'E' THEN d.capacidad ELSE 0 END) AS capacidadSuma,
                    d.propiedad, 
                    d.tipoenvase, 
                    d.nserie, 
                    d.accion
                FROM 
                    detalle_remito d
                INNER JOIN 
                    tipo_producto tp ON d.idtipo_producto = tp.idtipo_producto
                WHERE 
                    d.idremito = '$id'
                GROUP BY 
                    d.idtipo_producto, d.propiedad, d.tipoenvase, d.accion;
                ";
        return ejecutarConsulta($sql);
    }

    public function verMasDetalles($id, $idtipo_producto, $propiedad, $accion, $tipoenvase) {
        $sql = "SELECT detalle.*, tipo.nombre AS nombre_tipo_producto
        FROM detalle_remito detalle
        INNER JOIN tipo_producto tipo ON detalle.idtipo_producto = tipo.idtipo_producto
        WHERE detalle.idremito = '$id' 
        AND detalle.idtipo_producto = '$idtipo_producto' 
        AND detalle.propiedad = '$propiedad' 
        AND detalle.accion = '$accion' 
        AND detalle.tipoenvase = '$tipoenvase';";
        return ejecutarConsulta($sql);
    }

    public function importarDocumento()
    {
    $idusuario = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';
    $response = array("success" => false, "message" => "No se recibieron datos");
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data) {
        $success = true;
        foreach ($data as $remito) {
            $clasificacion = $remito['clasificacion'];
            $numero = $remito['numero'];
            $fecha_hora = $remito['fecha_hora'];
            $fecha_remito = excelToDate($remito['fecha_remito']);
            $estado = $remito['estado'];
            $cliente = $remito['cliente'];
            $informacion = $remito['informacion'];
            $pventa = $remito['pventa'];

            // Verificar si el cliente ya existe en la base de datos
            $sql = "SELECT idcliente FROM cliente WHERE nombre = '$cliente'";
            $resultadoCliente = ejecutarConsultaSimpleFila($sql);

            if (!$resultadoCliente) {
                $sqlInsertCliente = "INSERT INTO cliente (nombre, direccion, localidad, condicion) VALUES ('$cliente', '-', '-', '1')";
                $idcliente = ejecutarConsulta_retornarID($sqlInsertCliente); // Aquí debes proporcionar el resultado de la inserción
            } else {
                $idcliente = $resultadoCliente['idcliente'];
            }
            

            $sqlInsertRemito = "INSERT INTO remito (idusuario, clasificacion, numero, fecha_hora, fecha_remito, estado, cliente, informacion, pventa) VALUES ('$idusuario', '$clasificacion', '$numero', '$fecha_hora', '$fecha_remito', '$estado', '$idcliente', '$informacion', '$pventa')";
            $resultadoInsertRemito = ejecutarConsulta($sqlInsertRemito);
            if ($resultadoInsertRemito !== true) {
                $success = false;
                $response['message'] = 'Error al guardar los datos del remito en la base de datos: ' . $resultadoInsertRemito;
                break;
            }
        }

        if ($success) {
            $response = array("success" => true, "message" => "Datos guardados correctamente");
        }
    } else {
        $response['message'] = 'No se recibieron datos válidos';
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}





    
}

function excelToDate($excelDate) {
    $excelBaseDate = 25569; // 1/1/1970 en formato de fecha de Excel
    $unixBaseDate = strtotime('1970-01-01'); // 1/1/1970 en formato UNIX timestamp

    // Convertir el número de serie de fecha de Excel a UNIX timestamp
    $unixTimestamp = ($excelDate - $excelBaseDate) * 86400; // 86400 segundos en un día

    // Convertir UNIX timestamp a una fecha válida en PHP
    $date = date('Y-m-d H:i:s', $unixBaseDate + $unixTimestamp);

    return $date;
}
?>
