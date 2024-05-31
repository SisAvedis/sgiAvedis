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
    $("#direccion").val("");
    $("#localidad").val("");

    $("#imagenmuestra").attr("src","");
    $("#imagenactual").val("");

    $("#idcliente").val("");

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
                    url: '../ajax/cliente.php?op=listar',
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
        url: "../ajax/cliente.php?op=guardaryeditar",
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

function mostrar(idcliente) {
    $.post(
        "../ajax/cliente.php?op=mostrar",
        { idcliente: idcliente },
        function(data, status) {
            data = JSON.parse(data);
            mostrarform(true);

            $("#nombre").val(data.nombre);
            $("#direccion").val(data.direccion);
            $("#localidad").val(data.localidad);

            $("#imagenmuestra").show();
            $("#imagenmuestra").attr("src", "../files/usuarios/" + data.imagen);
            $("#imagenactual").val(data.imagen);

            $("#idcliente").val(data.idcliente); 
        }
    );
}


//funcion para descativar categorias
function desactivar(idcliente) {
    bootbox.confirm("¿Estas seguro de desactivar el cliente?", function(result) {
        if (result) {
            $.post(
                "../ajax/cliente.php?op=desactivar",
                { idcliente: idcliente },
                function(response) {
                    bootbox.alert(response, function() {
                        tabla.ajax.reload();
                    });
                }
            );
        }
    });
}

function activar(idcliente) {
    bootbox.confirm("¿Estas seguro de activar el Cliente?", function(result) {
        if (result) {
            $.post(
                "../ajax/cliente.php?op=activar",
                { idcliente: idcliente },
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