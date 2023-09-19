var tabla;

//funcion que se ejecuta al inicio
function init(){
    mostrarForm(false);
    listar();
}


//funcion mostrar formulario o registros segun el estado   
function mostrarForm(flag){
    if(flag){
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        $("#btnGuardar").prop("disabled", false);
        $("#btnagregar").hide();
        
    }
    else{
        $("#listadoregistros").show();
        $("#formularioregistros").hide();
        $("#btnagregar").hide();
    }
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
                url:'../ajax/permiso.php?op=listar',
                type: "get",
                dataType: "json",
                error:function(e){
                    console.log(e.responseText);
                }
             },
            "bDestroy":true,
            "iDisplayLength":7,//paginacion de cada 5 registros
            "order": [[0, "desc"]]//ordenar los registros (columna,orden)

    }).DataTable();
}
init();
