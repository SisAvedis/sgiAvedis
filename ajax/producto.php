<?php

session_start();

require_once '../modelos/Producto.php';

$producto = new Producto();

$idtipo_producto = isset($_POST["idtipo_producto"]) ? limpiarCadena($_POST["idtipo_producto"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$capacidad = isset($_POST["capacidad"]) ? limpiarCadena($_POST["capacidad"]) : "";
$capacidadNueva = isset($_POST["capacidadNueva"]) ? limpiarCadena($_POST["capacidadNueva"]) : "";

switch($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($idtipo_producto)) {
            $rspta = $producto->insertar($nombre);
            echo $rspta ? "Producto registrado" : "Producto no se pudo registrar";
        } else {
            $rspta = $producto->editar($nombre, $idtipo_producto);
            echo $rspta ? "Producto actualizado" : "Producto no se pudo actualizar";
        }
        break;

    case 'desactivar':
        $rspta = $producto->desactivar($idtipo_producto);
        echo $rspta ? "Producto desactivado" : "Producto no se pudo desactivar";
        break;

    case 'activar':
        $rspta = $producto->activar($idtipo_producto);
        echo $rspta ? "Producto activado" : "Producto no se pudo activar";
        break;

    case 'mostrar':
        $rspta = $producto->mostrar($idtipo_producto);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $producto->listar();
        $data = Array();
        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => ($reg->estado) ?
                    "<button class='btn btn-warning' onclick='mostrar(" . $reg->idtipo_producto . ")' data-toggle='tooltip' title='Editar'><li class='fa fa-pencil'></li></button>" .
                    " <button class='btn btn-danger' onclick='desactivar(" . $reg->idtipo_producto . ")' data-toggle='tooltip' title='Desactivar'><li class='fa fa-close'></li></button>" .
                    " <button class='btn btn-info' onclick='vervolumen(" . $reg->idtipo_producto . ")' data-toggle='tooltip' title='Ver Volumen'><li class='fa fa-flask'></li></button>"
                    :
                    "<button class='btn btn-warning' onclick='mostrar(" . $reg->idtipo_producto . ")' data-toggle='tooltip' title='Editar'><li class='fa fa-pencil'></li></button>" .
                    " <button class='btn btn-primary' onclick='activar(" . $reg->idtipo_producto . ")' data-toggle='tooltip' title='Activar'><li class='fa fa-check'></li></button>" .
                    " <button class='btn btn-info' onclick='vervolumen(" . $reg->idtipo_producto . ")' data-toggle='tooltip' title='Ver Volumen'><li class='fa fa-flask'></li></button>",
                "1" => $reg->nombre,
                "2" => ($reg->estado) ?
                    "<span class='label bg-green'>Activado</span>"
                    :
                    "<span class='label bg-red'>Desactivado</span>"
            );
        }
        
        $results = array(
            "sEcho" => 1, //Informacion para el datable
            "iTotalRecords" => count($data), //enviamos el total de registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total de registros a visualizar
            "aaData" => $data
        );
        echo json_encode($results);
        break;
        case 'verVolumen':
            $rspta = $producto->verVolumen($idtipo_producto);
    
            $data = array();
            while ($reg = $rspta->fetch_object()) {
                $data[] = array(
                    "capacidad" => $reg->capacidad
                );
            }
            echo json_encode($data);
            break;
        case 'eliminarVolumen':
            $producto->eliminarVolumen($idtipo_producto,$capacidad);
            break;
        case 'modificarVolumen':
            $producto->modificarVolumen($idtipo_producto,$capacidad,$capacidadNueva);
            break;
            case 'agregarVolumen':
                $capacidadNueva = $_POST['capacidad']; // Asegúrate de que el nombre del parámetro coincide
                $producto->agregarVolumen($idtipo_producto, $capacidadNueva);
                break;
            
}
?>
