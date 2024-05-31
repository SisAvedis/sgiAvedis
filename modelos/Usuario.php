<?php
    require '../config/conexion.php';

    Class Usuario 
    {
        public function __construct()
        {

        }

        public function insertar($nombre,$login,$clave,$imagen,$permisos)
        {
            $idusuario = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';
            $sql = "INSERT INTO usuario (nombre, login, clave, imagen, condicion) VALUES (\"$nombre\", \"$login\", \"$clave\", \"$imagen\", \"1\")";
            $idusuarionew = ejecutarConsulta_retornarID($sql);
            $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('1', '$idusuario', NOW(), '$sql')";
            ejecutarConsulta($sql2);
            $num_elementos = 0;
            $sw = true;

            while($num_elementos < count($permisos))
            {
                $sql_detalle ="INSERT INTO usuario_permiso ( idusuario, idpermiso) VALUES (\"$idusuarionew\", \"$permisos[$num_elementos]\")";
                
                ejecutarConsulta($sql_detalle) or $sw = false;
                
                $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('1', '$idusuario', NOW(), '$sql_detalle');";
                ejecutarConsulta($sql2);

                $num_elementos = $num_elementos + 1;
            }
            return $sw;
        }

        public function editar($idusuario,$nombre,$login,$clave,$imagen,$permisos)
        {
            $idusuarioEditar = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';
            $sql = "UPDATE usuario SET nombre=\"$nombre\", login=\"$login\", clave=\"$clave\", imagen=\"$imagen\" WHERE idusuario=\"$idusuario\";";
            
            ejecutarConsulta($sql);
            
            $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('1', '$idusuarioEditar', NOW(), '$sql');";
            ejecutarConsulta($sql2);
            //Eliminamos todos los permisos asignados para volverlos a registrar
            $sqldel = "DELETE FROM usuario_permiso WHERE idusuario=\"$idusuario\";";
            
            ejecutarConsulta($sqldel);

            $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('1', '$idusuarioEditar', NOW(), '$sqldel');";
            ejecutarConsulta($sql2);

            $num_elementos = 0;
            $sw = true;

            while($num_elementos < count($permisos))
            {
                $sql_detalle = "INSERT INTO usuario_permiso(idusuario, idpermiso) VALUES (\"$idusuario\", \"$permisos[$num_elementos]\")";
                ejecutarConsulta($sql_detalle) or $sw = false;
                $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('1', '$idusuarioEditar', NOW(), '$sql_detalle');";
                ejecutarConsulta($sql2);
                $num_elementos = $num_elementos + 1;
            }

            return $sw;
        }

        public function desactivar($idusuario)
        {
            $idusuarioEditar = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';
            $sql= "UPDATE usuario SET condicion=\"0\" WHERE idusuario=\"$idusuario\"";
            $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('1', '$idusuarioEditar', NOW(), '$sql');";
            ejecutarConsulta($sql2);
            return ejecutarConsulta($sql);
        }

        public function activar($idusuario)
        {
            $idusuarioEditar = isset($_COOKIE['idusuario']) ? $_COOKIE['idusuario'] : '';
            $sql= "UPDATE usuario SET condicion=\"1\" WHERE idusuario=\"$idusuario\"";
            $sql2 = "INSERT INTO logs (idventana_abm, idusuario, fecha_hora, consulta) VALUES ('1', '$idusuarioEditar', NOW(), '$sql');";
            ejecutarConsulta($sql2);
            return ejecutarConsulta($sql);
        }

    
        public function mostrar($idusuario)
        {
            $sql = "SELECT * FROM usuario 
                    WHERE idusuario='$idusuario'";

            return ejecutarConsultaSimpleFila($sql);
        }

        public function listar()
        {
            $sql = "SELECT * FROM usuario";

            return ejecutarConsulta($sql);
        }

        public function listarmarcados($idusuario)
        {
            $sql = "SELECT * FROM usuario_permiso
                    WHERE idusuario='$idusuario'";
            
            return ejecutarConsulta($sql);
        }

        //Verficacion de acceso
        public function verificar($login,$clave)
        {
            $sql = "SELECT 
                        idusuario,
                        nombre,
                        imagen,
                        num_documento,
                        login
                    FROM usuario
                    WHERE login='$login' 
                    AND clave='$clave'
                    AND condicion='1'";
            
			return ejecutarConsulta($sql);
        }
		
    }

?>