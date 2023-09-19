<?php
//incluimos inicialmente la conexion a la base de datos.
require '../config/Conexion.php';

class Permiso{

    //implementamos nuestro constructor 
    public function __construct(){

    }

    //implementar un metodo para listar los  registros
    public function listar(){
        $sql = "SELECT * FROM permiso";

        return ejecutarConsulta($sql);
    }
}
?>