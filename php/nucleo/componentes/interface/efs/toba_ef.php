<?php
define("apex_ef_cascada","%");	//Mascara para reemplazar el valor de una dependencia en un SQL

/**
 * Clase base de los elementos de formulario. 
 * 
 * Los efs son controles o widgets que forman parte de un formulario, tienen lógica de validación y formato tanto en js como en php.
 * Aquellos controles que se basan en la metáfora de selección permiten cargar sus opciones en base a métodos php, consultas SQL y lista de valores fijos.
 * 
 * @package Componentes
 * @subpackage Efs
 * @jsdoc ef ef
 * @wiki Referencia/efs
 */
abstract class toba_ef
{
	protected $padre;		    	// PADRE del ELEMENTO (ID del objeto en el que este esta incluido)
	protected $id;			    	// ID del ELEMENTO
	protected $id_padre;			// ID del formulario
	protected $etiqueta;	   		// Etiqueta del ELEMENTO
	protected $descripcion;   		// Descripcion del ELEMENTO
	protected $id_form_orig;		// ID original a utilizar en el FORM
	protected $id_form;	    		// ID a utilizar en el FORM (puede ser variado para incluir varios en el mismo form)
	protected $dato;          		// NOMBRE del DATO que esta manejando el ELEMENTO (si es un DATO compuesto, es un array)
	protected $estado;	    		// Estado ACTUAL del ELEMETO (Si el DATO es compuesto, es un array)
	protected $estado_defecto;
	protected $obligatorio;			// Flag que indica SI se el ELEMENTO representa un valor obligatorio
	protected $obligatorio_oculto_relaj;
	protected $solo_lectura;      	// Flag que indica si el objeto se debe deshabilitar cuando se muestra
	protected $javascript="";			// Javascript del elemento de formulario
	protected $input_extra = "";		// Parametros adicionales
	protected $expandido = true;
	protected $ancho_etiqueta = 150;
	protected $estilo_etiqueta = '';
	protected $agregado_form;			//Número de linea en un form multilinea	
	protected $clase_css = 'ef';
	protected $permitir_html = false;		//Hace un htmlentities para evitar ataques XSS
	protected $check_ml_toggle = false;
	protected $iconos = array();
	protected $solo_lectura_base;
	protected $solo_lectura_modificacion;
	protected $tamano;

	//--- DEPENDENCIAS ---
	protected $cascada_relajada = false;
	protected $cuando_cambia_valor = '';	//Js que se dispara cuando cambia el valor del ef
	protected $maestros = array();			// Array Ids de EFs MAESTROS
			
	//---- Metodo de carga
	protected $campos_clave = array(0);
	protected $campo_valor;
	protected $opciones_cargadas = false;
	
	static protected $_excepciones;		
	static protected $maximo_descripcion;

	
	static function set_maximo_descripcion($maximo)
	{
		self::$maximo_descripcion = $maximo;
	}

	static function get_maximo_descripcion()
	{
		return self::$maximo_descripcion;
	}
	
	function __construct($padre,$nombre_formulario,$id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		$this->id = $id;		
		$this->padre = $padre;
		$this->id_form_orig = "ef_" . $this->padre->get_id_form() . $this->id;
		$this->ir_a_fila();
		$this->etiqueta = $etiqueta;
		$this->descripcion = $descripcion;
		$this->dato = $dato;
		list($this->obligatorio, $this->obligatorio_oculto_relaj)  = $obligatorio;
		
		//---- Declaracion de dependencias
		if (isset($parametros['carga_maestros'])) {
			if ($parametros['carga_maestros']!=""){
				$this->maestros = explode(",",$parametros['carga_maestros']);
				$this->maestros = array_map("trim",$this->maestros);
			}
			if (isset($parametros['carga_cascada_relaj']) && $parametros['carga_cascada_relaj']) {
				$this->cascada_relajada = true;
			}
		}
		//Seteo las variables temporales de los modos solo_lectura
		$this->solo_lectura_base = ((isset($parametros["solo_lectura"]))&&($parametros["solo_lectura"]==1));
		$this->solo_lectura_modificacion = ((isset($parametros["solo_lectura_modificacion"]))&&($parametros["solo_lectura_modificacion"]==1));
		if (isset($parametros['estilo'])) {
			$this->clase_css = $parametros['estilo'];				//Estilo del EF, no de la etiqueta
		}
		//Valor FIJO
		if(isset($parametros['estado_defecto'])){
			$this->estado_defecto = $parametros['estado_defecto'];
			$this->estado = $this->estado_defecto;
		}		
		//---------------------- Manejo de CLAVES  ------------------
		if (isset($parametros['carga_col_clave'])) {
			$campos_clave = explode(",", $parametros['carga_col_clave']);
			$this->campos_clave = array_map("trim", $campos_clave);
		} else {
			if (isset($parametros['columna_proyecto'])) {
				$this->campos_clave = array(0,1);	
			}
		}
		if (isset($parametros['carga_col_desc'])) {
			$this->campo_valor = trim($parametros['carga_col_desc']);
		} else {
			$this->campo_valor = (isset($parametros['columna_proyecto'])) ? 2 : 1;
		}
		
		//Representacion a nivel datos de lo no_seteado
		$cant_claves = count($this->campos_clave);
		//¿Maneja más de un dato?
		if ($cant_claves > 1 || is_array($this->dato)) {
			$cant_datos = count($this->dato);
			if (!is_array($this->dato) || count($this->campos_clave) != $cant_datos) {
				throw new toba_error_def("EF: {$this->etiqueta}. La cantidad de claves ($cant_claves)
															tiene que corresponderse con la cantidad de datos manejados por el EF ($cant_datos)");
			}
			foreach ($this->dato as $dato){
				$this->estado_nulo[$dato] = null;
			}
		} else {
			$this->estado_nulo = null;	
		}
	}

	/**
	 * @ignore
	 */
	protected function analizar_cambio_solo_lectura()
	{
		//No tiene mucho de inteligente, solo bloquea las modificaciones.
		$sl_bloquea_modificacion = ($this->solo_lectura_modificacion && ($this->padre->get_grupo_eventos_activo() == 'cargado'));
		$no_es_fila_modelo_ml = ($this->get_fila_actual() !== '__fila__');
		$this->solo_lectura = ($this->solo_lectura_base || ($sl_bloquea_modificacion && $no_es_fila_modelo_ml));
	}
	//-----------------------------------------------------
	//---------- Propiedades ESTATICAS del ef -------------
	//-----------------------------------------------------	
		
	/**
	 * @ignore 
	 */
	static function get_lista_parametros()
	{
		return array();
	}	

	/**
	 * @ignore 
	 */	
	static function get_lista_parametros_carga()
	{
		return array();	
	}
	
	/**
	 * @ignore 
	 */	
	static function get_lista_parametros_carga_basico()
	{
		return array(
			'carga_metodo',
			'carga_clase',
			'carga_include',
			'carga_col_clave',
			'carga_col_desc',
			'carga_sql',
			'carga_dt',
			'carga_consulta_php',
			'carga_fuente',
			'carga_lista',
			'carga_maestros',
			'carga_cascada_relaj'
		);	
	}	
	
	/**
	 * El ef permite seleccionar valores o solo se pueden editar?
	 * @return boolean
	 */
	function es_seleccionable()
	{
		return false;
	}
	/**
	 * El ef permite seleccionable permite elegir más de un valor?
	 * @return boolean
	 */
	function permite_seleccion_multiple()
	{
		return false;
	}

	/**
	 * El ef maneja un único valor como estado? O maneja un arreglo de estados?
	 * @return boolean
	 */
	function es_estado_unico() 
	{
		return true;	
	}
	
	/**
	 * La carga de opciones de este ef depende de su estado actual?
	 * @return boolean
	 */
	function carga_depende_de_estado()
	{
		return false;	
	}	

	/**
	 * El ef maneja el concepto de etiqueta?
	 * @return boolean
	 */
	function tiene_etiqueta()
	{
		return true;	
	}
		
	static function set_excepciones($excepciones)
	{
		self::$_excepciones = $excepciones;
	}
	
	static function get_excepciones() 
	{
		return self::$_excepciones;
	}
		
	//-----------------------------------------------------
	//------ Propiedades relacionadas con la carga --------
	//-----------------------------------------------------		
	
	/**
	 * Retorna la lista de efs de los cuales depende 
	 * @return array Arreglo de identificadores de efs
	 */
	function get_maestros()
	{
		return $this->maestros;
	}	
	
	/**
	 * Retorna la/s columna/s clave/s del ef. 
	 * Esto está disponible cuando se brinda un mecanismo de carga asociado al ef.
	 * @return array
	 */
	function get_campos_clave()
	{
		return $this->campos_clave;
	}
	
	/**
	 * Retorna la columna 'valor' del ef
	 * Esto está disponible cuando se brinda un mecanismo de carga asociado al ef.
	 * @return mixed Nombre del campo definido como valor o descripción
	 */	
	function get_campo_valor()
	{
		return $this->campo_valor;	
	}
	
	/**
	 * Retorna true si tanto los campos clave como valor son posicionales
	 * @return boolean
	 */
	function son_campos_posicionales()
	{
		if (! is_numeric($this->campo_valor)) {
			return false;	
		}
		foreach ($this->campos_clave as $campo) {
			if (! is_numeric($campo)) {
				return false;	
			}
		}
		return true;
	}	
	
	/*
	 * Elimina un maestro particular de este ef de entre la lista
	 * @param string $maestro Id del maestro a ser eliminado
	 * @ignore
	 */
	function quitar_maestro($maestro)
	{
		$this->maestros = array_diff($this->maestros, array($maestro));
	}
	//-----------------------------------------------------
	//-------------- ACCESO a propiedades -----------------
	//-----------------------------------------------------	

	function clave_memoria($multiplexado=true)
	{
		if ($multiplexado) {
			return "ef_" . $this->id_form;
		} else {
			return "ef_" . $this->id_form_orig;
		}
	}	
	
	/**
	 * Si el ef permite seleccionar opciones, estas ya estan cargadas?
	 * @return boolean
	 */
	function tiene_opciones_cargadas()
	{
		return $this->opciones_cargadas;	
	}
	
	/**
	 * Un ef obligatorio lanza una excepción en PHP si su estado actual es nulo
	 * La obligatoriedad se define en el editor, aunque es posible modificarla durante un pedido de pagina específico
	 * @return boolean
	 * @see set_obligatorio	
	 */
	function es_obligatorio()
	{
		return $this->obligatorio;	
	}

	/**
	 * Retorna la clase css asociada a la etiqueta
	 * @return string
	 */
	function get_estilo_etiqueta()
	{
		return $this->estilo_etiqueta;	
	}

	/**
	 * El checkbox esta configurado en los ML para tener un tilde sel/des todos?
	 * @return boolean
	 */
	function get_toggle()
	{
		return $this->check_ml_toggle;
	}

	/**
	 * Un ef no expandido se muestra oculto en el layout del formulario.
	 * Para verlo el usuario explícitamente debe apretar un icono o vínculo.
	 * @return boolean
	 * @see set_expandido
	 */
	function esta_expandido()
	{
		return $this->expandido;
	}

	/**
	 * Devuelve el id del ef dentro del framework
	 * @return string
	 */
	function get_id()
	{
		return $this->id;
	}

	/**
	 * Retorna el texto de la etiqueta asociada
	 * @return string
	 */
	function get_etiqueta()
	{
		return $this->etiqueta;
	}

	/**
	 * Retorna la descripción o ayuda del ef.
	 * La descripción se muestra por defecto como un tooltip al lado de la etiqueta
	 * @return string
	 */
	function get_descripcion()
	{
		return trim($this->descripcion);	
	}
	
	/**
	 * El 'dato' del ef es la o las columnas de datos asociadas.
	 * Cuando al formulario se le pide un get_datos() este retorna como columnas los datos definidos en los efs
	 * @return mixed
	 */
	function get_dato()
	{
		return $this->dato;
	}

	/**
	 * Como el id html puede variar si se multiplexa el ef (caso formulario_ml), este metodo retorna el id original del ef
	 * @return string
	 */
	function get_id_form_orig()
	{
		return $this->id_form_orig;
	}	

	/**
	 * Retorna el id html del ef en el formulario
	 * @return string
	 */
	function get_id_form()
	{
		return $this->id_form;
	}	

	/**
	 * Retorna el valor o estado actual del ef
	 * @return mixed Si el ef maneja un unico dato el estado es un string, sino es un arreglo de strings
	 */
	function get_estado()
	{
		if ($this->tiene_estado()) {
			return $this->estado;			
		} else {
			return null;
		}
	}

	/**
	 * Retorna una descripción textual del estado.
	 * Para muchos efs la descripción es identica al estado (caso de un texto común por ejemplo), 
	 * pero para otros el estado es una clave interna distinta a su descripción
	 * @return string
	 */
	function get_descripcion_estado($tipo_salida)
	{
		$estado = $this->get_estado();
		switch ($tipo_salida) {
			case 'html':
			case 'impresion_html':
				return "<div class='{$this->clase_css}'>$estado</div>";
			break;
			case 'pdf':
			case 'xml':
				return $estado;
			case 'excel':
				return array($estado, null);
			break;
		}
		
	}
	
	/**
	 * Retorna true si el ef tiene un valor o estado distinto al nulo
	 */
	function tiene_estado()
	{
		return isset($this->estado) && ($this->estado !== apex_ef_no_seteado);
	}
	
	/**
	 * El ef tiene un valor positivo, similar a tiene_estado() pero puede ser mas restrictivo
	 */
	function seleccionado()
	{
		return $this->tiene_estado();
	}

	/**
	 * Retorna el valor del ef a su estado inicial.
	 * Si el ef no maneja un estado o valor por defecto, su valor sera NULL 
	 */
	function resetear_estado()
	{
		$this->set_estado($this->estado_defecto);
	}

	/**
	 * Chequea la validez del estado actual del ef
	 * @return mixed Retorna true cuando es valido y un string con el mensaje cuando es inválido
	 */
	function validar_estado()
	{
		$obligatorio = false;
		if ($this->obligatorio) {
			if (! $this->cascada_relajada) {
				$obligatorio = true;
			} else {
				$obligatorio = $this->padre->ef_tiene_maestros_seteados($this->id);
			}
			if ($this->obligatorio_oculto_relaj) {
				$obligatorio = false;	
			}
		}
		if ($obligatorio && !$this->tiene_estado()) {
			return "El campo es obligatorio";
		}
		return true;
	}
    
	/**
	 * Permite chequear si el valor del ef cae dentro de las excepciones
	 * @ignore
	 * @return boolean
	 */
	protected function confirma_excepcion_validacion()
	{
		if (isset(self::$_excepciones)) {			//Se fija si el valor es parte de las excepciones
			if (in_array($this->estado, self::$_excepciones)) {
				return true;
			}
		}			
		return false;		
	}
		
	/**
	 * Cambia el valor que toma el ef cuando no se lo carga con un estado
	 */
	function set_estado_defecto($estado)
	{
		$this->estado_defecto = $estado;
		if (! isset($this->estado)) {
			$this->estado = $estado;
		}
	}

	/**
	 * Permite normalizar un parametro recibido de una cascada a un formato estandar
	 * @param mixed $parametro Valor que se recibio por cascada
	 * @return mixed
	 */
	 function normalizar_parametro_cascada($parametro)
	 {
		 return $parametro;
	 }
	  
	//-----------------------------------------------------
	//-------------- CAMBIO DE PROPIEDADES -----------------
	//-----------------------------------------------------

	/**
	 * Multiplexa el ef (usado en el formulario_ml)
	 * Permite que una sola intancia de un objeto ef pueda ser utilizada para representar un conjunto de efs similares en estructura
	 */
	function ir_a_fila($agregado="")
	{
		$this->agregado_form = $agregado;
		$this->id_form = ($agregado !== '') ? $agregado . '_' . $this->id_form_orig :  $this->id_form_orig;
	}

	/**
	 * Obtiene la fila actual en el multiplexado en el ef (usado en el formulario_ml)
	 */	
	function get_fila_actual()
	{
		return $this->agregado_form;
	}

	/**
	 * Cambia la etiqueta actual del ef
	 * @param string $etiqueta
	 */
	function set_etiqueta($etiqueta)
	{
		$this->etiqueta = $etiqueta;
	}
	
	/**
	 * Cambia la descripción o ayuda del ef.
	 * La descripción se muestra por defecto como un tooltip al lado de la etiqueta
	 * @return string
	 */	
	function set_descripcion($descripcion)
	{
		$this->descripcion = $descripcion;
	}

	/**
	 * Cuando un ef se encuentra en solo_lectura su valor es visible al usuario pero no puede modificarlo.
	 * Notar que si un ef se fija solo_lectura en el servidor, este estado no puede variar en el cliente (javascript),
	 * Para armar lógica de cambio de solo_lectura en javascript utilizar la extensión javascript del componente usado
	 * @param boolean $solo_lectura Hacer o no solo lectura
	 */
	function set_solo_lectura($solo_lectura = true)
	{
		$this->solo_lectura_base = $solo_lectura;
		$this->analizar_cambio_solo_lectura();		//Se agrega para que en el ML el solo_lectura se pueda aplicar en distintas filas.
	}
    
	function es_solo_lectura()
	{
		$es_fila_modelo_ml = ($this->get_fila_actual() === '__fila__');
		if (! isset($this->solo_lectura) || $es_fila_modelo_ml) {		//Inicializo la variable o reanalizo su estado cuando se trata de la fila modelo del ML.
			$this->analizar_cambio_solo_lectura();
		}
		return $this->solo_lectura;
	}
	
	/**
	 * Cambia la obligatoriedad de un ef
	 * Notar que este cambio no se persiste para el siguiente pedido.
	 * Para cambiar la obligatoriedad durante todo un ciclo cliente-servidor usar {@link toba_ei_formulario::set_efs_obligatorios() set_efs_obligatorios del formulario}
	 * @param boolean $obligatorio
	 */
	function set_obligatorio($obligatorio = true)
	{
		$this->obligatorio = $obligatorio;
	}
    
	/**
	 * Cambia la clase css aplicada a la etiqueta
	 * @param string $estilo
	 */
	function set_estilo_etiqueta($estilo)
	{
		$this->estilo_etiqueta = $estilo;	
	}

	/**
	 * Determina si un ef se muestra o no expandido
	 * Un ef no expandido se muestra oculto en el layout del formulario.
	 * Para verlo el usuario explícitamente debe apretar un icono o vínculo.
	 * @param boolean $expandido
	 */
	function set_expandido($expandido)
	{
		$this->expandido = $expandido;
	}
	
	/**
	 * Cambia el valor o estado actual del ef
	 * @param mixed $estado
	 */
	function set_estado($estado)
	{
		if(isset($estado)){								
			$this->estado=$estado;
		} else {
			$this->estado = null;	
		}
	}

	/**
	 * Determina si el ef puede contener en su estado HTML, por defecto falso para evitar ataques de seguridad XSS
	 * @param boolean $permitir
	 */
	function set_permitir_html($permitir)
	{
		$this->permitir_html = $permitir;
	}

	/**
	 *  Expresa el tamaño del ef en cantidad de caracteres
	 * @param integer $tamanio
	 */
	function set_tamano($tamanio)
	{
		$this->tamano = $tamanio;
	}
		
	/**
	 * Cambia los iconos visibles a un lado del elemento
	 * @param array $iconos Arreglo de iconos que implementan toba_ef_icono_utileria
	 */
	function set_iconos_utilerias($iconos)
	{
		$this->iconos = array();
		foreach ($iconos as $icono) {
			$this->agregar_icono_utileria($icono);
		}
	}

	/**
	 * Agrega un icono con comportamiento al lado del elemento
	 */
	function agregar_icono_utileria(toba_ef_icono_utileria $icono)
	{
		$this->iconos[] = $icono;
	}

	function get_html_iconos_utilerias()
	{
		$salida = '';
		foreach ($this->iconos as $icono) {
			$salida .= $icono->get_html($this);
		}
		return $salida;
	}
	
	/**
	 * Carga el estado actual del ef a partir del $_POST dejado por este mismo componente en el pedido anterior
	 */
	function cargar_estado_post()
	{
		if (isset($_POST[$this->id_form])){
			$this->estado = $_POST[$this->id_form];
    	} else {
			$this->estado = null;
    	}
	}	
	
	function guardar_dato_sesion($dato, $multiplexado=false)
	{
		if (isset($dato)) {
			//toba::logger()->info("Guardando en ". $this->clave_memoria($multiplexado). ": ". var_export($dato, true));
			toba::memoria()->set_dato_operacion($this->clave_memoria($multiplexado), $dato);
		} else {
			//toba::logger()->info("Eliminando en ". $this->clave_memoria($multiplexado));
			toba::memoria()->eliminar_dato_operacion($this->clave_memoria($multiplexado));
		}		
	}
	
	//-----------------------------------------------------
	//-------------------- JAVASCRIPT ---------------------
	//-----------------------------------------------------
	
	/**
	 * Retorna la sentencia de creación del objeto javascript que representa al EF
	 */
	function crear_objeto_js()
	{
		return "new ef({$this->parametros_js()})";
	}

	/**
	 * Retorna el nombre de la instancia del objeto en javascript
	 * Ej: alert({$ef->objeto_js()}.valor())
	 */
	function objeto_js()
	{
		return $this->padre->get_objeto_js_ef($this->id);
	}

	/**
	 * Lista de parametros necesarios para el constructor del objeto en javascript
	 */
	protected function parametros_js()
	{
		$obligatorio = ( $this->obligatorio ) ? "true" : "false";
		$oculto_relaj = ($this->obligatorio_oculto_relaj) ? "true" : "false";
		$relajado = ( $this->cascada_relajada ) ? "true" : "false"; 
		$colapsable = ( $this->expandido ) ? "false" : "true";
		$etiqueta = str_replace("/", "\\/", $this->etiqueta);
		$etiqueta = str_replace(array('"', "'"), '', $etiqueta);
		return "'{$this->id_form_orig}', '$etiqueta', [$obligatorio, $relajado, $oculto_relaj], $colapsable";
	}

	/**
	 * Esta funcion permite que un EF declare la necesidad de incluir
	 * codigo javascript necesario para su correcto funcionamiento
	 * @return array Arreglo (sin extension .js) de los consumos
	 */
	function get_consumo_javascript()
	{
		return array("efs/ef");
	}	

	/**
	 * Determina el codigo personalizado a ejecutar cuando el ef cambia de valor en el cliente.
	 * Por ejemplo en el onchange de los input html
	 */
	function set_cuando_cambia_valor($js)
	{
		$this->cuando_cambia_valor = $js;
	}		

	/**
	 * Retorna el js utilizado cuando el ef cambia de valor en el cliente
	 */
	protected function get_cuando_cambia_valor()
	{
		//--- Se llama a $this->objeto_js para que los ML se posicionen en la fila correcta
		$js = $this->objeto_js().";".$this->cuando_cambia_valor;		
		return $js;
	}	
	
	//-----------------------------------------------------
	//-------------------- INTERFACE ----------------------
	//-----------------------------------------------------

	/**
	 * Genera el HTML del elemento
	 */
	abstract function get_input();
	
	/**
	 * Retorna la referencia al componente padre o formulario
	 * @return toba_formulario
	 */
	function controlador()
	{
		return $this->padre;	
	}
	
}

?>
