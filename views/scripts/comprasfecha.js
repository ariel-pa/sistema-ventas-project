var tabla;

//funcion que se ejecuta al inicio
function init(){
    listar();

    $("#fecha_inicio").change(listar);
    $("#fecha_fin").change(listar);
}

//funcion listar
function listar(){

    var fecha_inicio = $("#fecha_inicio").val();
    var fecha_fin = $("#fecha_fin").val();

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
                url:'../ajax/consultas.php?op=comprasFecha',
                data:{fecha_inicio:fecha_inicio, fecha_fin:fecha_fin},
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

init();
