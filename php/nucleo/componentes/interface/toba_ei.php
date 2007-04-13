<?php
define('apex_ei_analisis_fila', 'apex_ei_analisis_fila');   //Id de la columna utilizada para el resultado del analisis de una fila
define("apex_ei_evento","evt");
define("apex_ei_separador","__");
define("apex_db_registros_clave","x_dbr_clave");			//Clave interna de los DB_REGISTROS
define("apex_datos_clave_fila","x_dbr_clave");				//Clave interna de los datos_tabla, por compatibilidad es igual.
define('apex_ei_evt_sin_rpta', 'apex_ei_evt_sin_rpta');
define('apex_ei_evt_maneja_datos', 1);
define('apex_ei_evt_no_maneja_datos', -1);

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
	protected $_eventos = array();						// Eventos INTERNOS del componente
	protected $_eventos_usuario = array();				// Eventos declarados en el administrador
	protected $_eventos_usuario_utilizados = array();	// Lista de eventos del usuario que estan activos
	protected $_eventos_usuario_utilizados_sobre_fila;	// Lista de eventos del administrador que se utilizaran
	protected $_botones_graficados_ad_hoc = array();		// Lista de botones que se imprimieron por orden del usuario
	protected $_grupo_eventos_activo = '';				// Define el grupo de eventos activos
	protected $_utilizar_impresion_html = false;			// Indica que hay agregar funcionalidad para imprimir
	protected $_prefijo = 'ei';
	protected $_modo_descripcion_tooltip = true;
	protected $_nombre_formulario;
	protected $_posicion_botonera;
	
	function __construct($definicion)
	{
		parent::__construct($definicion);
        $this->_submit = $this->_prefijo.'_'.$this->_id[1];
		$this->objeto_js = "js_".$this->_submit;
		$this->preparar_componente();
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
			foreach($this->_eventos_usuario_utilizados as $id => $evento ){
				if($evento->maneja_datos()){
					$val = apex_ei_evt_maneja_datos;
				}else{
					$val = apex_ei_evt_no_maneja_datos;	
				}
				$this->_memoria['eventos'][$id] = $val;
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
	}
	
	//--------------------------------------------------------------------
	//--  EVENTOS   ------------------------------------------------------
	//--------------------------------------------------------------------

	/**
	* Recupera el objeto asociado de un evento
	* @return toba_evento_usuario
	*/
	function evento($id)
	{
		if (isset($this->_eventos_usuario_utilizados[$id])) {
			return $this->_eventos_usuario_utilizados[$id];
		} else {
			if(isset($this->_eventos_usuario[$id])){
				throw new toba_error($this->get_txt(). " El EVENTO '$id' no esta asociado actualmente al componente.");
			} else {
				throw new toba_error($this->get_txt(). " El EVENTO '$id' no está definido.");
			}
		}
	}

	/**
	 * Determina que un evento definido va a formar parte de los eventos a mostrar en el servicio actual
	 */
	function agregar_evento($id)
	{
		if(isset($this->_eventos_usuario[ $id ])){
			$this->_eventos_usuario_utilizados[ $id ] = $this->_eventos_usuario[ $id ];
		} else {
			throw new toba_error($this->get_txt(). 
					" Se quiere agregar el EVENTO '$id', pero no está definido.");
		}		
	}

	/**
	 * Elimina un evento definido de la lista de eventos a utilizar en el servicio actual
	 */
	function eliminar_evento($id)
	{
		if(isset($this->_eventos_usuario[ $id ])){
			if(isset($this->_eventos_usuario_utilizados[ $id ])){
				unset($this->_eventos_usuario_utilizados[ $id ]);
				toba::logger()->debug("Se elimino el evento: $id", 'toba');
			}		
		} else {
			throw new toba_error($this->get_txt(). 
					" Se quiere eliminar el EVENTO '$id', pero no está definido.");
		}		
	}

	//--- Manejo interno --------------------------------------
	
	/**
	 * Carga la lista de eventos definidos desde el editor
	 * @ignore 
	 */
	protected function cargar_lista_eventos()
	{
		foreach ($this->_info_eventos as $info_evento) {
			$e = new toba_evento_usuario($info_evento);
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
	 * Reporta un evento en el componente controlador
	 * Puede recibir N parametros adicionales (ej <pre>$this->reportar_evento('modificacion', $datos, $fila,...)</pre>)
	 * @param string $evento Id. del evento a disparar
	 */
	protected function reportar_evento($evento)
	{
		if (isset($this->_id_en_controlador)) {
			$parametros = func_get_args();
			$parametros	= array_merge(array($this->_id_en_controlador), $parametros);
			return call_user_func_array( array($this->controlador, 'registrar_evento'), $parametros);
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

	//--- Manejo de grupos de eventos --------------------------------------
	
	/**
	 * Activa un grupo de eventos, excluyendo a aquellos eventos que no pertenecen al mismo
	 */
	function set_grupo_eventos_activo($grupo)
	{
		$this->_grupo_eventos_activo = $grupo;
	}
	
	/**
	 * Retorna el grupo de eventos activos
	 */
	function get_grupo_eventos_activo()
	{
		return $this->_grupo_eventos_activo;	
	}

	/**
	 * Dispara el filtrado de eventos en base a grupos
	 * @ignore 
	 */
	protected function filtrar_eventos()
	{
		$grupo = $this->get_grupo_eventos_activo();
		foreach($this->_eventos_usuario_utilizados as $id => $evento){
			if( $evento->posee_grupo_asociado() ){
				if(!isset($grupo)){ 
					//No hay un grupo activo, no lo muestro
					unset($this->_eventos_usuario_utilizados[$id]);
					toba::logger()->debug("Se filtro el evento: $id", 'toba');
				} else {
					if( !$evento->pertenece_a_grupo($grupo) ){
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
	 * Retorna true si alguno de los eventos definidos por el usuario se va a graficar en la botonera del componente
	 * @return boolean
	 */
	function hay_botones() 
	{
		foreach ($this->_eventos_usuario_utilizados as $evento) {	
			if ( $evento->esta_en_botonera() ) {
				return true;
			}
		}
		return false;
	}	

	/**
	 * Genera la botonera del componente
	 * @param string $clase Clase css con el que se muestra la botonera
	 */
	function generar_botones($clase = '')
	{
		//----------- Generacion
		if ($this->hay_botones()) {
			echo "<div class='ei-botonera $clase'>";
			$this->generar_botones_eventos();
			echo "</div>";
		}
	}	
	
	/**
	 * Genera los botones de todos los eventos marcados para aparecer en la botonera.
	 */
	protected function generar_botones_eventos()
	{
		foreach($this->_eventos_usuario_utilizados as $evento )	{
			if ( $evento->esta_en_botonera() ) {
				if( !in_array($evento->get_id(), $this->_botones_graficados_ad_hoc ) ) {
					$this->generar_html_boton($evento);
				}
			}
		}
	}

	/**
	 * Genera el html de un botón específico
	 * @param toba_evento_usuario $evento
	 */
	protected function generar_html_boton($evento)
	{
		//--- Link al editor
		if (toba_editor::modo_prueba()) {
			echo toba_editor::get_vinculo_evento($this->_id, $this->_info['clase_editor_item'], $evento->get_id())."\n";
		}
		//--- Utilidades de impresion
		if ( $evento->posee_accion_imprimir() ) {
			$this->_utilizar_impresion_html = true;					
		}
		if( ! $evento->esta_anulado() ) {
			echo $evento->get_html($this->_submit, $this->objeto_js, $this->_id);
		}
	}

	/**
	* Metodo para graficar un boton por orden del usuario
	* @param string $id_evento Id. del evento a generar el botón
	* @param boolean $excluir_botonera El botón no se incluye en la botonera predeterminada del componente
	*/
	function generar_boton($id_evento, $excluir_botonera=true)
	{
		$this->generar_html_boton($this->evento($id_evento));
		if($excluir_botonera) {
			$this->_botones_graficados_ad_hoc[] = $id_evento;
		}
	}

	//--------------------------------------------------------------------
	//--  PUNTOS DE CONTROL ----------------------------------------------
	//--------------------------------------------------------------------
  function tiene_puntos_control($evento)
  {
    return (count($this->get_puntos_control($evento)) > 0);
  }

  function get_puntos_control($evento)
  {
    $ret = array();
    for ($i=0; $i < count($this->_info_puntos_control); $i++)
      if ($this->_info_puntos_control[$i]['evento'] == $evento || $evento == '')
        $ret[] = $this->_info_puntos_control[$i]['pto_control'];

    return $ret;
  }


	//--------------------------------------------------------------------
	//--  INTERFACE GRAFICA   --------------------------------------------
	//--------------------------------------------------------------------

	/**
	 * Fuerza a que el componente se grafique colpsado, pudiendo el usuario descolapsarlo posteriormente
	 */
	function colapsar()
	{
		$this->_colapsado = true;
		$this->_info['colapsable'] = true;
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
	 */
	function set_titulo($titulo)
	{
		$this->_info['titulo'] = $titulo;
	}
	
	/**
	 * Cambia la descripción del componente para el servicio actual
	 */	
	function set_descripcion($desc)
	{
		$this->_info["descripcion"] = $desc;
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
		$botonera_sup = $this->hay_botones() && isset($this->_posicion_botonera) && ($this->_posicion_botonera == "arriba" ||
				 $this->_posicion_botonera == "ambos");
		$tiene_titulo = trim($this->_info["titulo"])!="" || trim($titulo) != '';
		if ($botonera_sup || !$control_titulo_vacio || $tiene_titulo) {
			if (!isset($titulo)) {
				$titulo = $this->_info["titulo"];	
			}
			if ($botonera_sup && !$tiene_titulo) {
				$estilo .= ' ei-barra-sup-sin-tit';
			}
			if (!$botonera_sup && $tiene_titulo) {
				$estilo .= ' ei-barra-sup-sin-botonera';
			}
			//ei_barra_inicio("ei-barra-sup $estilo");
			echo "<div class='ei-barra-sup $estilo'>\n";
			//--> Botonera
			if ($botonera_sup) {
				$this->generar_botones();
			}						
			//---Barra de mensajeria		
			if (isset($this->objeto_js)) {
				echo "<a  class='ei-barra-mensajeria' id='barra_{$this->objeto_js}' style='display:none' href='#' onclick='notificacion.mostrar({$this->objeto_js})'>";
				echo toba_recurso::imagen_toba('warning.gif', true, null, null, 'Muestra las notificaciones encontradas durante la última operación.');
				echo "</a>";
			}
			//--- Descripcion Tooltip
			if(trim($this->_info["descripcion"])!="" &&  $this->_modo_descripcion_tooltip){
				echo '<span class="ei-barra-sup-desc">';
				echo toba_recurso::imagen_toba("descripcion.gif",true,null,null, $this->_info["descripcion"]);
				echo '</span>';
			}
	
			//---Barra de colapsado
			$colapsado = "";
			if ($this->_info['colapsable'] && isset($this->objeto_js)) {
				$colapsado = "style='cursor: pointer; cursor: hand;' onclick=\"{$this->objeto_js}.cambiar_colapsado();\" title='Mostrar / Ocultar'";
				$img_min = toba_recurso::imagen_toba('nucleo/sentido_asc_sel.gif', false);
				echo "<img class='ei-barra-colapsar' id='colapsar_boton_{$this->objeto_js}' src='$img_min' $colapsado>";
			}

			//---Titulo			
			echo "<span class='ei-barra-sup-tit' $colapsado>$titulo</span>\n";
			echo "</div>";
			//echo ei_barra_fin();
		}
		
		//--- Descripcion con barra
		if(trim($this->_info["descripcion"])!="" &&  !$this->_modo_descripcion_tooltip){
				//--- Muestra una barra en lugar de un tooltip	
			$imagen = toba_recurso::imagen_toba("info_chico.gif",true);
			$descripcion = toba_parser_ayuda::parsear($this->_info["descripcion"]);
			echo "<div class='ei-barra-sup-desc-barra'>$imagen&nbsp;$descripcion</div>\n";
		}				
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
		if( toba_editor::modo_prueba() ){ 
			$salida .= "<div class='div-editor'>";
			$salida .= toba_editor::generar_zona_vinculos_componente($this->_id, $this->_info['clase_editor_item'], $this->_info['clase'],
										$this->_info['subclase'] != '');
			$salida .= '<strong>&nbsp;[' .$this->_info['objeto'] . ']&nbsp;</strong>' . $this->_info["nombre"];
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

	/**
	 * Termina la construcción del objeto javscript asociado al componente
	 * @ignore 
	 */
	protected function iniciar_objeto_js()
	{
		$identado = toba_js::instancia()->identado();
		//-- EVENTO implicito --
		if(is_object($this->_evento_implicito)){
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
	 */
	function vista_impresion( toba_impresion $salida )
	{
		if ( $salida instanceof toba_impr_html ) {
			$this->vista_impresion_html( $salida );	
		}
	}

	/**
	 * Impresion HTML por defecto
	 */
	function vista_impresion_html( $salida )
	{
		$salida->titulo( $this->get_nombre() );
	}
}
?>