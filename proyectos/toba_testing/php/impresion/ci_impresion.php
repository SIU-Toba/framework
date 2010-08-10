<?php

class ci_impresion extends toba_testing_pers_ci
{
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		//$propiedades[] = 'propiedad_a_persistir';
		return $propiedades;
	}

	function vista_impresion($salida)
	{
		$salida->titulo($this->get_nombre());
		$salida->mensaje('Nota: Este es el Principal');
		$this->dependencia('cuadro')->vista_impresion($salida);
		$salida->salto_pagina();
		$salida->mensaje('Nota: Esta es una copia');
		$this->dependencia('cuadro')->vista_impresion($salida);
		$salida->salto_pagina();
		$salida->mensaje('Este es un formulario ML que esta en otra pagina');
		$this->dependencia('ml')->vista_impresion($salida);
	}

	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	//---- cuadro -------------------------------------------------------

	function evt__cuadro__seleccion($seleccion)
	{
	}

	function conf__cuadro()
	{
		$datos[0]['id'] = '1';
		$datos[0]['tipo'] = '1';
		$datos[0]['desc'] = 'Hola';
		$datos[1]['id'] = '2';
		$datos[1]['tipo'] = '1';
		$datos[1]['desc'] = 'Chau';
		$datos[2]['id'] = '3';
		$datos[2]['tipo'] = '1';
		$datos[2]['desc'] = 'Si';
		$datos[3]['id'] = '4';
		$datos[3]['tipo'] = '2';
		$datos[3]['desc'] = 'No';
		$datos[4]['id'] = '5';
		$datos[4]['tipo'] = '2';
		$datos[4]['desc'] = 'Mas';
		$datos[5]['id'] = '6';
		$datos[5]['tipo'] = '2';
		$datos[5]['desc'] = 'Menos';
		
		$sql = 'SELECT objeto as id, nombre as desc, clase as tipo
				FROM apex_objeto
				ORDER BY 3,2 LIMIT 50;';
		//$datos = consultar_fuente( $sql, 'instancia' );
		return $datos;
	}

	//---- filtro -------------------------------------------------------

	function evt__filtro__filtrar($datos)
	{
	}

	function evt__filtro__cancelar()
	{
	}

	function conf__filtro()
	{
		$datos['editable'] = 'editable';
		$datos['combo'] = 'P';
		$datos['checkbox'] = '1';
		$datos['precio'] = '227';
		$datos['lista'] = array('a', 'c');
		return $datos;
	}

	//---- ml -------------------------------------------------------

	function evt__ml__modificacion($datos)
	{
	}

	function conf__ml()
	{
		$datos[0]['id'] = '1';
		$datos[0]['tipo'] = '1';
		$datos[0]['desc'] = 'Hola';
		$datos[1]['id'] = '2';
		$datos[1]['tipo'] = '1';
		$datos[1]['desc'] = 'Chau';
		$datos[2]['id'] = '3';
		$datos[2]['tipo'] = '1';
		$datos[2]['desc'] = 'Si';
		$datos[3]['id'] = '4';
		$datos[3]['tipo'] = '2';
		$datos[3]['desc'] = 'No';
		$datos[4]['id'] = '5';
		$datos[4]['tipo'] = '2';
		$datos[4]['desc'] = 'Mas';
		$datos[5]['id'] = '6';
		$datos[5]['tipo'] = '2';
		$datos[5]['desc'] = 'Menos';
		return $datos;
	}
}
?>