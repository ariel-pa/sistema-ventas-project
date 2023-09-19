var tabla;

//funcion que se ejecuta al inicio
function init(){
    mostrarForm(false);//inicialmente el formulario no se muestre
    listar();//Mostrar listado de todas las categorias

    //TODO: Evita que se reenvie el formulario
    $("#formulario").on("submit",function(e)
    {
        guardaryeditar(e);//ejecutando la funcion guardaryeditar();
    });
}

//funcion limpiar
function limpiar(){
    $("#idcategoria").val("");//val es el valor que se le envia en este caso vacio
    $("#nombre").val("");
    $("#descripcion").val("");
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
                //TODO: Mediante esta url enviamos los datos sin necesidad de importar la carpeta
                url:'../ajax/categoria.php?op=listar',
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
    e.preventDefault();//No se activara la accion predeterminada del evento
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);
    $.ajax({
        url: "../ajax/categoria.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,

        //TODO: muestra mensaje de actualizacion o insercion
        success: function(mensaje)
	    {                    
            // console.log("hola",datos);
	          bootbox.alert(mensaje);	          
	          mostrarForm(false);
	          tabla.ajax.reload();//actualiza la tabla
	    }
    });

    limpiar();
}

//Para editar la categoria
function mostrar(idcategoria){    

    $.post("../ajax/categoria.php?op=mostrar", {idcategoria: idcategoria}, function(data, status){
    
        data = JSON.parse(data);
        mostrarForm(true);
        // console.log(data);
        $("#nombre").val(data.nombre);
        $("#descripcion").val(data.descripcion);
        $("#idcategoria").val(data.idcategoria);
    });
}
//funcion para desactivar categoria
function desactivar(idcategoria){
    //TODO: bootbox contine funciones para mostrar formularios flotantes
    bootbox.confirm("¿Esta seguro de desacticar la categoria?", function(result){
        if(result){
            $.post("../ajax/categoria.php?op=desactivar", {idcategoria: idcategoria}, function(mensaje){
                bootbox.alert(mensaje);
                tabla.ajax.reload();
            })
        }
    });
}

function activar(idcategoria){
    bootbox.confirm("¿Esta seguro de activar la categoria?", function(result){
        if(result){
            $.post("../ajax/categoria.php?op=activar", {idcategoria: idcategoria}, function(mensaje){
                bootbox.alert(mensaje);
                tabla.ajax.reload();
            })
        }
    });
}
init();
