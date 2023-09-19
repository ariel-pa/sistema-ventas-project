<?php
//incluimos inicialmente la conexion a la base de datos.
require '../config/Conexion.php';

class Ingreso{

    //implementamos nuestro constructor 
    public function __construct(){

    }

    //metodo para insertar un registro
    public function insertar($idproveedor, $idusuario, $tipo_comprobante, $serie_comprobante, $num_comprobante, $fecha_hora, $impuesto, $total_compra, $idarticulo, $cantidad, $precio_compra, $precio_venta){ 
        $sql = "INSERT INTO ingreso(idproveedor, idusuario, tipo_comprobante, serie_comprobante, num_comprobante, fecha_hora, impuesto, total_compra, estado) 
        VALUES ('$idproveedor', '$idusuario', '$tipo_comprobante', '$serie_comprobante', '$num_comprobante', '$fecha_hora', '$impuesto', '$total_compra', 'Aceptado')";

        // return ejecutarConsulta($sql);//devuelbe 1 o 0 para ver si se ejecuto la consulta
        $idingresonew = ejecutarConsulta_retornarID($sql);//TODO: Retorna el id del registro ingresado.

        $num_elemento = 0;
        $sw = true;

        //Registro de detelle de venta
        while ($num_elemento < count($idarticulo)){
            $sql_detalle = "INSERT INTO detalle_ingreso(idingreso, idarticulo, cantidad, precio_compra, precio_venta) VALUES ('$idingresonew', '$idarticulo[$num_elemento]', '$cantidad[$num_elemento]', '$precio_compra[$num_elemento]', '$precio_venta[$num_elemento]')";
            
            ejecutarConsulta($sql_detalle) or $sw = false;

            $num_elemento = $num_elemento + 1;
        }

        return $sw;
    }

    //Metodo para anular un ingreso registrado
    public function anular($idingreso){
        $sql = "UPDATE ingreso SET estado='Anulado' WHERE idingreso = '$idingreso'";

        return ejecutarConsulta($sql);
    }

    //Metodo para mostrar el proveedor que esta abasteciendo con el articulo y el usuario que registro dicho articulo
    public function mostrar($idingreso){
        $sql = "SELECT i.idingreso, DATE(i.fecha_hora) as fecha, i.idproveedor, p.nombre as proveedor, u.idusuario, u.nombre as usuario, i.tipo_comprobante, i.serie_comprobante, i.num_comprobante, i.total_compra, i.impuesto, i.estado  FROM ingreso i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN usuario u ON i.idusuario=u.idusuario WHERE idingreso = '$idingreso'";
        
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listarDetalle($idingreso){
        $sql = "SELECT di.idingreso, di.idarticulo, a.nombre, di.cantidad, di.precio_venta, di.precio_compra FROM detalle_ingreso di INNER JOIN articulo a ON di.idarticulo=a.idarticulo WHERE di.idingreso ='$idingreso'";
        
        return ejecutarConsulta($sql);
    }

    //Metodo para mostrar un registro y actualizar
    public function listar(){
        $sql = "SELECT i.idingreso, DATE(i.fecha_hora) as fecha, i.idproveedor, p.nombre as proveedor, u.idusuario, u.nombre as usuario, i.tipo_comprobante, i.serie_comprobante, i.num_comprobante, i.total_compra, i.impuesto, i.estado, u.imagen FROM ingreso i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN usuario u ON i.idusuario=u.idusuario ORDER BY i.idingreso desc";
        
        return ejecutarConsulta($sql);
    }
     
}
?>