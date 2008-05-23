<?php

abstract class toba_filtro_columna
{
	protected $_datos;
	protected $_ef;
	protected $_padre;
	protected $_id_form_cond;
	protected $_estado = null;	
	protected $_schema;
	protected $_condiciones = array();
	
	function __construct($datos, $padre) 
	{
		$this->_datos = $datos;
		$this->_padre = $padre;
		$this->_id_form_cond = "col_" . $this->_padre->get_id_form() . $this->_datos['nombre'];		
		$this->ini();
	}
	
	abstract function ini();

	function get_id_form()
	{
		return $this->_padre->get_id_form();
	}
	
	function get_tab_index()
	{
		return $this->_padre->get_tab_index();
	}
	
	function set_visible($visible)
	{
		$this->_datos['inicial'] = $visible;
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
	
	function get_schema()
	{
		if (isset($this->_schema)) {
			return $this->_schema.'.';
		}
	}
	
	function set_schema($schema)
	{
		$this->_schema = $schema;
	}
	
	function get_etiqueta()
	{
		return $this->_datos['etiqueta'];
	}

	function get_consumo_javascript()
	{
		return $this->_ef->get_consumo_javascript();
	}
	
	function crear_objeto_js()
	{
		return $this->_ef->crear_objeto_js();
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
	
	function tiene_condicion()
	{
		return ! empty($this->_condiciones);
	}
	
	function get_condicion()
	{
		if (isset($this->_estado)) {
			return $this->_estado['condicion'];
		}
	}
	
	function set_estado($estado)
	{
		$this->_estado = $estado;
		$this->_ef->set_estado($estado['valor']);
	}	
	
	function cargar_estado_post()
	{
		$this->_estado = array();		
		if ($this->tiene_condicion()) {
			if (isset($_POST[$this->_id_form_cond])) {
				$condicion = $_POST[$this->_id_form_cond];
				if (! isset($this->_condiciones[$condicion])) {
					throw new toba_error("La condicion '$condicion' no es una condicion válida");
				}
				$this->_estado['condicion'] = $condicion;
			}
		}
		$this->_ef->cargar_estado_post();			
		$this->_estado['valor'] = $this->_ef->get_estado();
		
	}	
	
	function get_where()
	{
		if (isset($this->_estado)) {
			$operador_sql = '=';
			$casting = '';
			$pre = '';
			$post = '';
			if (isset($this->_estado['condicion'])) {
				$id = $this->_estado['condicion'];
				$operador_sql = $this->_condiciones[$id]['operador_sql'];
				$pre = $this->_condiciones[$id]['pre'];
				$post = $this->_condiciones[$id]['post'];
				$casting = $this->_condiciones[$id]['casting'];
			}
			$valor = toba::db()->quote($pre.trim($this->_estado['valor']).$post);
			return $this->get_schema().$this->get_nombre().$casting.' '.$operador_sql.' '.
						$valor.$casting;
		}
	}
	
	function get_html_valor()
	{
		echo $this->_ef->get_input();
	}	
	
	function get_objeto_js_ef($id)
	{
		return $this->_padre->get_objeto_js_ef($id);
	}
	
	function get_objeto_js()
	{
		return $this->_padre->get_objeto_js();
	}
	
	function get_html_condicion()
	{
		$onchange = "{$this->get_objeto_js()}.cambio_condicion(\"{$this->get_nombre()}\");";
		$html = "<select id='{$this->_id_form_cond}' name='{$this->_id_form_cond}' onchange='$onchange'>";
		foreach ($this->_condiciones as $id => $condicion) {
			$selected = '';
			if (isset($this->_estado) && $this->_estado['condicion'] == $id) {
				$selected = 'selected';	
			}
			$html .= "<option value='$id' $selected>{$condicion['etiqueta']}</option>\n";
		}
		$html .= '</select>';
		return $html;
	}	
	
}

?>