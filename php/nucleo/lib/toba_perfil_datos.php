<?php

class toba_perfil_datos
{
	protected $restricciones = array();
	protected $info_dimensiones = array();
	protected $indice_gatillos = array();
	protected $gatillos_activos = array();

	function __construct()
	{
		if( $this->get_id() ) { //Si el usuario tiene un perfil de datos
			$this->cargar_info_restricciones();
			if($this->posee_restricciones()) {
				$this->cargar_info_dimensiones();
				$this->indexar_gatillos();	
				$this->cargar_info_relaciones_entre_tablas();
			}
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
		foreach($this->get_lista_dimensiones_restringidas() as $dim) {
			$this->info_dimensiones[$dim] = toba::proyecto()->get_info_dimension($dim);
		}
	}

	function indexar_gatillos()
	{
		foreach($this->info_dimensiones as $d => $dim) {
			foreach($dim['gatillos'] as $g => $gatillo) {
				//El indice es por tablas a detectar, se deja la posicion del gatillo en la dim correspondiente
				$this->indice_gatillos[$gatillo['tabla_rel_dim']][$d] = $g;
			}	
		}
		$this->gatillos_activos = array_keys($this->indice_gatillos);
	}

	function cargar_info_relaciones_entre_tablas()
	{
	}
	
	function dump()
	{
		ei_arbol($this->info_dimensiones,'Dimensiones');	
		ei_arbol($this->indice_gatillos,'Indice gatillos');	
	}
	
	//-----------------------------------------------
	//------ API para el usuario
	//-----------------------------------------------

	function fitrar($sql)
	{
		if( $this->posee_restricciones ) {
			$where = array();
			$gatillos_encontrados = $this->buscar_gatillos_en_sql( $sql );
			foreach( $gatillos_encontrados as $gatillo ) {
				$dimensiones = array_keys($this->indice_gatillos[$gatillo]);
				foreach( $this->dimensiones as $dimension ) {
					$where[] = $this->get_where_dimension_gatillo( $dimension, $gatillo );
				}
				//Agrego el WHERE en el SQL
			}
		} else {
			return $sql;	
		}
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
		return count($this->restricciones) > 0;
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

	/**
	*	Devuelve la lista de gatillos que esta utilizando el esquema para filtrar SQLs
	*/
	function get_gatillos_activos()
	{
		return $this->gatillos_activos;	
	}

	/**
	*	Devuelve el WHERE correspondiente a un gatillo de una dimension
	*/
	function get_where_dimension_gatillo($dimension, $gatillo)
	{
		//saco la comparacion del gatillo y el IN de las restricciones
	}
	
	/*
	* Devuelve la lista de gatillo encontrados en el query
	*/
	function buscar_gatillos_en_sql($sql)
	{
		//Reconocer FROM de nivel 0 y recuperar tablas
		//buscar gatillos entre las tablas
		
	}
	

}

?>