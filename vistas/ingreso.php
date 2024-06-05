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
            <div class="box-header with-border">
                    <h1 class="box-title" style="display:none;">Generar remitos <button class="btn btn-success" id="btnagregar" onclick="mostrarform(true)"><i class="fa fa-plus-circle"></i> Agregar</button></h1>
                        <div class="box-tools pull-right">
                    </div>
            </div>
            <div class="row" id="cabeceraRemito">
                <div class="col-md-3">
                    <h4>Detalles del remito</h4>
                </div>
                <div class="col-md-7"></div>
            </div>
            <div class="row" style="margin-top:10px;margin-right:0px;">
                <div class="col-md-12">
                    <div class="row">
                    <div class="col-md-4">
                            <div class="form-group">
                                <label for="puntoVenta">P. Venta</label>
                                <select id="puntoVenta" name="puntoVenta" class="form-control" style="font-size:14px;">
                                    <option value="">Selecciona uno</option>
                                    <!-- Aquí se agregarán las opciones dinámicamente -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="puntoEntrega">P. Entrega</label>
                                <select id="puntoEntrega" name="puntoEntrega" class="form-control" style="font-size:14px;">
                                    <option value="">Selecciona uno</option>
                                    <option value="AVEDIS">AVEDIS</option>
                                    <option value="REPARTO">REPARTO</option>
                                    <!-- Aquí se agregarán las opciones dinámicamente -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cliente">Nº Remito</label>
                                <input type="text" id="ncomprobante" name="ncomprobante" class="form-control">
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="cliente">Cliente</label>
                            <select id="cliente" name="cliente" class="form-control" style="font-size:14px;">
                                    <option value="">Selecciona uno</option>
                                    <!-- Aquí se agregarán las opciones dinámicamente -->
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="cliente">Fecha</label>
                            <input type="date" class="form-control" name="fecha" id="fecha" value="<?php //echo date("Y-m-d");?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                    <label for="estado">Clasificacion</label>
                    <div class="radio-group">
                            <label><input type="radio" name="estado" value="BB"> BB</label>
                            <label><input type="radio" name="estado" value="NN"> NN</label>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-right:0px;">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="detalles">Detalles</label>
                        <textarea id="detalles" name="detalles" class="form-control" rows="4"></textarea>
                    </div>
                </div>
            </div>
            <div class="row" id="cabeceraRemito">
                <div class="col-md-4">
                <h4>Detalles de los productos</h4>
                </div>
                <div class="col-md-5"></div>
                <div class="col-md-2">
                <button id="openModalBtn" class="btn btn-light" style="color:black;margin-top:2%;">Agregar productos</button>
                </div>
            </div>
            <div class="card-body" id="bodyCard2">
                    <div id="detallesProductos" style="margin-right:3%">
                        <!-- Aquí se agregarán las filas de campos dinámicamente -->
                    </div>
                </div>
                <br>
            <button id="enviarDatosBtn" class="btn btn-success">Confirmar remito</button>
            <button id="cambiarDatosBtn" class="btn btn-success" style="display:none;">Confirmar cambios</button>
            <button id="cancelarBtn" class="btn btn-danger" style="display:none;">Cancelar</button>

            <div class="modal" id="modalDetalles" tabindex="-1" role="dialog" aria-labelledby="modalDetallesLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDetallesLabel">Detalles del Producto</h5>
                    </div>
                    <div class="modal-body" id="modalDetallesBody">
                        <!-- Aquí se mostrarán los detalles del producto -->
                    </div>
                    <button type="button" onclick="cerrarModal()" class="btn btn-warning" style="font-size:14px;">Cerrar</button>
                </div>
            </div>
        </div>

        </div>
    </div>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="card">
                <div class="card-header text-center" id="headerCard">
                    <div class="form-row justify-content-center">
                        <div class="form-group mx-2">
                            <label for="tipoProducto">Tipo de Producto</label>
                            <select id="tipoProducto" name="tipoProducto" class="form-control" style="font-size:14px;">
                                <option value="">Selecciona un producto</option>
                                <!-- Aquí se agregarán las opciones dinámicamente -->
                            </select>
                        </div>
                        <div class="form-group mx-2">
                            <label for="propiedad">Tipo</label>
                            <div class="radio-group">
                                <label><input type="radio" name="propiedad" value="TER"> TER</label>
                                <label><input type="radio" name="propiedad" value="CIL"> CIL</label>
                            </div>
                        </div>
                        <div class="form-group mx-2">
                            <label for="cantidad">Cantidad</label>
                            <input type="text" name="cantidad" id="cantidad" class="form-control" style="font-size:14px;"> 
                        </div>
                        <div class="form-group mx-2">
                            <label for="propiedad2">Propiedad</label>
                            <div class="radio-group">
                                <label><input type="radio" name="propiedad2" value="NP"> NP</label>
                                <label><input type="radio" name="propiedad2" value="SP"> SP</label>
                            </div>
                        </div>
                        <div class="form-group mx-2">
                            <label for="accion">Accion</label>
                            <div class="radio-group">
                                <label><input type="radio" name="accion" value="E"> E</label>
                                <label><input type="radio" name="accion" value="D"> D</label>
                            </div>
                        </div>
                        <div class="form-group mx-2">
                            <button type="button" onclick="generarCampos()" class="btn btn-primary" style="font-size:14px; margin-top:16%;">Generar campos</button>
                        </div>
                    </div>
                </div>

                <div class="card-body" id="bodyCard">
                    <div id="camposExtra">
                        <!-- Aquí se agregarán las filas de campos dinámicamente -->
                    </div>
                </div>
                <div class="card-footer" id="headerFooter">
                    <div class="form-group">
                        <button id="cancelBtn" class="btn btn-danger" style="font-size:14px; margin-top:1%;margin-left:1%;">Cancelar</button>
                        <button id="confirmBtn" class="btn btn-success" style="font-size:14px; margin-top:1%;margin-left:1%;">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <?php
        require 'footer.php';
    ?>
    <script src="./scripts/ingreso.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   
<?php
        } else {
            require 'noacceso.php';
        }
    }

    ob_end_flush(); 
?>
