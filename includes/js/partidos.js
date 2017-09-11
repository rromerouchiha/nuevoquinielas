//-----------------------PARTIDOS-----------------------------------------------------------------------
function equipoN(){
	$('.boton').button();
    $('.listas').chosen();
	$(document).tooltip();
	$('.fecha').datepicker(confDatePick3);
	comboPartidosF();
	
	$('#locn,#visn').on('change',function(){
		var loc = $('#locn').val();
		var vis = $('#visn').val();
		//alert(loc);
		//alert(vis);
		if (loc != '' && vis != '') {
			if (loc == vis) {
				alert("El Equipo Visitante no puede ser el mismo que el Equipo Local");
				$('#visn').val('').trigger('chosen:updated');
				$('#fl,#fvs,#fv').html('');
			}else{
				var floc = "";
				$("#locn option:selected").each(function(){
					floc += $( this ).text();
				 });
				var fvis = "";
				$( "#visn option:selected" ).each(function() {
					fvis += $( this ).text();
				 });
				$('#fl').html('').prepend('<img id="locIMG" style="width:35px" src="../../includes/img/escudo/'+floc+'.png" />');
				$('#fvs').html('').html("VS");
				$('#fv').html('').prepend('<img id="locIMG" style="width:35px" src="../../includes/img/escudo/'+fvis+'.png" />');;
			}
		}else{
			$('#fl,#fvd,#fv').html('');
		}
		});
	
	$('#agregarPA').on('click',function(){
		if(camposVacios()){
			if (campoFecha()) {
				$('#resn').attr('disabled',false).trigger('chosen:updated');
				$('#agregarPA').attr('disabled',true);
				var ruta = $('#fagregarP').attr('action');
				var formData = new FormData($('#fagregarP')[0]);
					$.ajax({
						type: 'POST',
						url : ruta,
						data: formData,
						cache: false,
						contentType: false,
						processData: false,
						dataType: "json",
						success: function(data){
							$("#mensaje").addClass(data.clase);
							$("#mensaje").html(data.mensaje);
							$("#mensaje").slideDown();
							if (data.estado == 1) {
								var floc = "";
								$("#locn option:selected").each(function(){
									floc += $( this ).text();
								 });
								var fvis = "";
								$( "#visn option:selected" ).each(function() {
									fvis += $( this ).text();
								 });
								var jor = "";
								$( "#jorn option:selected" ).each(function() {
									jor += $( this ).text();
								 });
								var torn = "";
								$( "#ligan option:selected" ).each(function() {
									torn += $( this ).text();
								 });
								$('#pagregados').css({'display':'inline-block'});
								var muestra = "<tr><td><img style='width:35px' src='../../includes/img/escudo/"+ floc +".png'/></td>";
								muestra += "<td>VS</td>";
								muestra += "<td><img style='width:35px' src='../../includes/img/escudo/"+ fvis +".png'/></td>";
								muestra += "<td>"+jor+"</td>";
								muestra += "<td>"+torn+"</td>";
								$('#tpagregados').append(muestra);
								
							}
							setTimeout(function() {
									$("#mensaje").html('');
									$("#mensaje").removeClass(data.clase);
							},3000);
							
						},
						statusCode:{
							404: function(){
								alert("pagina no encontrada");
							}
						}
					});
				$('#agregarPA').attr('disabled',false);
				$('#resn').attr('disabled',true).trigger('chosen:updated');	
			}
		}
	});
	
	
	$('#gl,#gv').on('change',function(){
		var loc = $('#gl').val();
		var vis = $('#gv').val();
		if ( loc != '' && vis != '') {
		validaGoles(loc,vis);}
	});
	
	function validaGoles(loc,vis){
			if (loc >= 0 && vis >= 0) {
				if (loc == vis) {
					$('#resn').val('empate').trigger('chosen:updated');
				}else if (loc > vis) {
					$('#resn').val('local').trigger('chosen:updated');
				}else{
					$('#resn').val('visitante').trigger('chosen:updated');
				}
			}else{
				$('#resn').val('').trigger('chosen:updated');
			}
	}
	
}
function partidos(){
	$('#jorpart').on('change',function(){
		$('#feditarp').slideUp();
		$('#carga2').show();
		pars = {'operacion' : 1,'jornada': $(this).val()};
		var ruta = '../../herramientas/funcionalidad/operacionesadminpartidos.php';  //asignamos la ruta donde se enviaran los datos a una variable llamada url
		$.ajax({
			type: "POST",
				url : ruta,
				data: pars,
				success: function(msg){
					//console.log(msg);
					$("#modpar").html(msg);
				},
				statusCode:{
					404: function(){
						alert("pagina no encontrada");
					}
				}
		});
	});
	
}

function EvePartidos(){
    $('.boton').button();
    $('.listas').chosen();
	$(document).tooltip();
	$('.fecha').datepicker(confDatePick3);
    comboPartidosF();
	var ruta = '../../herramientas/funcionalidad/operacionesadminpartidos.php';  //asignamos la ruta donde se enviaran los datos a una variable llamada url

	$('.editarP').on('click',function(){
		var cveP = $(this).attr('for');
		actualizaPartido(cveP,1);
	});
	
	$('#editarPG').on('click',function(){
		$('.cveParG').each(function(){
			actualizaPartido($(this).val(),0);
		});	
		$("#nota").addClass('notaBien').html("Partidos Actualizados").slideDown();
		//$("#conMen"+cveP);
		setTimeout(function() {
			$("#nota").html('').removeClass('notaBien');
			//$("#conMen"+cveP).slideUp();
			pars = {'operacion' : 1,'jornada': $('#jorpart').val()};
			ajax(ruta,'POST','html',pars,false,function(msg){  $("#modpar").html(msg);} );
		},3000);
	});
	
	function actualizaPartido(cveP,tipo){
		par = {'operacion':2,'cveP':cveP,'local':$('#loc'+cveP).val(),'visitante':$('#vis'+cveP).val(),'fecha':$('#fecha'+cveP).val(),'hora':$('#hora'+cveP).val(),'jor':$('#jor'+cveP).val(),'liga':$('#liga'+cveP).val(),'tabla':$('#tablag'+cveP).val()};
		ajax(ruta,'POST','json',par,false,function(datos){
			if (tipo == 1) {
				$("#men"+cveP).addClass(datos.clase);
				$("#men"+cveP).html(datos.mensaje);
				$("#conMen"+cveP).slideDown();
				setTimeout(function() {
						$("#men"+cveP).html('');
						$("#men"+cveP).removeClass(datos.clase);
						$("#conMen"+cveP).slideUp();
						if (datos.estado == 1) {
							pars = {'operacion' : 1,'jornada': $('#jorv'+cveP).val()};
							ajax(ruta,'POST','html',pars,false,function(msg){  $("#modpar").html(msg);} );
						}
				},3000);
			}
		});
	}
	function confirmElimPar(cveP){
		var loc = "";
		$("#loc"+cveP+" option:selected").each(function(){
			loc += $( this ).text();
		});
		var vis = "";
		$( "#vis"+cveP+" option:selected" ).each(function() {
			vis += $( this ).text();
		});
	   var ruta = '../../herramientas/funcionalidad/operacionesadminpartidos.php';  //asignamos la ruta donde se enviaran los datos a una variable llamada url
	   var pars= {'operacion': 3,'loc':loc,'vis':vis,'cveP':cveP}; //asignamos el parametro que se enviara
	   $.ajax({
		   type: "POST",
			   url : ruta,
			   data: pars,
			   success: function(msg){
				   $("#confirmE").html(msg);
				   indexSecundario();
		   },
		   statusCode:{
			   404: function(){
				   alert("pagina no encontrada");
			   }
		   }
	   });
	}
	
	
	$('.eliminarP').on('click',function(){
		var cveP = $(this).attr('for');
		confirmElimPar(cveP);
		
		//par = {'operacion':3,'cveP':cveP,'local':$('#loc'+cveP).val(),'visitante':$('#vis'+cveP).val()};
		//ajax(ruta);
		
	});
	
	$('#eliminarPG').on('click',function(){
		$('.cveParG').each(function(){
			actualizaPartido($(this).val(),0);
		});	
		$("#nota").addClass('notaBien').html("Partidos Actualizados").slideDown();
		//$("#conMen"+cveP);
		setTimeout(function() {
			$("#nota").html('').removeClass('notaBien');
			//$("#conMen"+cveP).slideUp();
			pars = {'operacion' : 1,'jornada': $('#jorpart').val()};
			ajax(ruta,'POST','html',pars,false,function(msg){  $("#modpar").html(msg);} );
		},3000);
	});
	
	
}

function resultadosPart(){
	comboPartidosF();
	
	$('#jorpartR').on('change',function(){
		$('#feditarp').slideUp();
		$('#carga2').show();
		pars = {'operacion' : 6,'jornada': $(this).val()};
		var ruta = '../../herramientas/funcionalidad/operacionesadminpartidos.php';  //asignamos la ruta donde se enviaran los datos a una variable llamada url

		$.ajax({
			type: "POST",
				url : ruta,
				data: pars,
				success: function(msg){
					//console.log(msg);
					$("#respar").html(msg);
				},
				statusCode:{
					404: function(){
						alert("pagina no encontrada");
					}
				}
		});
	});
}
function modResultadosPartidos(){
	
	$('.goles').on('change',function(){
		var cual = $(this).attr('for');
		var glocal = $('#gloc'+cual).val();
		var gvisitante = $('#gvis'+cual).val();
		if (glocal != '' && gvisitante != '') {
			var resultado = (glocal>gvisitante)?'local':(glocal<gvisitante)? 'visitante':(glocal==gvisitante)? 'empate':'';
			$('#res'+cual).val(''+resultado+'');
		}else{
		$('#res'+cual).val('');
		}
		$('.listas').trigger('chosen:updated');
	});
	
	$('.cancelarP').on('click',function(){
		var cual = $(this).attr('for');
		var motivo = $('#motCan'+cual).val();
		var jorn = $(jornadaR).val();
		if (motivo == '') {
			$("#men"+cual).addClass('notaInfo').html('DEBE SELECCIONAR UN MOTIVO DE CANCELACION PARA EL PARTIDO');
			$("#conMen"+cual).slideDown();
				setTimeout(function() {
						$("#men"+cual).html('').removeClass('notaInfo');
						$("#conMen"+cual).slideUp();
						$('#motCan'+cual).focus();
				},3000);
		}else{
			var panelB = "<h4>&iquest;ESTA SEGURO QUE DESEA CANCELAR ESTE PARTIDO? &nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='delete' id='delete' value='SI' class='boton' onclick='cancelaPartido("+cual+",\""+motivo+"\","+jorn+")'/>&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='cancelar2' id='cancelar2' value='NO' class='boton' onclick='ocultameDelP("+cual+");' /></h4>";
			$("#men"+cual).addClass('notaAyuda').html(panelB);
			$('.boton').button();
			$("#conMen"+cual).slideDown();
			$("#contenedor"+cual).css('background','rgba(240,100,100,.5)');
		}
	});
	
	$('.activarP').on('click',function(){
		var cual = $(this).attr('for');
		var jorn = $(jornadaR).val();
		var panelB = "<h4>&iquest;ESTA SEGURO QUE DESEA ACTIVAR ESTE PARTIDO? &nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='delete' id='delete' value='SI' class='boton' onclick='activaPartido("+cual+","+jorn+")'/>&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='cancelar2' id='cancelar2' value='NO' class='boton' onclick='ocultameDelP("+cual+");' /></h4>";
		$("#men"+cual).addClass('notaAyuda').html(panelB);
		$('.boton').button();
		$("#conMen"+cual).slideDown();
		$("#contenedor"+cual).css('background','rgba(189,229,248,.4)');
		
	});
	
	$('.aplicarT').on('click',function(){
		var cual = $(this).attr('for');
		var jorn = $(jornadaR).val();
		var panelB = "<h4>&iquest;ESTA SEGURO QUE DESEA APLICAR LOS CAMBIOS DE ESTE PARTIDO A LA TABLA GENERAL? &nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='delete' id='delete' value='SI' class='boton' onclick='tablaGral("+cual+","+jorn+",8)'/>&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='cancelar2' id='cancelar2' value='NO' class='boton' onclick='ocultameDelP("+cual+");' /></h4>";
		$("#men"+cual).addClass('notaAyuda').html(panelB);
		$('.boton').button();
		$("#conMen"+cual).slideDown();
		$("#contenedor"+cual).css('background','rgba(189,229,248,.4)');

	});
	$('.revertirT').on('click',function(){
		var cual = $(this).attr('for');
		var jorn = $(jornadaR).val();
		var panelB = "<h4>&iquest;ESTA SEGURO QUE DESEA APLICAR LOS CAMBIOS DE ESTE PARTIDO A LA TABLA GENERAL? &nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='delete' id='delete' value='SI' class='boton' onclick='tablaGral("+cual+","+jorn+",9)'/>&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='cancelar2' id='cancelar2' value='NO' class='boton' onclick='ocultameDelP("+cual+");' /></h4>";
		$("#men"+cual).addClass('notaAyuda').html(panelB);
		$('.boton').button();
		$("#conMen"+cual).slideDown();
		$("#contenedor"+cual).css('background','rgba(189,229,248,.4)');
	});
	$('.editarP').on('click',function(){
		var cual = $(this).attr('for');
		var gol_local = $('#gloc'+cual).val() ;
		var gol_visit = $('#gvis'+cual).val() ;
		var resultado = $('#res'+cual).val() ;
		
		if (gol_local == '' && gol_visit == '') {
			$("#men"+cual).addClass('notaInfo').html('DEBE SELECCIONAR LOS GOLES DE AMBOS EQUIPOS');
			$("#conMen"+cual).slideDown();
			$("#contenedor"+cual).css('background','rgba(254,239,179,.4)');
				setTimeout(function() {
						$("#men"+cual).html('').removeClass('notaInfo');
						$("#conMen"+cual).slideUp();
						$("#contenedor"+cual).css('background','rgba(255,255,255,0.4)');
				},2000);
		}else{
			resultadoPartido(gol_local,gol_visit,resultado,cual);
		}
		
		
	});
	
}


function activaPartido(partido,jornada){
	$('.boton').attr('disabled',true);
	par = {'operacion':7,'idpart':partido};
	var ruta = '../../herramientas/funcionalidad/operacionesadminpartidos.php';  
	$("#men"+partido).html('').removeClass('notaAyuda');
	$("#conMen"+partido).slideUp();
		ajax(ruta,'POST','json',par,false,function(datos){
			if (datos.estatus == 'OK') {
				$("#men"+partido).addClass('notaBien').html('EL PARTIDO SE ACTIVO CON EXITO');
				$("#conMen"+partido).slideDown();
				setTimeout(function() {
						$("#men"+partido).html('');
						$("#men"+partido).removeClass('notaBien');
						$("#conMen"+partido).slideUp();
						pars = {'operacion' : 6,'jornada': jornada};
						ajax(ruta,'POST','html',pars,false,function(msg){  $("#respar").html(msg);} );
						
				},2000);
			}
		});
}

function cancelaPartido(partido,motivo,jornada){
	$('.boton').attr('disabled',true);
	par = {'cancelar':1,'idpart':partido,'motivoC':motivo};
	var ruta = '../../herramientas/funcionalidad/resultadoPartido.php'; 
	$("#men"+partido).html('').removeClass('notaAyuda');
	$("#conMen"+partido).slideUp();
		ajax(ruta,'POST','json',par,false,function(datos){
			if (datos.estatus == 'OK') {
				$("#men"+partido).addClass('notaBien').html('EL PARTIDO SE CANCELO CON EXITO');
				$("#conMen"+partido).slideDown();
				setTimeout(function() {
						$("#men"+partido).html('');
						$("#men"+partido).removeClass('notaBien');
						$("#conMen"+partido).slideUp();
						var ruta2 = '../../herramientas/funcionalidad/operacionesadminpartidos.php'; 
						pars = {'operacion' : 6,'jornada': jornada};
						ajax(ruta2,'POST','html',pars,false,function(msg){  $("#respar").html(msg);} );
						
				},2000);
			}
		});
}

function resultadoPartido(gol_local,gol_visit,resultado,idpart){
	$('.boton').attr('disabled',true);
	par = {'idpart':idpart,'gol_local':gol_local,'gol_visit':gol_visit,'resultado':resultado};
	var ruta = '../../herramientas/funcionalidad/resultadoPartido.php'; 
	$("#men"+idpart).html('').removeClass('notaAyuda');
	$("#conMen"+idpart).slideUp();
		ajax(ruta,'POST','json',par,false,function(datos){
			if (datos.estatus == 'OK') {
				$("#men"+idpart).addClass('notaBien').html('EL PARTIDO SE ACTUALIZO CON EXITO');
				$("#conMen"+idpart).slideDown();
				setTimeout(function() {
						$("#men"+idpart).html('');
						$("#men"+idpart).removeClass('notaBien');
						$("#conMen"+idpart).slideUp();
						$('.boton').attr('disabled',false);
						//var ruta2 = '../../herramientas/funcionalidad/operacionesadminpartidos.php'; 
						//pars = {'operacion' : 6,'jornada': jornada};
						//ajax(ruta2,'POST','html',pars,false,function(msg){  $("#respar").html(msg);} );
						
				},2000);
			}
		});
		
}

function tablaGral(partido,jornada,operacion){
	$('.boton').attr('disabled',true);
	par = {'operacion':operacion,'idpart':partido};
	var ruta = '../../herramientas/funcionalidad/operacionesadminpartidos.php';  
	$("#men"+partido).html('').removeClass('notaAyuda');
	$("#conMen"+partido).slideUp();
		ajax(ruta,'POST','json',par,false,function(datos){
				$("#men"+partido).addClass(datos.clase).html(datos.mensaje);
				$("#conMen"+partido).slideDown();
				setTimeout(function() {
						$("#men"+partido).html('');
						$("#men"+partido).removeClass(datos.clase);
						$("#conMen"+partido).slideUp();
						pars = {'operacion' : 6,'jornada': jornada};
						ajax(ruta,'POST','html',pars,false,function(msg){  $("#respar").html(msg);} );
						
				},2000);
		});
}


function ocultameDelP(cual){
	$("#men"+cual).html('').removeClass('notaAyuda');
	$("#conMen"+cual).slideUp();
	$("#contenedor"+cual).css('background','rgba(255,255,255,0.4)');
	
}


	function deletePartido(cveP,loc,vis,tipo){
		par = {'operacion':5,'cveP':cveP};
		  var ruta = '../../herramientas/funcionalidad/operacionesadminpartidos.php';  //asignamos la ruta donde se enviaran los datos a una variable llamada url
		ajax(ruta,'POST','json',par,false,function(datos){
				$("#men"+cveP).addClass(datos.clase);
				$("#men"+cveP).html(datos.mensaje);
				$("#conMen"+cveP).slideDown();
				setTimeout(function() {
						$("#men"+cveP).html('');
						$("#men"+cveP).removeClass(datos.clase);
						$("#conMen"+cveP).slideUp();
						if (datos.estado == 1) {
							if (tipo == 1) {
								pars = {'operacion' : 1,'jornada': $('#jorv'+cveP).val()};
								ajax(ruta,'POST','html',pars,false,function(msg){  $("#modpar").html(msg);} );
							}
						}
				},3000);
		});
	}



function comboPartidosF(){
	var url = '../../herramientas/funcionalidad/combos.php?consulta=';
    $('.jorG').llenaComboNA({'url' : url+'2'},function(){
		$('.jorG').trigger('chosen:updated');
	});
	
	$('.equiG').llenaComboNA({'url' : url+'3'},function(){
		$('.equiG').trigger('chosen:updated');
	});
}
