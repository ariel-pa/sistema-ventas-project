<?php
require_once "../models/Consultas.php";


//crear objeto categoria e istanaciar
$consultas = new Consultas();

switch( $_GET["op"]){

    case 'comprasFecha':
        $fecha_inicio=$_REQUEST["fecha_inicio"];
        $fecha_fin=$_REQUEST["fecha_fin"];

        $rspta = $consultas->comprasFecha($fecha_inicio, $fecha_fin);

        $data = Array();

        while($reg = $rspta->fetch_object()){
            $data[] = array( 
                
                "0"=>$reg->fecha,
                "1"=>$reg->usuario,
                "2"=>$reg->proveedor,
                "3"=>$reg->tipo_comprobante,
                "4"=>$reg->serie_comprobante.'-'.$reg->num_comprobante,
                "5"=>$reg->total_compra,
                "6"=>$reg->impuesto,
                "7"=>($reg->estado=='Aceptado')?'<span class="label bg-green">Aceptado</span>':'<span class="label bg-red">Anulado</span>'  
            );
        }

        $results = array(
            "sEcho"=>1, //Informacion para el datatables
            "iTotalRecords"=>count($data),//enviamos el total registro al datatable
            "iTotalDisplayRecords"=>count($data),//enviamos el total registro a visualizar.
            "aaData"=>$data);
        echo json_encode($results);

    break;

    case 'ventasFechaCliente':
        $fecha_inicio=$_REQUEST["fecha_inicio"];
        $fecha_fin=$_REQUEST["fecha_fin"];
        $idcliente=$_REQUEST["idcliente"];

        $rspta = $consultas->ventasFechaCliente($fecha_inicio, $fecha_fin,$idcliente);

        $data = Array();

        while($reg = $rspta->fetch_object()){
            $data[] = array( 
                
                "0"=>$reg->fecha,
                "1"=>$reg->usuario,
                "2"=>$reg->cliente,
                "3"=>$reg->tipo_comprobante,
                "4"=>$reg->serie_comprobante.'-'.$reg->num_comprobante,
                "5"=>$reg->total_venta,
                "6"=>$reg->impuesto,
                "7"=>($reg->estado=='Aceptado')?'<span class="label bg-green">Aceptado</span>':'<span class="label bg-red">Anulado</span>'  
            );
        }

        $results = array(
            "sEcho"=>1, //Informacion para el datatables
            "iTotalRecords"=>count($data),//enviamos el total registro al datatable
            "iTotalDisplayRecords"=>count($data),//enviamos el total registro a visualizar.
            "aaData"=>$data);
        echo json_encode($results);

    break;

    case 'totalCompraHoy':
        $rspta = $consultas->totalCompraHoy();
        $reg = $rspta->fetch_object();
        $results = $reg->total_compra;
        echo json_encode($results);
    break;

    case 'totalVentaHoy':
        $rspta = $consultas->totalVentaHoy();
        $reg = $rspta->fetch_object();
        $results = $reg->total_venta;
        echo json_encode($results);
    break;

}
?>