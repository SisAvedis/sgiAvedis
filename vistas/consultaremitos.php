<?php
    ob_start();
    session_start();

    if (!isset($_COOKIE["nombre"])) {
        header("Location: login.html");
    } else {
        require 'header.php';

        if ($_COOKIE['acceso'] == 1) {
?>
<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="../public/css/remitos.css">
  </head>
    <div class="content-wrapper">
        <div class="content">
            <div class="row" style="margin-top:10px;margin-right:0px;">
                
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-group">
                                <label for="cliente">Fecha inicial</label>
                                <input type="date" class="form-control" name="fechai" id="fechai" value="<?php //echo date("Y-m-d");?>">
                            </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                    <div class="form-group">
                                    <label for="cliente">Fecha final</label>
                                    <input type="date" class="form-control" name="fechaf" id="fechaf" value="<?php //echo date("Y-m-d");?>">
                                </div>
                            </div>
                        </div>  
                        <div class="col-md-4">
                            <div class="form-group">
                                <button class="btn btn-success" type="button" onclick="listarRemitos()" style="margin-top:11.5%;"><i class="fa fa-search"></i> Listar</button>
                            </div>
                        </div>
            </div>
            
            <div class="row" id="historial" style="display:none">

            
            <div class="row" id="cabeceraRemito">
                <div class="col-md-4">
                <h4>Historial de remitos</h4>
                </div>
                <div class="col-md-5"></div>
                <div class="col-md-2">
                </div>
            </div>
            <div class="card-body" id="bodyCard2">
                <div id="detallesRemitos" style="margin-right:3%">
                    <!-- Aquí se agregarán las filas de campos dinámicamente -->
                </div>
            </div>
               

            <div class="modal" id="modalDetalles" tabindex="-1" role="dialog" aria-labelledby="modalDetallesLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDetallesLabel">Detalles del remito</h5>
                    </div>
                    <div class="modal-body" id="modalDetallesBody">
                        <!-- Aquí se mostrarán los detalles del producto -->
                    </div>
                    <button type="button" onclick="cerrarModal()" class="btn btn-danger" style="font-size:14px;">Cerrar</button>
                </div>
            </div>
        </div>
        </div>
        <div id="detallesModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="cerrarDetallesModal()">&times;</span>
                <div id="detallesContainer"></div>
            </div>
        </div>

        <div id="masDetallesModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="cerrarMasDetallesModal()">&times;</span>
                <div id="masDetallesContainer"></div>
            </div>
        </div>

        </div>
    </div>

    

    
    <?php
        require 'footer.php';
    ?>
    <script src="./scripts/consultaremitos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   
<?php
        } else {
            require 'noacceso.php';
        }
    }

    ob_end_flush(); 
?>
