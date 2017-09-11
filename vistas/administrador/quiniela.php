<?php
ini_set('display_errors','1');

include_once('../../herramientas/config/configdb.php');
include_once('../../herramientas/clases/Conexion_new.php');

$jornada = 9;

$tabla =  '

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=Edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		 <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<title>Quinielas</title>
		<link rel="stylesheet" type="text/css" href="../../includes/css/prism.css"/>
		<link rel="stylesheet" type="text/css" href="../../includes/css/bootstrap.min.css"/>
		<!--link rel="stylesheet" type="text/css" href="../../includes/css/estilos.css"/-->
		<script type="text/javascript" src="../../includes/js/jquery-1.11.0.min.js"></script>
		<script type="text/javascript" src="../../includes/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="../../includes/js/funcionesGlobales.js"></script>
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>';
		
		
		


//echo $tabla;


$c = Conexion(CONEXION,SERVIDOR,USUARIO,CLAVE,BASE_SID,PUERTO);


$sqlPart = "select p.fecha, p.hora,  l.nom_equipo 'loc', v.nom_equipo 'vis'  from partido p inner join equipo l on p.eq_local = l.cve_equipo inner join equipo v on v.cve_equipo = p.eq_visitante where p.Jornada_cve_jornada = ".$jornada." order by p.fecha asc, p.hora asc;";
$partidos = $c->query($sqlPart, 'arregloAsociado');

	$tabla.= '<table class="table table-striped" style="width:33%;font-size:10px;border-style: dashed;">
					<tr>
						<th colspan="3" style="color:#fff;background:#000;"><h4><center>Jornada '.$jornada.'</center></h4></th>
					</tr>
					<tr>
						<th><center>Nombre:</center></th>
						<th colspan="2"></th>
					</tr>
                    <tr>
						<th style="color:#fff;background:#000;"><center>Local</center></th>
                        <th style="color:#fff;background:#000;"><center>Resultado</center></th>
						<th style="color:#fff;background:#000;"><center>Visitante</center></th>
					</tr>';
    $fecha = "";
for($i = 0; $i < count($partidos);$i++){
		if($fecha != $partidos[$i]['fecha']){
			$tabla.= '<tr>
						<th colspan="3" style="color:#fff;background:#000;padding:0;"><center>'.$partidos[$i]['fecha'].'</center></th>
					</tr>';
					$fecha = $partidos[$i]['fecha'];
		}
		$tabla .= '<tr>
				<td style="padding:1px;"><center><img src="../../includes/img/equipos/'.$partidos[$i]['loc'].'.png" style="width:30px;"/></center></td>
				<td style="padding:1px; padding-top:5px;"><center>
						<button type="button" class="btn btn-default btn-xs" style="width:20%;font-size:8px !important;padding:4px 0px;background:#fff !important;">L</button>&nbsp;&nbsp;
						<button type="button" class="btn btn-default btn-xs" style="width:20%;font-size:8px !important;padding:4px 0px;background:#fff !important;">E</button>&nbsp;&nbsp;
						<button type="button" class="btn btn-default btn-xs" style="width:20%;font-size:8px !important;padding:4px 0px;background:#fff !important;">V</button>
				</center></td>
				<td style="padding:1px;"><center><img src="../../includes/img/equipos/'.$partidos[$i]['vis'].'.png" style="width:30px;"/></center></td>
		</tr>';
}
	$tabla.= '</table>';
	
	$tabla2 = '<table style="width:100%;">
					<tr><td>'.$tabla.'</td><td>'.$tabla.'</td><td>'.$tabla.'</td></tr>
					<tr><td>'.$tabla.'</td><td>'.$tabla.'</td><td>'.$tabla.'</td></tr>
					
				</table>';
require_once '../../includes/librerias/DOMPDF/autoload.inc.php';
              $dompdf = new Dompdf\Dompdf;
              $dompdf->loadHtml($tabla2);
              $dompdf->setPaper('A4', 'portrait');
              $dompdf->render();
              $dompdf->stream("resultados".$jornada, array("Attachment" => false));



?>