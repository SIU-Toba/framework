<?php
/**
 * @package Componentes
 * @subpackage Filtro
 */
class toba_filtro_columna_hora extends toba_filtro_columna_compuesta 
{
	static function get_clase_ef()
	{
		return 'ef_editable_hora';
	}		
	
	function ini()
	{
		
		//--- Parámetros efs
		$parametros = $this->_datos;
		if (! isset($parametros['edit_tamano'])) {
			$parametros['edit_tamano'] = 6;
		}
		$obligatorio = array($this->_datos['obligatorio'], false);
		$this->_ef = new toba_ef_editable_hora($this, null, $this->_datos['nombre'], $this->_datos['etiqueta'],
											null, null, $obligatorio, $parametros);
											

		//--- Condiciones
		$this->agregar_condicion('es_igual_a', 			new toba_filtro_condicion('es igual a',	 			'=', 	'', 	'', 	'', 	'::time'));
		$this->agregar_condicion('es_distinto_de', 		new toba_filtro_condicion_negativa('es distinto de', 		'!=',	'', 	'', 	'', 	'::time'));
		$this->agregar_condicion('es_mayor_que', 		new toba_filtro_condicion('es mayor que', 			'>','', '', 	'', 	'',		'::time'));
		$this->agregar_condicion('es_mayor_igual_que', 	new toba_filtro_condicion('es mayor o igual que', 	'>=', 	'', 	'', 	'', 	'::time'));
		$this->agregar_condicion('es_menor_que', 		new toba_filtro_condicion('es menor que',			'<', 	'', 	'', 	'', 	'::time'));
		$this->agregar_condicion('es_menor_igual_que', 	new toba_filtro_condicion('es menor o igual que', 	'<=', 	'', 	'', 	'', 	'::time'));
		
		// Condicion entre
		$this->agregar_condicion('entre',			 	new toba_filtro_condicion_entre('', '::time'));
	}
}
	
?>
