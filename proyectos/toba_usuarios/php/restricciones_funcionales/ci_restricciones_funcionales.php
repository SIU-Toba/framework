<?php 
class ci_restricciones_funcionales extends toba_ci
{
	protected $s__arbol_cargado;
	
	function ini__operacion() 
	{
		
	}

	function conf__arbol(arbol_restricciones_funcionales $arbol) 
	{
		if (! isset($this->s__arbol_cargado) || !$this->s__arbol_cargado) {
			$catalogador = new toba_catalogo_items_perfil('toba_referencia');
			$catalogador->cargar_todo();
			$raiz = $catalogador->buscar_carpeta_inicial();
/*			$raiz = new toba_item_perfil();
			$hijo = new nodo_prueba($raiz);
			$nieto = new nodo_prueba($hijo);*/
			$arbol->set_datos(array($raiz), true);
			$this->s__arbol_cargado = true;
		}
	}
	
	
}


class nodo_prueba implements toba_nodo_arbol_form
{
	static $id_global;
	protected $id;
	protected $padre;
	protected $hijos;
	protected $oculto;
	protected $solo_lectura;
	protected $abierto = false;	
	
	function __construct($padre=null) {
		if (! isset(self::$id_global)) {
			self::$id_global = 1;
		}
		$this->id = self::$id_global++;
		$this->padre = $padre;
		$this->hijos = array();
		if (isset($padre)) {
			$padre->agregar_hijo($this);
		}
		$this->oculto = false;
		$this->solo_lectura = false;
	}
	
	function agregar_hijo($padre) {
		$this->hijos[] = $padre;
	}
	
	
	//--- Interface nodo
	
	function get_padre() {
		return $this->padre;
	}
	
	function es_hoja() {
		return count($this->hijos) == 0;
	}
	
	function get_hijos() {
		return $this->hijos;
	}
	
	function tiene_hijos_cargados() {
		return true;
	}
	
	function tiene_propiedades() {
		return false;
	}
	
	function get_nombre_corto() {
		return 'Nombre '.$this->id;
	}
	
	function get_nombre_largo() {
		return 'Nombre corto';
	}	
	
	function get_info_extra() {
		return '';
	}
	
	function get_utilerias() {
		return array();
	}
	
	function get_iconos() {
		return array();
	}
	
	function get_id() {
		return $this->id;
	}
	
	//--- Interface FORM

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