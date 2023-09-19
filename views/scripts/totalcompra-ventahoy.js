var tabla;

//funcion que se ejecuta al inicio
function init(){
    totalCompraHoy();
    totalVentaHoy();
}

function totalCompraHoy(){    

    $.post("../ajax/consultas.php?op=totalCompraHoy", function(data, status){
    
        data = JSON.parse(data);
        $("#total_compra").html(data);
        
    });
}

function totalVentaHoy(){    

    $.post("../ajax/consultas.php?op=totalVentaHoy", function(data, status){

        data = JSON.parse(data);
        console.log(data);
        // var valor = `<p>${data}</p>`;
        $("#total_venta").html(data);
        
    });
}

init();
