<?php 

class ci_subclases extends toba_ci
{
	protected $s__id_componente;
	protected $s__path_relativo;
	protected $s__datos_nombre;
	protected $s__subcomponente;
	protected $s__tipo_elemento;
	protected $s__pm;
	protected $s__modo_pers;
	protected $s__subclase_orig;
	
	protected $clase_php;
	protected $archivo_php;
	
	
	function ini()
	{
		$datos = toba::zona()->get_info();
		$this->recuperar_tipo_elemento();
		$this->recuperar_id_entidad($datos);
		$info = $this->get_metaclase();
		if (! isset($this->s__modo_pers)) {
			$hay_personalizacion = toba_personalizacion::get_personalizacion_iniciada(toba_editor::get_proyecto_cargado());
			$this->s__subclase_orig = toba::memoria()->get_parametro('subclase_pers');										//Indica el nombre de la clase que se personaliza
			$this->s__modo_pers = ($hay_personalizacion && (!is_null($this->s__subclase_orig)));
		}		
		if (! $this->s__modo_pers) {
			if ($info->get_subclase_archivo() != '' &&  $info->get_subclase_nombre() != '') {
				$this->s__path_relativo = dirname($info->get_subclase_archivo());
				if ($this->s__path_relativo == '.') {
					$this->s__path_relativo = '';
				}
				$this->s__datos_nombre = array('nombre' => basename($info->get_subclase_archivo(), '.php'));
				$this->set_pantalla('pant_generacion');
			}
		}
	}
	
	/**
	 * @return toba_componente_info
	 */
	function get_metaclase()
	{
		//-- Acceso a un SUBCOMPONENTE
		if (isset($this->s__subcomponente)) { //Cargue un subcomponente en un request anterior.
			$subcomponente = $this->s__subcomponente;
		} else {
			$subcomponente = toba::memoria()->get_parametro('subcomponente');
		}
		if (! is_null($this->s__tipo_elemento) && ($this->s__tipo_elemento != '')) {		//Busco el info correspondiente al elemento transversal
			$nombre_info = 'toba_' . $this->s__tipo_elemento . '_info';
			$info = new $nombre_info($this->s__id_componente);
		} else {
			$info = toba_constructor::get_info($this->s__id_componente);
		}
		if (isset($subcomponente)) {
			$info = $info->get_metaclase_subcomponente($subcomponente);				//Para pantallas 
			if ($info) {
				$this->s__subcomponente = $subcomponente;
			} else {
				throw new toba_error('ERROR cargando el SUBCOMPONENTE: No es posible acceder a la definicion del mismo.');
			}
		}			
		if (isset($this->s__subclase_orig)) {
			$info->cambiar_clase_origen($this->s__subclase_orig);
		}
		return $info;
	}
	
	function get_prefijo_clase()
	{
		return $this->get_metaclase()->get_nombre_instancia_abreviado().'_';	
	}

	private function recuperar_tipo_elemento()
	{
		if (! isset($this->s__tipo_elemento) || is_null($this->s__tipo_elemento)) {
				$this->s__tipo_elemento = toba::memoria()->get_parametro('elemento_tipo');			//Busco el tipo de elemento por si es transversal
		}
	}

	private function recuperar_id_entidad($datos)
	{
		if (! isset($this->s__id_componente)) {																						//Si recien entra
			$viene_x_memoria = ((!isset($datos) || is_null($datos)) && (!is_null($this->s__tipo_elemento)));
			if ($viene_x_memoria) {									//Es transversal viene por memoria
				$datos['proyecto'] = toba::memoria()->get_parametro('proyecto_extension');
				$datos['id'] = toba::memoria()->get_parametro('id_extension');
				if (is_null($datos['proyecto']) || is_null($datos['id'])) {					//No se cargo nada... todo mal!
					throw new toba_error('Necesita seleccionar un componente para poder extenderlo');
				}
				$this->s__id_componente = array('id'=>$datos['id'], 'proyecto'=>$datos['proyecto'] );
			} else {
				$this->s__id_componente = array('componente'=>$datos['objeto'], 'proyecto'=>$datos['proyecto'] );
			}
		}
	}

	private function recuperar_punto_montaje()
	{
		if (!isset($this->s__pm)) {
			$pmp = toba::memoria()->get_parametro('pm_pers');								//PM del evento de personalizacion
			$pm = toba::memoria()->get_parametro('punto_montaje');							//PM del evento de extension
			if (isset($pmp)) {
				$this->s__pm = toba_modelo_pms::get_pm($pmp, toba_editor::get_proyecto_cargado());
			} elseif (isset($pm)) {
				$this->s__pm = toba_modelo_pms::get_pm($pm, toba_editor::get_proyecto_cargado());
			} else {
				$pm = $this->get_metaclase()->get_punto_montaje();
				$this->s__pm = toba_modelo_pms::get_pm($pm, toba_editor::get_proyecto_cargado());
			}
		}
		return $this->s__pm;
	}
	
	//------------------------------------------------------------------
	//--------	UBICACION
	//------------------------------------------------------------------
	function evt__pant_ubicacion__salida()
	{
		$this->s__path_relativo = $this->dep('carpetas')->get_path_relativo();
	}	
	
	function conf__carpetas(toba_ei_archivos $archivos)
	{
		$archivos->set_solo_carpetas(true);
		$absoluto = $this->recuperar_punto_montaje()->get_path_absoluto();
		$archivos->set_path_absoluto($absoluto);
		$inicial = toba::memoria()->get_parametro('ef_popup_valor');
		if (! is_null($inicial)) {
			$archivos->set_path(dirname($inicial));
		}		
	}
	
	function get_path_relativo()
	{
		return $this->s__path_relativo;
	}
	
	function get_path_absoluto()
	{
		$relativo = $this->get_path_relativo();
		if ($relativo != '') {
			$relativo = '/'.$relativo;
		}
		$pm = $this->recuperar_punto_montaje();
		return $pm->get_path_absoluto().$relativo;
	}
		
	//------------------------------------------------------------------
	//--------	FORM NOMBRE
	//------------------------------------------------------------------
	function evt__pant_nombre__salida()
	{
		if ($this->s__modo_pers) {
			$this->s__modo_pers = false;
		}
	}
	
	function conf__form_nombre(toba_ei_formulario $form)
	{
		if (isset($this->s__datos_nombre)) {
			$datos = $this->s__datos_nombre;
		} else {
			$datos = array();
			$datos['nombre'] = $this->get_prefijo_clase();
		}
		$form->set_datos($datos);
	}
	
	function conf__archivos(toba_ei_archivos $archivos)
	{
		$archivos->set_extensiones_validas(array('php'));
		$archivos->set_path_absoluto($this->get_path_absoluto());
		$archivos->set_crear_archivos(false);
		$archivos->set_crear_carpetas(false);
		$archivos->set_titulo('Extensiones existentes en php/'.$this->get_path_relativo());
	}
	
	function evt__form_nombre__modificacion($datos)
	{
		$this->s__datos_nombre = $datos;
		
		//-- Sincroniza el cambio con la base
		$path_relativo = $this->get_path_relativo();
		if ($path_relativo != '') {
			$path_relativo .= '/';
		}
		$datos['subclase_archivo'] = $path_relativo.$this->s__datos_nombre['nombre'].'.php';
		$pm = $this->recuperar_punto_montaje();
		$this->get_metaclase()->set_subclase($this->s__datos_nombre['nombre'], $datos['subclase_archivo'], $pm->get_id());
		toba_constructor::set_refresco_forzado(true);
	}
	
	function get_path_archivo()
	{
		return $this->get_path_absoluto().'/'.$this->s__datos_nombre['nombre'].'.php';
	}
	
	//-----------------------------------------------------------------
	//---------- GENERACION
	//----------------------------------------------------------------
	
	function conf__pant_generacion()
	{
		$archivo_php = new toba_archivo_php($this->get_path_archivo());
		$codigo_existente = null; 
		if (! $archivo_php->esta_vacio()) {
			$codigo_existente = $archivo_php->get_codigo();
		}
		$molde_clase = $this->get_metaclase()->get_molde_subclase();
		$metodos = $molde_clase->get_lista_metodos($codigo_existente);
		
		$grupos = array();
		//-- Agrupamos los metodos segun dependencia y tipo
		foreach ($metodos as $metodo) {
			$elemento = $metodo['elemento'];
			$id = $metodo['id'];
			$grupo = $elemento->get_grupo();
			if ($elemento instanceof toba_codigo_metodo_js) {
				$grupo = 'Javascript';	
			}
			$grupos[$grupo][$id] = $elemento;
		}
		
		//-- Agrupamos por javascript
		
		// Se definen los EFs del formulario en runtime.
		$clave = array('componente' => '2291', 'proyecto' => 'toba_editor');
		$metadatos = toba_cargador::instancia()->get_metadatos_extendidos($clave, 'toba_ei_formulario');
        
		$ef_base = array(
			'obligatorio' => 0,
			'elemento_formulario' => 'ef_checkbox',
			'descripcion' => '',
			'colapsado' => 0,
			'oculto_relaja_obligatorio' => 0
		);
		$metadatos['_info_formulario_ef'] = array();
       
        
		$i = 0;
		foreach ($grupos as $id_grupo => $metodos) {
			if ($id_grupo == '') {
				$id_grupo = 'Propios';
			}
			$nombre_grupo = "$id_grupo <div><a href='javascript: cambiar_grupo(\"$id_grupo\", true);'>todos</a> / <a href='javascript: cambiar_grupo(\"$id_grupo\", false);''>ninguno</a></div>";
			$separador = $ef_base;
			$separador['identificador'] = "sep_$i";
			$separador['columnas'] = $separador['identificador'];
			$separador['etiqueta'] = $nombre_grupo;
			$separador['elemento_formulario'] = 'ef_barra_divisora';        	
			$metadatos['_info_formulario_ef'][] = $separador;
        	
			foreach ($metodos as $id => $metodo) {
				$ef = $ef_base;
				$ef['identificador'] = 'metodo_'.$id_grupo."_$id";
				$ef['columnas'] = $ef['identificador'];
				$ef['etiqueta'] = $metodo->get_descripcion();
				$ef['descripcion'] = $metodo->get_doc();
				$metadatos['_info_formulario_ef'][] = $ef;
			}
			$i++;
		}
		toba_cargador::instancia()->set_metadatos_extendidos($metadatos, $clave);
	}

	
	function evt__generar()
	{
		$opciones = $this->dep('ci_generacion')->get_opciones();
		$metodos = $this->dep('ci_generacion')->get_metodos_a_generar();		
		$archivo_php = new toba_archivo_php($this->get_path_archivo());
		$clase_php = new toba_clase_php($archivo_php, $this->get_metaclase());
	
		$clase_php->generar($metodos, $opciones['incluir_comentarios'], $opciones['incluir_separadores']);
		$this->pantalla()->set_descripcion('Clase generada correctamente');
		$this->dep('ci_generacion')->set_pantalla('pant_vista_previa');
		
		//Resetea los métodos para que fuerze al usuario a elegir otros si quiere generar nuevamente la clase
		$this->dep('ci_generacion')->resetear_metodos();
	}
	
	//-------------------------------------------------------------------------------
	//-- Apertura de archivos por AJAX ----------------------------------------------
	//-------------------------------------------------------------------------------

	function servicio__ejecutar()
	{ 
		$this->abrir_archivo();
	}

	function abrir_archivo()
	{
		$archivo_php = new toba_archivo_php($this->get_path_archivo());
		if ( !$archivo_php->existe()) {
			throw new toba_error('Se solicito la apertura de un archivo inexistente (\'' . $archivo_php->nombre() . '\').');	
		}
		$archivo_php->abrir();		
	}
		

}

?>