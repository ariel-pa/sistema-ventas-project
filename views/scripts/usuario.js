var tabla;

//funcion que se ejecuta al inicio
function init(){
    mostrarForm(false);
    listar();

    $("#formulario").on("submit",function(e)
    {
        guardaryeditar(e);
    });

    //Mostramos los permisos
    $.post("../ajax/usuario.php?op=permisos&id=", function(data){
        $("#permisos").html(data);
    })

    $("#imagenmuestra").hide();
}

//funcion limpiar
function limpiar(){

    $("#nombre").val("");
    $("#num_documento").val("");
    $("#direccion").val("");
    $("#telefono").val("");
    $("#email").val("");
    $("#cargo").val("");
    $("#login").val("");
    $("#clave").val("");
    $("#imagenmuestra").attr("src", "");
    $("#imagenactual").val("");
    $("#idusuario").val("");
}

//funcion mostrar formulario o registros segun el estado   
function mostrarForm(flag){
    limpiar();
    if(flag){
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        $("#btnGuardar").prop("disabled", false);
        $("#btnagregar").hide();
    }
    else{
        $("#listadoregistros").show();
        $("#formularioregistros").hide();
        $("#btnagregar").show();
    }
}

//funcion cancelarform
function cancelarForm(){
    limpiar();
    mostrarForm(false);
}

//funcion listar
function listar(){
    tabla=$('#tbllistado').dataTable(
        {
            "aProcessing": true,//activamos el procesamiento del datatables
            "aServerSide": true,//Paginacion y Filtrado realizado por el servidor
            dom: "Bfrtip", //Definimos los elementos del control de tabla
            buttons:[
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdf'
            ],
            "ajax":{
                url:'../ajax/usuario.php?op=listar',
                type: "get",
                dataType: "json",
                error:function(e){
                    console.log(e.responseText);
                }
             },
            "bDestroy":true,
            "iDisplayLength":5,//paginacion de cada 5 registros
            "order": [[0, "desc"]]//ordenar los registros (columna,orden)

    }).DataTable();
}

function guardaryeditar(e){
    e.preventDefault();
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);
    $.ajax({
        url: "../ajax/usuario.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function(mensaje)
	    {                    
	          bootbox.alert(mensaje);	          
	          mostrarForm(false);
	          tabla.ajax.reload();
	    }
    });

    limpiar();
}

function mostrar(idusuario){    

    $.post("../ajax/usuario.php?op=mostrar", {idusuario: idusuario}, function(data, status){
    
        data = JSON.parse(data);
        mostrarForm(true);

        $("#nombre").val(data.nombre);
        $("#tipo_documento").val(data.tipo_documento);
        $("#tipo_documento").selectpicker('refresh');
        $("#num_documento").val(data.num_documento);        
        $("#direccion").val(data.direccion);
        $("#telefono").val(data.telefono);
        $("#email").val(data.email);
        $("#cargo").val(data.cargo);
        $("#login").val(data.login);
        $("#clave").val(data.clave);
        $("#idusuario").val(data.idusuario);

        $("#imagenmuestra").show();
        $("#imagenmuestra").attr("src", "./../files/usuarios/"+data.imagen);
        $("#imagenactual").val(data.imagen);

    });

    $.post("../ajax/usuario.php?op=permisos&id="+idusuario, function(data){
        $("#permisos").html(data);
    })
}
//funcion para desactivar categoria
function desactivar(idusuario){
    bootbox.confirm("¿Esta seguro de desactivar al usuario?", function(result){
        if(result){
            $.post("../ajax/usuario.php?op=desactivar", {idusuario: idusuario}, function(mensaje){
                bootbox.alert(mensaje);
                tabla.ajax.reload();
            })
        }
    });
}

function activar(idusuario){
    bootbox.confirm("¿Esta seguro de activar al usuario?", function(result){
        if(result){
            $.post("../ajax/usuario.php?op=activar", {idusuario: idusuario}, function(mensaje){
                bootbox.alert(mensaje);
                tabla.ajax.reload();
            })
        }
    });
}


init();
