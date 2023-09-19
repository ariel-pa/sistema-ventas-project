<?php
//Activamos el almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1) 
  session_start();

if (!isset($_SESSION["nombre"]))
{
  echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
}
else
{
if ($_SESSION['ventas']==1)
{
//Incluímos el archivo Factura.php
require('Factura.php');

//Establecemos los datos de la empresa
$logo = "logo.jpg";
$ext_logo = "jpg";
$empresa = "Soluciones Innovadoras Sucre";
$documento = "0024354";
$direccion = "Av. Marcelo Quiroga Santa Cruz";
$telefono = "+591 69082132";
$email = "arielpa@gmail.com";

//Obtenemos los datos de la cabecera de la venta actual
require_once "../models/Venta.php";
$venta= new Venta();

$rsptaVenta = $venta->ventaCabeceraFactura($_GET["id"]);

//Recorremos todos los valores obtenidos
$regVenta = $rsptaVenta->fetch_object();

//Establecemos la configuración de la factura
$pdf = new PDF_Invoice( 'P', 'mm', 'A4' );
$pdf->AddPage();

//Enviamos los datos de la empresa al método addSociete de la clase Factura
$pdf->addSociete(utf8_decode($empresa),$documento."\n" .utf8_decode("Dirección: ").utf8_decode($direccion)."\n".utf8_decode("Teléfono: ").$telefono."\n" ."Email : ".$email,$logo,$ext_logo);

$pdf->fact_dev( "$regVenta->tipo_comprobante ", "$regVenta->serie_comprobante-$regVenta->num_comprobante" );
$pdf->temporaire( "" );
$pdf->addDate( $regVenta->fecha);

//Enviamos los datos del cliente al método addClientAdresse de la clase Factura
$pdf->addClientAdresse(utf8_decode($regVenta->cliente),"Domicilio: ".utf8_decode($regVenta->direccion),$regVenta->tipo_documento.": ".$regVenta->num_documento,"Email: ".$regVenta->email,"Telefono: ".$regVenta->telefono);

//Establecemos las columnas que va a tener la sección donde mostramos los detalles de la venta
$cols=array( "CODIGO"=>30,
             "DESCRIPCION"=>71,
             "CANTIDAD"=>22,
             "P.U."=>25,
             "DSCTO"=>20,
             "SUBTOTAL"=>22);
$pdf->addCols( $cols);
$cols=array( "CODIGO"=>"L",
             "DESCRIPCION"=>"L",
             "CANTIDAD"=>"C",
             "P.U."=>"R",
             "DSCTO" =>"R",
             "SUBTOTAL"=>"C");
$pdf->addLineFormat( $cols);
$pdf->addLineFormat($cols);
//Actualizamos el valor de la coordenada "y", que será la ubicación desde donde empezaremos a mostrar los datos
$y= 89;

// //Obtenemos todos los detalles de la venta actual
$rsptad = $venta->ventaDetalleFactura($_GET["id"]);

while ($regd = $rsptad->fetch_object()) {
  $line = array( "CODIGO"=> "$regd->codigo",
                "DESCRIPCION"=> utf8_decode("$regd->articulo"),
                "CANTIDAD"=> "$regd->cantidad",
                "P.U."=> "$regd->precio_venta",
                "DSCTO" => "$regd->descuento",
                "SUBTOTAL"=> "$regd->subtotal");

            $size = $pdf->addLine( $y, $line );
            $y   += $size + 2;
}

// //Convertimos el total en letras
require ('Letras.php');
$V=new EnLetras(); 
$con_letra=strtoupper($V->ValorEnLetras(1000,"ERROR AL DELETREAR MAYOR CANTIDAD BOLIVIANOS"));
$pdf->addCadreTVAs("---".$con_letra);

//Mostramos el impuesto
$pdf->addTVAs( $regVenta->impuesto, $regVenta->total_venta,"Bs. ");
$pdf->addCadreEurosFrancs("IVA"." $regVenta->impuesto %");
$pdf->Output('Reporte de Venta','I');


}
else
{
  echo 'No tiene permiso para visualizar el reporte';
}

}
ob_end_flush();
?>