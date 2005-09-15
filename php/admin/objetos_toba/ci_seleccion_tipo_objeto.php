<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
require_once('admin/db/dao_editores.php');
require_once('api/elemento_item.php');
require_once('api/elemento_objeto.php');
require_once('admin/editores/asignador_objetos.php');
require_once('admin/admin_util.php');
//----------------------------------------------------------------
class ci_seleccion_tipo_objeto extends objeto_ci
{
	protected $clase_actual;
	protected $datos_editor;
	protected $destino;
	protected $objeto_construido;
	
	function __construct($id)
	{
		parent::__construct($id);
		if (isset($this->clase_actual)) {
			$this->cargar_editor();
		}
		$hilo = toba::get_hilo();
		$destino_tipo = $hilo->obtener_parametro('destino_tipo');
		if (isset($destino_tipo)) {
			$this->destino = array();
			$this->destino['tipo'] = $destino_tipo;
			$this->destino['id'] = $hilo->obtener_parametro('destino_id');
			$this->destino['proyecto'] = $hilo->obtener_parametro('destino_proyecto');
			$this->destino['pantalla'] = $hilo->obtener_parametro('destino_pantalla');
		}
	}
	
	function mantener_estado_sesion()
	{
		$prop = parent::mantener_estado_sesion();
		$prop[] = 'clase_actual';
		$prop[] = 'datos_editor';
		$prop[] = 'destino';
		$prop[] = 'objeto_construido';
		return $prop;
	}
	
	
	/**
	*	Cuando se selecciona una clase se construye el objeto
	*/
	function get_etapa_actual()
	{
		if (! isset($this->clase_actual)) {
			return "tipos";
		} 
		if (! isset($this->objeto_construido)) {
			return "construccion";
		}
		if (isset($this->destino)) {
			return "asignacion";
		}
		//Sino es que el objeto se creo y no hay que asignarselo a nadie asi que 
		//hay que redireccionar
		$this->redireccionar_a_objeto_creado();
	}	
	
	function obtener_descripcion_pantalla($pantalla)
	{
		if ($pantalla == 'construccion') {
			return "Construyendo un <strong>{$this->clase_actual['clase']}</strong>" ;	
		} elseif ($pantalla == 'asignacion') {
			return "Objeto creado";
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
	
	/**
	*	Cuando se procesa este CI es porque el editor contenido ya proceso
	*	Por lo que se debe extraer la clave del objeto creado para su posterior asignacion
	*/
	function evt__editor__procesar()
	{
			$valores = $this->dependencias['editor']->get_entidad()->tabla('base')->get();
			$this->objeto_construido = array('id' => $valores['objeto'], 'proyecto' => $valores['proyecto']);
			//Si el destino es un item se asigna aqui nomas
			
			if (isset($this->destino) && $this->destino['tipo'] == 'item') {
				$this->evt__asignar();
			}
	}
	
	//----------------------------------------------------------
	//-----------------  ETAPA DE ASIGNACION   -----------------
	//----------------------------------------------------------
	function evt__info_asignacion__modificacion($datos)
	{
		$this->destino['id_dependencia'] = $datos['id_dependencia'];
	}
	
	function evt__asignar()
	{
		$asignador = new asignador_objetos($this->objeto_construido, $this->destino);
		$asignador->asignar();
		$this->redireccionar_a_objeto_creado();
	}
	
	function redireccionar_a_objeto_creado()
	{
		$elem_objeto = elemento_objeto::get_elemento_objeto($this->objeto_construido['proyecto'], 
														 	$this->objeto_construido['id']);
		$vinculo = $elem_objeto->vinculo_editor();
		admin_util::refrescar_editor_item();
		echo js::abrir();
		echo "window.location.href='$vinculo'\n";
		echo js::cerrar();
	}
	
}

?>