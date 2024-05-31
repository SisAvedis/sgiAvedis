<?php
require '../config/conexion.php';

class PVenta {
    public function __construct() {}

    public function insertar($prefijo, $numero) {
        $idusuarioEditar = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';
        $sql = "INSERT INTO punto_venta (prefijo, numero, condicion) VALUES (\"$prefijo\", \"$numero\", \"1\")";
        $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('5', '$idusuarioEditar', NOW(), '$sql');";
        ejecutarConsulta($sql2);
        return ejecutarConsulta($sql);
    }

    public function editar($prefijo, $numero, $idpunto_venta) {
        $idusuarioEditar = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';
        $sql = "UPDATE punto_venta SET prefijo=\"$prefijo\", numero=\"$numero\" WHERE idpunto_venta=\"$idpunto_venta\"";
        $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('5', '$idusuarioEditar', NOW(), '$sql');";
        ejecutarConsulta($sql2);
        return ejecutarConsulta($sql);
    }

    public function desactivar($idpunto_venta) {
        $idusuarioEditar = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';
        $sql = "UPDATE punto_venta SET condicion=\"0\" WHERE idpunto_venta=\"$idpunto_venta\"";
        $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('5', '$idusuarioEditar', NOW(), '$sql');";
        ejecutarConsulta($sql2);
        return ejecutarConsulta($sql);
    }

    public function activar($idpunto_venta) {
        $idusuarioEditar = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';
        $sql = "UPDATE punto_venta SET condicion=\"1\" WHERE idpunto_venta=\"$idpunto_venta\"";
        $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('5', '$idusuarioEditar', NOW(), '$sql');";
        ejecutarConsulta($sql2);
        return ejecutarConsulta($sql);
    }

    public function mostrar($idpunto_venta) {
        $sql = "SELECT * FROM punto_venta WHERE idpunto_venta='$idpunto_venta'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar() {
        $sql = "SELECT * FROM punto_venta";
        return ejecutarConsulta($sql);
    }
}
?>
