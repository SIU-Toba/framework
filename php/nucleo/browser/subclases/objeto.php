<?php
require_once("nucleo/browser/clases/objeto_mt_s_abm.php");

class objeto_mt_abms_objeto_vinculo extends objeto_mt_abms
/*
 	@@acceso: nucleo
	@@desc: Extension de la clase ABMS para realizar validaciones especificas
*/
{

	function objeto_mt_abms_objeto_vinculo($id,&$solicitud)
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
		$prefijo = "[validacion especifica VINCULADOR] -";
		//Si se eligio como tipo el POPUP, el mismo debe inicializarse
		if($datos['vinculo_tipo']=="popup"){
			if ($datos['inicializacion'] != "NULL")
			{
				$inic = explode(",",$datos['inicializacion']);
				if (count($inic) != 3){
					$this->registrar_info_proceso("$prefijo Si el tipo de vinculo es Popup, 
													debe completar el campo Inicializacion Popup 
													como lo indica la ayuda 
													(tamaño en X, tamaño en Y, scroll)","error");
					$status = false;
				}
			}else{
				$this->registrar_info_proceso("$prefijo Si el tipo de vinculo es Popup, 
												debe completar el campo Inicializacion Popup","error");
				$status = false;
			}
		}
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
		echo "
		if(formulario.{$nombre_ef['vinculo_tipo']}.value == 'popup'){
			if(formulario.{$nombre_ef['inicializacion']}.value == ''){
				alert('Si el Tipo de Link es Popup, debe completar el campo Inicializacion POPUP');
				formulario.{$nombre_ef['inicializacion']}.focus();
				return false;
			}
			else
			{
				var inic = formulario.{$nombre_ef['inicializacion']}.value; 
				miArray  = inic.split(',');
				if(miArray.length != 3){
					alert('Debe completar correctamente el campo Incializacion POPUP (Ver ayuda)');
					formulario.{$nombre_ef['inicializacion']}.focus();
					return false;
				}
			}	
		}";
	}
}
//#############################################################################################
//#############################################################################################

?>