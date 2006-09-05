<?php

/**
 * Elemento no-editable, que no viaja al cliente y mantiene su estado en el servidor
 * @package Componentes
 * @subpackage Efs
 */
class toba_ef_oculto extends toba_ef
{
//Atencion: los elementos ocultos no se propagan a traves del formulario porque no tienen interface.
//La propagacion de su estado a travez de solicitudes se lleva a cabo impementado MEMORIA

	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);	
		if(isset($parametros['estado_defecto'])){
			$this->estado_defecto = $parametros['estado_defecto'];
			$this->set_estado($this->estado_defecto);
		}		
	}

	function clave_memoria()
	{
		return "ef_" . $this->id_form;
	}

	function set_estado($estado)
	{
		$this->estado = $estado;
		toba::hilo()->persistir_dato_sincronizado($this->clave_memoria(), $this->estado);
		return true;
	}

	function cargar_estado_post()
	{
		//Intenta cargar el estado a partir del hilo
		$temp = toba::hilo()->recuperar_dato_sincronizado($this->clave_memoria());
		if(isset($temp)) {
			$this->estado = $temp;
			//Tengo que memorizar el estado para la proxima instanciacion
			toba::hilo()->persistir_dato_sincronizado($this->clave_memoria(), $this->estado);
			return true;
		}
		return false;
	}
	
	function resetear_estado()
	//Devuelve el estado interno
	{
		toba::hilo()->eliminar_dato_sincronizado($this->clave_memoria());
		if(isset($this->estado)){
			unset($this->estado);
		}
		if (isset($this->estado_defecto)) {
			$this->estado = $this->estado_defecto;	
		}
	}

	function get_input()
	{
		return null;
	}

	function get_interface()
	{
		return null;;
	}

}
//########################################################################################################
//########################################################################################################

/**
 * Elemento no-editable, que no viaja al cliente y mantiene su estado en el servidor. 
 * Su estado por defecto es el id del usuario actualmente logueado
 * @package Componentes
 * @subpackage Efs
 */
class toba_ef_oculto_usuario extends toba_ef_oculto
//Este elemento maneja un campo ques una secuencia
{

	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);	
		$this->estado = toba::usuario()->get_id();
	}

	function resetear_estado()
	//Devuelve el estado interno
	{
		$this->estado = toba::usuario()->get_id();
	}	

	function set_estado($estado=null)
	//Desabilito la carga via POST y utilizo memoria
	{
		$this->estado = toba::usuario()->get_id();
		return true;
	}

}
//########################################################################################################
//########################################################################################################

?>