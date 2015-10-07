<?php
/**
 * Un formulario simple presenta una grilla de campos editables. 
 * A cada uno de estos campos se los denomina Elementos de Formulario (efs).
 * @todo Los EF deberian cargar su estado en el momento de obtener la interface, no en su creacion.
 * @package Componentes
 * @subpackage Eis
 * @jsdoc ei_formulario ei_formulario
 * @wiki Referencia/Objetos/ei_formulario
 */
class toba_ei_formulario extends toba_ei
{
	protected $_prefijo = 'form';	
	protected $_elemento_formulario;			// interno | array |	Rererencias	a los	ELEMENTOS de FORMULARIO
	protected $_nombre_formulario;			// interno | string | Nombre del	FORMULARIO en el cliente
	protected $_lista_ef = array();			// interno | array |	Lista	completa	de	a los	EF
	protected $_lista_ef_post = array();		// interno | array |	Lista	de	elementos que se reciben por POST
	protected $_lista_toba_ef_ocultos = array();
	protected $_nombre_ef_cli = array(); 	// interno | array | ID html de los elementos
	protected $_parametros_carga_efs;		// Parámetros que se utilizan para cargar opciones a los efs
	protected $_modelo_eventos;
	protected $_flag_out = false;			// indica si el formulario genero output
	protected $_evento_mod_estricto;			// Solo dispara la modificacion si se apreto el boton procesar
	protected $_rango_tabs;					// Rango de números disponibles para asignar al taborder
	protected $_objeto_js;	
	protected $_ancho_etiqueta = '150px';
	protected $_ancho_etiqueta_temp;		//Ancho de la etiqueta anterior a un cambio de la misma
	protected $_efs_invalidos = array();
	protected $_efs_generados = array();		//Efs que fueron graficados
	protected $_info_formulario = array();
	protected $_info_formulario_ef = array();
	protected $_js_eliminar;
	protected $_js_agregar;
	protected $_lista_efs_servicio;
	protected $_clase_formateo = 'toba_formateo';
	protected $_detectar_cambios;			//La clase en javascript escucha si algun ef cambio y habilita/deshabilita el boton por defecto
	protected $_estilos = 'ei-base ei-form-base';
	
	protected $_eventos_ext = null;			// Eventos seteados desde afuera
	protected $_observadores;
	protected $_item_editor = '1000255';
	protected $_carga_opciones_ef;			//Encargado de cargar las opciones de los efs
	
	//Salida PDF
	protected $_pdf_letra_tabla = 8;
	protected $_pdf_tabla_ancho;
	protected $_pdf_tabla_opciones = array();
	
	//--- Estaticos
	protected static $_callback_validacion;

	function __construct($id)
	{
		parent::__construct($id);
		//Nombre de los botones de javascript
		$this->_js_eliminar = "eliminar_ei_{$this->_submit}";
		$this->_js_agregar = "agregar_ei_{$this->_submit}";
		$this->_evento_mod_estricto = true;
	}

	function destruir()
	{
		//Memorizo la lista de efs enviados
		$this->_memoria['lista_efs'] = $this->_lista_ef_post;
		parent::destruir();
	}
	
	function aplicar_restricciones_funcionales()
	{
		parent::aplicar_restricciones_funcionales();

		//-- Restricción funcional efs no-visibles y no-editables ------
		$no_visibles = toba::perfil_funcional()->get_rf_form_efs_no_visibles($this->_id[1]);
		$no_editables = toba::perfil_funcional()->get_rf_form_efs_no_editables($this->_id[1]);
		if (! empty($no_visibles) || ! empty($no_editables)) {
			for($a=0;$a<count($this->_info_formulario_ef);$a++) {
				$id_ef = $this->_info_formulario_ef[$a]['identificador'];
				//Existe el ef luego de la configuración?
				if (isset($this->_elemento_formulario[$id_ef])) {
					$id_metadato = $this->_info_formulario_ef[$a]['objeto_ei_formulario_fila'];
					if (in_array($id_metadato, $no_editables)) {
						$this->ef($id_ef)->set_solo_lectura(true);
					}
					if (in_array($id_metadato, $no_visibles)) {
						if (in_array($id_ef, $this->_lista_ef_post)) {							//Si no existe el ef.. la RF que lo desactiva no tiene sentido, puede pasar por excepcion en etapa eventos
							$this->desactivar_efs(array($id_ef));
						}
					}
				}
			}
		}
		//----------------

	}
	
	/**
	 * Método interno para iniciar el componente una vez construido
	 * @ignore 
	 */	
	function inicializar($parametros=array())
	{
		parent::inicializar($parametros);
		if (isset($parametros['nombre_formulario'])) {
			$this->_nombre_formulario =	$parametros["nombre_formulario"];
		}
		if (isset($this->_info_formulario['ancho_etiqueta']) && $this->_info_formulario['ancho_etiqueta'] != '') {
			$this->_ancho_etiqueta = $this->_info_formulario['ancho_etiqueta'];
		}	
		//Creo el array de objetos EF (Elementos de Formulario) que conforman	el	ABM
		$this->crear_elementos_formulario();
		//Cargo IDs en el CLIENTE
		foreach ($this->_lista_ef_post as $ef) {
			$this->_nombre_ef_cli[$ef] = $this->_elemento_formulario[$ef]->get_id_form();
		}
		//Inicializacion de especifica de cada tipo de formulario
		$this->inicializar_especifico();
	}
	
	/**
	 *  Arma las listas que determinan el plan de accion del ABM
	 * @param string $id_ef
	 * @param string $tipo_elemento
	 * @ignore
	 */
	protected function separar_listas_efs($id_ef, $tipo_elemento)
	{
		$this->_lista_ef[] = $id_ef;
		switch ($tipo_elemento) {
			case	'ef_oculto':
			case	'ef_oculto_secuencia':
			case	'ef_oculto_proyecto':
			case	'ef_oculto_usuario':
				$this->_lista_toba_ef_ocultos[] = $id_ef;
				break;
			default:
				$this->_lista_ef_post[] = $id_ef;
		}							
	}
	
	/**
	 * Prepara el identificador del dato que maneja el EF.
	 * Esta parametro puede ser	un	ARRAY	o un string: exiten EF complejos	que manejan	mas de una
	 * Columna de la tabla a	la	que esta	asociada	el	ABM
	 * @param string $columnas
	 * @return mixed
	 * @ignore
	 */
	protected function clave_dato_multi_columna($columnas)
	{
		if (strpos($columnas, ',') !== false) {
			$dato = explode(',', $columnas);
			for ($d=0; $d < count($dato); $d++) { //Elimino espacios en las	claves
				$dato[$d] = trim($dato[$d]);
			}
		} else {
			 $dato = $columnas;
		}
		return $dato;
	}	
	
	/**
	 * Crea un objeto perteneciente a la clase del ef y lo configura.
	 * @param string $id_ef
	 * @param string $clase_ef
	 * @param integer $indx
	 * @param string $clave_dato
	 * @param array $parametros
	 * @ignore
	 */
	protected function instanciar_ef($id_ef, $clase_ef, $indx, $clave_dato, $parametros)
	{
		$this->_elemento_formulario[$id_ef] = new $clase_ef(		$this, 
														$this->_nombre_formulario,
														$this->_info_formulario_ef[$indx]['identificador'],
														$this->_info_formulario_ef[$indx]['etiqueta'],
														addslashes($this->_info_formulario_ef[$indx]['descripcion']),
														$clave_dato,
														array($this->_info_formulario_ef[$indx]['obligatorio'], 
															$this->_info_formulario_ef[$indx]['oculto_relaja_obligatorio']),
														$parametros);
		$this->_elemento_formulario[$id_ef]->set_expandido(! $this->_info_formulario_ef[$indx]['colapsado']);
		if (isset($this->_info_formulario_ef[$indx]['etiqueta_estilo'])) {
			$this->_elemento_formulario[$id_ef]->set_estilo_etiqueta( $this->_info_formulario_ef[$indx]['etiqueta_estilo'] );
		}
		if (isset($this->_info_formulario_ef[$indx]['permitir_html']) && $this->_info_formulario_ef[$indx]['permitir_html']) {
			$this->_elemento_formulario[$id_ef]->set_permitir_html(true);
		} else {
			$this->_elemento_formulario[$id_ef]->set_permitir_html(false);
		}
	}	
	
	/**
	 * Crea los objetos efs asociados al formulario actual
	 * @ignore 
	 */
	protected function crear_elementos_formulario()
	{
		$this->_lista_ef = array();
		for($a=0; $a<count($this->_info_formulario_ef); $a++)
		{
			//-[1]- Separa los efs segun su tipo en varias listas.
			$id_ef = $this->_info_formulario_ef[$a]['identificador'];
			$this->separar_listas_efs($id_ef, $this->_info_formulario_ef[$a]['elemento_formulario']);
			
			//Preparo el identificador del dato que maneja el EF.
			$dato = $this->clave_dato_multi_columna($this->_info_formulario_ef[$a]['columnas']);
									
			$parametros = $this->_info_formulario_ef[$a];
			if (isset($parametros['carga_sql']) && !isset($parametros['carga_fuente'])) {
				$parametros['carga_fuente']=$this->_info['fuente'];
			}
			$this->_parametros_carga_efs[$id_ef] = $parametros;
			
			//Nombre	del formulario.
			$clase_ef = 'toba_'.$this->_info_formulario_ef[$a]['elemento_formulario'];			
			$this->instanciar_ef($id_ef, $clase_ef, $a, $dato, $parametros);
		}
		//--- Se registran las cascadas porque la validacion de efs puede hacer uso de la relacion maestro-esclavo
		$this->_carga_opciones_ef = new toba_carga_opciones_ef($this, $this->_elemento_formulario, $this->_parametros_carga_efs);
		$this->_carga_opciones_ef->registrar_cascadas();
	}
	
	/**
	 * Permite agregar dinamicamente un EF al formulario
	 * @param string $id_ef	Identificador del EF en el formulario	
	 * @param string $clase	Tipo de Ef a agregar
	 * @param string $etiqueta	Etiqueta que presentara el EF
	 * @param string $columnas_clave	 Columna/s de dato que maneja el EF (lista separada por comas si hay mas de una).
	 * @param array $parametros_extra  Arreglo de parametros de carga, de cascada, obligatoriedad, colapsado, etc para controlar el comportamiento del EF.
	 */
	function agregar_ef($id_ef, $clase, $etiqueta, $columnas_clave, $parametros_extra)
	{
		//Armo un par de arreglos con datos basicos y valores por defecto para ciertas columnas
		$default = array( 'descripcion' => '', 'obligatorio' => '0', 'oculto_relaja_obligatorio' => '0', 'colapsado' => '0', 'permitir_html' => '0');		
		$datos_basicos = array('identificador' => $id_ef, 'elemento_formulario' => $clase, 'columnas' => $columnas_clave, 'etiqueta' => $etiqueta);
		
		$prox_id = count($this->_info_formulario_ef);							//Averiguo en que posicion se va a encontrar el ef
		$definicion = array_merge($default, $parametros_extra, $datos_basicos);	//Mezclo el default, los datos basicos y los extras para completar la definicion			
		
		//Empiezo a impactar las estructuras
		$this->_info_formulario_ef[] = $definicion;
		$this->separar_listas_efs($id_ef, $clase);
			
		//Preparo el identificador del dato que maneja el EF.
		$dato = $this->clave_dato_multi_columna($columnas_clave);
		if (isset($definicion['carga_sql']) && !isset($definicion['carga_fuente'])) {
			$definicion['carga_fuente'] = $this->_info['fuente'];
		}
		$this->_parametros_carga_efs[$id_ef] = $definicion;

		//Nombre	del formulario.
		$clase_toba_ef = 'toba_'.$clase;			
		$this->instanciar_ef($id_ef, $clase_toba_ef, $prox_id, $dato, $definicion);

		//Reproceso cascadas por si las dudas
		unset($this->_carga_opciones_ef );
		$this->_carga_opciones_ef = new toba_carga_opciones_ef($this, $this->_elemento_formulario, $this->_parametros_carga_efs);
		$this->_carga_opciones_ef->registrar_cascadas();
		
		//Agrego el nombre del ef en el cliente
		$this->_nombre_ef_cli[$id_ef] = $this->_elemento_formulario[$id_ef]->get_id_form();
	}	
	
	/**
	 * @ignore 
	 */
	protected function inicializar_especifico()
	{
		$this->set_grupo_eventos_activo('no_cargado');
	}
	
	/**
	 * Cambia el ancho total del formulario
	 *	@param string $ancho Tamaño del formulario ej: '600px'
	 */
	function set_ancho($ancho)
	{
		$this->_info_formulario["ancho"] = $ancho;
	}
		

	/*
	*	Setea el tamaño minimo para la etiqueta del ef. El tamaño debe incluir la medida utilizada.
	*	@param string $ancho Tamaño de la etiqueta ej: '150px'
	*	@see restaurar_ancho_etiqueta
	*/
	function set_ancho_etiqueta($ancho)
	{
		$this->_ancho_etiqueta_temp = $this->_ancho_etiqueta;
		$this->_ancho_etiqueta = $ancho;
	}
	

	/**
	 * Restaura el valor previo a un cambio del ancho de la etiqueta
	 * @see set_ancho_etiqueta
	 */
	protected function restaurar_ancho_etiqueta()
	{
		$this->_ancho_etiqueta = $this->_ancho_etiqueta_temp;
	}
	
	/**
	 * Determina si todos los maestros de un ef esclavo poseen datos
	 * @return boolean
	 */
	function ef_tiene_maestros_seteados($id_ef)
	{
		return $this->_carga_opciones_ef->ef_tiene_maestros_seteados($id_ef);
	}	
	
	//-------------------------------------------------------------------------------
	//--------------------------------	EVENTOS  -----------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Acciones a realizar previo al disparo de los eventos
	 * @ignore 
	 */
	function pre_eventos()
	{
		//-- Resguarda la lista de efs para servicio
		$this->_lista_efs_servicio = $this->_lista_ef_post;
		$this->_lista_ef_post = $this->_memoria['lista_efs'];	
		if (isset($this->_memoria['efs'])) {
			foreach (array_keys($this->_memoria['efs']) as $id_ef) {
				if (isset($this->_memoria['efs'][$id_ef]['obligatorio'])) {
					$this->_elemento_formulario[$id_ef]->set_obligatorio($this->_memoria['efs'][$id_ef]['obligatorio']);
				}
				if (isset($this->_memoria['efs'][$id_ef]['desactivar_validacion'])) {
					$this->_elemento_formulario[$id_ef]->desactivar_validacion($this->_memoria['efs'][$id_ef]['desactivar_validacion']);
				}
			}
		}
	}

	/**
	 * Acciones a realizar posteriormente al disparo de eventos
	 * @ignore 
	 */
	function post_eventos()
	{
		if (isset($this->_memoria['efs'])) {
			//--- Restaura lo obligatorio
			foreach ($this->_info_formulario_ef as $def_ef) {
				$id_ef = $def_ef['identificador'];
				if (isset($this->_memoria['efs'][$id_ef])) {
					$this->_elemento_formulario[$id_ef]->set_obligatorio($def_ef['obligatorio']);
					if (isset($this->_memoria['efs'][$id_ef]['desactivar_validacion'])) {
						$this->_elemento_formulario[$id_ef]->desactivar_validacion(false);
					}				
				}
			}
			unset($this->_memoria['efs']);
		}
		$this->limpiar_interface();	
		//-- Restaura la lista de efs para servicio		
		$this->_lista_ef_post = $this->_lista_efs_servicio;
	}	
	
	/**
	 * @ignore 
	 */
	function disparar_eventos()
	{
		//$this->_log->debug( $this->get_txt() . " disparar_eventos", 'toba');
		$this->pre_eventos();
		foreach ($this->_lista_ef as $ef){
			$this->_elemento_formulario[$ef]->cargar_estado_post();
		}		
		$datos = $this->get_datos();
		$validado = false;
		//Veo si se devolvio algun evento!
		if (isset($_POST[$this->_submit]) && $_POST[$this->_submit]!="") {
			$evento = $_POST[$this->_submit];
			//La opcion seleccionada estaba entre las ofrecidas?
			if (isset($this->_memoria['eventos'][$evento])) {
				//Me fijo si el evento requiere validacion
				$maneja_datos = ($this->_memoria['eventos'][$evento] == apex_ei_evt_maneja_datos);
				if($maneja_datos) {
					if (! $validado) {
						$this->validar_estado();
						$validado = true;
					}
					$parametros = $datos;
				} else {
					$parametros = null;
				}
				//El evento es valido, lo reporto al contenedor
				$this->reportar_evento( $evento, $parametros );
			}
		}
		$this->post_eventos();
		$this->borrar_memoria_eventos_atendidos();		
	}

	/**
	 * Recorre todos los efs y valida sus valores actuales
	 * @throws toba_error_validacion En caso de que la validación de algún ef falle
	 */
	function validar_estado()
	{
		//Valida el	estado de los ELEMENTOS	de	FORMULARIO
		foreach ($this->_lista_ef_post as $ef) {
			$validacion = $this->_elemento_formulario[$ef]->validar_estado();
			if ($validacion !== true) {
				$this->_efs_invalidos[$ef] = str_replace("'", '"', $validacion);
				$etiqueta = $this->_elemento_formulario[$ef]->get_etiqueta();
				throw new toba_error_validacion($etiqueta.': '.$validacion, $this->ef($ef));
			}
		}
	}


	
	//-------------------------------------------------------------------------------
	//-------------------------------	Manejos de EFS ------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Permite alternar entre mostrar la ayuda a los efs con un tooltip (predeterminado) o a través de un texto visible inicialmente
	 * @param boolean $mostrar
	 */
	function set_expandir_descripcion($mostrar)
	{
		$this->_info_formulario['expandir_descripcion'] = $mostrar;
	}
	
	/**
	 * Detecta los cambios producidos en los distintos campos en el cliente, cambia los estilos de los mismos y habilita-deshabilita el botón por defecto
	 * en caso de que se hallan producido cambios
	 */
	function set_detectar_cambios($detectar = true)
	{
		$this->_detectar_cambios = $detectar;
	}
	
	
	/**
	 * Borra los datos actuales y resetea el estado de los efs
	 */
	function limpiar_interface()
	{
		foreach ($this->_lista_ef as $ef) {
			$this->_elemento_formulario[$ef]->resetear_estado();
		}
	}

	/**
	 * Retorna todos los ids de los efs
	 * @return array
	 */
	function get_nombres_ef()
	{
		return $this->_lista_ef_post;
	}

	/**
	 * Retorna la cantidad de efs
	 * @return integer
	 */
	function get_cantidad_efs()
	{
		return count($this->_lista_ef_post);
	}
	
	/**
	 * Retorna la lista de identificadores que no estan desactivados
	 * @return array
	 */
	protected function get_efs_activos()
	{
		$lista = array();
		foreach ($this->_lista_ef as $id_ef) {
			if (in_array($id_ef, $this->_lista_ef_post) || in_array($id_ef, $this->_lista_toba_ef_ocultos)) {
				$lista[] = $id_ef;
			}	
		}
		return $lista;
	}
	
	/**
	 * Retorna la referencia a un ef contenido
	 * @return toba_ef
	 */
	function ef($id) 
	{
		return $this->_elemento_formulario[$id];
	}
	
	/**
	 * Indica si existe un ef
	 * @return boolean
	 */
	function existe_ef($id)
	{
		return in_array($id, $this->get_efs_activos());
	}

	
	/**
	 * Permite o no la edición de un conjunto de efs de este formulario, pero sus valores se muestran al usuario
	 *
	 * @param array $efs Uno o mas efs, si es nulo se asume todos
	 * @param boolean $readonly Hacer solo_lectura? (true por defecto)
	 */
	function set_solo_lectura($efs=null, $readonly=true)
	{
		if(!isset($efs)){
			$efs = $this->_lista_ef_post;
		}
		if (! is_array($efs)) {
			$efs = array($efs);	
		}
		foreach ($efs as $ef) {
			if(isset($this->_elemento_formulario[$ef])){
				$this->_elemento_formulario[$ef]->set_solo_lectura($readonly);
			}else{
				throw new toba_error_def("El ef '$ef' no existe");
			}
		}
	}

	/**
	 * Establece que un conjunto de efs serán o no obligatorios
	 * Este estado perdura durante una interaccion
	 *
	 * @param array $efs Uno o mas efs, si es nulo se asume todos
	 * @param boolean $obligatorios Hacer obligatorio? (true por defecto)
	 */
	function set_efs_obligatorios($efs=null, $obligatorios=true) {
		if (!isset($efs)) {
			$efs = $this->_lista_ef_post;
		}
		if (! is_array($efs)) {
			$efs = array($efs);	
		}
		foreach ($efs as $ef) {
			if (isset($this->_elemento_formulario[$ef])) {
				$this->_elemento_formulario[$ef]->set_obligatorio($obligatorios);
				$this->_memoria['efs'][$ef]['obligatorio'] = $obligatorios;
			} else {
				throw new toba_error_def("El ef '$ef' no existe");
			}
		}
	}
	
	
	/**
	 * Desactiva la validación particular de un ef tanto en php como en javascript
	 * Este estado perdura durante una interacción
	 */
	function desactivar_validacion_ef($ef) 
	{
		if (isset($this->_elemento_formulario[$ef])) {
			$this->_elemento_formulario[$ef]->desactivar_validacion(true);
			$this->_memoria['efs'][$ef]['desactivar_validacion'] = 1;		
		} else {
			throw new toba_error_def("El ef '$ef' no existe");			
		}
	}
	
	/**
	 * Establece que un conjunto de efs NO seran enviados al cliente durante una interacción
	 * Para hacer un ef solo_lectura ver {@link toba_ef::set_solo_lectura() set_solo_lectura del ef}
	 * @param array $efs Uno o mas efs, si es nulo se asume todos
	 */
	function desactivar_efs($efs=null)
	{
		if(!isset($efs)){
			$efs = $this->_lista_ef_post;
		}
		if (! is_array($efs)) {
			$efs = array($efs);
		}
		
		foreach ($efs as $ef) {
			$pos = array_search($ef, $this->_lista_ef_post);
			if ($pos !== false) {
				array_splice($this->_lista_ef_post, $pos, 1);	
				$this->_carga_opciones_ef->quitar_ef($ef);
			} else {
				throw new toba_error_def("No se puede desactivar el ef '$ef' ya que no se encuentra en la lista de efs activos");
			}
		}		
		$this->_carga_opciones_ef->registrar_cascadas();
	}
	
	/**
	 * Consume un tabindex html del componente y lo retorna
	 * @return integer
	 */	
	function get_tab_index()
	{
		if (isset($this->_rango_tabs)) {
			return $this->_rango_tabs[0]++;
		}	
	}
	
	//-------------------------------------------------------------------------------
	//-------------------------	  MANEJO de DATOS	  -------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Recupera el estado actual del formulario. 
	 * @return array Asociativo de una dimension columna=>valor
	 */
	function get_datos()
	{
		$registro = array();
		foreach ($this->get_efs_activos() as $ef) {
			$dato	= $this->_elemento_formulario[$ef]->get_dato();
			$estado = $this->_elemento_formulario[$ef]->get_estado();
			if (is_array($dato)){	//El EF maneja	DATO COMPUESTO
				if ($this->_elemento_formulario[$ef]->es_estado_unico()) {
					if ((count($dato))!=(count($estado))) {//Error	de	consistencia interna	del EF
						throw new toba_error_def("Error de consistencia	interna en el EF etiquetado: ".
											$this->_elemento_formulario[$ef]->get_etiqueta().
											"\nRecibido: ".var_export($estado, true));
					}
					for($x=0;$x<count($dato);$x++){
						$registro[$dato[$x]] = $estado[$dato[$x]];
					}
				} else {
					//--- Es multi-estado y multi-dato!! Caso particular, no es posible normalizar el arreglo					
					$salida = array();
					$registro[$ef] = array();
					foreach ($estado as $sub_estado) {
						if (count($dato) != count($sub_estado)) {
							//Error	de	consistencia interna	del EF
							throw new toba_error_def("Error de consistencia	interna en el EF etiquetado: ".
												$this->_elemento_formulario[$ef]->get_etiqueta().
												"\nRecibido: ".var_export($sub_estado, true));
						}
						for ($x=0;$x<count($dato);$x++) {
							$salida[$dato[$x]] = $sub_estado[$dato[$x]];
						}
						$registro[$ef][] = $salida;						
					}
				}
			} else {
				$registro[$dato] = $estado;
			}
		}
		//-- Realiza una validacion transversal de datos propia del proyecto
		if (isset(self::$_callback_validacion)) {
			call_user_func_array(array(self::$_callback_validacion, 'set_componente'), array($this));
			call_user_func_array(array(self::$_callback_validacion, 'validar_datos'), array($registro));
		}
		
		return $registro;
	}
	
	static function set_callback_validacion(toba_valida_datos $validador)
	{
		self::$_callback_validacion = $validador;
	}

	/**
	 * @ignore 
	 */
	function post_configurar()
	{
		parent::post_configurar();
		//---Registar esclavos en los maestro
		$this->_carga_opciones_ef->registrar_cascadas();		
	}
	
	/**
	 * Carga el formulario con un conjunto de datos
	 * El formulario asume que pasa a un estado interno 'cargado' con lo cual, 
	 * por defecto, va a mostrar los eventos de modificacion,cancelar y eliminar en lugar del alta
	 * que solo se muestra cuando el estado interno es 'no_cargado'
	 * @param array $datos Arreglo columna=>valor/es
	 * @param boolean $set_cargado Cambia el grupo activo al 'cargado', mostrando los botones de modificacion, eliminar y cancelar por defecto
	 */
	function set_datos($datos, $set_cargado=true)
	{
		if (isset($datos)){
			//ei_arbol($datos,"DATOS para llenar el EI_FORM");
			//Seteo los	EF	con el valor recuperado
			foreach ($this->_lista_ef as $ef) {	//Tengo que	recorrer	todos	los EF...
				$temp = null;
				$dato = $this->_elemento_formulario[$ef]->get_dato();
				if(is_array($dato)){	//El EF maneja	DATO COMPUESTO
					if ($this->_elemento_formulario[$ef]->es_estado_unico()) {
						$temp = null;
						for($x=0;$x<count($dato);$x++) {
							if(isset($datos[$dato[$x]])) {
								$temp[$dato[$x]] = $datos[$dato[$x]];
							}
						}
					} else {
						//--- Es multi-estado y multi-dato!! Caso particular, no es posible normalizar el arreglo
						$temp = $datos[$ef];
					}
				} else {					//El EF maneja	un	DATO SIMPLE
					if (isset($datos[$dato])){
						if (!is_array($datos[$dato]))
							$temp = $datos[$dato];
						elseif (is_array($datos[$dato])) { //ATENCION: Este es el caso para el multi-seleccion, hay que mejorarlo
							$temp = array();
							foreach ($datos[$dato] as $string) {
								$temp[] = $string;
							}
						}
					}
				}
				if(isset($temp)){
					$this->_elemento_formulario[$ef]->set_estado($temp);
				}
			}
			if ($set_cargado && $this->_grupo_eventos_activo != 'cargado') {
				$this->set_grupo_eventos_activo('cargado');
			}
		}
	}

	/**
	 * Carga el formulario con valores por defecto, generalmente para un alta
	 * @param array $datos Arreglo columna=>valor
	 */
	function set_datos_defecto($datos)
	{
		$this->set_datos($datos);
		if ($this->_grupo_eventos_activo == 'cargado') {
			$this->set_grupo_eventos_activo('no_cargado');
		}
	}
	
	
	/**
	 * Cambia el layout actual del formulario usando un template
	 * @param string $template
	 */
	function set_template($template)
	{
		$this->_info_formulario['template'] = $template;
	}
	
	
	//-------------------------------------------------------------------------------
	//------------------------------	  SALIDA	  -------------------------------
	//-------------------------------------------------------------------------------
	
	/**
	 * Método que se utiliza en la respuesta a los efs_captcha
	 * @todo Este esquema solo se banca un solo ef_captcha. Para poder bancarse mas habria que 
	 * pensar por ejemplo, pasarle al GET "id_ef + text-captcha" para identificar que texto se 
	 * quiere recuperar. De todas maneras para que mas de un captcha???.
	 */	
	function servicio__mostrar_captchas_efs()
	{
		$texto 		= toba::memoria()->get_dato_operacion('texto-captcha');
		$parametros = toba::memoria()->get_dato_operacion('parametros-captcha');
		$refrescar  = toba::memoria()->get_parametro('refrescar');

		if (isset($refrescar)) {
			$texto = null;
		}
		
		$antispam = new toba_imagen_captcha($texto);
		
		if (!isset($texto)) {
			$tamanio = toba::memoria()->get_dato_operacion('tamanio-texto-captcha');
			toba::logger()->debug($tamanio);
			$texto   = $antispam->generateCode($tamanio);
			$antispam->set_codigo($texto);
			toba::memoria()->set_dato_operacion('texto-captcha', $texto);
		}
		
		toba::logger()->debug('Texto CAPTCHA: ' . $texto);
		
		if (isset($parametros)) {
			$antispam->set_parametros_captcha($parametros);
		}
				
		$antispam->show();
	}

	/**
	 * Método que se utiliza en la respuesta de las cascadas usando AJAX
	 */
	function servicio__cascadas_efs()
	{
		require_once(toba_dir() . '/php/3ros/JSON.php');				
		if (! isset($_GET['cascadas-ef']) || ! isset($_GET['cascadas-maestros'])) {
			throw new toba_error_seguridad("Cascadas: Invocación incorrecta");	
		}
		$id_ef = trim(toba::memoria()->get_parametro('cascadas-ef'));
		if (! $this->existe_ef($id_ef)) {
			throw new toba_error_seguridad($this->get_txt()." No existe ef '$id_ef'");
		}
		$fila_actual = trim(toba::memoria()->get_parametro('cascadas-fila'));
		$maestros = array();
		$cascadas_maestros = $this->_carga_opciones_ef->get_cascadas_maestros();
		$ids_maestros = $cascadas_maestros[$id_ef];
		foreach (explode('-|-', toba::memoria()->get_parametro('cascadas-maestros')) as $par) {
			if (trim($par) != '') {
				$param = explode("-;-", trim($par));
				if (count($param) != 2) {
					throw new toba_error_seguridad("Cascadas: Cantidad incorrecta de parametros ($par).");						
				}
				$id_ef_maestro = $param[0];
				
				//--- Verifique que este entre los maestros y lo elimina de la lista
				if (!in_array($id_ef_maestro, $ids_maestros)) {
					throw new toba_error_seguridad("Cascadas: El ef '$id_ef_maestro' no esta entre los maestros de '$id_ef'");
				}
				array_borrar_valor($ids_maestros, $id_ef_maestro);
				
				$campos = $this->_elemento_formulario[$id_ef_maestro]->get_dato();
				$valores = explode(apex_qs_separador, $param[1]);
				if (!is_array($campos)) {
					$maestros[$id_ef_maestro] = $this->ef($id_ef_maestro)->normalizar_parametro_cascada($param[1]);	
				} else {
					//--- Manejo de claves múltiples					
					if (count($valores) != count($campos)) {
						throw new toba_error("Cascadas: El ef $id_ef_maestro maneja distinta cantidad de datos que los campos pasados");
					}
					$valores_clave = array();
					for ($i=0; $i < count($campos) ; $i++) {
						$valores_clave[$campos[$i]] = $valores[$i];
					}
					$maestros[$id_ef_maestro] = $valores_clave;
				}
			}
		}
		//--- Recorro la lista de maestros para ver si falta alguno. Permite tener ocultos como maestros
		foreach ($ids_maestros as $id_ef_maestro) {
			if (isset($fila_actual)) {
				//-- Caso especial del ML, necesita ir a la fila actual y recargar su estado
				$this->ef($id_ef_maestro)->ir_a_fila($fila_actual);
				$this->ef($id_ef_maestro)->cargar_estado_post();
			}
			if (! $this->ef($id_ef_maestro)->tiene_estado()) {
				throw new toba_error_seguridad("Cascadas: El ef maestro '$id_ef_maestro' no tiene estado cargado");
			}
			$maestros[$id_ef_maestro] = $this->ef($id_ef_maestro)->get_estado();
		}
		toba::logger()->debug("Cascadas '$id_ef', Estado de los maestros: ".var_export($maestros, true));		
		$valores = $this->_carga_opciones_ef->ejecutar_metodo_carga_ef($id_ef, $maestros);
		toba::logger()->debug("Cascadas '$id_ef', Respuesta: ".var_export($valores, true));

		//--Guarda los datos en sesion para que los controle a la vuelta PHP
		if (isset($fila_actual)) {
			$this->ef($id_ef)->ir_a_fila($fila_actual);
		}
		
		$sesion = null;									//No hay claves para resguardar
		if (isset($valores) && is_array($valores)) {			//Si lo que se recupero es un arreglo de valores
			if ($this->ef($id_ef)->es_seleccionable()) {		//Si es un ef seleccionable
				$sesion = array_keys($valores);
			}/* else {									//No es seleccionable pero se envia clave / valor.. (aun no se chequea), ej: popup
				$sesion = current($valores);
			}*/
		}
		$this->ef($id_ef)->guardar_dato_sesion($sesion, true);
		$json = new Services_JSON();
		
		if (! is_null($sesion)) {			
			$resultado = array();
			foreach($valores as $klave => $valor) {						//Lo transformo en recordset para mantener el ordenamiento en Chrome
				$resultado[] = array($klave, $valor);
			}
			echo $json->encode($resultado);
		} else {
			echo $json->encode($valores);
		}
	}

	/**
	 * Método que se utiliza en la respuesta del filtro del combo editable usando AJAX
	 */
	function servicio__filtrado_ef_ce()
	{
		require_once(toba_dir() . '/php/3ros/JSON.php');				
		if (! isset($_GET['filtrado-ce-ef']) || ! isset($_GET['filtrado-ce-valor'])) {
			throw new toba_error_seguridad("Filtrado de combo editable: Invocación incorrecta");	
		}
		toba::memoria()->desactivar_reciclado();
		$id_ef = trim(toba::memoria()->get_parametro('filtrado-ce-ef'));
		$filtro = trim(toba::memoria()->get_parametro('filtrado-ce-valor'));
		$fila_actual = trim(toba::memoria()->get_parametro('filtrado-ce-fila'));

		//--- Resuelve la cascada
		$maestros = array($id_ef => $filtro);
		$cascadas_maestros = $this->_carga_opciones_ef->get_cascadas_maestros();
		$ids_maestros = $cascadas_maestros[$id_ef];
		foreach (explode('-|-', toba::memoria()->get_parametro('cascadas-maestros')) as $par) {
			if (trim($par) != '') {
				$param = explode("-;-", trim($par));
				if (count($param) != 2) {
					throw new toba_error_seguridad("Filtrado de combo editable: Cantidad incorrecta de parametros ($par).");						
				}
				$id_ef_maestro = $param[0];
				
				//--- Verifique que este entre los maestros y lo elimina de la lista
				if (!in_array($id_ef_maestro, $ids_maestros)) {
					throw new toba_error_seguridad("Filtrado de combo editable: El ef '$id_ef_maestro' no esta entre los maestros de '$id_ef'");
				}
				array_borrar_valor($ids_maestros, $id_ef_maestro);
				
				$campos = $this->_elemento_formulario[$id_ef_maestro]->get_dato();
				$valores = explode(apex_qs_separador, $param[1]);
				if (!is_array($campos)) {
					$maestros[$id_ef_maestro] = $param[1];
				} else {
					//--- Manejo de claves múltiples					
					if (count($valores) != count($campos)) {
						throw new excepction_toba("Filtrado de combo editable: El ef $id_ef_maestro maneja distinta cantidad de datos que los campos pasados");
					}
					$valores_clave = array();
					for ($i=0; $i < count($campos) ; $i++) {
						$valores_clave[$campos[$i]] = $valores[$i];
					}
					$maestros[$id_ef_maestro] = $valores_clave;
				}
			}
		}
		//--- Recorro la lista de maestros para ver si falta alguno. Permite tener ocultos como maestros
		foreach ($ids_maestros as $id_ef_maestro) {
			if (isset($fila_actual)) {
				//-- Caso especial del ML, necesita ir a la fila actual y recargar su estado
				$this->ef($id_ef_maestro)->ir_a_fila($fila_actual);
				$this->ef($id_ef_maestro)->cargar_estado_post();
			}
			if (! $this->ef($id_ef_maestro)->tiene_estado()) {
				throw new toba_error_seguridad("Filtrado de combo editable: El ef maestro '$id_ef_maestro' no tiene estado cargado");
			}
			$maestros[$id_ef_maestro] = $this->ef($id_ef_maestro)->get_estado();
		}
		
		toba::logger()->debug("Filtrado combo_editable '$id_ef', Cadena: '$filtro', Estado de los maestros: ".var_export($maestros, true));		
		$valores = $this->_carga_opciones_ef->ejecutar_metodo_carga_ef($id_ef, $maestros);
		/*//--- Matchea en la respuesta parte de la pregunta
		foreach ($valores as $clave => $valor) {
			$valores[$clave] = str_ireplace($filtro, "<em>$filtro</em>", $valor);
		}*/
		toba::logger()->debug("Filtrado combo_editable '$id_ef', Respuesta: ".var_export($valores, true));				
		$json = new Services_JSON();
		
		if (is_array($valores)) {
			$resultado = array();
			foreach($valores as $klave => $valor) {						//Lo transformo en recordset para mantener el ordenamiento en Chrome
				$resultado[] = array($klave, $valor);
			}
			echo $json->encode($resultado);
		} else {
			echo $json->encode($valores);
		}
	}

	/**
	 * Método que se utiliza en la respuesta del filtro del combo editable cuando se quiere validar un id seleccionado
	 */
	function servicio__filtrado_ef_ce_validar()
	{
		require_once(toba_dir() . '/php/3ros/JSON.php');				
		if (! isset($_GET['filtrado-ce-ef']) || ! isset($_GET['filtrado-ce-valor'])) {
			throw new toba_error_seguridad("Validación de combo editable: Invocación incorrecta");	
		}
		$id_ef = trim(toba::memoria()->get_parametro('filtrado-ce-ef'));
		$valor = trim(toba::memoria()->get_parametro('filtrado-ce-valor'));
		//$fila_actual = trim(toba::memoria()->get_parametro('filtrado-ce-fila'));

		$descripcion = $this->_carga_opciones_ef->ejecutar_metodo_carga_descripcion_ef($id_ef, $valor);
		$estado = array($valor => $descripcion);
		
		//--- Se arma la respuesta en formato JSON
		$json = new Services_JSON();
		echo $json->encode($estado);
	}
	
	function generar_html()
	{
		//Genero la interface
		echo "\n\n<!-- ***************** Inicio EI FORMULARIO (	".	$this->_id[1] ." )	***********	-->\n\n";
		//Campo de sincroniacion con JS
		echo toba_form::hidden($this->_submit, '');
		echo toba_form::hidden($this->_submit.'_implicito', '');
		$ancho = '';
		if (isset($this->_info_formulario["ancho"])) {
			$ancho = convertir_a_medida_tabla($this->_info_formulario["ancho"]);
		}
		echo "<table class='{$this->_estilos}' $ancho>";
		echo "<tr><td style='padding:0'>";
		echo $this->get_html_barra_editor();
		$this->generar_html_barra_sup(null, true,"ei-form-barra-sup");
		$this->generar_formulario();
		echo "</td></tr>\n";
		echo "</table>\n";
		$this->_flag_out = true;
	}

	/**
	 * @ignore 
	 */
	protected function generar_formulario()
	{
		//--- La carga de efs se realiza aqui para que sea contextual al servicio
		//--- ya que hay algunos que no lo necesitan (ej. cascadas)
		$this->_carga_opciones_ef->cargar();
		$this->_rango_tabs = toba_manejador_tabs::instancia()->reservar(250);		
				
		$ancho = ($this->_info_formulario['ancho'] != '') ? "width: {$this->_info_formulario['ancho']};" : '';
		$colapsado = (isset($this->_colapsado) && $this->_colapsado) ? "display:none;" : "";
	
		echo "<div class='ei-cuerpo ei-form-cuerpo' style='$ancho $colapsado' id='cuerpo_{$this->objeto_js}'>";
		$this->generar_layout();
		
		$hay_colapsado = false;
		foreach ($this->_lista_ef_post as $ef){
			if (! $this->_elemento_formulario[$ef]->esta_expandido()) {
				$hay_colapsado = true;
				break;
			}
		}		
		if ($hay_colapsado) {
			$img = toba_recurso::imagen_skin('expandir_vert.gif', false);
			$colapsado = "style='cursor: pointer; cursor: hand;' onclick=\"{$this->objeto_js}.cambiar_expansion();\" title='Mostrar / Ocultar'";
			echo "<div class='ei-form-fila ei-form-expansion'>";
			echo "<img id='{$this->objeto_js}_cambiar_expansion' src='$img' $colapsado>";
			echo "</div>";
		}
		if ($this->botonera_abajo()) {
				$this->generar_botones();
		}
		echo "</div>\n";
	}
	
	/**
	 * Genera el cuerpo del formulario conteniendo la lista de efs
	 * Por defecto el layout de esta lista es uno sobre otro, este método se puede extender
	 * para incluir algún layout específico
	 * @ventana Extender para cambiar el layout por defecto
	 */	
	protected function generar_layout()
	{
		if (!isset($this->_info_formulario['template']) || trim($this->_info_formulario['template']) == '') {
			foreach ($this->_lista_ef_post as $ef) {
				$this->generar_html_ef($ef);
			}
		} else {
			$this->generar_layout_template();
		}
	}
	
	protected function generar_layout_template()
	{
		//Parseo del template
		$pattern = '/\[ef([\s\w+=\w+]+)\]/i';
		if (preg_match_all($pattern, $this->_info_formulario['template'], $resultado)) {
			$salida = $this->_info_formulario['template'];
			for ($i=0; $i < count($resultado[0]); $i++) {
				$original = $resultado[0][$i];
				$atributos = array();
				foreach (explode(' ',trim($resultado[1][$i])) as $atributo) {
					$partes = explode('=', $atributo);
					$atributos[$partes[0]] = $partes[1];
				}
				if (! isset($atributos['id'])) {
					throw new toba_error_def($this->get_txt()."Tag [ef] incorrecto, falta atributo id");
				}
				$etiqueta_mostrar = true;
				if (isset($atributos['etiqueta_mostrar']) && $atributos['etiqueta_mostrar'] == 0) {
					$etiqueta_mostrar = false;
				}
				$etiqueta_ancho = null;
				if (isset($atributos['etiqueta_ancho'])) {
					$etiqueta_ancho = $atributos['etiqueta_ancho'];
				}				
				$html = $this->get_html_ef($atributos['id'], $etiqueta_ancho, $etiqueta_mostrar);				
				$salida = str_replace($original, $html, $salida);
			}
			echo $salida;
		} else {
			throw new toba_error_def($this->get_txt()."Template incorrecto");
		}		
	}
	
	/**
	 * Genera para la impresion html el cuerpo del formulario conteniendo la lista de efs
	 * Por defecto el layout de esta lista es uno sobre otro, este método se puede extender
	 * para incluir algún layout específico
	 * @ventana Extender para cambiar el layout por defecto de la impresion html
	 */		
	protected function generar_layout_impresion()
	{
		echo "<table class='{$this->_estilos}' width='{$this->_info_formulario['ancho']}'>";		
		foreach ( $this->_lista_ef_post as $ef){
			if ($this->_info_formulario['no_imprimir_efs_sin_estado']) {
				//Los combos que no tienen valor establecido no se imprimen
				if( $this->_elemento_formulario[$ef] instanceof toba_ef_combo ) {
					if ( $this->_elemento_formulario[$ef]->es_estado_no_seleccionado()) {
						continue;	
					}
				}
				//Los editables vacios no se imprimen
				if( $this->_elemento_formulario[$ef] instanceof toba_ef_editable ) {
					if (strlen(trim($this->_elemento_formulario[$ef]->get_estado()) == '')) {
						continue;	
					}
				}			
			}				
			echo "<tr>\n";
			$this->generar_html_impresion_ef($ef);
			echo "</tr>\n";
		}
		echo "</table>\n";		
	}

	protected function generar_layout_template_impresion()
	{
		//Parseo del template
		$pattern = '/\[ef([\s\w+=\w+]+)\]/i';
		if (preg_match_all($pattern, $this->_info_formulario['template_impresion'], $resultado)) {
			$salida = $this->_info_formulario['template_impresion'];
			for ($i=0; $i < count($resultado[0]); $i++) {
				$original = $resultado[0][$i];
				$atributos = array();
				foreach (explode(' ',trim($resultado[1][$i])) as $atributo) {
					$partes = explode('=', $atributo);
					$atributos[$partes[0]] = $partes[1];
				}
				if (! isset($atributos['id'])) {
					throw new toba_error_def($this->get_txt()."Tag [ef] incorrecto, falta atributo id");
				}
				$etiqueta_mostrar = true;
				if (isset($atributos['etiqueta_mostrar']) && $atributos['etiqueta_mostrar'] == 0) {
					$etiqueta_mostrar = false;
				}
				$etiqueta_ancho = null;
				if (isset($atributos['etiqueta_ancho'])) {
					$etiqueta_ancho = $atributos['etiqueta_ancho'];
				}
				$html = $this->get_html_impresion_ef($atributos['id']);
				$salida = str_replace($original, $html, $salida);
			}
			echo $salida;
		} else {
			throw new toba_error_def($this->get_txt()."Template impresión incorrecto");
		}
	}

	/**
	 * Genera la etiqueta y el componente HTML de un ef
	 * @param string $ef Identificador del ef
	 * @param string $ancho_etiqueta Ancho de la etiqueta del ef. Si no se setea, usa la definida en el editor.
	 * Recordar incluír las medidas (px, %, etc.). 
	 */
	protected function generar_html_ef($ef, $ancho_etiqueta=null)
	{
		echo $this->get_html_ef($ef, $ancho_etiqueta);
	}

	/**
	 * Retorna la etiqueta y el componente HTML de un ef
	 * @param string $ef Identificador del ef
	 * @param string $ancho_etiqueta Ancho de la etiqueta del ef. Si no se setea, usa la definida en el editor.
	 * Recordar incluír las medidas (px, %, etc.). 
	 */	
	protected function get_html_ef($ef, $ancho_etiqueta=null, $con_etiqueta=true)
	{
		$salida = '';
		if (! in_array($ef, $this->_lista_ef_post)) {
			//Si el ef no se encuentra en la lista posibles, es probable que se alla quitado con una restriccion o una desactivacion manual
			return;
		}
		$clase = 'ei-form-fila';
		$estilo_nodo = "";
		$id_ef = $this->_elemento_formulario[$ef]->get_id_form();
		if (! $this->_elemento_formulario[$ef]->esta_expandido()) {
			$clase .= ' ei-form-fila-oculta';
			$estilo_nodo = "display:none";
		}
		if (isset($this->_info_formulario['resaltar_efs_con_estado']) 
				&& $this->_info_formulario['resaltar_efs_con_estado'] && $this->_elemento_formulario[$ef]->seleccionado()) {
			$clase .= ' ei-form-fila-filtrada';
		}
		$es_fieldset = ($this->_elemento_formulario[$ef] instanceof toba_ef_fieldset);
		if (! $es_fieldset) {							//Si es fieldset no puedo sacar el <div> porque el navegador cierra visualmente inmediatamente el ef.
			$salida .= "<div class='$clase' style='$estilo_nodo' id='nodo_$id_ef'>\n";
		}
		if ($this->_elemento_formulario[$ef]->tiene_etiqueta() && $con_etiqueta) {
			$salida .= $this->get_etiqueta_ef($ef, $ancho_etiqueta);
			//--- El margin-left de 0 y el heigth de 1% es para evitar el 'bug de los 3px'  del IE
			$ancho = isset($ancho_etiqueta) ? $ancho_etiqueta : $this->_ancho_etiqueta;
			$salida .= "<div id='cont_$id_ef' style='margin-left: $ancho;'>\n";
			$salida .= $this->get_input_ef($ef);
			$salida .= "</div>";
			if (isset($this->_info_formulario['expandir_descripcion']) && $this->_info_formulario['expandir_descripcion']) {
				$salida .= '<span class="ei-form-fila-desc">'.$this->_elemento_formulario[$ef]->get_descripcion().'</span>';
			}

		} else {		
			$salida .= $this->get_input_ef($ef);
		}
		if (! $es_fieldset) {
			$salida .= "</div>\n";
		}
		return $salida;		
	}
	
	protected function get_html_impresion_ef($ef)
	{
		$html =  "<td class='ei-form-etiq'>\n";
		$html .= $this->_elemento_formulario[$ef]->get_etiqueta();
		$html .= "</td><td class='ei-form-valor'>\n";
		//Hay que formatear?
		if(isset($this->_info_formulario_ef[$ef]["formateo"])){
			$formateo = new $this->_clase_formateo('impresion_html');
			$funcion = "formato_" . $this->_info_formulario_ef[$ef]["formateo"];
			$valor_real = $this->_elemento_formulario[$ef]->get_estado();
			$valor = $formateo->$funcion($valor_real);
		} else {
			$valor = $this->_elemento_formulario[$ef]->get_descripcion_estado('impresion_html');
	    }
		$html .= $valor;
		$html .= "</td>\n";
		return $html;
	}
	
	/**
	 * Genera la etiqueta y la vista de impresion de un ef
	 * @param string $ef Identificador del ef
	 */
	protected function generar_html_impresion_ef($ef)
	{
		echo $this->get_html_impresion_ef($ef);
	}	
	
	/**
	 * Genera la salida gráfica de un ef particular
	 * @ventana Extender para agregar html antes o despues de un ef específico
	 */
	protected function generar_input_ef($ef)
	{
		$this->_efs_generados[] = $ef;
		if (! in_array($ef, $this->_lista_ef_post)) {
			//Si el ef no se encuentra en la lista posibles, es probable que se alla quitado con una restriccion o una desactivacion manual
			return;
		}		
		$salida = $this->_elemento_formulario[$ef]->get_input();
		echo $salida;
	}
	
	/**
	 * Genera la salida gráfica de un ef particular
	 * @ventana Extender para agregar html antes o despues de un ef específico
	 */
	protected function get_input_ef($ef)
	{
		ob_start();
		$this->generar_input_ef($ef, true);
		return ob_get_clean();
	}	

	
	/**
	 * General el html de la etiqueta de un ef especifico
	 * @param string $ef Id. del ef
	 * @param string $ancho_etiqueta Ancho de la etiqueta del ef. Si no se setea, usa la definida en el editor.
	 * Recordar incluír las medidas (px, %, etc.). 
	 */
	protected function generar_etiqueta_ef($ef, $ancho_etiqueta=null)
	{
		echo $this->get_etiqueta_ef($ef, $ancho_etiqueta);
	}
	
	/**
	 * Retorna el html de la etiqueta de un ef especifico
	 * @param string $ef Id. del ef
	 * @param string $ancho_etiqueta Ancho de la etiqueta del ef. Si no se setea, usa la definida en el editor.
	 * Recordar incluír las medidas (px, %, etc.). 
	 */
	protected function get_etiqueta_ef($ef, $ancho_etiqueta=null)
	{
		$estilo = $this->_elemento_formulario[$ef]->get_estilo_etiqueta();
		$marca ='';		
		if ($estilo == '') {
	        		if ($this->_elemento_formulario[$ef]->es_obligatorio()) {
	    	        		$estilo = 'ei-form-etiq-oblig';
				$marca = '(*)';
        			} else {
	            		$estilo = 'ei-form-etiq';
    	    		}
		}
		$desc='';
		if (!isset($this->_info_formulario['expandir_descripcion']) || ! $this->_info_formulario['expandir_descripcion']) {
			$desc = $this->_elemento_formulario[$ef]->get_descripcion();		
			if ($desc !=""){
				$desc = toba_parser_ayuda::parsear($desc);
				$desc = toba_recurso::imagen_toba("descripcion.gif",true,null,null,$desc);
			}
		}
		$id_ef = $this->_elemento_formulario[$ef]->get_id_form();
		$editor = $this->generar_vinculo_editor($ef);
		$etiqueta = $this->_elemento_formulario[$ef]->get_etiqueta();
		//--- El _width es para evitar el 'bug de los 3px'  del IE
		$ancho = isset($ancho_etiqueta) ? $ancho_etiqueta : $this->_ancho_etiqueta;
		return "<label style='width:$ancho;' for='$id_ef' class='$estilo'>$editor $desc $etiqueta $marca</label>\n";
	}
	
	/**
	 * @ignore 
	 */
	protected function generar_vinculo_editor($id_ef)
	{
		if (toba_editor::modo_prueba()) {
			$param_editor = array( apex_hilo_qs_zona => implode(apex_qs_separador,$this->_id),
									'ef' => $id_ef );
			return toba_editor::get_vinculo_subcomponente($this->_item_editor, $param_editor);			
		}
		return null;
	}
		
	//-------------------------------------------------------------------------------
	//------------------------------ JAVASCRIPT  ------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * @ignore 
	 */
	protected function crear_objeto_js()
	{
		$identado = toba_js::instancia()->identado();
		$rango_tabs = "new Array({$this->_rango_tabs[0]}, {$this->_rango_tabs[1]})";
		$esclavos = toba_js::arreglo($this->_carga_opciones_ef->get_cascadas_esclavos(), true, false);
		$maestros = toba_js::arreglo($this->_carga_opciones_ef->get_cascadas_maestros(), true, false);		
		$id = toba_js::arreglo($this->_id, false);
		$invalidos = toba_js::arreglo($this->_efs_invalidos, true);
		echo $identado."window.{$this->objeto_js} = new ei_formulario($id, '{$this->objeto_js}', $rango_tabs, '{$this->_submit}', $maestros, $esclavos, $invalidos);\n";
		if ($this->_disparo_evento_condicionado_a_datos) {
			echo $identado . "{$this->objeto_js}.set_eventos_condicionados_por_datos(true);";
		}
		foreach ($this->_lista_ef_post as $ef) {
			if (! in_array($ef, $this->_efs_generados)) {
				throw new toba_error_def($this->get_txt()." Error en la redefinición del layout: Falta salida ef '$ef'");
			}
			echo $identado."{$this->objeto_js}.agregar_ef({$this->_elemento_formulario[$ef]->crear_objeto_js()}, '$ef');\n";
		}
		if ($this->_detectar_cambios) {
			foreach (array_keys($this->_eventos_usuario_utilizados) as $id_evento) {
				if ($this->evento($id_evento)->es_predeterminado()) {
					$excluidos = array();
					foreach ($this->_lista_ef_post as $ef) {
						if ($this->ef($ef)->es_solo_lectura()) {
							$excluidos[] = $ef;
						}
					}					
					$excluidos = toba_js::arreglo($excluidos);
					echo $identado."{$this->objeto_js}.set_procesar_cambios(true, '$id_evento', $excluidos);\n";					
				}
			}
		}
	}

	/**
	 * Retorna una referencia al ef en javascript
	 * @param string $id Id. del ef
	 * @return string
	 */
	function get_objeto_js_ef($id)
	{
		return "{$this->objeto_js}.ef('$id')";
	}
	
	/**
	 * @ignore 
	 */
	function get_consumo_javascript()
	{
		$consumo = parent::get_consumo_javascript();
		$consumo[] = 'componentes/ei_formulario';
		//Busco las	dependencias
		foreach ($this->_lista_ef_post	as	$ef){
			$temp	= $this->_elemento_formulario[$ef]->get_consumo_javascript();
			if(isset($temp)) $consumo = array_merge($consumo, $temp);
		}
		$consumo = array_unique($consumo);//Elimino los	duplicados
		return $consumo;
	}

	//---------------------------------------------------------------
	//----------------------  SALIDA Impresion  ---------------------
	//---------------------------------------------------------------
		
	function vista_impresion_html( toba_impresion $salida )
	{
		$this->_carga_opciones_ef->cargar();
		$salida->subtitulo( $this->get_titulo() );
		if (!isset($this->_info_formulario['template_impresion']) || trim($this->_info_formulario['template_impresion']) == '') {
			$this->generar_layout_impresion();
		} else {
			$this->generar_layout_template_impresion();
		}
	}
	
	
	//---------------------------------------------------------------
	//----------------------  SALIDA PDF  ---------------------------
	//---------------------------------------------------------------
	
	/**
	 * Permite setear el ancho del formulario.
	 * @param unknown_type $ancho Es posible pasarle valores enteros o porcentajes (por ejemplo 85%).
	 */
	function set_pdf_tabla_ancho($ancho)
	{
		$this->_pdf_tabla_ancho = $ancho;
	}
	
	/**
	 * Permite setear el tamaño de la tabla que representa el formulario.
	 * @param integer $tamanio Tamaño de la letra.
	 */
	function set_pdf_letra_tabla($tamanio)
	{
		$this->_pdf_letra_tabla = $tamanio;
	}
	
	/**
	 * Permite setear el estilo que llevara la tabla en la salida pdf.
	 * @param array $opciones Arreglo asociativo con las opciones para la tabla de salida.
	 * @see toba_vista_pdf::tabla, ezpdf::ezTable
	 */
	function set_pdf_tabla_opciones($opciones)
	{
		$this->_pdf_tabla_opciones = $opciones;
	}
	
	function vista_pdf( $salida )
	{
		$this->_carga_opciones_ef->cargar();
		$formateo = new $this->_clase_formateo('pdf');
		$datos = array();
		$a['datos_tabla'] = array();
		foreach ( $this->_lista_ef_post as $ef ){
			if ($this->_elemento_formulario[$ef]->tiene_estado()) {
				$etiqueta = $this->_elemento_formulario[$ef]->get_etiqueta();
				//Hay que formatear? Le meto pa'delante...
            	if(isset($this->_info_formulario_ef[$ef]["formateo"])){
                	$funcion = "formato_" . $this->_info_formulario_ef[$ef]["formateo"];
                	$valor_real = $this->_elemento_formulario[$ef]->get_estado();
                	$valor = $formateo->$funcion($valor_real);
            	}else{
		            $valor = $this->_elemento_formulario[$ef]->get_descripcion_estado('pdf');
		        }	
				$datos['datos_tabla'][] = array('clave' => $etiqueta, 'valor'=>$valor);
			}
		}
		//-- Genera la tabla
        $ancho = null;
        if (strpos($this->_pdf_tabla_ancho, '%') !== false) {
        	$ancho = $salida->get_ancho(str_replace('%', '', $this->_pdf_tabla_ancho));	
        } elseif (isset($this->_pdf_tabla_ancho)) {
        		$ancho = $this->_pdf_tabla_ancho;
        }
        $opciones = $this->_pdf_tabla_opciones;
        if (isset($ancho)) {
        	$opciones['width'] = $ancho;		
        }        
		$datos['titulo_tabla'] = $this->get_titulo();
		$salida->tabla($datos, false, $this->_pdf_letra_tabla, $opciones);
	}

	/**
	 * @ignore
	 *  Obtiene la etiqueta y valor formateado del ef
	 * @param string $id_ef Identificador del ef
	 * @return array
	 */
	function get_valores_pdf( $id_ef ) {
		$formateo = new $this->_clase_formateo('pdf');
		$etiqueta = $this->_elemento_formulario[$id_ef]->get_etiqueta();
		//Hay que formatear? Le meto pa'delante...
		if(isset($this->_info_formulario_ef[$id_ef]["formateo"])){
			$funcion = "formato_" . $this->_info_formulario_ef[$id_ef]["formateo"];
			$valor_real = $this->_elemento_formulario[$id_ef]->get_estado();
			$valor = $formateo->$funcion($valor_real);
		}else{
			$valor = $this->_elemento_formulario[$id_ef]->get_descripcion_estado('pdf');
		}
		$k = $this->_elemento_formulario[$id_ef]->get_etiqueta();
		$a = array('clave' => $k, 'valor' => $valor);
		return $a;
	}


	//---------------------------------------------------------------
	//----------------------  SALIDA EXCEL --------------------------
	//---------------------------------------------------------------
		
	function vista_excel(toba_vista_excel $salida)
	{
		$this->_carga_opciones_ef->cargar();
		$formateo = new $this->_clase_formateo('excel');
		$datos = array();
		foreach ( $this->_lista_ef_post as $ef ){
			$opciones = array();
			$etiqueta = $this->_elemento_formulario[$ef]->get_etiqueta();
			//Hay que formatear?
			$estilo = array();
			if(isset($this->_info_formulario_ef[$ef]["formateo"])){
				$funcion = "formato_" . $this->_info_formulario_ef[$ef]["formateo"];
				$valor_real = $this->_elemento_formulario[$ef]->get_estado();
				list($valor, $estilo) = $formateo->$funcion($valor_real);
			}else{
				list($valor, $estilo) = $this->_elemento_formulario[$ef]->get_descripcion_estado('excel');
			}	
			if (isset($estilo)) {
				$opciones['valor']['estilo'] = $estilo;
			}	
			$opciones['etiqueta']['estilo']['font']['bold'] = true;
			$opciones['etiqueta']['ancho'] = 'auto';
			$opciones['valor']['ancho'] = 'auto';
			$datos = array(array('etiqueta' => $etiqueta, 'valor' => $valor));
			$salida->tabla($datos, array(), $opciones);
		}		
	}
	
	//---------------------------------------------------------------
	//----------------------  API BASICA ----------------------------
	//---------------------------------------------------------------

	/**
	 * Cambia la forma en que se le da formato a un ef en las salidas pdf, excel y html
	 * @param string $id_ef
	 * @param string $funcion Nombre de la función de formateo, sin el prefijo 'formato_'
	 * @param string $clase Nombre de la clase que contiene la funcion, por defecto toba_formateo
	 */
	function set_formateo_ef($id_ef, $funcion, $clase=null)
	{
		$this->_info_formulario_ef[$id_ef]["formateo"] = $funcion;
		if (isset($clase)) {
			$this->_clase_formateo = $clase;
		}
	}
	
	//---------------------------------------------------------------
	//------------------------- SALIDA XML --------------------------
	//---------------------------------------------------------------
	
	/**
	 * Genera el xml del componente
	 * @param boolean $inicial Si es el primer elemento llamado desde vista_xml
	 * @param string $xmlns Namespace para el componente
	 * @return string XML del componente
	 */
	function vista_xml($inicial=false, $xmlns=null)
	{
		if ($xmlns) {
			$this->xml_set_ns($xmlns);
		}
		$this->_carga_opciones_ef->cargar();
		$formateo = new $this->_clase_formateo('pdf');
		
        $ancho = null;
        if (strpos($this->_pdf_tabla_ancho, '%') !== false) {
        	$ancho = $salida->get_ancho(str_replace('%', '', $this->_pdf_tabla_ancho));	
        } elseif (isset($this->_pdf_tabla_ancho)) {
        		$ancho = $this->_pdf_tabla_ancho;
        }
        $opciones = $this->_pdf_tabla_opciones;
        if (isset($ancho)) {
        	$opciones['width'] = $ancho;		
        }        
		
		$xml = '<'.$this->xml_ns.'tabla'.$this->xml_ns_url;
		$xml .= $this->xml_get_att_comunes();
/*		if ($this->_pdf_letra_tabla) {
			$xml .= ' letra="'.$this->_pdf_letra_tabla.'"';
		}*/
		$xml .= '>';
		$xml .= $this->xml_get_elem_comunes();
		if ($this->_lista_ef_post || $opciones){ 
			if ($this->_lista_ef_post) {
				$tmpxml = null;
				foreach ( $this->_lista_ef_post as $ef ){
					if ($this->_elemento_formulario[$ef]->tiene_estado() && (!isset($this->xml_ef_no_procesar) || !in_array($ef,$this->xml_ef_no_procesar))) {
						$etiqueta = $this->_elemento_formulario[$ef]->get_etiqueta();
						//Hay que formatear? Le meto pa'delante...
						if(isset($this->_info_formulario_ef[$ef]["formateo"])){
							$funcion = "formato_" . $this->_info_formulario_ef[$ef]["formateo"];
							$valor_real = $this->_elemento_formulario[$ef]->get_estado();
							$valor = $formateo->$funcion($valor_real);
						}else{
							$valor = $this->_elemento_formulario[$ef]->get_descripcion_estado('pdf');
						}	
						$tmpxml .= '<'.$this->xml_ns.'dato clave="'.$etiqueta.'" valor="'.$valor.'"/>';
					}
				}
				if($tmpxml) {
					$xml .= '<'.$this->xml_ns.'datos>'.$tmpxml.'</'.$this->xml_ns.'datos>';
				}
			}
			if ($opciones) {
				$xml .= '<'.$this->xml_ns.'opciones>';
				foreach ($opciones as $nombre=>$valor) {
					$xml .= '<'.$this->xml_ns.'opcion nombre="'.$nombre.'" valor="'.$valor.'"/>';
				}
				$xml .= '</'.$this->xml_ns.'opciones>';
			}
		}
		$xml .= '</'.$this->xml_ns.'tabla>';
		return $xml;
	}
	
	/**
	 * Permite definir elementos de formulario que no se desea incluir en el XML
	 * @param mixed $ef Arreglo de tipo ("id_ef1", "id_ef2"), o un id_ef
	 */
	function xml_set_ef_no_procesar($ef) 
	{
		if(is_array($ef)) {
			if(isset($this->xml_ef_no_procesar)) {
				$this->xml_ef_no_procesar = array_merge($this->xml_ef_no_procesar,$ef);
			} else {
				$this->xml_ef_no_procesar = $ef;
			}
		} else {
			$this->xml_ef_no_procesar[] = $ef;
		}
	}
}
?>
