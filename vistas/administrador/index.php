<?php
session_start();
include_once '../../herramientas/config/msjErrores.php';

if(!$_SESSION['AUTEN'] || $_SESSION['TIPO'] != 'admin'){
	$_SESSION['ERROR'] = ERROR_ACCESO;
	header('Location:../../index.php');
}
?>
<!DOCTYPE html>
    <html>
        <head>
            <title>Administrador</title>
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <script src='../../includes/js/jquery-1.11.1.min.js' type="text/javascript"></script>
	    <script src='../../includes/js/jquery-ui-1.10.4.custom.js' type="text/javascript"></script>
	    <script src='../../includes/js/chosen.jquery.min.js' type="text/javascript"></script>
	    <script src='../../includes/js/general.js' type="text/javascript"></script>
		<script src='../../includes/js/jquery-ui-config.js' type="text/javascript"></script>
        <link href='../../includes/css/fuentes.css' rel='stylesheet'/>
	    <link href='../../includes/css/estilos_admin.css' rel='stylesheet'/>
	    <link href='../../includes/css/jquery-ui_admin/jquery.ui.all.css' rel='stylesheet'/>
	    <link href='../../includes/css/chosen.css' rel='stylesheet'/>
	    <!--<link href='../../includes/css/medias_admin.css' rel='stylesheet'/>-->

        </head>
        <body>
            <div id='contenedor'>
                
                <div id='menu_principal'>
                    <ul id='principal'>
                        <li id='1' class='enlacemenup'><img src='../../includes/img/iconos/inicio.png' class='menu' /><br/><span class='titulop'>Inicio</span></li>
                        <li id='2' class='enlacemenup'><img src='../../includes/img/iconos/user.png' class='menu' /><br/><span class='titulop'>Usuarios</span></li>
						<li id='3' class='enlacemenup'><img src='../../includes/img/iconos/premio.png' class='menu' /><br/><span class='titulop'>Torneo</span></li>
						<li id='4' class='enlacemenup'><img src='../../includes/img/iconos/campo.png' class='menu' /><br/><span class='titulop'>Quinielas</span></li>
                        <li id='5' class='enlacemenup'><img src='../../includes/img/iconos/pdf.png' class='menu' /><br/><span class='titulop'>Reportes</span></li>
                        <li id='6' class='enlacemenup'><img src='../../includes/img/iconos/salir.png' class='menu' /><br/><span class='titulop'>Salir</span></li>
                    </ul>
                </div>
				<div style='width:20%;padding-right: 15px;padding-top: 10px;text-align: right;font-size: 12px;color: #FFFFFF;position: relative;float: right'>
					<p style='padding: 0 0 5px 0;'><?php echo $_SESSION['NOMBRE']; ?></p>
					<div class='marco3'><img src='../../includes/img/perfil/<?php echo $_SESSION["IMGPER"]; ?>' /></div>
					
				</div>
                <div id='menu_secundario'></div>
                <div id='contenido_principal'>
		    <center><img src='../../includes/img/iconos/cargando.gif' id='carga' style='width:60px;display:none;'/></center>
		</div>
            </div>
            
        </body>
		<script type='text/javascript' src='../../includes/js/indexadmin.js' ></script>
		<script type='text/javascript' src='../../includes/js/indexsecundario.js' ></script>
		<script type='text/javascript' src='../../includes/js/equipos.js' ></script>
		<script type='text/javascript' src='../../includes/js/partidos.js' ></script>
    </html>