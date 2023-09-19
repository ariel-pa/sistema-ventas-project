var tabla;

//funcion que se ejecuta al inicio
function init(){
    mostrarForm(false);
    listar();

    //TODO: Evita que se reenvie el formulario
    $("#formulario").on("submit",function(e)
    {
        guardaryeditar(e);
    });

    //Cargamos los items al select proveedor
    $.post("../ajax/ingreso.php?op=selectProveedor", function(result){
        $("#idproveedor").html(result);
        $("#idproveedor").selectpicker('refresh');
    })

}

//funcion limpiar
function limpiar(){
    $("#idproveedor").val("");
    $("#serie_comprobante").val("");
    $("#num_comprobante").val("");
    $("#impuesto").val("0");

    //Obtenemos la fecha actual
    var now = new Date();
    var day = ("0"+now.getDate()).slice(-2);
    var month = ("0"+now.getMonth()+1).slice(-2);
    var today = now.getFullYear()+"-"+(month)+"-"+(day);
    $("#fecha_hora").val(today);
    
    
    $("#total_compra").val("");
    $(".filas").remove();
    $("#total").html("0");

    $("#tipo_comprobante").val("SELECCIONAR");
    $("#tipo_comprobante").selectpicker('refresh');
    
}

//funcion mostrar formulario
function mostrarForm(flag){
    limpiar();
    if(flag){
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        $("#btnGuardar").prop("disabled", false);
        $("#btnagregar").hide();
        listarArticulos();

        $("#guardar").show();
        $("#btnGuardar").hide();
        $("#btnCancelar").show();
        $("#btnAgregarArticulo").show();

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
                url:'../ajax/ingreso.php?op=listar',
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

//funcion listar
function listarArticulos(){
    tabla=$('#tblarticulos').dataTable(
        {
            "aProcessing": true,//activamos el procesamiento del datatables
            "aServerSide": true,//Paginacion y Filtrado realizado por el servidor
            dom: "Bfrtip", //Definimos los elementos del control de tabla
            buttons:[

            ],
            "ajax":{
                //TODO: Mediante esta url enviamos los datos sin necesidad de importar la carpeta
                url:'../ajax/ingreso.php?op=listarArticulosActivos',
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
    // $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);
    $.ajax({
        url: "../ajax/ingreso.php?op=guardaryeditar",
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
              //Actualizamos la tabla listar 
	          listar();
	    }
    });

    limpiar();
}

//Para editar la categoria
function mostrar(idingreso){    

    $.post("../ajax/ingreso.php?op=mostrar", {idingreso: idingreso}, function(data, status){
    
        data = JSON.parse(data);
        mostrarForm(true);
        // console.log("articulos",data);
        $("#idproveedor").val(data.idproveedor);
        $("#idproveedor").selectpicker('refresh');
        $("#tipo_comprobante").val(data.tipo_comprobante);
        $("#tipo_compribante").selectpicker('refresh');
        $("#serie_comprobante").val(data.serie_comprobante);
        $("#num_comprobante").val(data.num_comprobante);
        $("#fecha_hora").val(data.fecha);
        $("#impuesto").val(data.impuesto);
        $("#idingreso").val(data.idingreso);
        
        //Ocultar y mostrar los botones
        $("#guardar").show();
        $("#btnGuardar").hide();
        $("#btnCancelar").show();
        $("#btnAgregarArticulo").hide();
    });

    $.post("../ajax/ingreso.php?op=listarDetalle&id="+idingreso, function(result){
        $("#detalles").html(result);
    });
}
//funcion para desactivar categoria
function anular(idingreso){
    //TODO: bootbox contine funciones para mostrar formularios flotantes
    bootbox.confirm("¿Esta seguro de anular el ingreso?", function(result){
        if(result){
            $.post("../ajax/ingreso.php?op=anular", {idingreso: idingreso}, function(mensaje){
                bootbox.alert(mensaje);
                tabla.ajax.reload();
            })  
        }
    });
}

//Declaración de variables necesarias para trabajar con las compras y sus detalles
var impuesto = 18;
var cont = 0;
var detalle = 0;
$("#guardar").hide();
$("#tipo_comprobante").change(MarcarImpuesto);

function MarcarImpuesto(){
    var tipo_coprobante = $("#tipo_comprobante option:selected").text();
    if(tipo_coprobante == "Factura"){
        $("#impuesto").val(impuesto);
    }else{
        $("#impuesto").val("0");
    }
}

function agregarDetalle(idarticulo, articulo){
    var cantidad = 1;
    var precio_compra = 1;
    var precio_venta = 1;
    if(idarticulo != ""){
        var subtotal=cantidad*precio_compra;

        var fila=`<tr class="filas" id="fila${cont}">
        <td><button type="button" class="btn btn-danger" onclick="eliminardetalle(${cont})">X</button></td>
        <td><input type="hidden" name="idarticulo[]" value="${idarticulo}">${articulo}</td>
        <td><input type="number" name="cantidad[]" id="cantidad[]" value="${cantidad}"></td>
        <td><input type="number" name="precio_compra[]" id="precio_compra[]" value="${precio_compra}"></td>
        <td><input type="number" name="precio_venta[]" value="${precio_venta}"></td>
        <td><span name="subtotal" id="subtotal${cont}" >${subtotal}</span></td>
        <td><button type="button" onclick="modificarSubtotales()" class="btn btn-info"><i class="fa fa-refresh"></i></button></td>
        </tr>`; 

        cont++;
        detalle++;
        $("#detalles").append(fila);
        //Cada asignacion se ejecuta la funcion de manera automatica y se acxtualiza los datos
        modificarSubtotales();
    }else{
        alert("Error al ingresar el detalle, revisar los datos del artículo");
    }
}

function modificarSubtotales(){
    //Asignamos a las varibles los array con sus datos
    var cantidad = document.getElementsByName("cantidad[]");
    var precio_compra = document.getElementsByName("precio_compra[]");
    var subtotal = document.getElementsByName("subtotal");

    for (let i = 0; i < cantidad.length; i++) {
        var inpCantidad = cantidad[i];
        var inpPrecio_compra = precio_compra[i];
        var inpSubtotal = subtotal[i];

        inpSubtotal.value = inpCantidad.value * inpPrecio_compra.value;
        document.getElementsByName("subtotal")[i].innerHTML = inpSubtotal.value;
    }
    calcularTotales();
}

function calcularTotales(){
    var subtotales = document.getElementsByName("subtotal");
    var total = 0.0;

    for (let i = 0; i < subtotales.length; i++) {
        total += document.getElementsByName("subtotal")[i].value;        
    }

    $("#total").html("S/. "+ total);
    $("#total_compra").val(total);

    evaluar();
}

function evaluar(){
    if(detalle>0){
        $("#btnGuardar").show();
    }else{
        $("#guardar").hide();
        cont = 0;
    }
}


function eliminardetalle(index){
    $("#fila"+ index).remove();
    calcularTotales();
    detalle = detalle-1;
}
init();
