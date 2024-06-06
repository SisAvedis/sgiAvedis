function listarRemitos() {
    var fechaInicial = document.getElementById("fechai");
    var fechaFinal = document.getElementById("fechaf");
    var nremitoBuscado = document.getElementById("nremito");

    if (nremitoBuscado.value != '') {
        $.post(
            "../ajax/consultaremitos.php?op=listarRemitos",
            { remito: nremitoBuscado.value },
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
                            text: 'No hay un remito con el numero indicado.',
                        });
                        return;
                    }

                    $("#detallesRemitos").empty();

                    var table = "<table class='table table-bordered'>" +
                        "<thead>" +
                        "<tr>" +
                        "<th style='text-align: center;'>Fecha</th>" +
                        "<th style='text-align: center;'>Nº Remito</th>" +
                        "<th style='text-align: center;'>Clasificación</th>" +
                        "<th style='text-align: center;'>Cliente</th>" +
                        "<th style='text-align: center;'>Punto de venta</th>" +
                        "<th style='text-align: center;'>Acciones</th>" +
                        "</tr>" +
                        "</thead>" +
                        "<tbody>";

                    response.forEach(function(item) {
                        table += "<tr>" +
                            "<td style='display: none;'>" + item.id + "</td>" +
                            "<td style='text-align: center;'>" + item.fecha + "</td>" +
                            "<td style='text-align: center;'>" + item.numero + "</td>" +
                            "<td style='text-align: center;'>" + item.clasificacion + "</td>" +
                            "<td style='text-align: center;'>" + item.cliente + "</td>" +
                            "<td style='text-align: center;'>" + item.pventa + "</td>" +
                            "<td style='text-align: center;'><button class='btn btn-warning' onclick='editarRemito(" + item.id + ")' data-toggle='tooltip' title='Editar'><i class='fa fa-pencil' aria-hidden='true'></i></button><button class='btn btn-light' onclick='verDetalles(" + item.id + ")' style='margin-left:10px;' data-toggle='tooltip' title='Ver detalle'><i class='fa fa-eye' aria-hidden='true'></i></button><button class='btn btn-danger' onclick='eliminarRemito(" + item.id + ")' style='margin-left:10px;' data-toggle='tooltip' title='Eliminar'><i class='fa fa-trash' aria-hidden='true'></i></button></td>" +
                            "</tr>";
                    });

                    table += "</tbody></table>";

                    $("#detallesRemitos").append(table);
                    $("#historial").show();
                    nremitoBuscado.value = '';
                    fechaInicial.value = '';
                    fechaFinal.value = '';
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
    } else {
        if (!fechaInicial.value || !fechaFinal.value) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Por favor, seleccione ambas fechas o un Nº de remito para filtrar.',
            });
            return;
        }
        $.post(
            "../ajax/consultaremitos.php?op=listarRemitos",
            { fechai: fechaInicial.value, fechaf: fechaFinal.value },
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
                        "<th style='text-align: center;'>Nº Remito</th>" +
                        "<th style='text-align: center;'>Clasificación</th>" +
                        "<th style='text-align: center;'>Cliente</th>" +
                        "<th style='text-align: center;'>Punto de venta</th>" +
                        "<th style='text-align: center;'>Acciones</th>" +
                        "</tr>" +
                        "</thead>" +
                        "<tbody>";

                    response.forEach(function(item) {
                        table += "<tr>" +
                            "<td style='display: none;'>" + item.id + "</td>" +
                            "<td style='text-align: center;'>" + item.fecha + "</td>" +
                            "<td style='text-align: center;'>" + item.numero + "</td>" +
                            "<td style='text-align: center;'>" + item.clasificacion + "</td>" +
                            "<td style='text-align: center;'>" + item.cliente + "</td>" +
                            "<td style='text-align: center;'>" + item.pventa + "</td>" +
                            "<td style='text-align: center;'><button class='btn btn-warning' onclick='editarRemito(" + item.id + ")' data-toggle='tooltip' title='Editar'><i class='fa fa-pencil' aria-hidden='true'></i></button><button class='btn btn-light' onclick='verDetalles(" + item.id + ")' style='margin-left:10px;' data-toggle='tooltip' title='Ver detalle'><i class='fa fa-eye' aria-hidden='true'></i></button><button class='btn btn-danger' onclick='eliminarRemito(" + item.id + ")' style='margin-left:10px;' data-toggle='tooltip' title='Eliminar'><i class='fa fa-trash' aria-hidden='true'></i></button></td>" +
                            "</tr>";
                    });

                    table += "</tbody></table>";

                    $("#detallesRemitos").append(table);
                    $("#historial").show();
                    nremitoBuscado.value = '';
                    fechaInicial.value = '';
                    fechaFinal.value = '';
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
}

function filtrarDatos() {
    var input = document.getElementById("buscador");
    var filter = input.value.toLowerCase();
    var table = document.getElementById("detallesRemitos").getElementsByTagName("table")[0];
    var tr = table.getElementsByTagName("tr");

    for (var i = 1; i < tr.length; i++) {
        var visible = false;
        var td = tr[i].getElementsByTagName("td");

        for (var j = 0; j < td.length; j++) {
            if (td[j] && td[j].style.display !== "none") {
                if (td[j].innerHTML.toLowerCase().indexOf(filter) > -1) {
                    visible = true;
                    break;
                }
            }
        }

        if (visible === true) {
            tr[i].style.display = "";
        } else {
            tr[i].style.display = "none";
        }
    }
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
                let SumaMetros = 0;
                response.forEach(function(item) {
                    if(item.metros == null)
                        {
                            SumaMetros=-1;
                        }
                        else
                        {
                            SumaMetros += parseFloat(item.metros);
                        }
                    
                });
                var detalles = "<table class='table'>";
                if(SumaMetros == -1)
                {
                    if(entregas)
                        {
                            detalles += "<thead id='headDetalles'><tr><th colspan='7' style='text-align: center;'> Volumen entregado: " + totalM + "m³" + "</th></tr>";
                        }
                }
                else
                {
                    if(entregas)
                        {
                            detalles += "<thead id='headDetalles'><tr><th colspan='7' style='text-align: center;'> Volumen entregado: " + SumaMetros + "m³" + "</th></tr>";
                        }
                }
                
                
                detalles += "<thead id='headDetalles'><tr><th colspan='7' style='text-align: center;'>Envases entregados: " + response[0].totalE + " | NP: " + response[0].totalNPE + " | SP: " +  response[0].totalSPE +  " | Envases retirados: " + response[0].totalD + " | NP: " + response[0].totalNPD + " | SP: " +  response[0].totalSPD + "</th></tr>";
                detalles += "<tr><th style='text-align: center;'>Producto</th><th style='text-align: center;'>Propiedad</th><th style='text-align: center;'>Accion</th><th style='text-align: center;'>Metros</th><th style='text-align: center;'>Tipo de envase</th><th style='text-align: center;'>Cantidad</th><th style='text-align: center;'>Detalles</th></tr></thead>";
                detalles += "<tbody>";
                response.forEach(function(item) {
                    detalles += "<tr>";
                    detalles += "<td style='text-align: center;'>" + item.nombre_tipo_producto + "</td>";
                    detalles += "<td style='text-align: center;'>" + item.propiedad + "</td>";
                    detalles += "<td style='text-align: center;'>" + item.accion + "</td>";
                    if (item.metros !== null) {
                        if(item.accion == "E")
                            {
                                detalles += "<td style='text-align: center;'>" + item.metros + "</td>";
                            }else{
                                detalles += "<td style='text-align: center;'>" + "-" + "</td>";
                            }
                        
                    } else {
                        if(item.accion == 'E')
                            {
                                detalles += "<td style='text-align: center;'>" + item.capacidadSuma + "</td>";
                            }
                            else
                            {
                                detalles += "<td style='text-align: center;'>" + "-" + "</td>";
                            }
                    }
                    
                    detalles += "<td style='text-align: center;'>" + item.tipoenvase + "</td>";
                    detalles += "<td style='text-align: center;'>" + item.cantidad + "</td>";
                    detalles += "<td style='text-align: center;'><button class='btn btn-warning' onclick='verDetallesEspecificos(" + item.idtipo_producto + ", \"" + item.propiedad + "\", \"" + item.accion + "\", \"" + item.tipoenvase + "\", " + idRemito + "," + item.metros + ")'>Ver</button></td>";
                    detalles += "</tr>";
                });
                detalles += "</tbody></table>";

                $("#detallesContainer").html(detalles);
                $("#detallesModal").css("display", "block"); 
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
    sessionStorage.setItem('mostrarCambiarDatos', true);
    sessionStorage.setItem('idCambio', idRemito);
    window.location.href = "ingreso.php";
}

function eliminarRemito(idRemito) {
    Swal.fire({
        title: "¿Esta seguro de eliminar el remito?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Eliminar",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {

            $.post(
                "../ajax/consultaremitos.php?op=eliminarRemito",
                { id: idRemito },
                function(response) {
                    try {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        Swal.fire({
                            title: "¡Operación exitosa!",
                            text: "El remito fue eliminado.",
                            icon: "success"
                          });
                          listarRemitos();
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
      });
    
}

function verDetallesEspecificos(idtipo_producto,propiedad,accion,tipoenvase, idRemito, metros)
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
                if(metros==null)
                    {
                        detalles += "<thead id='headDetalles'><tr><th colspan='2' style='text-align: center;'>Producto: " + response[0].nombre_tipo_producto + " | Propiedad: " + propiedad + "</th></tr><tr><th style='text-align: center;'>Capacidad</th><th style='text-align: center;'>Nº Serie</th></tr></thead>";                detalles += "<tbody>";
                    }
                    else
                    {
                        detalles += "<thead id='headDetalles'><tr><th colspan='2' style='text-align: center;'>Producto: " + response[0].nombre_tipo_producto + " | Propiedad: " + propiedad + "</th></tr><tr><th style='text-align: center;'>Nº Serie</th></tr></thead>";                detalles += "<tbody>";
                    }
                response.forEach(function(item) {
                    detalles += "<tr>";
                    if(metros==null)
                    {
                        detalles += "<td style='text-align: center;'>" + item.capacidad + "</td>";
                    }
                   
                    detalles += "<td style='text-align: center;'>" + item.nserie + "</td>";
                    detalles += "</tr>";
                });
                detalles += "</tbody></table>";
                
                $("#masDetallesContainer").html(detalles);
                $("#masDetallesModal").css("display", "block"); 
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
    $("#detallesModal").css("display", "none"); 
}

function cerrarMasDetallesModal() {
    $("#masDetallesModal").css("display", "none"); 
    $("#detallesModal").css("display", "block");
}

function cerrarModal() {
    $("#modalDetalles").modal("hide");
}

function subirDocumento() {
            var input = document.createElement('input');
            input.type = 'file';
            input.accept = '.xlsx,.xls';
            input.onchange = function(event) {
                var archivo = event.target.files[0];
                if (archivo) {
                    console.log('Archivo seleccionado:', archivo.name, archivo.size);
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
            var metros = 0;
            var fechaActual = new Date();
            var fechaActualFormatted = fechaActual.getFullYear() + '-' + 
                                        ('0' + (fechaActual.getMonth() + 1)).slice(-2) + '-' + 
                                        ('0' + fechaActual.getDate()).slice(-2) + ' ' + 
                                        ('0' + fechaActual.getHours()).slice(-2) + ':' + 
                                        ('0' + fechaActual.getMinutes()).slice(-2) + ':' + 
                                        ('0' + fechaActual.getSeconds()).slice(-2);
            
            var remitosMap = {};
            
            for (var i = 1; i < data.length; i++) {
                var fila = data[i];
                var remitoNumero = fila[1];
                if (!remitosMap[remitoNumero]) {
                    remitosMap[remitoNumero] = {
                        idusuario: '-',
                        clasificacion: fila[5], // CONCEPTO
                        numero: fila[1],        // REMITO
                        fecha_hora: fechaActualFormatted,
                        fecha_remito: fila[0],  // FECHA
                        estado: 1,
                        cliente: fila[2],       // RAZON SOCIAL
                        informacion: '-',
                        pventa: fila[3],        // PVENTA
                        detalles: [],
                        pentrega: fila[4]
                    };
                }
        
                var control = 11;
                var serie = [];
                var cantidad = parseFloat(fila[8]);

                if (cantidad === 0 || fila[7].includes('TERMO') || fila[7].includes('GRANEL')) {
                    serie.push(0);
                } else {
                    for (var j = 0; j < cantidad; j++) {
                        var control_serie = fila[control];
                        if (control_serie === undefined) {
                            control++;
                            j--;
                        } else {
                            serie.push(fila[control]);
                            control++;
                        }
                    }
                }
        
                var envase;
                var propiedadEnvase = '-';
                if (['O2 TERMO', 'N2 TERMO'].includes(fila[7])) {
                    envase = 'TER';
                } else if (['CO2 GRANEL', 'O2 GRANEL'].includes(fila[7])) {
                    envase = 'GRANEL';
                } else {
                    envase = 'CIL';
                }
        
                var nombre, codigo;
                if (fila[7].includes('N2')) {
                    nombre = 'NITRÓGENO';
                    codigo = 'N2';
                } else if (fila[7].includes('ATAL')) {
                    nombre = 'ATAL';
                    codigo = 'AT';
                } else if (fila[7].includes('5')) {
                    nombre = 'ARGON 5.0';
                    codigo = 'ARN50';
                } else if (fila[7].includes('ARGON')) {
                    nombre = 'ARGÓN';
                    codigo = 'AR';
                } else if (fila[7].includes('CO2')) {
                    nombre = 'DIÓXIDO DE CARBONO';
                    codigo = 'CO2';
                } else if (fila[7].includes('O2')) {
                    nombre = 'OXÍGENO';
                    codigo = 'O2';
                } else if (fila[7].includes('C2H2')) {
                    nombre = 'ACETILENO';
                    codigo = 'C2H2';
                } else if (fila[7].includes('N2O')) {
                    nombre = 'ÓXIDO NITROSO';
                    codigo = 'N2O';
                } else if (fila[7].includes('CSR')) {
                    nombre = 'CONSERVAR';
                    codigo = 'CSR';
                } else if (fila[7].includes('CANASTO')) {
                    nombre = 'CANASTO';
                    codigo = 'CTO';
                }
        
                if (remitoNumero > 300000 && fila[3] === 'TECNO') {
                    pventa = 'AV-00007';
                } else if (remitoNumero < 10000 && remitoNumero > 999 && fila[3] === 'TECNO') {
                    pventa = 'AV-00011';
                } else if (fila[3] === 'RESPITEC') {
                    pventa = 'RE-00001';
                } else if (fila[3] === 'IAG') {
                    pventa = 'IAG-00001';
                } else if(fila[3] === 'COMARGAS'){
                    pventa = 'CO-00001'
                } else {
                    pventa = '-';
                }
                if(envase == 'TER' || envase == 'GRANEL')
                {
                    propiedadEnvase = '-'
                }
                else
                {
                    propiedadEnvase = fila[10];
                }
                if(fila[9] != undefined)
                {
                    metros = fila[9];
                }
                else
                {
                    metros = 0;
                }
                var detalle = {
                    nombre: nombre,
                    codigo: codigo,
                    envase: envase,
                    accion: fila[6],
                    propiedad: propiedadEnvase,
                    cantidad: fila[8],
                    serie: serie,
                    pentrega: fila[4],
                    metros:metros
                };
                
                remitosMap[remitoNumero].detalles.push(detalle);
            }
        
            for (var remitoNumero in remitosMap) {
                remitos.push(remitosMap[remitoNumero]);
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
        