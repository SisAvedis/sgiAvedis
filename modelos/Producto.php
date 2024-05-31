<?php
require '../config/conexion.php';

class Producto {
    public function __construct() {
    }

    public function insertar($nombre) {
        $sql = "INSERT INTO tipo_producto (
                    nombre,
                    estado
                ) VALUES (
                    '$nombre',
                    '1'
                )";
        return ejecutarConsulta($sql);
    }

    public function editar($nombre, $idtipo_producto) {
        $sql = "UPDATE tipo_producto SET 
                nombre='$nombre'
                WHERE idtipo_producto='$idtipo_producto'";
        return ejecutarConsulta($sql);
    }

    public function desactivar($idtipo_producto) {
        $sql = "UPDATE tipo_producto SET estado='0' 
               WHERE idtipo_producto='$idtipo_producto'";
        return ejecutarConsulta($sql);
    }

    public function activar($idtipo_producto) {
        $sql = "UPDATE tipo_producto SET estado='1' 
               WHERE idtipo_producto='$idtipo_producto'";
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
        $sql = "DELETE FROM capacidad_producto 
        WHERE idtipo_producto='$idtipo_producto' AND capacidad = '$capacidad'";
    return ejecutarConsulta($sql);
    }

    public function modificarVolumen($idtipo_producto,$capacidad,$capacidadNueva){
        $sql = "UPDATE capacidad_producto SET capacidad='$capacidadNueva' WHERE idtipo_producto='$idtipo_producto' AND capacidad='$capacidad'";
        return ejecutarConsulta($sql);
    }

    public function agregarVolumen($idtipo_producto, $capacidadNueva) {
        $sql = "INSERT INTO capacidad_producto (idtipo_producto, capacidad) VALUES ('$idtipo_producto', '$capacidadNueva')";
        return ejecutarConsulta($sql);
    }
    
}
?>
