<?php
/*Documento Generado para Consultas sin necesidad de generar clase
Rafael Romero 1/05/14
Ultima Edicion: 27/02/15 por: Rafael
*/
include "Conexion.class.php";

// FUNCIONES EQUIPOS -------------------------------------------------------------------------------

function todoEquipo($equipo,$Host,$User,$Pass,$dbName){
		$con = new Conexion($Host,$User,$Pass,$dbName);
		$sql = "select * from equipo where cve_equipo = ".$equipo.";";
		$datos = $con->query($sql,'arregloAsociado');
		$con->cerrarConexion();
		return $datos;
}

function valEquipo($nom,$Host,$User,$Pass,$dbName){
		$sql= "SELECT * FROM equipo;";
		$con = new Conexion($Host,$User,$Pass,$dbName);
		$datos = $con->query($sql,'arregloAsociado');
		$con->cerrarConexion();
		$res = true;
		for($i = 0;$i < count($datos); $i++){
		    if(trim(strtolower($nom)) === trim(strtolower($datos[$i]['nom_equipo']))){
		       $res = false;
		    }	
		}
		return $res;
}

function agregarEquipo($equipo,$Host,$User,$Pass,$dbName){
		$con = new Conexion($Host,$User,$Pass,$dbName);
		$sqlE = "INSERT INTO equipo (cve_equipo, nom_equipo, estadio, jue_jug, jue_gan, jue_per, jue_emp, gol_fa, gol_con, diferencia_gol, tot_puntos, liga_participa, escudo_equipo) VALUES (null, '".$equipo['nombre']."', '".$equipo['estadio']."', ".$equipo['jj'].", ".$equipo['jg'].", ".$equipo['jp'].", ".$equipo['je'].", ".$equipo['gf'].", ".$equipo['gc'].", ".$equipo['dg'].", ".$equipo['tot'].", ".$equipo['liga'].", ".$equipo['img'].");";
		$retorna = $con->query($sqlE,'afecto?');
		$con->cerrarConexion();
		if($retorna){
				return array('estado'=>1,'clase'=>'notaBien','equipo'=>$equipo,'mensaje'=>'EL EQUIPO SE AGREGO CON EXITO');
		}else{
				return array('estado'=>2,'clase'=>'notaMal','mensaje'=>'NO FUE POSIBLE AGREGAR EL EQUIPO');
		}
		
}

function modificarEquipo(){
		
}

function eliminarEquipo(){
		
}
//---------------------------------------------------------------------------------------------
//funcion para darle estilo a la tabla
function colortr($i){
	$color = "";
	if($i%2==0){ $color = "#9e9da3";}
	else{ $color = "#d5d5d7"; }
	return $color;
}

function fechaFor($fecha){
	$meses=array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
	$diaActual = substr($fecha,8,2); //extraemos el dia 
	$mesActual = substr($fecha,5,2); //extraemos el mes 
	$anioActual = substr($fecha,0,4); //extraemos el a?o 
	return $diaActual." de ".$meses[$mesActual-1]." del ".$anioActual;
}

//*
function optimizarImagen($archivo,$tam)
{
    $mimes     = array('png' => 'png','jpe' => 'jpeg','jpeg' => 'jpeg','jpg' => 'jpeg','gif' => 'gif');
    $extension = @strtolower(end(explode('.',$archivo)));
    if(!empty($mimes[$extension]))
    {
        $tipo         = $mimes[$extension];
        $crear        = 'imagecreatefrom'.$tipo;
        $image        = 'image'.$tipo;
        $imgOriginal  = $crear( $archivo );
        $anchoOrigen  = imagesx( $imgOriginal );
        $altoOrigen   = imagesy( $imgOriginal );
        $ancho_limite = $tam;
        if($anchoOrigen > $altoOrigen)
        {// para foto horizontal
                $anchoOrigen = $ancho_limite;
                $altoOrigen  = $ancho_limite*imagesy( $imgOriginal )/imagesx( $imgOriginal );
        }
        else
        {//para fotos verticales
                $altoOrigen  = $ancho_limite;
                $anchoOrigen = $ancho_limite*imagesx( $imgOriginal )/imagesy( $imgOriginal );
        }
        $imgNuevo = imagecreatetruecolor($anchoOrigen ,$altoOrigen );// se crea la imagen segun las dimensiones dadas
        if($tipo == 'png')
        {
            imagesavealpha($imgNuevo, true); 
            $color = imagecolorallocatealpha($imgNuevo,0x00,0x00,0x00,127 ); 
            imagefill($imgNuevo, 0, 0, $color);
        }
        imagecopyresized( $imgNuevo, $imgOriginal, 0, 0, 0, 0, $anchoOrigen, $altoOrigen, imagesx( $imgOriginal ), imagesy( $imgOriginal ) );
        $image( $imgNuevo, $archivo);//se guarda la nueva imagen
        imagedestroy( $imgOriginal );
        imagedestroy( $imgNuevo );
    }
}

//*
function cargaArchivo($archivo,$ruta,$tmp){
        //comprobamos si existe un directorio para subir el archivo
        //si no es as�, lo creamos
        if(!is_dir($ruta)) 
            mkdir($ruta, 0777);
        //comprobamos si el archivo ha subido
        if ($archivo && move_uploaded_file($tmp,$ruta.$archivo))
        {
           optimizarImagen($ruta.$archivo,200);
		   return true;
        }else{
			return false;	
		}
}

//*
function InserImg($nombre,$tipo,$descripcion,$Host,$User,$Pass,$dbName){
		$sql = "insert into cat_imagenes values(null,'".$nombre."',".$tipo.",'".$descripcion."','".date('Y-m-d')."');";
		$con = new Conexion($Host,$User,$Pass,$dbName);
		$idimg = $con->query($sql,'id');
		return $idimg;
}

//*
function updateImg($idimgact,$imgnva,$tipo,$descripcion,$Host,$User,$Pass,$dbName){
		$idnueva = InserImg($imgnva,$tipo,$descripcion,$Host,$User,$Pass,$dbName);
		if($idimgact != 1){
				eliminaArch($idimgact,$Host,$User,$Pass,$dbName);
		}
		return $idnueva;
}

//*
function eliminaArch($idimgact,$Host,$User,$Pass,$dbName){
		$con = new Conexion($Host,$User,$Pass,$dbName);
		$sql = "delete from cat_imagenes where cve_imagen = ".$idimgact;
		$con->query($sql,'');
		$con->cerrarConexion();
}



// FUNCIONES PARTIDOS -------------------------------------------------------------------------------

//*
function datosTodoPartidosJor($jor,$Host,$User,$Pass,$dbName){
		$con = new Conexion($Host,$User,$Pass,$dbName);
		$sql = "select par.*, loc.nom_equipo 'local', vis.nom_equipo 'visitante' from partido par inner join equipo loc on par.eq_local = loc.cve_equipo inner join equipo vis on par.eq_visitante = vis.cve_equipo	inner join jornada jor on par.Jornada_cve_jornada = jor.cve_jornada where par.Jornada_cve_jornada = ".$jor." order by par.fecha;";
		$datos = $con->query($sql,'arregloAsociado');
		$con->cerrarConexion();
		return $datos;
}
function datosPartidosJor($jor,$Host,$User,$Pass,$dbName){
		$con = new Conexion($Host,$User,$Pass,$dbName);
		$sql = "select par.aplico_tabla,par.torneo_pertenece,par.cve_partido, par.fecha, par.hora, par.eq_local,loc.nom_equipo, par.eq_visitante, vis.nom_equipo,par.Jornada_cve_jornada,jor.dsjornada from partido par inner join equipo loc on par.eq_local = loc.cve_equipo inner join equipo vis on par.eq_visitante = vis.cve_equipo inner join jornada jor on par.Jornada_cve_jornada = jor.cve_jornada where par.Jornada_cve_jornada = ".$jor." order by par.fecha;";
		$datos = $con->query($sql,'arregloAsociado');
		$con->cerrarConexion();
		return $datos;
}
function datosPartido($par,$Host,$User,$Pass,$dbName){
		$con = new Conexion($Host,$User,$Pass,$dbName);
		$sql = "select * from partido where cve_partido =".$par.";";
		$datos = $con->query($sql,'arregloAsociado');
		$con->cerrarConexion();
		return $datos;
}

function agregaPartido($Host,$User,$Pass,$dbName,$locn,$visn,$gl,$gv,$fechan,$horan,$jorn,$resn,$ligan){
		$con = new Conexion($Host,$User,$Pass,$dbName);
		$sqlE = "INSERT INTO partido (cve_partido, fecha, hora, gollocal, golvisitante, resultado_partido, eq_local, eq_visitante, Jornada_cve_jornada, stpartido, motivo_cancel,torneo_pertenece) VALUES (null, '".date('Y-m-d',strtotime($fechan))."', '".$horan."', ".$gl.", ".$gv.", '".$resn."', ".$locn.", ".$visn.", ".$jorn.", 1, '',".$ligan.");";
		$retorna = $con->query($sqlE,'afecto?');
		$con->cerrarConexion();
		return $retorna;
}

function modPartido($Host,$User,$Pass,$dbName,$cveP,$local,$visitante,$fecha,$hora,$jor,$ligan){
		$con = new Conexion($Host,$User,$Pass,$dbName);
		$sqlE = "UPDATE partido SET fecha = '".date('Y-m-d',strtotime($fecha))."', hora = '".$hora."', gollocal = null, golvisitante = null, resultado_partido = '', eq_local = ".$local.", eq_visitante = ".$visitante.", Jornada_cve_jornada = ".$jor.", stpartido = 0, motivo_cancel = '',torneo_pertenece= ".$ligan." WHERE cve_partido = ".$cveP;
		$retorna = $con->query($sqlE,'afecto?');
		$con->cerrarConexion();
		return $retorna;
}
function activarPartido($Host,$User,$Pass,$dbName,$idPart){
	$sql  = 'UPDATE partido SET motivo_cancel=null,stpartido=1 WHERE cve_partido=%d;';
	$con = new Conexion($Host,$User,$Pass,$dbName);
	$afe = $con->query(sprintf($sql,$idPart),'afecto?');
	return $afe;
}

function tablaGeneralAct($par,$Host,$User,$Pass,$dbName,$ope){
	$partidO = datosPartido($par,$Host,$User,$Pass,$dbName);
	$mensaje='';
    $clase = 'notaMal';
    if($partidO[0]['gollocal'] == '' || $partidO[0]['golvisitante'] == ''){
        $mensaje='EL PARTIDO DEBE CONTENER RESULTADO PARA PODER ACTUALIZAR LA TABLA';
        $clase = 'notaMal';
    }else{
		$elocal = todoEquipo($partidO[0]['eq_local'],$Host,$User,$Pass,$dbName);
		$evisitante = todoEquipo($partidO[0]['eq_visitante'],$Host,$User,$Pass,$dbName);
		if($ope == 1){
				if($partidO[0]['resultado_partido'] == 'local'){
						$elocal[1]['jue_jug'] = $elocal[0]['jue_jug'] + 1;
						$elocal[1]['jue_gan'] = $elocal[0]['jue_gan'] + 1;
						$elocal[1]['tot_puntos'] = $elocal[0]['tot_puntos'] + 3;
						$sqlL = "update equipo set jue_jug = ".$elocal[1]['jue_jug'].",jue_gan = ".$elocal[1]['jue_gan'].",tot_puntos = ".$elocal[1]['tot_puntos'].", gol_fa = %d, gol_con= %d,diferencia_gol= %d where cve_equipo = ".$elocal[0]['cve_equipo'].";";
						
						$evisitante[1]['jue_jug'] = $evisitante[0]['jue_jug'] + 1;
						$evisitante[1]['jue_per'] = $evisitante[0]['jue_per'] + 1;
						$sqlV = "update equipo set jue_jug = ".$evisitante[1]['jue_jug'].",jue_per = ".$evisitante[1]['jue_per'].",gol_fa = %d, gol_con= %d,diferencia_gol= %d where cve_equipo = ".$evisitante[0]['cve_equipo'].";";

				}else if($partidO[0]['resultado_partido'] == 'visitante'){
						
						$evisitante[1]['jue_jug'] = $evisitante[0]['jue_jug'] + 1;
						$evisitante[1]['jue_gan'] = $evisitante[0]['jue_gan'] + 1;
						$evisitante[1]['tot_puntos'] = $evisitante[0]['tot_puntos'] + 3;
						$sqlV = "update equipo set jue_jug = ".$evisitante[1]['jue_jug'].",jue_gan = ".$evisitante[1]['jue_gan'].",tot_puntos = ".$evisitante[1]['tot_puntos'].", gol_fa = %d, gol_con= %d,diferencia_gol= %d where cve_equipo = ".$evisitante[0]['cve_equipo'].";";

						$elocal[1]['jue_jug'] = $elocal[0]['jue_jug'] + 1;
						$elocal[1]['jue_per'] = $elocal[0]['jue_per'] + 1;
						$sqlL = "update equipo set jue_jug = ".$elocal[1]['jue_jug'].",jue_per = ".$elocal[1]['jue_per'].",gol_fa = %d, gol_con= %d,diferencia_gol= %d where cve_equipo = ".$elocal[0]['cve_equipo'].";";
						
				}if($partidO[0]['resultado_partido'] == 'empate'){
						
						$evisitante[1]['jue_jug'] = $evisitante[0]['jue_jug'] + 1;
						$evisitante[1]['jue_emp'] = $evisitante[0]['jue_emp'] + 1;
						$evisitante[1]['tot_puntos'] = $evisitante[0]['tot_puntos'] + 1;
						$sqlV = "update equipo set jue_jug = ".$evisitante[1]['jue_jug'].",jue_emp = ".$evisitante[1]['jue_emp'].",tot_puntos = ".$evisitante[1]['tot_puntos'].", gol_fa = %d, gol_con= %d,diferencia_gol= %d where cve_equipo = ".$evisitante[0]['cve_equipo'].";";

						$elocal[1]['jue_jug'] = $elocal[0]['jue_jug'] + 1;
						$elocal[1]['jue_emp'] = $elocal[0]['jue_emp'] + 1;
						$elocal[1]['tot_puntos'] = $elocal[0]['tot_puntos'] + 1;
						$sqlL = "update equipo set jue_jug = ".$elocal[1]['jue_jug'].",jue_emp = ".$elocal[1]['jue_emp'].",tot_puntos = ".$elocal[1]['tot_puntos'].", gol_fa = %d, gol_con= %d,diferencia_gol= %d where cve_equipo = ".$elocal[0]['cve_equipo'].";";

				}
				
				$elocal[1]['gol_fa'] = $elocal[0]['gol_fa'] + $partidO[0]['gollocal'];
				$elocal[1]['gol_con'] = $elocal[0]['gol_con'] + $partidO[0]['golvisitante'];
				$elocal[1]['diferencia_gol'] = $elocal[1]['gol_fa'] - $elocal[1]['gol_con'];
				$evisitante[1]['gol_fa'] = $evisitante[0]['gol_fa'] + $partidO[0]['golvisitante'];
				$evisitante[1]['gol_con'] = $evisitante[0]['gol_con'] + $partidO[0]['gollocal'];
				$evisitante[1]['diferencia_gol'] = $evisitante[1]['gol_fa'] - $evisitante[1]['gol_con'];
				$sqlPartido = "update partido set aplico_tabla = 1 where cve_partido = ".$partidO[0]['cve_partido'].";";
				$con = new Conexion($Host,$User,$Pass,$dbName);
				$afel = $con->query(sprintf($sqlL,$elocal[1]['gol_fa'],$elocal[1]['gol_con'],$elocal[1]['diferencia_gol']),'afecto?');
				$afev = $con->query(sprintf($sqlV,$evisitante[1]['gol_fa'],$evisitante[1]['gol_con'],$evisitante[1]['diferencia_gol']),'afecto?');
				$afep = $con->query($sqlPartido,'afecto?');
				if($afel && $afev){
						$mensaje='LA TABLA GENERAL SE ACTUALIZO CON EXITO';
						 $clase = 'notaBien';
				}else{
						$mensaje='NO SE DETECTARON CAMBIOS';
						 $clase = 'notaBien';
				}
		}else{
				if($partidO[0]['resultado_partido'] == 'local'){
						$elocal[1]['jue_jug'] = $elocal[0]['jue_jug'] - 1;
						$elocal[1]['jue_gan'] = $elocal[0]['jue_gan'] - 1;
						$elocal[1]['tot_puntos'] = $elocal[0]['tot_puntos'] - 3;
						$sqlL = "update equipo set jue_jug = ".$elocal[1]['jue_jug'].",jue_gan = ".$elocal[1]['jue_gan'].",tot_puntos = ".$elocal[1]['tot_puntos'].", gol_fa = %d, gol_con= %d,diferencia_gol= %d where cve_equipo = ".$elocal[0]['cve_equipo'].";";
						
						$evisitante[1]['jue_jug'] = $evisitante[0]['jue_jug'] - 1;
						$evisitante[1]['jue_per'] = $evisitante[0]['jue_per'] - 1;
						$sqlV = "update equipo set jue_jug = ".$evisitante[1]['jue_jug'].",jue_per = ".$evisitante[1]['jue_per'].",gol_fa = %d, gol_con= %d,diferencia_gol= %d where cve_equipo = ".$evisitante[0]['cve_equipo'].";";

				}else if($partidO[0]['resultado_partido'] == 'visitante'){
						
						$evisitante[1]['jue_jug'] = $evisitante[0]['jue_jug'] - 1;
						$evisitante[1]['jue_gan'] = $evisitante[0]['jue_gan'] - 1;
						$evisitante[1]['tot_puntos'] = $evisitante[0]['tot_puntos'] - 3;
						$sqlV = "update equipo set jue_jug = ".$evisitante[1]['jue_jug'].",jue_gan = ".$evisitante[1]['jue_gan'].",tot_puntos = ".$evisitante[1]['tot_puntos'].", gol_fa = %d, gol_con= %d,diferencia_gol= %d where cve_equipo = ".$evisitante[0]['cve_equipo'].";";

						$elocal[1]['jue_jug'] = $elocal[0]['jue_jug'] - 1;
						$elocal[1]['jue_per'] = $elocal[0]['jue_per'] - 1;
						$sqlL = "update equipo set jue_jug = ".$elocal[1]['jue_jug'].",jue_per = ".$elocal[1]['jue_per'].",gol_fa = %d, gol_con= %d,diferencia_gol= %d where cve_equipo = ".$elocal[0]['cve_equipo'].";";
						
				}if($partidO[0]['resultado_partido'] == 'empate'){
						
						$evisitante[1]['jue_jug'] = $evisitante[0]['jue_jug'] - 1;
						$evisitante[1]['jue_emp'] = $evisitante[0]['jue_emp'] - 1;
						$evisitante[1]['tot_puntos'] = $evisitante[0]['tot_puntos'] - 1;
						$sqlV = "update equipo set jue_jug = ".$evisitante[1]['jue_jug'].",jue_emp = ".$evisitante[1]['jue_emp'].",tot_puntos = ".$evisitante[1]['tot_puntos'].", gol_fa = %d, gol_con= %d,diferencia_gol= %d where cve_equipo = ".$evisitante[0]['cve_equipo'].";";

						$elocal[1]['jue_jug'] = $elocal[0]['jue_jug'] - 1;
						$elocal[1]['jue_emp'] = $elocal[0]['jue_emp'] - 1;
						$elocal[1]['tot_puntos'] = $elocal[0]['tot_puntos'] - 1;
						$sqlL = "update equipo set jue_jug = ".$elocal[1]['jue_jug'].",jue_emp = ".$elocal[1]['jue_emp'].",tot_puntos = ".$elocal[1]['tot_puntos'].", gol_fa = %d, gol_con= %d,diferencia_gol= %d where cve_equipo = ".$elocal[0]['cve_equipo'].";";

				}
				
				$elocal[1]['gol_fa'] = $elocal[0]['gol_fa'] - $partidO[0]['gollocal'];
				$elocal[1]['gol_con'] = $elocal[0]['gol_con'] - $partidO[0]['golvisitante'];
				$elocal[1]['diferencia_gol'] = $elocal[1]['gol_fa'] - $elocal[1]['gol_con'];
				$evisitante[1]['gol_fa'] = $evisitante[0]['gol_fa'] - $partidO[0]['golvisitante'];
				$evisitante[1]['gol_con'] = $evisitante[0]['gol_con'] - $partidO[0]['gollocal'];
				$evisitante[1]['diferencia_gol'] = $evisitante[1]['gol_fa'] - $evisitante[1]['gol_con'];
				$sqlPartido = "update partido set aplico_tabla = 0 where cve_partido = ".$partidO[0]['cve_partido'].";";
				$con = new Conexion($Host,$User,$Pass,$dbName);
				$afel = $con->query(sprintf($sqlL,$elocal[1]['gol_fa'],$elocal[1]['gol_con'],$elocal[1]['diferencia_gol']),'afecto?');
				$afev = $con->query(sprintf($sqlV,$evisitante[1]['gol_fa'],$evisitante[1]['gol_con'],$evisitante[1]['diferencia_gol']),'afecto?');
				$afep = $con->query($sqlPartido,'afecto?');
				if($afel && $afev){
						$mensaje='SE DESHICIERON LOS CAMBIOS DE ESTE PARTIDO EN LA TABLA GENERAL';
						 $clase = 'notaBien';
				}else{
						$mensaje='NO SE DETECTARON CAMBIOS';
						 $clase = 'notaBien';
				}
		}
	}
	return array('clase'=>$clase,'mensaje'=>$mensaje);
}

//---------------FUNCIONES REPORTES--------------------------------------------------------

function verJorQuinielas($Host,$User,$Pass,$dbName){
		$con = new Conexion($Host,$User,$Pass,$dbName);
		$sql = "select j.cve_jornada, j.numero_jornada from jornada j inner join partido p on j.cve_jornada = p.Jornada_cve_jornada group by j.cve_jornada;";
		$datos = $con->query($sql,'arregloAsociado');
		$con->cerrarConexion();
		return $datos;
}

function verJorQuinielasR($Host,$User,$Pass,$dbName){
		$con = new Conexion($Host,$User,$Pass,$dbName);
		$sql = "select j.cve_jornada, j.numero_jornada from jornada j inner join quiniela q on j.cve_jornada = q.Jornada_cve_jornada group by j.cve_jornada;";
		$datos = $con->query($sql,'arregloAsociado');
		$con->cerrarConexion();
		return $datos;
}

function verPartidosR($jornada,$Host,$User,$Pass,$dbName){
		$con = new Conexion($Host,$User,$Pass,$dbName);
		$sql = "select el.nom_equipo 'local', ev.nom_equipo 'visitante', p.fecha,p.hora from partido p inner join equipo el on p.eq_local = el.cve_equipo inner join equipo ev on p.eq_visitante = ev.cve_equipo where p.Jornada_cve_jornada = ".$jornada." and stpartido != 0 group by p.fecha asc, p.hora asc,p.cve_partido asc;";
		$datos = $con->query($sql,'arregloAsociado');
		$con->cerrarConexion();
		return $datos;
}
function EliminaPartido($Host,$User,$Pass,$dbName,$cveP){
		return true;
}


?>