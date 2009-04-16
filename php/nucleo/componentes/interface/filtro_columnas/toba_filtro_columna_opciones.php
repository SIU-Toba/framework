<?php

/**
 * @package Componentes
 * @subpackage Filtro
 */
class toba_filtro_columna_opciones extends toba_filtro_columna
{

	
	function ini()
	{
		$parametros = $this->_datos;
		$clase_ef = 'toba_'.$this->_datos['opciones_ef'];
		$obligatorio = array($this->_datos['obligatorio'], false);
		$this->_ef = new $clase_ef($this, null, $this->_datos['nombre'], $this->_datos['etiqueta'],
											null, null, $obligatorio, $parametros);

		//--- Condiciones											
		if (! $this->es_seleccion_multiple()) {
			$this->agregar_condicion('es_igual_a', 			new toba_filtro_condicion('es igual a',	 			'=', 	'', 	'', 	'', 	''));
			$this->agregar_condicion('es_distinto_de', 		new toba_filtro_condicion_negativa('es distinto de', 		'!=',	'', 	'', 	'', 	''));
		} else {
			$this->agregar_condicion('en_conjunto', 		new toba_filtro_condicion_multi('', 	''));
		}
											
	}
	
	function es_seleccion_multiple()
	{
		return $this->_datos['opciones_es_multiple'];
	}

	
}

?>