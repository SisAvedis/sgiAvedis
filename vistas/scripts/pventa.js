var tabla;

// Función que se ejecuta al inicio
function init() {
    mostrarform(false);
    listar();

    $("#formulario").on("submit", function(e) {
        guardaryeditar(e);
    });
}

// Función limpiar
function limpiar() {
    $("#nombre").val("");
    $("#prefijo").val("");
    $("#numero").val("");
    $("#idpunto_venta").val("");
}

// Función mostrar formulario
function mostrarform(flag) {
    limpiar();
    if (flag) {
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        $("#btnGuardar").prop("disabled", false);
        $("#btnagregar").hide();
    } else {
        $("#listadoregistros").show();
        $("#formularioregistros").hide();
        $("#btnagregar").show();
    }
}

// Función cancelar formulario
function cancelarform() {
    limpiar();
    mostrarform(false);
}

// Función listar
function listar() {
    tabla = $('#tblistado').dataTable({
        "aProcessing": true, // Activamos el procesamiento del datatables
        "aServerSide": true, // Paginación y filtrado realizados por el servidor
        dom: 'Bfrtip', // Definimos los elementos del control de tabla
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdf'
        ],
        "ajax": {
            url: '../ajax/pventa.php?op=listar',
            type: "get",
            dataType: "json",
            error: function(e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 5, // Paginación
        "order": [[0, "desc"]] // Ordenar (Columna, orden)
    }).DataTable();
}

// Función para guardar o editar
function guardaryeditar(e) {
    e.preventDefault(); // No se activará la acción predeterminada del evento
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "../ajax/pventa.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos) {
            bootbox.alert(datos);
            mostrarform(false);
            tabla.ajax.reload();
        },
        error: function(error) {
            console.log("error: " + error);
        }
    });

    limpiar();
}

function mostrar(idpunto_venta) {
    $.post(
        "../ajax/pventa.php?op=mostrar",
        { idpunto_venta: idpunto_venta },
        function(data, status) {
            data = JSON.parse(data);
            mostrarform(true);

            $("#prefijo").val(data.prefijo);
            $("#numero").val(data.numero);

            $("#idpunto_venta").val(data.idpunto_venta); 
        }
    );
}

// Función para desactivar
function desactivar(idpunto_venta) {
    bootbox.confirm("¿Está seguro de desactivar el punto de venta?", function(result) {
        if (result) {
            $.post(
                "../ajax/pventa.php?op=desactivar",
                { idpunto_venta: idpunto_venta },
                function(response) {
                    bootbox.alert(response, function() {
                        tabla.ajax.reload();
                    });
                }
            );
        }
    });
}

// Función para activar
function activar(idpunto_venta) {
    bootbox.confirm("¿Está seguro de activar el punto de venta?", function(result) {
        if (result) {
            $.post(
                "../ajax/pventa.php?op=activar",
                { idpunto_venta: idpunto_venta },
                function(response) {
                    bootbox.alert(response, function() {
                        tabla.ajax.reload();
                    });
                }
            );
        }
    });
}

init();
