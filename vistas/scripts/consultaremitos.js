function listarRemitos() {
    var fechaInicial = document.getElementById("fechai").value;
    var fechaFinal = document.getElementById("fechaf").value;

    if (!fechaInicial || !fechaFinal) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Por favor, seleccione ambas fechas.',
        });
        return;
    }

    $.post(
        "../ajax/consultaremitos.php?op=listarRemitos",
        { fechai: fechaInicial, fechaf: fechaFinal },
        function(response) {
            try {
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }

                if (response.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.error,
                    });
                    return;
                }

                if (response.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Sin resultados',
                        text: 'No se encontraron remitos en el rango de fechas seleccionado.',
                    });
                    return;
                }

                $("#detallesRemitos").empty();
                
                var table = "<table class='table table-bordered'" +
                                "<thead>" +
                                    "<tr>" +
                                        "<th style='text-align: center;'>Fecha</th>" +
                                        "<th style='text-align: center;'>Cliente</th>" +
                                        "<th style='text-align: center;'>Punto de venta</th>" +
                                        "<th style='text-align: center;'>Acciones</th>" +
                                    "</tr>" +
                                "</thead>" +
                                "<tbody>";

                                response.forEach(function(item) {
                                    table += "<tr>" +
                                                "<td style='display: none;'>" + item.id + "</td>" + // Oculta la columna ID
                                                "<td style='text-align: center;'>" + item.fecha + "</td>" +
                                                "<td style='text-align: center;'>" + item.cliente + "</td>" +
                                                "<td style='text-align: center;'>" + item.pventa + "</td>" +
                                                "<td style='text-align: center;'><button class='btn btn-warning' onclick='editarRemito(" + item.id + ")'>Editar</button><button class='btn btn-light' onclick='verDetalles(" + item.id + ")' style='margin-left:10px;'>Detalles</button></td>" +
                                             "</tr>";
                                });
                                

                table += "</tbody></table>";

                $("#detallesRemitos").append(table);
                $("#historial").show();
            } catch (e) {
                console.error("Error parsing JSON:", e);
                console.error("Response received:", response);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al procesar la solicitud.',
                });
            }
        }
    );
}

function verDetalles(idRemito) {
    $.post(
        "../ajax/consultaremitos.php?op=verDetalles",
        { id: idRemito },
        function(response) {
            try {
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                let entregas = false;
                let devoluciones = false;
                let totalM = 0;
                for(var i=0;i<response.length;i++)
                {
                    if(response[i].accion == 'E')
                    {
                        entregas = true;
                        totalM += parseInt(response[i].capacidadSuma);
                    }
                    if(response[i].accion == 'D')
                    {
                        devoluciones = true;   
                    }
                }
                var detalles = "<table class='table'>";
                if(entregas)
                {
                    detalles += "<thead id='headDetalles'><tr><th colspan='7' style='text-align: center;'>Envases entregados: " + response[0].totalE + " | NP: " + response[0].totalNPE + " | SP: " +  response[0].totalSPE +  " | Volumen entregado: " + totalM + "m³" + "</th></tr>";
                }
                if(devoluciones)
                {
                    detalles += "<thead id='headDetalles'><tr><th colspan='7' style='text-align: center;'>Envases retirados: " + response[0].totalD + " | NP: " + response[0].totalNPD + " | SP: " +  response[0].totalSPD + "</th></tr>";

                }
                detalles += "<tr><th style='text-align: center;'>Producto</th><th style='text-align: center;'>Propiedad</th><th style='text-align: center;'>Accion</th><th style='text-align: center;'>Metros</th><th style='text-align: center;'>Tipo de envase</th><th style='text-align: center;'>Cantidad</th><th style='text-align: center;'>Detalles</th></tr></thead>";
                detalles += "<tbody>";
                response.forEach(function(item) {
                    detalles += "<tr>";
                    detalles += "<td style='text-align: center;'>" + item.nombre_tipo_producto + "</td>";
                    detalles += "<td style='text-align: center;'>" + item.propiedad + "</td>";
                    detalles += "<td style='text-align: center;'>" + item.accion + "</td>";
                    if(item.accion == 'E')
                        {
                            detalles += "<td style='text-align: center;'>" + item.capacidadSuma + "</td>";
                        }
                        else
                        {
                            detalles += "<td style='text-align: center;'>" + "-" + "</td>";
                        }
                    
                    detalles += "<td style='text-align: center;'>" + item.tipoenvase + "</td>";
                    detalles += "<td style='text-align: center;'>" + item.cantidad + "</td>";
                    detalles += "<td style='text-align: center;'><button class='btn btn-warning' onclick='verDetallesEspecificos(" + item.idtipo_producto + ", \"" + item.propiedad + "\", \"" + item.accion + "\", \"" + item.tipoenvase + "\", " + idRemito + ")'>Ver</button></td>";
                    detalles += "</tr>";
                });
                detalles += "</tbody></table>";

                $("#detallesContainer").html(detalles);
                $("#detallesModal").css("display", "block"); // Muestra el modal
            } catch (e) {
                console.error("Error parsing JSON:", e);
                console.error("Response received:", response);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al procesar la solicitud.',
                });
            }
        }
    );
}

function editarRemito(idRemito) {
    var fechaInicial = document.getElementById("fechai").value;
    var fechaFinal = document.getElementById("fechaf").value;
    $.post(
        "../ajax/consultaremitos.php?op=editarRemito",
        { id: idRemito },
        function(response) {
            try {
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                // Guarda el estado en sessionStorage
                sessionStorage.setItem('mostrarCambiarDatos', true);
                sessionStorage.setItem('idCambio', idRemito);
                sessionStorage.setItem('fechaInicial', fechaInicial);
                sessionStorage.setItem('fechaFinal', fechaFinal);
                
                // Redirigir a ingreso.php
                window.location.href = "ingreso.php";
            } catch (e) {
                console.error("Error parsing JSON:", e);
                console.error("Response received:", response);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al procesar la solicitud.',
                });
            }
        }
    );
}

function verDetallesEspecificos(idtipo_producto,propiedad,accion,tipoenvase, idRemito)
{
    $.post(
        "../ajax/consultaremitos.php?op=verMasDetalles",
        { idtipo_producto: idtipo_producto, propiedad: propiedad, accion: accion, tipoenvase: tipoenvase, id: idRemito },
        function(response) {
            try {
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }

                var detalles = "<table class='table' id='tableMasDetalles'>";
                detalles += "<thead id='headDetalles'><tr><th colspan='2' style='text-align: center;'>Producto: " + response[0].nombre_tipo_producto + " | Propiedad: " + propiedad + "</th></tr><tr><th style='text-align: center;'>Capacidad</th><th style='text-align: center;'>Nº Serie</th></tr></thead>";                detalles += "<tbody>";
                response.forEach(function(item) {
                    detalles += "<tr>";
                    detalles += "<td style='text-align: center;'>" + item.capacidad + "</td>";
                    detalles += "<td style='text-align: center;'>" + item.nserie + "</td>";
                    detalles += "</tr>";
                });
                detalles += "</tbody></table>";
                
                $("#masDetallesContainer").html(detalles);
                $("#masDetallesModal").css("display", "block"); // Muestra el modal
                $("#detallesModal").css("display", "none");
            } catch (e) {
                console.error("Error parsing JSON:", e);
                console.error("Response received:", response);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al procesar la solicitud.',
                });
            }
        }
    );
}

function cerrarDetallesModal() {
    $("#detallesModal").css("display", "none"); // Oculta el modal
}

function cerrarMasDetallesModal() {
    $("#masDetallesModal").css("display", "none"); // Oculta el modal
    $("#detallesModal").css("display", "block");
}

function cerrarModal() {
    $("#modalDetalles").modal("hide");
}


document.addEventListener('DOMContentLoaded', function() {
    // Leer el estado de sessionStorage
            const fechaInicialR = sessionStorage.getItem('fechaInicial');
            const fechaFinalR = sessionStorage.getItem('fechaFinal');
            var fechaInicial = document.getElementById("fechai");
            var fechaFinal = document.getElementById("fechaf");

            if (fechaInicialR && fechaFinalR) {

                fechaInicial.value = fechaInicialR;
                fechaFinal.value = fechaFinalR;
                listarRemitos();
                // Limpiar sessionStorage
                sessionStorage.removeItem('fechaInicial');
                sessionStorage.removeItem('fechaFinal');
            }
        });

        function subirDocumento() {
            var input = document.createElement('input');
            input.type = 'file';
            input.accept = '.xlsx,.xls';
            input.onchange = function(event) {
                var archivo = event.target.files[0]; 
                if (archivo) {
                    console.log('Archivo seleccionado:', archivo.name, archivo.size);
                    alert('Archivo seleccionado: ' + archivo.name);
                    leerExcel(archivo);
                }
            };
            input.click();
        }

        function leerExcel(archivo) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var data = new Uint8Array(e.target.result);
                var workbook = XLSX.read(data, { type: 'array' });

                var hoja = workbook.Sheets[workbook.SheetNames[0]];
                var jsonData = XLSX.utils.sheet_to_json(hoja, { header: 1 });
                procesarDatos(jsonData);
            };
            reader.readAsArrayBuffer(archivo);
        }

        function procesarDatos(data) {
            var remitos = [];
            var fechaActual = new Date();
            var fechaActualFormatted = fechaActual.getFullYear() + '-' + 
                                        ('0' + (fechaActual.getMonth() + 1)).slice(-2) + '-' + 
                                        ('0' + fechaActual.getDate()).slice(-2) + ' ' + 
                                        ('0' + fechaActual.getHours()).slice(-2) + ':' + 
                                        ('0' + fechaActual.getMinutes()).slice(-2) + ':' + 
                                        ('0' + fechaActual.getSeconds()).slice(-2);
            
            for (var i = 1; i < data.length; i++) {
                var fila = data[i];
                var pventa = '';
                
                var numero = parseFloat(fila[1]);
                var pventa = parseFloat(fila[3]);
                
                if (numero > 300000 && fila[3] === 'TECNO') {
                    pventa = 'AV-00007';
                } else if (numero < 10000 && numero > 999 && fila[3] === 'TECNO') {
                    pventa = 'AV-00011';
                } else if (fila[3] === 'RESPITEC') {
                    pventa = 'RE-00001';
                } else if(fila[3] === 'IAG'){
                    pventa = 'IAG-00001';
                } else {
                    pventa = '-';
                }
        
                var remito = {
                    idusuario: '-',
                    clasificacion: fila[5], // CONCEPTO
                    numero: fila[1],        // REMITO
                    fecha_hora: fechaActualFormatted,
                    fecha_remito: fila[0],  // FECHA
                    estado: 1,
                    cliente: fila[2],       // RAZON SOCIAL
                    informacion: '-',
                    pventa: pventa          // PVENTA
                };
                remitos.push(remito);
            }
        
            enviarDatosAlServidor(remitos);
        }
        
        
        
        

        function enviarDatosAlServidor(remitos) {
            fetch('../ajax/consultaremitos.php?op=importarDocumento', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(remitos)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Éxito:', data);
                    alert('Datos guardados correctamente');
                } else {
                    console.error('Error en la respuesta del servidor:', data.message);
                    alert('Error: ' + data.message);
                }
            })
            .catch((error) => {
                console.error('Error en la solicitud:', error);
                alert('Error al guardar los datos');
            });
        }
        
        