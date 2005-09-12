<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
require_once('admin/db/dao_editores.php');
require_once('api/elemento_item.php');
//----------------------------------------------------------------
class ci_seleccion_tipo_objeto extends objeto_ci
{
	protected $clase_actual;
	protected $datos_editor;
	protected $destino_id;
	protected $destino_proyecto;
	protected $destino_tipo;
	
	function __construct($id)
	{
		parent::__construct($id);
		if (isset($this->clase_actual)) {
			$this->cargar_editor();
		}
		$destino_tipo = toba::get_hilo()->obtener_parametro('destino_tipo');
		//
		if (isset($destino_tipo)) {
			$this->destino_tipo = $destino_tipo;
			$this->destino_id = toba::get_hilo()->obtener_parametro('destino_id');
			$this->destino_proyecto = toba::get_hilo()->obtener_parametro('destino_proyecto');
		}
	}
	
	function mantener_estado_sesion()
	{
		$prop = parent::mantener_estado_sesion();
		$prop[] = 'clase_actual';
		$prop[] = 'datos_editor';
		$prop[] = 'destino_id';
		$prop[] = 'destino_proyecto';		
		$prop[] = 'destino_tipo';
		return $prop;
	}
	
	
	/**
	*	Cuando se selecciona una clase se construye el objeto
	*/
	function get_etapa_actual()
	{
		return (isset($this->clase_actual)) ? 'construccion' : 'tipos';
	}	
	
	function obtener_descripcion_pantalla($pantalla)
	{
		if ($pantalla == 'construccion') {
			return " Construyendo un <strong>{$this->clase_actual['clase']}</strong>" ;	
		}
		return parent::obtener_descripcion_pantalla($pantalla);	
	}
	
	//------------------------------------------------------------
	//-----------------  TIPOS DE OBJETOS   ----------------------
	//------------------------------------------------------------
	
	function evt__tipos__carga()
	{
		return dao_editores::get_clases_editores();
	}
	
	function evt__tipos__seleccionar($clase)
	{
		$this->clase_actual = $clase;
		$this->cargar_editor();
	}	


	//------------------------------------------------------------
	//-----------------  ETAPA DE CONSTRUCCION   ----------------------
	//------------------------------------------------------------
	function evt__volver()
	{
		unset($this->clase_actual);
		unset($this->datos_editor);
	}
	
	/**
	*	Durante la construcción mostrar el editor
	*/	
	function get_lista_ei__construccion()
	{
		return array("editor");
	}
	
		
	function cargar_editor()
	{
		if (!isset($this->datos_editor)) {
			$this->datos_editor = dao_editores::get_ci_editor_clase($this->clase_actual['proyecto'], $this->clase_actual['clase']);
		}
		$this->agregar_dependencia('editor', $this->datos_editor['proyecto'], $this->datos_editor['objeto']);
	}
	
	function evt__editor__procesar()
	{
		echo "SI";
	}
	
}

?>