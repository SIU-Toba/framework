<?php

class toba_rf implements toba_nodo_arbol_form 
{
	protected $padre;
	protected $id_padre;
	protected $nombre_corto;
	protected $nombre_largo = null;
	protected $id = null;
	protected $iconos = array();
	protected $utilerias = array();
	protected $info_extra = null;
	protected $tiene_hijos_cargados = false;
	protected $es_hoja = true;
	protected $hijos = array();
	protected $propiedades = null;
	protected $imagen;
	protected $imagen_origen;
	protected $nivel;
	protected $carpeta = false;
	protected $camino;
	protected $proyecto;

	protected $no_visible_original;
	protected $no_visible_actual;

	protected $abierto = false;
	protected $padre_loco;	

	protected $restriccion = '';
	protected $item;

	function __construct($nombre, $padre=null, $id=null)
	{
		$this->nombre_corto = $nombre;
		$this->padre = $padre;
		$this->id = $id;
		$this->inicializar();
	}

	function inicializar(){}

	//-- Setters -------------------------------------------------------

	function set_restriccion($restriccion)
	{
		$this->restriccion = $restriccion;	
		foreach($this->hijos as $hijo) {
			$hijo->set_restriccion($this->restriccion);	
		}
	}

	function agregar_utileria($utileria)
	{
		$this->utilerias[] = $utileria;
	}
	
	function agregar_icono($icono)	
	{
		$this->iconos[] = $icono;	
	}

	function agregar_hijo($hijo)
	{
		$this->hijos[] = $hijo;
		$this->tiene_hijos_cargados = true;
		$this->es_hoja = false;
	}
	
	function set_hijos($hijos)
	{
		$this->hijos = $hijos;
		$this->tiene_hijos_cargados = true;
		$this->es_hoja = false;
	}	
		
	function set_utilerias($utilerias)
	{
		$this->utilerias = $utilerias;
	}
	
	function set_iconos($iconos)	
	{
		$this->iconos = $iconos;	
	}
	
	function set_padre($padre)
	{
		$this->padre = $padre;
	}
	
	function set_nivel($nivel)
	{
		$this->nivel = $nivel;
	}
	
	function set_camino($camino)
	{
		$this->camino = $camino;
	}
	
	//-- Interface -----------------------------------------------------
	
	function marcar_abiertos()
	{
		$nodo = $this->get_padre();
		while ( ($nodo != null) && ($nodo->get_id() != '__raiz__') && (!$nodo->get_apertura()) ) {
			$nodo->set_apertura(true);
			$nodo = $nodo->get_padre();				
		}
	}
	
	function get_padre_loco()
	{
		return $this->padre_loco;
	}
	
	function get_id()
	{
		return $this->id;
	}
	
	function get_nombre_corto()
	{
		return $this->nombre_corto;
	}
	
	function get_nombre_largo()
	{
		return $this->nombre_largo;
	}
	
	function get_info_extra()
	{
		return $this->info_extra;
	}
	
	function get_iconos()
	{
		return $this->iconos;
	}
	
	function get_utilerias()
	{
		return $this->utilerias;
	}

	function get_padre()
	{
		return $this->padre;	
	}
	
	function get_id_padre()
	{
		return $this->id_padre;	
	}
	
	function tiene_hijos_cargados()
	{
		return $this->tiene_hijos_cargados;	
	}
	
	function es_hoja()
	{
		return $this->es_hoja;
	}
	
	function get_hijos()
	{
		return $this->hijos;
	}

	function tiene_propiedades()
	{
		return $this->propiedades;
	}

	function get_input($id)
	{
	}
	
	function cargar_estado_post($id)
	{
	}
	
	function set_apertura($abierto) 
	{
		$this->abierto = $abierto;
	}
	
	function get_apertura() 
	{
		return $this->abierto;
	}

	function es_carpeta()
	{
		return $this->carpeta;
	}
}

?>