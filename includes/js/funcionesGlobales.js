/*========================================================*
 *               FUNCIONES GLOBALES
 *  ELABORADO POR:
 *  	* RAFAEL ALEJANDRO ROMERO VILLEGAS
 *  				05/2016
 *========================================================*/

function llenaCombo(url,combo,opcSeleccione)
{
  var opciones = '';
  if (opcSeleccione === undefined)
  {
	opciones += '<option value="">Seleccione</option>';
  }
  opciones += opcionsCombo(url);

  $(combo).html(opciones);
}

function opcionsCombo(url)
{
	var opciones = '';
	$.ajax({
	   contentType: 'application/json; charset=utf-8',
		dataType: "json",
		type : "GET",
		url: url,
		async: false,
		success: function(datos) {
			//console.log(datos);
			$.each(datos,function(indice,valor){
				opciones += '<option value="'+valor[0]+'">'+valor[1]+'</option>';
			});
		},
		error: function (xhr, ajaxOptions, thrownError) {
		  alert(ajaxOptions);
		  alert(thrownError);
		}
	});
	return opciones;
}

function remplazoSimple(texto,valor)
{
  return texto.replace(/%s/g, valor);
}

$.fn.stringFecha = function(callback)
{
  if (callback === undefined)
  {
	callback = function(){};
  }
  var tam = $(this).length;
  if ( !(tam == 3 || tam == 5))
  {
	return false;
  }
  var dia='', mes='', anio='', hora='', min='';
  var fechaArray = new Array();
  var fechaString = '';
  $(this).each(function(indice,valor){
	fechaArray[indice] = "#"+valor.id;
  });
  fechaString = fechaArray.join(",");
  $(fechaString).on("change",function(){
	dia  = $(fechaArray[0]).val().toString();
	mes  = $(fechaArray[1]).val().toString();
	anio = $(fechaArray[2]).val().toString();
	if (tam == 5)
	{
	  hora = $(fechaArray[3]).val().toString();
	  min  = $(fechaArray[4]).val().toString();
	}
	
	if (dia != '' && mes !='' && anio != '')
	{
	  if (tam == 5)
	  {
		if (hora !='' && min !='')
		{
		  callback(dia+"/"+mes+"/"+anio+" "+hora+":"+min);
		}
		else
		{
		  callback(false)
		}
		
	  }
	  else{
		callback(dia+"/"+mes+"/"+anio);
	  }
	  
	}
	else
	{
	  callback(false)
	}
  });
}
function reloj() {
	var fecha = new Date();
	var hora = 0;
	var min = 0;
	var ampm = '';
	//console.log(fecha.getHours());
	if (fecha.getHours() > 11){
	  if (fecha.getHours() > 12) {
		hora = strPad(fecha.getHours()-12)
	  }
	  else{
		hora = strPad(fecha.getHours())
	  }
	  min  = strPad(fecha.getMinutes());
	  ampm = 'pm';
	}
	else{
	  hora = strPad(fecha.getHours())
	  min  = strPad(fecha.getMinutes());
	  ampm = 'am';
	}
	$("#_hor").html(hora);
	$("#_min").html(min);
	$("#_ampm").html(ampm);
	$('#_seg').animate({opacity: "0"}, 500, "linear",function(){ $(this).css({opacity:"1"});});
	setTimeout(reloj,1000);
}

function compararFechas(fecha,fecha2,separador,separador2)
{
	separador  = (separador  !== undefined)? separador  : '/';
	separador2 = (separador2 !== undefined)? separador2 : '/';
	
	var arrFecha  = fecha.split(separador);
	var arrFecha2 = fecha2.split(separador2);
	
	console.log(arrFecha);
	console.log(arrFecha2);
	
	var dia1  = parseInt(arrFecha[0]);  
    var mes1  = parseInt(arrFecha[1]);  
    var anio1 = parseInt(arrFecha[2]);
	
	var dia2  = parseInt(arrFecha2[0]);
    var mes2  = parseInt(arrFecha2[1]);  
    var anio2 = parseInt(arrFecha2[2]);
	
    //console.log("mes 1 " + mes1);
    //console.log("mes 2 " + mes2);
	
    if (anio1 < anio2)  
    {  
        return(true)  
    }  
    else  
    {  
      if (anio1 == anio2)  
      {   
        if (mes1 < mes2)  
        {  
            return(true)  
        }  
        else  
        {   
          if (mes1 == mes2)  
          {  
            if (dia1 <= dia2)  {
              return(true);  
            }
            else  {
              return(false);  
            }
          }  
          else  {
            return(false);  
          }
        }  
      }  
      else  
        return(false);  
    }  
}

function validaForm()
{
  quitarObli();
  var enviar     = true;
  var requeridos = '';
  var barrido    = 0;
  $("body .requerido").each(function(){
	var val = $.trim($(this).val());
	var id = $(this).attr("id");
	if (val == "") {
      if (barrido == 0)
      {
         $("#"+id).focus();
      }
	  requeridos += $("label[for="+id+"]").text()+"\n";
	  $("label[for="+id+"]").addClass("requeridoValidado");
	  enviar = false;
      barrido++;
	}else{
	  $("label[for="+id+"]").removeClass("requeridoValidado");
	}
  });
  if (!enviar) {
	alert("Verifique los siguientes campos:\n"+requeridos);
  }
  agregarObli();
  //return confirm("enviar Formulario?");
  return enviar;
  //return true;
}

function muestraPopUp(URL,nombre,ancho,alto)
{
	posicion_x  = (screen.width/2)-(ancho/2); 
	posicion_y  = (screen.height/2)-(alto/2);
	nombre      = nombre.replace(' ', '_');
	var ventana = window.open(URL,nombre, 'width='+ancho+', height='+alto+',scrollbars=yes,left='+posicion_x+',top='+posicion_y);
	if(window.focus)
	{
		ventana.focus();
	}
	return false;
}

function agregarObli()
{
	quitarObli();
    $("body .requerido").each(function(){
	  var id = $(this).attr("id");
	  $("label[for="+id+"]").append('<span class="required-tag">*</span>');
    });
}

function quitarObli()
{
    $("body label").each(function(){
	  var nuevotext = $(this).text().replace('*', '');
	  $(this).html(nuevotext);
    });
}

function fechaActual(tipo) {
    var f = new Date();
	if (tipo == 1) {
	  return f.getDate() + "/" + (f.getMonth() +1) + "/" + f.getFullYear();
	}
	else{
	  return f.getDate() + "/" + (f.getMonth() +1) + "/" + f.getFullYear() + " " + f.getHours() + ":" + f.getMinutes();
	}
}

function fechaMayorEdad(anios) {
  var res = (anios*12);
  var myDate = new Date()
  myDate.setMonth(myDate.getMonth() - res);
  
  myDate.setDate (myDate.getDate());
  
  //return myDate.getDate() + "/" + (myDate.getMonth() +1) + "/" + myDate.getFullYear();
	return  myDate.getFullYear() + '/' + (myDate.getMonth() +1) +  '/' + myDate.getDate();
}

function strPad(min) {
    return String("00" + min).slice(-2);
}

function rangoSemana()
{
	var retorno = new Array();
    var fecha  = new Date();
	var tiempo = fecha.getTime();
	var fFin   = 0;
	var fIni   = 0;
	var numDia = (-1 * (fecha.getDay()));
	
	var dia    = fecha.getDate();
	var mes    = fecha.getMonth()+1;
    var anio   = fecha.getFullYear();
	
    fFin = strPad(dia)+"/"+strPad(mes)+"/"+anio;
    
	if (fecha.getDay() > 0) {
	    tiempo  = fecha.getTime();
		miliseg = parseInt(numDia*24*60*60*1000);
		fecha.setTime(tiempo+miliseg);
		dia     = fecha.getDate();
		mes     = fecha.getMonth()+1;
		anio    = fecha.getFullYear();
		fIni = strPad(dia)+"/"+strPad(mes)+"/"+anio;
	}
	else{
	  fIni = fFin;
	}
	retorno[0] = fIni;
	retorno[1] = fFin;
	
	return retorno;
}
$(function(){
	reloj();
});


$.fn.botonTooltip = function(opciones){
    var $boton = $(this);
    var defecto = {
        icono : ""
    }
    $.extend(defecto,opciones); 
    $boton.addClass("ui-state-default");
    $boton.addClass("ui-corner-all");
    $boton.html("<span class='ui-icon ui-icon-"+defecto.icono+"'></span>");
    $boton.tooltip({
        track: false,
        show: {
          delay: 60
        }
    });
}
function dialogoForm(campo,opciones)
{
    var defecto = {
        ancho   : 400,
        alto    : 500,
        modal   : true,
        abrir   : false,
        mover   : false,
		botones : {
            Guardar : function(){
                var $form = $(this).find("form");
                $form.trigger("submit");
            },
            Cancelar : function() {
                $(this).dialog("close");
            }
        }
    }
	$.extend(defecto,opciones);
    $(campo).dialog({
        autoOpen: defecto.abrir,
        width: defecto.ancho,
        height: defecto.alto,
        modal: defecto.modal,
        draggable: defecto.mover,
        resizable: false,
        open: function() {
            $(".ui-dialog-titlebar-close").hide(0);
        },
        show: {
            effect : "blind",
            duration: 400
        },
        hide: {
            effect : "clip",
            duration: 400
        },
        buttons: defecto.botones,
        close: function() {
            try{
				var $form = $(this).find("form");
				$form[0].reset();
				$form.find("select").trigger("chosen:updated");
				$("button:focus").blur();
				cierreDialog($(this).attr('id'));
			}
			catch(error){}
		}
    });
}
function ajax(url,tipo,tipoRetorno,datos,async,retorno)
{
    var resultado = null;
    if (async == undefined)
    {
        async = false;
    }
    if (retorno == undefined) {
        retorno = function(){};
    }
	$.ajax({
        dataType: tipoRetorno,
		type : tipo,
		url: url,
		data: datos,
		async: async,
		success: function(datos) {
            retorno(datos);
		}
    });
}

function recupera(id,tb,retorno)
{
    var url = "../php/funciones/recuperaDatos.php";
    if (retorno == undefined) {
        retorno = function(){};
    }
    ajax(url,"post","json",{'c':id,'t':tb},true,function(info){
        retorno(info);
    });
}



function getTipoObj(obj) {
	if (typeof obj === "undefined"){
		return "undefined";
	}
	if (obj === null){
		return "null";
	}
	return Object.prototype.toString.call(obj).match(/^\[object\s(.*)\]$/)[1];
}
(function($){
	$.fn.confirm = function(opciones){
		var defecto = {
			titulo  : "Confirmar",
			abrir   : false,
			ancho   : 400,
			alto    : 300,
			modal   : true,
			mover   : false,
			botones : {
				OK : function(){
					$(this).dialog("close");
				}
			},
			ocultarCerrar : true
		}
		switch (getTipoObj(opciones)){
			case "Object" : $.extend(defecto,opciones); break;
			case "String" :
				opciones = opciones.toLowerCase();
				if (opciones == "abrir") {
					$(this).dialog("open");
				} 
				if (opciones == "cerrar") {
					$(this).dialog("close");
				} 
				return this;
				break;
		}
		
		$(this).dialog({
			title : defecto.titulo,
			autoOpen: defecto.abrir,
			width: defecto.ancho,
			height: defecto.alto,
			modal: defecto.modal,
			draggable: defecto.mover,
			resizable: false,
			open: function() {
				if (defecto.ocultarCerrar) {
					$(".ui-dialog-titlebar-close").hide("blind");
				}
			},
			show: {
				effect : "blind",
				duration: 400
			},
			hide: {
				effect : "clip",
				duration: 400
			},
			buttons: defecto.botones
			
		});
		return this;
	}
	
	
}( jQuery ));

$.fn.solConfirmacion = function(opciones)
{
    var campo = $(this);
    var defecto = {
        ancho : 400,
        alto  : 300,
        modal : true,
        abrir : false,
        mover : false,
		fm : ''
    }
   
    $.extend(defecto,opciones);
    $(campo).dialog({
        autoOpen: defecto.abrir,
        width: defecto.ancho,
        height: defecto.alto,
        modal: defecto.modal,
        draggable: defecto.mover,
        resizable: false,
        open: function() {
            $(".ui-dialog-titlebar-close").hide("blind");
        },
        show: {
            effect : "blind",
            duration: 400
        },
        hide: {
            effect : "clip",
            duration: 400
        },
        buttons: {
            Aceptar : function(){
                $(defecto.fm).trigger("submit");
				$(this).dialog("close");
            },
            Cancelar : function() {
                $(this).dialog("close");
            }
        }
    });
}

function carLatinos(cadena){
  var res = cadena.match(/^[0-9a-zA-ZáéíóúàèìòùÀÈÌÒÙÁÉÍÓÚñÑüÜ_\s]+$/);
  return res;
}     

//funcion para md5 en javascript
var md5=(function(){function e(e,t){var o=e[0],u=e[1],a=e[2],f=e[3];o=n(o,u,a,f,t[0],7,-680876936);f=n(f,o,u,a,t[1],
12,-389564586);a=n(a,f,o,u,t[2],17,606105819);u=n(u,a,f,o,t[3],22,-1044525330);o=n(o,u,a,f,t[4],7,-176418897);f=n(f,o,u,a,t[5],
12,1200080426);a=n(a,f,o,u,t[6],17,-1473231341);u=n(u,a,f,o,t[7],22,-45705983);o=n(o,u,a,f,t[8],7,1770035416);f=n(f,o,u,a,t[9],
12,-1958414417);a=n(a,f,o,u,t[10],17,-42063);u=n(u,a,f,o,t[11],22,-1990404162);o=n(o,u,a,f,t[12],7,1804603682);f=n(f,o,u,a,t[13],
12,-40341101);a=n(a,f,o,u,t[14],17,-1502002290);u=n(u,a,f,o,t[15],22,1236535329);o=r(o,u,a,f,t[1],5,-165796510);f=r(f,o,u,a,t[6],
9,-1069501632);a=r(a,f,o,u,t[11],14,643717713);u=r(u,a,f,o,t[0],20,-373897302);o=r(o,u,a,f,t[5],5,-701558691);f=r(f,o,u,a,t[10],
9,38016083);a=r(a,f,o,u,t[15],14,-660478335);u=r(u,a,f,o,t[4],20,-405537848);o=r(o,u,a,f,t[9],5,568446438);f=r(f,o,u,a,t[14],
9,-1019803690);a=r(a,f,o,u,t[3],14,-187363961);u=r(u,a,f,o,t[8],20,1163531501);o=r(o,u,a,f,t[13],5,-1444681467);f=r(f,o,u,a,t[2],
9,-51403784);a=r(a,f,o,u,t[7],14,1735328473);u=r(u,a,f,o,t[12],20,-1926607734);o=i(o,u,a,f,t[5],4,-378558);f=i(f,o,u,a,t[8],
11,-2022574463);a=i(a,f,o,u,t[11],16,1839030562);u=i(u,a,f,o,t[14],23,-35309556);o=i(o,u,a,f,t[1],4,-1530992060);f=i(f,o,u,a,t[4],
11,1272893353);a=i(a,f,o,u,t[7],16,-155497632);u=i(u,a,f,o,t[10],23,-1094730640);o=i(o,u,a,f,t[13],4,681279174);f=i(f,o,u,a,t[0],
11,-358537222);a=i(a,f,o,u,t[3],16,-722521979);u=i(u,a,f,o,t[6],23,76029189);o=i(o,u,a,f,t[9],4,-640364487);f=i(f,o,u,a,t[12],
11,-421815835);a=i(a,f,o,u,t[15],16,530742520);u=i(u,a,f,o,t[2],23,-995338651);o=s(o,u,a,f,t[0],6,-198630844);f=s(f,o,u,a,t[7],
10,1126891415);a=s(a,f,o,u,t[14],15,-1416354905);u=s(u,a,f,o,t[5],21,-57434055);o=s(o,u,a,f,t[12],6,1700485571);f=s(f,o,u,a,t[3],
10,-1894986606);a=s(a,f,o,u,t[10],15,-1051523);u=s(u,a,f,o,t[1],21,-2054922799);o=s(o,u,a,f,t[8],6,1873313359);f=s(f,o,u,a,t[15],
10,-30611744);a=s(a,f,o,u,t[6],15,-1560198380);u=s(u,a,f,o,t[13],21,1309151649);o=s(o,u,a,f,t[4],6,-145523070);f=s(f,o,u,a,t[11],
10,-1120210379);a=s(a,f,o,u,t[2],15,718787259);u=s(u,a,f,o,t[9],21,-343485551);e[0]=m(o,e[0]);e[1]=m(u,e[1]);e[2]=m(a,e[2]);e[3]=m(f,e[3])}
function t(e,t,n,r,i,s){t=m(m(t,e),m(r,s));return m(t<<i|t>>>32-i,n)}function n(e,n,r,i,s,o,u){return t(n&r|~n&i,e,n,s,o,u)}
function r(e,n,r,i,s,o,u){return t(n&i|r&~i,e,n,s,o,u)}function i(e,n,r,i,s,o,u){return t(n^r^i,e,n,s,o,u)}
function s(e,n,r,i,s,o,u){return t(r^(n|~i),e,n,s,o,u)}function o(t){var n=t.length,r=[1732584193,-271733879,-1732584194,271733878],i;
for(i=64;i<=t.length;i+=64){e(r,u(t.substring(i-64,i)))}t=t.substring(i-64);var s=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
for(i=0;i<t.length;i++)s[i>>2]|=t.charCodeAt(i)<<(i%4<<3);s[i>>2]|=128<<(i%4<<3);if(i>55){e(r,s);for(i=0;i<16;i++)s[i]=0}s[14]=n*8;e(r,s);return r}
function u(e){var t=[],n;for(n=0;n<64;n+=4){t[n>>2]=e.charCodeAt(n)+(e.charCodeAt(n+1)<<8)+(e.charCodeAt(n+2)<<16)+(e.charCodeAt(n+3)<<24)}return t}
function c(e){var t="",n=0;for(;n<4;n++)t+=a[e>>n*8+4&15]+a[e>>n*8&15];return t}
function h(e){for(var t=0;t<e.length;t++)e[t]=c(e[t]);return e.join("")}
function d(e){return h(o(unescape(encodeURIComponent(e))))}
function m(e,t){return e+t&4294967295}var a="0123456789abcdef".split("");return d})();



function mostrarFecha(days,fecha){
    milisegundos=parseInt(35*24*60*60*1000);
 
    fecha=new Date(fecha);
    day=fecha.getDate();
    // el mes es devuelto entre 0 y 11
    month=fecha.getMonth()+1;
    year=fecha.getFullYear();
 
    //alert("Fecha actual: "+day+"/"+month+"/"+year);
 
    //Obtenemos los milisegundos desde media noche del 1/1/1970
    tiempo=fecha.getTime();
    //Calculamos los milisegundos sobre la fecha que hay que sumar o restar...
    milisegundos=parseInt(days*24*60*60*1000);
    //Modificamos la fecha actual
    total=fecha.setTime(tiempo+milisegundos);
    day=fecha.getDate();
    month=fecha.getMonth()+1;
    year=fecha.getFullYear();
    return fecha;
    //alert("Fecha modificada: "+day+"/"+month+"/"+year);
}
