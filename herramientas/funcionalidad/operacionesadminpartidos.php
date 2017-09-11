<?php
ini_set('display_errors','1');
extract($_POST);
//$operacion = $_POST['operacion'];

include "../clases/query.php";
include "../seguridad/configdb.php";

if($operacion == 1){
    $jornada = ($jornada != '')? $jornada:0;
    $par = datosPartidosJor($jornada,$Host,$User,$Pass,$dbName);
    if(count($par)>0){
        echo "  
            <center><img src='../../includes/img/iconos/cargando.gif' id='carga2' style='width:60px;display:none;'/>	
                <form id='feditarp' enctype='multipart/form-data' method='POST' action='../../herramientas/funcionalidad/operacionesadminusuario.php'>
                <table class='tb_100 oculta'>
                    <tr>
                       <td colspan='8' ><div id='nota' style='display:none'></div></td>
                   </tr>
                   <tr class='cabecera' style='font-size:.8em;'>
                       <td>Local</td>
                       <td>Visitante</td>
                       <td>Fecha</td>
                       <td>Hora</td>
                       <td>Jornada</td>
                       <td>Liga</td>
                       <td colspan='2'>Operaci&oacute;n</td>
                       
                   </tr>
                 ";
                   for($i=0;$i<count($par);$i++){
                        echo "<tr id='conMen".$par[$i]['cve_partido']."' style='background:rgba(255,255,255,0.4);display:none;'>
                            <td colspan='8'>
                                <p id='men".$par[$i]['cve_partido']."' ></p>
                            </td>
                        </tr>
                        <tr style='background:rgba(255,255,255,0.4)'>
                            <td>
                                <input type='hidden' class='cveParG' id='idpar' name='".$par[$i]['cve_partido']."' value='".$par[$i]['cve_partido']."'/>
                                <input type='hidden' class='tablag' id='tablag".$par[$i]['cve_partido']."' name='tablag".$par[$i]['cve_partido']."' value='".$par[$i]['aplico_tabla']."'/>

                                <select id='loc".$par[$i]['cve_partido']."' class='listas equiG' name='loc".$par[$i]['cve_partido']."'></select>
                            </td>
                            <td>
                                <select id='vis".$par[$i]['cve_partido']."' class='listas equiG' name='vis".$par[$i]['cve_partido']."'></select>
                            </td>
                            <td>
                                <input type='text' name='fecha".$par[$i]['cve_partido']."' class='fecha ' id='fecha".$par[$i]['cve_partido']."' value='".date('d-m-Y',strtotime($par[$i]['fecha']))."'/>
                            </td>
                            <td>
                                <input type='text' name='hora".$par[$i]['cve_partido']."' class='' id='hora".$par[$i]['cve_partido']."' value='".$par[$i]['hora']."'/>
                            </td>
                             <td>
                                <input type='hidden' id='jorv".$par[$i]['cve_partido']."' name='jorv".$par[$i]['cve_partido']."' value='".$par[$i]['Jornada_cve_jornada']."'/>
                                <select id='jor".$par[$i]['cve_partido']."' name='jor".$par[$i]['cve_partido']."' class='listas jorG'></select>
                            </td>
                            <td>
                                <select id='liga".$par[$i]['cve_partido']."' name='liga".$par[$i]['cve_partido']."' class='listas liga'>
                                    <option value='1'>Liga MX</option>
                                    <option value='0'>Otra Liga</option>
                                </select>
                            </td>
                            <td>
                                <input type='button' class='boton editarP' id='edit".$par[$i]['cve_partido']."' name='edit".$par[$i]['cve_partido']."' for='".$par[$i]['cve_partido']."' value='Editar'/>
                            </td>
                            <td>
                                <input type='button' class='boton eliminarP' id='del".$par[$i]['cve_partido']."' name='del".$par[$i]['cve_partido']."' for='".$par[$i]['cve_partido']."' value='Borrar'/>
                            </td>
                        </tr>";
                   }
                   echo "<tr style='background:rgba(255,255,255,0.4)'>
                            <td colspan='6'></td>
                            <td colspan='2'>
                                <input type='button' class='boton' id='editarPG' name='editarPG' value='Editar Todo'/>
                                <input type='button' class='boton' id='editarPG' name='editarPG' value='Borrar Todo'/>
                            </td>
                        </tr>
                   </table>
                   <br/><div id='confirmE'></div>
                        <script>
                            EvePartidos(); 
                        </script>";
                   for($i=0;$i<count($par);$i++){
                        echo "<script>
                            $('#loc".$par[$i]['cve_partido']."').val(".$par[$i]['eq_local'].");
                            $('#vis".$par[$i]['cve_partido']."').val(".$par[$i]['eq_visitante'].");
                            $('#jor".$par[$i]['cve_partido']."').val(".$par[$i]['Jornada_cve_jornada'].");
                            $('#liga".$par[$i]['cve_partido']."').val(".$par[$i]['torneo_pertenece'].");
                            $('.listas').trigger('chosen:updated');
                            </script>";
                   }
    }else{
        echo "<center><img src='../../includes/img/iconos/cargando.gif' id='carga2' style='width:60px;display:none;'/>	
        <p class='oculta'><br/>NO HAY PARTIDOS PARA ESTA JORNADA</p>";
    }
    
    
}else if($operacion == 2){
    //Modifica Partido
    $par = datosPartido($cveP,$Host,$User,$Pass,$dbName);
    if($par[0]['aplico_tabla'] == 1 && $par[0]['torneo_pertenece'] == 1){
        if($par[0]['eq_local'] != $local || $par[0]['eq_visitante'] != $visitante){
            tablaGeneralAct($cveP,$Host,$User,$Pass,$dbName,2);
        }
    }
   
    if(modPartido($Host,$User,$Pass,$dbName,$cveP,$local,$visitante,$fecha,$hora,$jor,$liga)){
        $respuesta = array('mensaje' => 'El partido se actualizo con &Eacute;xito', 'clase' =>'notaBien','estado' => 1);
    }else{
        $respuesta = array('mensaje' => 'No fue Posible actualizar el partido', 'clase' =>'notaMal','estado' => 0);
    }
    echo json_encode($respuesta);
    
}else if($operacion == 3){
    //panel eliminar
	echo "
    <div id='realdelp' class='resultadospost oculta' ><h4>Esta seguro que Desea Eliminar el partido <strong>&nbsp;&nbsp;".$loc." VS ".$vis."&nbsp;&nbsp;
		<input type='button' name='delete' id='delete' value='Si' class='boton' onclick='deletePartido(".$cveP.",\"".$loc."\",\"".$vis."\",1);'/>
		<input type='button' name='cancelar2' id='cancelar2' value='No' class='boton' onclick='ocultameDel(\'realdelp\');' /></h4>
	</div>
	<script>$('.boton').button();</script>
	";
}else if($operacion == 4){
    //agregar partido
    $gl = ($gl != '')? $gl: "null";
    $gv = ($gv != '')? $gv : "null";
    //echo $gl."--".$gv;
    if(agregaPartido($Host,$User,$Pass,$dbName,$locn,$visn,$gl,$gv,$fechan,$horan,$jorn,$resn,$ligan)){
        $respuesta = array('mensaje' => 'El partido se agrego con &Eacute;xito', 'clase' =>'notaBien','estado' => 1);
    }else{
        $respuesta = array('mensaje' => 'El partido pudo ser agregado', 'clase' =>'notaMal','estado' => 0);
       
    }
    echo json_encode($respuesta);
}else if($operacion == 5){
    if(EliminaPartido($Host,$User,$Pass,$dbName,$cveP)){
        $respuesta = array('mensaje' => 'El partido se agrego con &Eacute;xito', 'clase' =>'notaBien','estado' => 1);
    }else{
        $respuesta = array('mensaje' => 'El partido pudo ser agregado', 'clase' =>'notaMal','estado' => 0);
    }
    
}else if($operacion == 6){
    //panel resultados partidos
    $jornada = ($jornada != '')? $jornada:0;
    $par = datosTodoPartidosJor($jornada,$Host,$User,$Pass,$dbName);
    //print_r($par);
    if(count($par)>0){
        echo "  
            <center><img src='../../includes/img/iconos/cargando.gif' id='carga2' style='width:60px;display:none;'/>	
                <form id='feditarp' enctype='multipart/form-data' method='POST' action='../../herramientas/funcionalidad/operacionesadminusuario.php'>
                <table class='tb_100 oculta'>
                    <tr>
                        <td><h1>Quiniela ".$jornada."</h1></td>
                        <input type='hidden' id='jornadaR' name='jornadaR' value='".$jornada."'/>
                    </tr>
                    <tr style='display:none'>
                       <td colspan='8' ><div id='nota' ></div></td>
                   </tr>
                   <tr class='cabecera' style='font-size:.8em;'>
                       <td>Cancelar</td>
                       <td>Motivo</td>
                       <td>Local</td>
                       <td>Goles L.</td>
                       <td>Resultado</td>
                       <td>Goles V.</td>
                       <td>Visitante</td>
                       <td colspan='2'>Operaci&oacute;n</td>
                   </tr>
                 ";
                   for($i=0;$i<count($par);$i++){
                        $bcancelar = ($par[$i]['aplico_tabla'] == 0)? ($par[$i]['stpartido'] == 1 )? "<td><input type='button' class='boton cancelarP' id='cancel".$par[$i]['cve_partido']."' name='cancel".$par[$i]['cve_partido']."' for='".$par[$i]['cve_partido']."' value='Cancelar Partido'/></td>": "<td><input type='button' class='boton activarP' id='activ".$par[$i]['cve_partido']."' name='activ".$par[$i]['cve_partido']."' for='".$par[$i]['cve_partido']."' value='Activar'/></td>":"<td></td>" ;
                        $btabla = ($par[$i]['torneo_pertenece']==1)? ($par[$i]['aplico_tabla']==0)? "<input type='button' class='boton aplicarT' id='apliT".$par[$i]['cve_partido']."' name='apliT".$par[$i]['cve_partido']."' for='".$par[$i]['cve_partido']."' value='Aplicar a Tabla'/>":"<input type='button' class='boton revertirT' id='revT".$par[$i]['cve_partido']."' name='revT".$par[$i]['cve_partido']."' for='".$par[$i]['cve_partido']."' value='Revertir en Tabla'/>":"<p style='font-size:12px;color:#ffffff;'>Partido de<br/>otra liga</p>";
                        $bactualizar = ($par[$i]['aplico_tabla'] == 0)? "<input type='button' class='boton editarP' id='edit".$par[$i]['cve_partido']."' name='edit".$par[$i]['cve_partido']."' for='".$par[$i]['cve_partido']."' value='Actualizar'/>": "<p style='font-size:12px;color:#ffffff;'>Para modificar deshaga cambios<br/> en tabla general</p>";
                        echo "<tr id='conMen".$par[$i]['cve_partido']."' style='background:rgba(255,255,255,0.4);display:none;'>
                            <td colspan='9'>
                                <p id='men".$par[$i]['cve_partido']."' ></p>
                                <input type='hidden' class='cveParG' id='idpar' name='".$par[$i]['cve_partido']."' value='".$par[$i]['cve_partido']."'/>
                                <input type='hidden' id='cveloc".$par[$i]['cve_partido']."' name='cveloc".$par[$i]['cve_partido']."' value='".$par[$i]['eq_local']."'/>
                                <input type='hidden' id='cvevis".$par[$i]['cve_partido']."' name='cvevis".$par[$i]['cve_partido']."' value='".$par[$i]['eq_visitante']."'/>
                            </td>
                        </tr>
                        <tr style='background:rgba(255,255,255,0.4)' id='contenedor".$par[$i]['cve_partido']."'>
                            ".$bcancelar."
                            <td><input type='text' class='motCan campoform' id='motCan".$par[$i]['cve_partido']."' name='motCan".$par[$i]['cve_partido']."' placeholder='Motivo' value='".$par[$i]['motivo_cancel']."'/></td>
                            <td><img src='../../includes/img/escudo/".$par[$i]['local'].".png' style='width:35px;'/></td>
                            <td><select id='gloc".$par[$i]['cve_partido']."' class='goles listas' name='gloc".$par[$i]['cve_partido']."' for='".$par[$i]['cve_partido']."'>
                                    <option value='' ></option>";
                                    for($j = 0; $j <= 9; $j++){
                                        echo "<option value='".$j."' >".$j."</option>";
                                    }
                            echo "</select></td>
                            <td><select name='resultado' id='res".$par[$i]['cve_partido']."' class='requerido listas' disabled='true'>
                                    <option value=''>Resultado</option>
                                    <option value='local'>Local</option>
                                    <option value='empate'>Empate</option>
                                    <option value='visitante'>Visitante</option>
                            </select></td>
                            <td><select id='gvis".$par[$i]['cve_partido']."' class='goles listas' name='gvis".$par[$i]['cve_partido']."' for='".$par[$i]['cve_partido']."'>
                                    <option value='' ></option>";
                                    for($j = 0; $j <= 9; $j++){
                                        echo "<option value='".$j."' >".$j."</option>";
                                    }
                            echo "</select></td>
                            <td><img src='../../includes/img/escudo/".$par[$i]['visitante'].".png' style='width:35px;'/></td>
                            <td>".$bactualizar."</td>
                            <td>".$btabla."</td>
                        </tr>";
                   }
                   echo "
                   </table>
                   <br/><div id='confirmE'></div>
                        <script>
                            EvePartidos();
                            modResultadosPartidos();
                        </script>";
                   for($i=0;$i<count($par);$i++){
                        echo "<script>
                            $('#gloc".$par[$i]['cve_partido']."').val(".$par[$i]['gollocal'].");
                            $('#gvis".$par[$i]['cve_partido']."').val(".$par[$i]['golvisitante'].");
                            $('#res".$par[$i]['cve_partido']."').val('".$par[$i]['resultado_partido']."');
                            $('.listas').trigger('chosen:updated');
                            </script>";
                   }
    }else{
        echo "<center><img src='../../includes/img/iconos/cargando.gif' id='carga2' style='width:60px;display:none;'/>	
        <p class='oculta'><br/>NO HAY PARTIDOS PARA ESTA JORNADA</p>";
    }
}else if($operacion == 7){
    //activar partido
    activarPartido($Host,$User,$Pass,$dbName,$idpart);
    $respuesta = array('estatus'=>'OK','accion'=>'x','contenido'=>$_POST);
    echo json_encode($respuesta);
}else if($operacion == 8){
    //APLICAR TABLA
    $respuesta =tablaGeneralAct($idpart,$Host,$User,$Pass,$dbName,1);
    echo json_encode($respuesta);
}
else if($operacion == 9){
    //DESHACER TABLA
    $respuesta =tablaGeneralAct($idpart,$Host,$User,$Pass,$dbName,2);
    echo json_encode($respuesta);
}



?>