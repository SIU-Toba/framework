<?php
require_once("objeto.php");	

class objeto_ut extends objeto
/*
 	@@acceso: nucleo
	@@desc: Descripcion
*/
{
        var $nombre_formulario;
        var $estado;					//Datos del ESTADO

	function objeto_ut($id)
/*
 	@@acceso: nucleo
	@@desc: Muestra la definicion del OBJETO
*/
	{
		parent::objeto($id);
	}
	//-------------------------------------------------------------------------------

	//-------------------------------------------------------------------------------
	//-------------  PROCESOS REQUERIDOS en los HIJOS  ------------------------------
	//-------------------------------------------------------------------------------

	function estado_cargado()
	{
		if(isset($this->estado)){
			return (is_array($this->estado));
		}else{
			return false;
		}
	}
	//-------------------------------------------------------------------------------

	function inicializar( $parametros )
/*
 	@@acceso: objeto
	@@desc: Inicializar un valor despues del contructor pero antes de utilizarla
	@@param: array | parametros necesarios para la inicializacion
*/
	{
                $this->nombre_formulario = $parametros["nombre_formulario"];
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

	function obtener_sql( $tipo )
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

	function cargar_estado_ef( $estado_ef )
/*
 	@@acceso: actividad
	@@desc: Carga el estado de un subcomponente. Como no es llamado desde el MT sino desde la
	@@desc: actividad que utiliza el MT, no es obgligatorio
	@@param: array | Array asociativo con el nombre de un subcomponente y el valor que debe cargarse en el mismo
*/
	{
	}	
	//-------------------------------------------------------------------------------

	function obtener_datos()
/*
 	@@acceso: actividad
	@@desc: Recupera el estado actual del formulario
	@@retorno: array | estado de cada elemento de formulario
*/
	{
    }
	//-------------------------------------------------------------------------------
        
	function obtener_estado_interno()
/*
	@@acceso: actividad
	@@desc: Recupera el estado del UT
	@@retorno: array | estado interno
*/
	{
	}
	//-------------------------------------------------------------------------------

	function establecer_estado_interno($estado)
/*
	@@acceso: actividad
	@@desc: Permite establecer el estado interno del UT
	@@param: array | Estado del UT
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