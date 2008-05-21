<?php

class toba_filtro_columna_fecha extends toba_filtro_columna
{
	protected $_condiciones = array(
			'es_igual_a' 	=> array('etiqueta' => 'es igual a',	'operador_sql' => '=',			'pre' => '', 	'post' => '', 	'casting' => '::date'),
			'es_distinto_de' => array('etiqueta' => 'es distinto de',	'operador_sql' => '!=',			'pre' => '', 	'post' => '', 	'casting' => '::date'),
			'desde' 		=> array('etiqueta' => 'desde',			'operador_sql' => '<',			'pre' => '', 	'post' => '', 	'casting' => '::date'),
			'hasta' 		=> array('etiqueta' => 'hasta',			'operador_sql' => '>',			'pre' => '', 	'post' => '', 	'casting' => '::date'),
			'entre' 		=> array('etiqueta' => 'entre',			'operador_sql' => 'BETWEEN',	'pre' => '', 	'post' => '', 	'casting' => '::date'),
	);

	function ini()
	{
		$parametros = array();
		$this->_ef = new toba_ef_editable_fecha($this, null, $this->_datos['nombre'], $this->_datos['etiqueta'],
											null, null, false, $parametros);
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
	
	function es_compuesto()
	{
		return isset($this->_condiciones['entre']);
	}	
	
	function set_estado($estado)
	{
		$this->estado = $estado;
		$this->_ef->set_estado($estado['valor']);
	}	
	
	function cargar_estado_post()
	{
		if (isset($_POST[$this->_id_form_cond])) {
			$condicion = $_POST[$this->_id_form_cond];
			if (! isset($this->_condiciones[$condicion])) {
				throw new toba_error("La condicion '$condicion' no es una condicion válida");
			}
			//Cargo el estado del ef original
			$this->_ef->ir_a_fila(1);
			$this->_ef->cargar_estado_post();
						
			$this->_estado = array();
			$this->_estado['condicion'] = $condicion;
			$this->_estado['valor'] = $this->_ef->get_estado();
		}
	}
	
	function get_html_valor()
	{
		echo $this->_ef->get_input();
		echo "<div id='{$this->_id_form_cond}_ef_extra' style='display:none'>";
		//Se fuerza a que el ef genere un nuevo id para que sean dos campos fecha distintos
		$this->_ef->ir_a_fila(1);
		echo $this->_ef->get_input();
		//Se retorna al id original
		echo $this->_ef->ir_a_fila();
		echo "</div>";
	}		
	
	function get_html_condicion()
	{
		$html = parent::get_html_condicion();
		$html .= "<div id='{$this->_id_form_cond}_label_extra' style='display:none'>";
		$html .= '<br>y</div>';
		return $html;
	}		
		
}

?>