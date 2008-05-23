<?php

class toba_filtro_columna_booleano extends toba_filtro_columna
{
	
	function ini()
	{
		$parametros = $this->_datos;
		if (! isset($parametros['selec_cant_columnas'])) {
			$parametros['selec_cant_columnas'] = 2;	
		}
		if (! isset($parametros['estado_defecto'])) {		
			$parametros['estado_defecto'] = 1;
		}
		$obligatorio = array($this->_datos['obligatorio'], false);
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

	function cargar_estado_post()
	{
		$this->_ef->cargar_estado_post();			
		$this->_estado = array();
		$this->_estado['condicion'] = null;
		$this->_estado['valor'] = $this->_ef->get_estado();
	}
		
}

?>