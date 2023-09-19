<?php
if(strlen(session_id()) < 1)
    session_start();
require_once "../models/Venta.php";

//Instanciar el modelo Persona.
$venta = new Venta();

$idventa = isset($_POST["idventa"])? limpiarCadena($_POST["idventa"]):"";
$idcliente = isset($_POST["idcliente"])? limpiarCadena($_POST["idcliente"]):"";
$idusuario = $_SESSION['idusuario'];
$tipo_comprobante = isset($_POST["tipo_comprobante"])? limpiarCadena($_POST["tipo_comprobante"]):"";
$serie_comprobante = isset($_POST["serie_comprobante"])? limpiarCadena($_POST["serie_comprobante"]):"";
$num_comprobante = isset($_POST["num_comprobante"])? limpiarCadena($_POST["num_comprobante"]):"";
$fecha_hora = isset($_POST["fecha_hora"])? limpiarCadena($_POST["fecha_hora"]):"";
$impuesto = isset($_POST["impuesto"])? limpiarCadena($_POST["impuesto"]):"";
$total_venta = isset($_POST["total_venta"])? limpiarCadena($_POST["total_venta"]):"";

switch( $_GET["op"]){

    case 'guardaryeditar':

        if(empty($idventa)){
            $rspta = $venta->insertar($idcliente, $idusuario, $tipo_comprobante, $serie_comprobante, $num_comprobante, $fecha_hora, $impuesto, $total_venta, $_POST['idarticulo'], $_POST['cantidad'], $_POST['precio_venta'], $_POST['descuento']);
            echo $rspta ? "Venta registrado" : "No se pudo registrar todos los datos de la venta";
        }else{
             
        }
    break;
 
    case 'anular':
        $rspta = $venta->anular($idventa);
        echo $rspta ? "Venta anulado" : "Venta no se puede anular";
    break;
    

    case 'mostrar':
        $rspta = $venta->mostrar($idventa);
        //codificar el resultado utilizando json
        echo json_encode($rspta);
    break;

    case 'listarDetalle':
        $id=$_GET['id'];
        $rspta = $venta->listarDetalle($id);
        echo '<thead style="background-color:#A9D0F5">
        <th>Total</th>
        <th>Artículos</th>
        <th>Cantidad</th>
        <th>Precio Venta</th>
        <th>Descuento</th>
        <th>Subtotal</th>
        </thead>';
        $total = 0;
        while($reg = $rspta->fetch_object()){
            echo '<tr class="filas">
            <td></td>
            <td>'.$reg->nombre.'</td>
            <td>'.$reg->cantidad.'</td>
            <td>'.$reg->precio_venta.'</td>
            <td>'.$reg->descuento.'</td>
            <td>'.$reg->subtotal.'</td>
            </tr>';

            $total = $total+$reg->subtotal;
        }

        echo '<tfoot>
        <th>TOTAL</th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th><h4 id="total">S/.'.$total.'</h4><input type="hidden" name="total_venta" id="total_venta"></th>
        </tfoot>';
        
    break;

    case 'listar':
        $rspta = $venta->listar();

        $data = Array();

        while($reg = $rspta->fetch_object()){
            if ($reg->tipo_comprobante=='Ticket') {
                $url = '../reports/exTicket.php?id=';
            }else{
                $url = '../reports/exFactura.php?id=';
            }

            $data[] = array( 
                // Condicionar para anular el ingreso
                "0"=>(($reg->estado=='Aceptado')?'<button class="btn btn-warning" onclick="mostrar('.$reg->idventa.')"> <i class="fa fa-eye"></i> </button>'.' <button class="btn btn-danger" onclick="anular('.$reg->idventa.')"><i class="fa fa-close"></i> </button>':'<button class="btn btn-warning" onclick="mostrar('.$reg->idventa.')"> <i class="fa fa-eye"></i> </button>').
                '<a target="_blank" href="'.$url.$reg->idventa.'"> <button class="btn btn-info"> <i class="fa fa-file"></i> </button></a>',
                "1"=>$reg->fecha,
                "2"=>$reg->cliente,
                "3"=>$reg->usuario,
                "4"=>$reg->tipo_comprobante,
                "5"=>$reg->serie_comprobante.'-'.$reg->num_comprobante,
                "6"=>$reg->total_venta,
                // "7"=>"<img src='../files/usuarios/".$reg->imagen."' height='50px' width='50px'>  ",
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

    case 'selectCliente':
        require_once "../models/Persona.php";
        $persona = new Persona();

        $rspta = $persona->listarClientes();

        echo '<option>SELECCIONAR</option>';
        while ($reg = $rspta->fetch_object()){
            echo '<option value='.$reg->idpersona.'>'.$reg->nombre.'</option>';
        }
    break;

    case 'listarArticulosVenta':
        require_once "../models/Articulo.php";
        $articulo = new Articulo();

        $rspta = $articulo->listarArticulosVenta();
        $data = Array();

        while($reg = $rspta->fetch_object()){
            $data[] = array( 
                // Condicionar para activar y desactivara categoria
                "0"=>'<button class="btn btn-warning" onclick="agregarDetalle('.$reg->idarticulo.',\''.$reg->nombre.'\',\''.$reg->precio_venta.'\')"><span class="fa fa-plus"></span></button>',
                "1"=>$reg->nombre,
                "2"=>$reg->categoria,
                "3"=>$reg->codigo,
                "4"=>$reg->stock,
                "5"=>$reg->precio_venta,
                "6"=>"<img src='../files/articulos/".$reg->imagen."' height='50px' width='50px'>  "
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