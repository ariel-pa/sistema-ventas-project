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

    $.post("../ajax/articulo.php?op=selectCategoria", function(data){
        $("#idcategoria").html(data);
        $("#idcategoria").selectpicker('refresh');
    })

    $("#imagenmuestra").hide();
}

//funcion limpiar
function limpiar(){
    $("#idcategoria").val("");//val es el valor que se le envia en este caso vacio
    $("#codigo").val("");
    $("#nombre").val("");
    $("#descripcion").val("");
    $("#stock").val("");
    $("#imagenmuestra").attr("src", "");
    $("#imagenactual").val("");
    $("#print").hide();
    $("#idarticulo").val("");
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
                url:'../ajax/articulo.php?op=listar',
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
        url: "../ajax/articulo.php?op=guardaryeditar",
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
function mostrar(idarticulo){    

    $.post("../ajax/articulo.php?op=mostrar", {idarticulo: idarticulo}, function(data, status){
    
        data = JSON.parse(data);
        mostrarForm(true);
        // console.log("articulos",data);
        $("#idcategoria").val(data.idcategoria);
        $("#idcategoria").selectpicker('refresh');

        $("#codigo").val(data.codigo);
        $("#nombre").val(data.nombre);
        $("#stock").val(data.stock);
        $("#idarticulo").val(data.idarticulo);

        
        $("#imagenmuestra").show();
        $("#imagenmuestra").attr("src", "./../files/articulos/"+data.imagen);
        $("#imagenactual").val(data.imagen);//TODO:Se invia esta img para que no este vacio al momento de guardar

        $("#generar").hide();
        $("#generarbarcode").hide();
        generarbarcode();
    });
}
//funcion para desactivar categoria
function desactivar(idarticulo){
    //TODO: bootbox contine funciones para mostrar formularios flotantes
    bootbox.confirm("¿Esta seguro de desactivar el articulo?", function(result){
        if(result){
            $.post("../ajax/articulo.php?op=desactivar", {idarticulo: idarticulo}, function(mensaje){
                bootbox.alert(mensaje);
                tabla.ajax.reload();
            })
        }
    });
}

function activar(idarticulo){
    bootbox.confirm("¿Esta seguro de activar el articulo?", function(result){
        if(result){
            $.post("../ajax/articulo.php?op=activar", {idarticulo: idarticulo}, function(mensaje){
                bootbox.alert(mensaje);
                tabla.ajax.reload();
            })
        }
    });
}

function generarbarcode(){
    JsBarcode("#barcode", $("#codigo").val());
    $("#print").show();
}

function imprimir(){
    $("#print").printArea();
}
init();
