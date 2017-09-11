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

$sqlJor = 'select p.*, l.nom_equipo "loc", v.nom_equipo "vis" from partido p inner join equipo l on p.eq_local = l.cve_equipo inner join equipo v on v.cve_equipo = p.eq_visitante where p.Jornada_cve_jornada = '.$jornada.' order by p.fecha asc, p.hora asc;';
$partidos = $c->query($sqlJor, 'arregloAsociado');


$ususql = "select idusu 'id', concat(nombre_usu, ' ', apellidopa_usu) 'nombre' from usuario where idusu in(select Usuario_idusu from quiniela where Jornada_cve_jornada = ".$jornada." group by Usuario_idusu) ;";
$usuarios = $c->query($ususql,'arregloAsociado');
//print_r($usuarios);

$resultados = array();
$cabecera = array('Participante');

for($i = 0; $i < count($partidos); $i++){
    $cabecera[] = '<center><img src="../../includes/img/equipos/'.$partidos[$i]['loc'].'.png" style="width:25px;"/><br/> vs <br/><img src="../../includes/img/equipos/'.$partidos[$i]['vis'].'.png" style="width:25px;"/></center>';
    for($j = 0; $j < count($usuarios); $j++){
        $resulsql = "select resultado_quiniela, comodin from quiniela where Partido_cve_partido = ".$partidos[$i]['cve_partido']." and Usuario_idusu = ".$usuarios[$j]['id'].";";
        $resultados = $c->query($resulsql,'registro');
        if(!empty($resultados['comodin'])){
            $usuarios[$j][$partidos[$i]['cve_partido']] = strtoupper(substr($resultados['resultado_quiniela'],0,1) . " - ". substr($resultados['comodin'],0,1));
        }else{
            $usuarios[$j][$partidos[$i]['cve_partido']] = strtoupper(substr($resultados['resultado_quiniela'],0,1));
        }
    }
}

//print_r($usuarios); exit();

$cabecera[] = "Total";
//$tab = new Tabulador($usuarios,$cabecera,1,100,array(0,0,0,0,0),0,array());
$tabla .=  '<div style="width:100%;">';
//$tabla .=  $tab->muestraTabla();
                $tabla.= '<h1>Jornada '.$jornada.' <small>Premio : $ '.(count($usuarios)*20).'</small></h1><table class="table table-bordered">
                            <tr>
                                <th style="color:#fff;background:#000;">N.</th>';
                            for($i = 0; $i < count($cabecera); $i++){
                                $tabla .= '<th style="color:#fff;background:#000;">'.$cabecera[$i].'</th>';
                            }
                $tabla .= '</tr>';
                            for($i = 0; $i < count($usuarios); $i++){
                                unset($usuarios[$i]['id']);
                                $tabla.= '  <tr style="font-size:10px !important;">
                                    <th style="color:#fff;background:#000;">'.($i + 1).'</th>';
                                $cont = 0;
                                foreach($usuarios[$i] as $clave => $valor){
                                    if($cont < 1){
                                        $tabla .= '<th style="color:#fff;background:#000;"><center>'.$valor.'</center></th>';
                                    }else{
                                        $tabla .= '<th><center>'.$valor.'</center></th>';
                                    }
                                    $cont++;
                                }
                                $tabla .= ' <td></td></tr>';   
                            }
                $tabla.= '<table>';
            
$tabla .=  '</div><br/><br/>';
$tabla .= '
    </body>
</html>';


//echo $tabla;

require_once '../../includes/librerias/DOMPDF/autoload.inc.php';
              $dompdf = new Dompdf\Dompdf;
              $dompdf->loadHtml($tabla);
              $dompdf->setPaper('A4', 'portrait');
              $dompdf->render();
              $dompdf->stream("resultados".$jornada, array("Attachment" => false));

?>