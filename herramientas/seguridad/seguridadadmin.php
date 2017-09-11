<?php
include "sesiones.php";
if($rol != 'admin'){
	header ("location:../../index.php?error=1");
}
?>