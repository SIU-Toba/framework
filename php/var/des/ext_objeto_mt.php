<?php
require_once("nucleo/browser/clases/objeto_mt.php");

class objeto_mt_x extends objeto_mt
{
	function objeto_mt_x($id,&$solicitud)
	{	
		parent::objeto_mt($id, $solicitud);	
	}
	//-------------------------------------------------------------------------------

	function procesar()
	{
		if( $this->controlar_activacion() )
		{
/*
			$this->iniciar_transaccion();
			$status = $this->ejecutar_sql($sql);
			$this->finalizar_transaccion("mensaje");
			$this->abortar_transaccion("mensaje");
			$this->registrar_info_proceso("Mensaje","error | tipo")
*/
 		}
 	}
	//-------------------------------------------------------------------------------

	function consumo_javascript_global()
	{
		//Array con el javascript generico consumido
	}
	//-------------------------------------------------------------------------------

	function obtener_javascript()
	{
		//Echo del javascript a incluir
	}
	//-------------------------------------------------------------------------------

	function obtener_interface()
	{
		//Crear la interface
	}
	//-------------------------------------------------------------------------------
}
?>