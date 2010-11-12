<?php

/**
 * Contiene una condicion y un ef. Se trata de reutilizar al maximo la logica de los efs sin heredarlos, es por eso que muchas llamadas pasan directo
 *
 * @package Componentes
 * @subpackage Filtro
 **/
abstract class toba_filtro_columna
{
	protected $_datos;
	protected $_ef;
	protected $_padre;
	protected $_id_form_cond;
	protected $_estado = null;	
	protected $_condiciones = array();
	protected $_solo_lectura = false;
	protected $_funcion_formateo = null;
	protected $_condicion_default = null;
	
	function __construct($datos, $padre) 
	{
		$this->_datos = $datos;
		$this->_padre = $padre;
		$this->_id_form_cond = "col_" . $this->_padre->get_id_form() . $this->_datos['nombre'];		
		$this->ini();
	}
	
	/**
	 * Método para construir el ef adecuado según el tipo de columna
	 */
	abstract function ini();

	//-----------------------------------------------
	//--- COMANDOS ---------------------------------
	//-----------------------------------------------	
	
	function set_estado($estado)
	{
		if ($this->hay_condicion_fija()){
			if  (isset($estado['condicion']) && isset($this->_estado)  &&($this->_estado['condicion'] != $estado['condicion'])){	//Si la condicion no viene seteada retorna al default
				$msg = "Existe una condicion fija para la columna '".$this->get_nombre().
							"' la misma no se puede cambiar seteando el estado.";
				throw new toba_error_def($msg);
			}
		}

		$this->_estado = $estado;
		$this->_ef->set_estado($estado['valor']);
	}	
	
	function set_visible($visible)
	{
		$this->_datos['inicial'] = $visible;
	}	
	
	function set_solo_lectura($solo_lectura = true)
	{
		$this->_solo_lectura = $solo_lectura;
		$this->_ef->set_solo_lectura($solo_lectura);
	}
	
	function set_expresion($campo)
	{
		$this->_datos['expresion'] = $campo;
	}
	
	function cargar_estado_post()
	{
		$this->_estado = array();	
		if (isset($_POST[$this->_id_form_cond])) {
			$condicion = $_POST[$this->_id_form_cond];
			if (! isset($this->_condiciones[$condicion])) {
				throw new toba_error_seguridad("La condicion '$condicion' no es una condicion válida");
			}
			$this->_estado['condicion'] = $condicion;
		} else {
			throw new toba_error_seguridad("No hay una condición valida");
		}

		$this->_ef->cargar_estado_post();			
		$this->_estado['valor'] = $this->_ef->get_estado();
		
	}	
	
	function agregar_condicion($id, toba_filtro_condicion $condicion)
	{
		$this->_condiciones[$id] = $condicion;
	}
	
	function borrar_condicion($id)
	{
		unset($this->_condiciones[$id]);
	}
	
	
	function set_formateo($funcion)
	{
		$this->_funcion_formateo = $funcion;
	}
	//-----------------------------------------------
	//--- CONSULTAS ---------------------------------
	//-----------------------------------------------
	
	
	
	function get_id_metadato()
	{
		return $this->_datos['objeto_ei_filtro_col'];
	}
	
	function get_id_form()
	{
		return $this->_padre->get_id_form();
	}
	
	function get_tab_index()
	{
		return $this->_padre->get_tab_index();
	}
	
	function es_obligatorio()
	{
		return $this->_ef->es_obligatorio();
	}

	function es_solo_lectura()
	{
		return $this->_solo_lectura;
	}
	
	function es_visible()
	{
		return $this->_datos['inicial'];
	}
	
	function es_compuesto()
	{
		return false;
	}
	
	function get_nombre()
	{
		return $this->_datos['nombre'];
	}
	
	function get_ef()
	{
		return $this->_ef;
	}
	
	function get_expresion()
	{
		return $this->_datos['expresion'];
	}

	function get_etiqueta()
	{
		return $this->_datos['etiqueta'];
	}


	function get_formateo()
	{
		return $this->_funcion_formateo;	
	}
	
	function validar_estado()
	{
		return $this->_ef->validar_estado();
	}
	
	
	function resetear_estado()
	{
		$this->_ef->resetear_estado();
		$this->_estado = null;
	}
	
	function get_estado()
	{
		return $this->_estado;
	}

	function tiene_estado()
	{
		return isset($this->_estado);
	}
	
	function get_cant_condiciones()
	{
		return count($this->_condiciones);
	}

	/**
	 * Permite saber si la columna tiene una condicion fija o no.
	 * @return boolean
	 */
	function hay_condicion_fija()
	{
		$hay_fija = false;
		foreach($this->_condiciones as $condicion){
			if ($condicion->es_condicion_fija()){
				$hay_fija = true;
				break;
			}
		}
		return $hay_fija;
	}

	/**
	 * Coloca una condicion como fija para esta columna, la condicion permanecera solo_lectura y se
	 * transformara en default para esta columna. El estado decide si esta seteada o no.
	 * @param string $nombre
	 * @param boolean $estado
	 */
	function set_condicion_fija($nombre, $estado = true)
	{
		if (!isset($this->_condiciones[$nombre])){
			throw new toba_error_def("No existe la condicion '$nombre' para la columna '". $this->get_nombre()."'");
		}

		if ($this->hay_condicion_fija()){
			throw new toba_error_def("Ya existe una condicion fija para la columna '".$this->get_nombre()."'");
		}

		$this->_condicion_default = ($estado) ? $nombre : null;		//Si el estado es false se limpia el default
		$this->condicion($nombre)->set_condicion_fija($estado);
	}

	/**
	 * Setea una condicion como default para la columna, esto es, cuando no haya estado especificado
	 * se tomara la condicion default para la columna
	 * @param string $nombre
	 */
	function set_condicion_default($nombre)
	{
		if (!isset($this->_condiciones[$nombre])){
			throw new toba_error_def("No existe la condicion '$nombre' para la columna '". $this->get_nombre()."'");
		}
		$this->_condicion_default = $nombre;
	}

	/**
	 *  Elimina la condicion default para la columna
	 */
	function eliminar_condicion_default()
	{
		$this->_condicion_default = null;
	}

	/**
	 * Determina si la columna tiene condicion default o no.
	 * @return boolean
	 */
	function hay_condicion_default()
	{
		return (! is_null($this->_condicion_default));
	}

	/**
	 * Retorna una condición asociada a la columna, por defecto la que actualmente selecciono el usuario
	 * @return toba_filtro_condicion
	 */
	function condicion($nombre = null)
	{
		if (! isset($nombre)) {
			if (isset($this->_estado)) {
				return $this->_condiciones[$this->_estado['condicion']];
			} else {
				throw new toba_error_def("No hay una condicion actualmente seleccionada para la columna '".$this->get_nombre()."'");
			}
		} else {
			return $this->_condiciones[$nombre];
		}
	}
	
	function set_condicion(toba_filtro_condicion $condicion, $nombre=null)
	{
		if (! isset($nombre)) {
			if (isset($this->_estado)) {
				$this->_condiciones[$this->_estado['condicion']] = $condicion;
			} else {
				throw new toba_error_def("No hay una condicion actualmente seleccionada para la columna '".$this->get_nombre()."'");
			}
		} else {
			$this->_condiciones[$nombre] = $condicion;
		}		
	}
	
	
	function get_sql_where()
	{
		if (isset($this->_estado)) {
			$id = $this->_estado['condicion'];	
			return $this->_condiciones[$id]->get_sql($this->get_expresion(), $this->_estado['valor']);
		}
	}


	//-----------------------------------------------
	//--- SALIDA HTML  ------------------------------
	//-----------------------------------------------
	
	function get_html_condicion()
	{
		if (count($this->_condiciones) > 1) {
			//-- Si tiene mas de una condicion se muestran con un combo
			$onchange = "{$this->get_objeto_js()}.cambio_condicion(\"{$this->get_nombre()}\");";
			$html = '';
			if ($this->hay_condicion_default() && (!isset($this->_estado['condicion']) || is_null($this->_estado['condicion']))){
				//Si no tiene estado y hay default seteado, el default es el nuevo estado
				$this->_estado['condicion'] = $this->_condicion_default;
			}
			if ($this->_solo_lectura || $this->hay_condicion_fija()) {
				$id = $this->_id_form_cond.'_disabled';
				$disabled = 'disabled';
				$html .= "<input type='hidden' id='{$this->_id_form_cond}' name='{$this->_id_form_cond}' value='{$this->_estado['condicion']}'/>\n";				
			} else {
				$disabled = '';
				$id = $this->_id_form_cond;
			}
			$html .= "<select id='$id' name='$id' $disabled onchange='$onchange'>";
			foreach ($this->_condiciones as $id => $condicion) {
				$selected = '';
				if (isset($this->_estado) && $this->_estado['condicion'] == $id) {
					$selected = 'selected';	
				}
				$html .= "<option value='$id' $selected>".$condicion->get_etiqueta()."</option>\n";
			}
			$html .= '</select>';

			return $html;
		} else {
			reset($this->_condiciones);
			$condicion = key($this->_condiciones);
			//-- Si tiene una unica, seria redundante mostrarle la unica opción, se pone un hidden
			return "<input type='hidden' id='{$this->_id_form_cond}' name='{$this->_id_form_cond}' value='$condicion'/>&nbsp;";
		}
	}	
	
	function get_html_valor()
	{
		echo $this->_ef->get_input();
	}

	function get_html_etiqueta()
	{
		$html = '';
		$marca ='';		
        if ($this->_ef->es_obligatorio()) {
    	        $estilo = 'ei-filtro-etiq-oblig';
				$marca = '(*)';
    	} else {
            $estilo = 'ei-filtro-etiq';
	    }
		$desc='';
		$desc = $this->_datos['descripcion'];
		if ($desc !=""){
			$desc = toba_parser_ayuda::parsear($desc);
			$desc = toba_recurso::imagen_toba("descripcion.gif",true,null,null,$desc);
		}
		$id_ef = $this->_ef->get_id_form();					
		$editor = '';		
		//$editor = $this->generar_vinculo_editor($ef);
		$etiqueta = $this->get_etiqueta();
		$html .= "<label for='$id_ef' class='$estilo'>$editor $desc $etiqueta $marca</label>\n";
		return $html;
	}
		

	//-----------------------------------------------
	//--- JAVASCRIPT   ------------------------------
	//-----------------------------------------------

	function get_objeto_js_ef($id)
	{
		return $this->_padre->get_objeto_js_ef($id);
	}
	
	function get_objeto_js()
	{
		return $this->_padre->get_objeto_js();
	}
		
	function get_consumo_javascript()
	{
		return $this->_ef->get_consumo_javascript();
	}
	
	function crear_objeto_js()
	{
		return $this->_ef->crear_objeto_js();
	}	
}

?>