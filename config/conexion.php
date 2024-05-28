<?php
    require_once 'global.php';

    $conexion = new mysqli(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_NAME);
  
    mysqli_query($conexion,'SET NAMES "'.DB_ENCODE.'"');
    // $conexion->query('SET_CHARSET = "'.DB_ENCODE.'"');
	//$conexion->query('SET LC_TIME_NAMES = "'.DB_LC_TIME_NAMES.'"');
	
    if(mysqli_connect_errno())
    {
        printf("Fallo la conexion a la base de datos: %s\n",mysqli_connect_error());
        exit();
    }

    if(!function_exists('ejecutarConsulta'))
    {
        function ejecutarConsulta($sql)
        {
            global $conexion;
			$query = $conexion->query($sql);

            return $query;
        }

        function ejecutarConsultaSimpleFila($sql)
        {
            global $conexion;
            $query = $conexion->query($sql);
            $row = $query->fetch_assoc();

            return $row;
        }

        function ejecutarConsulta_retornarID($sql)
        {
            global $conexion;
            $query = $conexion->query($sql);
            echo $query;
            return $conexion->insert_id;
        }

        function limpiarCadena($str)
        {
            global $conexion;
            $str = mysqli_real_escape_string($conexion,trim($str));

            return htmlspecialchars($str, ENT_NOQUOTES, "UTF-8");
			//return htmlspecialchars($str);
        }
    }
?>