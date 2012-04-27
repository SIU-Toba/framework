<?php

/**
 * Columna que tiene una condicion 'entre' que implica poner dos efs identicos y genera un BETWEEN en el where
 * @package Componentes
 * @subpackage Filtro
 */
abstract class toba_filtro_columna_compuesta extends toba_filtro_columna 
{
	function es_compuesto()
	{
		return isset($this->_condiciones['entre']);
	}	


	function set_estado($estado)
	{
		$this->_estado = $estado;
		if ($estado['condicion'] != 'entre') {
			$this->_ef->set_estado($estado['valor']);
		} else {
			//-- La fila extra se carga cuando sale el html
			$this->_ef->set_estado($estado['valor']['desde']);
		}
	}	
	
	function cargar_estado_post()
	{
		if (isset($_POST[$this->_id_form_cond])) {
			$condicion = $_POST[$this->_id_form_cond];
			if (! isset($this->_condiciones[$condicion])) {
				throw new toba_error_seguridad("La condicion '$condicion' no es una condicion válida");
			}
			//Cargo el estado del ef original
			$this->_ef->cargar_estado_post();						
			$this->_estado = array();
			$this->_estado['condicion'] = $condicion;
			if ($condicion != 'entre') {
				$this->_estado['valor'] = $this->_ef->get_estado();
			} else {
				//-- Fila Normal
				$this->_estado['valor'] = array();
				$this->_estado['valor']['desde'] = $this->_ef->get_estado();
				
				//-- Fila Extra
				$this->_ef->ir_a_fila('extra');
				$this->_ef->cargar_estado_post();
				$this->_estado['valor']['hasta'] = $this->_ef->get_estado();
			}
		}
	}
	

	function get_html_valor()
	{
		//-- Fila normal
		echo $this->_ef->ir_a_fila();	
		echo $this->_ef->get_input();
		
		//--Fila Extra
		$this->_ef->ir_a_fila('extra');		
		if (isset($this->_estado) && $this->_estado['condicion'] == 'entre' && isset($this->_estado['valor']['hasta'])) {
			$this->_ef->set_estado($this->_estado['valor']['hasta']);
		}
		echo "<div id='{$this->_id_form_cond}_ef_extra' style='display:none'>";		
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