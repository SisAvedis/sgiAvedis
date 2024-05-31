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
                
                var table = "<table class='table table-bordered'>" +
                                "<thead>" +
                                    "<tr>" +
                                        "<th style='text-align: center;'>Fecha</th>" +
                                        "<th style='text-align: center;'>Cliente</th>" +
                                        "<th style='text-align: center;'>Acciones</th>" +
                                    "</tr>" +
                                "</thead>" +
                                "<tbody>";

                                response.forEach(function(item) {
                                    table += "<tr>" +
                                                "<td style='display: none;'>" + item.id + "</td>" + // Oculta la columna ID
                                                "<td style='text-align: center;'>" + item.fecha + "</td>" +
                                                "<td style='text-align: center;'>" + item.cliente + "</td>" +
                                                "<td style='text-align: center;'><button class='btn btn-warning' onclick='verDetalles(" + item.id + ")'>Ver detalles</button></td>" +
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

                var detalles = "<table class='table'>";
                detalles += "<thead><tr><th style='text-align: center;'>Producto</th><th style='text-align: center;'>Propiedad</th><th style='text-align: center;'>Accion</th><th style='text-align: center;'>Metros</th><th style='text-align: center;'>Tipo de envase</th><th style='text-align: center;'>Detalles</th></tr></thead>";
                detalles += "<tbody>";
                response.forEach(function(item) {
                    detalles += "<tr>";
                    detalles += "<td style='text-align: center;'>" + item.nombre_tipo_producto + "</td>";
                    detalles += "<td style='text-align: center;'>" + item.propiedad + "</td>";
                    detalles += "<td style='text-align: center;'>" + item.accion + "</td>";
                    detalles += "<td style='text-align: center;'>" + item.capacidadSuma + "</td>";
                    detalles += "<td style='text-align: center;'>" + item.tipoenvase + "</td>";
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

                var detalles = "<table class='table'>";
                detalles += "<thead><tr><th style='text-align: center;'>Capacidad</th><th style='text-align: center;'>Nº Serie</th></tr></thead>";
                detalles += "<tbody>";
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
