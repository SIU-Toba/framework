<?
/*
SISTEMA de COMUNICACION ASINCRONICA sin recargar la pagina del BROWSER

*** CODIGO PROVEEDOR *** (Generacion de la consulta)

EL mecanismo requiere de la interaccion de 3 PARTES:

1) El ITEM que realiza la respuesta.
En el caso de este ejemplo, esta pagina consume el item: '/pruebas/comunicacion_js_prov'
(El archivo que posee la actividad del mismo es js_prov.php)
Este ITEM tiene que seguir los siguientes lineamientos
	- El TIPO de PAGINA del mismo tiene que ser "comunicacion_JS"
	- Recibe parametros a travez de $_POST['parametros']
	- Tiene que realizar la respuesta (PHP) a travez de la funcion "responder(xxx)"

2) La funcion JAVASCRIPT que procesa la respuesta (en este ejemplo se encuentra en esta
pagina. "procesar_retorno"). Esta funcion sera llamada como callback una vez que el
servidor haya terminado de procesar los datos. Tiene que tener un parametro de entrada

3) El codigo JAVASCRIPT que dispara la pregunta. La misma debe realizarse con la funcion
	consultar_info(item_proyecto, item, parametros, callback). En este ejemplo esta llamada
	esta en esata paagina y se activa presionando el boton
*/
?>
<script language='javascript'>
function procesar_retorno(datos){
	alert(datos);
}
</script>
<?
	include_once('nucleo/browser/interface/form.php');
	echo form::button("A","prueba","onclick=\"consultar_info('toba','/pruebas/comunicacion_js_prov','ABCDEFGHIJK','procesar_retorno');\"");
?>
