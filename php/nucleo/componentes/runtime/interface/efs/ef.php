<?
require_once("nucleo/lib/form.php");// Elementos STANDART de formulario

define("apex_ef_no_seteado","nopar");// Valor que debe ser considerado como NO ACTIVADO, si se cambia cambiar en las clases JS
define("apex_ef_separador","||");
define("apex_ef_valor_oculto", "#oculto#"); // Valor que debe ser considerado como SOLO DISPONIBLE EN SERVER
define("apex_ef_cascada","%");	//Mascara para reemplazar el valor de una dependencia en un SQL

require_once("ef_combo.php");
require_once("ef_editable.php");
require_once("ef_oculto.php");
require_once("ef_popup.php");
require_once("ef_varios.php");
require_once("ef_multi_seleccion.php");
require_once("ef_sin_estado.php");
require_once("ef_upload.php");

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
	
	function __construct($padre,$nombre_formulario,$id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		$this->id = $id;				
		//--- Esto es para tener compatibilidad para atras		
		if (is_object($padre)) {
			$this->padre = $padre;
			$this->id_padre = $padre->get_id();
		} else {
			$this->id_padre = $padre;	
		}
		$this->id_form_orig = "ef_" . $this->id_padre[1] . $this->id;
		$this->ir_a_fila();
		$this->etiqueta = $etiqueta;
		$this->descripcion = $descripcion;
     	$this->dato = $dato;
     	$this->obligatorio = $obligatorio;
		
		//---- Declaracion de dependencias
		if (isset($parametros["dependencias"])) {
			if($parametros["dependencias"]!=""){
				$this->maestros = explode(",",$parametros["dependencias"]);
				$this->maestros = array_map("trim",$this->maestros);
			}
			if (isset($parametros['cascada_relajada']) && $parametros['cascada_relajada']) {
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
		if (isset($parametros['clave'])) {
			$campos_clave = explode(",", $parametros["clave"]);
			$this->campos_clave = array_map("trim", $campos_clave);
		} else {
			if (isset($parametros['columna_proyecto'])) {
				$this->campos_clave = array(0,1);	
			}
		}
		if (isset($parametros['valor'])) {
			$this->campo_valor = trim($parametros['valor']);
		} else {
			$this->campo_valor = (isset($parametros['columna_proyecto'])) ? 2 : 1;
		}
		
		//Representacion a nivel datos de lo no_seteado
		$cant_claves = count($this->campos_clave);
		//¿Maneja más de un dato?
		if ($cant_claves > 1 || is_array($this->dato)) {
			$cant_datos = count($this->dato);
			if (!is_array($this->dato) || count($this->campos_clave) != $cant_datos) {
				throw new excepcion_toba_def("EF: {$this->etiqueta}. La cantidad de claves ($cant_claves)
															tiene que corresponderse con la cantidad de datos manejados por el EF ($cant_datos)");
			}
			foreach ($this->dato as $dato){
				$this->estado_nulo[$dato] = null;
			}
		} else {
			$this->estado_nulo = null;	
		}
		
	}

	static function get_parametros()
	{
		return array();
	}
	
	static function get_parametros_carga()
	{
		$parametros = array();
		$parametros["dao"]["descripcion"]="Metodo a ejecutar para recuperar datos.";
		$parametros["dao"]["opcional"]=0;	
		$parametros["dao"]["etiqueta"]="DAO - Metodo";	
		$parametros["clase"]["descripcion"]="Nombre de la clase";
		$parametros["clase"]["opcional"]=1;	
		$parametros["clase"]["etiqueta"]="DAO - Clase";	
		$parametros["include"]["descripcion"]="Archivo donde se encuentra definida la clase";
		$parametros["include"]["opcional"]=1;	
		$parametros["include"]["etiqueta"]="DAO - Include";	
		$parametros["clave"]["descripcion"]="Indica que INDICES de la matriz recuperada se utilizaran como CLAVE (Si son varios separar con comas)";
		$parametros["clave"]["opcional"]=0;	
		$parametros["clave"]["etiqueta"]="DAO - resultado: CLAVE";	
		$parametros["valor"]["descripcion"]="Indica que INDICE de la matriz recuperada se utilizara como DESCRIPCION";
		$parametros["valor"]["opcional"]=0;	
		$parametros["valor"]["etiqueta"]="DAO - resultado: DESC.";	
		$parametros["sql"]["descripcion"]="Query que carga al combo. Si hay condiciones dinámicas que inciden en el where, indicar el mismo con %w%";
		$parametros["sql"]["opcional"]=0;	
		$parametros["sql"]["etiqueta"]="SQL";	
		$parametros["fuente"]["descripcion"]="(Util solo si existe [sql]) Fuente a utilizar para ejecutar el SQL.";
		$parametros["fuente"]["opcional"]=1;	
		$parametros["fuente"]["etiqueta"]="SQL: fuente";
		$parametros["columna_proyecto"]["descripcion"]= "Columna de la tabla que representa el proyecto";
		$parametros["columna_proyecto"]["opcional"]=0;	
		$parametros["columna_proyecto"]["etiqueta"]= "Columna del proyecto";
		$parametros["columna_clave"]["etiqueta"]="SQL: Columna clave a filtrar";
		$parametros["columna_clave"]["descripcion"]="Permite incidir en el where de la consulta (necesita tener un %w% indicando donde se encuentra el where) comparando una columna con el estado actual del ef.";
		$parametros["columna_clave"]["opcional"]=1;			
		$parametros["incluir_toba"]["descripcion"]= "¿Hay que listar a toba entre los proyectos?";
		$parametros["incluir_toba"]["opcional"]=0;	
		$parametros["incluir_toba"]["etiqueta"]= "Incluir Toba";
		$parametros['lista']['descripcion'] = "La clave/valor se separa con el caracter [/] y los pares con el caracter [,]";
		$parametros['lista']['opcional'] = 0;
		$parametros['lista']['etiqueta'] = "Lista de valores";	
		$parametros["dependencias"]["descripcion"]="El estado dependende de otro EF (CASCADA). Lista de EFs separada por comas";
		$parametros["dependencias"]["opcional"]=1;	
		$parametros["dependencias"]["etiqueta"]="Dependencias";
		$parametros["cascada_relajada"]["descripcion"]="Cuando el ef tiene maestros y alguno de estos no está activo se relaja la obligatoriedad";
		$parametros["cascada_relajada"]["opcional"]=1;
		$parametros["cascada_relajada"]["etiqueta"]="Cascada relaja obligatoriedad";
		return $parametros;
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
	//-------------- ACCESO a propiedades -----------------
	//-----------------------------------------------------	
    
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
		if ($this->activado()) {
			return $this->estado;			
		} else {
			return null;
		}
	}

	function get_descripcion_estado()
	{
		return $this->get_estado();
	}

	function activado()
	{
		return isset($this->estado) && ($this->estado !== apex_ef_no_seteado);
	}
	
	/**
	 * El ef tiene un valor positivo, parecido a activado() pero puede ser mas restrictivo
	 */
	function seleccionado()
	{
		return $this->activado();
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
        if ($obligatorio && !$this->activado()) {
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
		return "'{$this->id_form_orig}', '{$this->etiqueta}', [$obligatorio, $relajado], $colapsable";
	}

	/**
	 * Esta funcion permite que un EF declare la necesidad de incluir
	 * codigo javascript necesario para su correcto funcionamiento
	 * @return array Arreglo (sin extension .js) de los consumos
	 */
	function get_consumo_javascript()
	{
		return array("interface/ef");
	}	
	
	function set_cuando_cambia_valor($js)
	{
		$this->cuando_cambia_valor = $js;
	}		
	
	protected function get_cuando_cambia_valor()
	{
		$js = '';
		return $js . $this->cuando_cambia_valor;
	}	
	
	//-----------------------------------------------------
	//-------------------- INTERFACE ----------------------
	//-----------------------------------------------------

	abstract function get_input();
	
}
//########################################################################################################
//########################################################################################################
?>
