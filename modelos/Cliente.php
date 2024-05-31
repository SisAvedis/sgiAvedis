<?php
require '../config/conexion.php';

class Cliente {
    public function __construct() {
    }

    public function insertar($nombre, $direccion, $localidad) {
        $sql = "INSERT INTO cliente (
                    nombre,
                    direccion,
                    localidad,
                    condicion
                ) VALUES (
                    '$nombre',
                    '$direccion',
                    '$localidad',
                    '1'
                )";
        return ejecutarConsulta($sql);
    }

    public function editar($nombre, $direccion, $localidad, $idcliente) {
        $sql = "UPDATE cliente SET 
                nombre='$nombre', 
                direccion='$direccion', 
                localidad='$localidad' 
                WHERE idcliente='$idcliente'";
        return ejecutarConsulta($sql);
    }

    public function desactivar($idcliente) {
        $sql = "UPDATE cliente SET condicion='0' 
               WHERE idcliente='$idcliente'";
        return ejecutarConsulta($sql);
    }

    public function activar($idcliente) {
        $sql = "UPDATE cliente SET condicion='1' 
               WHERE idcliente='$idcliente'";
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
