<?php
require_once('objetos_toba/ci_editores_toba.php'); 

class ci_principal extends ci_editores_toba
{
	protected $clase_actual = 'toba_ei_formulario';	
	protected $info_actual = 'toba_ei_formulario_info';

	function ini()
	{
		parent::ini();
		$ef = toba::memoria()->get_parametro('ef');
		//¿Se selecciono un ef desde afuera?
		if (isset($ef)) {
			$this->set_pantalla(2);
			$this->dependencia('efs')->seleccionar_ef($ef);
		}
	}

	function evt__procesar()
	{
		if (! $this->get_dbr_eventos()->hay_evento_maneja_datos()) {
			toba::notificacion()->agregar('El formulario no posee evento que <strong>maneje datos</strong>,
				esto implica que los datos no viajaran del cliente al servidor.<br><br>
				Para que este comportamiento funcione debe generar algún 
				[wiki:Referencia/Eventos#Modelos modelo de eventos] en la solapa
				de Eventos', 'info');
		}
		parent::evt__procesar();		
	}
	
	//*******************************************************************
	//*****************  PROPIEDADES BASICAS  ***************************
	//*******************************************************************

	function conf__prop_basicas()
	{
		return $this->get_entidad()->tabla("prop_basicas")->get();
	}

	function evt__prop_basicas__modificacion($datos)
	{
		$this->get_entidad()->tabla("prop_basicas")->set($datos);
	}

	//*******************************************************************
	//** Dialogo con el CI de EFs  **************************************
	//*******************************************************************

	function evt__2__salida()
	{
		$this->dependencia('efs')->limpiar_seleccion();
	}

	function get_dbr_efs()
	{
		return $this->get_entidad()->tabla('efs');
	}


	//*******************************************************************
	//** Dialogo con el CI de EVENTOS  **********************************
	//*******************************************************************
	
	function get_eventos_estandar($modelo)
	{
		return toba_ei_formulario_info::get_lista_eventos_estandar($modelo);
	}

	function evt__3__salida()
	{
		$this->dependencia('eventos')->limpiar_seleccion();
	}

	function get_dbr_eventos()
	{
		return $this->get_entidad()->tabla('eventos');
	}

}
?>