<?php
session_start();

require_once '../modelos/PVenta.php';

$punto_venta = new PVenta();

$idpunto_venta = isset($_POST["idpunto_venta"]) ? limpiarCadena($_POST["idpunto_venta"]) : "";
$prefijo = isset($_POST["prefijo"]) ? limpiarCadena($_POST["prefijo"]) : "";
$numero = isset($_POST["numero"]) ? limpiarCadena($_POST["numero"]) : "";

switch($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($idpunto_venta)) {
            $rspta = $punto_venta->insertar($prefijo, $numero);
            echo $rspta ? "Punto de venta registrado" : "Punto de venta no se pudo registrar";
        } else {
            $rspta = $punto_venta->editar($prefijo, $numero, $idpunto_venta);
            echo $rspta ? "Punto de venta actualizado" : "Punto de venta no se pudo actualizar";
        }
        break;

    case 'desactivar':
        $rspta = $punto_venta->desactivar($idpunto_venta);
        echo $rspta ? "Punto de venta desactivado" : "Punto de venta no se pudo desactivar";
        break;

    case 'activar':
        $rspta = $punto_venta->activar($idpunto_venta);
        echo $rspta ? "Punto de venta activado" : "Punto de venta no se pudo activar";
        break;

    case 'mostrar':
        $rspta = $punto_venta->mostrar($idpunto_venta);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $punto_venta->listar();
        $data = array();
        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => ($reg->condicion) ?
                    '<button class="btn btn-warning" onclick="mostrar(' . $reg->idpunto_venta . ')" data-toggle="tooltip" title="Editar"><li class="fa fa-pencil"></li></button>' .
                    ' <button class="btn btn-danger" onclick="desactivar(' . $reg->idpunto_venta . ')" data-toggle="tooltip" title="Desactivar"><li class="fa fa-close"></li></button>'
                    :
                    '<button class="btn btn-warning" onclick="mostrar(' . $reg->idpunto_venta . ')" data-toggle="tooltip" title="Editar"><li class="fa fa-pencil"></li></button>' .
                    ' <button class="btn btn-primary" onclick="activar(' . $reg->idpunto_venta . ')" data-toggle="tooltip" title="Activar"><li class="fa fa-check"></li></button>',
                "1" => $reg->prefijo,
                "2" => $reg->numero,
                "3" => ($reg->condicion) ?
                    '<span class="label bg-green">Activado</span>'
                    :
                    '<span class="label bg-red">Desactivado</span>'
            );
        }
        $results = array(
            "sEcho" => 1, // Informacion para el datable
            "iTotalRecords" => count($data), // Enviamos el total de registros al datatable
            "iTotalDisplayRecords" => count($data), // Enviamos el total de registros a visualizar
            "aaData" => $data
        );
        echo json_encode($results);
        break;
}
?>
