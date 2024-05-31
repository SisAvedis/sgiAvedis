var tabla;

//Funcion que se ejecuta al inicio
function init()
{
    mostrarform(false);
    listar();

    $("#formulario").on("submit",function(e)
    {
        guardaryeditar(e);
    })

    $("#imagenmuestra").hide();

    $('[data-toggle="tooltip"]').tooltip();

}

//funcion limpiar
function limpiar()
{
    $("#nombre").val("");

    $("#idtipo_producto").val("");

}

//funcion mostrar formulario
function mostrarform(flag)
{
    limpiar();

    if(flag)
    {
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        $("#btnGuardar").prop("disabled",false);
        $("#btnagregar").hide();
    }
    else
    {
        $("#listadoregistros").show();
        $("#formularioregistros").hide();
        $("#btnagregar").show();
    }
}

//Funcion cancelarform
function cancelarform()
{
    limpiar();
    mostrarform(false);
}

//Funcion listar
function listar()
{
    tabla = $('#tblistado')
        .dataTable(
            {
                "aProcessing":true, //Activamos el procesamiento del datatables
                "aServerSide":true, //Paginacion y filtrado realizados por el servidor
                dom: "Bfrtip", //Definimos los elementos del control de tabla
                buttons:[
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdf'
                ],
                "ajax":{
                    url: '../ajax/producto.php?op=listar',
                    type: "get",
                    dataType:"json",
                    error: function(e) {
                        console.log(e.responseText);
                    }
                },
                "bDestroy": true,
                "iDisplayLength": 5, //Paginacion
                "order": [[0,"desc"]] //Ordenar (Columna, orden)
            
            })
        .DataTable();
}

//funcion para guardar o editar
function guardaryeditar(e)
{
    e.preventDefault(); //No se activará la acción predeterminada del evento
	$("#btnGuardar").prop("disabled",true);
    var formData = new FormData($("#formulario")[0]);
    
    $.ajax({
        url: "../ajax/producto.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos)
        {
            bootbox.alert(datos);
            mostrarform(false);
            tabla.ajax.reload();
        },
        error: function(error)
        {
            console.log("error: " + error);
        } 
    });

    limpiar();
}

function mostrar(idtipo_producto) {
    $.post(
        "../ajax/producto.php?op=mostrar",
        { idtipo_producto: idtipo_producto },
        function(data, status) {
            data = JSON.parse(data);
            mostrarform(true);

            $("#nombre").val(data.nombre);
            $("#codigo").val(data.codigo);

            $("#idtipo_producto").val(data.idtipo_producto); 
        }
    );
}


//funcion para descativar categorias
function desactivar(idtipo_producto) {
    bootbox.confirm("¿Estas seguro de desactivar el producto?", function(result) {
        if (result) {
            $.post(
                "../ajax/producto.php?op=desactivar",
                { idtipo_producto: idtipo_producto },
                function(response) {
                    bootbox.alert(response, function() {
                        tabla.ajax.reload();
                    });
                }
            );
        }
    });
}

function activar(idtipo_producto) {
    bootbox.confirm("¿Estas seguro de activar el Producto?", function(result) {
        if (result) {
            $.post(
                "../ajax/producto.php?op=activar",
                { idtipo_producto: idtipo_producto },
                function(response) {
                    bootbox.alert(response, function() {
                        tabla.ajax.reload();
                    });
                }
            );
        }
    });
}

function vervolumen(idtipo_producto) {
    $.post(
        "../ajax/producto.php?op=verVolumen",
        { idtipo_producto: idtipo_producto },
        function(response) {
            // Parsear la respuesta JSON
            var data = JSON.parse(response);
            // Limpiar el contenido anterior de la tabla
            $("#volumenTableBody").empty();
            // Iterar sobre los datos y crear filas para la tabla
            data.forEach(function(item) {
                var row = "<tr>" +
                            "<td>" + item.capacidad + "</td>" +
                            "<td>" +
                                "<button class='btn btn-warning editar' data-toggle='tooltip' title='Editar'><li class='fa fa-pencil'></li></button>" +
                                "<button class='btn btn-danger eliminar' style='margin-left:10px;' data-toggle='tooltip' title='Borrar'><li class='fa fa-times'></li></button>" +
                            "</td>" +
                          "</tr>";
                $("#volumenTableBody").append(row);
            });
            // Mostrar la ventana modal
            $("#volumenModal").modal("show");

            // Acción para el botón de editar
            $(".editar").click(function() {
                var capacidadActual = $(this).closest("tr").find("td:first").text();
                mostrarSweetAlert(idtipo_producto, capacidadActual);
            });

            // Acción para el botón de eliminar
            $(".eliminar").click(function() {
                var capacidad = $(this).closest("tr").find("td:first").text();
            
                // Mostrar un mensaje de confirmación antes de eliminar la capacidad
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Se eliminará la capacidad: " + capacidad,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Si se confirma la eliminación, realizar la petición AJAX para eliminar la capacidad
                        $.post(
                            "../ajax/producto.php?op=eliminarVolumen",
                            { idtipo_producto: idtipo_producto, capacidad: capacidad },
                            function(response) {
                                Swal.fire({
                                    position: "top-end",
                                    icon: "success",
                                    title: "Volumen eliminado con éxito",
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                // Actualiza los datos en el modal
                                vervolumen(idtipo_producto);
                            }
                        );
                    }
                });
            });
            
            $("#btnagregarC").click(function() {
                agregarCapacidad(idtipo_producto);
            });
        }
    );
}

// Función para mostrar el cuadro de diálogo de SweetAlert2 con el valor actual de la capacidad
function mostrarSweetAlert(idtipo_producto, capacidadActual) {
    Swal.fire({
        title: 'Ingrese la nueva capacidad',
        input: 'text',
        inputValue: capacidadActual, // Muestra el valor actual de la capacidad en el campo de entrada
        inputPlaceholder: 'Capacidad',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
        showLoaderOnConfirm: true,
        preConfirm: (capacidadNueva) => {
            return new Promise((resolve) => {
                $.post(
                    "../ajax/producto.php?op=modificarVolumen",
                    { idtipo_producto: idtipo_producto, capacidad: capacidadActual, capacidadNueva: capacidadNueva },
                    function(response) {
                        // Muestra un mensaje de éxito
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: "Volumen modificado con éxito",
                            showConfirmButton: false,
                            timer: 1500
                        });
                        // Actualiza los datos en el modal
                        vervolumen(idtipo_producto);
                        resolve();
                    }
                ).fail(function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al modificar la capacidad',
                        text: error.responseText // Puedes obtener el mensaje de error desde el servidor
                    });
                    resolve();
                });
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    });
}

function agregarCapacidad(idtipo_producto) {
    Swal.fire({
        title: 'Ingrese la nueva capacidad',
        input: 'text',
        showCancelButton: true,
        confirmButtonText: 'Agregar',
        cancelButtonText: 'Cancelar',
        preConfirm: (nuevaCapacidad) => {
            return new Promise((resolve) => {
                $.post(
                    "../ajax/producto.php?op=agregarVolumen",
                    { idtipo_producto: idtipo_producto, capacidad: nuevaCapacidad },
                    function(response) {
                        // Mostrar mensaje de éxito
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: "Capacidad agregada con éxito",
                            showConfirmButton: false,
                            timer: 1500
                        });

                        // Actualizar los datos en el modal
                        vervolumen(idtipo_producto);
                        resolve();
                    }
                );
            });
        }
    });
}



init();