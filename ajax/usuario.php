<?php

    session_start();
    
    require_once '../modelos/Usuario.php';

    $usuario = new Usuario();

    $idusuario=isset($_POST["idusuario"])? limpiarCadena($_POST["idusuario"]):"";
    $nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
    $login=isset($_POST["login"])? limpiarCadena($_POST["login"]):"";
    $clave=isset($_POST["clave"])? limpiarCadena($_POST["clave"]):"";
    $imagen=isset($_POST["imagen"])? limpiarCadena($_POST["imagen"]):"";

    switch($_GET["op"])
    {
        case 'guardaryeditar':

            if(!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name']))
            {
                $imagen = $_POST["imagenactual"];
            }
            else
            {
                $ext = explode(".",$_FILES["imagen"]["name"]);
                if($_FILES['imagen']['type'] == "image/jpg" || $_FILES['imagen']['type'] == "image/jpeg" || $_FILES['imagen']['type'] == "image/png")
                {
                    $imagen = round(microtime(true)).'.'.end($ext);
                    move_uploaded_file($_FILES['imagen']['tmp_name'], "../files/usuarios/" . $imagen);
                }
            }

            //Hash SHA256 en la contraseña
            $clavehash = hash("SHA256",$clave);

            if (empty($idusuario)){
                $rspta=$usuario->insertar($nombre,$login,$clavehash,$imagen,$_POST['permiso']);
                echo $rspta ? "Usuario registrado" : "No se pudieron registrar todos los datos del usuario";
            }
            else {
                $rspta=$usuario->editar($idusuario,$nombre,$login,$clavehash,$imagen,$_POST['permiso']);
                echo $rspta ? "Usuario actualizado" : "Usuario no se pudo actualizar";
            }
        break;

        case 'desactivar':
                $rspta = $usuario->desactivar($idusuario);
                echo $rspta ? "Usuario desactivado" : "Usuario no se pudo desactivar";
        break;

        case 'activar':
            $rspta = $usuario->activar($idusuario);
            echo $rspta ? "Usuario activado" : "Usuario no se pudo activar";
        break;

        case 'mostrar':
            $rspta = $usuario->mostrar($idusuario);
            echo json_encode($rspta);
        break;

        case 'listar':
            $rspta = $usuario->listar();
            $data = Array();
            while ($reg = $rspta->fetch_object()) {
                $data[] = array(
                    "0"=> ($reg->condicion) ? 
                        '<button class="btn btn-warning" onclick="mostrar('.$reg->idusuario.')" data-toggle="tooltip" title="Editar"><li class="fa fa-pencil"></li></button>'.
                        ' <button class="btn btn-danger" onclick="desactivar('.$reg->idusuario.')" data-toggle="tooltip" title="Desactivar"><li class="fa fa-close"></li></button>'
                        :
                        '<button class="btn btn-warning" onclick="mostrar('.$reg->idusuario.')" data-toggle="tooltip" title="Editar"><li class="fa fa-pencil"></li></button>'.
                        ' <button class="btn btn-primary" onclick="activar('.$reg->idusuario.')" data-toggle="tooltip" title="Activar"><li class="fa fa-check"></li></button>'
                        ,
                    "1"=>$reg->nombre,
                    "2"=>$reg->login,
                    "3"=>"<img src='../files/usuarios/".$reg->imagen."' height='50px' width='50px'>",
                    "4"=>($reg->condicion) ?
                         '<span class="label bg-green">Activado</span>'
                         :      
                         '<span class="label bg-red">Desactivado</span>'
                );
            }
            $results = array(
                "sEcho"=>1, //Informacion para el datable
                "iTotalRecords" =>count($data), //enviamos el total de registros al datatable
                "iTotalDisplayRecords" => count($data), //enviamos el total de registros a visualizar
                "aaData" =>$data
            );
            echo json_encode($results);
        break;

        case 'permisos':
            //obtenemos los permisos de la tabla permisos
            require_once '../modelos/Permiso.php';
            $permiso = new Permiso();
            $rspta = $permiso->listar();

            //Obtener los permisos del usuario
            $id=$_GET['id'];
            $marcados = $usuario->listarmarcados($id);
            
            //declaramos el array para almacenar todos los permisos marcados
            $valores = array();

            //Almacenar los permisos asignados al usuario en el array
            while ($per = $marcados->fetch_object()) 
            {
                array_push($valores,$per->idpermiso);
            }

            while($reg = $rspta->fetch_object())
            {
                $sw = in_array($reg->idpermiso, $valores) ? 'checked' : '';

                echo '<li> 
                        <input type="checkbox" '.$sw.' name="permiso[]" value="'.$reg->idpermiso.'">'
                        .$reg->nombre.
                    '</li>';
            }
        break;


        case 'verificar':
			//SQL Injection
			$logina = limpiarCadena($_POST['logina']);
            $clavea = limpiarCadena($_POST['clavea']);
            
            //Desencriptar clave SHA256
            $clavehash = hash("SHA256",$clavea);

            $rspta = $usuario->verificar($logina, $clavehash);

            $fetch = $rspta->fetch_object();

            if(isset($fetch))
            {
                //Declarando variables de session
               /* $_SESSION['idusuario'] = $fetch->idusuario;
                $_SESSION['nombre'] = $fetch->nombre;
                $_SESSION['imagen'] = $fetch->imagen;
                $_SESSION['login'] = $fetch->login;
                $_SESSION['num_documento'] = $fetch->num_documento;*/
                setcookie("idusuario", $fetch->idusuario, 0 , '/');
                setcookie("nombre", $fetch->nombre, 0 , '/');
                setcookie("imagen", $fetch->imagen, 0 , '/');
                setcookie("login", $fetch->login, 0 , '/');
                setcookie("num_documento", $fetch->num_documento, 0 , '/');
                setcookie("sistema", "3", 0 , '/');
               

                //Obtenemos los permisos del usuario
                $permisos = $usuario->listarmarcados($fetch->idusuario);

                //Array para almacenar los permisos
                $valores= array();

                while($per = $permisos->fetch_object())
                {
                    array_push($valores, $per->idpermiso);
                }
            
                in_array(1,$valores) ? setcookie("escritorio", "1", 0, "/") : setcookie("escritorio", "0", 0, "/");
                in_array(2,$valores) ? setcookie("ABM", "1", 0, "/") : setcookie("ABM", "0", 0, "/");
                in_array(3,$valores) ? setcookie("acceso", "1", 0, "/") : setcookie("acceso", "0", 0, "/");
                in_array(4,$valores) ? setcookie("consulta", "1", 0, "/") : setcookie("consulta", "0", 0, "/");
                in_array(5,$valores) ? setcookie("remitos", "1", 0, "/") : setcookie("remitos", "0", 0, "/");
            }

            echo json_encode($fetch); //Retornando JSON
        break;

        case 'salir':
            session_unset(); //Limpiamos las variables de sesion
            session_destroy(); //Destriumos la sesion
            header("Location: ../index.php");
        break;

    }

?>