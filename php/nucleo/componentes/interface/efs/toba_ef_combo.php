<?php

/**
 * Clase base de los elementos de formulario que permiten seleccionar un único elemento
 * @package Componentes
 * @subpackage Efs
 */
abstract class toba_ef_seleccion extends toba_ef
{
	protected $opciones;
	protected $predeterminado;		//Si el combo tiene predeterminados, tengo que inicializarlo
	protected $no_seteado;
	protected $categorias;
	protected $estado_nulo = null;
	protected $estado_defecto;

    static function get_lista_parametros_carga()
    {
    	$param = toba_ef::get_lista_parametros_carga_basico();    	
		$param[] = 'carga_no_seteado';
		$param[] = 'carga_no_seteado_ocultar';
		return $param;
    }
	
	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
		
		//---------------------- Manejo de Estado por defecto  ------------------		
		if (isset($parametros['estado_defecto']) && $parametros['estado_defecto']!="") {
			if (is_array($this->dato)) {
				$this->estado_defecto = array();
				$param = explode(',', $parametros['estado_defecto']);
				for ($i=0; $i < count($this->dato); $i++) {
					$this->estado_defecto[$this->dato[$i]] = trim($param[$i]);
				}
			} else {
				$this->estado_defecto = trim($parametros['estado_defecto']);	
			}
		}		
		
		if (! isset($this->estado_defecto)) {
			$this->estado_defecto = $this->estado_nulo;
		}	
		$this->set_estado($this->estado_defecto);

	}

	function es_seleccionable()
	{
		return true;
	}
	
	/**
	 * Cambia el conjunto de opciones disponibles para que el usuario seleecione
	 * @param array $datos Arreglo asociativo clave => valor. Si es null se asume que el ef esta temporalmente deshabilitado
	 */
	function set_opciones($datos, $maestros_cargados=true)
	{
		$this->opciones_cargadas = true;
		if (! $maestros_cargados) {
			$this->input_extra = " disabled ";
		}
		$this->opciones = $datos;
		
		//	Verifica que si existe estado, este este contemplado en las opciones,
		//	Sino se hace este chequeo existe la posibilidad de que se haga get_estado para el cascada por ej
		//	Y termine brindando un dato que no existe
		$actual = $this->get_estado_para_input();
		if ($actual !== apex_ef_no_seteado && ! isset($this->opciones[$actual])) {
			$this->estado = $this->estado_nulo;
			toba::logger()->warning("Se resetea el estado del ef '{$this->id}' debido a que su estado actual ('$actual') no está cotemplado en las opciones");
		}
	}

	function get_estado()
	{
		return $this->estado;
	}	

	/**
	 * Retorna la descripción asociada a la opción actualmente seleccionada
	 */
	function get_descripcion_estado()
	{
		if ( isset( $this->estado ) && isset( $this->opciones[ $this->estado ] ) ) {
			return $this->opciones[ $this->estado ];
		} else {
			return null;	
		}
	}

	protected function es_estado_nulo($estado)
	{
		if (is_array($this->dato)) {
			//Si el estado es nulo tengo que manejarlo de una forma especial
			$valores = "";
			foreach ($estado as $valor) {
				$valores .= $valor;
			}		
			return trim($valores) === '';
		} else {
			return $estado === null;	
		}
	}

	function es_estado_no_seleccionado()
	{
		return $this->estado == apex_ef_no_seteado;		
	}

	protected function get_estado_para_input()
	{
		if ($this->es_estado_nulo($this->estado)) {
			return apex_ef_no_seteado;	
		}
		if (is_array($this->dato)) {
			//Maneja muchos datos
			$opcion = "";
		    foreach ($this->dato as $dato) { //Sigo el orden de las columnas
	        	$opcion .= $this->estado[$dato] . apex_qs_separador;
		    }
		   //Saca el ultimo apex_qs_separador
			$opcion = substr($opcion, 0, -1 * strlen(apex_qs_separador));
			return $opcion;
		} else {
			return $this->estado;				
		}
	}

	function set_estado($estado)
	{
		if (isset($estado) && is_array($this->dato)) {
			//Maneja multiples datos
			//El estado tiene el formato adecuado?
			$cant_datos = count($this->dato);
			if (count($estado) <> $cant_datos) {
				throw new toba_error("Ha intentado cargar el combo '{$this->id}' con un array que posee un formato inadecuado " .
								" se esperaban {$cant_datos} claves, pero se utilizaron: ". count($estado) . ".");
			}
		}
		if ($this->es_estado_nulo($estado)) {
			$this->estado = $this->estado_nulo;
		} else {
    		$this->estado = $estado;
		}
	}
	
	function cargar_estado_post()
	{
		if (! isset($_POST[$this->id_form])) {
			return false;
		}
		if (is_array($this->dato)) {
            //Deduzco el estado de la opcion seleccionada
   			$seleccion = $_POST[$this->id_form];
			if ($seleccion == apex_ef_no_seteado){
				$this->estado = $this->estado_nulo;
			} else {
				$cant_datos = count($this->dato);
	            $valores = explode(apex_qs_separador, $seleccion);
				if (count($valores) <> $cant_datos) {
					throw new toba_error("Ha intentado cargar el combo '{$this->id}' con un array que posee un formato inadecuado " .
									" se esperaban {$cant_datos} claves, pero se utilizaron: ". count($valores) . ".");
				}
				$this->estado = array();
				for ($i=0; $i < count($this->dato); $i++) {
				   	$this->estado[$this->dato[$i]] = $valores[$i];
				}
			}
		} else {
			$estado = $_POST[$this->id_form];
			if ($estado == apex_ef_no_seteado) {
				$estado = null;	
			}
			$this->set_estado($estado);
		}
		return true;
	}
	
	function tiene_estado()
	{
		return isset($this->estado) && !$this->es_estado_nulo($this->estado);
	}

	function get_consumo_javascript()
	{
		$consumos = array('efs/ef','efs/ef_combo');
		return $consumos;
	}	
	
	
}
//########################################################################################################
//########################################################################################################

/**
 * Combo equivalente a un <select> en HTML 
 * @package Componentes
 * @subpackage Efs
 * @jsdoc ef_combo ef_combo 
 */
class toba_ef_combo extends toba_ef_seleccion
{

	function get_input()
	{
		$html = "";

		//El estado que puede contener muchos datos debe ir en un unico string
		$estado = $this->get_estado_para_input();
        if ($this->solo_lectura) {
        	$input = toba_form::select("",$estado, $this->opciones, "ef-combo", "disabled");	
			$input .= toba_form::hidden($this->id_form, $estado);
            return $input;
		} else {
			$tab = $this->padre->get_tab_index();
			$extra = " tabindex='$tab'";
			$js = '';

			if ($this->cuando_cambia_valor != '') {
				$js = "onchange=\"{$this->get_cuando_cambia_valor()}\"";
			}
			$html .= toba_form::select($this->id_form, $estado ,$this->opciones, 'ef-combo', $js . $this->input_extra.$extra, $this->categorias);
			return $html;
		}
	}	

	function crear_objeto_js()
	{
		return "new ef_combo({$this->parametros_js()})";
	}
}

//########################################################################################################
//########################################################################################################


/**
 * Radio buttons equivalentes a <input type='radio'>
 * @package Componentes
 * @subpackage Efs
 * @jsdoc ef_radio ef_radio
 */
class toba_ef_radio extends toba_ef_seleccion 
{
	protected $cantidad_columnas = 1;	
	
    static function get_lista_parametros()
    {
    	$param = toba_ef_seleccion::get_lista_parametros();
    	$param[] = 'selec_cant_columnas';
    	return $param;
    }
    
	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
		if (isset($parametros['selec_cant_columnas'])) {
			$this->cantidad_columnas = $parametros['selec_cant_columnas'];
		}
	}    
	
	function set_cantidad_columnas($cantidad)
	{
		$this->cantidad_columnas = $cantidad;
	}	
	
	function get_input()
	{
		$estado = $this->get_estado_para_input();
		$html = '';
		if ($this->solo_lectura) {
			$html .= toba_form::hidden($this->id_form, $estado);			
		}
		$callback = "onchange=\"{$this->get_cuando_cambia_valor()}\"";
		//--- Se guarda el callback en el <div> asi puede ser recuperada en caso de que se borren las opciones
		$html .= "<div id='opciones_{$this->id_form}' $callback>\n";
		$html .= "<table>\n";
    	if (!is_array($this->opciones)) {
    		$datos = array();	
    	} else {
    		$datos = $this->opciones;	
    	}
		$i=0;
		$tab_index = "tabindex='".$this->padre->get_tab_index()."'";
    	foreach ($datos as $clave => $valor) {
    		if ($i % $this->cantidad_columnas == 0) {
    			$html .= "<tr>\n";	
    		}
	    	$id = $this->id_form . $i;    		
	    	$html .= "\t<td><label class='ef-radio' for='$id'>";
	    	$es_actual = (strval($estado) == strval($clave));
			if (! $this->solo_lectura) {
	    		$sel = ($es_actual) ? "checked" : "";
	    		$clave = htmlentities($clave, ENT_QUOTES);
				$html .= "<input type='radio' id='$id' name='{$this->id_form}' value='$clave' $sel $callback $tab_index />";
				$tab_index = '';
			} else {
				//--- Caso solo lectura
				$img = ($es_actual) ? 'efradio_on.gif' : 'efradio_off.gif';
				$html .= toba_recurso::imagen_toba('nucleo/'.$img,true,16,16);
			}
			$valor = htmlentities($valor, ENT_QUOTES);
			$html .= "$valor</label></td>\n";			
    		$i++;
    		if ($i % $this->cantidad_columnas == 0) {
    			$html .= "</tr>\n";	
    		}    		
    	}
    	$sobran = $i % $this->cantidad_columnas;
    	if ($sobran > 0) {
    		$html .= str_repeat("\t<td></td>\n", $sobran);
    		$html .= "</tr>\n";	
    	}
		$html .= "</table></div>\n";    	
		return $html;
	}
		
	function crear_objeto_js()
	{
		return "new ef_radio({$this->parametros_js()}, $this->cantidad_columnas)";
	}	
}

?>
