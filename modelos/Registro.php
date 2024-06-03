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
            echo json_encode(['success' => false, 'message' => "La acción no está definida en la solicitud POST."]);
            exit();
        }
    }
}

    public function editarRemito(){
        $idusuarioEditar = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';
        $cliente = $_POST['cliente'];
        $detalles = $_POST['detalles'];
        $ncomprobante = $_POST['ncomprobante'];
        $fecha = $_POST['fecha'];
        $estado = $_POST['estado'];
        $puntoVenta = $_POST['puntoVenta'];
        $idCambio = $_POST['idCambio'];
        $idusuario = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';

        $sql = "UPDATE remito SET idusuario = \"$idusuario\", numero = \"$ncomprobante\", cliente = \"$cliente\", informacion = \"$detalles\", fecha_hora = NOW(), fecha_remito = \"$fecha\", clasificacion = \"$estado\", pventa = \"$puntoVenta\", estado = \"1\" WHERE idremito = \"$idCambio\"";
        ejecutarConsulta($sql);
        $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('2', '$idusuarioEditar', NOW(), '$sql');";
        ejecutarConsulta($sql2);

        $sql = "DELETE FROM detalle_remito WHERE idremito = '$idCambio'";
        ejecutarConsulta($sql);

        $sql = "SELECT tipoproducto, tipoenvase, propiedad, nserie, capacidad, accion FROM detalle_temporal";
        $resultData = ejecutarConsulta($sql);

        while ($rowData = $resultData->fetch_assoc()) {
            $tipoProducto = $rowData['tipoproducto'];
            $tipoEnvase = $rowData['tipoenvase'];
            $propiedad = $rowData['propiedad'];
            $nserie = $rowData['nserie'];
            $capacidad = $rowData['capacidad'];
            $accion = $rowData['accion'];

            $sqlInsert = "INSERT INTO detalle_remito (idremito, idtipo_producto, tipoenvase, propiedad, nserie, capacidad, accion) VALUES (\"$idCambio\",\"$tipoProducto\", \"$tipoEnvase\", \"$propiedad\", \"$nserie\", \"$capacidad\", \"$accion\")";
            ejecutarConsulta($sqlInsert);
            $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('2', '$idusuarioEditar', NOW(), '$sqlInsert');";
            ejecutarConsulta($sql2);
        }
        $sql = "DELETE FROM detalle_temporal";
        ejecutarConsulta($sql);
        $sql = "ALTER TABLE detalle_temporal AUTO_INCREMENT = 1;";
        ejecutarConsulta($sql);
        
        echo json_encode(['success' => true]);
        exit();
    }
    public function enviarDatosDetalleRemito()
    {
        $idusuarioEditar = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';
        $cliente = $_POST['cliente'];
        $detalles = $_POST['detalles'];
        $ncomprobante = $_POST['ncomprobante'];
        $fecha = $_POST['fecha'];
        $estado = $_POST['estado'];
        $puntoVenta = $_POST['puntoVenta'];
        $idusuario = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';

        $sql = "INSERT INTO remito (idusuario, numero, cliente, informacion, fecha_hora, fecha_remito, clasificacion, pventa, estado) VALUES (\"$idusuario\",\"$ncomprobante\",\"$cliente\",\"$detalles\",NOW(), \"$fecha\",\"$estado\", \"$puntoVenta\", \"1\")";
        ejecutarConsulta($sql);
        $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('2', '$idusuarioEditar', NOW(), '$sql');";
        ejecutarConsulta($sql2);


        $sql = "SELECT IFNULL(MAX(idremito), 1) AS proximo_id FROM remito";

        $result = ejecutarConsulta($sql);

        $row = mysqli_fetch_assoc($result);

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

            $sqlconsulta = "SELECT COUNT(*) AS existe FROM envase WHERE nserie = '$nserie'";
            $resultado = ejecutarConsultaSimpleFila($sqlconsulta);
            if ($resultado['existe'] == 0) {
                $sqlRegistro = "INSERT INTO envase (nserie, capacidad) VALUES ('$nserie', '$capacidad')";
                ejecutarConsulta($sqlRegistro);
            }
            $sqlInsert = "INSERT INTO detalle_remito (idremito, idtipo_producto, tipoenvase, propiedad, nserie, capacidad, accion) VALUES (\"$proximo_id\",\"$tipoProducto\", \"$tipoEnvase\", \"$propiedad\", \"$nserie\", \"$capacidad\", \"$accion\")";
            ejecutarConsulta($sqlInsert);
            $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('2', '$idusuarioEditar', NOW(), '$sqlInsert');";
            ejecutarConsulta($sql2);
        }
        $sql = "DELETE FROM detalle_temporal";
        ejecutarConsulta($sql);
        $sql = "ALTER TABLE detalle_temporal AUTO_INCREMENT = 1;";
        ejecutarConsulta($sql);
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

        $result = ejecutarConsulta($sql);

        $detallesProducto = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $detallesProducto[] = $row;
            }
            echo json_encode(array('success' => true, 'data' => $detallesProducto));
        } else {
            echo json_encode(array('success' => false, 'message' => 'No se encontraron detalles para el producto especificado.'));
        }
    }

    function eliminarProductoIndividual(){
        $nserie = $_POST["nserie"];

        $sql = "DELETE FROM detalle_temporal WHERE nserie = '$nserie';";

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
            
                $puntosVenta[] = $row;
            }
    
      
            echo json_encode(array("success" => true, "data" => $puntosVenta));
        } else {
           
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
      
        echo json_encode(['success' => true]);
        exit();
    }
    
    function mostrarRemito($idRemito){
        $sql="SELECT * FROM detalle_remito WHERE idremito = '$idRemito';";
        $result = ejecutarConsulta($sql);
        if ($result && $result->num_rows > 0) {
           
            while ($fila = $result->fetch_assoc()) {
                
                $iddetalle = $fila['iddetalle_remito'];
                $tipoproducto = $fila['idtipo_producto'];
                $tipoenvase = $fila['tipoenvase'];
                $propiedad = $fila['propiedad'];
                $nserie = $fila['nserie'];
                $capacidad = $fila['capacidad'];
                $accion = $fila['accion'];
        
                $sqlInsertarTemporal = "INSERT INTO detalle_temporal (iddetalle, tipoproducto, tipoenvase, propiedad, nserie, capacidad, accion) VALUES ('$iddetalle','$tipoproducto','$tipoenvase','$propiedad','$nserie','$capacidad','$accion') ";
                ejecutarConsulta($sqlInsertarTemporal);
            }
        }
        $sql="SELECT * FROM remito WHERE idremito = '$idRemito';";
        $result =  ejecutarConsulta($sql);
        if ($result) {
            $remitoData = $result->fetch_assoc();
            echo json_encode($remitoData);
        } else {
            echo json_encode(['error' => 'No se encontraron datos del remito']);
        }
    }

    function buscarNserie() {
        $query = isset($_POST['query']) ? $_POST['query'] : '';
        if (empty($query)) {
            echo json_encode(['success' => false, 'message' => 'Query parameter is missing']);
            return;
        }
        $sql = "SELECT nserie FROM envase WHERE nserie LIKE '%$query%'";
        $result = ejecutarConsulta($sql);
        if ($result && $result->num_rows > 0) {
            $nseries = [];
            while ($row = $result->fetch_assoc()) {
                $nseries[] = $row['nserie'];
            }
            echo json_encode(['success' => true, 'data' => $nseries]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontraron números de serie']);
        }
    }

    function completarCapacidad(){
        $value = isset($_POST['value']) ? $_POST['value'] : '';
        $sql = "SELECT capacidad FROM envase WHERE nserie = '$value'";
        $result = ejecutarConsulta($sql);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode(['success' => true, 'capacidad' => $row['capacidad']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró la capacidad']);
        }
    }
    
}
?>
