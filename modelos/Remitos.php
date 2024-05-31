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
        (SELECT COUNT(*) FROM detalle_remito WHERE propiedad = 'NP' AND idremito = '$id') AS totalNP,
        (SELECT COUNT(*) FROM detalle_remito WHERE propiedad = 'SP' AND idremito = '$id') AS totalSP,
        COUNT(*) AS cantidad,
        d.idtipo_producto, 
        tp.nombre AS tipo_producto_nombre, 
        SUM(d.capacidad) AS capacidadSuma, 
        d.propiedad, 
        d.tipoenvase, 
        d.nserie, 
        d.accion, 
        d.capacidad
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
    
    
}
?>
