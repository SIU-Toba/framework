<?php

/**
 * Elemento no-editable, que no viaja al cliente y mantiene su estado en el servidor
 * los elementos ocultos no se propagan a traves del formulario porque no tienen interface.
 * La propagacion de su estado a travez de solicitudes se lleva a cabo impementado MEMORIA
 * @package Componentes
 * @subpackage Efs
 */
class toba_ef_oculto extends toba_ef
{
	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);	
		if(isset($parametros['estado_defecto'])){
			$this->estado_defecto = $parametros['estado_defecto'];
			$this->set_estado($this->estado_defecto);
		}		
	}

	function set_estado($estado)
	{
		$this->estado = $estado;
		toba::memoria()->set_dato_sincronizado($this->clave_memoria(), $this->estado);
		return true;
	}

	/**
	 * Como el componente no viaja al cliente, carga su estado de la memoria (sesion)
	 */
	function cargar_estado_post()
	{
		//Intenta cargar el estado a partir del hilo
		$temp = toba::memoria()->get_dato_sincronizado($this->clave_memoria());
		if(isset($temp)) {
			$this->estado = $temp;
			//Tengo que memorizar el estado para la proxima instanciacion
			toba::memoria()->set_dato_sincronizado($this->clave_memoria(), $this->estado);
		}
	}
	
	function resetear_estado()
	{
		toba::memoria()->eliminar_dato_sincronizado($this->clave_memoria());
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
{

	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);	
		$this->estado = toba::usuario()->get_id();
	}

	function resetear_estado()
	{
		$this->estado = toba::usuario()->get_id();
	}	

	function set_estado($estado=null)
	{
		$this->estado = toba::usuario()->get_id();
		return true;
	}

}
//########################################################################################################
//########################################################################################################

?>