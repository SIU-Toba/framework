<?php

/**
 * Elemento que permite la selección de varios valores. Clase base abstracta
 * @package Componentes
 * @subpackage Efs
 * @jsdoc ef_multi_seleccion ef_multi_seleccion
 */
abstract class toba_ef_multi_seleccion extends toba_ef
{
	protected $opciones = array();
	protected $tamanio;
	protected $ancho;
	protected $estado_nulo = array();
	protected $serializar = false;	
	protected $mostrar_utilidades;
		
	//parametros validación
	protected $cant_maxima;
	protected $cant_minima;
		
	static function get_lista_parametros()
	{
		$param[] = 'selec_cant_minima';
		$param[] = 'selec_cant_maxima';
		$param[] = 'selec_utilidades';
		$param[] = 'selec_serializar';
		return $param;    	
	}

	static function get_lista_parametros_carga()
	{
		return toba_ef::get_lista_parametros_carga_basico();	
	}
	
	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);		
		if (isset($parametros['selec_utilidades'])) {
			$this->mostrar_utilidades = $parametros['selec_utilidades'];
		} else { 
			$this->mostrar_utilidades = false;
		}		
		if(isset($parametros['selec_tamano'])) {
			$this->tamanio = $parametros['selec_tamano'];
			unset($parametros['selec_tamano']);
		}
		if(isset($parametros["selec_cant_maxima"])) {
			$this->cant_maxima = $parametros['selec_cant_maxima'];
			unset($parametros['selec_cant_maxima']);
		}
		if(isset($parametros['selec_cant_minima'])) {
			$this->cant_minima = $parametros['selec_cant_minima'];
			unset($parametros['selec_cant_minima']);
		}
		if(isset($parametros['selec_ancho'])) {
			$this->ancho = $parametros['selec_ancho'];
			unset($parametros['selec_ancho']);
		}		
		if(isset($parametros["selec_serializar"]) && $parametros["selec_serializar"] != 0) {
			$this->serializar = ',';
		}				
		
		//---------------------- Manejo de Estado por defecto  ------------------		
		if (isset($parametros['estado_defecto']) && $parametros['estado_defecto']!="") {
			$estados = explode(',', $parametros['estado_defecto']);
			$estados = array_map('trim', $estados);
			if (is_array($this->dato)) {
				$this->estado_defecto = array();
				$actual = 0;
				foreach ($estados as $estado) {
					$param = explode('/', $parametros['estado_defecto']);
					$parm = array_map('trim', $param);
					for ($i=0; $i < count($this->dato); $i++) {
						$this->estado_defecto[$actual][$this->dato[$i]] = trim($param[$i]);
					}
					$actual++;
				}
			} else {
				$this->estado_defecto = $estados;	
			}
		}		
		
		$this->estado_nulo = array();
		if (! isset($this->estado_defecto)) {
			$this->estado_defecto = $this->estado_nulo;
		}	
		$this->set_estado($this->estado_defecto);
	}
	
	function tiene_estado()
	{
		return isset($this->estado) && !$this->es_estado_nulo($this->estado);
	}	
	
	function set_opciones($datos, $maestros_cargados=true, $tiene_maestros=false)
	{
		$this->opciones_cargadas = true;		
		if (!isset($datos)) {
			$datos = array();	
		}
		$this->opciones = $datos;
		
		//--Guarda en sesion las opciones disponibles
		$sesion = isset($this->opciones) ? array_keys($this->opciones) : null;
		//Se guarda multiplexado si lo está el ef, y además tiene maestros, sino es una sola carga de opciones para todas las filas
		$fila_actual = $this->get_fila_actual();
		$this->guardar_dato_sesion($sesion, $tiene_maestros && isset($fila_actual));
	}
	
	protected function es_estado_individual_nulo($estado)
	{
		if (is_array($this->dato)) {
			if ($estado === null) {
				return true;	
			}
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
	
	protected function es_estado_nulo($estado)
	{
		if (!isset($estado)) {
			return true;	
		}
		if (is_array($estado) && empty($estado)) {
			return true;	
		}
		return false;
	}

	protected function validar_estado_particular($estado)
	{
		if (is_array($this->dato)) {
			//Maneja multiples datos
			//El estado tiene el formato adecuado?
			$cant_datos = count($this->dato);
			if (count($estado) <> $cant_datos) {
				throw new toba_error_def("Ha intentado cargar el ef '{$this->id}' con un array que posee un formato inadecuado " .
								" se esperaban {$cant_datos} claves, pero se utilizaron: ". count($estado) . ".");
			}
		}								
	}
	
	function set_estado($estado)
	{
		if ($this->serializar !== false && is_scalar($estado)) {
			$estado = explode($this->serializar, $estado);
			$estado = array_map('trim', $estado);
		}
		if ($this->es_estado_nulo($estado)) {
			$this->estado = $this->estado_nulo;
		} else {
			foreach ($estado as $elem) {
				$this->validar_estado_particular($elem);
			}
			$this->estado = $estado;
		}
	}
	
	function cargar_estado_post()
	{
		if (! isset($_POST[$this->id_form])) {
			$this->estado = $this->estado_nulo;
			return false;
		}
		$estado = $_POST[$this->id_form];
		if (! is_array($estado)) {
			throw new toba_error_seguridad("Se esperaba un arreglo, se recibio ".var_export($estado, true));
		}
		
		//-- Chequeo de seguridad que lo que viene es parte de lo que se ofrecio
		$globales = toba::memoria()->get_dato_operacion($this->clave_memoria(false));
		$por_fila = toba::memoria()->get_dato_operacion($this->clave_memoria(true));
		foreach ($estado as $valor) {
			//toba::logger()->info("Cotejando $valor en ".$this->clave_memoria(false)." contra ".var_export($globales, true));
			if (!isset($globales) || ! in_array($valor, $globales)) {
				//Busca los valores disponibles en la fila actual
				if (!isset($por_fila) || ! in_array($valor, $por_fila)) {
					//toba::logger()->info("Fallback Cotejando $valor en ".$this->clave_memoria(true)." contra ".var_export($por_fila, true));				
					throw new toba_error_seguridad("El ef '{$this->id}' no posee a la opción '$valor' entre las enviadas");
				}
			}
		}
				
		if (! is_array($this->dato)) {
			$this->estado = $estado;
		} else {
			$cant_datos = count($this->dato);
			$this->estado = array();
			foreach ($estado as $seleccion) {
	            $valores = explode(apex_qs_separador, $seleccion);
				if (count($valores) <> $cant_datos) {
					throw new toba_error_def("Ha intentado cargar el ef '{$this->id}' con un array que posee un formato inadecuado " .
									" se esperaban {$cant_datos} claves, pero se utilizaron: ". count($valores) . ".");
				}
				$nuevo = array();
				for ($i=0; $i < count($this->dato); $i++) {
				   	$nuevo[$this->dato[$i]] = $valores[$i];
				}
				$this->estado[] = $nuevo;
			}
		}
		return true;
	}

	/**
	 * La validación verifica si se cumple con la cantidad mínima y máxima
	 */
	function validar_estado()
	{
		$padre = parent::validar_estado();
		if ($padre !== true) {
			return $padre;	
		}
		if ($this->confirma_excepcion_validacion()) {
			return true;
		}
		if (isset($this->cant_minima)) { 
			if (count($this->estado) < $this->cant_minima){
				$elemento = ($this->cant_minima == 1) ? "un elemento" : "{$this->cant_minima} elementos";
				return "Seleccione al menos $elemento.";
			}
		}
		if (isset($this->cant_maxima)){ 
			if (count($this->estado) > $this->cant_maxima){
				$elemento = ($this->cant_maxima == 1) ? "un elemento" : "{$this->cant_maxima} elementos";				
				return "No puede seleccionar más de $elemento.";
			}
		}
		return true;
	}
	
	protected function parametros_js()
	{
		$limites = array();
		$limites[0] = isset($this->cant_minima) ? $this->cant_minima : null;
		$limites[1] = isset($this->cant_maxima) ? $this->cant_maxima : null;
		return parent::parametros_js().','.toba_js::arreglo($limites, false);
	}
	
	function es_seleccionable()
	{
		return true;	
	}

	function permite_seleccion_multiple()
	{
		return true;
	}
	
	function es_estado_unico() 
	{
		return false;	
	}	
	
	function get_consumo_javascript()
	{
		$consumos = array('efs/ef', 'efs/ef_multi_seleccion');
		return $consumos;
	}
		
	function get_descripcion_estado($tipo_salida)
	{
		switch ($tipo_salida) {
			case 'html':
			case 'impresion_html':
				$desc = "<ul class='{$this->clase_css}'>\n";
				foreach ($this->get_estado_para_input() as $estado) {
					$desc .= "<li>{$this->opciones[$estado]}</li>\n";
				}
				$desc .= "</ul>\n";
				return $desc;	
			break;
			case 'pdf':
				$desc = array();
				foreach ($this->get_estado_para_input() as $estado) {
					$desc[] = $this->opciones[$estado];
				}
				return implode("\n", $desc);
			case 'excel';
				$desc = array();
				foreach ($this->get_estado_para_input() as $estado) {
					$desc[] = $this->opciones[$estado];
				}
				return array(implode(", ", $desc), null);					
			break;
		}
	}
	
	/**
	 * Puede retornar el estado serializado (un unico string) o en un arreglo dependiendo de su definición en el editor
	 */
	function get_estado()
	{
		if ($this->tiene_estado()) {
			if ($this->serializar !== false) {
				return implode($this->estado, $this->serializar);	
			} else {
				return $this->estado;
			}
		} else {
			if ($this->serializar !== false) {
				return null;	
			} else {
				return array();
			}
		}		
	}
	
	protected function get_estado_para_input()
	{
		if ($this->es_estado_nulo($this->estado))	{
			return $this->estado_nulo;	
		}
		if (! is_array($this->dato)) {
			return $this->estado;	
		} else {
			$salida = array();
			foreach ($this->estado as $registro) {
				$salida[] = implode(apex_qs_separador, $registro);
			}	
			return $salida;
		}
	}
	

	
}

//########################################################################################################
//########################################################################################################

/**
 * Permite la selección de varios valores a partir de una lista. Equivale al tag <select multiple> en HTML
 * @package Componentes
 * @subpackage Efs
 * @jsdoc ef_multi_seleccion_lista ef_multi_seleccion_lista
 */
class toba_ef_multi_seleccion_lista extends toba_ef_multi_seleccion
{
	protected $clase_css = 'ef-multi-sel-lista';
	
	static function get_lista_parametros()
	{
		$param = parent::get_lista_parametros();
		$param[] = 'selec_tamano';
		$param[] = 'selec_ancho';
		return $param;    	
	}	
	
	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);		
	}

	function get_input()
	{
		$estado = $this->get_estado_para_input();
		$html = "";
		if (!$this->es_solo_lectura() && $this->mostrar_utilidades)	{
			$html .= "
				<div class='ef-multi-sel-todos' id='{$this->id_form}_utilerias'>
					<a href=\"javascript:{$this->objeto_js()}.seleccionar_todo(true)\">Todos</a> / 
					<a href=\"javascript:{$this->objeto_js()}.seleccionar_todo(false)\">Ninguno</a></div>
			";
		}
		$tamanio = isset($this->tamanio) ? $this->tamanio: count($this->opciones);
		$tab = $this->padre->get_tab_index();
		$extra = " tabindex='$tab'";
		$extra .= ($this->es_solo_lectura()) ? "disabled" : "";
		if (isset($this->ancho)) {
			$extra .= " style='width: {$this->ancho}'";
		}
		$html .= toba_form::multi_select($this->id_form, $estado, $this->opciones, $tamanio, $this->clase_css, $extra);
		$html .= $this->get_html_iconos_utilerias();
		return $html;
	}
	
	function crear_objeto_js()
	{
		return "new ef_multi_seleccion_lista({$this->parametros_js()})";
	}	
	
}
//########################################################################################################
//########################################################################################################

/**
 * Permite la selección de varios valores a partir de un conjunto de checkboxes
 * @package Componentes
 * @subpackage Efs
 * @jsdoc ef_multi_seleccion_check ef_multi_seleccion_check
 */
class toba_ef_multi_seleccion_check extends toba_ef_multi_seleccion
{
	protected $clase_css = 'ef-multi-sel-check';
	protected $cantidad_columnas = 1;	
	
	static function get_lista_parametros()
	{
		$param = toba_ef_multi_seleccion::get_lista_parametros();
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
    
	function crear_objeto_js()
	{
		return "new ef_multi_seleccion_check({$this->parametros_js()}, $this->cantidad_columnas)";
	}	
	
	function set_cantidad_columnas($cantidad)
	{
		$this->cantidad_columnas = $cantidad;
	}	
	
	function es_multicolumna()
	{
		return ($this->cantidad_columnas > 1);
	}
	
	function get_input()
	{
		$estado = $this->get_estado_para_input();
		$html = "";
		$i = 0;
		$tab = $this->padre->get_tab_index();
		$input_extra = " tabindex='$tab'";
		
		if ($this->mostrar_utilidades && !$this->es_solo_lectura())	{
			$html .= "
				<div id='{$this->id_form}_utilerias' class='ef-multi-sel-todos'>
					<a href=\"javascript:{$this->objeto_js()}.seleccionar_todo(true)\">Todos</a> / 
					<a href=\"javascript:{$this->objeto_js()}.seleccionar_todo(false)\">Ninguno</a></div>
			";
		}
		$html .= "<div id='{$this->id_form}_opciones' class='{$this->clase_css}'><table>\n";
		foreach ($this->opciones as $clave => $descripcion) {
			if ($i % $this->cantidad_columnas == 0) {
					$html .= "<tr>\n";	
			}
			$id = $this->id_form.$i;			
			$html .= "\t<td><label class='ef-multi-check' for='$id'>";
			$ok = in_array($clave, $estado);
			if (! $this->permitir_html) {
				$clave = texto_plano($clave);
			}
			if (! $this->es_solo_lectura()) {
				$checkeado =  $ok ? "checked" : "";
				$html .= "<input name='{$this->id_form}[]' id='$id' type='checkbox' value='$clave' $checkeado class='ef-checkbox' $input_extra>";
				$input_extra = '';
			} else {
				//---Caso solo-lectura	
				$img = $ok ? 'efcheck_on.gif' : 'efcheck_off.gif';
				$html .= toba_recurso::imagen_toba('nucleo/'.$img,true,16,16);
				if ($ok) {
					$html .= "<input name='{$this->id_form}[]' id='$id' type='hidden' value='$clave'>";
				}
			}
			if (! $this->permitir_html) {
				$descripcion = texto_plano($descripcion);
			}
			$html .= "$descripcion</label></td>\n";		
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
		$html .= "</table>";
		$html .= "</div>\n";
		$html .= $this->get_html_iconos_utilerias();
		return $html;
	}	
	
}

//########################################################################################################
//########################################################################################################

/**
 * Permite la selección de varios valores a partir de una lista doble, pasando los elementos de un lado hacia el otro
 * @package Componentes
 * @subpackage Efs
 * @jsdoc ef_multi_seleccion_doble ef_multi_seleccion_doble
 */
class toba_ef_multi_seleccion_doble extends toba_ef_multi_seleccion
{
	protected $clase_css = 'ef-multi-sel-check';	
	
	static function get_lista_parametros()
	{
		$param = parent::get_lista_parametros();
		$param[] = 'selec_tamano';
		$param[] = 'selec_ancho';
		return $param;    	
	}	
		
	protected function parametros_js()
	{
		$imgs = array();
		$imgs[] = toba_recurso::imagen_toba('nucleo/paginacion/no_siguiente.gif', false);
		$imgs[] = toba_recurso::imagen_toba('nucleo/paginacion/si_siguiente.gif', false);
		$imgs[] = toba_recurso::imagen_toba('nucleo/paginacion/no_anterior.gif', false);
		$imgs[] = toba_recurso::imagen_toba('nucleo/paginacion/si_anterior.gif', false);
		$claves = array();
		foreach (array_keys($this->opciones) as $clave) {
			$claves[] = texto_plano($clave);
		}
		$orden_opciones = toba_js::arreglo($claves);
		return parent::parametros_js().",".toba_js::arreglo($imgs, false).', '.$orden_opciones;
	}
	
	function crear_objeto_js()
	{
		return "new ef_multi_seleccion_doble({$this->parametros_js()})";
	}	
		
	function get_input()
	{
		$tab = $this->padre->get_tab_index();
		$extra = " tabindex='$tab'";
		if (isset($this->ancho)) {
			$extra .= " style='width: {$this->ancho}'";
		}		
		$html = '';
		if (!$this->es_solo_lectura() && $this->mostrar_utilidades)	{
			$html .= "
				<div class='ef-multi-sel-todos' id='{$this->id_form}_utilerias'>
					<a href=\"javascript:{$this->objeto_js()}.seleccionar_todo(true)\">Todos</a> /
					<a href=\"javascript:{$this->objeto_js()}.seleccionar_todo(false)\">Ninguno</a>
				</div>
			";
		}
		$tamanio = isset($this->tamanio) ? $this->tamanio: count($this->opciones);
		$estado = $this->get_estado_para_input();
		$izq = array();
		$der = array();
		foreach ($this->opciones as $clave => $valor) {
			if (in_array($clave, $estado)) {
				$der[$clave] = $valor;	
			} else {
				$izq[$clave] = $valor;	
			}
		}	
		$etiq_izq = "Disponibles";
		$etiq_der = "Seleccionados";
		$ef_js = $this->objeto_js();
		$img_der = toba_recurso::imagen_toba('nucleo/paginacion/no_siguiente.gif', false);
		$boton_der = "<img src='$img_der' id='{$this->id_form}_img_izq' onclick=\"$ef_js.pasar_a_derecha()\" class='ef-multi-doble-boton'>";
		$img_izq = toba_recurso::imagen_toba('nucleo/paginacion/no_anterior.gif', false);
		$boton_izq = "<img src='$img_izq' id='{$this->id_form}_img_der' onclick=\"$ef_js.pasar_a_izquierda()\" class='ef-multi-doble-boton'>";
		
		$disabled = ($this->es_solo_lectura()) ? "disabled" : "";
		$html .= "<table class='{$this->clase_css}'>";
		$html .= "<tr><td>$etiq_izq</td><td></td><td>$etiq_der</td></tr>";
		$html .= "<tr><td>";

		$html .= toba_form::multi_select($this->id_form."_izq", array(), $izq, $tamanio, 'ef-combo', "$extra $disabled ondblclick=\"$ef_js.pasar_a_derecha();\" onchange=\"$ef_js.refrescar_iconos('izq');\"");
		$html .= "</td><td>$boton_der<br /><br />$boton_izq</td><td>";
		$html .= toba_form::multi_select($this->id_form, array(), $der, $tamanio, 'ef-combo', "$extra $disabled ondblclick=\"$ef_js.pasar_a_izquierda();\" onchange=\"$ef_js.refrescar_iconos('der');\"");
		$html .= $this->get_html_iconos_utilerias();
		$html .= "</td></tr>";
		$html .= "</table>";
		return $html;
	}

}

?>