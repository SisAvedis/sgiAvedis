function init() {
    listarProductos();
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
                    options += '<option value="' +  producto.idtipo_producto + '">' + producto.nombre + "-" + producto.codigo + '</option>';
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
                    options += '<option value="' + puntoVenta.prefijo + "-" + puntoVenta.numero + '">' + puntoVenta.prefijo + "-" + puntoVenta.numero + '</option>';
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

                data.forEach(function(producto) {
                    var fila = $('<tr></tr>');

                    fila.append('<td>' + producto.nombre_producto + '</td>');
                    fila.append('<td>' + producto.tipoenvase + '</td>');
                    fila.append('<td>' + producto.propiedad + '</td>');
                    fila.append('<td>' + producto.cantidad + '</td>');
                    fila.append('<td>' + producto.accion + '</td>');

                    var botonOjo = $('<button class="btn btn-warning">Detalle</button>');
                    botonOjo.on('click', function() {
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
            if (response.success) {
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
function mostrarDetalles(data) {
    $('#modalDetallesBody').empty();

    var table = $('<table class="table text-center align-middle"></table>');
    var thead = $('<thead></thead>').appendTo(table);
    var tbody = $('<tbody></tbody>').appendTo(table);
    
    var headers = ['N° Serie', 'Capacidad', 'Acciones'];
    var headerRow = $('<tr></tr>');
    headers.forEach(function(headerText) {
        headerRow.append('<th>' + headerText + '</th>');
    });
    thead.append(headerRow);

    data.forEach(function(detalle) {
        var fila = $('<tr></tr>');

        fila.append('<td>' + detalle.nserie + '</td>');
        fila.append('<td>' + detalle.capacidad + '</td>');

        var columnaAcciones = $('<td></td>');
        var botonEliminar = $('<button class="btn btn-danger">Eliminar</button>').click(function() {
            eliminarProducto(detalle.nserie);
        });
        columnaAcciones.append(botonEliminar);
        fila.append(columnaAcciones);

        tbody.append(fila);
    });

    $('#modalDetallesBody').append(table);
    $('#detalleProductoInfo').remove();

    var producto = data[0].nombre_producto;
    var envase = data[0].tipoenvase;
    var propiedad = data[0].propiedad;
    var accion = data[0].accion;
    $('<div id="detalleProductoInfo">Producto: ' + producto + ' | Envase: ' + envase +  '<br>' + ' Propiedad: ' + propiedad + ' | Accion: ' + accion + '</div>').insertAfter('#modalDetallesLabel');

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

function cerrarModal() {
    $('#modalDetalles').modal('hide');
}

function generarCampos() {
    var cantidad = parseInt($('#cantidad').val());

    if (!isNaN(cantidad) && cantidad > 0) {
        $('#camposExtra').empty();
        var tipoProducto = $('#tipoProducto').val();
        $.ajax({
            url: '../ajax/ingreso.php?op=obtenerCapacidad',
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

                    for (var i = 0; i < cantidad; i++) {
                        var fila = '<div class="form-row">' +
                                    '<div class="form-group col-md-6">' +
                                        '<label for="nserie' + i + '">Nº Serie</label>' +
                                        '<input type="number" id="nserie' + i + '" name="nserie' + i + '" class="form-control autocompletar" min="1" data-index="' + i + '">' +
                                        '<div id="suggestions' + i + '" class="suggestions"></div>' +
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

                    $('.autocompletar').on('input', handleAutocomplete);
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

function handleAutocomplete() {
    var index = $(this).data('index');
    var query = $(this).val();
    if (query.length > 0) {
        $.ajax({
            url: '../ajax/ingreso.php?op=buscarNserie',
            type: 'POST',
            data: {
                query: query
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var suggestions = response.data;
                    var suggestionsList = '';
                    suggestions.forEach(function(suggestion) {
                        suggestionsList += '<div class="suggestion-item" data-value="' + suggestion + '">' + suggestion + '</div>';
                    });
                    $('#suggestions' + index).html(suggestionsList).show();
                    
                    $('.suggestion-item').on('click', function() {
                        var value = $(this).data('value');
                        $('#nserie' + index).val(value);
                        $('#suggestions' + index).hide();
                        $.ajax({
                            url: '../ajax/ingreso.php?op=completarCapacidad',
                            type: 'POST',
                            data: {
                                value: value
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    var originalOptions = $('#capacidad' + index).html();
                                    $('#capacidad' + index).html(originalOptions + '<option value="' + response.capacidad + '">' + response.capacidad + '</option>');
                                    $('#capacidad' + index).val(response.capacidad);
                                } else {
                                    console.error('Error al completar la capacidad:', response.message);
                                }
                            },                            
                            error: function(xhr, status, error) {
                                console.error('Error en la solicitud AJAX:', error);
                            }
                        });
                    });
                } else {
                    $('#suggestions' + index).hide();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
            }
        });
    } else {
        $('#suggestions' + index).hide();
    }
}



$(document).on('click', function(event) {
    if (!$(event.target).closest('.form-group').length) {
        $('.suggestions').hide();
    }
});

window.onbeforeunload = function(event) {
    $.ajax({
        url: '../ajax/ingreso.php?op=vaciarTemporal',
        type: 'POST',
        success: function(response) {
            console.log('Petición AJAX ejecutada con éxito:', response);
        },
        error: function(xhr, status, error) {
            console.error('Error en la petición AJAX:', error);
        }
    });
};

document.addEventListener('DOMContentLoaded', function() {
    const mostrarCambiarDatos = sessionStorage.getItem('mostrarCambiarDatos');
    const idCambio = sessionStorage.getItem('idCambio');
    listarPuntosVenta();
    listarClientes();
            if (mostrarCambiarDatos) {
                document.getElementById('enviarDatosBtn').style.display = 'none';
                document.getElementById('cambiarDatosBtn').style.display = 'inline-block';
                document.getElementById('cancelarBtn').style.display = 'inline-block';
                $.ajax({
                    url: '../ajax/ingreso.php?op=mostrarRemito',
                    data: { idRemito: idCambio },
                    type: 'POST',
                    success: function(response) {
                        
                        var remitoData = JSON.parse(response);
                        if (remitoData.clasificacion === 'BB') {
                            document.querySelector('input[name="estado"][value="BB"]').checked = true;
                        } else if (remitoData.clasificacion === 'NN') {
                            document.querySelector('input[name="estado"][value="NN"]').checked = true;
                        }
                        var selectPuntoVenta = document.getElementById('puntoVenta');
                        var selectPuntoEntrega = document.getElementById('puntoEntrega');
                        var selectCliente = document.getElementById('cliente');
                        var puntoVentaRemito = remitoData.pventa;
                        var puntoEntregaRemito = remitoData.pentrega;
                        var clienteRemito = remitoData.cliente;
                        for (var i = 1; i < selectPuntoVenta.options.length; i++) {
                            var option = selectPuntoVenta.options[i];
                            if (option.value == puntoVentaRemito) {
                                option.selected = true;
                                break;
                            }
                        }
                        for (var i = 1; i < selectCliente.options.length; i++) {
                            var option = selectCliente.options[i];
                            if (option.value == clienteRemito) {
                                option.selected = true;
                                break;
                            }
                        }
                        for (var i = 1; i < selectPuntoEntrega.options.length; i++) {
                            var option = selectPuntoEntrega.options[i];
                            if (option.value == puntoEntregaRemito) {
                                option.selected = true;
                                break;
                            }
                        }
                        document.getElementById('ncomprobante').value = remitoData.numero;
                        var fecha = new Date(remitoData.fecha_remito);
                        var formattedDate = fecha.toISOString().split('T')[0];
                        document.getElementById('fecha').value = formattedDate;
                    },
                    error: function(xhr, status, error) {
                        
                    }
                });

                sessionStorage.removeItem('mostrarCambiarDatos');
            }else{
                listarPuntosVenta();
                listarClientes();
            }
        });

$(document).ready(function() {
    init();

    var openModalBtn = $('#openModalBtn');
    var modal = $('#myModal');
    var closeModalBtn = modal.find('.close');
    var cancelBtn = $('#cancelBtn');
    var confirmBtn = $('#confirmBtn');
    var $cancelarBtn = $('#cancelarBtn');
        
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

    $cancelarBtn.on('click', function() {
        sessionStorage.removeItem('idCambio');
        window.location.href = "consultaremitos.php";
    });

    confirmBtn.on('click', function() {
        
        var tipoProducto = $('#tipoProducto').val();
        var propiedad = $('input[name="propiedad"]:checked').val();
        var propiedad2 = $('input[name="propiedad2"]:checked').val();
        var cantidad = parseInt($('#cantidad').val());
        var accion = $('input[name="accion"]:checked').val();
        var campos = [];
    
        if (!propiedad || !propiedad2 || !accion) {
            Swal.fire({
                icon: 'warning',
                title: 'Campos sin seleccionar',
                text: 'Por favor, seleccione una opción en todos los campos de Tipo, Propiedad y Accion antes de continuar.',
                customClass: {
                    container: 'alert-z-index'
                },
            });
            return;
        }
    
        if (!tipoProducto) {
            Swal.fire({
                icon: 'warning',
                title: 'Campo sin seleccionar',
                text: 'Por favor, seleccione una opción en el campo "Tipo de Producto" antes de continuar.',
                customClass: {
                    container: 'alert-z-index'
                },
            });
            return; 
        }
        
        if (isNaN(cantidad) || cantidad <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Cantidad inválida',
                text: 'Por favor, ingrese una cantidad válida mayor que cero.',
                customClass: {
                    container: 'alert-z-index'
                },
            });
            return;
        }
    
        for (var i = 0; i < cantidad; i++) {
            var nserie = $('#nserie' + i).val();
            var capacidad = $('#capacidad' + i).val();
    
            if (nserie === '' || capacidad === '' || isNaN(capacidad) || isNaN(nserie)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos inválidos',
                    text: 'Por favor, complete todos los campos de número de serie y capacidad con valores numéricos antes de continuar.',
                    customClass: {
                        container: 'alert-z-index'
                    },
                });
                return; 
            }
    
            campos.push({ nserie: nserie, capacidad: capacidad });
        }
    
        $.ajax({
            url: '../ajax/ingreso.php?op=guardarTemporal',
            type: 'POST',
            data: {
                tipoProducto: tipoProducto,
                propiedad: propiedad,
                propiedad2: propiedad2,
                accion: accion,
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

   
    $('#cambiarDatosBtn').on('click',function(){
        var cliente = $('#cliente').val();
        var ncomprobante = $('#ncomprobante').val();
        var fecha = $('#fecha').val();
        var detalles = $('#detalles').val();
        var estado = $('input[name="estado"]:checked').val();
        var puntoVenta = $('#puntoVenta').val();
        const idCambio = sessionStorage.getItem('idCambio');
    
        if (!cliente || !ncomprobante || !fecha || !puntoVenta || !estado) {
            Swal.fire({
                icon: 'warning',
                title: 'Campos obligatorios vacíos',
                text: 'Por favor, complete todos los campos obligatorios antes de continuar.',
                customClass: {
                    container: 'alert-z-index'
                },
            });
            return;
        }

        $.ajax({
            url: '../ajax/ingreso.php?op=editarRemito',
            data: { idRemito: idCambio },
            type: 'POST',
            data: {
                cliente: cliente,
                detalles: detalles,
                ncomprobante: ncomprobante,
                estado: estado,
                puntoVenta: puntoVenta,
                fecha: fecha,
                idCambio: idCambio
            },
            success: function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    Swal.fire({
                        position: "top-end",
                        icon: "success",
                        title: "Remito editado con éxito",
                        showConfirmButton: false,
                        timer: 1500
                    });
                    sessionStorage.removeItem('idCambio');
                    window.location.href = "consultaremitos.php";
                } else {
                    Swal.fire({
                        icon: "Error",
                        text: "El remito no pudo cargarse"
                    });
                    console.error("Error al enviar datos a detalle_remito:", response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la petición AJAX:', error);
            }
        });
    });

    $('#enviarDatosBtn').on('click', function() {
        
        var cliente = $('#cliente').val();
        var ncomprobante = $('#ncomprobante').val();        
        var fecha = $('#fecha').val();
        var detalles = $('#detalles').val();
        var estado = $('input[name="estado"]:checked').val();
        var puntoVenta = $('#puntoVenta').val();
        var puntoEntrega = $('#puntoEntrega').val();
    
        if (!cliente || !ncomprobante || !fecha || !puntoVenta || !estado || !puntoEntrega) {
            Swal.fire({
                icon: 'warning',
                title: 'Campos obligatorios vacíos',
                text: 'Por favor, complete todos los campos obligatorios antes de continuar.',
                customClass: {
                    container: 'alert-z-index'
                },
            });
            return; 
        }
    
        $.ajax({
            url: '../ajax/ingreso.php?op=enviarDatosDetalleRemito',
            type: 'POST',
            data: {
                cliente: cliente,
                detalles: detalles,
                ncomprobante: ncomprobante,
                estado: estado,
                puntoVenta: puntoVenta,
                puntoEntrega: puntoEntrega,
                fecha: fecha
            },
            success: function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    Swal.fire({
                        position: "top-end",
                        icon: "success",
                        title: "Remito cargado con éxito",
                        showConfirmButton: false,
                        timer: 1500
                    });
                    // Limpiar los campos si la operación fue exitosa
                    $('#cliente').val('');
                    $('#ncomprobante').val('');
                    $('#fecha').val('');
                    $('#detalles').val('');
                    $('input[name="estado"]:checked').prop('checked', false);
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
