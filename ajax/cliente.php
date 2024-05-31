<?php

session_start();

require_once '../modelos/Cliente.php';

$cliente = new Cliente();

$idcliente = isset($_POST["idcliente"]) ? limpiarCadena($_POST["idcliente"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$direccion = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";
$localidad = isset($_POST["localidad"]) ? limpiarCadena($_POST["localidad"]) : "";

switch($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($idcliente)) {
            $rspta = $cliente->insertar($nombre, $direccion, $localidad);
            echo $rspta ? "Cliente registrado" : "Cliente no se pudo registrar";
        } else {
            $rspta = $cliente->editar($nombre, $direccion, $localidad, $idcliente);
            echo $rspta ? "Cliente actualizado" : "Cliente no se pudo actualizar";
        }
        break;

    case 'desactivar':
        $rspta = $cliente->desactivar($idcliente);
        echo $rspta ? "Cliente desactivado" : "Cliente no se pudo desactivar";
        break;

    case 'activar':
        $rspta = $cliente->activar($idcliente);
        echo $rspta ? "Cliente activado" : "Cliente no se pudo activar";
        break;

    case 'mostrar':
        $rspta = $cliente->mostrar($idcliente);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $cliente->listar();
        $data = Array();
        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => ($reg->condicion) ?
                    '<button class="btn btn-warning" onclick="mostrar(' . $reg->idcliente . ')" data-toggle="tooltip" title="Editar"><li class="fa fa-pencil"></li></button>' .
                    ' <button class="btn btn-danger" onclick="desactivar(' . $reg->idcliente . ')" data-toggle="tooltip" title="Desactivar"><li class="fa fa-close"></li></button>'
                    :
                    '<button class="btn btn-warning" onclick="mostrar(' . $reg->idcliente . ')" data-toggle="tooltip" title="Editar"><li class="fa fa-pencil"></li></button>' .
                    ' <button class="btn btn-primary" onclick="activar(' . $reg->idcliente . ')" data-toggle="tooltip" title="Activar"><li class="fa fa-check"></li></button>',
                "1" => $reg->nombre,
                "2" => $reg->direccion,
                "3" => $reg->localidad,
                "4" => ($reg->condicion) ?
                    '<span class="label bg-green">Activado</span>'
                    :
                    '<span class="label bg-red">Desactivado</span>'
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
}
?>
