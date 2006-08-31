<?
require_once("nucleo/lib/interface/form.php");// Elementos STANDART de formulario

define("apex_ef_no_seteado","nopar");// Valor que debe ser considerado como NO ACTIVADO, si se cambia cambiar en las clases JS
define("apex_ef_valor_oculto", "#oculto#"); // Valor que debe ser considerado como SOLO DISPONIBLE EN SERVER
define("apex_ef_cascada","%");	//Mascara para reemplazar el valor de una dependencia en un SQL

require_once("toba_ef_combo.php");
require_once("toba_ef_editable.php");
require_once("toba_ef_oculto.php");
require_once("toba_ef_popup.php");
require_once("toba_ef_varios.php");
require_once("toba_ef_multi_seleccion.php");
require_once("toba_ef_sin_estado.php");
require_once("toba_ef_upload.php");

abstract class ef
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
	protected $solo_lectura;      	// Flag que indica si el objeto se debe deshabilitar cuando se muestra
	protected $javascript="";			// Javascript del elemento de formulario
	protected $input_extra = "";		// Parametros adicionales
	protected $expandido = true;
	protected $ancho_etiqueta = 150;
	protected $estilo_etiqueta = '';
	protected $agregado_form;			//Número de linea en un form multilinea	
	
	//--- DEPENDENCIAS ---
	protected $cascada_relajada = false;
	protected $cuando_cambia_valor = '';	//Js que se dispara cuando cambia el valor del ef
	protected $maestros = array();			// Array Ids de EFs MAESTROS
			
	//---- Metodo de carga
	protected $campos_clave = array(0);
	protected $campo_valor;
	protected $opciones_cargadas = false;
	
	function __construct($padre,$nombre_formulario,$id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		$this->id = $id;		
		$this->padre = $padre;
		$this->id_form_orig = "ef_" . $this->padre->get_id_form() . $this->id;
		$this->ir_a_fila();
		$this->etiqueta = $etiqueta;
		$this->descripcion = $descripcion;
     	$this->dato = $dato;
     	$this->obligatorio = $obligatorio;
		
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
		//Solo Lectura
		if ((isset($parametros["solo_lectura"]))&&($parametros["solo_lectura"]==1)) {
			$this->solo_lectura = true;
		} else {
			$this->solo_lectura = false;
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
				throw new toba_excepcion_def("EF: {$this->etiqueta}. La cantidad de claves ($cant_claves)
															tiene que corresponderse con la cantidad de datos manejados por el EF ($cant_datos)");
			}
			foreach ($this->dato as $dato){
				$this->estado_nulo[$dato] = null;
			}
		} else {
			$this->estado_nulo = null;	
		}
		
	}

	static function get_lista_parametros()
	{
		return array();
	}	
		
	static function get_lista_parametros_carga()
	{
		return array();	
	}
	
	static function get_lista_parametros_carga_basico()
	{
		return array(
			'carga_metodo',
			'carga_clase',
			'carga_include',
			'carga_col_clave',
			'carga_col_desc',
			'carga_sql',
			'carga_fuente',
			'carga_lista',
			'carga_maestros',
			'carga_cascada_relaj'
		);	
	}	
	
	//-----------------------------------------------------
	//-------------- ACCESO a propiedades -----------------
	//-----------------------------------------------------	
    
	function tiene_opciones_cargadas()
	{
		return $this->opciones_cargadas;	
	}
	
    function es_obligatorio()
    {
    	return $this->obligatorio;	
    }
    
    function get_estilo_etiqueta()
    {
    	return $this->estilo_etiqueta;	
    }

	function esta_expandido()
	{
		return $this->expandido;
	}

	function get_id()
	{
		return $this->id;
	}

	function get_etiqueta()
	{
		return $this->etiqueta;
	}

	function get_descripcion()
	{
		return trim($this->descripcion);	
	}
	
	function get_dato()
	{
		return $this->dato;
	}

	function get_id_form_orig()
	{
		return $this->id_form_orig;
	}	

	function get_id_form()
	{
		return $this->id_form;
	}	
	
	function get_estado()
	{
		if ($this->tiene_estado()) {
			return $this->estado;			
		} else {
			return null;
		}
	}

	function get_descripcion_estado()
	{
		return $this->get_estado();
	}

	function tiene_estado()
	{
		return isset($this->estado) && ($this->estado !== apex_ef_no_seteado);
	}
	
	/**
	 * El ef tiene un valor positivo, parecido a tiene_estado() pero puede ser mas restrictivo
	 */
	function seleccionado()
	{
		return $this->tiene_estado();
	}

	function resetear_estado()
	{
		$this->set_estado($this->estado_defecto);
	}

    function validar_estado()
    {
    	$obligatorio = false;
    	if ($this->obligatorio) {
    		if (! $this->cascada_relajada) {
    			$obligatorio = true;
    		} else {
    			$obligatorio = $this->padre->ef_tiene_maestros_seteados($this->id);
    		}
    	}
        if ($obligatorio && !$this->tiene_estado()) {
			return "El campo es obligatorio";
        }
        return true;
    }
	
	function get_maestros()
	{
		return $this->maestros;
	}	
	
	function es_seleccionable()
	{
		return false;
	}
	
	function es_estado_unico() 
	{
		return true;	
	}
	
	function carga_depende_de_estado()
	{
		return false;	
	}

	function tiene_etiqueta()
	{
		return true;	
	}
	
	function get_campos_clave()
	{
		return $this->campos_clave;
	}
	
	function get_campo_valor()
	{
		return $this->campo_valor;	
	}
	
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

	//-----------------------------------------------------
	//-------------- CAMBIO DE PROPIEDADES -----------------
	//-----------------------------------------------------

	function ir_a_fila($agregado="")
	{
		$this->agregado_form = $agregado;
		$this->id_form = $this->id_form_orig . $agregado;
	}
    
	function set_etiqueta($etiqueta)
	{
		$this->etiqueta = $etiqueta;
	}
	
	function set_descripcion($descripcion)
	{
		$this->descripcion = $descripcion;
	}

	function set_solo_lectura($solo_lectura = true)
	{
        $this->solo_lectura = $solo_lectura;
    }
	
    function set_obligatorio($obligatorio = true)
    {
	    $this->obligatorio = $obligatorio;
    }
    
    function set_estilo_etiqueta($estilo)
    {
    	$this->estilo_etiqueta = $estilo;	
    }

	function set_expandido($expandido)
	{
		$this->expandido = $expandido;
	}
	
	function set_estado($estado)
	{
   		if(isset($estado)){								
    		$this->estado=$estado;
			return true;
	    } else {
	    	$this->estado = null;	
	    }
	    return false;
	}

	function cargar_estado_post()
	{
		if (isset($_POST[$this->id_form])){
			$this->estado = $_POST[$this->id_form];
			return true;
    	} else {
			$this->estado = null;
			return false;
    	}
	}	
	
	//-----------------------------------------------------
	//-------------------- JAVASCRIPT ---------------------
	//-----------------------------------------------------
	
	function crear_objeto_js()
	//Retorna la sentencia de creación del objeto javascript que representa al EF
	{
		return "new ef({$this->parametros_js()})";
	}
	
	function objeto_js()
	//Retorna el nombre de la instancia del objeto en javascript
	//Ej: alert({$ef->objeto_js()}.valor())
	{
		return $this->padre->get_objeto_js_ef($this->id);
	}
	
	protected function parametros_js()
	{
		$obligatorio = ( $this->obligatorio ) ? "true" : "false";
		$relajado = ( $this->cascada_relajada ) ? "true" : "false"; 
		$colapsable = ( $this->expandido ) ? "false" : "true";
		$etiqueta = str_replace("/", "\\/", $this->etiqueta);
		return "'{$this->id_form_orig}', '$etiqueta', [$obligatorio, $relajado], $colapsable";
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
	
	function set_cuando_cambia_valor($js)
	{
		$this->cuando_cambia_valor = $js;
	}		
	
	protected function get_cuando_cambia_valor()
	{
		//--- Se llama a $this->objeto_js para que los ML se posicionen en la fila correcta
		$js = $this->objeto_js().";".$this->cuando_cambia_valor;		
		return $js;
	}	
	
	//-----------------------------------------------------
	//-------------------- INTERFACE ----------------------
	//-----------------------------------------------------

	abstract function get_input();
	
}
//########################################################################################################
//########################################################################################################
?>
