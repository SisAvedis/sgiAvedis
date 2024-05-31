<?php
require '../config/conexion.php';

class Cliente {
    public function __construct() {
    }

    public function insertar($nombre, $direccion, $localidad) {
        $idusuarioEditar = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';

        $sql = "INSERT INTO cliente (nombre, direccion, localidad, condicion) VALUES ( \"$nombre\", \"$direccion\", \"$localidad\", \"1\")";
        
        $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('4', '$idusuarioEditar', NOW(), '$sql');";
        ejecutarConsulta($sql2);
        return ejecutarConsulta($sql);
    }

    public function editar($nombre, $direccion, $localidad, $idcliente) {
        $idusuarioEditar = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';
        $sql = "UPDATE cliente SET nombre=\"$nombre\", direccion=\"$direccion\", localidad=\"$localidad\" WHERE idcliente=\"$idcliente\"";
        $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('4', '$idusuarioEditar', NOW(), '$sql');";
        ejecutarConsulta($sql2);
        return ejecutarConsulta($sql);
    }

    public function desactivar($idcliente) {
        $idusuarioEditar = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';
        $sql = "UPDATE cliente SET condicion=\"0\" WHERE idcliente=\"$idcliente\"";
        $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('4', '$idusuarioEditar', NOW(), '$sql');";
        ejecutarConsulta($sql2);
        return ejecutarConsulta($sql);
    }

    public function activar($idcliente) {
        $idusuarioEditar = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';
        $sql = "UPDATE cliente SET condicion=\"1\" WHERE idcliente=\"$idcliente\"";
        $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('4', '$idusuarioEditar', NOW(), '$sql');";
        ejecutarConsulta($sql2);
        return ejecutarConsulta($sql);
    }

    public function mostrar($idcliente) {
        $sql = "SELECT * FROM cliente 
                WHERE idcliente='$idcliente'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar() {
        $sql = "SELECT * FROM cliente";
        return ejecutarConsulta($sql);
    }
}
?>
