<?php
require_once('objetos_toba/asignador_objetos.php');
require_once('seleccion_imagenes.php');
//----------------------------------------------------------------
class ci_creador_objeto extends toba_ci
{
	protected $clase_actual;
	protected $datos_editor;
	protected $destino;
	protected $objeto_construido;
	
	function ini()
	{
		if (! toba_info_editores::hay_fuente_definida(toba_editor::get_proyecto_cargado())) {
			throw new toba_error('El proyecto actual no tiene definida una fuente de datos propia. Chequear en las propiedades del proyecto.');
		}		
		
		if (isset($this->clase_actual)) {
			$this->cargar_editor();
		}
		$hilo = toba::memoria();
		$destino_tipo = $hilo->get_parametro('destino_tipo');
		if (isset($destino_tipo)) {
			$this->destino = array();
			$this->destino['tipo'] = $destino_tipo;
			$this->destino['objeto'] = $hilo->get_parametro('destino_id');
			$this->destino['proyecto'] = $hilo->get_parametro('destino_proyecto');
			$this->destino['pantalla'] = $hilo->get_parametro('destino_pantalla');
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
	
	function post_eventos()
	{
		if (! isset($this->clase_actual)) {
			$this->set_pantalla('pant_tipos');
		} else if (! isset($this->objeto_construido)) {
			$this->set_pantalla('pant_construccion');
		} else {
			//Sino es que el objeto se creo y no hay que asignarselo a nadie asi que 
			//hay que redireccionar
			$this->redireccionar_a_objeto_creado();
		}
	}
	
	function conf__pant_tipos()
	{
		
		if (isset($this->destino)) {
			if ($this->destino['tipo'] == 'toba_datos_relacion') {
					$this->pantalla()->agregar_dep('info_asignacion_dr');
			} elseif ($this->destino['tipo'] == 'toba_ci' ||
						$this->destino['tipo'] == 'toba_ci_pantalla' || 
							$this->destino['tipo'] == 'toba_cn') { 
				$this->pantalla()->agregar_dep('info_asignacion');
			}
		}
		$this->pantalla()->agregar_dep('tipos');
	}
	
	function conf()
	{
		//--- Se cambia la descripcion de las pantallas
		$des = '';
		switch ($this->get_id_pantalla()) {
			case 'tipos':
				$des = '<strong>Tipo de objeto</strong><br>Seleccione el tipo de [wiki:Referencia/Objetos objeto] a crear.';
				switch ($this->destino['tipo']) {
					case 'toba_item': 
						$des .= '<br>El objeto construido se asignará automáticamente al 
								<strong>item</strong> seleccionado.';
						break;
					case 'toba_ci':
						$des .= '<br>El objeto construido se asignará automáticamente al 
								<strong>CI</strong> seleccionado,<br> con el rol ingresado.';
						break;		
					case 'toba_ci_pantalla':
						$des .= '<br>El objeto construido se asignará automáticamente a la 
								<strong>pantalla</strong> seleccionada,<br> con el rol ingresado.';
						break;		
					case 'datos_relacion':
						$des .= '<br>El datos_tabla construido se asignará automáticamente al
								<strong>datos_relacion</strong> seleccionado,<br> con el rol ingresado.';								
				}
				break;
			case 'construccion':
				$clase_reducida = substr($this->clase_actual['clase'], 7);
				$des = "<strong>Construcción</strong><br>
						Construyendo un [wiki:Referencia/Objetos/$clase_reducida {$this->clase_actual['clase']}]";
				break;			
			case 'asignacion':
				$des = '<strong>Asignación</strong><br>Para poder asignarlo necesita indicar con que identificador se conocera el objeto en el CI.';
				break;
			case 'asignacion_dr':
				$des = '<strong>Asignación a un datos_relacion</strong><br>Ingrese los datos de la tabla en la relación.';
				break;
		}
		if ($des != '') {
			$this->pantalla()->set_descripcion($des);	
		}
	}
	
	//------------------------------------------------------------
	//-----------------  TIPOS DE OBJETOS   ----------------------
	//------------------------------------------------------------
	
	function conf__tipos()
	{
		return toba_info_editores::get_info_tipos_componente($this->destino['tipo'], true, $this->destino['objeto']);
	}
	
	function evt__tipos__seleccionar($clase)
	{
		$this->clase_actual = $clase;
		if (! asignador_objetos::verificar_nuevo_rol($this->destino)) {
			unset($this->destino['id_dependencia']);															//Elimino la componente, sino no la reasigna
			throw new toba_error_usuario('El rol indicado ya esta en uso en el objeto destino');
		}
		$this->cargar_editor();
	}	

	function evt__info_asignacion__modificacion($datos)
	{
		$this->destino += $datos;
	}
	
	function conf__info_asignacion()
	{
		if (isset($this->destino)) {
			return $this->destino;
		}
	}
	
	/**
	*	Parametros para asignar el objeto a un datos_relacion
	*/
	function evt__info_asignacion_dr__modificacion($datos)
	{
		$this->destino += $datos;
	}
	
	function conf__info_asignacion_dr()
	{
		if (isset($this->destino)) {
			return $this->destino;
		}
	}

	//------------------------------------------------------------
	//-----------------  ETAPA DE CONSTRUCCION   ----------------------
	//------------------------------------------------------------
	function evt__volver()
	{
		unset($this->clase_actual);
		unset($this->datos_editor);
	}
	
	function conf__pant_construccion()
	{
		$this->pantalla()->agregar_dep('editor');
	}
		
	function cargar_editor()
	{
		if (!isset($this->datos_editor)) {
			$this->datos_editor = toba_info_editores::get_ci_editor_clase($this->clase_actual['proyecto'], $this->clase_actual['clase']);
		}
		$this->agregar_dependencia('editor', $this->datos_editor['proyecto'], $this->datos_editor['objeto']);
	}
	
	function get_nombre_destino()
	{
		$clave = array('componente' => $this->destino['objeto'],
						'proyecto' => $this->destino['proyecto']);
		$nombre = '';
		if (isset($this->destino)) {
			switch ($this->destino['tipo']) {
				case 'toba_item': 
				case 'toba_ci':
				case 'toba_datos_relacion':
					$info = toba_constructor::get_info($clave, $this->destino['tipo'], false);								
					$nombre .= $info->get_nombre_corto();
					break;
				case 'toba_ci_pantalla':
					//--- Si es una pantalla el info_ci se carga en profunidad para traer el nombre de la misma
					$info = toba_constructor::get_info($clave, 'toba_ci', true);			
					$nombre .= $info->get_nombre_corto();
					break;
			}	
		}	
		return $nombre;
	}
	
	function hay_destino()
	{
		return isset($this->destino['tipo']);	
	}
	
	function destino_es_item()
	{
		return $this->destino['tipo'] == 'toba_item';	
	}
	
	function get_nombre_rol()
	{
		if (isset($this->destino['id_dependencia'])) {
			return $this->destino['id_dependencia'];
		}	
	}
	
	/**
	*	Cuando se procesa este CI es porque el editor contenido ya proceso
	*	Por lo que se debe extraer la clave del objeto creado para su posterior asignacion
	*/
	function evt__editor__procesar()
	{
		$this->objeto_construido = $this->dependencia('editor')->get_entidad()->tabla('base')->get_clave_valor(0);
		
		//---Asigna el objeto creado al destino
		if (isset($this->destino)) {
			$asignador = new asignador_objetos($this->objeto_construido, $this->destino);
			$asignador->asignar();
			$this->redireccionar_a_objeto_creado();
		}
	}
		
	function redireccionar_a_objeto_creado()
	{
		admin_util::redireccionar_a_editor_objeto($this->objeto_construido['proyecto'], 
													$this->objeto_construido['objeto']);
	}

	/**
	* Retorna el objeto destino en el cual se creará el objeto en edicion.
	*/
	function get_destino_objeto()
	{
		return $this->destino['objeto'];
	}
	
	//------------------------------------------------------------------------
	//-------------------------- SERVICIOS --------------------------
	//------------------------------------------------------------------------

	/**
	 * Servicio de mostrar listado de imagenes para elegir
	 */
	static function servicio__ejecutar()
	{
		seleccion_imagenes::generar_html_listado();
	}		
}

?>