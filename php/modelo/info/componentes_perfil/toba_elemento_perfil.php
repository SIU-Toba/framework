<?php

abstract class toba_elemento_perfil implements toba_nodo_arbol_form 
{
	protected $padre;
	protected $nombre_corto;
	protected $nombre_largo;
	protected $id = null;
	protected $grupo_acceso = null;
	protected $iconos = array();
	protected $utilerias = array();
	protected $info_extra = null;
	protected $tiene_hijos_cargados = false;
	protected $es_hoja = true;
	protected $hijos = array();
	protected $propiedades = null;
	protected $id_padre;
	protected $nivel;
	protected $camino;
	protected $carpeta = false;
	protected $imagen;
	protected $imagen_origen;
	protected $proyecto;

	protected $oculto;
	protected $solo_lectura;
	protected $abierto = true;	

	protected $acceso_original;
	protected $acceso_actual;
	protected $img_acceso;
	protected $img_sin_acceso;
	
	protected $id_js_arbol; 
	protected $comunicacion_elemento_input = true;

	function __construct($datos, $grupo_acceso)
	{
		$this->id = $datos['item'];
		$this->proyecto = $datos['proyecto'];
		$this->grupo_acceso = $grupo_acceso;
		$this->proyecto = $datos['proyecto'];
		$this->nombre_corto = $datos['nombre'];
		if (!isset($datos['descripcion'])) {
			$this->nombre_largo = $this->nombre_corto;	
		}else{
			$this->nombre_largo = $datos['descripcion'];
		}		
		$this->id_padre = $datos['padre'];
		$this->imagen = $datos['imagen'];
		$this->imagen_origen = $datos['imagen_recurso_origen'];
		$this->acceso_original = ($datos['acceso'] != '') ? true : false;
		$this->acceso_actual = $this->acceso_original;
		if (!$this->es_carpeta()) {
			$this->img_acceso = toba_recurso::imagen_toba('aplicar.png', false);
			$this->img_sin_acceso = toba_recurso::imagen_toba('prohibido.png', false);
		}
	}

	//-- Sincronizacion ------------------------------------------------
	
	function sincronizar()
	{	
		if($this->acceso_original !== $this->acceso_actual) {
			if ($this->acceso_actual) {
				$sql = "INSERT INTO 
							apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item) 
						VALUES 
							('$this->proyecto','$this->grupo_acceso','$this->id');";
			} elseif ($this->acceso_actual === false) {
				$sql = "DELETE FROM 
							apex_usuario_grupo_acc_item 
						WHERE 
							item = '$this->id'
						AND usuario_grupo_acc = '$this->grupo_acceso'
						AND proyecto = '$this->proyecto';";
			}
			toba_contexto_info::get_db()->ejecutar($sql);
		}
		return $this->acceso_actual;
	}

	function set_grupo_acceso($acceso)
	{
		$this->grupo_acceso = $acceso;
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
	
	function get_id_padre()
	{
		return $this->id_padre;	
	}
	
	function set_nivel($nivel)
	{
		$this->nivel = $nivel; 
	}
	
	function set_camino($camino)
	{
		$this->camino = $camino;
	}
	
	function es_carpeta()
	{
		return $this->carpeta;	
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
		if (isset($this->imagen) && ($this->imagen != '') && ($this->imagen_origen != '')) {
			if ($this->imagen_origen == 'apex') {
				$imagen = toba_recurso::imagen_toba($this->imagen, false);	
			} else {
				$imagen = toba_recurso::url_proyecto($this->proyecto).'/img/'.$this->imagen;
			}
		}
		if (!isset($imagen)) {
			$imagen = toba_recurso::imagen_toba($this->icono, false);
		}
		$iconos = array();
		$iconos[] = array('imagen' => $imagen, 'ayuda' => $this->nombre_corto);
		
		return $iconos;
	}
	
	function get_utilerias()
	{
		return $this->utilerias;
	}

	function get_padre()
	{
		return $this->padre;	
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

	function set_apertura($abierto) 
	{
		$this->abierto = $abierto;
	}
	
	function get_apertura() 
	{
		return $this->abierto;
	}
			
	function set_js_ei_arbol($arbol_padre)
	{
		$this->id_js_arbol = $arbol_padre;
	}

	function desactivar_envio_inputs()
	{
		$this->comunicacion_elemento_input = false;		
	}
	
	function set_estado_acceso($acceso)
	{
		$this->acceso_actual = $acceso;
	}
	
	//-------------------------------------------------------------------------------------------//
	//				ESTADO DEL POST
	//-------------------------------------------------------------------------------------------//	
	function propagar_estado_hijos($activos, $inactivos)
	{
		//Primero miro el valor actual del elemento
		$esta_activo = (in_array($this->id, $activos) && ! in_array($this->id, $inactivos));
		$this->set_estado_acceso($esta_activo);	
			
		if ($this->tiene_hijos_cargados()) {
			foreach ($this->get_hijos() as $hijo) {
				$hijo->propagar_estado_hijos($activos, $inactivos);
			}
		}		
	}
	
	//-------------------------------------------------------------------------------------------//
	//				ENVIADO AL CLIENTE
	//-------------------------------------------------------------------------------------------//
	function recuperar_estado_recursivo()
	{
		$estado = array('activos' => array(), 'inactivos' => array());
		if ($this->tiene_hijos_cargados()) {
			foreach ($this->get_hijos() as $hijo) {
				$aux = $hijo->recuperar_estado_recursivo();
				$estado['activos'] = array_merge($estado['activos'], $aux['activos']);
				$estado['inactivos'] = array_merge($estado['inactivos'], $aux['inactivos']);
			}
		}
		
		if (isset($this->acceso_actual) && $this->acceso_actual) {
			$estado['activos'][] = $this->get_id();
		} elseif ($this->acceso_actual === false) {
			$estado['inactivos'][] = $this->get_id();
		}
		return $estado;
	}	
}

?>