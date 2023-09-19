<?php
require_once "../models/Articulo.php"; 

//crear objeto categoria e istanaciar
$articulo = new Articulo();

$idarticulo = isset($_POST["idarticulo"])? limpiarCadena($_POST["idarticulo"]):"";
$idcategoria = isset($_POST["idcategoria"])? limpiarCadena($_POST["idcategoria"]):"";
$codigo = isset($_POST["codigo"])? limpiarCadena($_POST["codigo"]):"";
$nombre = isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$stock = isset($_POST["stock"])? limpiarCadena($_POST["stock"]):"";
$descripcion = isset($_POST["descripcion"])? limpiarCadena($_POST["descripcion"]):"";
$imagen = isset($_POST["imagen"])? limpiarCadena($_POST["imagen"]):"";

switch( $_GET["op"]){

    case 'guardaryeditar':
        //verifica si no existe imagen o mo fue seleccionada
        if(!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name'])){
            $imagen = $_POST["imagenactual"];
        }else{
            // if(!empty($_POST["imagenactual"])){
            // unlink ("./../files/articulos/".$_POST["imagenactual"]);

            //verifica extencion y lo optiene
            $ext = explode(".",$_FILES['imagen']['name']);
            if($_FILES['imagen']['type'] == "image/jpg" || $_FILES['imagen']['type'] == "image/jpeg" || $_FILES['imagen']['type'] == "image/png" ){
                $imagen =round(microtime(true)) .'.'. end($ext); //TODO Asignar nombre diferente y concatenera con extencion
                move_uploaded_file($_FILES["imagen"]["tmp_name"], "./../files/articulos/".$imagen);
            }

            
        }
        if(empty($idarticulo)){//verifica si variable idcategoria esta vacia
            $rspta = $articulo->insertar($idcategoria, $codigo, $nombre, $stock, $descripcion, $imagen);
            echo $rspta ? "Articulo registrado" : "Articulo no se pudo registrar";
        }else{
            $rspta = $articulo->editar($idarticulo, $idcategoria, $codigo, $nombre, $stock, $descripcion, $imagen);
            echo $rspta ? "Articulo actualizado" : "Articulo no se pudo actualizar"; 
        }
    break;
 
    case 'desactivar':
        $rspta = $articulo->desactivar($idarticulo);
        echo $rspta ? "Articulo desactivado" : "Articulo no se puede desactivar";
    break;
    
    case 'activar':
        $rspta = $articulo->activar($idarticulo);
        echo $rspta ? "Articulo activado" : "Articulo no se puede activar";
    break;

    case 'mostrar':
        $rspta = $articulo->mostrar($idarticulo);
        //codificar el resultado utilizando json
        
        echo json_encode($rspta);
    break;

    case 'listar':
        $rspta = $articulo->listar();

        //declaramos un array para almacenar lo recorrido o registros 
        $data = Array();

        //Todo: Recorremos los datos y lo almacenamos en un array
        while($reg = $rspta->fetch_object()){
            $data[] = array( 
                // Condicionar para activar y desactivara categoria
                "0"=>($reg->condicion)?'<button class="btn btn-warning" onclick="mostrar('.$reg->idarticulo.')"> <i class="fa fa-pencil"></i> </button>'.' <button class="btn btn-danger" onclick="desactivar('.$reg->idarticulo.')"><i class="fa fa-close"></i> </button>':'<button class="btn btn-warning" onclick="mostrar('.$reg->idarticulo.')"> <i class="fa fa-pencil"></i> </button>'.' <button class="btn btn-primary" onclick="activar('.$reg->idarticulo.')"><i class="fa fa-check"></i> </button>',
                "1"=>$reg->nombre,
                "2"=>$reg->categoria,
                "3"=>$reg->codigo,
                "4"=>$reg->stock,
                "5"=>"<img src='../files/articulos/".$reg->imagen."' height='50px' width='50px'>  ",
                "6"=>($reg->condicion)?'<span class="label bg-green">Activado</span>':'<span class="label bg-red">Desactivado</span>'
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

    case 'selectCategoria':
        require_once "../models/Categoria.php"; 
        $categoria = new categoria();

        $rspta = $categoria->select();

        //Todo: Recorremos los datos y lo almacenamos en un array
        while($reg = $rspta->fetch_object()){
            echo '<option value='.$reg->idcategoria.'>'.$reg->nombre.'</option>';
        }

    break;
}
?>