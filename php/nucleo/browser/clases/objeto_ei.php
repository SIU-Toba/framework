<?
require_once("objeto.php");
require_once('eventos.php');
define('apex_ei_analisis_fila', 'apex_ei_analisis_fila');   //Id de la columna utilizada para el resultado del analisis de una fila
define("apex_ei_evento","evt");
define("apex_ei_separador","__");
define("apex_db_registros_clave","x_dbr_clave");			//Clave interna de los DB_REGISTROS


class objeto_ei extends objeto
{
	protected $controlador;
	protected $info_eventos;

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
													ayuda					as ayuda
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
	
	//--------------------------------------------------------------------
	//--  EVENTOS   ------------------------------------------------------
	//--------------------------------------------------------------------

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
		return $this->get_lista_eventos_definidos();
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
	
	function obtener_botones_eventos()
	{
		foreach($this->eventos as $id => $evento )
		{
			if (!isset($evento['en_botonera']) || $evento['en_botonera']) {
				$tip = '';
				if (isset($evento['ayuda']))
					$tip = $evento['ayuda'];
				$clase = ( isset($evento['estilo']) && (trim( $evento['estilo'] ) != "")) ? $evento['estilo'] : "abm-input";
				$tab_order = 0;//Esto esta MAAL!!!
				$acceso = tecla_acceso( $evento["etiqueta"] );
				$html = '';
				if (isset($evento['imagen']) && $evento['imagen']) {
					if (isset($evento['imagen_recurso_origen']))
						$img = recurso::imagen_de_origen($evento['imagen'], $evento['imagen_recurso_origen']);
					else
						$img = $evento['imagen'];
					$html = recurso::imagen($img, null, null, null, null, null, 'vertical-align: middle;').' ';
				}
				$html .= $acceso[0];
				$tecla = $acceso[1];
				$evento_js = eventos::a_javascript($id, $evento);
				$js = "onclick=\"{$this->objeto_js}.set_evento($evento_js);\"";
				echo "&nbsp;" . form::button_html( $this->submit."_".$id, $html, $js, $tab_order, $tecla, $tip, 'button', '', $clase);
			}
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