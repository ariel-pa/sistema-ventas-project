<?php
//incluimos inicialmente la conexion a la base de datos.
require '../config/Conexion.php';

class Usuario{

    //implementamos nuestro constructor 
    public function __construct(){

    }

    //metodo para insertar un registro
    public function insertar($nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $login, $clave, $imagen, $permisos){ 
        $sql = "INSERT INTO usuario(nombre, tipo_documento, num_documento, direccion, telefono, email, cargo, login, clave, imagen, condicion) 
        VALUES ('$nombre', '$tipo_documento', '$num_documento', '$direccion', '$telefono', '$email', '$cargo', '$login', '$clave', '$imagen', '1')";

        // return ejecutarConsulta($sql);//devuelbe 1 o 0 para ver si se ejecuto la consulta
        $idusuarioNew = ejecutarConsulta_retornarID($sql);//TODO: Retorna id una ves realizado la consulta de registro.

        $num_elemento = 0;
        $sw = true;

        //Registro de permisos a un usuario
        while ($num_elemento < count($permisos)){
            $sql_detalle = "INSERT INTO usuario_permiso(idusuario, idpermiso) VALUES ('$idusuarioNew', '$permisos[$num_elemento]')";
            
            ejecutarConsulta($sql_detalle) or $sw = false;

            $num_elemento = $num_elemento + 1;
        }

        return $sw;
    }

    //metodo para editar un registro
    public function editar($idusuario, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $login, $clave, $imagen, $permisos){
        $sql = "UPDATE usuario SET nombre='$nombre', tipo_documento='$tipo_documento', num_documento='$num_documento', direccion='$direccion', telefono='$telefono', email='$email', cargo='$cargo', login='$login', clave='$clave', imagen='$imagen' 
        WHERE idusuario = '$idusuario'";

        ejecutarConsulta($sql);

        //Eliminar todos los permisos asignados para volverlos a registrar
        $sqlDelete = "DELETE FROM usuario_permiso WHERE idusuario='$idusuario'";
        ejecutarConsulta($sqlDelete);

        $num_elemento = 0;
        $sw = true;

        //Registro de permisos a un usuario
        while ($num_elemento < count($permisos)){
            $sql_detalle = "INSERT INTO usuario_permiso(idusuario, idpermiso) VALUES ('$idusuario', '$permisos[$num_elemento]')";
            
            ejecutarConsulta($sql_detalle) or $sw = false;

            $num_elemento = $num_elemento + 1;
        }

        return $sw;
    }

    public function desactivar($idusuario){
        $sql = "UPDATE usuario SET condicion='0' WHERE idusuario = '$idusuario'";

        return ejecutarConsulta($sql);
    }

    public function activar($idusuario){
        $sql = "UPDATE usuario SET condicion='1' WHERE idusuario = '$idusuario'";

        return ejecutarConsulta($sql);
    }

    //Metodo para mostrar un registro y actualizar
    public function mostrar($idusuario){
        $sql = "SELECT * FROM usuario WHERE idusuario = '$idusuario'";
        
        return ejecutarConsultaSimpleFila($sql);
    }

    //Metodo para mostrar un registro y actualizar
    public function listar(){
        $sql = "SELECT * FROM usuario";

        return ejecutarConsulta($sql);
    }
     
    //Metodo para mostrar los permisos marcados de un usuario      
    public function listarPermisosMarcados($idusuario){
        $sql = "SELECT * FROM usuario_permiso WHERE idusuario='$idusuario'";

        return ejecutarConsulta($sql);
    }

    //Función para verificar el acceso al sistema mediante login
    public function verificar($login, $clave){
        $sql = "SELECT idusuario, nombre, tipo_documento, num_documento, telefono, email, cargo, imagen, login FROM usuario WHERE login='$login' AND clave='$clave' AND condicion='1' ";

        return ejecutarConsulta($sql);
    }
}
?>