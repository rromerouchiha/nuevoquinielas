<?php

class Tabulador {
    private $datos = array();
    private $datos_por_pagina;
    private $cabecera = array();
    private $pagina_actual;
    private $paginas = array();
    private $total_reg;
    private $total_paginas;
    private $botones = array();
    private $cualver ;
    private $llamada = null;
    
    public function __construct($datos,$cabecera,$pagina_actual,$datos_por_pagina,$botones,$cualver,$llamada = null){
        $this->datos = $datos;
        $this->cabecera = $cabecera;
        $this->pagina_actual = $pagina_actual;
        $this->total_reg = count($datos);
        $this->datos_por_pagina = $datos_por_pagina;
        $this->total_paginas = (($this->total_reg/$this->datos_por_pagina) > 1)? ceil(($this->total_reg/$this->datos_por_pagina)) : 1;
        $this->botones = $botones;
        $this->cualver = $cualver;
        $this->llamada = $llamada;
    }
    
    public function creaPaginas(){
        $inicio = 1;
        $fin = $this->datos_por_pagina;
        
        for($i = 0;$i < $this->total_paginas; $i++){
            $this->paginas[$i]['inicio'] = $inicio;
            if($this->total_reg < $fin){
                $fin = $this->total_reg;
            }
            $this->paginas[$i]['fin'] = $fin;
            $inicio = ($fin+1);
            $fin = ($fin + $this->datos_por_pagina);
        }
        //print_r($this->paginas);
    }
    public function imprimeTabla(){
        $jsLlamada = "<script>";
        $tabla = '
        <div class="CTble">';
            if($this->botones[4] == 1){
                 $tabla .= ' <div id="infoT">Total de Registros: <strong>'.count($this->datos).'</strong>, Total de p&aacute;ginas:<strong>'.$this->total_paginas.'</strong></div>';
            }
           echo '
            <table class="table table-striped">
                <thead>
                    <tr class="success">';
                        if($this->botones[0] == 1){
                           $tabla .= '<th><input type="checkbox" id="todos" value="todos" class=""></th>';
                        }
                        $tabla .= '
                        <th>No.</th>';
                        foreach($this->cabecera as $clave => $valor){
                                 $tabla .= '<th>'.$valor.'</th>';
                        }
                        if($this->botones[2] == 1 || $this->botones[3] == 1){
                           $tabla .= '<th class="text-center">&nbsp;&nbsp;&nbsp;Acci&oacute;n&nbsp;&nbsp;&nbsp;</th>';
                        }
        
                    $tabla .= '
                    </tr>
                </thead>
                <tbody>
        ';
        
        for($i = $this->paginas[($this->pagina_actual-1)]['inicio']; $i <= $this->paginas[($this->pagina_actual-1)]['fin']; $i++){
            $con = 0;
            $tabla .= '<tr id="'.$this->datos[($i-1)]['id'].'" class="evenTabla">';
                        if($this->botones[0] == 1){
                           $tabla .= '<td><input type="checkbox" id="'.$this->datos[($i-1)]['id'].'" value="'.$this->datos[($i-1)]['id'].'" class="seleccionado" clave="'.$this->datos[($i-1)]['id'].'"></td>';
                        }
                           $tabla .= '<td>'.$i.'</td>';
            foreach($this->datos[($i-1)] as $cve => $val){
                if($con > 0){
                    if($con == $this->cualver){
                        if($this->botones[1] == 1){
                            $tabla .= '<td><a href="#" class="ver" for="'.$this->datos[($i-1)]['id'].'">'.utf8_encode($val).'</a></td>';
                        }else{
                            $tabla .= '<td>'.utf8_encode($val).'</td>';
                        }
                    }else{
                        $tabla .= '<td id="'.$cve.$this->datos[($i-1)]['id'].'">'.utf8_encode($val).'</td>';
                    }
                }
                $con++;
            }
            if($this->botones[2] == 1 || $this->botones[3] == 1){
                $tabla .= '<td class="text-center">';
                if($this->botones[2] == 1){
                    $tabla .= '<button name="modificar'.$this->datos[($i-1)]['id'].'" id="modificar'.$this->datos[($i-1)]['id'].'" for="'.$this->datos[($i-1)]['id'].'" class="modificar btn btn-primary btn-xs"><span class="glyphicon glyphicon-edit"></span></button>&nbsp;';
                }
                if($this->botones[3] == 1){
                    $tabla .= '&nbsp;<button name="eliminar'.$this->datos[($i-1)]['id'].'" id="eliminar'.$this->datos[($i-1)]['id'].'" for="'.$this->datos[($i-1)]['id'].'" class="eliminar btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></button>';
                }
                $tabla .= '</td>';
            }
             
             $tabla .= '</tr>';
             
             if($this->llamada != null){
                for($k = 0; $k < count($this->llamada); $k++){
                    $jsLlamada .= $this->devolverLlamada($this->llamada[$k],$this->datos[($i-1)]);
                }
             }
        }
        $tabla .= '
                </tbody>
            </table>
        </div>';
        return $tabla.$jsLlamada."</script>";
    }
    
    public function botonPag(){
        $btn = "";
        $pa = $this->pagina_actual;
        if($this->total_reg > $this->datos_por_pagina){
            $btn .= '<center><div class="btn-group">';
            for($i = 1; $i <= $this->total_paginas; $i++){
                $activo = ($i == $this->pagina_actual)? "active" : "";
                $btn .= '<button type="button" class="btn btn-default btn-paginador btn-xs '.$activo.'" pagina="'.$i.'">'.$i.'</button>';
            }
            $btn .= '</div></center><br/>';
        }
        return $btn;
    }
    
    public function muestraTabla(){
        $this->creaPaginas();
        return $this->imprimeTabla().$this->botonPag();
    }
    
    public function devolverLlamada($cual,$datos){
        $llamada = "";
        switch($cual){
            case 1 :
               // print_r($datos);
                $r = $this->obtenerRol($_SESSION['rol_usuario_id']);
                //echo $r;
                if($r != 1 && $r != 5){
                    date_default_timezone_set('America/Mexico_City');
                    $mnow = date('n');
                    $registro = new DateTime($datos['recepcion']);
                    $registro->format('n');
                    $meses = ($mnow - $registro->format('n')).",";
                    if($meses > 1){
                        $llamada .= '$("#modificar'.$datos['id'].', #eliminar'.$datos['id'].'").prop("disabled",true).removeClass("modificar eliminar btn-primary btn-danger");';
                    }
                }
            break;
            case 2 :
                if(!empty($datos['Estatus real']) && ($datos['Estatus real'] == 'Eliminada' || $datos['Estatus real'] == 'Concluida' )){
                    $llamada .= '$("#modificar'.$datos['id'].',#eliminar'.$datos['id'].'").prop("disabled",true).removeClass("eliminar modificar btn-danger btn-primary");';
                }
            break;
            case 3 :
                $r = $this->obtenerRol($_SESSION['rol_usuario_id']);
                //echo $r;
                if($r == 2 || $r == 3){
                    if($datos['Estatus Ficticio'] == 'Turnada a DNPP'){
                        $llamada .= '$("#eliminar'.$datos['id'].'").prop("disabled",true).removeClass("eliminar btn-danger");';
                    }
                }
            break;
            case 4 :
                $r = $this->obtenerRol($_SESSION['rol_usuario_id']);
                if($r == 2 || $r == 3){
                    $editar = 0;
                    date_default_timezone_set('America/Mexico_City');
                    $recepcion = date($datos['recepcion']);
                    $month = substr($recepcion, 3, 2);
                    $year = substr($recepcion, 6, 4);
                    $diasMes = $this->getMonthDays($month, $year);
                    $inicioMes =  date('Y-m-d', mktime(0,0,0, $month, 1, $year));
                    $ultimoDiaAct = strtotime ( '+'.($diasMes+3).' day' , strtotime ( $inicioMes ) ) ;
                    $ultimoDiaAct = date ( 'Y-m-d' , $ultimoDiaAct );
                    $hoy = date( 'Y-m-d');
                    if(strtotime($hoy) >= strtotime($inicioMes) && strtotime($hoy) < strtotime($ultimoDiaAct)){
                    }else{
                        $llamada .= '$("#modificar'.$datos['id'].', #eliminar'.$datos['id'].'").prop("disabled",true).removeClass("modificar eliminar btn-primary btn-danger");';
                        $llamada .= '$("#eliminar'.$datos['id'].'").prop("disabled",true).removeClass("eliminar btn-danger");';
                    }
                }
            break;
            case 5 :
                $llamada .= '$(".ver").addClass("verInvolucrado").removeClass("ver");';
                $llamada .= '$(".eliminar").addClass("eliminarInvolucrado").removeClass("eliminar");';
            break;
            case 6 :
                $llamada .= '$(".ver").addClass("verTestigo").removeClass("ver");';
                $llamada .= '$(".eliminar").addClass("eliminarTestigo").removeClass("eliminar");';
            break;
            case 7 :
                $llamada .= '$(".eliminar").addClass("eliminarSol").removeClass("eliminar");';
                $llamada .= '$(".ver").addClass("verSol").removeClass("ver");';
            break;
            case 8 :
            $r = $this->obtenerRol($_SESSION['rol_usuario_id']);

            if ( $r==1 || $r==5) {
                if(!empty($datos['Estatus real']) && ($datos['Estatus real'] == 'Eliminada')){
                    $llamada .= '$("#eliminar'.$datos['id'].'").prop("disabled",true).removeClass("eliminar btn-danger");';
                }else{

                }
                
            }
            else if($r == 2 || $r == 3){
                $editar = 0;
                date_default_timezone_set('America/Mexico_City');
                $recepcion = date($datos['recepcion']);
                $month = substr($recepcion, 3, 2);
                $year = substr($recepcion, 6, 4);
                $diasMes = $this->getMonthDays($month, $year);                    
                $inicioMes =  date('d-m-Y', mktime(0,0,0, $month, 1, $year));
                //$limiteDias = date('d-m-Y', mktime(0,0,0, $month, 3, $year));
                //echo "<p>Recibido:".$recepcion." Inicio mes:".$inicioMes." Mes:".$month." Año:".$year." Días de mes:".$diasMes."</p>";
                $ultimoDiaAct = strtotime ( '+'.($diasMes+2).' day' , strtotime ($inicioMes) );              
                $ultimoDiaAct = date ( 'd-m-Y' , $ultimoDiaAct );
                $hoy = date( 'd-m-Y');
                    if(strtotime($hoy) >= strtotime($inicioMes) && strtotime($hoy) < strtotime($ultimoDiaAct)){
                    }else{
                        $llamada .= '$("#modificar'.$datos['id'].', #eliminar'.$datos['id'].'").prop("disabled",true).removeClass("modificar eliminar btn-primary btn-danger");';
                        $llamada .= '$("#eliminar'.$datos['id'].'").prop("disabled",true).removeClass("eliminar btn-danger");';
                    } 
                 //echo $recepcion."--  inicio " .$inicioMes." -- nueva ".$ultimoDiaAct." -- hoy ".$hoy." -- permiso = ".$editar."<br/><br/>";    
            } 
            break;
            default: break;
        }
        return $llamada;
    }
    
    function getMonthDays($Month, $Year){ // funcion, dias del mes
       //Si la extensión que mencioné está instalada, usamos esa.
       if( is_callable("cal_days_in_month"))
       {
          return cal_days_in_month(CAL_GREGORIAN, $Month, $Year);
       }
       else
       {
          //Lo hacemos a mi manera.
          return date("d",mktime(0,0,0,$Month+1,0,$Year));
       }
    }
    
    function obtenerRol($rol){
        $r = 1;
        for($i = $r; $i < 10; $i++ ){
            if(md5($i) == $rol){
                $ro = $i;
            }
        }
        return $ro;
    }
}


?>