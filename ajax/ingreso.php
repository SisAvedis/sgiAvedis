<?php
    require_once '../modelos/Registro.php';
    $registro = new Registro();
    switch($_GET["op"])
{
    case 'listarProductos':
        $registro = new Registro();
        $resultados = $registro->listar();
        if ($resultados) {
            $data = array();
            while ($row = mysqli_fetch_assoc($resultados)) {
                $data[] = $row;
            }
            echo json_encode(array("success" => true, "data" => $data));
        } else {
            echo json_encode(array("success" => false, "message" => "No se encontraron productos."));
        }
    break;
    case 'guardarTemporal':
        $registro->guardarTemporal();
    break;
        
    case 'enviarDatosDetalleRemito':
        $registro->enviarDatosDetalleRemito();
    break;

    case 'obtenerProductosAgrupados':
        $registro->obtenerProductosAgrupados();
        break;
    case 'obtenerDetallesProducto':
        $registro->obtenerDetallesProducto();
        break;
    case 'eliminarProductoIndividual':
        $registro->eliminarProductoIndividual();
    break;
    case 'obtenerCapacidad':
        $registro->obtenerCapacidad();
        break;
    case 'listarPuntosVenta':
        $registro->listarPuntosVenta();
        break;
    case 'listarClientes':
        $registro->listarClientes();
        break;
    case 'vaciarTemporal':
        $registro->vaciarTemporal();
        break;
}


?>