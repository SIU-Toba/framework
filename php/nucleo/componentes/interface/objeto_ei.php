<?
require_once("nucleo/componentes/objeto.php");
require_once('nucleo/lib/salidas/impresion_toba.php');
require_once('nucleo/lib/toba_evento_usuario.php');
require_once('nucleo/lib/toba_vinculo.php');
require_once('eventos.php');
define('apex_ei_analisis_fila', 'apex_ei_analisis_fila');   //Id de la columna utilizada para el resultado del analisis de una fila
define("apex_ei_evento","evt");
define("apex_ei_separador","__");
define("apex_db_registros_clave","x_dbr_clave");			//Clave interna de los DB_REGISTROS
define("apex_datos_clave_fila","x_dbr_clave");				//Clave interna de los datos_tabla, por compatibilidad es igual.
define('apex_ei_evt_sin_rpta', 'apex_ei_evt_sin_rpta');

/**
 * Clase base de los elementos de interface (ei)
 * @package Objetos
 * @subpackage Ei
 */
abstract class objeto_ei extends objeto
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
	}
	
	function destruir()
	{
		//Recuerdo los eventos enviados durante los servicios
		if(!isset($this->memoria['eventos'])){
			$this->memoria['eventos'] = array();
		}
		if(isset($this->eventos)){
			foreach($this->eventos as $id => $evento ){
				$this->memoria['eventos'][$id] = true;
			}
		}
		if(isset($this->eventos_usuario_utilizados)){
			foreach($this->eventos_usuario_utilizados as $id => $evento ){
				if($evento->maneja_datos()){
					$val = true;
				}else{
					$val = false;	
				}
				$this->memoria['eventos'][$id] = $val;
			}
		}
		parent::destruir();
	}	
	
	function pre_configurar()
	{
		$this->cargar_lista_eventos();
	}
	
	function post_configurar()
	{
		$this->filtrar_eventos();
	}
	
	//--------------------------------------------------------------------
	//--  EVENTOS   ------------------------------------------------------
	//--------------------------------------------------------------------

	/**
	*	Recupera un evento
	*/
	function evento($id)
	{
		if (isset($this->eventos_usuario_utilizados[$id])) {
			return $this->eventos_usuario_utilizados[$id];
		} else {
			if(isset($this->eventos_usuario[$id])){
				throw new excepcion_toba($this->get_txt(). " El EVENTO '$id' no esta ASOCIADO actualmente al componente.");
			} else {
				throw new excepcion_toba($this->get_txt(). " El EVENTO '$id' no está definido.");
			}
		}
	}

	function agregar_evento($id)
	{
		if(isset($this->eventos_usuario[ $id ])){
			$this->eventos_usuario_utilizados[ $id ] = $this->eventos_usuario[ $id ];
		} else {
			throw new excepcion_toba($this->get_txt(). 
					" Se quiere agregar el EVENTO '$id', pero no está definido.");
		}		
	}

	function eliminar_evento($id)
	{
		if(isset($this->eventos_usuario[ $id ])){
			if(isset($this->eventos_usuario_utilizados[ $id ])){
				unset($this->eventos_usuario_utilizados[ $id ]);
				toba::get_logger()->debug("Se elimino el evento: $id", 'toba');
			}		
		} else {
			throw new excepcion_toba($this->get_txt(). 
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
				toba::get_logger()->debug($this->get_txt() . " IMPLICITO: " . $e->get_id(), 'toba');
				$this->evento_implicito = $e;
			}
		}
	}

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

	function disparar_eventos(){}
		
	/**
	 * Reporto un evento en mi controlador
	 */
	protected function reportar_evento($evento)
	{
		if(!isset( $this->memoria['eventos'][$evento] )){
			throw new excepcion_toba('ERROR EI: Se recibio el EVENTO ['.$evento.']. El mismo no fue enviado en el servicio anterior');	
		}
		$parametros = func_get_args();
		$parametros	= array_merge(array($this->id_en_controlador), $parametros);
		return call_user_func_array( array($this->controlador, 'registrar_evento'), $parametros);
	}

	function get_lista_eventos_usuario()
	{
		return $this->eventos_usuario_utilizados;
	}

	function get_lista_eventos_internos()
	{
		return $this->eventos;
	}

	//--- Manejo de grupos de eventos --------------------------------------
	
	/**
		Activa un grupo de eventos
	*/
	function set_grupo_eventos_activo($grupo)
	{
		$this->grupo_eventos_activo = $grupo;
	}
	
	/**
		Devuelve el grupo de eventos activos
	*/
	function get_grupo_eventos_activo()
	{
		return $this->grupo_eventos_activo;	
	}
		
	protected function filtrar_eventos()
	{
		$grupo = $this->get_grupo_eventos_activo();
		if(trim($grupo)!=''){ 
			foreach($this->eventos_usuario_utilizados as $id => $evento){
				if( $evento->posee_grupo_asociado() ){
					if( !$evento->pertenece_a_grupo($grupo) ){
						unset($this->eventos_usuario_utilizados[$id]);
						toba::get_logger()->debug("Se filtro el evento: $id", 'toba');
					}
				}
			}
		}		
	}

	//--- BOTONES -------------------------------------------------
	
	function hay_botones() 
	{
		foreach ($this->eventos_usuario_utilizados as $evento) {	
			if ( $evento->esta_en_botonera() ) {
				return true;
			}
		}
		return false;
	}	
	
	function generar_botones($clase = '')
	{
		//----------- Generacion
		echo "<div class='ei-botonera $clase'>";
		$this->generar_botones_eventos();
		echo "</div>";
	}	
	
	/*
		Genera los botones de todos los eventos marcardos para aparecer en la botonera.
	*/
	protected function generar_botones_eventos()
	{
		//--- Si el componente no reservo tabs, se reservan ahora
		if (!isset($this->rango_tabs)) {
			//$this->rango_tabs = manejador_tabs::instancia()->reservar(count($this->eventos));			
		}
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
		if (editor::modo_prueba()) {
			echo editor::get_vinculo_evento($this->id, $this->info['clase_editor_item'], $evento->get_id())."\n";
		}
		//--- Utilidades de impresion
		if ( $evento->posee_accion_imprimir() ) {
			$this->utilizar_impresion_html = true;					
		}
		$evento->generar_boton($this->submit, $this->objeto_js);
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

	public function colapsar()
	{
		$this->colapsado = true;
		$this->info['colapsable'] = true;
	}
	
	public function set_colapsable($colapsable)
	{
		$this->info['colapsable'] = $colapsable;
	}
	
	public function get_consumo_javascript()
	{
		return array('componentes/ei');
	}
	
	function generar_js()
	{
		$identado = js::instancia()->identado();
		echo "\n$identado//---------------- CREANDO OBJETO {$this->objeto_js} --------------  \n";
		$this->crear_objeto_js();
		$this->extender_objeto_js();
		echo "\n";
		$this->iniciar_objeto_js();
		echo "$identado//-----------------------------------------------------------------  \n";		
		return $this->objeto_js;
	}

	function get_id_objeto_js()
	{
		return $this->objeto_js;
	}
	
	protected function crear_objeto_js()
	{
		$identado = js::instancia()->identado();
		echo $identado."window.{$this->objeto_js} = new ei('{$this->objeto_js}');\n";
	}
	
	protected function extender_objeto_js()
	{
		
	}
	
	protected function iniciar_objeto_js()
	{
		$identado = js::instancia()->identado();
		//-- EVENTO implicito --
		if(is_object($this->evento_implicito)){
			$evento_js = $this->evento_implicito->get_evt_javascript();
			echo js::instancia()->identado()."{$this->objeto_js}.set_evento_implicito($evento_js);\n";
		}
		if ($this->colapsado) {
			echo $identado."window.{$this->objeto_js}.colapsar();\n";
		}
		//Se agrega al objeto al singleton toba
		echo $identado."toba.agregar_objeto(window.{$this->objeto_js});\n";		
	}

	function set_titulo($titulo)
	{
		$this->info['titulo'] = $titulo;
	}
		
	function barra_superior_especifica()
	{
	}

	function barra_superior($titulo=null, $control_titulo_vacio=false, $estilo="")
	{
		//Marco la existencia de una interface previa
		if($control_titulo_vacio){
			if(trim($this->info["titulo"])==""){
				return;	
			}
		}
		if (!isset($titulo)) {
			$titulo = $this->info["titulo"];	
		}
		echo "<div class='ei-barra-sup $estilo'>";
		//---ICONOS
		echo '<span class="ei-barra-sup-iconos">';		
		if( editor::modo_prueba() ){ 
			editor::generar_zona_vinculos_componente($this->id, $this->info['clase_editor_item']);
		}		
		echo $this->barra_superior_especifica();
		echo '</span>';
		
		//---Barra de mensajeria		
		if (isset($this->objeto_js)) {
			echo "<a  class='ei-barra-mensajeria' id='barra_{$this->objeto_js}' style='display:none' href='#' onclick='cola_mensajes.mostrar({$this->objeto_js})'>";
			echo recurso::imagen_apl('warning.gif', true, null, null, 'Muestra las notificaciones encontradas durante la última operación.');
			echo "</a>";
		}

		//--- Descripcion	
		if(trim($this->info["descripcion"])!=""){
			echo '<span class="ei-barra-sup-desc">';
			echo recurso::imagen_apl("descripcion.gif",true,null,null,$this->info["descripcion"]);
			echo '</span>';
		}		
		
		//---Barra de colapsado
		$colapsado = "";
		if ($this->info['colapsable'] && isset($this->objeto_js)) {
			$colapsado = "style='cursor: pointer; cursor: hand;' onclick=\"{$this->objeto_js}.cambiar_colapsado();\" title='Mostrar / Ocultar'";
			$img_min = recurso::imagen_apl('sentido_asc_sel.gif', false);
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
	
	//---------------------------------------------------------------
	//----------------------  SALIDA Impresion  ---------------------
	//---------------------------------------------------------------

	/*
	*	Despachador de tipos de salidas de impresion
	*/
	function vista_impresion( impresion_toba $salida )
	{
		if ( $salida instanceof html_impr ) {
			$this->vista_impresion_html( $salida );	
		}
	}

	/*
	*	Impresion HTML por defecto
	*/
	function vista_impresion_html( $salida )
	{
		$salida->titulo( $this->get_nombre() );
	}
}
?>