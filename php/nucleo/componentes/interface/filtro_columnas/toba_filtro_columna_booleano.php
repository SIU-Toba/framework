<?php

class toba_filtro_columna_booleano extends toba_filtro_columna
{
	
	function ini()
	{
		$parametros = array();
		$parametros['selec_cant_columnas'] = 2;	
		$parametros['estado_defecto'] = 1;
		$this->_ef = new toba_ef_radio($this, null, $this->_datos['nombre'], $this->_datos['etiqueta'],
											null, null, false, $parametros);
								
		$opciones = array();			
		$opciones['1'] = 'Sí';
		$opciones['0'] = 'No';
		$this->_ef->set_opciones($opciones);
	}

	function tiene_condicion()
	{
		return false;
	}

	function set_estado($estado)
	{
		$this->estado = $estado;
		$this->_ef->set_estado($estado['valor']);
	}	
	
	function cargar_estado_post()
	{
		$this->_ef->cargar_estado_post();			
		$this->_estado = array();
		$this->_estado['condicion'] = null;
		$this->_estado['valor'] = $this->_ef->get_estado();
	}
		
}

?>