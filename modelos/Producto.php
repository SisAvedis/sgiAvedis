<?php
require '../config/conexion.php';

class Producto {
    public function __construct() {
    }

    public function insertar($nombre,$codigo) {
        $idusuarioEditar = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';
        $sql = "INSERT INTO tipo_producto (nombre, codigo, estado) VALUES (\"$nombre\",\"$codigo\",\"1\")";
        $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('3', '$idusuarioEditar', NOW(), '$sql');";
        ejecutarConsulta($sql2);
        return ejecutarConsulta($sql);
    }

    public function editar($nombre, $codigo, $idtipo_producto) {
        $idusuarioEditar = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';
        $sql = "UPDATE tipo_producto SET nombre=\"$nombre\", codigo=\"$codigo\" WHERE idtipo_producto=\"$idtipo_producto\"";
        $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('3', '$idusuarioEditar', NOW(), '$sql');";
        ejecutarConsulta($sql2);
        return ejecutarConsulta($sql);
    }

    public function desactivar($idtipo_producto) {
        $idusuarioEditar = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';
        $sql = "UPDATE tipo_producto SET estado=\"0\" WHERE idtipo_producto=\"$idtipo_producto\"";
        $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('3', '$idusuarioEditar', NOW(), '$sql');";
        ejecutarConsulta($sql2);
        return ejecutarConsulta($sql);
    }

    public function activar($idtipo_producto) {
        $idusuarioEditar = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';
        $sql = "UPDATE tipo_producto SET estado=\"1\" WHERE idtipo_producto=\"$idtipo_producto\"";
        $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('3', '$idusuarioEditar', NOW(), '$sql');";
        ejecutarConsulta($sql2);
        return ejecutarConsulta($sql);
    }

    public function mostrar($idtipo_producto) {
        $sql = "SELECT * FROM tipo_producto 
                WHERE idtipo_producto='$idtipo_producto'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar() {
        $sql = "SELECT * FROM tipo_producto";
        return ejecutarConsulta($sql);
    }

    public function verVolumen($idtipo_producto) {
        $sql = "SELECT * FROM capacidad_producto 
               WHERE idtipo_producto='$idtipo_producto'";
        return ejecutarConsulta($sql);
    }

    public function eliminarVolumen($idtipo_producto,$capacidad){
        $idusuarioEditar = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';
        $sql = "DELETE FROM capacidad_producto WHERE idtipo_producto=\"$idtipo_producto\" AND capacidad = \"$capacidad\"";
        $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('3', '$idusuarioEditar', NOW(), '$sql');";
        ejecutarConsulta($sql2);
        return ejecutarConsulta($sql);
    }

    public function modificarVolumen($idtipo_producto,$capacidad,$capacidadNueva){
        $idusuarioEditar = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';
        $sql = "UPDATE capacidad_producto SET capacidad=\"$capacidadNueva\" WHERE idtipo_producto=\"$idtipo_producto\" AND capacidad=\"$capacidad\"";
        $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('3', '$idusuarioEditar', NOW(), '$sql');";
        ejecutarConsulta($sql2);
        return ejecutarConsulta($sql);
    }

    public function agregarVolumen($idtipo_producto, $capacidadNueva) {
        $idusuarioEditar = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';
        $sql = "INSERT INTO capacidad_producto (idtipo_producto, capacidad) VALUES (\"$idtipo_producto\", \"$capacidadNueva\")";
        $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('3', '$idusuarioEditar', NOW(), '$sql');";
        ejecutarConsulta($sql2);
        return ejecutarConsulta($sql);
    }
    
}
?>
