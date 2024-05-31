
      <?php
  if(strlen(session_id()) < 1) //Si la variable de session no esta iniciada
  {
    session_start();
  } 
  if(isset($_COOKIE['sistema']))
  if($_COOKIE['sistema'] != '3'){
    session_unset(); //Limpiamos las variables de sesion
    session_destroy(); //Destriumos la sesion
    if (isset($_SERVER['HTTP_COOKIE'])) {
      $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
      foreach($cookies as $cookie) {
          $parts = explode('=', $cookie);
          $name = trim($parts[0]);
          setcookie($name, '', time()-1000);
          setcookie($name, '', time()-1000, '/');
      }
  }
    header("Location: login.html");
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SGI | Gases Industriales</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="../public/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../public/css/font-awesome.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../public/css/AdminLTE.min.css">
    
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="../public/css/_all-skins.min.css">
    <link rel="apple-touch-icon" href="../public/img/apple-touch-icon.png">
    <link rel="shortcut icon" href="../public/img/avedis_favicon.ico">

    <!--DATATABLES-->
    <link rel="stylesheet" href="../public/datatables/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../public/datatables/buttons.dataTables.min.css">
    <link rel="stylesheet" href="../public/datatables/responsive.dataTables.min.css">
    
    <link rel="stylesheet" href="../public/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    

  </head>
  <body class="hold-transition skin-green sidebar-mini">
    <div class="wrapper">

      <header class="main-header">

        <!-- Logo -->
        <a href="escritorio.php" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini"><b>SGI</b></span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg"><b>Gases Industriales</b></span>
        </a>

        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Navegación</span>
          </a>
          <!-- Navbar Right Menu -->
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              <!-- Messages: style can be found in dropdown.less-->
              
              <!-- User Account: style can be found in dropdown.less -->
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <img src="../files/usuarios/<?php echo $_COOKIE['imagen']; ?>" class="user-image" alt="User Image">
                  <span class="hidden-xs"><?php echo $_COOKIE['nombre']; ?></span>
                </a>
                <ul class="dropdown-menu">
                  <!-- User image -->
                  <li class="user-header">
                    <img src="../files/usuarios/<?php echo $_COOKIE['imagen']; ?>" class="img-circle" alt="User Image">
                    <p>
                      Sistemas
                      <small>Avedis</small>
                    </p>
                  </li>
                  
                  <!-- Menu Footer-->
                  <li class="user-footer">
                    
                    <div class="pull-right">
                      <a href="../ajax/usuario.php?op=salir" class="btn btn-default btn-flat">Cerrar</a>
                    </div>
                  </li>
                </ul>
              </li>
              
            </ul>
          </div>

        </nav>
      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">       
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header"></li>
            <?php
              if($_COOKIE['escritorio'] == 1)
              {
                echo 
                '<li>
                  <a href="escritorio.php">
                    <i class="fa fa-tasks"></i> <span>Escritorio</span>
                  </a>
                </li>';
              }

              if($_COOKIE['remitos'] == 1)
              {
                echo 
                '<li class="treeview">
                    <a href="#">
                    <i class="fa fa-book" aria-hidden="true"></i> <span>Remito</span>
                      <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                      <li><a href="ingreso.php"><i class="fa fa-circle-o"></i> Generar remito</a></li>
                      <li><a href="consultaremitos.php"><i class="fa fa-circle-o"></i> Consulta remitos</a></li>
                    </ul>
                  </li>'
                 ;
              }
			 
              
              if($_COOKIE['ABM'] == 1)
              {
                echo 
                '<li class="treeview">
                    <a href="#">
                      <i class="fa fa-archive" aria-hidden="true"></i> <span>Gestión</span>
                      <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                      <li><a href="cliente.php"><i class="fa fa-circle-o"></i> Clientes</a></li> 
                      <li><a href="producto.php"><i class="fa fa-circle-o"></i> Productos</a></li>
                      <li><a href="pventa.php"><i class="fa fa-circle-o"></i> Puntos de venta</a></li>                
                    </ul>
                  </li>'
                 ;
              }
              if($_COOKIE['acceso'] == 1)
              {
                echo 
                '<li class="treeview">
                    <a href="#">
                      <i class="fa fa-users"></i> <span>Acceso</span>
                      <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                      <li><a href="usuario.php"><i class="fa fa-circle-o"></i> Usuarios</a></li>
                      <li><a href="permiso.php"><i class="fa fa-circle-o"></i> Permisos</a></li>
                    </ul>
                  </li>'
                 ;
              }
            ?>                                
          
            <li>
              <a href="#">
                <i class="fa fa-plus-square"></i> <span>Ayuda</span>
                <small class="label pull-right bg-red">PDF</small>
              </a>
            </li>
            <li>
              <a href="#">
                <i class="fa fa-info-circle"></i> <span>Acerca De...</span>
                <small class="label pull-right bg-yellow">IT</small>
              </a>
            </li>
                        
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>