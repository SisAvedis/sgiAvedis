<?php
require_once "../config/conexion.php";

class Remitos {
    public function __construct() {}

    public function listarRemitos($fechai, $fechaf) {
        $sql = "SELECT r.idremito, c.nombre AS cliente_nombre, r.fecha_remito AS fecha 
                FROM remito r
                INNER JOIN cliente c ON r.cliente = c.idcliente
                WHERE r.fecha_remito >= '$fechai' AND r.fecha_remito <= '$fechaf'";
        return ejecutarConsulta($sql);
    }
    

    public function verDetalles($id) {
        $sql = "SELECT d.idtipo_producto, tp.nombre AS tipo_producto_nombre, 
                SUM(d.capacidad) AS capacidadSuma, d.propiedad, d.tipoenvase, d.nserie, d.accion, d.capacidad
                FROM detalle_remito d
                INNER JOIN tipo_producto tp ON d.idtipo_producto = tp.idtipo_producto
                WHERE d.idremito = '$id'
                GROUP BY d.idtipo_producto, d.propiedad, d.tipoenvase, d.accion";
        return ejecutarConsulta($sql);
    }

    public function verMasDetalles($id, $idtipo_producto, $propiedad, $accion, $tipoenvase) {
        $sql = "SELECT * FROM detalle_remito 
                WHERE idremito = '$id' 
                AND idtipo_producto = '$idtipo_producto' 
                AND propiedad = '$propiedad' 
                AND accion = '$accion' 
                AND tipoenvase = '$tipoenvase';";
        return ejecutarConsulta($sql);
    }
    
    
}
?>
