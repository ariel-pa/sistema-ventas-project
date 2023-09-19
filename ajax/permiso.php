<?php
require_once "../models/Permiso.php";

$permiso = new Permiso();

switch( $_GET["op"]){

    case 'listar':
        $rspta = $permiso->listar();

        $data = Array();

        while($reg = $rspta->fetch_object()){
            $data[] = array( 
                "0"=>$reg->nombre  
            );
        }

        //TODO: Esta funcion devuelbe un json con los datos para ser listados
        $results = array(
            "sEcho"=>1, //Informacion para el datatables
            "iTotalRecords"=>count($data),//enviamos el total registro al datatable
            "iTotalDisplayRecords"=>count($data),//enviamos el total registro a visualizar.
            "aaData"=>$data);
        echo json_encode($results);

    break;
}
?>