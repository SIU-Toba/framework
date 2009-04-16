<?php

/**
 * @package Componentes
 * @subpackage Filtro
 */
class toba_filtro_columna_numero extends toba_filtro_columna_compuesta 
{
	static function get_clase_ef()
	{
		return 'ef_editable_numero';
	}		
	
	function ini()
	{
		
		//--- Parámetros efs
		$parametros = $this->_datos;
		if (! isset($parametros['edit_tamano'])) {
			$parametros['edit_tamano'] = 18;
		}
		$obligatorio = array($this->_datos['obligatorio'], false);
		$this->_ef = new toba_ef_editable_numero($this, null, $this->_datos['nombre'], $this->_datos['etiqueta'],
											null, null, $obligatorio, $parametros);
											

		//--- Condiciones
		$this->agregar_condicion('es_igual_a', 			new toba_filtro_condicion('es igual a',	 			'=', 	'', 	'', 	'', 	''));
		$this->agregar_condicion('es_distinto_de', 		new toba_filtro_condicion_negativa('es distinto de', 		'!=',	'', 	'', 	'', 	''));
		$this->agregar_condicion('es_mayor_que', 		new toba_filtro_condicion('es mayor que', 			'>','', '', 	'', 	'',		''));
		$this->agregar_condicion('es_mayor_igual_que', 	new toba_filtro_condicion('es mayor o igual que', 	'>=', 	'', 	'', 	'', 	''));
		$this->agregar_condicion('es_menor_que', 		new toba_filtro_condicion('es menor que',			'<', 	'', 	'', 	'', 	''));
		$this->agregar_condicion('es_menor_igual_que', 	new toba_filtro_condicion('es menor o igual que', 	'<=', 	'', 	'', 	'', 	''));
		
		// Condicion entre
		$this->agregar_condicion('entre',			 	new toba_filtro_condicion_entre('', ''));
	}

}

?>