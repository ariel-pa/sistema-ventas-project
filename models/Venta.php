<?php
//incluimos inicialmente la conexion a la base de datos.
require '../config/Conexion.php';

class Venta{

    //implementamos nuestro constructor 
    public function __construct(){

    }

    //metodo para insertar un registro
    public function insertar($idcliente, $idusuario, $tipo_comprobante, $serie_comprobante, $num_comprobante, $fecha_hora, $impuesto, $total_venta, $idarticulo, $cantidad, $precio_venta, $descuento){ 
        $sql = "INSERT INTO venta(idcliente, idusuario, tipo_comprobante, serie_comprobante, num_comprobante, fecha_hora, impuesto, total_venta, estado) 
        VALUES ('$idcliente', '$idusuario', '$tipo_comprobante', '$serie_comprobante', '$num_comprobante', '$fecha_hora', '$impuesto', '$total_venta', 'Aceptado')";

        // return ejecutarConsulta($sql);//devuelbe 1 o 0 para ver si se ejecuto la consulta
        $idventanew = ejecutarConsulta_retornarID($sql);//TODO: Retorna el id del registro ingresado.

        $num_elemento = 0;
        $sw = true;

        //Registro de detelle de venta
        while ($num_elemento < count($idarticulo)){
            $sql_detalle = "INSERT INTO detalle_venta(idventa, idarticulo, cantidad, precio_venta, descuento) VALUES ('$idventanew', '$idarticulo[$num_elemento]', '$cantidad[$num_elemento]', '$precio_venta[$num_elemento]', '$descuento[$num_elemento]')";
            
            ejecutarConsulta($sql_detalle) or $sw = false;

            $num_elemento = $num_elemento + 1;
        }

        return $sw;
    }

    //Metodo para anular un ingreso registrado
    public function anular($idventa){
        $sql = "UPDATE venta SET estado='Anulado' WHERE idventa = '$idventa'";

        return ejecutarConsulta($sql);
    }

    //Metodo para mostrar el proveedor que esta abasteciendo con el articulo y el usuario que registro dicho articulo
    public function mostrar($idventa){
        $sql = "SELECT v.idventa, DATE(v.fecha_hora) as fecha, v.idcliente, p.nombre as cliente, u.idusuario, u.nombre as usuario, v.tipo_comprobante, v.serie_comprobante, v.num_comprobante, v.total_venta, v.impuesto, v.estado  FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario WHERE idventa = '$idventa'";
        
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listarDetalle($idventa){
        $sql = "SELECT dv.idventa, dv.idarticulo, a.nombre, dv.cantidad, dv.precio_venta, dv.descuento,(dv.precio_venta*dv.cantidad-dv.descuento) as subtotal FROM detalle_venta dv INNER JOIN articulo a ON dv.idarticulo=a.idarticulo WHERE dv.idventa ='$idventa'";
        
        return ejecutarConsulta($sql);
    }

    //Metodo para mostrar un registro y actualizar
    public function listar(){
        $sql = "SELECT v.idventa, DATE(v.fecha_hora) as fecha, v.idcliente, p.nombre as cliente, u.idusuario, u.nombre as usuario, v.tipo_comprobante, v.serie_comprobante, v.num_comprobante, v.total_venta, v.impuesto, v.estado, u.imagen FROM  venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario ORDER BY v.idventa desc";
        
        return ejecutarConsulta($sql);
    }
     
    public function ventaCabeceraFactura($idventa){
        $sql = "SELECT v.idventa, v.idcliente, p.nombre as cliente, p.direccion, p.tipo_documento, p.num_documento, p.email, p.telefono, v.idusuario, u.nombre as usuario, v.tipo_comprobante, v.serie_comprobante, v.num_comprobante, date(v.fecha_hora) as fecha, v.impuesto, v.total_venta FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario WHERE v.idventa='$idventa'";
        
        return ejecutarConsulta($sql);
    }


    public function ventaDetalleFactura($idventa){
        $sql = "SELECT a.nombre as articulo, a.codigo, dv.cantidad, dv.precio_venta, dv.descuento, (dv.cantidad*dv.precio_venta-dv.descuento) as subtotal FROM detalle_venta dv INNER JOIN articulo a ON dv.idarticulo=a.idarticulo WHERE dv.idventa='$idventa'";
        
        return ejecutarConsulta($sql);
        
    }

}
?>