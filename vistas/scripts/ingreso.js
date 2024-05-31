function init() {
    listarProductos();
    listarPuntosVenta();
    listarClientes();
    actualizarProductos();
}
function listarClientes(){
    $.ajax({
        url: '../ajax/ingreso.php?op=listarClientes',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var options = '<option value="">Seleccione uno</option>';
                response.data.forEach(function(cliente) {
                    options += '<option value="' +  cliente.idcliente + '">' + cliente.nombre + '</option>';
                });
                $('#cliente').html(options);
                
            } else {
                console.error("Error al cargar clientes:", response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar clientes:", error);
        }
    });
}

function listarProductos() {
    $.ajax({
        url: '../ajax/ingreso.php?op=listarProductos',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var options = '<option value="">Seleccione uno</option>';
                response.data.forEach(function(producto) {
                    //alert("ID: " + producto.idtipo_producto + " Nombre: " + producto.nombre);
                    options += '<option value="' +  producto.idtipo_producto + '">' + producto.nombre + '</option>';
                });
                $('#tipoProducto').html(options);
                
            } else {
                console.error("Error al cargar productos:", response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar productos:", error);
        }
    });
}

function listarPuntosVenta() {
    $.ajax({
        url: '../ajax/ingreso.php?op=listarPuntosVenta',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var options = '<option value="">Seleccione uno</option>';
                response.data.forEach(function(puntoVenta) {
                    // Usar el prefijo en lugar del nombre para las opciones
                    options += '<option value="' + puntoVenta.prefijo + '">' + puntoVenta.prefijo + " - " + puntoVenta.numero + '</option>';
                });
                $('#puntoVenta').html(options);
                
            } else {
                console.error("Error al cargar puntos de venta:", response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar puntos de venta:", error);
        }
    });
}


function actualizarProductos(){
    $.ajax({
        url: '../ajax/ingreso.php?op=obtenerProductosAgrupados',
        type: 'GET',
        success: function(response) {
            console.log("Raw response from server:", response);
            try {
                var data = JSON.parse(response);
                console.log("Parsed data:", data);

                if (Array.isArray(data) && data.length > 0) {
                    data = data[0];
                }

                if (!Array.isArray(data)) {
                    console.error("Expected an array but got:", data);
                    return;
                }

                var detallesProductos = $('#detallesProductos');
                detallesProductos.empty();

                var table = $('<table class="table text-center align-middle"></table>');
                var thead = $('<thead></thead>').appendTo(table);
                var tbody = $('<tbody></tbody>').appendTo(table);

                var headers = ['Producto', 'Envase', 'Propiedad', 'Cantidad', 'Acción'];
                var headerRow = $('<tr></tr>');
                headers.forEach(function(headerText) {
                    headerRow.append('<th>' + headerText + '</th>');
                });
                thead.append(headerRow);

                // Llenar la tabla con los datos
                data.forEach(function(producto) {
                    var fila = $('<tr></tr>');

                    fila.append('<td>' + producto.nombre_producto + '</td>');
                    fila.append('<td>' + producto.tipoenvase + '</td>');
                    fila.append('<td>' + producto.propiedad + '</td>');
                    fila.append('<td>' + producto.cantidad + '</td>');
                    fila.append('<td>' + producto.accion + '</td>');

                    // Agregar botón con ícono de ojo
                    var botonOjo = $('<button class="btn btn-warning">Detalle</button>');
                    botonOjo.on('click', function() {
                        // Obtener datos de la fila
                        var productoSeleccionado = fila.find('td:eq(0)').text();
                        var envaseSeleccionado = fila.find('td:eq(1)').text();
                        var propiedadSeleccionada = fila.find('td:eq(2)').text();
                        var accionSeleccionada = fila.find('td:eq(4)').text();
                        verDetalle(productoSeleccionado, envaseSeleccionado, propiedadSeleccionada, accionSeleccionada);
                    });
                    var columnaBoton = $('<td></td>');
                    columnaBoton.append(botonOjo);
                    fila.append(columnaBoton);

                    tbody.append(fila);
                });

                detallesProductos.append(table);
            } catch (e) {
                console.error("Failed to parse JSON:", e);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error al obtener los productos agrupados:", error);
        }
    });
}

function verDetalle(producto, envase, propiedad, accion) {
    // Realizar la petición AJAX para obtener los detalles
    $.ajax({
        url: '../ajax/ingreso.php?op=obtenerDetallesProducto',
        type: 'POST',
        dataType: 'json',
        data: {
            producto: producto,
            envase: envase,
            propiedad: propiedad,
            accion: accion
        },
        success: function(response) {
            // Verificar si se recibieron datos correctamente
            if (response.success) {
                // Mostrar los detalles en un modal
                mostrarDetalles(response.data);
            } else {
                console.error("Error al obtener los detalles:", response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error al obtener los detalles:", error);
        }
    });
}

// Función para mostrar los detalles en el modal
function mostrarDetalles(data) {
    // Limpiar el contenido del modal antes de agregar nuevos detalles
    $('#modalDetallesBody').empty();

    // Crear una tabla para mostrar los detalles
    var table = $('<table class="table text-center align-middle"></table>');
    var thead = $('<thead></thead>').appendTo(table);
    var tbody = $('<tbody></tbody>').appendTo(table);
    
    // Crear las cabeceras de la tabla
    var headers = ['N° Serie', 'Capacidad', 'Acciones'];
    var headerRow = $('<tr></tr>');
    headers.forEach(function(headerText) {
        headerRow.append('<th>' + headerText + '</th>');
    });
    thead.append(headerRow);

    // Llenar la tabla con los detalles recibidos
    data.forEach(function(detalle) {
        var fila = $('<tr></tr>');

        fila.append('<td>' + detalle.nserie + '</td>');
        fila.append('<td>' + detalle.capacidad + '</td>');

        // Agregar columna de acciones con botón de eliminar
        var columnaAcciones = $('<td></td>');
        var botonEliminar = $('<button class="btn btn-danger">Eliminar</button>').click(function() {
            eliminarProducto(detalle.nserie);
        });
        columnaAcciones.append(botonEliminar);
        fila.append(columnaAcciones);

        tbody.append(fila);
    });

    // Agregar la tabla al cuerpo del modal
    $('#modalDetallesBody').append(table);

    // Eliminar el texto anterior antes de agregar uno nuevo
    $('#detalleProductoInfo').remove();

    // Agregar el subtítulo con el producto, envase y propiedad
    var producto = data[0].nombre_producto;
    var envase = data[0].tipoenvase;
    var propiedad = data[0].propiedad;
    var accion = data[0].accion;
    $('<div id="detalleProductoInfo">Producto: ' + producto + ' | Envase: ' + envase + ' | Propiedad: ' + propiedad + ' | Accion: ' + accion + '</div>').insertAfter('#modalDetallesLabel');

    // Mostrar el modal
    $('#modalDetalles').css('display', 'block');
    $('#modalDetalles').modal('show').css('z-index', '1050');

    
}


function eliminarProducto(nserie)
{
    Swal.fire({
        title: "¿Estas seguro de eliminar el producto?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "No",
        confirmButtonText: "Si"
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire({
            title: "¡Eliminado!",
            text: "El producto fue eliminado con exito.",
            icon: "success",
            
          });
          $.ajax({
            url: '../ajax/ingreso.php?op=eliminarProductoIndividual',
            type: 'POST',
            dataType: 'json',
            data: {
                nserie: nserie
            },
            success: function(response) {
                // Verificar si se recibieron datos correctamente
                if (!response.success) {
                    console.error("Error al obtener los detalles:", response.message);
                } 
            },
            error: function(xhr, status, error) {
                console.error("Error al obtener los detalles:", error);
            }
        });
        cerrarModal();
        actualizarProductos();
        }
      });
    
}

// Función para cerrar el modal
function cerrarModal() {
    $('#modalDetalles').modal('hide');
}



function generarCampos() {
    var cantidad = parseInt($('#cantidad').val());

    if (!isNaN(cantidad) && cantidad > 0) {
        $('#camposExtra').empty();
        var tipoProducto = $('#tipoProducto').val();
        // Realizar la solicitud AJAX para obtener las capacidades desde la base de datos
        $.ajax({
            url: '../ajax/ingreso.php?op=obtenerCapacidad', // Ruta del archivo PHP que manejará la solicitud AJAX
            type: 'POST',
            data: {
                producto: tipoProducto
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var capacidades = response.data;
                    var options = '<option value="">Seleccione una capacidad</option>';

                    capacidades.forEach(function(capacidad) {
                        options += '<option value="' + capacidad + '">' + capacidad + '</option>';
                    });

                    // Generar las filas del formulario con los select de capacidad
                    for (var i = 0; i < cantidad; i++) {
                        var fila = '<div class="form-row">' +
                                    '<div class="form-group col-md-6">' +
                                        '<label for="nserie' + i + '">Nº Serie</label>' +
                                        '<input type="number" id="nserie' + i + '" name="nserie' + i + '" class="form-control" min="1">' +
                                    '</div>' +
                                    '<div class="form-group col-md-6">' +
                                        '<label for="capacidad' + i + '">Capacidad</label>' +
                                        '<select id="capacidad' + i + '" name="capacidad' + i + '" class="form-control">' +
                                            options +
                                        '</select>' +
                                    '</div>' +
                                '</div>';

                        $('#camposExtra').append(fila);
                    }
                } else {
                    alert('Error al obtener las capacidades desde la base de datos.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
            }
        });
    } else {
        alert('Por favor ingresa un número válido mayor a 0 en el campo de cantidad.');
    }
}






$(document).ready(function() {
    init();

    var openModalBtn = $('#openModalBtn');
    var modal = $('#myModal');
    var closeModalBtn = modal.find('.close');
    var cancelBtn = $('#cancelBtn');
    var confirmBtn = $('#confirmBtn');

    openModalBtn.on('click', function() {
        modal.show();
        listarProductos();
    });

    closeModalBtn.on('click', function() {
        modal.hide();
    });

    cancelBtn.on('click', function() {
        modal.hide();
    });

    confirmBtn.on('click', function() {
        var tipoProducto = $('#tipoProducto').val();
        var propiedad = $('input[name="propiedad"]:checked').val();
        var propiedad2 = $('input[name="propiedad2"]:checked').val();
        var cantidad = parseInt($('#cantidad').val());
        var accion = $('input[name="accion"]:checked').val();
        var campos = [];

        for (var i = 0; i < cantidad; i++) {
            var nserie = $('#nserie' + i).val();
            var capacidad = $('#capacidad' + i).val();
            campos.push({ nserie: nserie, capacidad: capacidad });
        }
        $.ajax({
            url: '../ajax/ingreso.php?op=guardarTemporal',
            type: 'POST',
            data: {
                tipoProducto: tipoProducto,
                propiedad: propiedad,
                propiedad2: propiedad2,
                accion:accion,
                campos: campos
            },
            success: function(response) {

                response = JSON.parse(response);
                if (response.success) {
                    $('#tipoProducto').val(''); 
                    $('input[name="propiedad"]:checked').prop('checked', false);
                    $('input[name="propiedad2"]:checked').prop('checked', false);
                    $('input[name="accion"]:checked').prop('checked', false);
                    $('#cantidad').val('1');
                    for (var i = 0; i < cantidad; i++) {
                        $('#nserie' + i).val('');
                        $('#capacidad' + i).val('');
                    }
                    generarCampos();
                    actualizarProductos();
                    modal.hide();
                } else {
                    console.error("Error al guardar datos:", response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error al guardar datos:", error);
            }
        });
    });

   
    $('#enviarDatosBtn').on('click', function() {
        var cliente = $('#cliente').val();
        var ncomprobante = $('#ncomprobante').val();
        var fecha = $('#fecha').val();
        var detalles = "Remito a Uds. la siguiente mercadería: ";

        $('#detallesProductos tbody tr').each(function() {
            var producto = $(this).find('td:nth-child(1)').text(); 
            var cantidad = $(this).find('td:nth-child(4)').text(); 
            var propiedad = $(this).find('td:nth-child(3)').text();
            
            detalles += 'Cargas de ' + producto + ' x ' + cantidad + ' ' + propiedad + ', ';
        });

        detalles = detalles.slice(0, -2);
        $('#detalles').val(detalles);
        
        $.ajax({
            url: '../ajax/ingreso.php?op=enviarDatosDetalleRemito',
            type: 'POST',
            data: {
                cliente: cliente,
                detalles: detalles,
                ncomprobante: ncomprobante,
                fecha: fecha
            },
            success: function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    Swal.fire({
                        position: "top-end",
                        icon: "success",
                        title: "Remito cargado con exito",
                        showConfirmButton: false,
                        timer: 1500
                      });
                    $('#cliente').val('');
                    $('#detalles').val('');
                    $('#ncomprobante').val('');
                    actualizarProductos();
                } else {
                    Swal.fire({
                        icon: "Error",
                        text: "El remito no pudo cargarse"
                      });
                    console.error("Error al enviar datos a detalle_remito:", response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error al enviar datos a detalle_remito:", error);
            }
        });
    });
});
