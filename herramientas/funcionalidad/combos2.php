<?php
session_start();
ini_set('display_errors','1');

include_once('../config/configdb.php');
include_once('../clases/Conexion_new.php');

$sql = array();
$sql['jornada'] = 'select cve_jornada, numero_jornada from jornada;';


$c = Conexion(CONEXION,SERVIDOR,USUARIO,CLAVE,BASE_SID,PUERTO);
$consulta = (int) $_GET['consulta'];
$select = '';

switch($consulta){
    case 1 : $select = $sql['jornada']; break;
  
    default: $select = null;break;
}

if(!empty($select)){
   $datos = $c->query($select,'arregloNumerico');
   
   if(!empty($edoRepMen)){
        $existe = 0;
        $datos2 = $c->query($edoRepMen,'arregloNumerico');
        //print_r($datos2);
        for($i = 0; $i < count($datos2); $i++){
            for($j = 0; $j < count($datos); $j++){
                if($datos2[$i][1] == $datos[$j][1]){
                    $existe+1;
                }
            }
        if($existe == 0){
            $datos[] = $datos2[$i];
        }
      }
      //array_multisort($datos[0], SORT_ASC, SORT_STRING);
   }   

   for($i = 0; $i < count($datos); $i++){
        $datos[$i][1] = utf8_encode($datos[$i][1]);
   }
   
   #print_r($datos);
    echo json_encode($datos);
   
}

?>