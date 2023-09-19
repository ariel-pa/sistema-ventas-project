<?php
//incluimos inicialmente la conexion a la base de datos.
require '../config/Conexion.php';

class Persona{

    //implementamos nuestro constructor 
    public function __construct(){

    }

    //metodo para insertar un registro
    public function insertar($tipo_persona, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email){ 
        $sql = "INSERT INTO persona(tipo_persona, nombre, tipo_documento, num_documento, direccion, telefono, email) 
        VALUES ('$tipo_persona', '$nombre', '$tipo_documento', '$num_documento', '$direccion', '$telefono', '$email')";

        return ejecutarConsulta($sql);//devuelbe 1 o 0 para ver si se ejecuto la consulta
    }

    //metodo para editar un registro
    public function editar($idpersona, $tipo_persona, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email){
        $sql = "UPDATE persona SET tipo_persona='$tipo_persona', nombre='$nombre', tipo_documento='$tipo_documento', num_documento='$num_documento', direccion='$direccion', telefono='$telefono', email='$email' 
        WHERE idpersona = '$idpersona'";

        return ejecutarConsulta($sql);
    }

    //Implemnetamos metodo para eliminar registro persona 
    public function eliminar($idpersona){
        $sql = "DELETE FROM persona WHERE idpersona = '$idpersona'";

        return ejecutarConsulta($sql);
    }

    //Metodo para mostrar un registro y actualizar
    public function mostrar($idpersona){
        $sql = "SELECT * FROM persona WHERE idpersona = '$idpersona'";
        
        return ejecutarConsultaSimpleFila($sql);
    }

    //Metodo para listar todos los registros de Proveedores
    public function listarProveedores(){
        $sql = "SELECT * FROM persona WHERE tipo_persona ='Proveedor'";

        return ejecutarConsulta($sql);
    }
    
    //Metodo para listar todos los registros de Clientes.
    public function listarClientes(){
        $sql = "SELECT * FROM persona WHERE tipo_persona='Cliente'";

        return ejecutarConsulta($sql);
    } 
}
?>