<?php
require '../config/conexion.php';

class Registro 
{
    public function __construct()
    {
    }

    public function listar()
    {
        $sql = "SELECT idtipo_producto, nombre, codigo FROM tipo_producto WHERE estado = '1'";
        return ejecutarConsulta($sql);
    }

    public function guardarTemporal()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['op']) && $_GET['op'] == 'guardarTemporal') {
        // Verificar si la clave 'accion' está definida en $_POST
        if (isset($_POST['accion'])) {
            $tipoProducto = $_POST['tipoProducto'];
            $propiedad = $_POST['propiedad'];
            $propiedad2 = $_POST['propiedad2'];
            $accion = $_POST['accion'];
            $campos = $_POST['campos'];
            foreach ($campos as $campo) {
                $nserie = $campo['nserie'];
                $capacidad = $campo['capacidad'];
                $sql = "INSERT INTO detalle_temporal (tipoproducto, tipoenvase, propiedad, nserie, capacidad,accion) VALUES ('$tipoProducto', '$propiedad', '$propiedad2', '$nserie', '$capacidad','$accion')";
                if (!ejecutarConsulta($sql)) {
                    echo json_encode(['success' => false, 'message' => "Error al insertar datos en la tabla temporal."]);
                    exit();
                }
            }
            echo json_encode(['success' => true]);
            exit();
        } else {
            // La clave 'accion' no está definida en $_POST
            echo json_encode(['success' => false, 'message' => "La acción no está definida en la solicitud POST."]);
            exit();
        }
    }
}

    public function enviarDatosDetalleRemito()
    {
        $cliente = $_POST['cliente'];
        $detalles = $_POST['detalles'];
        $ncomprobante = $_POST['ncomprobante'];
        $fecha = $_POST['fecha'];
        $estado = $_POST['estado'];
        $puntoVenta = $_POST['puntoVenta'];
        $idusuario = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';

        // Obtener los datos de la tabla temporal
        $sql = "INSERT INTO remito (idusuario, numero, cliente, informacion, fecha_hora, fecha_remito, clasificacion, pventa, estado) VALUES ('$idusuario','$ncomprobante','$cliente','$detalles',NOW(), '$fecha','$estado', '$puntoVenta', 1)";
        ejecutarConsulta($sql);

        $sql = "SELECT IFNULL(MAX(idremito), 1) AS proximo_id FROM remito";

        // Ejecutar la consulta
        $result = ejecutarConsulta($sql);

        // Obtener el resultado de la consulta
        $row = mysqli_fetch_assoc($result);

        // Obtener el próximo idremito
        $proximo_id = $row['proximo_id'];

        $sql = "SELECT tipoproducto, tipoenvase, propiedad, nserie, capacidad, accion FROM detalle_temporal";
        $resultData = ejecutarConsulta($sql);

        while ($rowData = $resultData->fetch_assoc()) {
            $tipoProducto = $rowData['tipoproducto'];
            $tipoEnvase = $rowData['tipoenvase'];
            $propiedad = $rowData['propiedad'];
            $nserie = $rowData['nserie'];
            $capacidad = $rowData['capacidad'];
            $accion = $rowData['accion'];

            // Insertar los datos en la tabla detalle_remito
            $sqlInsert = "INSERT INTO detalle_remito (idremito, idtipo_producto, tipoenvase, propiedad, nserie, capacidad, accion)
                        VALUES ('$proximo_id','$tipoProducto', '$tipoEnvase', '$propiedad', '$nserie', '$capacidad', '$accion')";
            ejecutarConsulta($sqlInsert);
        }
        $sql = "DELETE FROM detalle_temporal";
        ejecutarConsulta($sql);
        $sql = "ALTER TABLE detalle_temporal AUTO_INCREMENT = 1;";
        ejecutarConsulta($sql);
        // Retornar el indicador de éxito junto con los datos insertados
        echo json_encode(['success' => true]);
        exit();
    }
    public function obtenerProductosAgrupados() {
        $sql = "SELECT dt.tipoproducto, tp.nombre AS nombre_producto, dt.tipoenvase, dt.propiedad, dt.accion, COUNT(*) AS cantidad
        FROM detalle_temporal dt
        JOIN tipo_producto tp ON dt.tipoproducto = tp.idtipo_producto
        GROUP BY dt.tipoproducto, dt.tipoenvase, dt.propiedad, dt.accion
        ";
    
        $result = ejecutarConsulta($sql);
    
        $productosAgrupados = array();
    
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $productosAgrupados[] = $row;
            }
        }
    
        echo json_encode([$productosAgrupados]);
        exit();
    }
    
    function obtenerDetallesProducto() {
        $producto = $_POST["producto"];
        $envase = $_POST["envase"];
        $propiedad = $_POST["propiedad"];
        $accion = $_POST["accion"];

        // Crear la consulta SQL para obtener los detalles del producto
        $sql = "SELECT dt.tipoproducto, tp.nombre AS nombre_producto, dt.tipoenvase, dt.propiedad, dt.nserie, dt.capacidad, dt.accion
        FROM detalle_temporal dt
        INNER JOIN tipo_producto tp ON dt.tipoproducto = tp.idtipo_producto
        WHERE tp.nombre = '$producto' AND dt.tipoenvase = '$envase' AND dt.propiedad = '$propiedad' AND dt.accion = '$accion'
        ";

        // Ejecutar la consulta SQL
        $result = ejecutarConsulta($sql);

        // Crear un array para almacenar los detalles del producto
        $detallesProducto = array();

        // Verificar si se encontraron resultados
        if ($result->num_rows > 0) {
            // Recorrer los resultados y agregarlos al array de detalles
            while ($row = $result->fetch_assoc()) {
                $detallesProducto[] = $row;
            }
            // Devolver los detalles del producto como JSON
            echo json_encode(array('success' => true, 'data' => $detallesProducto));
        } else {
            // Si no se encontraron detalles, devolver un mensaje de error
            echo json_encode(array('success' => false, 'message' => 'No se encontraron detalles para el producto especificado.'));
        }
    }

    function eliminarProductoIndividual(){
        $nserie = $_POST["nserie"];

        $sql = "DELETE FROM detalle_temporal WHERE nserie = '$nserie';";

        // Ejecutar la consulta SQL
        ejecutarConsulta($sql);
    }

    function obtenerCapacidad(){
        $producto = $_POST["producto"];

        $sql="SELECT capacidad FROM capacidad_producto WHERE idtipo_producto = '$producto'";
        $result = ejecutarConsulta($sql);

        if ($result) {
            $capacidades = array();
    
            while ($row = mysqli_fetch_assoc($result)) {
                $capacidades[] = $row['capacidad'];
            }

            echo json_encode(array("success" => true, "data" => $capacidades));
        } else {
            echo json_encode(array("success" => false, "message" => "Error al obtener las capacidades."));
        }
    }

    function listarPuntosVenta(){
        $sql = "SELECT prefijo, numero FROM punto_venta";
        $result = ejecutarConsulta($sql);
        
        if ($result) {
            $puntosVenta = array();
    
            while ($row = mysqli_fetch_assoc($result)) {
                // Almacena cada fila como un array asociativo en $puntosVenta
                $puntosVenta[] = $row;
            }
    
            // Devuelve todos los datos de puntos de venta como JSON
            echo json_encode(array("success" => true, "data" => $puntosVenta));
        } else {
            // Si hay un error en la consulta, devuelve un mensaje de error
            echo json_encode(array("success" => false, "message" => "Error al obtener los puntos de venta."));
        }
    }
    
    function listarClientes(){
        $sql = "SELECT idcliente, nombre FROM cliente";
        $result = ejecutarConsulta($sql);

        if ($result) {
            $clientes = array();
    
            while ($row = mysqli_fetch_assoc($result)) {
                $clientes[] = $row;
            }
            echo json_encode(array("success" => true, "data" => $clientes));
        } else {
            echo json_encode(array("success" => false, "message" => "Error al obtener los puntos de venta."));
        }
    }

    function vaciarTemporal(){
        $sql = "DELETE FROM detalle_temporal";
        ejecutarConsulta($sql);
        $sql = "ALTER TABLE detalle_temporal AUTO_INCREMENT = 1;";
        ejecutarConsulta($sql);
        // Retornar el indicador de éxito junto con los datos insertados
        echo json_encode(['success' => true]);
        exit();
    }
    
}
?>
