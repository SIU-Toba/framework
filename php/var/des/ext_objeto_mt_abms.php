<?php
require_once("nucleo/browser/clases/objeto_mt_abms.php");

class objeto_mt_abms_x extends objeto_mt_abms
/*
 	@@acceso: nucleo
	@@desc: Extension de la clase ABMS para realizar validaciones especificas
*/
{

	function objeto_mt_abms_x($id,&$solicitud)
/*
 	@@acceso: nucleo
	@@desc: Muestra la definicion del OBJETO
*/
	{	parent::objeto_mt_abms($id, $solicitud);	}
	//-------------------------------------------------------------------------------

	function validar_estado()
/*
 	@@acceso: interno
	@@desc: Validacion de registro en el servidor
*/
	{
		$status = parent::validar_estado();
		$datos = $this->obtener_datos();
		//CONSIGNAS:
		//1) La forma de referirse a los elementos: $datos['identificador_ef']

		//2) La forma de informar problemas:
		// $this->registrar_info_proceso("mensaje","tipo");//Tipo: error | info

		//3)Tiene que devolver true o false, de acuerdo al estado
		return $status;
	}
	//-------------------------------------------------------------------------------
	
	function obtener_javascript()
/*
 	@@acceso: interno
	@@desc: Validacion del registro en el cliente
*/
	{
		echo parent::obtener_javascript();
		$nombre_ef = $this->obtener_nombres_ef();//recupero el nombre de los EF
		//CONSIGNAS:
		//1) Esta funcion debe devolver un BLOQUE que posea un "return true" o "return false"
		// de acuerdo al estado de la validacion
		//2) Para acceder a los valores del formulario hay que utilizar la sintaxis
		//   formulario.{$nombre_ef['identificador_ef']}.value
		echo "";//Javascript particular creado
	}
}
//#############################################################################################
//#############################################################################################

?>