<?php
require_once('admin/objetos_toba/ci_editores_toba.php');

class ci_principal extends ci_editores_toba
{
	protected $ef_seleccionado;
	
	function __construct($id)
	{
		parent::__construct($id);
		$ef = toba::get_hilo()->obtener_parametro('ef');
		//¿Se selecciono un ef desde afuera?
		if (isset($ef)) {
			$this->ef_seleccionado = $ef;
		}
	}

	function destruir()
	{
		parent::destruir();
		//ei_arbol($this->get_entidad()->tabla('efss')->info(true),"efsS");
		//ei_arbol($this->get_estado_sesion(),"Estado sesion");
	}
	
	function get_etapa_actual()
	{
		if (isset($this->ef_seleccionado)) {
			return 2;	//Si se selecciono un ef desde afuera va a la pantalla de edición de ef
		} 
		return parent::get_etapa_actual();
	}
	

	function get_lista_eventos()
	{
		$eventos = parent::get_lista_eventos();
		if( false ){	//Como se va a menejar la eliminacion (dbt y zona!)
			$eventos += eventos::evento_estandar('eliminar',"Eliminar");
		}		
		$eventos += eventos::ci_procesar();
		return $eventos;
	}

	//*******************************************************************
	//*****************  PROPIEDADES BASICAS  ***************************
	//*******************************************************************

	function evt__base__carga()
	{
		return $this->get_entidad()->tabla("base")->get();
	}

	function evt__base__modificacion($datos)
	{
		$this->get_entidad()->tabla("base")->set($datos);
	}

	function evt__prop_basicas__carga()
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
	
	//Antes de cargar los datos de los efs, ver si alguno particular se selecciono desde afuera
	function evt__pre_cargar_datos_dependencias__2()
	{
		if (isset($this->ef_seleccionado)) {
			$this->dependencias['efs']->seleccionar_ef($this->ef_seleccionado);
		}
	}	
	
	function evt__salida__2()
	{
		$this->dependencias['efs']->limpiar_seleccion();
	}

	function get_dbr_efs()
	{
		return $this->get_entidad()->tabla('efs');
	}


	//*******************************************************************
	//** Dialogo con el CI de EVENTOS  **********************************
	//*******************************************************************

	function get_modelos_evento()
	{
		require_once('api/elemento_objeto_ei_formulario_ml.php');
		return elemento_objeto_ei_formulario_ml::get_modelos_evento();
	}
	
	function get_eventos_estandar($modelo)
	{
		require_once('api/elemento_objeto_ei_formulario_ml.php');
		return elemento_objeto_ei_formulario_ml::get_lista_eventos_estandar($modelo);
	}

	function evt__salida__3()
	{
		$this->dependencias['eventos']->limpiar_seleccion();
	}

	function get_dbr_eventos()
	{
		return $this->get_entidad()->tabla('eventos');
	}

	//*******************************************************************
	//*******************  PROCESAMIENTO  *******************************
	//*******************************************************************

	function evt__procesar()
	{
		//Seteo los datos asociados al uso de este editor
		$this->get_entidad()->tabla('base')->set_fila_columna_valor(0,"proyecto",toba::get_hilo()->obtener_proyecto() );
		$this->get_entidad()->tabla('base')->set_fila_columna_valor(0,"clase_proyecto", "toba" );
		$this->get_entidad()->tabla('base')->set_fila_columna_valor(0,"clase", "objeto_ei_formulario_ml" );
		//Sincronizo el DBT
		$this->get_entidad()->sincronizar();	
	}
	//-------------------------------------------------------------------
}
?>