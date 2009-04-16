<?php

/**
 * @package Componentes
 * @subpackage Filtro
 */
class toba_filtro_columna_cadena extends toba_filtro_columna
{
	static function get_clase_ef()
	{
		return 'ef_editable';
	}	
	
	function ini()
	{
		//--- Parámetros ef
		$parametros = $this->_datos;
		if (! isset($parametros['edit_tamano'])) {
			$parametros['edit_tamano'] = 18;
		}
		if (! isset($parametros['edit_maximo'])) {
			$parametros['edit_maximo'] = 255;
		}
		$obligatorio = array($this->_datos['obligatorio'], false);		
		$this->_ef = new toba_ef_editable($this, null, $this->_datos['nombre'], $this->_datos['etiqueta'],
											null, null, $obligatorio, $parametros);

		//--- Condiciones
		$this->agregar_condicion('contiene', 		new toba_filtro_condicion('contiene',	 	'ILIKE', 	'%', 	'%', 	'::varchar', 	''));
		$this->agregar_condicion('no_contiene', 	new toba_filtro_condicion_negativa('no contiene', 	'NOT ILIKE','%', 	'%', 	'::varchar', 	''));
		$this->agregar_condicion('comienza_con', 	new toba_filtro_condicion('comienza con', 	'ILIKE', 	'', 	'%', 	'::varchar', 	''));
		$this->agregar_condicion('termina_con', 	new toba_filtro_condicion('termina con', 	'ILIKE', 	'%', 	'', 	'::varchar', 	''));
		$this->agregar_condicion('es_igual_a', 		new toba_filtro_condicion('es igual a',		'=', 		'', 	'', 	'::varchar', 	''));
		$this->agregar_condicion('es_distinto_de', 	new toba_filtro_condicion_negativa('es distinto de', '!=', 		'', 	'', 	'::varchar', 	''));
		
	}


}

?>