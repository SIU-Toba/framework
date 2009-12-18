<?php

/**
 * @package Componentes
 * @subpackage Filtro
 */
class toba_filtro_columna_fecha extends toba_filtro_columna_compuesta
{
	static function get_clase_ef()
	{
		return 'ef_editable_fecha';
	}		
	
	function ini()
	{
		//--- Parámetros efs		
		$parametros = $this->_datos;
		$obligatorio = array($this->_datos['obligatorio'], false);
		$this->_ef = new toba_ef_editable_fecha($this, null, $this->_datos['nombre'], $this->_datos['etiqueta'],
											null, null, $obligatorio, $parametros);

		//--- Condiciones
		$this->agregar_condicion('es_igual_a', 		new toba_filtro_condicion('es igual a',	 	'=', 	'', 	'', 	'::date', 	'::date'));
		$this->agregar_condicion('es_distinto_de', 	new toba_filtro_condicion_negativa('es distinto de', '!=',	'', 	'', 	'::date', 	'::date'));
		$this->agregar_condicion('desde', 			new toba_filtro_condicion('desde', 			'>=', 	'', 	'', 	'::date', 	'::date'));
		$this->agregar_condicion('hasta', 			new toba_filtro_condicion('hasta', 			'<=', 	'', 	'', 	'::date', 	'::date'));
		
		//Condicion entre
		$this->agregar_condicion('entre', 			new toba_filtro_condicion_entre('::date', 	'::date'));
	}

	
		
}

?>