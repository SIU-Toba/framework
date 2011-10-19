<?php
/**
 * @package Componentes
 * @subpackage Filtro
 */
class toba_filtro_columna_fecha_hora extends toba_filtro_columna_compuesta 
{
	static function get_clase_ef()
	{
		return 'ef_editable_fecha_hora';
	}		
	
	function ini()
	{
		
		//--- Parámetros efs
		$parametros = $this->_datos;
		$obligatorio = array($this->_datos['obligatorio'], false);
		$this->_ef = new toba_ef_editable_fecha_hora($this, null, $this->_datos['nombre'], $this->_datos['etiqueta'],
											null, null, $obligatorio, $parametros);
											

		//--- Condiciones
		$this->agregar_condicion('es_igual_a', 			new toba_filtro_condicion('es igual a',	 			'=', 	'', 	'', 	'', 	'::timestamp'));
		$this->agregar_condicion('es_distinto_de', 		new toba_filtro_condicion_negativa('es distinto de', 		'!=',	'', 	'', 	'', 	'::timestamp'));
		$this->agregar_condicion('es_mayor_que', 		new toba_filtro_condicion('es mayor que', 			'>','', '', 	'', 	'',		'::timestamp'));
		$this->agregar_condicion('es_mayor_igual_que', 	new toba_filtro_condicion('es mayor o igual que', 	'>=', 	'', 	'', 	'', 	'::timestamp'));
		$this->agregar_condicion('es_menor_que', 		new toba_filtro_condicion('es menor que',			'<', 	'', 	'', 	'', 	'::timestamp'));
		$this->agregar_condicion('es_menor_igual_que', 	new toba_filtro_condicion('es menor o igual que', 	'<=', 	'', 	'', 	'', 	'::timestamp'));
		
		// Condicion entre
		$this->agregar_condicion('entre',			 	new toba_filtro_condicion_entre('', '::timestamp'));
	}		
	
	function get_sql_where()
	{
		if (isset($this->_estado)) {
			$id = $this->_estado['condicion'];				
			if (isset($this->_estado['valor']['desde'])) {	
				$desde = implode(' ', $this->_estado['valor']['desde']);
				$hasta = implode(' ', $this->_estado['valor']['hasta']);				
				$valor = array('desde' => $desde, 'hasta' => $hasta);			
			} else {				
				$valor = implode(' ', $this->_estado['valor']);
			}
			return $this->_condiciones[$id]->get_sql($this->get_expresion(), $valor);
		}
	}
}
	
?>
