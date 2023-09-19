<?php
//incluimos inicialmente la conexion a la base de datos.
require '../config/Conexion.php';

class Categoria{

    //implementamos nuestro constructor 
    public function __construct(){

    }

    //metodo para insertar un registro
    public function insertar($nombre, $descripcion){ 
        $sql = "INSERT INTO categoria(nombre, descripcion, condicion) 
        VALUES ('$nombre', '$descripcion', '1') ";//valor en condicion por defecto se introduce 1

        return ejecutarConsulta($sql);//devuelbe 1 o 0 para ver si se ejecuto la consulta
    }

    //metodo para editar un registro
    public function editar($idcategoria, $nombre, $descripcion){
        $sql = "UPDATE categoria SET nombre='$nombre', descripcion ='$descripcion' 
        WHERE idcategoria = '$idcategoria'";

        return ejecutarConsulta($sql);
    }

    //implemnetamos metodo para desactivar una categoria 
    public function desactivar($idcategoria){
        $sql = "UPDATE categoria SET condicion = '0' WHERE idcategoria = '$idcategoria'";

        return ejecutarConsulta($sql);
    }

    //implemnetamos metodo para activar una categoria 
     public function activar($idcategoria){
        $sql = "UPDATE categoria SET condicion = '1' WHERE idcategoria = '$idcategoria'";

        return ejecutarConsulta($sql);
    }

    //implementar un metodo para mostrar los datos de un registro a modificar
    public function mostrar($idcategoria){
        $sql = "SELECT * FROM categoria WHERE idcategoria = '$idcategoria'";
        
        return ejecutarConsultaSimpleFila($sql);
    }

    //implementar un metodo para listar los  registros
    public function listar(){
        $sql = "SELECT * FROM categoria";

        return ejecutarConsulta($sql);
    }
    
    //Listar registro para mostrar en el select
    public function select(){
        $sql = "SELECT * FROM categoria WHERE condicion = 1";

        return ejecutarConsulta($sql);
    }
}
?>