<?php

class toba_perfil_datos
{
	protected $restricciones = array();

	function __construct()
	{
		if( $this->get_id() ) { //Si el usuario tiene un perfil de datos
			$this->cargar_info_restricciones();
			$this->cargar_info_dimensiones();
			$this->cargar_info_relaciones_entre_tablas();
			$this->indexar_gatillos();	
		}
	}
	
	function cargar_info_restricciones()
	{
		$restricciones = toba_proyecto_implementacion::get_perfil_datos_restricciones( $this->get_id() );
		if($restricciones) {
			foreach( $restricciones as $restriccion ) {
					$this->restricciones[$restriccion['dimension']][] = explode(',',$restriccion['clave']);
			}
		}		
	}

	function cargar_info_dimensiones()
	{
	}

	function cargar_info_relaciones_entre_tablas()
	{
	}

	function indexar_gatillos()
	{
		
	}
	
	//-----------------------------------------------
	//------ API para el usuario
	//-----------------------------------------------

	function fitrar($sql)
	{
		
	}

	/**
	*	retorna el perfil de datos del usuario
	*	@return $value string. Si el usuario no posee un perfil devuelve NULL
	*/
	function get_id()
	{
		return toba_manejador_sesiones::get_perfil_datos();		
	}

	/**
	*	Indica si el perfil de datos del usuario
	*	@return $value	boolean
	*/
	function posee_restricciones()
	{
		return $this->get_id() && (count($this->restricciones) > 0);
	}

	/**
	*	Retorna un array con las restricciones aplicadas sobre las dimensiones
	*	@return $value	Retorna un array de dimensiones con un subarray de restricciones
	*/
	function get_restricciones()
	{
		return $this->restricciones;
	}

	/**
	*	Retorna un array con las dimensiones sobre las que se establecieron restricciones
	*	@return $value	Retorna un array de dimensiones
	*/
	function get_lista_dimensiones_restringidas()
	{
		if( $this->restricciones) return array_keys($this->restricciones);
	}

	function get_where_dimension_gatillo($dimension, $gatillo)
	{
		
	}

	
	//-----------------------------------------------
	//------ Procesamiento interno
	//-----------------------------------------------

	function recococer_dimensiones_implicadas($sql)
	{
		//Reconocer nivel 0
		//buscar gatillos	
	}
	
	

}

?>