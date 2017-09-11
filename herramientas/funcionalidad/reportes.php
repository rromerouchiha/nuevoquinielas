<?php
ini_set("display_errors",1);
include "../clases/query.php";
include "../seguridad/configdb.php";
include "../../includes/MPDF5/mpdf.php";
include "query.php";
$tipo = $_GET['tp'];
$jor = $_GET['jrnd'];
for($i=1;$i<=18;$i++){
	if(md5($i) == $jor){
		$jornada = $i;
		break;
	}
}
if(md5(1) === $tipo){
	$partidos = verPartidosR($jornada,$Host,$User,$Pass,$dbName);
	$cont = "<table style='text-align:center;border: dashed 2px;'>
				<tr style='background:#C4C4C4;'>
					<td colspan='5'><h1>Quiniela Jornada ".$jornada."</h1></td>
				</tr>
				<tr style='background:#C4C4C4;'>
					<td colspan='2'><strong>Local</strong></td>
					<td><strong>Empate</strong></td>
					<td colspan='2'><strong>Visitante</strong></td>
				</tr>
			";
	$fdef = '';
	for($i = 0; $i < count($partidos); $i++){
		if($partidos[$i]['fecha'] != $fdef){
			$cont .= "<tr style='background:#C4C4C4;'><td colspan='5'><center>".date("d-m-Y",strtotime($partidos[$i]['fecha']))."</center></td></tr>";
			$fdef = $partidos[$i]['fecha'];
		}
		$cont .= "<tr>
			<td><img src='../../includes/img/escudo/".$partidos[$i]['local'].".png' style='width:30px;height:30px'/></td>
			<td>".$partidos[$i]['local']."</td>
			<td>
				<img src='../../includes/img/iconos/chck.png' style='width:20px;height:25px'/>
				<img src='../../includes/img/iconos/chck.png' style='width:20px;height:25px'/>
				<img src='../../includes/img/iconos/chck.png' style='width:20px;height:25px'/>
			</td>
			<td>".$partidos[$i]['visitante']."</td>
			<td><img src='../../includes/img/escudo/".$partidos[$i]['visitante'].".png' style='width:30px;height:30px'/></td>
		</tr>";
	}
	 $cont .= "</table><center>";
	$tabla = "<table style='width:100%;'>
			<tr>
				<td>".$cont."</td>
				<td>".$cont."</td>
			</tr>
			<tr>
				<td>".$cont."</td>
				<td>".$cont."</td>
			</tr>
	</table>";
	$nombre = "quiniela_".$jornada.".pdf";
	$orientacion = 'L';
	
}else if(md5(2) === $tipo){
	
	if(!empty($jornada)){
		$partidos = partidos($jornada);
		$quiniela = quinielasUsu($jornada);
		$tabla = "<h1>Quiniela Jornada ".$jornada."</h1>";
		$tabla .= "<center><table style='width:100%;text-align:center;border-collapse:collapse;'>
					<tr style='background:#000000;color:#ffffff;'>
						<td style='color:#ffffff'>Participante</td>";
		$resultados = $idQ = array();
			for($i = 0; $i < count($partidos); $i++){
				$idQ[] = $partidos[$i]['cve_partido'];
				$tabla .= "<td style='color:#ffffff'>
					<img src='../../includes/img/escudo/".$partidos[$i]['elocal'].".png' style='width:30px;height:30px'/>
					<br/>VS<br/>
					<img src='../../includes/img/escudo/".$partidos[$i]['evisit'].".png' style='width:30px;height:30px'/></td>";
			}
			$tabla .= "</tr>";
			$total  = count($quiniela);
            $total2 = count($idQ);
            foreach($quiniela as $key => $value){
                $aux2 = 0;
                $tabla .= '<tr ><td style="background:black;color:#ffffff;font-size:11px;">'.$value['nombre'].'</td>';
                for($y = 0; $y < $total2; $y++){
                    $tabla .= '<td style="border-style: solid;border-width: 1px;font-size:11px;">'.$value[$idQ[$y]]['resultado'].'</td>';
                }
            }
			$tabla .="</tr>";

	}
	$nombre = "resultados_".$jornada.".pdf";
	$tabla .= "</table></center>";
	//echo $tabla;
	$orientacion = 'P';//exit(0);
}
$mpdf = new mPDF('','',0,'',5,5,10,6,5, 5,$orientacion);
$mpdf->WriteHTML($tabla);
$mpdf->Output($nombre,'I');
exit;

?>