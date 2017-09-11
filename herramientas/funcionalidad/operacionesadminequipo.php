<?php
ini_set('display_errors','1');

$operacion = $_POST['operacion'];
extract($_POST);
include "../clases/query.php";
include "../seguridad/configdb.php";
if($operacion == 1){//agregar equipo
	//print_r($_POST);exit(0);
	$fileperfil = 0;
	//print_r($_FILES);exit(0);
	if($_FILES['imgeq']['error'] == 0){
		$sep = explode('image/',$_FILES["imgeq"]["type"]); // Separamos image/
        $tipo=$sep[1]; // Optenemos el tipo de imagen que es
		$nombre_foto = trim($nome0).".".$tipo;
		cargaArchivo($nombre_foto,"../../includes/img/escudo/",$_FILES['imgeq']['tmp_name']);
		$idimg = InserImg($nombre_foto,2,'Foto Equipo',$Host,$User,$Pass,$dbName);
		if($idimg != '' && $idimg != null){
			$fileperfil = $idimg;
		}
	}
	
	$equipo = array();
	$equipo['nombre'] = trim($nome0);
	$equipo['estadio'] = trim($estadio0);
	$equipo['liga'] = $liga0;
	$equipo['jj'] = ($jj0 == '')? 0 : $jj0 ;
	$equipo['jg'] = ($jg0 == '')? 0 : $jg0 ;
	$equipo['jp'] = ($jp0 == '')? 0 : $jp0 ;
	$equipo['je'] = ($je0 == '')? 0 : $je0 ;
	$equipo['gf'] = ($gf0 == '')? 0 : $gf0 ;
	$equipo['gc'] = ($gc0 == '')? 0 : $gc0 ;
	$equipo['dg'] = ($dif0 == '')? 0 : $dif0 ;
	$equipo['tot'] = ($tot0 == '')? 0 : $tot0 ;
	$equipo['img'] = $fileperfil;
	$equipo['imagen'] = "../../includes/img/escudo/".$nombre_foto;
	
	$res = agregarEquipo($equipo,$Host,$User,$Pass,$dbName);
	
	echo json_encode($res);
	
}else if($operacion == 2){
	//validar equipo
	$nom = $_POST['equipo'];
	$respuesta = array();
	if($nom != ''){
		if(!valEquipo($nom,$Host,$User,$Pass,$dbName)){
			$respuesta['edo'] = 2;
			$respuesta['mensaje'] = "EL EQUIPO \"".$nom."\" YA EXISTE, INGRESE OTRO NOMBRE";
			$respuesta['clase'] = "notaMal";
		}else{
			$respuesta['edo'] = 1;
		}
	}else{
		$respuesta['edo'] = 3;
	}
	echo json_encode($respuesta);
	
}else if($operacion == 3){
	

}



?>