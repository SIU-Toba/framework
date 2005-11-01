<?
require_once("objeto.php");
require_once('eventos.php');
define('apex_ei_analisis_fila', 'apex_ei_analisis_fila');   //Id de la columna utilizada para el resultado del analisis de una fila
define("apex_ei_evento","evt");
define("apex_ei_separador","__");
define("apex_db_registros_clave","x_dbr_clave");			//Clave interna de los DB_REGISTROS
define("apex_datos_clave_fila","x_dbr_clave");				//Clave interna de los datos_tabla, por compatibilidad es igual.

class objeto_ei extends objeto
{
	protected $controlador;
	protected $info_eventos;
	protected $colapsado = false;						//El elemento sólo mantiene su título
	protected $evento_por_defecto;						//Evento disparado cuando no hay una orden explicita
	protected $eventos = array();
	protected $grupo_eventos_activo = '';				// Define el grupo de eventos activos

	function obtener_definicion_db()
	/*
	*	Se redefine para incluir los eventos en la SQL de carga
	*/
	{
		$sql = parent::obtener_definicion_db();
		$sql["info_eventos"]["sql"] = "SELECT		identificador			as identificador,
													etiqueta				as etiqueta,
													maneja_datos			as maneja_datos,
													sobre_fila				as sobre_fila,
													confirmacion			as confirmacion,
													estilo					as estilo,
													imagen_recurso_origen	as imagen_recurso_origen,
													imagen					as imagen,
													en_botonera				as en_botonera,
													ayuda					as ayuda,
													ci_predep				as ci_predep,				
													implicito				as implicito,					
													grupo					as grupo
										FROM	apex_objeto_eventos
										WHERE	proyecto='".$this->id[0]."'
										AND	objeto = '".$this->id[1]."'
										ORDER BY orden;";
		$sql["info_eventos"]["tipo"]="x";
		$sql["info_eventos"]["estricto"]="0";
		return $sql;
	}
	
	function destruir()
	{
		$this->memoria["eventos"] = array();
		if(isset($this->eventos)){
			foreach($this->eventos as $id => $evento ){
				if(isset($evento['maneja_datos'])){
					$val = $evento['maneja_datos'];
				}else{
					$val = true;	
				}
				$this->memoria["eventos"][$id] = $val;
			}
		}
		parent::destruir();
	}	
	
	function inicializar($parametros)
	{
		$this->id_en_padre = $parametros['id'];		
	}

	function cargar_datos(){}

	//--------------------------------------------------------------------
	//--  EVENTOS   ------------------------------------------------------
	//--------------------------------------------------------------------

	protected function disparar_eventos(){}

	public function agregar_controlador($controlador)
	{
		$this->controlador = $controlador;
	}

	protected function reportar_evento($evento)
	//Registro un evento en todos mis controladores
	{
		$parametros = func_get_args();
		$parametros	= array_merge(array($this->id_en_padre), $parametros);
		return call_user_func_array( array($this->controlador, 'registrar_evento'), $parametros);
		//$this->controladores[$id]->registrar_evento( $this->id_en_padre, $evento, $parametros );			
	}

	public function definir_eventos()
	{
		$this->eventos = $this->get_lista_eventos();
	}
		
	public function set_eventos($eventos)
	{
		$this->eventos = $eventos;
	}
	
	public function get_lista_eventos()
	{
		$eventos = $this->get_lista_eventos_definidos();
		$grupo = $this->get_grupo_eventos();
		if(trim($grupo)!=''){ //Si hay un grupo de eventos definido, filtro...
			foreach(array_keys($eventos) as $id){
				if($eventos[$id]['grupo']){
					unset($eventos[$id]);	
				}
			}
		}
		return $eventos;
	}
	
	protected function get_lista_eventos_definidos()
	/*
	*	Obtiene la lista de eventos definidos desde el administrador 
	*/
	{
		$eventos = array();
		foreach ($this->info_eventos as $evento) {
			$eventos[$evento['identificador']] = $evento;
		}
		return $eventos;
	}
	
	function agregar_evento($evento, $establecer_como_predeterminado=false)
	{
		asercion::es_array_dimension($evento,1);
		$this->eventos = array_merge($this->eventos, $evento);
		if($establecer_como_predeterminado){
			$id = key($evento);
			$this->set_evento_defecto($id);
		}
	}
	
	public function set_evento_defecto($id)
	{
		$this->evento_por_defecto = $id;
	}

	function cant_eventos_sobre_fila()
	{
		$cant = 0;
		foreach ($this->eventos as $evento) {
			if ($evento['sobre_fila'])
				$cant++;
		}
		return $cant;
	}
	
	function hay_botones() 
	{
		foreach($this->eventos as $id => $evento ) {	
			if (!isset($evento['en_botonera']) || $evento['en_botonera']) {
				return true;
			}
		}
		return false;
	}	
	
	function obtener_botones()
	{
		//----------- Generacion
		echo "<table class='tabla-0' align='center' width='100%'>\n";
		echo "<tr><td align='right'>";
		$this->obtener_botones_eventos();
		echo "</td></tr>\n";
		echo "</table>\n";
	}	
	
	/*
		Genera los botones de todos los eventos marcardos para aparecer en la botonera.
	*/
	function obtener_botones_eventos()
	{
		foreach(array_keys($this->eventos) as $id )
		{
			if (!isset($this->eventos[$id]['en_botonera']) || $this->eventos[$id]['en_botonera']) {
				$this->generar_boton_evento($id);
			}
		}
	}

	/*
		Genera el HTML del BOTON correspondiente a un evento definido
	*/
	function generar_boton_evento($id)
	{
		if(!isset($this->eventos[$id])){
			throw new excepcion_toba("Se solicito la generacion de un boton sobre un evento inexistente: '$id'");
		}
		$tip = '';
		if (isset($this->eventos[$id]['ayuda']))
			$tip = $this->eventos[$id]['ayuda'];
		$clase = ( isset($this->eventos[$id]['estilo']) && (trim( $this->eventos[$id]['estilo'] ) != "")) ? $this->eventos[$id]['estilo'] : "abm-input";
		$tab_order = 0;//Esto esta MAAL!!!
		$acceso = tecla_acceso( $this->eventos[$id]["etiqueta"] );
		$html = '';
		if (isset($this->eventos[$id]['imagen']) && $this->eventos[$id]['imagen']) {
			if (isset($this->eventos[$id]['imagen_recurso_origen']))
				$img = recurso::imagen_de_origen($this->eventos[$id]['imagen'], $this->eventos[$id]['imagen_recurso_origen']);
			else
				$img = $this->eventos[$id]['imagen'];
			$html = recurso::imagen($img, null, null, null, null, null, 'vertical-align: middle;').' ';
		}
		$html .= $acceso[0];
		$tecla = $acceso[1];
		$evento_js = eventos::a_javascript($id, $this->eventos[$id]);
		$js = "onclick=\"{$this->objeto_js}.set_evento($evento_js);\"";
		echo "&nbsp;" . form::button_html( $this->submit."_".$id, $html, $js, $tab_order, $tecla, $tip, 'button', '', $clase);
	}

	//--- Manejo de grupos de eventos
	
	/**
		Activa un grupo de eventos
	*/
	function set_grupo_eventos($grupo)
	{
		$this->grupo_eventos_activo = $grupo;
	}
	
	/**
		Devuelve el grupo de eventos activos
	*/
	function get_grupo_eventos()
	{
		return $this->grupo_eventos_activo;	
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
	
	public function consumo_javascript_global()
	{
		return array('clases/objeto');
	}
	
	protected function obtener_javascript()
/*
	@@acceso: Actividad
	@@desc: Construye la clase javascript asociada al objeto
*/
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
	
	protected function crear_objeto_js()
	{
		$identado = js::instancia()->identado();
		echo $identado."var {$this->objeto_js} = new objeto('{$this->objeto_js}');\n";
	}
	
	protected function extender_objeto_js()
	{

	}
	
	protected function iniciar_objeto_js()
	{
		$identado = js::instancia()->identado();
		//-- EVENTO por DEFECTO --
		if($this->evento_por_defecto != null && isset($this->eventos[$this->evento_por_defecto])){
			$evento = eventos::a_javascript($this->evento_por_defecto, $this->eventos[$this->evento_por_defecto]);
			echo js::instancia()->identado()."{$this->objeto_js}.set_evento_defecto($evento);\n";
		}
		if ($this->colapsado) {
			echo $identado."{$this->objeto_js}.colapsar();\n";
		}
		echo $identado."{$this->objeto_js}.iniciar();\n";
		//Se agrega al objeto al singleton toba
		echo $identado."toba.agregar_objeto({$this->objeto_js});\n";		
	}
}
?>