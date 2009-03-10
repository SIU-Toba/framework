<?php

/**
 * @package Componentes
 * @subpackage Filtro
 */
class toba_filtro_columna_booleano extends toba_filtro_columna
{
	
	static function get_clase_ef()
	{
		return 'ef_radio';
	}
	
	function ini()
	{
		//-- Parámetros del ef
		$parametros = $this->_datos;
		if (! isset($parametros['selec_cant_columnas'])) {
			$parametros['selec_cant_columnas'] = 2;	
		}
		if (! isset($parametros['estado_defecto'])) {		
			$parametros['estado_defecto'] = 1;
		}
		$obligatorio = array($this->_datos['obligatorio'], false);
		$this->_ef = new toba_ef_radio($this, null, $this->_datos['nombre'], $this->_datos['etiqueta'],
											null, null, $obligatorio, $parametros);
								
		$opciones = array();			
		$opciones['1'] = 'Sí';
		$opciones['0'] = 'No';
		$this->_ef->set_opciones($opciones);
		
			//--- Condiciones
		$this->agregar_condicion('es_igual_a', 		new toba_filtro_condicion('es igual a',	 	'=', 	'', 	'', 	'', 	''));
	}

	
}

?>