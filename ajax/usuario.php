<?php
session_start();
require_once "../models/Usuario.php";

//Instanciar el modelo Persona.
$usuario = new Usuario();

$idusuario = isset($_POST["idusuario"])? limpiarCadena($_POST["idusuario"]):"";
$nombre = isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$tipo_documento = isset($_POST["tipo_documento"])? limpiarCadena($_POST["tipo_documento"]):"";
$num_documento = isset($_POST["num_documento"])? limpiarCadena($_POST["num_documento"]):"";
$direccion = isset($_POST["direccion"])? limpiarCadena($_POST["direccion"]):"";
$telefono = isset($_POST["telefono"])? limpiarCadena($_POST["telefono"]):"";
$email = isset($_POST["email"])? limpiarCadena($_POST["email"]):"";
$cargo = isset($_POST["cargo"])? limpiarCadena($_POST["cargo"]):"";
$login = isset($_POST["login"])? limpiarCadena($_POST["login"]):"";
$clave = isset($_POST["clave"])? limpiarCadena($_POST["clave"]):"";
$imagen = isset($_POST["imagen"])? limpiarCadena($_POST["imagen"]):"";

switch( $_GET["op"]){

    case 'guardaryeditar':

        if(!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name'])){
            $imagen = $_POST["imagenactual"];
        }else{
    
            //verifica extencion y lo optiene
            $ext = explode(".",$_FILES['imagen']['name']);
            if($_FILES['imagen']['type'] == "image/jpg" || $_FILES['imagen']['type'] == "image/jpeg" || $_FILES['imagen']['type'] == "image/png" ){
                $imagen =round(microtime(true)) .'.'. end($ext); //TODO Asignar nombre diferente y concatenera con extencion
                move_uploaded_file($_FILES["imagen"]["tmp_name"], "./../files/usuarios/".$imagen);
            }                
        }            

        //Hash SHA256 en la contraseña
        $claveHash = hash("SHA256", $clave);

        if(empty($idusuario)){
            $rspta = $usuario->insertar($nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $login, $claveHash, $imagen, $_POST['permiso']);
            echo $rspta ? "Usuario registrado" : "No se pudo registrar todos los datos del usuario";
        }else{
            $rspta = $usuario->editar($idusuario, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $login, $claveHash, $imagen, $_POST['permiso']); 
            echo $rspta ? "Usuario actualizado" : "Usuario no se pudo actualizar"; 
        }
    break;
 
    case 'desactivar':
        $rspta = $usuario->desactivar($idusuario);
        echo $rspta ? "Usuario desactivado" : "Usuario no se puede desactivar";
    break;
    
    case 'activar':
        $rspta = $usuario->activar($idusuario);
        echo $rspta ? "Usuario activado" : "Usuario no se puede activar";
    break;

    case 'mostrar':
        $rspta = $usuario->mostrar($idusuario);
        //codificar el resultado utilizando json
        echo json_encode($rspta);
    break;

    case 'listar':
        $rspta = $usuario->listar();

        $data = Array();

        while($reg = $rspta->fetch_object()){
            $data[] = array( 
                // Condicionar para activar y desactivara categoria
                "0"=>($reg->condicion)?'<button class="btn btn-warning" onclick="mostrar('.$reg->idusuario.')"> <i class="fa fa-pencil"></i> </button>'.' <button class="btn btn-danger" onclick="desactivar('.$reg->idusuario.')"><i class="fa fa-close"></i> </button>':'<button class="btn btn-warning" onclick="mostrar('.$reg->idusuario.')"> <i class="fa fa-pencil"></i> </button>'.' <button class="btn btn-primary" onclick="activar('.$reg->idusuario.')"><i class="fa fa-check"></i> </button>',
                "1"=>$reg->nombre,
                "2"=>$reg->tipo_documento,
                "3"=>$reg->num_documento,
                "4"=>$reg->telefono,
                "5"=>$reg->email,
                "6"=>$reg->login,
                "7"=>"<img src='../files/usuarios/".$reg->imagen."' height='50px' width='50px'>  ",
                "8"=>($reg->condicion)?'<span class="label bg-green">Activado</span>':'<span class="label bg-red">Desactivado</span>'
            );
        }

        $results = array(
            "sEcho"=>1, //Informacion para el datatables
            "iTotalRecords"=>count($data),//enviamos el total registro al datatable
            "iTotalDisplayRecords"=>count($data),//enviamos el total registro a visualizar.
            "aaData"=>$data);
        echo json_encode($results);

    break;

    case 'permisos':
        //Optenemos todos los permisos de la tabla permisos
        require_once "../models/Permiso.php";

        $permiso = new Permiso();
        $rspta = $permiso->listar();

        //Obtener los permisos asignados a un usuario $id=$idusuario
        $id = $_GET['id'];
        $marcados = $usuario->listarPermisosMarcados($id);

        //Declaramos un Array para almacenar todos los permisos marcados
        //Almacenar los permisos asignados al usuario en el array
        $valores = array();
        while($per = $marcados->fetch_object()){
            //Almacenamos los registor en el array
            array_push($valores, $per->idpermiso);
        }

        //Mostrar la lista de permisos en la vista
        while($reg = $rspta->fetch_object()){
            //TODO:Realizamos uns condiconal para ver si exixten los permisos marcados
            $sw = in_array($reg->idpermiso, $valores)?'checked':'';//Ver si permisos se encuentran almacenados dentro de $valores

            //permiso[]: es un array de permisos, cada permiso tendra una identificación unica
            echo '<li> <input type="checkbox" '.$sw.' name="permiso[]" value="'.$reg->idpermiso.'"> '.$reg->nombre.'</li>';
        }
    break;

    case 'verificar':
        //Optener datos del formulario login
         $login = $_POST['loginAcceso'];
         $clave = $_POST['claveAcceso'];

         //Hash SHA256 en la contraseña
         $claveHash = hash("SHA256", $clave);
         $rspta = $usuario->verificar($login, $claveHash);

         $fetch = $rspta->fetch_object();
        //Verificar objeto fetch no este vacio
         if(isset($fetch)){
            //Decalramos las variables de sesión
            $_SESSION['idusuario'] = $fetch->idusuario;
            $_SESSION['nombre'] = $fetch->nombre;
            $_SESSION['imagen'] = $fetch->imagen;
            $_SESSION['login'] = $fetch->login;

            //Obtiene todos los permisos del usuario logueado
            $permisosUsuario = $usuario->listarPermisosMarcados($fetch->idusuario);

            //Declaramos el array para almacenar todos los permisos marcados
            $valores = array();

            //Almacenamos los permisos marcados en el array
            while($permiso = $permisosUsuario->fetch_object()){
                array_push($valores, $permiso->idpermiso);
            }

            //Determinamos los acceso del usuario
            in_array(1, $valores)?$_SESSION['escritorio']=1:$_SESSION['escritorio']=0;
            in_array(2, $valores)?$_SESSION['almacen']=1:$_SESSION['almacen']=0;
            in_array(3, $valores)?$_SESSION['compras']=1:$_SESSION['compras']=0;
            in_array(4, $valores)?$_SESSION['ventas']=1:$_SESSION['ventas']=0;
            in_array(5, $valores)?$_SESSION['acceso']=1:$_SESSION['acceso']=0;
            in_array(6, $valores)?$_SESSION['consultacompras']=1:$_SESSION['consultacompras']=0;
            in_array(7, $valores)?$_SESSION['consultaventas']=1:$_SESSION['consultaventas']=0;
         }
         echo json_encode($fetch); 
    break;

    case 'salir':
        //Limpieza de todas las variables de sesión
        session_unset();
        //Destruimos las sesiones
        session_destroy();
        //Redireccionamos al login
        header("location: ../index.php");

    break;
}
?>