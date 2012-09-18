<?php
require_once('nucleo/componentes/interface/interfaces.php');

/**
*	Implemantacion basica de la interface toba_nodo_arbol.
*/
class toba_nodo_basico implements toba_nodo_arbol
{
	protected $padre;
	protected $nombre_corto;
	protected $nombre_largo = null;
	protected $id = null;
	protected $id_padre = null;
	protected $iconos = array();
	protected $utilerias = array();
	protected $info_extra = null;
	protected $tiene_hijos_cargados = false;
	protected $es_hoja = true;
	protected $hijos = array();
	protected $propiedades = null;
	protected $id_js_arbol; 

	function __construct($nombre, $padre=null, $id=null, $id_padre=null)
	{
		$this->nombre_corto = $nombre;
		$this->padre = $padre;
		$this->id = $id;
		$this->id_padre = $id_padre;
	}

	//-- Setters -------------------------------------------------------

	function agregar_utileria($utileria)
	{
		$this->utilerias[] = $utileria;
	}
	
	function agregar_icono($icono)	
	{
		$this->iconos[] = $icono;	
	}

	function set_hijos($hijos)
	{
		$this->hijos = $hijos;
		$this->tiene_hijos_cargados = true;
		$this->es_hoja = false;
	}	
	
	function agregar_hijo($hijo)
	{
		$this->hijos[] = $hijo;
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
	
	//-- Interface -----------------------------------------------------
	
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
	
	function set_js_ei_arbol($arbol_padre)
	{
		$this->id_js_arbol = $arbol_padre;
	}		
}

class toba_nodo_form_basico extends toba_nodo_basico  implements toba_nodo_arbol_form 
{
	protected $oculto;
	protected $solo_lectura;
	protected $abierto = false;	
		
	function get_input($id)
	{
		$check_solo_lectura = $this->solo_lectura ? 'checked' : '';		
		$check_oculto = $this->oculto ? 'checked' : '';
		$html = '';
		$html .= "<input type='checkbox' $check_solo_lectura value='1' name='".$id."_solo_lectura' />";
		$html .= "<input type='checkbox' $check_oculto value='1' name='".$id."_oculto' />";
		return $html;
	}
	
	function cargar_estado_post($id)
	{
		if (isset($_POST[$id.'_solo_lectura'])) {
			$this->solo_lectura = $_POST[$id.'_solo_lectura'];
		} else {
			$this->solo_lectura = false;
		}
		
		if (isset($_POST[$id.'_oculto'])) {
			$this->oculto = $_POST[$id.'_oculto'];
		} else {
			$this->oculto = false;
		}		
	}
	
	function set_apertura($abierto) 
	{
		$this->abierto = $abierto;
	}
	
	function get_apertura() 
	{
		return $this->abierto;
	}	
}

?>