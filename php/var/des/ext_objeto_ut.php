<?php
require_once("nucleo/browser/clases/objeto_ut.php");	

class objeto_ut_x extends objeto_ut
/*
 	@@acceso: nucleo
	@@desc: Descripcion
*/
{
	function objeto_ut_x($id,&$solicitud)
/*
 	@@acceso: nucleo
	@@desc: Muestra la definicion del OBJETO
*/
	{
		parent::objeto_x($id, $solicitud);
	}
	//-------------------------------------------------------------------------------

	function info_estado()
/*
 	@@acceso: actividad
	@@desc: Muestra es ESTADO del OBJETO
*/
	{
		$dump["padre"]=parent::info_estado();
		//$dump["x"]= $this->x;
		ei_arbol($dump,"Estado del OBJETO");
	}

	//-------------------------------------------------------------------------------
	//-------------  PROCESOS REQUERIDOS en los HIJOS  ------------------------------
	//-------------------------------------------------------------------------------

	function inicializar( $parametros=null )
/*
 	@@acceso: objeto
	@@desc: Inicializar un valor despues del contructor pero antes de utilizarla
	@@param: array | parametros necesarios para la inicializacion
*/
	{
	}	
	//-------------------------------------------------------------------------------
		
	function limpiar_interface()
/*
 	@@acceso: objeto
	@@desc: Limpia la INTERFACE del UT
*/
	{
	}	
	//-------------------------------------------------------------------------------

	function obtener_sql( $tipo=null )
/*
 	@@acceso: objeto
	@@desc: Devuelve el SQL del UT
	@@param: string | ACCION SQL solicitada ( insert / update / delete )
	@@retorno: array | sentencias SQL generadas por la interface
*/
	{
	}	
	//-------------------------------------------------------------------------------

	function actualizacion_post_insert()
/*
 	@@acceso: objeto
	@@desc: Proceso de actualizacion de valores necesarios despues del insert (ej: recuperar valores de secuencias)
*/
	{
	}	
	//-------------------------------------------------------------------------------

	function obtener_clave()
/*
 	@@acceso: objeto
	@@desc: [exclusivo MAESTRO] Indica la CLAVE que se esta procesando
	@@retorno: array | Clave que se esta procesando
*/
	{
	}	
	//-------------------------------------------------------------------------------

	function permitir_eliminar()
/*
 	@@acceso: objeto
	@@desc: [exclusivo MAESTRO] Indica si es posible eliminar los datos que maneja la UT
	@@retorno: boolean | true si se puede eliminar, false en caso contrario
*/
	{
	}	
	//-------------------------------------------------------------------------------

	function establecer_clave_maestro( $clave )
/*
 	@@acceso: objeto
	@@desc: [exclusivo DETALLE] Indica la CLAVE que se esta procesando
	@@param: array | Array asociativo indicando los valores de padre a tomar.
*/
	{
	}	
	//-------------------------------------------------------------------------------	
	
	function cargar_db()
/*
 	@@acceso: objeto
	@@desc: Carga los datos que maneja la interface de la UT desde la base
	@@retorno: boolean | true si se cargo correctamente, false en el caso contrario
*/
	{
	}	
	//-------------------------------------------------------------------------------

	function cargar_post()
/*
 	@@acceso: objeto
	@@desc: Carga los datos que maneja la interface desde el HTTP POST
	@@retorno: boolean | true si se cargo correctamente, false en el caso contrario
*/
	{
	}	
	//-------------------------------------------------------------------------------

	function validar_estado()
/*
 	@@acceso: objeto
	@@desc: Valida que la informacion de la interface es VALIDA
	@@retorno: boolean | true si el estado es correcto, false en el caso contrario
*/
	{
	}	
	//-------------------------------------------------------------------------------

	function cargar_estado_ef()
/*
 	@@acceso: actividad
	@@desc: Carga el estado de un subcomponente. Como no es llamado desde el MT sino desde la
	@@desc: actividad que utiliza el MT, no es obgligatorio.
	@@retorno: boolean | true si el estado es correcto, false en el caso contrario
*/
	{
	}	
	//-------------------------------------------------------------------------------

	function obtener_html()
/*
 	@@acceso: objeto
	@@desc: Hace un echo de la interface de la UT
*/
	{
	}	
	//-------------------------------------------------------------------------------

	function consumo_javascript_global()
/*
 	@@acceso: objeto
	@@desc: Indica que funciones generales de javascript (disponibles a travez del MT) 
	@@desc: necesita para ejecutarse
*/
	{
	}	
	//-------------------------------------------------------------------------------

	function obtener_javascript()
/*
 	@@acceso: objeto
	@@desc: Hace un echo del javascript que necesita la UT. La salida se agrega a la funcion de validacion
	@@desc: del <form> del MT
*/
	{
	}	
	//-------------------------------------------------------------------------------


}
?>