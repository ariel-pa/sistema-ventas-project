<?php
    require_once "global.php";//require_once, si archivo ya esta incluido no volver a incluir

    //Conexion para aceder al servidor de la base de datos
    $conexion = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

    //Cosulta a la base de datos
    mysqli_query($conexion, 'SET NAMES "'.DB_ENCODE.'"');

    //Si tenemos un posible error en la conexion, lo mostramos
    if(mysqli_connect_errno()) {
        printf("Falló conexion a la base de datos: %s\n", mysqli_connect_error());
        exit();
    }

    if(!function_exists('ejecutarConsulta')) {//Verificar si existe la funcion ejecutarConsulta

        function ejecutarConsulta($sql) {
            global $conexion;
            $query = $conexion->query($sql);
            return $query;
        }

        function ejecutarConsultaSimpleFila($sql) {
            global $conexion;
            $query = $conexion->query($sql);
            $row = $query->fetch_assoc();
            return $row;
        }

        function ejecutarConsulta_retornarID($sql) {
            global $conexion;
            $query = $conexion->query($sql);
            return $conexion->insert_id;
        }

        function limpiarCadena($str){
            global $conexion;
            $str = mysqli_real_escape_string($conexion, trim($str));
            return htmlspecialchars($str);
        }
    }
?>


















