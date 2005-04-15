<?	/* Javascript STANDART */ ?>
<script language='javascript'>

//*******  Funciones para el manejo de strings  ********
function trim(s){
    return s.replace(/^\s*(\S*(\s+\S+)*)\s*$/,'$1');
};

//********************  POPUPS  ************************

function solicitar_item_popup( url, tx, ty, scroll, resizable ){
	vars = "width=" + tx + ",scrollbars=" + scroll + ",height=" + ty + ", resizable=" + resizable + ",dependent=yes";
	if (!window.ventana_hija){
		// No fue definida.
		ventana_hija = window.open( url , 'ventana_hija', vars);
		ventana_hija.focus();
	} else {
		// Ya fue definida.
		if(!ventana_hija.closed){
			//Todavia esta abierta
			ventana_hija.focus();
			ventana_hija.location.href = url;
		}else{
			ventana_hija = window.open( url , 'ventana_hija', vars);
		}
	}
	return false;
}
//Paso el foco de los eventos a los tooltips.
document.onmousemove=positiontip;
<?
/*
**********  Sistema de comunicaciones asincronicas con el SERVER  *********

--- RECUPERACION DE DATOS ---
Parametros:
	- 1 y 2) Item que hay que llamar: proyecto, item
	- 3) Parametros que se desea pasar al item
	- 4) Callback que hay que llamar cuando los datos regresen

La lista de espera se implementa como una pila, de esta forma primero se carga el ef más lejano
y en último lugar el ef más cercano. Así los focos se otorgan en el orden de la pantalla.
*/
?>
var lista_espera = new Array();

function encolar_consulta()
{
	lista_espera.push(arguments);
}

function atender_proxima_consulta()
{
	var proximo = lista_espera.pop();
	if (proximo)
		consultar_info (proximo[0], proximo[1], proximo[2], proximo[3], proximo[4]);
}

function consultar_info(item_proyecto, item, parametros, callback, prefijo_vinculo)
{
	nombre_frame = this.name;
	nombre_callback = callback;
	top.<? echo  apex_frame_com ?>.realizar_consulta(nombre_frame, item_proyecto, item, parametros, prefijo_vinculo);
}
<?
/*
--- DEVOLUCION de DATOS ---
Esta forma de llamar la callback es medio trucha, hay que retocarla
*/
?>
nombre_callback=""; 		//Funcion a activar cuando se retorna el valor
com_datos_retornados="";	//Datos retornados por la funcion

function retornar_info(datos)
{
	//alert('La respuesta llego al proxy.\n Datos: ' + datos + '\n Callback a llamar: ' + nombre_callback + '\n');
	com_datos_retornados = datos;
	setTimeout(nombre_callback+"(com_datos_retornados)", 0);
}


<?/*****************************************************************************/?>
</script>
<?php 
js::cargar_consumos_globales(array('basico'));
?>
