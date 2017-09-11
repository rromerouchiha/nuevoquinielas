<?php
/*
 *  ELABORADO POR ROMERO VILLEGAS RAFAEL ALEJANDRO
 *                 20/09/2015
 *  Funciona en versiones igual o menores a 5.6 
 *                  
 */
function Conexion($tipo,$ser,$usua,$clav,$base_sid,$puerto,$extraOci=array()/*['puerto'=>1521],['caracter'=>'AL32UTF8']*/)
{
    $tipo   = strtoupper($tipo);
    $clases = array('MYSQL'=>'ConexionMySQL','MSSQL'=>'ConexionMsSQL','ORACLE'=>'conexionOracle');
    if($tipo == 'ORACLE')
    {
        $extOciDef = array('puerto'=>$puerto,'caracter'=>'AL32UTF8');
        if(!empty($extraOci) && is_array($extraOci))
        {
            $extOciDef = array_merge($extOciDef,$extraOci);
        }
        extract($extOciDef);
        $obj = new $clases[$tipo]($ser,$usua,$clav,$base_sid,$puerto,$caracter);
    }
    else
    {
        $obj = new $clases[$tipo]($ser,$usua,$clav,$base_sid,$puerto);
    }
    return $obj;
}

class MsjError
{
    protected function msgErrores($mensaje, $especificacionError = '')
    {
        $msgFormato1 = '<br/><label style="color: red;">%s</label>';
        $msgFormato2 = ' (</b> %s <b>)</b>';
        $msg         = sprintf($msgFormato1,ucfirst( $mensaje ));
        $msg        .= ($especificacionError != '')? sprintf($msgFormato2,$especificacionError) : '';
        die($msg);
    }
}

class ConexionMySQL extends MsjError
{
    private $conexion;
    private $servidor;
    private $usuario;
    private $clave;
    private $base;
    private $resultado;
    private $puerto;
    
    public function __construct($serv,$usua,$clav,$base='',$puerto='')
    {
        $this->servidor  = $serv;
        $this->usuario   = $usua;
        $this->clave     = $clav;
        $this->base      = $base;
        $this->puerto    = $puerto;
        $this->conexion  = NULL;
        $this->resultado = NULL;
        $this->conexionBD();
    }
    
    private function conexionBD()
    { 
        $this->conexion = mysqli_connect($this->servidor,$this->usuario,$this->clave,'',$this->puerto)  or  
        $this->msgErrores('... A ocurrido un error al intentar conectarse al Servidor de Base de Datos',mysqli_connect_error($this->conexion));
        if(!empty($this->base))
        {
            $this->setBase($this->base);
        }
    }
    
    public function setBase($base)
    {
        $this->base = mysqli_select_db($this->conexion,$base)   or
        $this->msgErrores('... A ocurrido un error al intentar conectarse a la Base de Datos','Base de datos "'.$base.'" no encontrada');
        $this->nombreBase = $base;
    }
    
    public function query($sql,$regresa='')
    {  
        $this->resultado = @mysqli_query($this->conexion,$sql) or 
        $this->msgErrores('... A ocurrido un error en la consulta',mysqli_error($this->conexion).' <br><br> <b>Consulta:</b><i> '.$sql.'</i>');
        switch($regresa)
        {
            case ''                  : return $this->resultado; break;
            case 'arregloNumerico'   : return $this->arregloNumerico(); break;
            case 'arregloAsociado'   : return $this->arregloAsociado(); break;
            case 'arregloUnicoNum'   : return $this->arregloUnico(); break;
            case 'arregloUnicoAsoc'  : return $this->arregloUnicoAsociado(); break;
            case 'numColums&Regs'    : return $this->numeroColumYReg(); break;
            case 'id'                : return mysqli_insert_id($this->conexion); break;
            case 'registro'          : return mysqli_fetch_assoc($this->resultado); break;
            case 'registroNumerico'  : return mysqli_fetch_row($this->resultado); break;
            case 'afecto?'           : return mysqli_affected_rows($this->conexion); break;
            case 'numeroColumnas'    : return mysqli_num_fields($this->resultado); break;
            case 'numeroRegistros'   : return mysqli_num_rows($this->resultado); break;
        }
    }
    
    public function numeroColumYReg($cual = 0)
    {
        $ar = null;
        $nR = mysqli_num_rows($this->resultado);
        $nC = mysqli_num_fields($this->resultado);
        if($cual == 0)
        {
            $ar['registros'] = $nR;
            $ar['columnas']  = $nC;
        }
        else if($cual == 1){
            $ar = $nR;
        }
        else if($cual == 2){
            $ar = $nC;
        }
        return $ar;
    }
    
    private function arregloNumerico()
    {
        $n = mysqli_num_fields($this->resultado);
        $x = 0;
        $arreglo = array();
        while($result = mysqli_fetch_array($this->resultado))
        {
            for($i = 0; $i < $n; $i++)
            {
                $arreglo[$x][$i] = $result[$i];
            }
            $x++;
        }
        $this->libera();
        return $arreglo;
    }
    
    public function arregloUnico()
    {
        $n = mysqli_num_fields($this->resultado);
        $arreglo = array();
        while($result = mysqli_fetch_array($this->resultado))
        {
            for($i = 0; $i < $n; $i++)
            {
                $arreglo[] = $result[$i];
            }
        }
        $this->libera();
        return $arreglo;
    }
    
    public function arregloUnicoAsociado()
    {
        $arreglo = array();
        while($result = mysqli_fetch_assoc($this->resultado))
        {
            foreach($result as $nombre => $valor)
            {
                $arreglo[$nombre] = $valor;
            }
        }
        $this->libera();
        return $arreglo;
    }
    
    public function arregloAsociado()
    {
        $x = 0;
        $arreglo = array();
        while($result = mysqli_fetch_assoc($this->resultado))
        {
            foreach($result as $nombre => $valor)
            {
                $arreglo[$x][$nombre] = $valor;
            }
            $x++;
        }
        $this->libera();
        return $arreglo;
    }
    
    public function fetchRow()
    {
        return mysqli_fetch_row($this->resultado);
    }
    
    public function fetchArray()
    {
        return mysqli_fetch_array($this->resultado);
    }
    
    public function fetchAssoc()
    {
        return mysqli_fetch_assoc($this->resultado);
    }
    
    public function libera()
    {
        @mysqli_free_result($this->resultado);
    }
    
    public function cerrarConexion()
    {
        $this->libera();
        @mysqli_close($this->conexion);
    }
    
    public function __destruct()
    {
        if($this->conexion)
        {
            $this->cerrarConexion(); 
        }
    }
        
}
/************************************************************************************
 *  CONEXION ORACLE
 */
class conexionOracle extends MsjError
{
    private $conexion;
    private $servidor;
    private $usuario;
    private $clave;
    private $sid;
    private $resultado;
    private $str_conexion;
    private $puerto;
    private $codificacion;
    private $numRegistro;
    
    public function __construct($servidor,$usuario,$clave,$sid,$puerto='',$codificacion='AL32UTF8')
    {
        $this->str_conexion = '(DESCRIPTION = (ADDRESS_LIST = (ADDRESS = (COMMUNITY = tcp.world)(PROTOCOL = TCP)(HOST = %s)(PORT = %s)))(CONNECT_DATA = (SID = %s)))';
        $this->servidor = $servidor;
        $this->usuario  = $usuario;
        $this->clave    = $clave;
        $this->sid      = $sid;
        $this->puerto   = $puerto;
        $this->codificacion = $codificacion;
        $this->conexionBD();
    }
    
    private function conexionBD()
    {
        $this->str_conexion = sprintf($this->str_conexion,$this->servidor,$this->puerto,$this->sid);
        $this->conexion     = oci_pconnect($this->usuario, $this->clave, $this->str_conexion,$this->codificacion) or
        $this->msgErrores('... A ocurrido un error al intentar conectarse al Servidor de Base de Datos');
    }
    
    public function query($sql,$regresa='')
    {
        $sql = str_replace(';','',$sql);
        $this->resultado = oci_parse($this->conexion,$sql);
        oci_execute($this->resultado);
        switch($regresa)
        {
            case 'arregloNumerico'   : return $this->arregloNumerico(); break;
            case 'arregloAsociado'   : return $this->arregloAsociado(); break;
            case 'arregloUnicoNum'   : return $this->arregloUnico(); break;
            case 'arregloUnicoAsoc'  : return $this->arregloUnicoAsociado(); break;
            case 'numColums&Regs'    : $this->numeroColumYReg(); break;
            case 'id'                : return null; break;
            case 'registro'          : oci_fetch_assoc($this->resultado); break;
            case 'registroNumerico'  : oci_fetch_row($this->resultado); break;
            case 'afecto?'           : return oci_num_rows($this->resultado); break;
            case 'numeroColumnas'    : return oci_num_fields($this->resultado); break;
            case 'numeroRegistros'   : return null; break;
            default                  : return $this->resultado; break;
        }
    }
    
    public function numeroColumYReg()
    {
        return null;
    }
    
    public function arregloAsociado()
    {
        $x = 0;
        $arreglo = array();
        while($result = oci_fetch_assoc($this->resultado))
        {
            foreach($result as $nombre => $valor)
            {
                $arreglo[$x][$nombre] = $valor;
            }
            $x++;
        }
        $this->libera();
        return $arreglo;
    }
    
    public function arregloNumerico()
    {
        $x = 0;
        $arreglo = array();
        while($result = oci_fetch_row($this->resultado))
        {
            foreach($result as $indice => $valor)
            {
                $arreglo[$x][$indice] = $valor;
            }
            $x++;
        }
        $this->libera();
        return $arreglo;
    }
    
    public function id($secuencia,$esquema) /* ... USAR EN SU LUGAR LA FUNCION seq_ID*/
    {
        $SQL_ID = 'SELECT LAST_NUMBER,INCREMENT_BY FROM all_sequences WHERE sequence_owner = \''.$esquema.'\' AND sequence_name = \'%s\'';
        $SQL_ID = sprintf($SQL_ID,$secuencia);
        $id = $this->query($SQL_ID,'arregloUnicoNum');
        return (int)$id[0] - (int)$id[1];
    }
    
    public function seq_ID($secuencia,$esquema='')
    {
        $nombre = (!empty($esquema))? $esquema.'.'.$secuencia : $secuencia;
        $SQL_ID = 'SELECT %s.NEXTVAL FROM DUAL;';
        $SQL_ID = sprintf($SQL_ID,$nombre);
        $id = $this->query($SQL_ID,'arregloUnicoNum');
        return (int)$id[0];
    }
    
    public function arregloUnico()
    {
        $arreglo = array();
        while($result = oci_fetch_row($this->resultado))
        {
            foreach($result as $nombre => $valor)
            {
                $arreglo[] = $valor;
            }
        }
        $this->libera();
        return $arreglo;
    }
    
    public function arregloUnicoAsociado()
    {
        $arreglo = array();
        while($result = oci_fetch_assoc($this->resultado))
        {
            foreach($result as $nombre => $valor)
            {
                $arreglo[$nombre] = $valor;
            }
        }
        $this->libera();
        return $arreglo;
    }
    
    public function fetchRow()
    {
        return oci_fetch_row($this->resultado);
    }
    
    public function fetchArray()
    {
        return oci_fetch_array($this->resultado);
    }
    
    public function fetchAssoc()
    {
        return oci_fetch_assoc($this->resultado);
    }
    
    public function commit()
    {
        oci_commit($this->conexion);
    }
    
    public function libera()
    {
        @oci_free_statement($this->resultado);
    }
    
    public function cerrarConexion()
    {
        $this->libera();
        @oci_commit($this->conexion);
        @oci_close($this->conexion);
    }
    
    public function __destruct()
    {
        if($this->conexion)
        {
            $this->cerrarConexion(); 
        }
    }
}

class ConexionMsSQL extends MsjError
{
    private $conexion;
    private $servidor;
    private $usuario;
    private $clave;
    private $base;
    private $resultado;
    
    public function __construct($serv,$usua,$clav,$base)
    {
        $this->servidor  = $serv;
        $this->usuario   = $usua;
        $this->clave     = $clav;
        $this->base      = $base;
        $this->conexion  = NULL;
        $this->resultado = NULL;
        $this->conexionBD();
    }
    
    private function conexionBD()
    {
        $this->conexion = mssql_connect($this->servidor,$this->usuario,$this->clave)  or  
        $this->msgErrores('... A ocurrido un error al intentar conectarse al Servidor de Base de Datos',mssql_get_last_message($this->conexion)); 
        $this->setBase($this->base);
    }
    
    private function setBase($base)
    {
        mssql_select_db($base,$this->conexion) or
        $this->msgErrores('... A ocurrido un error al intentar conectarse a la Base de Datos','Base de datos "'.$base.'" no encontrada');
        $this->base = $base;
    }
    
    public function query($sql,$regresa='')
    {  
        $this->resultado = mssql_query($sql,$this->conexion) or 
        $this->msgErrores('... A ocurrido un error en la consulta',mssql_get_last_message($this->conexion));
        switch($regresa)
        {
            case ''                  : return $this->resultado; break;
            case 'arregloNumerico'   : return $this->arregloNumerico(); break;
            case 'arregloAsociado'   : return $this->arregloAsociado(); break;
            case 'arregloUnicoNum'   : return $this->arregloUnico(); break;
            case 'arregloUnicoAsoc'  : return $this->arregloUnicoAsociado(); break;
            case 'numColums&Regs'    : return $this->numeroColumYReg(); break;
            case 'id'                : return NULL; break;
            case 'registro'          : return mssql_fetch_assoc($this->resultado); break;
            case 'afecto?'           : return mssql_rows_affected($this->conexion); break;
            case 'numeroColumnas'    : return mssql_num_fields($this->resultado); break;
            case 'numeroRegistros'   : return mssql_num_rows($this->resultado); break;
        }
    }
    
    public function numeroColumYReg($tipo = 0)
    {
        $ar = array();
        $nR = mssql_num_rows($this->resultado);
        $nC = mssql_num_fields($this->resultado);
        $ar['registros'] = $nR;
        $ar['columnas']  = $nC;
        if($tipo == 0){
            return $ar;
        }
        else if($tipo == 1){
            return $ar['registros'];
        }
        else if($tipo == 2){
            return $ar['columnas'];
        }
    }
    
    private function arregloNumerico()
    {
        $n = mssql_num_fields($this->resultado);
        $x = 0;
        $arreglo = array();
        while($result = mssql_fetch_row($this->resultado))
        {
            for($i = 0; $i < $n; $i++)
            {
                $arreglo[$x][$i] = $result[$i];
            }
            $x++;
        }
        $this->libera();
        return $arreglo;
    }
    
    private function arregloUnico()
    {
        $n = mssql_num_fields($this->resultado);
        $arreglo = array();
        while($result = mssql_fetch_row($this->resultado))
        {
            for($i = 0; $i < $n; $i++)
            {
                $arreglo[] = $result[$i];
            }
        }
        $this->libera();
        return $arreglo;
    }
    
    private function arregloUnicoAsociado()
    {
        $arreglo = array();
        while($result = mssql_fetch_assoc($this->resultado))
        {
            foreach($result as $nombre => $valor)
            {
                $arreglo[$nombre] = $valor;
            }
        }
        $this->libera();
        return $arreglo;
    }
    
    private function arregloAsociado()
    {
        $x = 0;
        $arreglo = array();
        while($result = mssql_fetch_assoc($this->resultado))
        {
            foreach($result as $nombre => $valor)
            {
                $arreglo[$x][$nombre] = $valor;
            }
            $x++;
        }
        $this->libera();
        return $arreglo;
    }
    
    public function fetchRow()
    {
        return mssql_fetch_row($this->resultado);
    }
    
    public function fetchArray()
    {
        return mssql_fetch_array($this->resultado);
    }
    
    public function fetchAssoc()
    {
        return mssql_fetch_assoc($this->resultado);
    }
    
    public function libera()
    {
        @mssql_free_result($this->resultado);
    }
    
    public function cerrarConexion()
    {
        $this->libera();
        @mssql_close($this->conexion);
    }
    
    public function __destruct()
    {
        $this->cerrarConexion();
    }
}
?>