var tabla;

function init(){
    mostrarForm(false);
    listar();

    $("#formulario").on("submit",function(e)
    {
        guardaryeditar(e);
    });
}

function limpiar(){

    $("#idpersona").val("");
    // $("#tipo_persona").val("");
    $("#nombre").val("");
    // $("#tipo_documento").val("");
    $("#num_documento").val("");
    $("#direccion").val("");
    $("#telefono").val("");
    $("#email").val("");
}
 
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

function cancelarForm(){
    limpiar();
    mostrarForm(false);
}

function listar(){
    tabla=$('#tbllistado').dataTable(
        {
            "aProcessing": true,
            "aServerSide": true,
            dom: "Bfrtip", 
            buttons:[
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdf'
            ],
            "ajax":{
                
                url:'../ajax/persona.php?op=listarProveedores',
                type: "get",
                dataType: "json",
                error:function(e){
                    console.log(e.responseText);
                }
             },
            "bDestroy":true,
            "iDisplayLength":7,
            "order": [[0, "desc"]]

    }).DataTable();
}

function guardaryeditar(e){
    e.preventDefault();
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);
    $.ajax({
        url: "../ajax/persona.php?op=guardaryeditar",
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

function mostrar(idpersona){    

    $.post("../ajax/persona.php?op=mostrar", {idpersona: idpersona}, function(data, status){
    
        data = JSON.parse(data);
        mostrarForm(true);

        // $("#tipo_persona").val(data.tipo_persona);
        $("#nombre").val(data.nombre);
        $("#tipo_documento").val(data.tipo_documento);
        $("#tipo_documento").selectpicker('refresh');
        $("#num_documento").val(data.num_documento);
        $("#direccion").val(data.direccion);
        $("#telefono").val(data.telefono);
        $("#email").val(data.email);
        $("#idpersona").val(data.idpersona);
    });
}

function eliminar(idpersona){

    bootbox.confirm("¿Esta seguro de eliminar a la persona?", function(result){
        if(result){
            $.post("../ajax/persona.php?op=eliminar", {idpersona: idpersona}, function(mensaje){
                bootbox.alert(mensaje);
                tabla.ajax.reload();
            })
        }
    });
}

init();
