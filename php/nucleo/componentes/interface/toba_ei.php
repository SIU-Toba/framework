<?php


/**
 * Clase base de los componentes gráficos o elementos de interface (ei)
 * @package Componentes
 * @subpackage Eis
 * @jsdoc ei ei
 * @wiki Referencia/Objetos
 */
abstract class toba_ei extends toba_componente
{
 	protected $_submit;
	protected $_info_eventos;
 	protected $_info_puntos_control;
 	protected $objeto_js;
	protected $_colapsado = false;						// El elemento sólo mantiene su título
	protected $_evento_implicito=null;					// Evento disparado cuando no hay una orden explicita
	protected $_eventos_atendidos = array();			// Eventos que fueron atendidos en la etapa de eventos
	protected $_eventos = array();						// Eventos INTERNOS del componente
	protected $_eventos_usuario = array();				// Eventos declarados en el administrador
	protected $_eventos_usuario_utilizados = array();	// Lista de eventos del usuario que estan activos
	protected $_eventos_usuario_utilizados_sobre_fila;	// Lista de eventos del administrador que se utilizaran
	protected $_disparo_evento_condicionado_a_datos = false;
	protected $_botones_graficados_ad_hoc = array();		// Lista de botones que se imprimieron por orden del usuario
	protected $_grupo_eventos_activo = '';				// Define el grupo de eventos activos
	protected $_utilizar_impresion_html = false;			// Indica que hay agregar funcionalidad para imprimir
	protected $_prefijo = 'ei';
	protected $_modo_descripcion_tooltip = true;
	protected $_nombre_formulario;
	protected $_posicion_botonera;
	protected static $refresh_ejecuta_eventos = false;
	protected $_notificaciones = array();	
	protected $_mostrar_barra_superior = true;			// Indica si se muestra o no la barra superior
	protected $xml_ns = '';
	protected $xml_ns_url = '';
	protected $xml_atts_ei = '';
	protected $xml_ancho;
	protected $xml_alto;
	protected $xml_tabla_cols;
	protected $xml_incluir_pie = true;
	protected $xml_incluir_cabecera = true;
	protected $xml_pie;
	protected $xml_cabecera;
	protected $xml_alto_pie;
	protected $xml_alto_cabecera;
	protected $xml_copia;
	protected $xml_margenes=array("sup"=>false,"inf"=>false, "izq"=>false, "der"=>false);
	
	function __construct($definicion)
	{
		parent::__construct($definicion);
		$this->_submit = $this->_prefijo.'_'.$this->_id[1];
		$this->objeto_js = "js_".$this->_submit;
		$this->_posicion_botonera = (! is_null($this->_info['posicion_botonera'])) ? $this->_info['posicion_botonera'] : 'abajo';
		$this->preparar_componente();
	}

	/**
	 * Hace que los componentes reenvien sus eventos cuando se hace un refresh sobre la pagina
	 *	por defecto se encuentra desactivado
	 */
	static function set_refresh_ejecuta_eventos($activado=true)
	{
		self::$refresh_ejecuta_eventos = $activado;
	}	

	/**
	 * Extensión de la construcción del componente
	 * No recomendado como ventana de extensión, salvo que se asegure llamar al padre
	 * @ignore 
	 */
	protected function preparar_componente()
	{
		$this->cargar_lista_eventos();	
	}
	
	/**
	 * Destructor del componente
	 */
	function destruir()
	{
		$this->_memoria['eventos'] = array();
		if(isset($this->_eventos)){
			foreach($this->_eventos as $id => $evento ){
				$this->_memoria['eventos'][$id] = apex_ei_evt_maneja_datos;
			}
		}
		if(isset($this->_eventos_usuario_utilizados)){
			$no_visibles = toba::perfil_funcional()->get_rf_eventos_no_visibles();
			foreach($this->_eventos_usuario_utilizados as $id => $evento ){
				//-- Restricción funcional eventos no-visibles. No se guardan en sesion
				if (! in_array($evento->get_id_metadato(), $no_visibles)) {
					if($evento->maneja_datos()){
						$val = apex_ei_evt_maneja_datos;
					}else{
						$val = apex_ei_evt_no_maneja_datos;	
					}
					$this->_memoria['eventos'][$id] = $val;
				}
			}
		}
		parent::destruir();
	}	
	
	/**
	 * Espacio donde el componente deja su estado interno listo para la configuración del componente
	 * @ignore 
	 */
	function pre_configurar()
	{
	}

	/**
	 * Espacio donde el componente cierra su configuración
	 * @ignore 
	 */
	function post_configurar()
	{
		$this->filtrar_eventos();
		$this->aplicar_restricciones_funcionales();
	}
	
	/**
	 * Se aplican las restricciones funcionales posibles para este componente
	 * @ignore 
	 */
	protected function aplicar_restricciones_funcionales()
	{
		//-- Restricción funcional eventos no-visibles ------
		$no_visibles = toba::perfil_funcional()->get_rf_eventos_no_visibles();
		if (! empty($no_visibles)) {
			foreach($this->_eventos_usuario_utilizados as $id => $evento){
				if (in_array($evento->get_id_metadato(), $no_visibles)) {
					$evento->ocultar();
					toba::logger()->debug("Restricción funcional. Se filtro el evento: $id", 'toba');
				}
			}
		}
		//--------------------------------------------------		
	}
	
	//--------------------------------------------------------------------
	//--  EVENTOS   ------------------------------------------------------
	//--------------------------------------------------------------------

	/**
	* Recupera el objeto asociado de un evento
	* @param string $id Id del evento
	* @return toba_evento_usuario
	*/
	function evento($id)
	{
		if (isset($this->_eventos_usuario_utilizados[$id])) {
			return $this->_eventos_usuario_utilizados[$id];
		} else {
			if(isset($this->_eventos_usuario[$id])){
				throw new toba_error_def($this->get_txt(). " El EVENTO '$id' no esta asociado actualmente al componente.");
			} else {
				throw new toba_error_def($this->get_txt(). " El EVENTO '$id' no está definido.");
			}
		}
	}

	/**
	 * Determina que un evento definido va a formar parte de los eventos a mostrar en el servicio actual
	 * @param string $id
	 */
	function agregar_evento($id)
	{
		if(isset($this->_eventos_usuario[ $id ])){
			$this->_eventos_usuario_utilizados[ $id ] = $this->_eventos_usuario[ $id ];
		} else {
			throw new toba_error_def($this->get_txt(). 
					" Se quiere agregar el EVENTO '$id', pero no está definido.");
		}		
	}

	/**
	 * Verifica si un evento esta definido en la lista de eventos a utilizar en el servicio actual
	 * @param string $id
	 */
	function existe_evento($id)
	{
		$existe = false;
		if (isset($this->_eventos_usuario[ $id ])){							//Si existe el evento
			if (isset($this->_eventos_usuario_utilizados[ $id ])){			//Si esta entre los que se usaran en esta pantalla.
				$existe = true;
			}//if
		}//if
		
		return $existe;
	}

	/**
	 * Elimina un evento definido de la lista de eventos a utilizar en el servicio actual
	 * @param string $id
	 */
	function eliminar_evento($id)
	{
		if(isset($this->_eventos_usuario[ $id ])){
			if(isset($this->_eventos_usuario_utilizados[ $id ])){
				if (isset($this->_evento_implicito) && 
						$this->_evento_implicito === $this->_eventos_usuario_utilizados[ $id ]) {
					unset($this->_evento_implicito);
				}
				unset($this->_eventos_usuario_utilizados[ $id ]);
				toba::logger()->debug("Se elimino el evento: $id", 'toba');
			}		
		} else {
			throw new toba_error_def($this->get_txt(). 
					" Se quiere eliminar el EVENTO '$id', pero no está definido.");
		}		
	}

	/**
	 * Especifica si el disparo de los eventos implicitos debe estar asociado al cambio de datos o no
	 * @param boolean $disparo 
	 */
	function set_disparo_eventos_condicionado_datos($disparo = true)
	{
		$this->_disparo_evento_condicionado_a_datos = $disparo;		
	}
	
	//--- Manejo interno --------------------------------------
	
	/**
	 * Carga la lista de eventos definidos desde el editor
	 * @ignore 
	 */
	protected function cargar_lista_eventos()
	{
		foreach ($this->_info_eventos as $info_evento) {
			$e = new toba_evento_usuario($info_evento, $this);
			$this->_eventos_usuario[ $e->get_id() ] = $e;				//Lista de eventos
			$this->_eventos_usuario_utilizados[ $e->get_id() ] = $e;		//Lista de utilizados
			if( $e->es_implicito() ){
				toba::logger()->debug($this->get_txt() . " IMPLICITO: " . $e->get_id(), 'toba');
				$this->_evento_implicito = $e;
			}
		}
	}

	/**
	 * Retorna la lista de eventos que fueron definidos a nivel de fila
	 * @return array(id => toba_evento_usuario)
	 */
	function get_eventos_sobre_fila()
	{
		if(!isset($this->_eventos_usuario_utilizados_sobre_fila)){
			$this->_eventos_usuario_utilizados_sobre_fila = array();
			foreach ($this->_eventos_usuario_utilizados as $id => $evento) {
				if ($evento->esta_sobre_fila()) {
					$this->_eventos_usuario_utilizados_sobre_fila[$id]=$evento;
				}
			}
		}
		return $this->_eventos_usuario_utilizados_sobre_fila;
	}
	
	/**
	 * Retorna la cantidad de eventos que fueron definidos a nivel de fila
	 * @return integer
	 */	
	function cant_eventos_sobre_fila()
	{
		return count( $this->get_eventos_sobre_fila() );
	}

	/**
	 * Inicia la etapa de eventos en este componente
	 * @ignore 
	 */
	function disparar_eventos(){}

	/**
	 * Borra los eventos ejecutados de la memoria para que no se vuelvan a reejecutar con un REFRESH
	 * @ignore 
	 */
	function borrar_memoria_eventos_atendidos()
	{
		if( ! self::$refresh_ejecuta_eventos ) {
			foreach ($this->_eventos_atendidos as $id_evento) {
				toba::memoria()->eliminar_evento_sincronizado_solicitud_previa("obj_".$this->_id[1], $id_evento);
			}
		}
	}
		
	/**
	 * Reporta un evento en el componente controlador
	 * Puede recibir N parametros adicionales (ej <pre>$this->reportar_evento('modificacion', $datos, $fila,...)</pre>)
	 * @param string $evento Id. del evento a disparar
	 */
	protected function reportar_evento($evento)
	{
		$this->_eventos_atendidos[] = $evento;		//Se guarda que eventos se atendieron para que no se vuelvan a ejecutar en caso de refresh
		if (isset($this->_id_en_controlador)) {
			$parametros = func_get_args();
			$parametros	= array_merge(array($this->_id_en_controlador), $parametros);
			return call_user_func_array( array($this->controlador, 'registrar_evento'), $parametros);
		}
	}

	protected function reportar_evento_interno($evento)
	{
		$this->_eventos_atendidos[] = $evento;		//Se guarda que eventos se atendieron para que no se vuelvan a ejecutar en caso de refresh
		if (isset($this->_id_en_controlador)) {
			$parametros = func_get_args();
			$parametros	= array_merge(array($this->_id_en_controlador), $parametros);
			return call_user_func_array( array($this->controlador, 'registrar_evento_interno'), $parametros);
		}		
	}
	
	
	/**
	 * Retorna todos los eventos definidos por el usuario, excluyendo los internos del componente
	 * @return array(toba_evento_usuario)
	 */
	function get_lista_eventos_usuario()
	{
		return $this->_eventos_usuario_utilizados;
	}

	/**
	 * Retorna todos los eventos definidos por el componente (llamados internos), excluyendo los definidos por el usuario
	 * @return array(toba_evento_usuario)
	 */	
	function get_lista_eventos_internos()
	{
		return $this->_eventos;
	}

	/**
	 * Dado una fila, genera el html de los eventos de la misma
	 * @param integer $fila
	 */
	function get_invocacion_evento_fila($evento, $fila, $clave_fila, $salida_como_vinculo = false, $param_extra = array())
	{
		$invoc_evt = '';
		$id = $evento->get_id();
		if( ! $evento->esta_anulado() ) { //Si el evento viene desactivado de la conf, no lo utilizo
			//1: Posiciono al evento en la fila
			$evento->set_parametros($clave_fila);
			if($evento->posee_accion_vincular()) {
				$parametros = $param_extra;
				$parametros[apex_ei_evento] = $id;
				$parametros['fila'] = $fila;
				$evento->vinculo(true)->set_parametros($parametros);
			}
			//2: Ventana de modificacion del evento por fila
			//- a - ¿Existe una callback de modificacion en el CONTROLADOR?
			$callback_modificacion_eventos_contenedor = 'conf_evt__' . $this->_parametros['id'] . '__' . $id;
			if (method_exists($this->controlador, $callback_modificacion_eventos_contenedor)) {
				$this->controlador->$callback_modificacion_eventos_contenedor($evento, $fila);
			} else {
				//- b - ¿Existe una callback de modificacion una subclase?
				$callback_modificacion_eventos = 'conf_evt__' . $id;
				if (method_exists($this, $callback_modificacion_eventos)) {
					$this->$callback_modificacion_eventos($evento, $fila);
				}
			}
			//3: Genero el boton o el js para el link
			if( ! $evento->esta_anulado() ) {
				if ($salida_como_vinculo) {								//Si es un vinculo lo que se envia
					$evento->set_en_botonera(false);
					$evento->set_nivel_de_fila(false);
					$evento->ocultar();
					$invoc_evt = $evento->get_invocacion_js($this->objeto_js, $this->_id);
				} else if ($evento->posee_accionar_diferido()) {		//Si es un evento que no dispara submit inmediatamente (solo para el cuadro por ahora)
					$invoc_evt = $evento->get_html_evento_diferido($id .$this->_submit, $fila, $this->objeto_js, $this->_id);
				} else {																				//Cualquier otro evento, inclusive los de multiple seleccion.
					$invoc_evt = $evento->get_html($this->_submit.$fila, $this->objeto_js, $this->_id);
				}
			} else {
				$evento->restituir();	//Lo activo para la proxima fila
			}
		}
		return $invoc_evt;
	}

	//--- Manejo de grupos de eventos --------------------------------------
	
	/**
	 * Activa un grupo de eventos, excluyendo a aquellos eventos que no pertenecen al mismo
	 * @param string $grupo Id del grupo de eventos
	 */
	function set_grupo_eventos_activo($grupo)
	{
		$this->_grupo_eventos_activo = $grupo;
	}
	
	/**
	 * Retorna el grupo de eventos activos
	 * @return string
	 */
	function get_grupo_eventos_activo()
	{
		return $this->_grupo_eventos_activo;	
	}

	/**
	 * Dispara el filtrado de eventos en base a grupos y a restricciones funcionales
	 * @ignore 
	 */
	protected function filtrar_eventos()
	{
		$grupo = $this->get_grupo_eventos_activo();
		$keys_evt = array_keys($this->_eventos_usuario_utilizados);
		foreach($keys_evt as $id) {
			if ($this->_eventos_usuario_utilizados[$id]->posee_grupo_asociado()) {
				if(!isset($grupo)){ 
					//No hay un grupo activo, no lo muestro
					unset($this->_eventos_usuario_utilizados[$id]);
					toba::logger()->debug("Se filtro el evento: $id", 'toba');
				} else {
					if (! $this->_eventos_usuario_utilizados[$id]->pertenece_a_grupo($grupo)) {
						//El evento no pertenece al grupo
						unset($this->_eventos_usuario_utilizados[$id]);
						toba::logger()->debug("Se filtro el evento: $id", 'toba');
					}
				}
			}
		}
		
	}

	//--- BOTONES -------------------------------------------------
	/**
	 * Devuelve True si la botonera del componente se debe ubicar abajo o en ambos extremos del mismo.
	 * @return boolean
	 */
	function botonera_abajo()
	{
		return ($this->_posicion_botonera != 'arriba');
	}

	/**
	 * Devuelve True si la botonera del componente se debe ubicar arriba o en ambos extremos del mismo.
	 * @return boolean
	 */
	function botonera_arriba()
	{
		return ($this->_posicion_botonera != 'abajo');
	}

	/**
	 * Retorna true si alguno de los eventos definidos por el usuario se va a graficar en la botonera del componente
	 * @return boolean
	 */
	function hay_botones() 
	{
		foreach ($this->_eventos_usuario_utilizados as $evento) {	
			if ( $evento->esta_en_botonera() ) {
				if( !in_array($evento->get_id(), $this->_botones_graficados_ad_hoc ) ) {
					return true;
				}				
			}
		}
		return false;
	}	

	/**
	 * Genera la botonera del componente
	 * @param string $clase Clase css con el que se muestra la botonera
	 */
	function generar_botones($clase = '', $extra='')
	{
		//----------- Generacion
		if ($this->hay_botones()) {
			echo toba::output()->get('ElementoInterfaz')->getInicioBotonera($clase, $extra);
			$this->generar_botones_eventos();
			echo toba::output()->get('ElementoInterfaz')->getFinBotonera();
		} elseif ($extra != '') {
			echo $extra;
		}
	}	
	
	/**
	 * Genera los botones de todos los eventos marcados para aparecer en la botonera.
	 */
	protected function generar_botones_eventos($excluir_botonera=false)
	{
		foreach($this->_eventos_usuario_utilizados as $evento )	{
			if ( $evento->esta_en_botonera() ) {
				if( !in_array($evento->get_id(), $this->_botones_graficados_ad_hoc ) ) {
					$this->generar_boton($evento->get_id(), $excluir_botonera, false);
				}
			}
		}
	}

	/**
	 * Genera el html de un botón específico
	 * @param toba_evento_usuario $evento
	 * @param boolean $retornar Define si devuelve como resultado el HTML o lo 'imprime'
	 */
	protected function generar_html_boton($evento, $retornar=false)
	{
		$salida = '';
		//--- Link al editor
		if (toba_editor::modo_prueba()) {
			$salida .= toba_editor::get_vinculo_evento($this->_id, $this->_info['clase_editor_item'], $evento->get_id())."\n";
		}
		//--- Utilidades de impresion
		if ( $evento->posee_accion_imprimir() ) {
			$this->_utilizar_impresion_html = true;					
		}
		if( ! $evento->esta_anulado() ) {
			$salida .= $evento->get_html($this->_submit, $this->objeto_js, $this->_id);
		}
		if ($retornar) {
			return $salida;
		} else {
			echo $salida;
		}
	}

	/**
	* Metodo para graficar un boton por orden del usuario
	* @param string $id_evento Id. del evento a generar el botón
	* @param boolean $excluir_botonera El botón no se incluye en la botonera predeterminada del componente
	*/
	function generar_boton($id_evento, $excluir_botonera=true, $retornar=false)
	{
		$salida = $this->generar_html_boton($this->evento($id_evento), $retornar);
		if($excluir_botonera) {
			$this->_botones_graficados_ad_hoc[] = $id_evento;
		}
		if ($retornar) {
			return $salida;
		} else {
			echo $salida;
		}		
	}

	//--------------------------------------------------------------------
	//--  PUNTOS DE CONTROL ----------------------------------------------
	//--------------------------------------------------------------------
	
	/**
	 * Determina si el componente tiene algún punto de control asignado para un evento 
	 * @param string $evento Id. del evento
	 * @see toba_puntos_control
	 * @return boolean
	 */
	function tiene_puntos_control($evento)
	{
		return (count($this->get_puntos_control($evento)) > 0);
	}

	/**
	 * Retorna la definición de un punto de control para un evento
	 * @param string $evento Id. del evento
	 * @see toba_puntos_control
	 * @return array
	 */
	function get_puntos_control($evento)
	{
		$ret = array();
		for ($i=0; $i < count($this->_info_puntos_control); $i++) {
		  if ($this->_info_puntos_control[$i]['evento'] == $evento || $evento == '') {
			    $ret[] = $this->_info_puntos_control[$i]['pto_control'];
			}
		}
		return $ret;
	}


	//--------------------------------------------------------------------
	//--  INTERFACE GRAFICA   --------------------------------------------
	//--------------------------------------------------------------------

	/**
	 * Agrega un mensaje de notificacion a esta pantalla
	 * @param string $mensaje
	 * @param string $tipo Puede ser 'info', 'warning', 'error'
	 */
	function agregar_notificacion($mensaje, $tipo='info')
	{
		$this->_notificaciones[] = array('mensaje' => $mensaje, 'tipo' => $tipo);		
	}	
	
	/**
	 * Fuerza a que el componente se grafique colpsado, pudiendo el usuario descolapsarlo posteriormente
	 */
	function colapsar()
	{
		$this->_colapsado = true;
		$this->_info['colapsable'] = true;
	}
	
	/**
	 * Fuerza a que el componente se grafique descolapsado, pudiendo el usuario colapsarlo posteriormente
	 */
	function descolapsar()
	{
		$this->colapsado = false;
	} 	
	
	/**
	 * Determina si el componente podra ser colapsado/descolapsado por el usuario
	 * @param boolean $colapsable Si o no se permite colapsar
	 */
	function set_colapsable($colapsable)
	{
		if (! $colapsable) {
			$this->_colapsado = false;
		}
		$this->_info['colapsable'] = $colapsable;
	}

	/**
	 * Cambia el titulo del componente para el servicio actual
	 * @param string $titulo
	 */
	function set_titulo($titulo)
	{
		$this->_info['titulo'] = $titulo;
	}
	
	/**
	 * Cambia la descripción del componente para el servicio actual
	 * @param string $desc
	 * @param string $tipo Puede ser 'info', 'warning', 'error'
	 */	
	function set_descripcion($desc, $tipo='info')
	{
		$this->_info["descripcion"] = $desc;
		$this->_info["descripcion_tipo"] = $tipo;
	}
	
	/**
	 * Cambia el modo en el que se muestra la descripción del componente (por defecto con un tooltip)
	 * @param boolean $tooltip Si es false la descripción se muestra como una barra aparte
	 */
	function set_modo_descripcion($tooltip=true)
	{
		$this->_modo_descripcion_tooltip = $tooltip;
	}
		
	/**
	 * Genera la barra con el título y los íconos
	 *
	 * @param string $titulo Título de la barra
	 * @param boolean $control_titulo_vacio Si el comp. no tiene titulo definido, ni se lo pasa por parametro, no grafica la barra
	 * @param string $estilo Clase css a utilizar
	 */
	function generar_html_barra_sup($titulo=null, $control_titulo_vacio=false, $estilo="")
	{
		if ($this->_mostrar_barra_superior) {
			
			$botonera_en_item = false;
			if (isset($this->_info_ci['botonera_barra_item']) && $this->_info_ci['botonera_barra_item']) {
				$botonera_en_item = true;	 			
			}
			$botonera_sup = $this->hay_botones() && isset($this->_posicion_botonera) && ($this->_posicion_botonera == "arriba" ||
					 $this->_posicion_botonera == "ambos") && ! $botonera_en_item;
			$tiene_titulo = trim($this->_info["titulo"])!="" || trim($titulo) != '';
			$fuerza_titulo = (isset($this->_info_cuadro) && $this->_info_cuadro['siempre_con_titulo'] == '1');
			if ($botonera_sup || !$control_titulo_vacio || $tiene_titulo || $fuerza_titulo) {
				if (!isset($titulo)) {
					$titulo = $this->_info["titulo"];	
				}
				
				//---Barra de colapsado
				$colapsado = "";
				// Se colapsa cuando no hay botones o cuando hay pero no esta la botonera arriba
				$colapsado_coherente = (! $this->hay_botones() || ($this->hay_botones() && !$this->botonera_arriba()));	
				if ($this->_info['colapsable'] && isset($this->objeto_js) && $colapsado_coherente) {
					$colapsado = "style='cursor: pointer; cursor: hand;' onclick=\"{$this->objeto_js}.cambiar_colapsado();\" title='Mostrar / Ocultar'";
				}
				
				echo toba::output()->get('ElementoInterfaz')->getInicioBarraSuperior($tiene_titulo, $botonera_sup, $estilo, $colapsado);
				
				//--> Botonera
				if ($botonera_sup) {
					$this->generar_botones();
				}						
			
				echo toba::output()->get('ElementoInterfaz')->getContenidoBarraSuperior($titulo, $this->_info["descripcion"], $this->_modo_descripcion_tooltip, $this->_info['colapsable'], $colapsado_coherente, $this->objeto_js,$colapsado);
				//---Titulo			
				echo toba::output()->get('ElementoInterfaz')->getFinBarraSuperior();
				
				//echo ei_barra_fin();
			}
			
			//--- Descripcion con barra. Muestra una barra en lugar de un tooltip
			if(trim($this->_info["descripcion"])!="" &&  !$this->_modo_descripcion_tooltip){
				$tipo = isset($this->_info['descripcion_tipo']) ? $this->_info['descripcion_tipo'] : null;
				$this->generar_html_descripcion($this->_info['descripcion'], $tipo);
			}		
			echo "<div id='{$this->_submit}_notificacion'>";
			foreach ($this->_notificaciones as $notificacion){
				$this->generar_html_descripcion($notificacion['mensaje'], $notificacion['tipo']);
			}
			echo "</div>";
			$this->_notificaciones = array();
		}
		
	}
	
	/**
	 * Configura la visibilidad de la barra superior
 	 * 
	 */
	function mostrar_barra_superior($estado=true)
	{
		$this->_mostrar_barra_superior = $estado;
	}

	
	/**
	 * Genera una descripcion HTML para informar la ocurrencia de algun evento
	 * @ignore
	 */
	protected function generar_html_descripcion($mensaje, $tipo=null)
	{
		echo toba::output()->get("ElementoInterfaz")->getHtmlDescripcion($mensaje, $tipo);
	}
	
	/**
	 * @ignore 
	 */
	function get_nombre_clase()
	{
		return str_replace('objeto', 'toba', $this->_info['clase']);
	}	

	/**
	 * @ignore 
	 */
	function get_html_barra_editor()
	{
		$salida = '';
		$servicio = toba::memoria()->get_servicio_solicitado();
		if( toba_editor::modo_prueba() && ($servicio == 'generar_html' || $servicio == 'html_parcial') ){ 
			$salida .= "<div class='div-editor'>";
			$salida .= toba_editor::generar_zona_vinculos_componente($this->_id, $this->_info['clase_editor_item'], $this->_info['clase'],
										$this->_info['subclase'] != '');
			$id_dep = ($this->_id_en_controlador)? '&nbsp;<strong>'.$this->_id_en_controlador.'</strong>&nbsp;-' : '';
			$salida .= $id_dep . '&nbsp;[' .$this->_info['objeto'] . ']&nbsp;' . $this->_info["nombre"];
			$salida .= "</div>";
		}		
		return $salida;
	}
	
	/**
	 * Retorna el identificador base para los campos HTML
	 * @return string
	 */
	function get_id_form()
	{
		return $this->_submit;	
	}	
	
	//-----------------------------------------
	//--  JAVASCRIPT --------------------------
	//-----------------------------------------
	
	/**
	 * @return array Liberias js a utilizar, se especifican con el path relativo a www/js sin la extension .js
	 * @ignore 
	 */
	function get_consumo_javascript()
	{
		return array('componentes/ei');
	}
	
	/**
	 * Sentencias de creacion, extensión e inicialización en js del objeto js que controla este componente
	 * @ignore 
	 */
	function generar_js()
	{
		$identado = toba_js::instancia()->identado();
		echo "\n$identado//---------------- CREANDO OBJETO {$this->objeto_js} --------------  \n";
		$this->crear_objeto_js();
		$this->extender_objeto_js();
		echo "\n";
		$this->iniciar_objeto_js();
		echo "$identado//-----------------------------------------------------------------  \n";		
		return $this->objeto_js;
	}

	/**
	 * Retorna el id del componente en javascript.
	 * @return string
	 */
	function get_id_objeto_js()
	{
		return $this->objeto_js;
	}

	/**
	 * Sentencia de creacion del componente en javascript
	 * @ignore 
	 */	
	protected function crear_objeto_js()
	{
		$identado = toba_js::instancia()->identado();
		echo $identado."window.{$this->objeto_js} = new ei('{$this->objeto_js}','{$this->_submit}');\n";
	}
	
	/**
	 * Ventana de extensión javascript del componente
	 * @ventana
	 */
	protected function extender_objeto_js()
	{}
	
	function get_objeto_js()
	{
		return $this->objeto_js;
	}	

	/**
	 * Termina la construcción del objeto javascript asociado al componente
	 * @ignore 
	 */
	protected function iniciar_objeto_js()
	{
		$identado = toba_js::instancia()->identado();
		//-- EVENTO implicito --
		if (isset($this->_evento_implicito) && is_object($this->_evento_implicito)){
			$evento_js = $this->_evento_implicito->get_evt_javascript();
			echo toba_js::instancia()->identado()."{$this->objeto_js}.set_evento_implicito($evento_js);\n";
		}
		if ($this->_colapsado) {
			echo $identado."window.{$this->objeto_js}.colapsar();\n";
		}
		//Se agrega al objeto al singleton toba
		echo $identado."toba.agregar_objeto(window.{$this->objeto_js});\n";		
	}
	
	
	//---------------------------------------------------------------
	//----------------------  SALIDA Impresion  ---------------------
	//---------------------------------------------------------------

	/**
	 * Despachador de tipos de salidas de impresion
	 * @param toba_impresion $salida
	 */
	function vista_impresion( toba_impresion $salida )
	{
		if ( $salida instanceof toba_impr_html ) {
			$this->vista_impresion_html( $salida );	
		}
	}

	/**
	 * Impresion HTML por defecto
	 * @param toba_impresion $salida
	 */
	function vista_impresion_html( toba_impresion $salida )
	{
		$salida->titulo( $this->get_nombre() );
	}
	
	//---------------------------------------------------------------
	//----------------------  SALIDA XML  ---------------------------
	//---------------------------------------------------------------
	
	/**
	 * Define la orientación de la página
	 * 
	 * @param string $orientacion Soporta los valores 'landscape' o 'portrait' (default).
	 */
	function xml_set_orientacion($orientacion='portrait') 
	{
		$this->xml_orientacion = ($orientacion == 'landscape')?'landscape':'portrait';
	}
	
	/**
	 * Define el logo de la institución a utilizar en la cabecera del pdf
	 * 
	 * @param string $logo Path a la imagen
	 */
	function xml_set_logo($logo)
	{
		$this->xml_logo = $logo;
	}
	
	/**
	 * Define el titulo a utilizar en la cabecera del pdf
	 * 
	 * @param string $titulo
	 */
	
	function xml_set_titulo($titulo) 
	{
		$this->xml_titulo = $titulo;
	}
	
	/**
	 * Define el subtítulo a utilizar en la cabecera del pdf
	 *  
	 * @param $subtitulo
	 * @return unknown_type
	 */
	function xml_set_subtitulo($subtitulo) 
	{
		$this->xml_subtitulo = $subtitulo;
	}
	
	/**
	 * Define las dimensiones de la página
	 * 
	 * @param string $ancho
	 * @param string $alto
	 */	
	function xml_set_dim_pagina($ancho=false, $alto=false) {
		$this->xml_ancho = $ancho;
		$this->xml_alto = $alto;
	}

	/**
	 * Define si se crea el pié de página.
	 * 
	 * @param boolean $incluir default true
	 */
	function xml_set_incluir_pie($incluir=true) {
		$this->xml_incluir_pie = $incluir;	
	}
	
	/**
	 * Define el pié de página. 
	 * El parámetro $pie debe ser un xml creado con las funciones xml_imagen, xml_texto o xml_tabla.
	 * Para mostrar el número de página actual, incluir '[[actual]]' dentro del texto.
	 * Para mostrar el total de páginas, incluir '[[total]]' dentro del texto.
	 * Por ejemplo, si se quiere mostrar pág 1 de 10, se debe incluir el texto 'pág [[actual]] de [[total]]'.
	 * 
	 * @param string $pie
	 */
	function xml_set_pie($pie=false) {
		$this->xml_pie = !$pie?false:'<pie>'.str_replace(array('[[actual]]', '[[total]]'), array('<pagina-actual/>', '<pagina-total/>'), $pie).'</pie>';	
	}
	
	/**
	 * Define el alto del pié de página. 
	 * @param string $alto
	 */
	function xml_set_alto_pie($alto=false) {
		$this->xml_alto_pie = $alto;	
	}
	
	/**
	 * Define si se crea la cabecera de la página.
	 * 
	 * @param boolean $incluir default true
	 */
	function xml_set_incluir_cabecera($incluir=true) {
		$this->xml_incluir_cabecera = $incluir;	
	}

	/**
	 * Define la cabecera de página. 
	 * El parámetro $cabecera debe ser un xml creado con las funciones xml_imagen, xml_texto o xml_tabla.
	 * Para mostrar el número de página actual, incluir '[[actual]]' dentro del texto.
	 * Para mostrar el total de páginas, incluir '[[total]]' dentro del texto.
	 * Por ejemplo, si se quiere mostrar pág 1 de 10, se debe incluir el texto 'pág [[actual]] de [[total]]'.
	 * 
	 * @param string $cabecera
	 */
	function xml_set_cabecera($cabecera=false) {
		$this->xml_cabecera = !$cabecera?false:'<cabecera>'.str_replace(array('[[actual]]', '[[total]]'), array('<pagina-actual/>', '<pagina-total/>'),$cabecera).'</cabecera>';	
	}

	/**
	 * Define el alto de la cabecera de página. 
	 * @param string $alto
	 */
	function xml_set_alto_cabecera($alto=false) {
		$this->xml_alto_cabecera = $alto;	
	}
	
	/**
	 * Define los márgenes de la página. $margenes debe ser un array de tipo 'nombre'=>'valor', 
	 * donde 'sup', 'inf', 'izq' y 'der' son los nombres para definir los márgenes superior, 
	 * inferior, izquierdo y derecho respectivamente.
	 * 
	 * @param array $margenes 
	 */
	function xml_set_margenes($margenes=array()) {
		foreach($margenes as $k=>$m) {
			if(isset($this->xml_margenes[$k])) {
				$this->xml_margenes[$k] = $m;			
			}
		}
	}
	
	/**
	 * Forma genérica de definir parámetros de usuario. El parámetro $atts debe ser un array de tipo
	 * "nombre"=>"valor".
	 * 
	 * @param array $atts
	 */
	function xml_set_atts_ei($atts=array()) {
		foreach($atts as $k=>$att) {
			$this->xml_atts_ei .= ' '.$k.'="'.$att.'"';
		}
	}

	/**
	 * Define el numero de copias que deben aparecer en el pdf.
	 * 
	 * @param int $copias
	 */
	function xml_set_nro_copias($copias=1) {
		$this->xml_copia = $copias;	
	}
	
	/**
	 * Retorna los atributos que pueden ser incluidos en cualquier tag, y que definen propiedades del documento pdf.
	 * 
	 * @return string con atributos a incluir en un tag xml 
	 */
	function xml_get_att_comunes() {
		$xml = '';
		if (trim($this->_info["titulo"])!="" || (isset($this->xml_titulo) && $this->xml_titulo != '')) {
			$xml .= ' titulo="'.((isset($this->xml_titulo) && $this->xml_titulo != '')?$this->xml_titulo:trim($this->_info["titulo"])).'"';
		}
		if (isset($this->xml_logo) && trim($this->xml_logo)!="") {
			$xml .= ' logo="url(\''.$this->xml_logo.'\')"';
		}
		if (isset($this->xml_subtitulo) && trim($this->xml_subtitulo)!="") {
			$xml .= ' subtitulo="'.trim($this->xml_subtitulo).'"';
		}
		if (isset($this->xml_orientacion)) {
			$xml .= ' orientacion="'.$this->xml_orientacion.'"';
		}
		if (isset($this->xml_ancho) && $this->xml_ancho) {
			$xml .= ' ancho="'.$this->xml_ancho.'"';
		}
		if (isset($this->xml_alto) && $this->xml_alto) {
			$xml .= ' alto="'.$this->xml_alto.'"';
		}
		if (!$this->xml_incluir_pie) {
			$xml .= ' pie="false"';
		}
		if (!$this->xml_incluir_cabecera) {
			$xml .= ' cabecera="false"';
		}
		if ($this->xml_alto_cabecera) {
			$xml .= ' cab_size="'.$this->xml_alto_cabecera.'"';
		}
		if ($this->xml_alto_pie) {
			$xml .= ' pie_size="'.$this->xml_alto_pie.'"';
		}
		if ($this->xml_copia) {
			$xml .= ' copia="'.$this->xml_copia.'"';
		}
		foreach($this->xml_margenes as $k=>$m) {
			if($m) {
				$xml .= ' margen_'.$k.'="'.$m.'"';
			}
		}
		return $xml.$this->xml_atts_ei;
	}

	/**
	 * Retorna los elementos que pueden ser incluidos en cualquier tag, y que definen propiedades del documento pdf, como la cabecera y el pié.
	 * 
	 * @return string con xml de los elementos a incluir.
	 */
	function xml_get_elem_comunes() {
		$xml = '';
		if($this->xml_cabecera) {
			$xml .= $this->xml_cabecera;
		}
		if($this->xml_pie) {
			$xml .= $this->xml_pie;
		}
		return $xml;
	}
	
	/**
	 * Devuelve un string con el xml de un texto y sus atributos a incluir
	 *  
	 * @param string $texto
	 * @param array $atts Array de tipo 'nombre'=>'valor' 
	 * @return string
	 */
	function xml_texto($texto, $atts=array())
	{
		$xml = '<'.$this->xml_ns.'texto ';
		foreach($atts as $k=>$att) {
			$xml .= ' '.$k.'="'.$att.'"';
		}
		if(!array_key_exists('font-size',$atts)) {
			$xml .= ' font-size="8pt"';
		}
		$xml .= '>'.$texto.'</'.$this->xml_ns.'texto>';
		return $xml;
	}
	
	/**
	 * Devuelve un string con el xml de una tabla a incluir. $datos es un array cuyo primer nivel representan las 
	 * filas, y el segundo nivel representan las columnas dentro de una fila. Es decir $datos[0] representa la 
	 * primer fila, y $datos[0][0] representa la primer columna de la primer fila. $datos[n][m] a su vez, puede ser
	 * tanto un string como un array. Si es un string, se toma este como valor de la celda. Si es un array, debe ser de
	 * tipo 'key'=>'value' donde 'key' represente un atributo de la celda de la tabla (atributos del elemento table-cell 
	 * de xsl-fo). Si existe $datos[n][m]['valor'], entonces no es tomado como atributo, sino como el valor de la celda. 
	 * $datos[n][m]['valor'] puede ser tanto un array como un string. Si es string, se incluye diréctamente. Si es array,
	 * se concatenan todos los valores y se incluye el string resultante. Como valor de una celda se puede incluir otro xml.   
	 * 
	 * @param array $datos
	 * @param boolean $es_formulario Indica que cuando el array tiene una fila se deba tratar como un formulario.
	 * @return string
	 */
	function xml_tabla($datos=array(), $es_formulario=true) {
		$xml = '<'.$this->xml_ns.'tabla'.$this->xml_ns_url;
		if(isset($this->xml_tabla_cols) || $datos) {
			$xml .= '><'.$this->xml_ns.'datos>';
			$sfila = (count($datos) > 1 || !$es_formulario)?'<'.$this->xml_ns.'fila>':'';
			$efila = (count($datos) > 1 || !$es_formulario)?'</'.$this->xml_ns.'fila>':'';
			foreach($datos as $fila) {
				$xml .= $sfila;
				foreach($fila as $dato) {
					$xml .= '<'.$this->xml_ns.'dato';
					if(is_array($dato)) {
						foreach($dato as $k=>$v) {
							if($k != 'valor') {
								$xml .= ' '.$k.'="'.$v.'"';
							}
						}
						if(!isset($dato['valor'])) {
							$xml .= '/>';
						} else {
							$xml .= '>'.(is_array($dato['valor'])?implode('',$dato['valor']):$dato['valor']).'</dato>';
						}
					} else {
						$xml .= '>'.$dato.'</dato>';
					}
				}
				$xml .= $efila;
			}
			$xml .= $this->xml_tabla_cols;
			$xml .= '</'.$this->xml_ns.'datos></'.$this->xml_ns.'tabla>';
		} else {
			$xml .= '/>';
		}
		return $xml;
	}
	
	/**
	 * Devuelve un string con el xml de una imagen a incluir.
	 * 
	 * @param string $src Path al archivo de la imagen
	 * @param string $tipo 'svg' o 'jpg' (default)
	 * @param string $titulo
	 * @param string $caption
	 * @return string
	 */
	function xml_imagen($src, $tipo='jpg', $titulo=false, $caption=false) {
		$xml = '<'.$this->xml_ns.'img type ="'.$tipo.'"'.$this->xml_ns_url;
		if($caption) {
			$xml .= ' caption="'.$this->xml_caption.'"';
		}
		if($titulo) {
			$xml .= ' titulo="'.$this->xml_caption.'"';
		}
		$xml .= ' src="url(\''.$src.'\')"';
		if ($tipo=='svg') {
			$svg = file_get_contents($src);
			$svg = substr($svg, stripos($svg, '<svg'));
			$svg = substr($svg, 0, strripos($svg, '</svg>')+6);
			$enc = mb_detect_encoding($svg);
			if (strtolower(substr($enc, 0, 8)) != 'iso-8859') {
				$svg = iconv($enc, 'iso-8859-1', $svg);
			}
			$xml .= $svg.'</'.$this->xml_ns.'img>';
		} else {
			$xml .= '/>';
		}
		return $xml;
	}
	
	/**
	 * Define atributos comunes a columnas de una tabla (atributos del elemento table-column 
	 * de xsl-fo).
	 *  
	 * @param array $cols Array de tipo 'nombre'=>'valor'
	 */
	function xml_set_tabla_cols($cols=array()) {
		if($cols) {
			foreach($cols as $col) {
				$this->xml_tabla_cols .= '<'.$this->xml_ns.'col';
				foreach($col as $k=>$v) {
					$this->xml_tabla_cols .= ' '.$k.'="'.$v.'"';
				}
				$this->xml_tabla_cols .= '/>';
			}
		} else {
			$this->xml_tabla_cols = '';
		}
	}
	
	/**
	 * Define un namespace a utilizar con los elementos xml.
	 * 
	 * @param string $xmlns El namespace propiamente dicho
	 * @param string $url un url del namespace 
	 * @param boolean $usar Usar el namespace en este elemento o sólo declararlo.
	 */
	function xml_set_ns($xmlns, $url='', $usar=true)
	{
		if ($xmlns=='' || $xmlns==null) {
			$this->xml_ns = '';
			$this->xml_ns_url = ($url!='')?' xmlns="'.$url.'"':'';
		} else {
			$this->xml_ns = ($usar)?$xmlns.':':'';
			$this->xml_ns_url= ($url!='')?' xmlns:'.$xmlns.'="'.$url.'"':'';
		}
	}

	function xml_get_informacion_basica_vista()
	{
		//Mantener el orden de las variables ya que se recuperan con list.
		//Temporal hasta que se separen las vistas de los otros componentes.
		return array($this->xml_ns,
							$this->xml_ns_url,
							$this->xml_atts_ei,
							$this->xml_ancho,
							$this->xml_alto,
							$this->xml_tabla_cols,
							$this->xml_incluir_pie,
							$this->xml_incluir_cabecera,
							$this->xml_pie,
							$this->xml_cabecera,
							$this->xml_alto_pie,
							$this->xml_alto_cabecera,
							$this->xml_copia,
							$this->xml_margenes );
	}
}
?>
