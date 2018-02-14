<?php
/**
 * Una pantalla es la parte gráfica de una etapa del controlador de interface (ci).
 * Es posible acceder a la pantalla desde el ci usando el metodo $this->pantalla()->..
 * 
 * La pantalla se encarga de graficar:
 *  - Un conjunto de dependencias (componentes pertenecientes a la pantalla actual)
 *  - Un conjunto de tabs hacia otras pantalla
 *  - Un conjunto de eventos
 * 
 * @package Componentes
 * @subpackage Eis
 * @jsdoc ci ci
 */
class toba_ei_pantalla extends toba_ei
{
	const NAVEGACION_TAB_HORIZONTAL		= 'tab_h';
	const NAVEGACION_TAB_VERTICAL		= 'tab_v';
	const NAVEGACION_WIZARD				= 'wizard';
	const NAVEGACION_BASICA				= 'basica';
	
	// Navegacion
	protected $_info_ci = array();
	protected $_info_ci_me_pantalla = array();
	protected $_info_pantalla = array();
	protected $_lista_tabs = array();
	protected $_dependencias;
	protected $_nombre_formulario;					// Nombre del <form> del MT
	protected $_submit;								// Boton de SUBMIT
	protected $_id_en_controlador;
	protected $_notificaciones = array();			// Arreglo con notificaciones a mostrar
	protected $_objetos_pantalla = array();		//Arreglo con info de los objetos asociados
	protected $_eventos_pantalla = array();		//Arreglo con info de los eventos asociados
	protected $_navegacion_ajax = false;

	final function __construct($info_pantalla, $submit, $objeto_js)
	{
		parent::__construct($info_pantalla);
		$this->_nombre_formulario = "formulario_toba" ;//Cargo el nombre del <form>
		$this->_submit = $submit;
		$this->objeto_js = $objeto_js;
	}

	/**
	 * @see dependencia
	 * @return toba_componente
	 */

	function set_controlador($controlador, $id_en_padre=null)
	{
		$this->controlador = $controlador;
		$this->_id_en_controlador = $id_en_padre;
	}	

	/**
	 * @ignore 
	 */
	function pre_configurar()
	{
		$this->cargar_lista_dep();
		$this->cargar_lista_tabs();
		$this->cargar_lista_eventos();
	}
	
	/**
	 * Se aplican las restricciones funcionales posibles para este componente
	 * @ignore 
	 */
	protected function aplicar_restricciones_funcionales()
	{
		parent::aplicar_restricciones_funcionales();
		
		//-- Restricción funcional pantalla no-visible ------		
		$no_visibles = toba::perfil_funcional()->get_rf_pantallas_no_visibles($this->_id[1]);
		for ($a = 0; $a<count($this->_info_ci_me_pantalla);$a++)	{		
			if (in_array($this->_info_ci_me_pantalla[$a]['pantalla'], $no_visibles)) {
				$id = $this->_info_ci_me_pantalla[$a]["identificador"];
				$this->_lista_tabs[$id]->ocultar();
				if (isset($this->_eventos['cambiar_tab_'.$id])) {
					unset($this->_eventos['cambiar_tab_'.$id]);	//Borra el registro del evento cambio de tab (si aplica)
				}
			}
		}
		
		//-- Restricción funcional eis no-visible ------
		$no_visibles = toba::perfil_funcional()->get_rf_eis_no_visibles();
		$this->_dependencias = array();
		$lista = $this->_lista_dependencias;
		$this->_lista_dependencias = array();
		foreach ($lista as $id) {
			$dep = $this->controlador->dependencia($id);
			$id_dep = $dep->get_id();
			if (! in_array($id_dep[1], $no_visibles)) {
				$this->_dependencias[$id] = $this->controlador->dependencia($id);
				$this->_lista_dependencias[] = $id;	
			}
		}
		//-----------------------------				
	}
	
	/**
	 * Retorna la etiqueta de la pantalla actual
	 * @return string
	 */
	function get_etiqueta()
	{
		return $this->_info_pantalla["etiqueta"];
	}
	
	/**
	 * Cambia la etiqueta de la pantalla actual
	 * @param string $nueva
	 */
	function set_etiqueta($nueva)
	{
		$this->_info_pantalla["etiqueta"] = $nueva;
	}
	
	/**
	 * Cambia el layout actual de la pantalla usando un template
	 * @param string $template
	 */
	function set_template($template)
	{
		$this->_info_pantalla['template'] = $template;
	}
	
	
	/**
	 * Retorna la descripción de esta pantalla
	 * @return string
	 */
	function get_descripcion()
	{
		return trim($this->_info_pantalla["descripcion"]);
	}

	/**
	 * Cambia la descripción de esta pantalla
	 * @param string $descr
	 * @param string $tipo Puede ser 'info', 'warning', 'error'
	 */
	function set_descripcion($descr, $tipo='info')
	{
		$this->_info_pantalla["descripcion"] = $descr;
		$this->_info_pantalla["descripcion_tipo"] = $tipo;
	}
	
	/**
	 * Cambia el tipo de navegación de la pantalla
	 *
	 * @param mixed $tipo Por ejemplo toba_ei_pantalla::NAVEGACION_TAB_VERTICAL
	 */
	function set_tipo_navegacion($tipo)
	{
		$this->_info_ci['tipo_navegacion'] = $tipo;
	}
	
	function set_navegacion_ajax($set=true)
	{
		$this->_navegacion_ajax = $set;
	}

	//------------------------------------------------------
	//---------------		Dependencias    ----------------
	//------------------------------------------------------

	/**
	 * Agrega una dependencia a esta pantalla.
	 * La dependencia tiene que estar asignada al ci actual, este método sólo indica que esta dependencia
	 * se graficará en esta pantalla
	 * @param string $id_obj ID. de la dependencia en el ci
	 */
	function agregar_dep($id_obj)
	{
		//--- Chequeo para evitar el bug #389				
		if ($this->controlador->existe_dependencia($id_obj)) {
			$this->_lista_dependencias[] = $id_obj;
		} else {
			toba::logger()->error($this->get_txt(). 
					" Se quiere agregar la dependencia '$id_obj', pero esta no está definida en el CI");
		}
		//--- Por si ya estamos en la etapa de servicios
		if (is_array($this->_dependencias)) {
			$this->_dependencias[$id_obj] = $this->controlador->dependencia($id_obj);	
		}
	}

	/**
	 * Determina que una dependencia no será mostrada en la pantalla actual
	 * @param string $id ID. de la dependencia en el ci
	 */
	function eliminar_dep($id)
	{
		if (in_array($id, $this->_lista_dependencias)) {
			array_borrar_valor($this->_lista_dependencias, $id);
		} else {
			throw new toba_error_def($this->get_txt(). 
					" Se quiere eliminar la dependencia '$id', pero esta no está en la pantalla actual");
		}
		//--- Por si ya estamos en la etapa de servicios
		if (isset($this->_dependencias[$id])) {
			unset($this->_dependencias[$id]);
		}
	}
		
	/**
	 * Retorna los ID de las dependencias que se utilizan en esta pantalla
	 * @return array
	 */
	function get_lista_dependencias()
	{
		return $this->_lista_dependencias;
	}
	
	/**
	 * Retorna verdadero si la dependencia se va a mostrar en esta pantalla
	 * @return boolean
	 */
	function existe_dependencia($nombre_dep)
	{
		$lista_dep = $this->get_lista_dependencias();
		return in_array($nombre_dep, $lista_dep);
	}
	
	
	/**
	 * @ignore 
	 */
	protected function cargar_lista_dep()
	{
		$id_actual = $this->_info_pantalla['identificador'];
		foreach($this->_objetos_pantalla as $dep)
		{
			if ($dep['identificador_pantalla'] == $id_actual){
					$this->agregar_dep($dep['identificador_dep']);
			}
		}
	}

	//----------------------------------------------
	//---------------		TABS    ----------------
	//----------------------------------------------
	
	/**
	 * Acceso a un tab o solapa específico
	 * Un tab representa el posible acceso a una pantalla distinta a la actual
	 * @param string $id Identificador de la pantalla
	 * @return toba_tab Objeto toba_tab que representa al tab o solapa
	 */
	function tab($id)
	{
		if(isset($this->_lista_tabs[$id])){
			return $this->_lista_tabs[$id];	
		} else {
			throw new toba_error_def($this->get_txt(). " El tab '$id' no existe.");
		}
	}
	
	/**
	 * Elimina un tab especifico
	 * La consecuencia es que ya no es posible accederlo más durante el pedido de página actual, 
	 * y al momento de graficar la barra de tabs, no será incluido
	 * Si lo que se quiere hacer es desactivar el tab (que se vea pero no se puede acceder), usar el metodo toba_tab::desactivar()
	 * @param string $id Identificador de la pantalla
	 * @see toba_tab::desactivar()
	 */
	function eliminar_tab($id)
	{
		/*if($id == $this->_id_en_controlador ) {
			throw new toba_error_def($this->get_txt(). 
					'No es posible eliminar el tab correspondiente a la pantalla que se esta mostrando');
		}*/
		if (isset($this->_lista_tabs[$id])) {
			unset($this->_lista_tabs[$id]);
		} else {
			throw new toba_error_def($this->get_txt(). 
					" Se quiere eliminar el tab '$id', pero esta no está en la pantalla actual");
		}
	}
	
	/**
	 * Retorna la lista de tabs de la pantalla actual
	 * @return array de toba_tab
	 */
	function get_lista_tabs()
	{
		//-- Restricción funcional pantalla no-visible ------
		//Se modifica el metodo para que el ci almacene la lista de tabs modificados por las restricciones (caso wizard)		
		$lista = array();
		$no_visibles = toba::perfil_funcional()->get_rf_pantallas_no_visibles($this->_id[1]);
		for ($a = 0; $a<count($this->_info_ci_me_pantalla);$a++)	{
			$id = $this->_info_ci_me_pantalla[$a]["identificador"];
			if (!in_array($this->_info_ci_me_pantalla[$a]['pantalla'], $no_visibles)) {
				if (isset($this->_lista_tabs[$id])) {
					$lista[$id] = $this->_lista_tabs[$id];
				}
			}
		}		
		return $lista;
	}

	/**
	 * Carga la lista de botones que representan a las pestañas o tabs que se muestran en la pantalla actual
	 * @ignore 
	 */
	protected function cargar_lista_tabs()
	{
		$this->_lista_tabs = array();
		for($a = 0; $a<count($this->_info_ci_me_pantalla);$a++)
		{
			$id = $this->_info_ci_me_pantalla[$a]["identificador"];
			$datos['identificador'] = $id;
			$datos['etiqueta'] = $this->_info_ci_me_pantalla[$a]["etiqueta"];
			$datos['ayuda'] = $this->_info_ci_me_pantalla[$a]["tip"];
			$datos['imagen'] = $this->_info_ci_me_pantalla[$a]["imagen"];
			$datos['imagen_recurso_origen'] = $this->_info_ci_me_pantalla[$a]["imagen_recurso_origen"];
			$this->_lista_tabs[$id] = new toba_tab($datos);
		}
	}	
	
	//---------------------------------------------
	//---------------	EVENTOS    ----------------
	//---------------------------------------------

	/**
	 * Carga la lista de eventos definidos desde el administrador 
	 * La redefinicion filtra solo aquellos utilizados en esta pantalla
	 * y agrega los tabs como eventos
	 * @ignore 
	 */
	protected function cargar_lista_eventos()
	{
		//--- Filtra los eventos definidos por el usuario segun la asignacion a pantallas
		parent::cargar_lista_eventos();

		if (isset($this->_evento_implicito)) {
			//Si el evento implicito no esta en esta pantalla, no usarlo			
			$id = $this->_evento_implicito->get_id();
			if (! isset($this->_eventos_usuario_utilizados[$id])) {
				unset($this->_evento_implicito);
			}
		}		
		
		//Como los eventos de pantalla vienen indexados por identificador (al igual que los utilizados por el usuario) podemos usar eso a nuestro favor
		// en lugar de hacer el tipico ciclo, asi obtenemos los eventos usados por el usuario en una linea.
		$this->_eventos_usuario_utilizados = array_intersect_key($this->_eventos_usuario_utilizados, $this->_eventos_pantalla);
		
		//-- Agrega los eventos internos relacionados con la navegacion tabs
		switch($this->_info_ci['tipo_navegacion']) {
			case self::NAVEGACION_TAB_HORIZONTAL:
			case self::NAVEGACION_TAB_VERTICAL:
				foreach ($this->_lista_tabs as $id => $tab) {
					$this->registrar_evento_cambio_tab($id);
				}
				break;
			case self::NAVEGACION_WIZARD:
				list($anterior, $siguiente) = array_elem_limitrofes(array_keys($this->get_lista_tabs()),
																	$this->_info_pantalla['identificador']);
				if ($anterior !== false) {
					$e = new toba_evento_usuario();
					$e->set_id('cambiar_tab__anterior');
					$e->set_etiqueta('< &Anterior');
					$e->set_estilo_css('ei-boton-izq');
					$e->set_maneja_datos(false);
					$this->_eventos_usuario[ $e->get_id() ] = $e;				//Lista de eventos
					$nuevo[$e->get_id()] = $e;
					$this->_eventos_usuario_utilizados = array_merge($nuevo, $this->_eventos_usuario_utilizados);
					//$this->_eventos_usuario_utilizados[ $e->get_id() ] = $e;	//Lista de utilizados
				}
				if ($siguiente !== false) {
					$e = new toba_evento_usuario();
					$e->set_id('cambiar_tab__siguiente');
					$e->set_etiqueta('&Siguiente >');
					$this->_eventos_usuario[ $e->get_id() ] = $e;				//Lista de eventos
					$this->_eventos_usuario_utilizados[ $e->get_id() ] = $e;	//Lista de utilizados
				}
				break;
		}		
	}

	/**
	 * Deja registrado en el componente que en el cliente (javascript) se va a consumir el api de cambio de tabs
	 * Esto es necesario explitarlo cuando la forma de navegación no es a través de tabs
	 * @param string $id Id. de la pantalla a la que se va permitir cambiar en el cliente
	 */
	function registrar_evento_cambio_tab($id)
	{
		$this->_eventos['cambiar_tab_'.$id] = array('maneja_datos' => true);		
	}

	//--------------------------------------------------------------------
	//	API para posicionar la botonera
	//--------------------------------------------------------------------

	/**
	 * Posiciona la botonera en la parte inferior del ci
	 * @return array
	 */
	function posicionar_botonera_abajo()
	{
		$this->_posicion_botonera = 'abajo';
	}
	
	/**
	 * Posiciona la botonera en la parte superior del ci
	 * @return array
	 */
	function posicionar_botonera_arriba()
	{
		$this->_posicion_botonera = 'arriba';
	}
	
	/**
	 * Posiciona la botonera en la parte inferior y superior del ci
	 * @return array
	 */
	function posicionar_botonera_ambos()
	{
		$this->_posicion_botonera = 'ambos';
	}	
	
	//---------------------------------------------------------------
	//-------------------------- SALIDA HTML --------------------------
	//----------------------------------------------------------------
	
	/**
	 * Genera el html de todo el componente, incluyendo hiddens necesarios para el correcto funcionamiento del componente
	 */
	function generar_html()
	{
		echo "\n<!-- ################################## Inicio CI ( ".$this->_id[1]." ) ######################## -->\n\n";		
		$ancho = isset($this->_info_ci["ancho"]) ? "style='width:{$this->_info_ci["ancho"]};'" : '';
		
		echo toba::output()->get('Pantalla')->getInicioHtml("{$this->objeto_js}_cont", $ancho);
		
		echo $this->controlador->get_html_barra_editor();
		$class_extra = '';
		if ($this->_info_ci['tipo_navegacion'] == self::NAVEGACION_TAB_HORIZONTAL) {
			$class_extra = 'ci-barra-sup-tabs';
		}
		$this->generar_html_barra_sup(null,true,"ci-barra-sup $class_extra");
		$colapsado = (isset($this->_colapsado) && $this->_colapsado) ? "style='display:none'" : "";
		
		echo toba::output()->get('Pantalla')->getInicioColapsado("cuerpo_{$this->objeto_js}", $colapsado);
		
		//-->Listener de eventos
		if ( (count($this->_eventos) > 0) || (count($this->_eventos_usuario_utilizados) > 0) ) {
			echo toba_form::hidden($this->_submit, '');
			echo toba_form::hidden($this->_submit."__param", '');
		}
		
		//--> Cuerpo del CI		
		echo toba::output()->get('Pantalla')->getInicioWrapperCuerpo($this->_info_ci);
		$this->generar_html_cuerpo();
		echo toba::output()->get('Pantalla')->getFinWrapperCuerpo();
		
		//--> Botonera
		$clase_abajo = toba::output()->get('Pantalla')->getClaseBotonera(false);
		if($this->botonera_abajo()) {
			$this->generar_botones($clase_abajo);
		}
		if ( $this->_utilizar_impresion_html ) {
			$this->generar_utilidades_impresion_html();
		}		
		

		echo toba::output()->get('Pantalla')->getFinColapsado();
		

		echo toba::output()->get('Pantalla')->getFinHtml();
		echo "\n<!-- ###################################  Fin CI  ( ".$this->_id[1]." ) ######################## -->\n\n";
	}

	/**
	 * Genera el html de la barra tabs, el toc (si tiene) y el contenido de las dependencias actuales
	 * @ignore 
	 * @todo ver la manera de factorizar los metodos
	 */
	protected function generar_html_cuerpo()
	{
		echo toba::output()->get('Pantalla')->getInicioCuerpo($this->_info_ci['tipo_navegacion']);
		switch($this->_info_ci['tipo_navegacion'])
		{
			case self::NAVEGACION_TAB_HORIZONTAL:									//*** TABs horizontales
				//Tabs
				echo toba::output()->get('Pantalla')->getPreTabs($this->_info_ci['tipo_navegacion']);

				$this->generar_tabs_horizontales();
				echo toba::output()->get('Pantalla')->getPostTabs($this->_info_ci['tipo_navegacion']);
				//Interface de la etapa correspondiente
				echo toba::output()->get('Pantalla')->getPreContenido($this->_info_ci['tipo_navegacion']);
				$this->generar_html_contenido();
				echo toba::output()->get('Pantalla')->getPostContenido($this->_info_ci['tipo_navegacion']);
				break;
			case self::NAVEGACION_TAB_VERTICAL:								//*** TABs verticales
				echo toba::output()->get('Pantalla')->getPreTabs($this->_info_ci['tipo_navegacion']);
				$this->generar_tabs_verticales();
				echo toba::output()->get('Pantalla')->getPostTabs($this->_info_ci['tipo_navegacion']);
				echo toba::output()->get('Pantalla')->getPreContenido($this->_info_ci['tipo_navegacion']);
				$this->generar_html_contenido();
				echo toba::output()->get('Pantalla')->getPostContenido($this->_info_ci['tipo_navegacion']);
				break;
			case self::NAVEGACION_WIZARD: 									//*** Wizard (secuencia estricta hacia adelante)
				echo toba::output()->get('Pantalla')->getPreTabs($this->_info_ci['tipo_navegacion']);


				if ($this->_info_ci['con_toc']) {
					$this->generar_toc_wizard();
				}
				echo toba::output()->get('Pantalla')->getPostTabs($this->_info_ci['tipo_navegacion']);
				echo toba::output()->get('Pantalla')->getPreContenido($this->_info_ci['tipo_navegacion']);
				$this->generar_html_contenido();
				echo toba::output()->get('Pantalla')->getPostContenido($this->_info_ci['tipo_navegacion']);

				break;
			default:										//*** Sin mecanismo de navegacion
				$this->generar_html_contenido();
		}
		echo toba::output()->get('Pantalla')->getFinCuerpo($this->_info_ci['tipo_navegacion']);
	}

	/**
	 * Grafica el contenido de la pantalla actual, por defecto incluye una sección de descripción
	 * @ignore 
	 */
	protected function generar_html_contenido()
	{
		//--- Descripcion de la PANTALLA
		$es_wizard = $this->_info_ci['tipo_navegacion'] == 'wizard';
		if ($this->_info_pantalla['descripcion'] !="" || $es_wizard) {
			$tipo = isset($this->_info_pantalla['descripcion_tipo']) ? $this->_info_pantalla['descripcion_tipo'] : null;			
			if ($es_wizard) {
				echo toba::output()->get('Pantalla')->getInicioDescWizard();
				echo toba::output()->get('Pantalla')->getTituloWizard($this->get_etiqueta());
				if ($this->_info_pantalla['descripcion'] != "") {
					$this->generar_html_descripcion($this->_info_pantalla['descripcion'], $tipo);	
				}				
				foreach ($this->_notificaciones as $notificacion){
					$this->generar_html_descripcion($notificacion['mensaje'], $notificacion['tipo']);
				}
				echo toba::output()->get('Pantalla')->getFinDescWizard();
				
			} else {
				$this->generar_html_descripcion($this->_info_pantalla['descripcion'], $tipo);
				foreach ($this->_notificaciones as $notificacion){
					$this->generar_html_descripcion($notificacion['mensaje'], $notificacion['tipo']);
				}
			}
			echo toba::output()->get('Pantalla')->getSeparadorDependencias();
		}
		$this->generar_layout();
		echo "<div id='{$this->objeto_js}_pie'></div>";
	}
	
	/**
	 * Dispara la generación de html de los objetos contenidos en esta pantalla
	 * Extender en caso de querer modificar la totalidad del contenido de la pantalla
	 */	
	protected function generar_layout()
	{
		if (!isset($this->_info_pantalla['template']) || trim($this->_info_pantalla['template']) == '') {	
			$existe_previo = 0;
			foreach($this->_dependencias as $dep) {
				if($existe_previo){ //Separador
					echo toba::output()->get('Pantalla')->getSeparadorDependencias();
				}
				$dep->generar_html();	
				$existe_previo = 1;
			}
		} else {
			$this->generar_layout_template();
		}
	}
	
	protected function generar_layout_template()
	{
		$restantes = array_keys($this->_dependencias);		
		//Parseo del template
		$pattern = '/\[dep([\s\w+=\w+]+)\]/i';
		if (preg_match_all($pattern, $this->_info_pantalla['template'], $resultado)) {
			$salida = $this->_info_pantalla['template'];
			if (count($resultado[0]) > count($restantes)) {		//Para cuando se hizo un pantalla->eliminar_dep();
				toba::logger()->debug(' Dependencias Template:');
				toba::logger()->var_dump($resultado[0]);
				throw new toba_error_def($this->get_txt(). " Template incompleto, faltan dependencias en la pantalla:");
			}
			for ($i=0; $i < count($resultado[0]); $i++) {
				$original = $resultado[0][$i];
				$atributos = array();
				foreach (explode(' ',trim($resultado[1][$i])) as $atributo) {
					$partes = explode('=', $atributo);
					$atributos[$partes[0]] = $partes[1];
				}
				if (! isset($atributos['id'])) {
					throw new toba_error_def($this->get_txt()."Tag [dep] incorrecto, falta atributo id");
				}
				if (isset($this->_dependencias[$atributos['id']])) {
					ob_start();
					$this->_dependencias[$atributos['id']]->generar_html();
					$html = ob_get_clean();
					$salida = str_replace($original, $html, $salida);
					array_borrar_valor($restantes, $atributos['id']);
				}
			}
			echo $salida;
		} else {
			echo $this->_info_pantalla['template'];
		}
		if (! empty($restantes)) {
			$faltan = implode(', ', $restantes);
			throw new toba_error_def($this->get_txt(). " Template incompleto, falta incluir las siguientes dependencias: $faltan");
		}		
	}

	/**
	 * Genera la tabla de contenidos del modo navegacion wizard
	 * @ignore 
	 */
	protected function generar_toc_wizard()
	{
		echo toba::output()->get('Pantalla')->getTocWizard($this->_lista_tabs,$this->_id_en_controlador);
	}
	
	/**
	 * @ignore 
	 */
	protected function generar_tabs_horizontales()
	{
		
		echo toba::output()->get('Pantalla')->getInicioTabs($this->_info_ci['tipo_navegacion']);
		foreach( $this->_lista_tabs as $id => $tab ) {
			$editor = '';
			if (toba_editor::modo_prueba()) {
				$editor = toba_editor::get_vinculo_pantalla($this->_id, $this->_info['clase_editor_item'], $id)."\n";
			}			
			echo $tab->get_html('H', $this->_submit, $this->objeto_js, ($this->_id_en_controlador == $id), $editor );
		}
		echo toba::output()->get('Pantalla')->getFinTabs($this->_info_ci['tipo_navegacion']);
		
	}

	/**
	 * @ignore 
	 */	
	protected function generar_tabs_verticales()
	{
		
		echo toba::output()->get('Pantalla')->getInicioTabs($this->_info_ci['tipo_navegacion']);
		foreach( $this->_lista_tabs as $id => $tab ) {
			$editor = '';
			if (toba_editor::modo_prueba()) {
				$editor = toba_editor::get_vinculo_pantalla($this->_id, $this->_info['clase_editor_item'], $id)."\n";
			}
			echo $tab->get_html('V', $this->_submit, $this->objeto_js, ($this->_id_en_controlador == $id), $editor);
		}
		echo toba::output()->get('Pantalla')->getFinTabs($this->_info_ci['tipo_navegacion']);
		
	}
	
	/**
	 * @ignore 
	 */	
	protected function generar_utilidades_impresion_html()
	{
		$id_frame = "{$this->_submit}_print";
		echo "<iframe style='position:absolute;width: 0px; height: 0px; border-style: none;' "
			."name='$id_frame' id='$id_frame' src='about:blank'></iframe>";
		echo toba_js::abrir();
		echo "
		function imprimir_html( url, forzar_popup )
		{
			var usar_popup = (forzar_popup) ? true : false ;
			var f = window.frames.$id_frame.document;
			if ( f && !usar_popup ) {
			    var html = '';
			    html += '<html>';
			    html += '<body onload=\"parent.printFrame(window.frames.urlToPrint);\">';
			    html += '<iframe name=\"urlToPrint\" src=\"' + url + '\"><\/iframe>';
			    html += '<\/body><\/html>';
			    f.open();
			    f.write(html);
			    f.close();
			} else {
				solicitar_item_popup( url, 650, 500, 'yes', 'yes');
			}
		}
		function printFrame (frame) {
		  if (frame.print) {
		    frame.focus();
		    frame.print();
		  }
		}
		";
		echo toba_js::cerrar();
	}

	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Retorna los consumos javascript requerido por este objeto y sus dependencias
	 * @return array
	 * @ignore 
	 */
	function get_consumo_javascript()
	{
		$consumo_js = parent::get_consumo_javascript();
		$consumo_js[] = 'basicos/ajax_respuesta';		
		$consumo_js[] = 'componentes/ci';
		foreach($this->_dependencias as $dep) {
			$temp = $dep->get_consumo_javascript();
			if (isset($temp)) {
				$consumo_js = array_merge($consumo_js, $temp);
			}
		}
		return $consumo_js;
	}

	/**
	 * @ignore 
	 */	
	protected function crear_objeto_js()
	{
		$id = toba_js::arreglo($this->_id, false);
		$identado = toba_js::instancia()->identado();	
		$ajax = toba_js::bool($this->_navegacion_ajax);
		//Crea le objeto CI
		echo $identado."window.{$this->objeto_js} = new ci($id, '{$this->objeto_js}', '{$this->_nombre_formulario}', '{$this->_submit}', '{$this->_id_en_controlador}', $ajax);\n";

		//Agrega la lista de pantallas con las que trabaja el ci
		toba_js::instancia()->identar(1);				
		if ($this->_info_ci['tipo_navegacion'] == self::NAVEGACION_TAB_HORIZONTAL || $this->_info_ci['tipo_navegacion']  ==  self::NAVEGACION_TAB_VERTICAL) {
			$pantallas_activas = array();
			foreach($this->_lista_tabs as $id => $tab) {
				$pantallas_activas[$id] = $tab->esta_activado();
			}
			echo $identado."window.{$this->objeto_js}.agregar_pantallas(".toba_js::arreglo($pantallas_activas, true)."); \n";
		}
				
		//Crea los objetos hijos		
		$objetos = array();		
		foreach($this->_dependencias as $id => $dep) {
			$objetos[$id] = $dep->generar_js();
		}
		$identado = toba_js::instancia()->identar(-1);		
		//Agrega a los objetos hijos
		//ATENCION: Esto no permite tener el mismo formulario instanciado dos veces
		echo "\n";
		foreach ($objetos as $id => $objeto) {
			echo $identado."window.{$this->objeto_js}.agregar_objeto($objeto, '$id');\n";
		}
	}

	/**
	 * @ignore 
	 */	
	function generar_js()
	{
		$identado = toba_js::instancia()->identado();
		echo "\n$identado//---------------- CREANDO OBJETO {$this->objeto_js} --------------  \n";
		$this->crear_objeto_js();
		$this->controlador->extender_objeto_js();
		$this->extender_objeto_js();
		echo "\n";
		$this->iniciar_objeto_js();
		echo "$identado//-----------------------------------------------------------------  \n";		
		return $this->objeto_js;
	}

	//---------------------------------------------------------------
	//------------------------ SALIDA Impresion ---------------------
	//---------------------------------------------------------------
	
	function vista_impresion_html( toba_impresion $salida )
	{
		if (!isset($this->_info_pantalla['template_impresion']) || trim($this->_info_pantalla['template_impresion']) == '') {
			$salida->titulo( $this->controlador->get_titulo() );
			foreach($this->_dependencias as $dep) {
				$dep->vista_impresion_html( $salida );
			}
		} else {
			$this->generar_layout_template_impresion($salida);
		}
	}

	function generar_layout_template_impresion(toba_impresion $obj_salida)
	{
		$restantes = array_keys($this->_dependencias);
		//Parseo del template
		$pattern = '/\[dep([\s\w+=\w+]+)\]/i';
		if (preg_match_all($pattern, $this->_info_pantalla['template_impresion'], $resultado)) {
			$salida = $this->_info_pantalla['template_impresion'];
			for ($i=0; $i < count($resultado[0]); $i++) {
				$original = $resultado[0][$i];
				$atributos = array();
				foreach (explode(' ',trim($resultado[1][$i])) as $atributo) {
					$partes = explode('=', $atributo);
					$atributos[$partes[0]] = $partes[1];
				}
				if (! isset($atributos['id'])) {
					throw new toba_error_def($this->get_txt()."Tag [dep] incorrecto, falta atributo id");
				}
				if (isset($this->_dependencias[$atributos['id']])) {
					ob_start();
					$this->_dependencias[$atributos['id']]->vista_impresion_html($obj_salida);
					$html = ob_get_clean();
					$salida = str_replace($original, $html, $salida);
					array_borrar_valor($restantes, $atributos['id']);
				}
			}
			echo $salida;
		} else {
			echo $this->_info_pantalla['template_impresion'];
		}
		if (! empty($restantes)) {
			$faltan = implode(', ', $restantes);
			throw new toba_error_def($this->get_txt(). " Template de impresiï¿½n incompleto, falta incluir las siguientes dependencias: $faltan");
		}
	}
	
}
?>
