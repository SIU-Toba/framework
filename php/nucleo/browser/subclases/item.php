<?php
require_once("nucleo/browser/clases/objeto_mt_s_abm.php");	

class objeto_mt_abms_item extends objeto_mt_abms
/*
 	@@acceso: nucleo
	@@desc: Extension de la clase ABMS para realizar validaciones especificas
*/
{

	function objeto_mt_abms_item($id,&$solicitud)
/*
 	@@acceso: nucleo
	@@desc: Muestra la definicion del OBJETO
*/
	{	parent::objeto_mt_abms($id, $solicitud);	}

	function validar_estado()
/*
 	@@acceso: interno
	@@desc:  Validacion de registro en el servidor
*/
	//
	{
		$status = parent::validar_estado();
		$datos = $this->obtener_datos();
		$prefijo = "[validacion especifica ITEM] -";
		//ei_arbol($datos,"DATOS recibidos por el VALIDADOR");
		//Controlo que la primera porcion del identificador del ITEM corresponda al
		//nombre de su PADRE.
		if($datos['padre']=="NULL"){
			$datos['padre']="";//Lo preparo para la comparacion
		}
/*
		if( ((strpos($datos['item'],$datos['padre']."/")) === false) ||
			((strpos($datos['item'],$datos['padre']."/")) != 0 ) )
			{
				//echo "ITEM: '{$datos['item']}'<br>";
				//echo "PADRE: '{$datos['padre']}'<br>";
				$this->registrar_info_proceso("$prefijo El nombre del ITEM debe tomar como 
												base el nombre de su PADRE","error");
				$status = false;
			}
*/		
		//Controlo que se setee una accion si no hay ningun PATRON ni BUFFER definido.
		if(	($datos["actividad_buffer_proyecto"] == "toba") &&
			($datos["actividad_buffer"] == "0") &&
			($datos["actividad_patron_proyecto"] == "toba")&&
			($datos["actividad_patron"] == "especifico")&& 
			((trim($datos["actividad_accion"]) == "")||(trim($datos["actividad_accion"]) == "NULL")) )
			{
				$this->registrar_info_proceso("$prefijo Es necesario definir una 
												ACTIVIDAD (Patron, Buffer o Accion)","error");
				$status = false;
			}

		//Si hay MENU, tiene que haber MENU-ORDEN
		if(	($datos["menu"] == "1")&& 
			((trim($datos["orden"]) == "")||(trim($datos["orden"]) == "NULL")) )
			{
				$this->registrar_info_proceso("$prefijo Si incluye el ITEM en el menu, 
												debe especificar un ORDEN del mismo","error");
				$status = false;
			}

		//-------------
		return $status;
	}
	
	function obtener_javascript()
/*
 	@@acceso: interno
	@@desc: Validacion del registro en el cliente
*/
	{
		echo parent::obtener_javascript();
		$nombre_ef = $this->obtener_nombres_ef();//recupero el nombre de los EF
		echo "
if((formulario.{$nombre_ef['menu']}.checked)&&(formulario.{$nombre_ef['orden']}.value == '')){
	alert('Si incluye el ITEM en el menu, tiene que establecer el ORDEN del mismo');
	formulario.{$nombre_ef['orden']}.focus();
	return false;
}

if((formulario.{$nombre_ef['patron']}.value=='toba".apex_ef_separador."especifico')
	&&(formulario.{$nombre_ef['buffer']}.value == 'toba".apex_ef_separador."0')
	&& (formulario.{$nombre_ef['accion']}.value == '') ){
	alert('Es necesario definir una ACTIVIDAD (PHP encargado del comportamiento del ITEM)');
	formulario.{$nombre_ef['accion']}.focus();
	return false;
}
";	
	}
}
//#############################################################################################
//#############################################################################################
//#############################################################################################


class objeto_mt_abms_item_vinculo extends objeto_mt_abms
/*
 	@@acceso: nucleo
	@@desc: Extension de la clase ABMS para realizar validaciones especificas
*/
{

	function objeto_mt_abms_item_vinculo($id,&$solicitud)
/*
 	@@acceso: nucleo
	@@desc: Muestra la definicion del OBJETO
*/
	{	parent::objeto_mt_abms($id, $solicitud);	}

	function validar_estado()
/*
 	@@acceso: interno
	@@desc: Validacion de registro en el servidor
*/
	{
		$status = parent::validar_estado();
		$datos = $this->obtener_datos();
		$prefijo = "[validacion especifica VINCULADOR] -";
		//Controlo que la primera porcion del identificador del ITEM corresponda al
		//nombre de su PADRE.
		if($datos['vinculo_tipo']=="popup"){
			if ($datos['inicializacion'] != "NULL")
			{
				$inic = explode(",",$datos['inicializacion']);
				if (count($inic) != 3 && count($inic) != 4) {
				$this->registrar_info_proceso("$prefijo Si el tipo de vinculo es Popup, 
												debe completar el campo Inicializacion Popup como lo 
												indica la ayuda (tamaño en X, tamaño en Y, scroll)","error");
					$status = false;
				}
			}else{
				$this->registrar_info_proceso("$prefijo Si el tipo de vinculo es Popup, debe completar 
												el campo Inicializacion Popup","error");
				$status = false;
			}
		}
		return $status;
	}
	
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
				if(miArray.length != 3 && miArray.length != 4){
					alert('Debe completar correctamente el campo Incializacion POPUP (Ver ayuda)');
					formulario.{$nombre_ef['inicializacion']}.focus();
					return false;
				}
			}	
		}";
	}
	//----------------------------------------------------------------------
}
//#############################################################################################
//#############################################################################################

?>