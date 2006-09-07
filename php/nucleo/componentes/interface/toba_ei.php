<?
require_once("nucleo/componentes/toba_componente.php");
require_once('nucleo/lib/salidas/toba_impresion.php');
require_once('nucleo/lib/toba_evento_usuario.php');
require_once('nucleo/lib/toba_vinculo.php');
define('apex_ei_analisis_fila', 'apex_ei_analisis_fila');   //Id de la columna utilizada para el resultado del analisis de una fila
define("apex_ei_evento","evt");
define("apex_ei_separador","__");
define("apex_db_registros_clave","x_dbr_clave");			//Clave interna de los DB_REGISTROS
define("apex_datos_clave_fila","x_dbr_clave");				//Clave interna de los datos_tabla, por compatibilidad es igual.
define('apex_ei_evt_sin_rpta', 'apex_ei_evt_sin_rpta');
define('apex_ei_evt_maneja_datos', 1);
define('apex_ei_evt_no_maneja_datos', -1);

/**
 * Clase base de los componentes o elementos de interface (ei)
 * @package Componentes
 * @subpackage Eis
 */
abstract class toba_ei extends toba_componente
{
 	protected $submit;	
 	protected $objeto_js;
	protected $info_eventos;
	protected $colapsado = false;						// El elemento sólo mantiene su título
	protected $evento_implicito=null;					// Evento disparado cuando no hay una orden explicita
	protected $eventos = array();						// Eventos INTERNOS del componente
	protected $eventos_usuario = array();				// Eventos declarados en el administrador
	protected $eventos_usuario_utilizados = array();	// Lista de eventos del usuario que estan activos
	protected $eventos_usuario_utilizados_sobre_fila;	// Lista de eventos del administrador que se utilizaran
	protected $botones_graficados_ad_hoc = array();		// Lista de botones que se imprimieron por orden del usuario
	protected $grupo_eventos_activo = '';				// Define el grupo de eventos activos
	protected $utilizar_impresion_html = false;			// Indica que hay agregar funcionalidad para imprimir
	protected $prefijo = 'ei';
	
	function __construct($definicion)
	{
		parent::__construct($definicion);
        $this->submit = $this->prefijo.'_'.$this->id[1];
		$this->objeto_js = "js_".$this->submit;
		$this->preparar_componente();
	}
	
	function preparar_componente()
	{
		$this->cargar_lista_eventos();	
	}
	
	/**
	 * Destructor del objeto
	 */
	function destruir()
	{
		$this->memoria['eventos'] = array();
		if(isset($this->eventos)){
			foreach($this->eventos as $id => $evento ){
				$this->memoria['eventos'][$id] = apex_ei_evt_maneja_datos;
			}
		}
		if(isset($this->eventos_usuario_utilizados)){
			foreach($this->eventos_usuario_utilizados as $id => $evento ){
				if($evento->maneja_datos()){
					$val = apex_ei_evt_maneja_datos;
				}else{
					$val = apex_ei_evt_no_maneja_datos;	
				}
				$this->memoria['eventos'][$id] = $val;
			}
		}
		parent::destruir();
	}	
	
	/**
	 * Ventana previa a la configuración del componente
	 */
	function pre_configurar()
	{
	}

	/**
	 * Ventana posterior a la configuración del componente
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
		if (isset($this->eventos_usuario_utilizados[$id])) {
			return $this->eventos_usuario_utilizados[$id];
		} else {
			if(isset($this->eventos_usuario[$id])){
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
		if(isset($this->eventos_usuario[ $id ])){
			$this->eventos_usuario_utilizados[ $id ] = $this->eventos_usuario[ $id ];
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
		if(isset($this->eventos_usuario[ $id ])){
			if(isset($this->eventos_usuario_utilizados[ $id ])){
				unset($this->eventos_usuario_utilizados[ $id ]);
				toba::logger()->debug("Se elimino el evento: $id", 'toba');
			}		
		} else {
			throw new toba_error($this->get_txt(). 
					" Se quiere eliminar el EVENTO '$id', pero no está definido.");
		}		
	}

	//--- Manejo interno --------------------------------------
	
	/*
	*	Carga la lista de eventos definidos desde el editor
	*/		
	protected function cargar_lista_eventos()
	{
		foreach ($this->info_eventos as $info_evento) {
			$e = new toba_evento_usuario($info_evento);
			$this->eventos_usuario[ $e->get_id() ] = $e;				//Lista de eventos
			$this->eventos_usuario_utilizados[ $e->get_id() ] = $e;		//Lista de utilizados
			if( $e->es_implicito() ){
				toba::logger()->debug($this->get_txt() . " IMPLICITO: " . $e->get_id(), 'toba');
				$this->evento_implicito = $e;
			}
		}
	}

	/**
	 * Retorna la lista de eventos que fueron definidos a nivel de fila
	 * @return array(id => toba_evento_usuario)
	 */
	function get_eventos_sobre_fila()
	{
		if(!isset($this->eventos_usuario_utilizados_sobre_fila)){
			$this->eventos_usuario_utilizados_sobre_fila = array();
			foreach ($this->eventos_usuario_utilizados as $id => $evento) {
				if ($evento->esta_sobre_fila()) {
					$this->eventos_usuario_utilizados_sobre_fila[$id]=$evento;
				}
			}
		}
		//ei_arbol($this->eventos_usuario_utilizados_sobre_fila,'pepe');
		return $this->eventos_usuario_utilizados_sobre_fila;
	}
	
	function cant_eventos_sobre_fila()
	{
		return count( $this->get_eventos_sobre_fila() );
	}

	/**
	 * Inicia la etapa de eventos en este componente
	 */
	function disparar_eventos(){}
		
	/**
	 * Reporto un evento en mi controlador
	 */
	protected function reportar_evento($evento)
	{
		$parametros = func_get_args();
		$parametros	= array_merge(array($this->id_en_controlador), $parametros);
		return call_user_func_array( array($this->controlador, 'registrar_evento'), $parametros);
	}

	/**
	 * Retorna todos los eventos definidos por el usuario, excluyendo los internos del componente
	 * @return array(toba_evento_usuario)
	 */
	function get_lista_eventos_usuario()
	{
		return $this->eventos_usuario_utilizados;
	}

	/**
	 * Retorna todos los eventos definidos por el componente (llamados internos), excluyendo los definidos por el usuario
	 * @return array(toba_evento_usuario)
	 */	
	function get_lista_eventos_internos()
	{
		return $this->eventos;
	}

	//--- Manejo de grupos de eventos --------------------------------------
	
	/**
	 * Activa un grupo de eventos, excluyendo a aquellos eventos que no pertenecen al mismo
	 */
	function set_grupo_eventos_activo($grupo)
	{
		$this->grupo_eventos_activo = $grupo;
	}
	
	/**
	 * Retorna el grupo de eventos activos
	 */
	function get_grupo_eventos_activo()
	{
		return $this->grupo_eventos_activo;	
	}

	/**
	 * Dispara el filtrado de eventos en base a grupos
	 */
	protected function filtrar_eventos()
	{
		$grupo = $this->get_grupo_eventos_activo();
		foreach($this->eventos_usuario_utilizados as $id => $evento){
			if( $evento->posee_grupo_asociado() ){
				if(!isset($grupo)){ 
					//No hay un grupo activo, no lo muestro
					unset($this->eventos_usuario_utilizados[$id]);
					toba::logger()->debug("Se filtro el evento: $id", 'toba');
				} else {
					if( !$evento->pertenece_a_grupo($grupo) ){
						//El evento no pertenece al grupo
						unset($this->eventos_usuario_utilizados[$id]);
						toba::logger()->debug("Se filtro el evento: $id", 'toba');
					}
				}
			}
		}
	}

	//--- BOTONES -------------------------------------------------

	/**
	 * Retorna true si alguno de los eventos definidos por el usuario se va a graficar en la botonera del componente
	 */
	function hay_botones() 
	{
		foreach ($this->eventos_usuario_utilizados as $evento) {	
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
		echo "<div class='ei-botonera $clase'>";
		$this->generar_botones_eventos();
		echo "</div>";
	}	
	
	/**
	 * Genera los botones de todos los eventos marcardos para aparecer en la botonera.
	 */
	protected function generar_botones_eventos()
	{
		foreach($this->eventos_usuario_utilizados as $evento )	{
			if ( $evento->esta_en_botonera() ) {
				if( !in_array($evento->get_id(), $this->botones_graficados_ad_hoc ) ) {
					$this->generar_html_boton($evento);
				}
			}
		}
	}

	protected function generar_html_boton($evento)
	{
		//--- Link al editor
		if (toba_editor::modo_prueba()) {
			echo toba_editor::get_vinculo_evento($this->id, $this->info['clase_editor_item'], $evento->get_id())."\n";
		}
		//--- Utilidades de impresion
		if ( $evento->posee_accion_imprimir() ) {
			$this->utilizar_impresion_html = true;					
		}
		if( $evento->esta_activado() ) {
			echo $evento->get_html($this->submit, $this->objeto_js);
		}
	}

	/**
	*	Metodo para graficar un boton por orden del usuario
	*/
	function generar_boton($id_evento, $excluir_botonera=true)
	{
		$this->generar_html_boton($this->evento($id_evento));
		if($excluir_botonera) {
			$this->botones_graficados_ad_hoc[] = $id_evento;
		}
	}

	//--------------------------------------------------------------------
	//--  INTERFACE GRAFICA   --------------------------------------------
	//--------------------------------------------------------------------

	/**
	 * Fuerza a que el componente se grafique colpsado, pudiendo el usuario descolapsarlo posteriormente
	 */
	function colapsar()
	{
		$this->colapsado = true;
		$this->info['colapsable'] = true;
	}
	
	/**
	 * Determina si el componente podra ser colapsado/descolapsado por el usuario
	 * @param boolean $colapsable Si o no se permite colapsar
	 */
	function set_colapsable($colapsable)
	{
		$this->info['colapsable'] = $colapsable;
	}

	/**
	 * Cambia el titulo del componente para el servicio actual
	 */
	function set_titulo($titulo)
	{
		$this->info['titulo'] = $titulo;
	}
		
	function generar_html_barra_sup_especifica()
	{
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
		if($control_titulo_vacio){
			if(trim($this->info["titulo"])=="" && trim($titulo) == ''){
				return;	
			}
		}
		if (!isset($titulo)) {
			$titulo = $this->info["titulo"];	
		}
		echo "<div class='ei-barra-sup $estilo'>";
		//---ICONOS
		echo '<span class="ei-barra-sup-iconos">';		
		if( toba_editor::modo_prueba() ){ 
			toba_editor::generar_zona_vinculos_componente($this->id, $this->info['clase_editor_item']);
		}		
		echo $this->generar_html_barra_sup_especifica();
		echo '</span>';
		
		//---Barra de mensajeria		
		if (isset($this->objeto_js)) {
			echo "<a  class='ei-barra-mensajeria' id='barra_{$this->objeto_js}' style='display:none' href='#' onclick='notificacion.mostrar({$this->objeto_js})'>";
			echo toba_recurso::imagen_apl('warning.gif', true, null, null, 'Muestra las notificaciones encontradas durante la última operación.');
			echo "</a>";
		}

		//--- Descripcion	
		if(trim($this->info["descripcion"])!=""){
			echo '<span class="ei-barra-sup-desc">';
			echo toba_recurso::imagen_apl("descripcion.gif",true,null,null,$this->info["descripcion"]);
			echo '</span>';
		}		
		
		//---Barra de colapsado
		$colapsado = "";
		if ($this->info['colapsable'] && isset($this->objeto_js)) {
			$colapsado = "style='cursor: pointer; cursor: hand;' onclick=\"{$this->objeto_js}.cambiar_colapsado();\" title='Mostrar / Ocultar'";
			$img_min = toba_recurso::imagen_apl('sentido_asc_sel.gif', false);
			echo "<img class='ei-barra-colapsar' id='colapsar_boton_{$this->objeto_js}' src='$img_min' $colapsado>";
		}

		//---Titulo
		echo "<span class='ei-barra-sup-tit' $colapsado>$titulo</span>\n";
		echo "</div>";
	}
	
	function get_id_form()
	{
		return $this->submit;	
	}	
	
	//-----------------------------------------
	//--  JAVASCRIPT --------------------------
	//-----------------------------------------
	
	function get_consumo_javascript()
	{
		return array('componentes/ei');
	}
	
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
	 */
	function get_id_objeto_js()
	{
		return $this->objeto_js;
	}
	
	protected function crear_objeto_js()
	{
		$identado = toba_js::instancia()->identado();
		echo $identado."window.{$this->objeto_js} = new ei('{$this->objeto_js}','{$this->submit}');\n";
	}
	
	protected function extender_objeto_js()
	{}
	
	protected function iniciar_objeto_js()
	{
		$identado = toba_js::instancia()->identado();
		//-- EVENTO implicito --
		if(is_object($this->evento_implicito)){
			$evento_js = $this->evento_implicito->get_evt_javascript();
			echo toba_js::instancia()->identado()."{$this->objeto_js}.set_evento_implicito($evento_js);\n";
		}
		if ($this->colapsado) {
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